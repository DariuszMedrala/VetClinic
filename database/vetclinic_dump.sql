--
-- PostgreSQL database dump
--

\restrict CCtSmuRlduiaJpJnHk3DGQszD5KyAt5AjJLqnJaIvuFFwVKZyAsCITlwMjyfKiC

-- Dumped from database version 16.14
-- Dumped by pg_dump version 16.14

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

ALTER TABLE IF EXISTS ONLY public.visit_reasons DROP CONSTRAINT IF EXISTS visit_reasons_clinic_id_fkey;
ALTER TABLE IF EXISTS ONLY public.vet_profiles DROP CONSTRAINT IF EXISTS vet_profiles_user_id_fkey;
ALTER TABLE IF EXISTS ONLY public.vet_availability DROP CONSTRAINT IF EXISTS vet_availability_vet_id_fkey;
ALTER TABLE IF EXISTS ONLY public.vaccine_types DROP CONSTRAINT IF EXISTS vaccine_types_clinic_id_fkey;
ALTER TABLE IF EXISTS ONLY public.vaccinations DROP CONSTRAINT IF EXISTS vaccinations_pet_id_fkey;
ALTER TABLE IF EXISTS ONLY public.vaccinations DROP CONSTRAINT IF EXISTS vaccinations_administered_by_fkey;
ALTER TABLE IF EXISTS ONLY public.users DROP CONSTRAINT IF EXISTS users_clinic_id_fkey;
ALTER TABLE IF EXISTS ONLY public.procedures DROP CONSTRAINT IF EXISTS procedures_clinic_id_fkey;
ALTER TABLE IF EXISTS ONLY public.pets DROP CONSTRAINT IF EXISTS pets_species_id_fkey;
ALTER TABLE IF EXISTS ONLY public.pets DROP CONSTRAINT IF EXISTS pets_client_id_fkey;
ALTER TABLE IF EXISTS ONLY public.password_resets DROP CONSTRAINT IF EXISTS password_resets_user_id_fkey;
ALTER TABLE IF EXISTS ONLY public.loyalty_tiers DROP CONSTRAINT IF EXISTS loyalty_tiers_clinic_id_fkey;
ALTER TABLE IF EXISTS ONLY public.loyalty_settings DROP CONSTRAINT IF EXISTS loyalty_settings_clinic_id_fkey;
ALTER TABLE IF EXISTS ONLY public.invoices DROP CONSTRAINT IF EXISTS invoices_appointment_id_fkey;
ALTER TABLE IF EXISTS ONLY public.clients DROP CONSTRAINT IF EXISTS clients_user_id_fkey;
ALTER TABLE IF EXISTS ONLY public.appointments DROP CONSTRAINT IF EXISTS appointments_vet_id_fkey;
ALTER TABLE IF EXISTS ONLY public.appointments DROP CONSTRAINT IF EXISTS appointments_pet_id_fkey;
ALTER TABLE IF EXISTS ONLY public.appointment_procedures DROP CONSTRAINT IF EXISTS appointment_procedures_vaccine_type_id_fkey;
ALTER TABLE IF EXISTS ONLY public.appointment_procedures DROP CONSTRAINT IF EXISTS appointment_procedures_procedure_id_fkey;
ALTER TABLE IF EXISTS ONLY public.appointment_procedures DROP CONSTRAINT IF EXISTS appointment_procedures_appointment_id_fkey;
DROP TRIGGER IF EXISTS trg_prevent_double_booking ON public.appointments;
DROP INDEX IF EXISTS public.idx_vaccinations_pet;
DROP INDEX IF EXISTS public.idx_pets_species;
DROP INDEX IF EXISTS public.idx_pets_client;
DROP INDEX IF EXISTS public.idx_password_resets_token;
DROP INDEX IF EXISTS public.idx_login_attempts_ip;
DROP INDEX IF EXISTS public.idx_appointments_vet;
DROP INDEX IF EXISTS public.idx_appointments_starts_at;
DROP INDEX IF EXISTS public.idx_appointments_pet;
DROP INDEX IF EXISTS public.idx_appointment_procedures_procedure;
ALTER TABLE IF EXISTS ONLY public.visit_reasons DROP CONSTRAINT IF EXISTS visit_reasons_pkey;
ALTER TABLE IF EXISTS ONLY public.visit_reasons DROP CONSTRAINT IF EXISTS visit_reasons_clinic_id_name_key;
ALTER TABLE IF EXISTS ONLY public.vet_profiles DROP CONSTRAINT IF EXISTS vet_profiles_pkey;
ALTER TABLE IF EXISTS ONLY public.vet_profiles DROP CONSTRAINT IF EXISTS vet_profiles_license_number_key;
ALTER TABLE IF EXISTS ONLY public.vet_availability DROP CONSTRAINT IF EXISTS vet_availability_vet_id_weekday_key;
ALTER TABLE IF EXISTS ONLY public.vet_availability DROP CONSTRAINT IF EXISTS vet_availability_pkey;
ALTER TABLE IF EXISTS ONLY public.vaccine_types DROP CONSTRAINT IF EXISTS vaccine_types_pkey;
ALTER TABLE IF EXISTS ONLY public.vaccine_types DROP CONSTRAINT IF EXISTS vaccine_types_clinic_id_name_key;
ALTER TABLE IF EXISTS ONLY public.vaccinations DROP CONSTRAINT IF EXISTS vaccinations_pkey;
ALTER TABLE IF EXISTS ONLY public.users DROP CONSTRAINT IF EXISTS users_pkey;
ALTER TABLE IF EXISTS ONLY public.users DROP CONSTRAINT IF EXISTS users_email_key;
ALTER TABLE IF EXISTS ONLY public.species DROP CONSTRAINT IF EXISTS species_pkey;
ALTER TABLE IF EXISTS ONLY public.species DROP CONSTRAINT IF EXISTS species_name_key;
ALTER TABLE IF EXISTS ONLY public.procedures DROP CONSTRAINT IF EXISTS procedures_pkey;
ALTER TABLE IF EXISTS ONLY public.procedures DROP CONSTRAINT IF EXISTS procedures_clinic_name_key;
ALTER TABLE IF EXISTS ONLY public.pets DROP CONSTRAINT IF EXISTS pets_pkey;
ALTER TABLE IF EXISTS ONLY public.password_resets DROP CONSTRAINT IF EXISTS password_resets_pkey;
ALTER TABLE IF EXISTS ONLY public.loyalty_tiers DROP CONSTRAINT IF EXISTS loyalty_tiers_pkey;
ALTER TABLE IF EXISTS ONLY public.loyalty_tiers DROP CONSTRAINT IF EXISTS loyalty_tiers_clinic_id_min_points_key;
ALTER TABLE IF EXISTS ONLY public.loyalty_settings DROP CONSTRAINT IF EXISTS loyalty_settings_pkey;
ALTER TABLE IF EXISTS ONLY public.login_attempts DROP CONSTRAINT IF EXISTS login_attempts_pkey;
ALTER TABLE IF EXISTS ONLY public.invoices DROP CONSTRAINT IF EXISTS invoices_pkey;
ALTER TABLE IF EXISTS ONLY public.invoices DROP CONSTRAINT IF EXISTS invoices_invoice_number_key;
ALTER TABLE IF EXISTS ONLY public.invoices DROP CONSTRAINT IF EXISTS invoices_appointment_id_key;
ALTER TABLE IF EXISTS ONLY public.clinics DROP CONSTRAINT IF EXISTS clinics_pkey;
ALTER TABLE IF EXISTS ONLY public.clinics DROP CONSTRAINT IF EXISTS clinics_name_key;
ALTER TABLE IF EXISTS ONLY public.clients DROP CONSTRAINT IF EXISTS clients_pkey;
ALTER TABLE IF EXISTS ONLY public.appointments DROP CONSTRAINT IF EXISTS appointments_pkey;
ALTER TABLE IF EXISTS ONLY public.appointment_procedures DROP CONSTRAINT IF EXISTS appointment_procedures_pkey;
DROP VIEW IF EXISTS public.vw_vet_weekly_schedule;
DROP VIEW IF EXISTS public.vw_pet_vaccination_status;
DROP TABLE IF EXISTS public.visit_reasons;
DROP TABLE IF EXISTS public.vet_profiles;
DROP TABLE IF EXISTS public.vet_availability;
DROP TABLE IF EXISTS public.vaccine_types;
DROP TABLE IF EXISTS public.vaccinations;
DROP TABLE IF EXISTS public.users;
DROP TABLE IF EXISTS public.species;
DROP TABLE IF EXISTS public.procedures;
DROP TABLE IF EXISTS public.pets;
DROP TABLE IF EXISTS public.password_resets;
DROP TABLE IF EXISTS public.loyalty_tiers;
DROP TABLE IF EXISTS public.loyalty_settings;
DROP TABLE IF EXISTS public.login_attempts;
DROP TABLE IF EXISTS public.invoices;
DROP TABLE IF EXISTS public.clinics;
DROP TABLE IF EXISTS public.clients;
DROP TABLE IF EXISTS public.appointments;
DROP TABLE IF EXISTS public.appointment_procedures;
DROP FUNCTION IF EXISTS public.fn_prevent_double_booking();
DROP FUNCTION IF EXISTS public.fn_calculate_invoice_total(p_appointment_id bigint);
DROP TYPE IF EXISTS public.user_role;
DROP TYPE IF EXISTS public.procedure_type;
DROP TYPE IF EXISTS public.payment_method;
DROP TYPE IF EXISTS public.invoice_status;
DROP TYPE IF EXISTS public.appointment_status;
DROP TYPE IF EXISTS public.animal_sex;
--
-- Name: animal_sex; Type: TYPE; Schema: public; Owner: vetclinic
--

CREATE TYPE public.animal_sex AS ENUM (
    'male',
    'female',
    'unknown'
);


ALTER TYPE public.animal_sex OWNER TO vetclinic;

--
-- Name: appointment_status; Type: TYPE; Schema: public; Owner: vetclinic
--

CREATE TYPE public.appointment_status AS ENUM (
    'scheduled',
    'confirmed',
    'in_progress',
    'completed',
    'cancelled'
);


ALTER TYPE public.appointment_status OWNER TO vetclinic;

--
-- Name: invoice_status; Type: TYPE; Schema: public; Owner: vetclinic
--

CREATE TYPE public.invoice_status AS ENUM (
    'pending',
    'paid',
    'cancelled'
);


ALTER TYPE public.invoice_status OWNER TO vetclinic;

--
-- Name: payment_method; Type: TYPE; Schema: public; Owner: vetclinic
--

CREATE TYPE public.payment_method AS ENUM (
    'card',
    'cash',
    'insurance'
);


ALTER TYPE public.payment_method OWNER TO vetclinic;

--
-- Name: procedure_type; Type: TYPE; Schema: public; Owner: vetclinic
--

CREATE TYPE public.procedure_type AS ENUM (
    'treatment',
    'medication'
);


ALTER TYPE public.procedure_type OWNER TO vetclinic;

--
-- Name: user_role; Type: TYPE; Schema: public; Owner: vetclinic
--

CREATE TYPE public.user_role AS ENUM (
    'client',
    'vet',
    'admin'
);


ALTER TYPE public.user_role OWNER TO vetclinic;

--
-- Name: fn_calculate_invoice_total(bigint); Type: FUNCTION; Schema: public; Owner: vetclinic
--

CREATE FUNCTION public.fn_calculate_invoice_total(p_appointment_id bigint) RETURNS numeric
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_subtotal      NUMERIC(10, 2);
    v_points        INTEGER;
    v_clinic        BIGINT;
    v_discount_rate NUMERIC(6, 4) := 0;
BEGIN
    SELECT COALESCE(SUM(ap.unit_price * ap.quantity), 0) INTO v_subtotal
    FROM appointment_procedures ap WHERE ap.appointment_id = p_appointment_id;

    SELECT c.loyalty_points, u.clinic_id INTO v_points, v_clinic
    FROM appointments a
    JOIN pets p ON p.id = a.pet_id
    JOIN clients c ON c.user_id = p.client_id
    JOIN users u ON u.id = c.user_id
    WHERE a.id = p_appointment_id;

    SELECT COALESCE(MAX(discount_percent), 0) / 100 INTO v_discount_rate
    FROM loyalty_tiers
    WHERE clinic_id = v_clinic AND min_points <= COALESCE(v_points, 0);

    RETURN ROUND(v_subtotal * (1 - v_discount_rate), 2);
END;
$$;


ALTER FUNCTION public.fn_calculate_invoice_total(p_appointment_id bigint) OWNER TO vetclinic;

--
-- Name: fn_prevent_double_booking(); Type: FUNCTION; Schema: public; Owner: vetclinic
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


ALTER FUNCTION public.fn_prevent_double_booking() OWNER TO vetclinic;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: appointment_procedures; Type: TABLE; Schema: public; Owner: vetclinic
--

CREATE TABLE public.appointment_procedures (
    appointment_id bigint NOT NULL,
    procedure_id bigint,
    quantity integer DEFAULT 1 NOT NULL,
    unit_price numeric(10,2) NOT NULL,
    id bigint NOT NULL,
    vaccine_type_id bigint,
    CONSTRAINT ap_line_kind CHECK (((procedure_id IS NOT NULL) <> (vaccine_type_id IS NOT NULL))),
    CONSTRAINT appointment_procedures_quantity_check CHECK ((quantity > 0)),
    CONSTRAINT appointment_procedures_unit_price_check CHECK ((unit_price >= (0)::numeric))
);


ALTER TABLE public.appointment_procedures OWNER TO vetclinic;

--
-- Name: appointment_procedures_id_seq; Type: SEQUENCE; Schema: public; Owner: vetclinic
--

ALTER TABLE public.appointment_procedures ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.appointment_procedures_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: appointments; Type: TABLE; Schema: public; Owner: vetclinic
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


ALTER TABLE public.appointments OWNER TO vetclinic;

--
-- Name: appointments_id_seq; Type: SEQUENCE; Schema: public; Owner: vetclinic
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
-- Name: clients; Type: TABLE; Schema: public; Owner: vetclinic
--

CREATE TABLE public.clients (
    user_id bigint NOT NULL,
    phone character varying(20),
    loyalty_points integer DEFAULT 0 NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    CONSTRAINT clients_loyalty_points_check CHECK ((loyalty_points >= 0))
);


ALTER TABLE public.clients OWNER TO vetclinic;

--
-- Name: clinics; Type: TABLE; Schema: public; Owner: vetclinic
--

CREATE TABLE public.clinics (
    id bigint NOT NULL,
    name character varying(150) NOT NULL,
    address character varying(255) NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    join_code character varying(60) NOT NULL
);


ALTER TABLE public.clinics OWNER TO vetclinic;

--
-- Name: clinics_id_seq; Type: SEQUENCE; Schema: public; Owner: vetclinic
--

ALTER TABLE public.clinics ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.clinics_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: invoices; Type: TABLE; Schema: public; Owner: vetclinic
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


ALTER TABLE public.invoices OWNER TO vetclinic;

--
-- Name: invoices_id_seq; Type: SEQUENCE; Schema: public; Owner: vetclinic
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
-- Name: login_attempts; Type: TABLE; Schema: public; Owner: vetclinic
--

CREATE TABLE public.login_attempts (
    id bigint NOT NULL,
    ip character varying(45) NOT NULL,
    attempted_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.login_attempts OWNER TO vetclinic;

--
-- Name: login_attempts_id_seq; Type: SEQUENCE; Schema: public; Owner: vetclinic
--

ALTER TABLE public.login_attempts ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.login_attempts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: loyalty_settings; Type: TABLE; Schema: public; Owner: vetclinic
--

CREATE TABLE public.loyalty_settings (
    clinic_id bigint NOT NULL,
    points_per integer DEFAULT 1 NOT NULL,
    per_amount numeric(10,2) DEFAULT 10 NOT NULL,
    CONSTRAINT loyalty_settings_per_amount_check CHECK ((per_amount > (0)::numeric)),
    CONSTRAINT loyalty_settings_points_per_check CHECK ((points_per >= 0))
);


ALTER TABLE public.loyalty_settings OWNER TO vetclinic;

--
-- Name: loyalty_tiers; Type: TABLE; Schema: public; Owner: vetclinic
--

CREATE TABLE public.loyalty_tiers (
    id bigint NOT NULL,
    clinic_id bigint NOT NULL,
    min_points integer NOT NULL,
    discount_percent numeric(5,2) NOT NULL,
    CONSTRAINT loyalty_tiers_discount_percent_check CHECK (((discount_percent >= (0)::numeric) AND (discount_percent <= (100)::numeric))),
    CONSTRAINT loyalty_tiers_min_points_check CHECK ((min_points >= 0))
);


ALTER TABLE public.loyalty_tiers OWNER TO vetclinic;

--
-- Name: loyalty_tiers_id_seq; Type: SEQUENCE; Schema: public; Owner: vetclinic
--

ALTER TABLE public.loyalty_tiers ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.loyalty_tiers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: password_resets; Type: TABLE; Schema: public; Owner: vetclinic
--

CREATE TABLE public.password_resets (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    token_hash character(64) NOT NULL,
    expires_at timestamp with time zone NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.password_resets OWNER TO vetclinic;

--
-- Name: password_resets_id_seq; Type: SEQUENCE; Schema: public; Owner: vetclinic
--

ALTER TABLE public.password_resets ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.password_resets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: pets; Type: TABLE; Schema: public; Owner: vetclinic
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


ALTER TABLE public.pets OWNER TO vetclinic;

--
-- Name: pets_id_seq; Type: SEQUENCE; Schema: public; Owner: vetclinic
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
-- Name: procedures; Type: TABLE; Schema: public; Owner: vetclinic
--

CREATE TABLE public.procedures (
    id bigint NOT NULL,
    name character varying(150) NOT NULL,
    description text,
    type public.procedure_type DEFAULT 'treatment'::public.procedure_type NOT NULL,
    base_price numeric(10,2) NOT NULL,
    clinic_id bigint NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    CONSTRAINT procedures_base_price_check CHECK ((base_price >= (0)::numeric))
);


ALTER TABLE public.procedures OWNER TO vetclinic;

--
-- Name: procedures_id_seq; Type: SEQUENCE; Schema: public; Owner: vetclinic
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
-- Name: species; Type: TABLE; Schema: public; Owner: vetclinic
--

CREATE TABLE public.species (
    id smallint NOT NULL,
    name character varying(50) NOT NULL
);


ALTER TABLE public.species OWNER TO vetclinic;

--
-- Name: species_id_seq; Type: SEQUENCE; Schema: public; Owner: vetclinic
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
-- Name: users; Type: TABLE; Schema: public; Owner: vetclinic
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    email character varying(255) NOT NULL,
    password_hash character varying(255) NOT NULL,
    first_name character varying(100) NOT NULL,
    last_name character varying(100) NOT NULL,
    role public.user_role DEFAULT 'client'::public.user_role NOT NULL,
    clinic_id bigint NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.users OWNER TO vetclinic;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: vetclinic
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
-- Name: vaccinations; Type: TABLE; Schema: public; Owner: vetclinic
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


ALTER TABLE public.vaccinations OWNER TO vetclinic;

--
-- Name: vaccinations_id_seq; Type: SEQUENCE; Schema: public; Owner: vetclinic
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
-- Name: vaccine_types; Type: TABLE; Schema: public; Owner: vetclinic
--

CREATE TABLE public.vaccine_types (
    id bigint NOT NULL,
    clinic_id bigint NOT NULL,
    name character varying(150) NOT NULL,
    price numeric(10,2) NOT NULL,
    validity_months integer NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    CONSTRAINT vaccine_types_price_check CHECK ((price >= (0)::numeric)),
    CONSTRAINT vaccine_types_validity_months_check CHECK ((validity_months > 0))
);


ALTER TABLE public.vaccine_types OWNER TO vetclinic;

--
-- Name: vaccine_types_id_seq; Type: SEQUENCE; Schema: public; Owner: vetclinic
--

ALTER TABLE public.vaccine_types ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.vaccine_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: vet_availability; Type: TABLE; Schema: public; Owner: vetclinic
--

CREATE TABLE public.vet_availability (
    id bigint NOT NULL,
    vet_id bigint NOT NULL,
    weekday smallint NOT NULL,
    start_time time without time zone NOT NULL,
    end_time time without time zone NOT NULL,
    CONSTRAINT vet_availability_order CHECK ((end_time > start_time)),
    CONSTRAINT vet_availability_weekday_check CHECK (((weekday >= 1) AND (weekday <= 7)))
);


ALTER TABLE public.vet_availability OWNER TO vetclinic;

--
-- Name: vet_availability_id_seq; Type: SEQUENCE; Schema: public; Owner: vetclinic
--

ALTER TABLE public.vet_availability ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.vet_availability_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: vet_profiles; Type: TABLE; Schema: public; Owner: vetclinic
--

CREATE TABLE public.vet_profiles (
    user_id bigint NOT NULL,
    license_number character varying(50) NOT NULL,
    title character varying(20) DEFAULT 'Dr'::character varying NOT NULL,
    room character varying(50),
    specialization character varying(100)
);


ALTER TABLE public.vet_profiles OWNER TO vetclinic;

--
-- Name: visit_reasons; Type: TABLE; Schema: public; Owner: vetclinic
--

CREATE TABLE public.visit_reasons (
    id bigint NOT NULL,
    clinic_id bigint NOT NULL,
    name character varying(150) NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.visit_reasons OWNER TO vetclinic;

--
-- Name: visit_reasons_id_seq; Type: SEQUENCE; Schema: public; Owner: vetclinic
--

ALTER TABLE public.visit_reasons ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.visit_reasons_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: vw_pet_vaccination_status; Type: VIEW; Schema: public; Owner: vetclinic
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
    COALESCE((((vu.first_name)::text || ' '::text) || (vu.last_name)::text), (v.external_clinic)::text) AS administered_by,
    cu.clinic_id
   FROM (((((public.vaccinations v
     JOIN public.pets p ON ((p.id = v.pet_id)))
     JOIN public.species s ON ((s.id = p.species_id)))
     JOIN public.clients c ON ((c.user_id = p.client_id)))
     JOIN public.users cu ON ((cu.id = c.user_id)))
     LEFT JOIN public.users vu ON ((vu.id = v.administered_by)));


ALTER VIEW public.vw_pet_vaccination_status OWNER TO vetclinic;

--
-- Name: vw_vet_weekly_schedule; Type: VIEW; Schema: public; Owner: vetclinic
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
    c.phone AS client_phone,
    vu.clinic_id,
    p.breed,
    a.notes
   FROM ((((((public.appointments a
     JOIN public.vet_profiles vp ON ((vp.user_id = a.vet_id)))
     JOIN public.users vu ON ((vu.id = vp.user_id)))
     JOIN public.pets p ON ((p.id = a.pet_id)))
     JOIN public.species s ON ((s.id = p.species_id)))
     JOIN public.clients c ON ((c.user_id = p.client_id)))
     JOIN public.users cu ON ((cu.id = c.user_id)));


ALTER VIEW public.vw_vet_weekly_schedule OWNER TO vetclinic;

--
-- Data for Name: appointment_procedures; Type: TABLE DATA; Schema: public; Owner: vetclinic
--

COPY public.appointment_procedures (appointment_id, procedure_id, quantity, unit_price, id, vaccine_type_id) FROM stdin;
1	1	1	80.00	1	\N
1	2	1	60.00	2	\N
1	5	1	50.00	3	\N
2	3	1	90.00	4	\N
4	6	1	150.00	5	\N
4	9	1	70.00	6	\N
12	6	1	150.00	7	\N
19	6	1	150.00	8	\N
\.


--
-- Data for Name: appointments; Type: TABLE DATA; Schema: public; Owner: vetclinic
--

COPY public.appointments (id, pet_id, vet_id, starts_at, ends_at, reason, status, notes, created_at) FROM stdin;
1	1	2	2026-06-15 07:00:00+00	2026-06-15 08:00:00+00	Coroczny przegląd	completed	Pacjent w dobrej kondycji.	2026-06-18 07:59:53.549177+00
2	3	2	2026-06-15 08:30:00+00	2026-06-15 09:00:00+00	Szczepienie	completed	\N	2026-06-18 07:59:53.549177+00
3	4	3	2026-06-15 09:00:00+00	2026-06-15 09:30:00+00	Kontrola dermatologiczna	confirmed	Świąd skóry, podejrzenie alergii.	2026-06-18 07:59:53.549177+00
4	5	4	2026-06-16 07:00:00+00	2026-06-16 07:45:00+00	Czyszczenie zębów	completed	\N	2026-06-18 07:59:53.549177+00
5	6	2	2026-06-16 10:00:00+00	2026-06-16 10:30:00+00	Szczepienie przeciw wściekliźnie	confirmed	\N	2026-06-18 07:59:53.549177+00
6	2	3	2026-06-17 06:30:00+00	2026-06-17 07:30:00+00	Zabieg chirurgiczny	scheduled	Wymagane badania przedoperacyjne.	2026-06-18 07:59:53.549177+00
7	7	4	2026-06-17 12:00:00+00	2026-06-17 12:30:00+00	Przegląd ogólny	scheduled	\N	2026-06-18 07:59:53.549177+00
9	9	3	2026-06-18 11:00:00+00	2026-06-18 11:30:00+00	Badanie krwi	cancelled	Odwołane przez klienta.	2026-06-18 07:59:53.549177+00
11	10	11	2026-06-18 08:00:00+00	2026-06-18 08:30:00+00	Pierwsza wizyta	confirmed	\N	2026-06-18 07:59:53.549177+00
13	6	2	2026-06-18 12:30:00+00	2026-06-18 13:15:00+00	Szczepienie	scheduled	\N	2026-06-18 08:21:43.164024+00
8	8	2	2026-06-18 08:00:00+00	2026-06-18 08:30:00+00	Konsultacja	completed	Halo Halo	2026-06-18 07:59:53.549177+00
17	4	2	2026-06-18 14:00:00+00	2026-06-18 15:00:00+00	Szczepienie	scheduled	\N	2026-06-18 14:55:16.593911+00
12	4	2	2026-06-18 11:00:00+00	2026-06-18 12:00:00+00	Szczepienie	completed	\N	2026-06-18 08:21:23.468013+00
19	4	3	2026-06-18 14:00:00+00	2026-06-18 15:00:00+00	Szczepienie	completed	\N	2026-06-18 14:58:59.458142+00
10	1	4	2026-06-19 09:00:00+00	2026-06-19 10:00:00+00	USG jamy brzusznej	scheduled	\N	2026-06-18 07:59:53.549177+00
21	4	4	2026-06-19 07:00:00+00	2026-06-19 08:00:00+00	Kontrola pooperacyjna	scheduled	\N	2026-06-18 15:25:01.270673+00
\.


--
-- Data for Name: clients; Type: TABLE DATA; Schema: public; Owner: vetclinic
--

COPY public.clients (user_id, phone, loyalty_points, created_at) FROM stdin;
5	600100200	150	2026-06-18 07:59:53.502866+00
8	731222111	0	2026-06-18 07:59:53.502866+00
9	605909808	95	2026-06-18 07:59:53.502866+00
12	501601701	60	2026-06-18 07:59:53.502866+00
14	\N	0	2026-06-18 14:02:43.002352+00
6	512333444	40	2026-06-18 07:59:53.502866+00
7	698777888	232	2026-06-18 07:59:53.502866+00
\.


--
-- Data for Name: clinics; Type: TABLE DATA; Schema: public; Owner: vetclinic
--

COPY public.clinics (id, name, address, created_at, join_code) FROM stdin;
2	Lecznica Wesoła Łapa	ul. Polna 5, 30-002 Kraków	2026-06-18 07:59:53.456436+00	WESOLA-2024
1	Przychodnia Centrum	ul. Główna 1, 00-001 Warszawa	2026-06-18 07:59:53.456436+00	CENTRUM-2024
\.


--
-- Data for Name: invoices; Type: TABLE DATA; Schema: public; Owner: vetclinic
--

COPY public.invoices (id, appointment_id, invoice_number, status, payment_method, issued_at, paid_at) FROM stdin;
1	1	FV-2026-0001	paid	card	2026-06-18 07:59:53.577104+00	2026-06-15 08:05:00+00
3	4	FV-2026-0003	paid	cash	2026-06-18 07:59:53.577104+00	2026-06-16 07:50:00+00
2	2	FV-2026-0002	pending	\N	2026-06-18 07:59:53.577104+00	\N
5	12	FV-2026-0004	paid	card	2026-06-18 14:57:20.729352+00	2026-06-18 14:57:56.352567+00
6	19	FV-2026-0005	pending	\N	2026-06-18 14:59:33.772476+00	\N
\.


--
-- Data for Name: login_attempts; Type: TABLE DATA; Schema: public; Owner: vetclinic
--

COPY public.login_attempts (id, ip, attempted_at) FROM stdin;
\.


--
-- Data for Name: loyalty_settings; Type: TABLE DATA; Schema: public; Owner: vetclinic
--

COPY public.loyalty_settings (clinic_id, points_per, per_amount) FROM stdin;
2	1	10.00
1	1	10.00
\.


--
-- Data for Name: loyalty_tiers; Type: TABLE DATA; Schema: public; Owner: vetclinic
--

COPY public.loyalty_tiers (id, clinic_id, min_points, discount_percent) FROM stdin;
1	2	100	10.00
2	2	200	15.00
3	2	500	20.00
4	1	100	10.00
5	1	200	15.00
6	1	500	20.00
\.


--
-- Data for Name: password_resets; Type: TABLE DATA; Schema: public; Owner: vetclinic
--

COPY public.password_resets (id, user_id, token_hash, expires_at, created_at) FROM stdin;
\.


--
-- Data for Name: pets; Type: TABLE DATA; Schema: public; Owner: vetclinic
--

COPY public.pets (id, client_id, species_id, name, breed, sex, birth_date, weight_kg, photo_path, created_at) FROM stdin;
1	5	1	Luna	Golden Retriever	female	2021-04-12	28.50	\N	2026-06-18 07:59:53.524768+00
2	5	1	Reksio	Owczarek niemiecki	male	2019-09-01	34.20	\N	2026-06-18 07:59:53.524768+00
3	6	2	Mruczek	Dachowiec	male	2020-06-20	4.80	\N	2026-06-18 07:59:53.524768+00
4	7	3	Coco	Baran francuski	female	2022-02-10	2.10	\N	2026-06-18 07:59:53.524768+00
5	7	1	Maks	Labrador	male	2018-11-05	31.00	\N	2026-06-18 07:59:53.524768+00
6	8	1	Tofik	Beagle	male	2023-01-30	12.40	\N	2026-06-18 07:59:53.524768+00
7	8	2	Pusia	Brytyjski krótkowłosy	female	2021-07-22	5.30	\N	2026-06-18 07:59:53.524768+00
8	9	5	Gucio	Nimfa	male	2022-05-18	0.10	\N	2026-06-18 07:59:53.524768+00
9	9	4	Filip	Syryjski	male	2024-03-03	0.15	\N	2026-06-18 07:59:53.524768+00
10	12	1	Burek	Kundelek	male	2020-03-15	18.00	\N	2026-06-18 07:59:53.524768+00
11	8	4	Lun	brak	unknown	2025-01-01	1.00	/assets/uploads/pets/pet_36b09b7f53aaf38a.png	2026-06-18 12:21:51.9921+00
\.


--
-- Data for Name: procedures; Type: TABLE DATA; Schema: public; Owner: vetclinic
--

COPY public.procedures (id, name, description, type, base_price, clinic_id, is_active) FROM stdin;
1	Przegląd ogólny	Coroczne badanie fizykalne i kontrola stanu zdrowia	treatment	80.00	1	t
4	Odrobaczanie	Tabletka przeciw pasożytom wewnętrznym	medication	45.00	1	t
5	Leczenie przeciw pchłom	Preparat na pchły i kleszcze	medication	50.00	1	t
6	Czyszczenie zębów	Usuwanie kamienia nazębnego w narkozie	treatment	150.00	1	t
7	Zabieg chirurgiczny	Standardowy zabieg operacyjny	treatment	400.00	1	t
8	USG jamy brzusznej	Badanie ultrasonograficzne	treatment	120.00	1	t
10	Konsultacja dermatologiczna	Diagnostyka chorób skóry	treatment	100.00	1	t
11	Przegląd ogólny	Coroczne badanie fizykalne i kontrola stanu zdrowia	treatment	80.00	2	t
12	Szczepienie przeciw wściekliźnie	Obowiązkowa dawka przypominająca	medication	60.00	2	t
13	Szczepienie DHPP	Nosówka, parwowiroza, zapalenie wątroby	medication	90.00	2	t
14	Odrobaczanie	Tabletka przeciw pasożytom wewnętrznym	medication	45.00	2	t
15	Leczenie przeciw pchłom	Preparat na pchły i kleszcze	medication	50.00	2	t
16	Czyszczenie zębów	Usuwanie kamienia nazębnego w narkozie	treatment	150.00	2	t
17	Zabieg chirurgiczny	Standardowy zabieg operacyjny	treatment	400.00	2	t
18	USG jamy brzusznej	Badanie ultrasonograficzne	treatment	120.00	2	t
19	Badanie krwi	Morfologia i biochemia	treatment	70.00	2	t
20	Konsultacja dermatologiczna	Diagnostyka chorób skóry	treatment	100.00	2	t
9	Badanie krwi	Morfologia i biochemia	treatment	70.00	1	f
2	Szczepienie przeciw wściekliźnie	Obowiązkowa dawka przypominająca	medication	60.00	1	f
3	Szczepienie DHPP	Nosówka, parwowiroza, zapalenie wątroby	medication	90.00	1	f
\.


--
-- Data for Name: species; Type: TABLE DATA; Schema: public; Owner: vetclinic
--

COPY public.species (id, name) FROM stdin;
1	Pies
2	Kot
3	Królik
4	Chomik
5	Papuga
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: vetclinic
--

COPY public.users (id, email, password_hash, first_name, last_name, role, clinic_id, is_active, created_at, updated_at) FROM stdin;
3	m.wisniewska@vetclinic.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Magdalena	Wiśniewska	vet	1	t	2026-06-18 07:59:53.46721+00	2026-06-18 07:59:53.46721+00
4	t.lewandowski@vetclinic.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Tomasz	Lewandowski	vet	1	t	2026-06-18 07:59:53.46721+00	2026-06-18 07:59:53.46721+00
6	k.wojcik@example.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Katarzyna	Wójcik	client	1	t	2026-06-18 07:59:53.46721+00	2026-06-18 07:59:53.46721+00
7	jan.kowalczyk@example.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Jan	Kowalczyk	client	1	t	2026-06-18 07:59:53.46721+00	2026-06-18 07:59:53.46721+00
8	sara.jankowska@example.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Sara	Jankowska	client	1	t	2026-06-18 07:59:53.46721+00	2026-06-18 07:59:53.46721+00
9	m.zielinski@example.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Marek	Zieliński	client	1	t	2026-06-18 07:59:53.46721+00	2026-06-18 07:59:53.46721+00
10	recepcja2@vetclinic.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Ewa	Nowicka	admin	2	t	2026-06-18 07:59:53.46721+00	2026-06-18 07:59:53.46721+00
11	a.zajac@vetclinic.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Adam	Zając	vet	2	t	2026-06-18 07:59:53.46721+00	2026-06-18 07:59:53.46721+00
2	p.nowak@vetclinic.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Piotr	Nowak	vet	1	t	2026-06-18 07:59:53.46721+00	2026-06-18 09:41:03.226835+00
1	recepcja@vetclinic.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Recepcja	Centrum	admin	1	t	2026-06-18 07:59:53.46721+00	2026-06-18 12:46:48.084899+00
12	t.mazur@example.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Tomasz	Mazur	client	2	t	2026-06-18 07:59:53.46721+00	2026-06-18 07:59:53.46721+00
5	robert.lis@example.pl	$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK	Robert	Lis	client	1	t	2026-06-18 07:59:53.46721+00	2026-06-18 13:01:40.314574+00
14	zephyrmc4@gmail.com	$2y$10$XFOKyX78nQ/2nJ5f7I6AaeBZonvl9V.KiHMCSj35cUWvhOpOKZSKy	Dariusz	Mędrala	client	1	t	2026-06-18 14:02:43.002352+00	2026-06-18 14:02:43.002352+00
\.


--
-- Data for Name: vaccinations; Type: TABLE DATA; Schema: public; Owner: vetclinic
--

COPY public.vaccinations (id, pet_id, vaccine_name, administered_at, expires_at, administered_by, external_clinic) FROM stdin;
1	1	Wścieklizna	2025-06-01	2026-06-01	2	\N
2	1	DHPP	2025-12-10	2026-12-10	2	\N
3	3	Wścieklizna	2025-08-15	2026-08-15	3	\N
5	2	DHPP	2026-01-10	2027-01-10	2	\N
7	6	Wścieklizna	2025-11-20	2026-11-20	2	\N
8	4	Wścieklizna	2026-06-18	2027-06-18	2	\N
6	4	Myksomatoza	2026-06-18	2027-06-18	3	\N
4	5	Wścieklizna	2024-05-15	2025-05-15	\N	Przychodnia City Pets
\.


--
-- Data for Name: vaccine_types; Type: TABLE DATA; Schema: public; Owner: vetclinic
--

COPY public.vaccine_types (id, clinic_id, name, price, validity_months, is_active, created_at) FROM stdin;
1	1	Wścieklizna	60.00	12	t	2026-06-18 11:41:44.001396+00
4	2	Wścieklizna	60.00	12	t	2026-06-18 11:41:44.001396+00
2	1	DHPP	90.00	12	t	2026-06-18 11:41:44.001396+00
5	2	DHPP	90.00	12	t	2026-06-18 11:41:44.001396+00
3	1	Myksomatoza	45.00	12	t	2026-06-18 11:41:44.001396+00
6	2	Myksomatoza	45.00	12	t	2026-06-18 11:41:44.001396+00
\.


--
-- Data for Name: vet_availability; Type: TABLE DATA; Schema: public; Owner: vetclinic
--

COPY public.vet_availability (id, vet_id, weekday, start_time, end_time) FROM stdin;
1	2	1	09:00:00	17:00:00
2	2	2	09:00:00	17:00:00
3	2	3	09:00:00	17:00:00
4	2	4	09:00:00	17:00:00
5	2	5	09:00:00	17:00:00
6	3	1	09:00:00	17:00:00
7	3	2	09:00:00	17:00:00
8	3	3	09:00:00	17:00:00
9	3	4	09:00:00	17:00:00
10	3	5	09:00:00	17:00:00
11	4	1	09:00:00	17:00:00
12	4	2	09:00:00	17:00:00
13	4	3	09:00:00	17:00:00
14	4	4	09:00:00	17:00:00
15	4	5	09:00:00	17:00:00
16	11	1	09:00:00	17:00:00
17	11	2	09:00:00	17:00:00
18	11	3	09:00:00	17:00:00
19	11	4	09:00:00	17:00:00
20	11	5	09:00:00	17:00:00
\.


--
-- Data for Name: vet_profiles; Type: TABLE DATA; Schema: public; Owner: vetclinic
--

COPY public.vet_profiles (user_id, license_number, title, room, specialization) FROM stdin;
3	LIC-100871	Dr	Gabinet 2	Dermatologia
4	LIC-101455	Dr	Gabinet 3	Stomatologia
11	LIC-200500	Dr	Gabinet A	Interna
2	LIC-100234	Dr	Gabinet 1	Chirurgia
\.


--
-- Data for Name: visit_reasons; Type: TABLE DATA; Schema: public; Owner: vetclinic
--

COPY public.visit_reasons (id, clinic_id, name, is_active, created_at) FROM stdin;
2	1	Szczepienie	t	2026-06-18 11:41:43.994443+00
3	1	Konsultacja	t	2026-06-18 11:41:43.994443+00
4	1	Kontrola pooperacyjna	t	2026-06-18 11:41:43.994443+00
5	1	Zabieg chirurgiczny	t	2026-06-18 11:41:43.994443+00
6	2	Przegląd ogólny	t	2026-06-18 11:41:43.994443+00
7	2	Szczepienie	t	2026-06-18 11:41:43.994443+00
8	2	Konsultacja	t	2026-06-18 11:41:43.994443+00
9	2	Kontrola pooperacyjna	t	2026-06-18 11:41:43.994443+00
10	2	Zabieg chirurgiczny	t	2026-06-18 11:41:43.994443+00
1	1	Przegląd ogólny	f	2026-06-18 11:41:43.994443+00
\.


--
-- Name: appointment_procedures_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vetclinic
--

SELECT pg_catalog.setval('public.appointment_procedures_id_seq', 9, true);


--
-- Name: appointments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vetclinic
--

SELECT pg_catalog.setval('public.appointments_id_seq', 21, true);


--
-- Name: clinics_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vetclinic
--

SELECT pg_catalog.setval('public.clinics_id_seq', 2, true);


--
-- Name: invoices_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vetclinic
--

SELECT pg_catalog.setval('public.invoices_id_seq', 7, true);


--
-- Name: login_attempts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vetclinic
--

SELECT pg_catalog.setval('public.login_attempts_id_seq', 5, true);


--
-- Name: loyalty_tiers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vetclinic
--

SELECT pg_catalog.setval('public.loyalty_tiers_id_seq', 7, true);


--
-- Name: password_resets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vetclinic
--

SELECT pg_catalog.setval('public.password_resets_id_seq', 1, false);


--
-- Name: pets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vetclinic
--

SELECT pg_catalog.setval('public.pets_id_seq', 11, true);


--
-- Name: procedures_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vetclinic
--

SELECT pg_catalog.setval('public.procedures_id_seq', 21, true);


--
-- Name: species_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vetclinic
--

SELECT pg_catalog.setval('public.species_id_seq', 5, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vetclinic
--

SELECT pg_catalog.setval('public.users_id_seq', 14, true);


--
-- Name: vaccinations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vetclinic
--

SELECT pg_catalog.setval('public.vaccinations_id_seq', 8, true);


--
-- Name: vaccine_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vetclinic
--

SELECT pg_catalog.setval('public.vaccine_types_id_seq', 7, true);


--
-- Name: vet_availability_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vetclinic
--

SELECT pg_catalog.setval('public.vet_availability_id_seq', 20, true);


--
-- Name: visit_reasons_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vetclinic
--

SELECT pg_catalog.setval('public.visit_reasons_id_seq', 11, true);


--
-- Name: appointment_procedures appointment_procedures_pkey; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.appointment_procedures
    ADD CONSTRAINT appointment_procedures_pkey PRIMARY KEY (id);


--
-- Name: appointments appointments_pkey; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.appointments
    ADD CONSTRAINT appointments_pkey PRIMARY KEY (id);


--
-- Name: clients clients_pkey; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (user_id);


--
-- Name: clinics clinics_name_key; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.clinics
    ADD CONSTRAINT clinics_name_key UNIQUE (name);


--
-- Name: clinics clinics_pkey; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.clinics
    ADD CONSTRAINT clinics_pkey PRIMARY KEY (id);


--
-- Name: invoices invoices_appointment_id_key; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.invoices
    ADD CONSTRAINT invoices_appointment_id_key UNIQUE (appointment_id);


--
-- Name: invoices invoices_invoice_number_key; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.invoices
    ADD CONSTRAINT invoices_invoice_number_key UNIQUE (invoice_number);


--
-- Name: invoices invoices_pkey; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.invoices
    ADD CONSTRAINT invoices_pkey PRIMARY KEY (id);


--
-- Name: login_attempts login_attempts_pkey; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.login_attempts
    ADD CONSTRAINT login_attempts_pkey PRIMARY KEY (id);


--
-- Name: loyalty_settings loyalty_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.loyalty_settings
    ADD CONSTRAINT loyalty_settings_pkey PRIMARY KEY (clinic_id);


--
-- Name: loyalty_tiers loyalty_tiers_clinic_id_min_points_key; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.loyalty_tiers
    ADD CONSTRAINT loyalty_tiers_clinic_id_min_points_key UNIQUE (clinic_id, min_points);


--
-- Name: loyalty_tiers loyalty_tiers_pkey; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.loyalty_tiers
    ADD CONSTRAINT loyalty_tiers_pkey PRIMARY KEY (id);


--
-- Name: password_resets password_resets_pkey; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.password_resets
    ADD CONSTRAINT password_resets_pkey PRIMARY KEY (id);


--
-- Name: pets pets_pkey; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.pets
    ADD CONSTRAINT pets_pkey PRIMARY KEY (id);


--
-- Name: procedures procedures_clinic_name_key; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.procedures
    ADD CONSTRAINT procedures_clinic_name_key UNIQUE (clinic_id, name);


--
-- Name: procedures procedures_pkey; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.procedures
    ADD CONSTRAINT procedures_pkey PRIMARY KEY (id);


--
-- Name: species species_name_key; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.species
    ADD CONSTRAINT species_name_key UNIQUE (name);


--
-- Name: species species_pkey; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.species
    ADD CONSTRAINT species_pkey PRIMARY KEY (id);


--
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: vaccinations vaccinations_pkey; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.vaccinations
    ADD CONSTRAINT vaccinations_pkey PRIMARY KEY (id);


--
-- Name: vaccine_types vaccine_types_clinic_id_name_key; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.vaccine_types
    ADD CONSTRAINT vaccine_types_clinic_id_name_key UNIQUE (clinic_id, name);


--
-- Name: vaccine_types vaccine_types_pkey; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.vaccine_types
    ADD CONSTRAINT vaccine_types_pkey PRIMARY KEY (id);


--
-- Name: vet_availability vet_availability_pkey; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.vet_availability
    ADD CONSTRAINT vet_availability_pkey PRIMARY KEY (id);


--
-- Name: vet_availability vet_availability_vet_id_weekday_key; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.vet_availability
    ADD CONSTRAINT vet_availability_vet_id_weekday_key UNIQUE (vet_id, weekday);


--
-- Name: vet_profiles vet_profiles_license_number_key; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.vet_profiles
    ADD CONSTRAINT vet_profiles_license_number_key UNIQUE (license_number);


--
-- Name: vet_profiles vet_profiles_pkey; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.vet_profiles
    ADD CONSTRAINT vet_profiles_pkey PRIMARY KEY (user_id);


--
-- Name: visit_reasons visit_reasons_clinic_id_name_key; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.visit_reasons
    ADD CONSTRAINT visit_reasons_clinic_id_name_key UNIQUE (clinic_id, name);


--
-- Name: visit_reasons visit_reasons_pkey; Type: CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.visit_reasons
    ADD CONSTRAINT visit_reasons_pkey PRIMARY KEY (id);


--
-- Name: idx_appointment_procedures_procedure; Type: INDEX; Schema: public; Owner: vetclinic
--

CREATE INDEX idx_appointment_procedures_procedure ON public.appointment_procedures USING btree (procedure_id);


--
-- Name: idx_appointments_pet; Type: INDEX; Schema: public; Owner: vetclinic
--

CREATE INDEX idx_appointments_pet ON public.appointments USING btree (pet_id);


--
-- Name: idx_appointments_starts_at; Type: INDEX; Schema: public; Owner: vetclinic
--

CREATE INDEX idx_appointments_starts_at ON public.appointments USING btree (starts_at);


--
-- Name: idx_appointments_vet; Type: INDEX; Schema: public; Owner: vetclinic
--

CREATE INDEX idx_appointments_vet ON public.appointments USING btree (vet_id);


--
-- Name: idx_login_attempts_ip; Type: INDEX; Schema: public; Owner: vetclinic
--

CREATE INDEX idx_login_attempts_ip ON public.login_attempts USING btree (ip, attempted_at);


--
-- Name: idx_password_resets_token; Type: INDEX; Schema: public; Owner: vetclinic
--

CREATE INDEX idx_password_resets_token ON public.password_resets USING btree (token_hash);


--
-- Name: idx_pets_client; Type: INDEX; Schema: public; Owner: vetclinic
--

CREATE INDEX idx_pets_client ON public.pets USING btree (client_id);


--
-- Name: idx_pets_species; Type: INDEX; Schema: public; Owner: vetclinic
--

CREATE INDEX idx_pets_species ON public.pets USING btree (species_id);


--
-- Name: idx_vaccinations_pet; Type: INDEX; Schema: public; Owner: vetclinic
--

CREATE INDEX idx_vaccinations_pet ON public.vaccinations USING btree (pet_id);


--
-- Name: appointments trg_prevent_double_booking; Type: TRIGGER; Schema: public; Owner: vetclinic
--

CREATE TRIGGER trg_prevent_double_booking BEFORE INSERT OR UPDATE ON public.appointments FOR EACH ROW EXECUTE FUNCTION public.fn_prevent_double_booking();


--
-- Name: appointment_procedures appointment_procedures_appointment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.appointment_procedures
    ADD CONSTRAINT appointment_procedures_appointment_id_fkey FOREIGN KEY (appointment_id) REFERENCES public.appointments(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: appointment_procedures appointment_procedures_procedure_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.appointment_procedures
    ADD CONSTRAINT appointment_procedures_procedure_id_fkey FOREIGN KEY (procedure_id) REFERENCES public.procedures(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: appointment_procedures appointment_procedures_vaccine_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.appointment_procedures
    ADD CONSTRAINT appointment_procedures_vaccine_type_id_fkey FOREIGN KEY (vaccine_type_id) REFERENCES public.vaccine_types(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: appointments appointments_pet_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.appointments
    ADD CONSTRAINT appointments_pet_id_fkey FOREIGN KEY (pet_id) REFERENCES public.pets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: appointments appointments_vet_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.appointments
    ADD CONSTRAINT appointments_vet_id_fkey FOREIGN KEY (vet_id) REFERENCES public.vet_profiles(user_id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: clients clients_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.clients
    ADD CONSTRAINT clients_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: invoices invoices_appointment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.invoices
    ADD CONSTRAINT invoices_appointment_id_fkey FOREIGN KEY (appointment_id) REFERENCES public.appointments(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: loyalty_settings loyalty_settings_clinic_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.loyalty_settings
    ADD CONSTRAINT loyalty_settings_clinic_id_fkey FOREIGN KEY (clinic_id) REFERENCES public.clinics(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: loyalty_tiers loyalty_tiers_clinic_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.loyalty_tiers
    ADD CONSTRAINT loyalty_tiers_clinic_id_fkey FOREIGN KEY (clinic_id) REFERENCES public.clinics(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: password_resets password_resets_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.password_resets
    ADD CONSTRAINT password_resets_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: pets pets_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.pets
    ADD CONSTRAINT pets_client_id_fkey FOREIGN KEY (client_id) REFERENCES public.clients(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: pets pets_species_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.pets
    ADD CONSTRAINT pets_species_id_fkey FOREIGN KEY (species_id) REFERENCES public.species(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: procedures procedures_clinic_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.procedures
    ADD CONSTRAINT procedures_clinic_id_fkey FOREIGN KEY (clinic_id) REFERENCES public.clinics(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: users users_clinic_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_clinic_id_fkey FOREIGN KEY (clinic_id) REFERENCES public.clinics(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: vaccinations vaccinations_administered_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.vaccinations
    ADD CONSTRAINT vaccinations_administered_by_fkey FOREIGN KEY (administered_by) REFERENCES public.vet_profiles(user_id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: vaccinations vaccinations_pet_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.vaccinations
    ADD CONSTRAINT vaccinations_pet_id_fkey FOREIGN KEY (pet_id) REFERENCES public.pets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: vaccine_types vaccine_types_clinic_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.vaccine_types
    ADD CONSTRAINT vaccine_types_clinic_id_fkey FOREIGN KEY (clinic_id) REFERENCES public.clinics(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: vet_availability vet_availability_vet_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.vet_availability
    ADD CONSTRAINT vet_availability_vet_id_fkey FOREIGN KEY (vet_id) REFERENCES public.vet_profiles(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: vet_profiles vet_profiles_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.vet_profiles
    ADD CONSTRAINT vet_profiles_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: visit_reasons visit_reasons_clinic_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: vetclinic
--

ALTER TABLE ONLY public.visit_reasons
    ADD CONSTRAINT visit_reasons_clinic_id_fkey FOREIGN KEY (clinic_id) REFERENCES public.clinics(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict CCtSmuRlduiaJpJnHk3DGQszD5KyAt5AjJLqnJaIvuFFwVKZyAsCITlwMjyfKiC

