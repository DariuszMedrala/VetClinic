
\restrict 1ww5gV6ddAZaNpnxQlFe9qMCZTuyQsjCWgQRzAIedrrxFWG3YhFErfFwcpkRhzI

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: animal_sex; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.animal_sex AS ENUM (
    'male',
    'female',
    'unknown'
);


--
-- Name: appointment_status; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.appointment_status AS ENUM (
    'scheduled',
    'confirmed',
    'in_progress',
    'completed',
    'cancelled'
);


--
-- Name: invoice_status; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.invoice_status AS ENUM (
    'pending',
    'paid',
    'cancelled'
);


--
-- Name: payment_method; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.payment_method AS ENUM (
    'card',
    'cash',
    'insurance'
);


--
-- Name: procedure_type; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.procedure_type AS ENUM (
    'treatment',
    'medication'
);


--
-- Name: user_role; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.user_role AS ENUM (
    'client',
    'vet',
    'admin'
);


--
-- Name: fn_calculate_invoice_total(bigint); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.fn_calculate_invoice_total(p_appointment_id bigint) RETURNS numeric
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_subtotal      NUMERIC(10, 2);
    v_points        INTEGER;
    v_discount_rate NUMERIC(4, 2) := 0;
BEGIN
    SELECT COALESCE(SUM(ap.unit_price * ap.quantity), 0)
    INTO v_subtotal
    FROM appointment_procedures ap
    WHERE ap.appointment_id = p_appointment_id;

    SELECT c.loyalty_points
    INTO v_points
    FROM appointments a
    JOIN pets p ON p.id = a.pet_id
    JOIN clients c ON c.user_id = p.client_id
    WHERE a.id = p_appointment_id;

    IF COALESCE(v_points, 0) >= 100 THEN
        v_discount_rate := 0.10;
    END IF;

    RETURN ROUND(v_subtotal * (1 - v_discount_rate), 2);
END;
$$;


--
-- Name: fn_prevent_double_booking(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.fn_prevent_double_booking() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF EXISTS (
        SELECT 1
        FROM appointments a
        WHERE a.vet_id = NEW.vet_id
          AND a.id <> NEW.id
          AND a.status <> 'cancelled'
          AND a.starts_at < NEW.ends_at
          AND a.ends_at > NEW.starts_at
    ) THEN
        RAISE EXCEPTION 'Lekarz ma juz wizyte w tym terminie (kolizja harmonogramu).';
    END IF;

    RETURN NEW;
END;
$$;


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: appointment_procedures; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.appointment_procedures (
    appointment_id bigint NOT NULL,
    procedure_id bigint NOT NULL,
    quantity integer DEFAULT 1 NOT NULL,
    unit_price numeric(10,2) NOT NULL,
    CONSTRAINT appointment_procedures_quantity_check CHECK ((quantity > 0)),
    CONSTRAINT appointment_procedures_unit_price_check CHECK ((unit_price >= (0)::numeric))
);


--
-- Name: appointments; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.appointments (
    id bigint NOT NULL,
    pet_id bigint NOT NULL,
    vet_id bigint NOT NULL,
    starts_at timestamp with time zone NOT NULL,
    ends_at timestamp with time zone NOT NULL,
    reason character varying(255) NOT NULL,
    status public.appointment_status DEFAULT 'scheduled'::public.appointment_status NOT NULL,
    notes text,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    CONSTRAINT appointments_time_order CHECK ((ends_at > starts_at))
);


--
-- Name: appointments_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

ALTER TABLE public.appointments ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.appointments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: clients; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.clients (
    user_id bigint NOT NULL,
    phone character varying(20),
    loyalty_points integer DEFAULT 0 NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    CONSTRAINT clients_loyalty_points_check CHECK ((loyalty_points >= 0))
);


--
-- Name: invoices; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.invoices (
    id bigint NOT NULL,
    appointment_id bigint NOT NULL,
    invoice_number character varying(30) NOT NULL,
    status public.invoice_status DEFAULT 'pending'::public.invoice_status NOT NULL,
    payment_method public.payment_method,
    issued_at timestamp with time zone DEFAULT now() NOT NULL,
    paid_at timestamp with time zone
);


--
-- Name: invoices_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

ALTER TABLE public.invoices ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.invoices_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: pets; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.pets (
    id bigint NOT NULL,
    client_id bigint NOT NULL,
    species_id smallint NOT NULL,
    name character varying(100) NOT NULL,
    breed character varying(100),
    sex public.animal_sex DEFAULT 'unknown'::public.animal_sex NOT NULL,
    birth_date date,
    weight_kg numeric(5,2),
    photo_path character varying(255),
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    CONSTRAINT pets_weight_kg_check CHECK (((weight_kg IS NULL) OR (weight_kg > (0)::numeric)))
);


--
-- Name: pets_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

ALTER TABLE public.pets ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.pets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: procedures; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.procedures (
    id bigint NOT NULL,
    name character varying(150) NOT NULL,
    description text,
    type public.procedure_type DEFAULT 'treatment'::public.procedure_type NOT NULL,
    base_price numeric(10,2) NOT NULL,
    CONSTRAINT procedures_base_price_check CHECK ((base_price >= (0)::numeric))
);


--
-- Name: procedures_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

ALTER TABLE public.procedures ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.procedures_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: species; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.species (
    id smallint NOT NULL,
    name character varying(50) NOT NULL
);


--
-- Name: species_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

ALTER TABLE public.species ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.species_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    email character varying(255) NOT NULL,
    password_hash character varying(255) NOT NULL,
    first_name character varying(100) NOT NULL,
    last_name character varying(100) NOT NULL,
    role public.user_role DEFAULT 'client'::public.user_role NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

ALTER TABLE public.users ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: vaccinations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.vaccinations (
    id bigint NOT NULL,
    pet_id bigint NOT NULL,
    vaccine_name character varying(150) NOT NULL,
    administered_at date NOT NULL,
    expires_at date NOT NULL,
    administered_by bigint,
    external_clinic character varying(150),
    CONSTRAINT vaccinations_validity_order CHECK ((expires_at > administered_at))
);


--
-- Name: vaccinations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

ALTER TABLE public.vaccinations ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.vaccinations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: vet_profiles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.vet_profiles (
    user_id bigint NOT NULL,
    license_number character varying(50) NOT NULL,
    title character varying(20) DEFAULT 'Dr'::character varying NOT NULL,
    room character varying(50),
    specialization character varying(100)
);


--
-- Name: vw_pet_vaccination_status; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW public.vw_pet_vaccination_status AS
 SELECT v.id AS vaccination_id,
    p.id AS pet_id,
    p.name AS pet_name,
    s.name AS species,
    (((cu.first_name)::text || ' '::text) || (cu.last_name)::text) AS owner_name,
    v.vaccine_name,
    v.administered_at,
    v.expires_at,
        CASE
            WHEN (v.expires_at < CURRENT_DATE) THEN 'overdue'::text
            ELSE 'valid'::text
        END AS status,
    COALESCE((((vu.first_name)::text || ' '::text) || (vu.last_name)::text), (v.external_clinic)::text) AS administered_by
   FROM (((((public.vaccinations v
     JOIN public.pets p ON ((p.id = v.pet_id)))
     JOIN public.species s ON ((s.id = p.species_id)))
     JOIN public.clients c ON ((c.user_id = p.client_id)))
     JOIN public.users cu ON ((cu.id = c.user_id)))
     LEFT JOIN public.users vu ON ((vu.id = v.administered_by)));


--
-- Name: vw_vet_weekly_schedule; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW public.vw_vet_weekly_schedule AS
 SELECT a.id AS appointment_id,
    a.starts_at,
    a.ends_at,
    a.status,
    a.reason,
    vu.id AS vet_id,
    (((((vp.title)::text || ' '::text) || (vu.first_name)::text) || ' '::text) || (vu.last_name)::text) AS vet_name,
    vp.room,
    p.id AS pet_id,
    p.name AS pet_name,
    s.name AS species,
    cu.id AS client_id,
    (((cu.first_name)::text || ' '::text) || (cu.last_name)::text) AS client_name,
    c.phone AS client_phone
   FROM ((((((public.appointments a
     JOIN public.vet_profiles vp ON ((vp.user_id = a.vet_id)))
     JOIN public.users vu ON ((vu.id = vp.user_id)))
     JOIN public.pets p ON ((p.id = a.pet_id)))
     JOIN public.species s ON ((s.id = p.species_id)))
     JOIN public.clients c ON ((c.user_id = p.client_id)))
     JOIN public.users cu ON ((cu.id = c.user_id)));


--
-- Data for Name: appointment_procedures; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.appointment_procedures (appointment_id, procedure_id, quantity, unit_price) FROM stdin;
1	1	1	80.00
1	2	1	60.00
1	5	1	50.00
2	3	1	90.00
4	6	1	150.00
4	9	1	70.00
\.


--
-- Data for Name: appointments; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.appointments (id, pet_id, vet_id, starts_at, ends_at, reason, status, notes, created_at) FROM stdin;
1	1	2	2026-06-15 07:00:00+00	2026-06-15 08:00:00+00	Coroczny przegląd	completed	Pacjent w dobrej kondycji.	2026-06-15 17:51:04.838357+00
2	3	2	2026-06-15 08:30:00+00	2026-06-15 09:00:00+00	Szczepienie	completed	\N	2026-06-15 17:51:04.838357+00
3	4	3	2026-06-15 09:00:00+00	2026-06-15 09:30:00+00	Kontrola dermatologiczna	confirmed	Świąd skóry, podejrzenie alergii.	2026-06-15 17:51:04.838357+00
4	5	4	2026-06-16 07:00:00+00	2026-06-16 07:45:00+00	Czyszczenie zębów	completed	\N	2026-06-15 17:51:04.838357+00
5	6	2	2026-06-16 10:00:00+00	2026-06-16 10:30:00+00	Szczepienie przeciw wściekliźnie	confirmed	\N	2026-06-15 17:51:04.838357+00
6	2	3	2026-06-17 06:30:00+00	2026-06-17 07:30:00+00	Zabieg chirurgiczny	scheduled	Wymagane badania przedoperacyjne.	2026-06-15 17:51:04.838357+00
7	7	4	2026-06-17 12:00:00+00	2026-06-17 12:30:00+00	Przegląd ogólny	scheduled	\N	2026-06-15 17:51:04.838357+00
8	8	2	2026-06-18 08:00:00+00	2026-06-18 08:30:00+00	Konsultacja	scheduled	\N	2026-06-15 17:51:04.838357+00
9	9	3	2026-06-18 11:00:00+00	2026-06-18 11:30:00+00	Badanie krwi	cancelled	Odwołane przez klienta.	2026-06-15 17:51:04.838357+00
10	1	4	2026-06-19 09:00:00+00	2026-06-19 10:00:00+00	USG jamy brzusznej	scheduled	\N	2026-06-15 17:51:04.838357+00
\.


--
-- Data for Name: clients; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.clients (user_id, phone, loyalty_points, created_at) FROM stdin;
5	600100200	150	2026-06-15 17:51:04.791307+00
6	512333444	40	2026-06-15 17:51:04.791307+00
7	698777888	220	2026-06-15 17:51:04.791307+00
8	731222111	0	2026-06-15 17:51:04.791307+00
9	605909808	95	2026-06-15 17:51:04.791307+00
\.


--
-- Data for Name: invoices; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.invoices (id, appointment_id, invoice_number, status, payment_method, issued_at, paid_at) FROM stdin;
1	1	FV-2026-0001	paid	card	2026-06-15 17:51:04.866712+00	2026-06-15 08:05:00+00
2	2	FV-2026-0002	pending	\N	2026-06-15 17:51:04.866712+00	\N
3	4	FV-2026-0003	paid	cash	2026-06-15 17:51:04.866712+00	2026-06-16 07:50:00+00
\.


--
-- Data for Name: pets; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.pets (id, client_id, species_id, name, breed, sex, birth_date, weight_kg, photo_path, created_at) FROM stdin;
1	5	1	Luna	Golden Retriever	female	2021-04-12	28.50	\N	2026-06-15 17:51:04.816201+00
2	5	1	Reksio	Owczarek niemiecki	male	2019-09-01	34.20	\N	2026-06-15 17:51:04.816201+00
3	6	2	Mruczek	Dachowiec	male	2020-06-20	4.80	\N	2026-06-15 17:51:04.816201+00
4	7	3	Coco	Baran francuski	female	2022-02-10	2.10	\N	2026-06-15 17:51:04.816201+00
5	7	1	Maks	Labrador	male	2018-11-05	31.00	\N	2026-06-15 17:51:04.816201+00
6	8	1	Tofik	Beagle	male	2023-01-30	12.40	\N	2026-06-15 17:51:04.816201+00
7	8	2	Pusia	Brytyjski krótkowłosy	female	2021-07-22	5.30	\N	2026-06-15 17:51:04.816201+00
8	9	5	Gucio	Nimfa	male	2022-05-18	0.10	\N	2026-06-15 17:51:04.816201+00
9	9	4	Filip	Syryjski	male	2024-03-03	0.15	\N	2026-06-15 17:51:04.816201+00
\.


--
-- Data for Name: procedures; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.procedures (id, name, description, type, base_price) FROM stdin;
1	Przegląd ogólny	Coroczne badanie fizykalne i kontrola stanu zdrowia	treatment	80.00
2	Szczepienie przeciw wściekliźnie	Obowiązkowa dawka przypominająca	medication	60.00
3	Szczepienie DHPP	Nosówka, parwowiroza, zapalenie wątroby	medication	90.00
4	Odrobaczanie	Tabletka przeciw pasożytom wewnętrznym	medication	45.00
5	Leczenie przeciw pchłom	Preparat na pchły i kleszcze	medication	50.00
6	Czyszczenie zębów	Usuwanie kamienia nazębnego w narkozie	treatment	150.00
7	Zabieg chirurgiczny	Standardowy zabieg operacyjny	treatment	400.00
8	USG jamy brzusznej	Badanie ultrasonograficzne	treatment	120.00
9	Badanie krwi	Morfologia i biochemia	treatment	70.00
10	Konsultacja dermatologiczna	Diagnostyka chorób skóry	treatment	100.00
\.


--
-- Data for Name: species; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.species (id, name) FROM stdin;
1	Pies
2	Kot
3	Królik
4	Chomik
5	Papuga
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.users (id, email, password_hash, first_name, last_name, role, is_active, created_at, updated_at) FROM stdin;
1	recepcja@vetclinic.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Anna	Kowalska	admin	t	2026-06-15 17:51:04.767629+00	2026-06-15 17:51:04.767629+00
2	p.nowak@vetclinic.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Piotr	Nowak	vet	t	2026-06-15 17:51:04.767629+00	2026-06-15 17:51:04.767629+00
3	m.wisniewska@vetclinic.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Magdalena	Wiśniewska	vet	t	2026-06-15 17:51:04.767629+00	2026-06-15 17:51:04.767629+00
4	t.lewandowski@vetclinic.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Tomasz	Lewandowski	vet	t	2026-06-15 17:51:04.767629+00	2026-06-15 17:51:04.767629+00
5	robert.lis@example.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Robert	Lis	client	t	2026-06-15 17:51:04.767629+00	2026-06-15 17:51:04.767629+00
6	k.wojcik@example.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Katarzyna	Wójcik	client	t	2026-06-15 17:51:04.767629+00	2026-06-15 17:51:04.767629+00
7	jan.kowalczyk@example.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Jan	Kowalczyk	client	t	2026-06-15 17:51:04.767629+00	2026-06-15 17:51:04.767629+00
8	sara.jankowska@example.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Sara	Jankowska	client	t	2026-06-15 17:51:04.767629+00	2026-06-15 17:51:04.767629+00
9	m.zielinski@example.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Marek	Zieliński	client	t	2026-06-15 17:51:04.767629+00	2026-06-15 17:51:04.767629+00
\.


--
-- Data for Name: vaccinations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.vaccinations (id, pet_id, vaccine_name, administered_at, expires_at, administered_by, external_clinic) FROM stdin;
1	1	Wścieklizna	2025-06-01	2026-06-01	2	\N
2	1	DHPP	2025-12-10	2026-12-10	2	\N
3	3	Wścieklizna	2025-08-15	2026-08-15	3	\N
4	5	Wścieklizna	2024-05-15	2025-05-15	\N	Przychodnia City Pets
5	2	DHPP	2026-01-10	2027-01-10	2	\N
6	4	Myksomatoza	2025-03-01	2026-03-01	3	\N
7	6	Wścieklizna	2025-11-20	2026-11-20	2	\N
\.


--
-- Data for Name: vet_profiles; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.vet_profiles (user_id, license_number, title, room, specialization) FROM stdin;
2	LIC-100234	Dr	Gabinet 1	Chirurgia
3	LIC-100871	Dr	Gabinet 2	Dermatologia
4	LIC-101455	Dr	Gabinet 3	Stomatologia
\.


--
-- Name: appointments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.appointments_id_seq', 10, true);


--
-- Name: invoices_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.invoices_id_seq', 3, true);


--
-- Name: pets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.pets_id_seq', 9, true);


--
-- Name: procedures_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.procedures_id_seq', 10, true);


--
-- Name: species_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.species_id_seq', 5, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.users_id_seq', 9, true);


--
-- Name: vaccinations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.vaccinations_id_seq', 7, true);


--
-- Name: appointment_procedures appointment_procedures_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.appointment_procedures
    ADD CONSTRAINT appointment_procedures_pkey PRIMARY KEY (appointment_id, procedure_id);


--
-- Name: appointments appointments_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.appointments
    ADD CONSTRAINT appointments_pkey PRIMARY KEY (id);


--
-- Name: clients clients_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (user_id);


--
-- Name: invoices invoices_appointment_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.invoices
    ADD CONSTRAINT invoices_appointment_id_key UNIQUE (appointment_id);


--
-- Name: invoices invoices_invoice_number_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.invoices
    ADD CONSTRAINT invoices_invoice_number_key UNIQUE (invoice_number);


--
-- Name: invoices invoices_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.invoices
    ADD CONSTRAINT invoices_pkey PRIMARY KEY (id);


--
-- Name: pets pets_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pets
    ADD CONSTRAINT pets_pkey PRIMARY KEY (id);


--
-- Name: procedures procedures_name_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.procedures
    ADD CONSTRAINT procedures_name_key UNIQUE (name);


--
-- Name: procedures procedures_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.procedures
    ADD CONSTRAINT procedures_pkey PRIMARY KEY (id);


--
-- Name: species species_name_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.species
    ADD CONSTRAINT species_name_key UNIQUE (name);


--
-- Name: species species_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.species
    ADD CONSTRAINT species_pkey PRIMARY KEY (id);


--
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: vaccinations vaccinations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vaccinations
    ADD CONSTRAINT vaccinations_pkey PRIMARY KEY (id);


--
-- Name: vet_profiles vet_profiles_license_number_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vet_profiles
    ADD CONSTRAINT vet_profiles_license_number_key UNIQUE (license_number);


--
-- Name: vet_profiles vet_profiles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vet_profiles
    ADD CONSTRAINT vet_profiles_pkey PRIMARY KEY (user_id);


--
-- Name: idx_appointment_procedures_procedure; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_appointment_procedures_procedure ON public.appointment_procedures USING btree (procedure_id);


--
-- Name: idx_appointments_pet; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_appointments_pet ON public.appointments USING btree (pet_id);


--
-- Name: idx_appointments_starts_at; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_appointments_starts_at ON public.appointments USING btree (starts_at);


--
-- Name: idx_appointments_vet; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_appointments_vet ON public.appointments USING btree (vet_id);


--
-- Name: idx_pets_client; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_pets_client ON public.pets USING btree (client_id);


--
-- Name: idx_pets_species; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_pets_species ON public.pets USING btree (species_id);


--
-- Name: idx_vaccinations_pet; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_vaccinations_pet ON public.vaccinations USING btree (pet_id);


--
-- Name: appointments trg_prevent_double_booking; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER trg_prevent_double_booking BEFORE INSERT OR UPDATE ON public.appointments FOR EACH ROW EXECUTE FUNCTION public.fn_prevent_double_booking();


--
-- Name: appointment_procedures appointment_procedures_appointment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.appointment_procedures
    ADD CONSTRAINT appointment_procedures_appointment_id_fkey FOREIGN KEY (appointment_id) REFERENCES public.appointments(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: appointment_procedures appointment_procedures_procedure_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.appointment_procedures
    ADD CONSTRAINT appointment_procedures_procedure_id_fkey FOREIGN KEY (procedure_id) REFERENCES public.procedures(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: appointments appointments_pet_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.appointments
    ADD CONSTRAINT appointments_pet_id_fkey FOREIGN KEY (pet_id) REFERENCES public.pets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: appointments appointments_vet_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.appointments
    ADD CONSTRAINT appointments_vet_id_fkey FOREIGN KEY (vet_id) REFERENCES public.vet_profiles(user_id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: clients clients_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.clients
    ADD CONSTRAINT clients_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: invoices invoices_appointment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.invoices
    ADD CONSTRAINT invoices_appointment_id_fkey FOREIGN KEY (appointment_id) REFERENCES public.appointments(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: pets pets_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pets
    ADD CONSTRAINT pets_client_id_fkey FOREIGN KEY (client_id) REFERENCES public.clients(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: pets pets_species_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pets
    ADD CONSTRAINT pets_species_id_fkey FOREIGN KEY (species_id) REFERENCES public.species(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: vaccinations vaccinations_administered_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vaccinations
    ADD CONSTRAINT vaccinations_administered_by_fkey FOREIGN KEY (administered_by) REFERENCES public.vet_profiles(user_id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: vaccinations vaccinations_pet_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vaccinations
    ADD CONSTRAINT vaccinations_pet_id_fkey FOREIGN KEY (pet_id) REFERENCES public.pets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: vet_profiles vet_profiles_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vet_profiles
    ADD CONSTRAINT vet_profiles_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict 1ww5gV6ddAZaNpnxQlFe9qMCZTuyQsjCWgQRzAIedrrxFWG3YhFErfFwcpkRhzI

