INSERT INTO users (email, password_hash, first_name, last_name, role) VALUES
('recepcja@vetclinic.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Anna', 'Kowalska', 'admin'),
('p.nowak@vetclinic.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Piotr', 'Nowak', 'vet'),
('m.wisniewska@vetclinic.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Magdalena', 'Wiśniewska', 'vet'),
('t.lewandowski@vetclinic.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Tomasz', 'Lewandowski', 'vet'),
('robert.lis@example.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Robert', 'Lis', 'client'),
('k.wojcik@example.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Katarzyna', 'Wójcik', 'client'),
('jan.kowalczyk@example.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Jan', 'Kowalczyk', 'client'),
('sara.jankowska@example.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Sara', 'Jankowska', 'client'),
('m.zielinski@example.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Marek', 'Zieliński', 'client');

INSERT INTO vet_profiles (user_id, license_number, title, room, specialization) VALUES
((SELECT id FROM users WHERE email = 'p.nowak@vetclinic.pl'), 'LIC-100234', 'Dr', 'Gabinet 1', 'Chirurgia'),
((SELECT id FROM users WHERE email = 'm.wisniewska@vetclinic.pl'), 'LIC-100871', 'Dr', 'Gabinet 2', 'Dermatologia'),
((SELECT id FROM users WHERE email = 't.lewandowski@vetclinic.pl'), 'LIC-101455', 'Dr', 'Gabinet 3', 'Stomatologia');


INSERT INTO clients (user_id, phone, loyalty_points) VALUES
((SELECT id FROM users WHERE email = 'robert.lis@example.pl'), '600100200', 150),
((SELECT id FROM users WHERE email = 'k.wojcik@example.pl'), '512333444', 40),
((SELECT id FROM users WHERE email = 'jan.kowalczyk@example.pl'), '698777888', 220),
((SELECT id FROM users WHERE email = 'sara.jankowska@example.pl'), '731222111', 0),
((SELECT id FROM users WHERE email = 'm.zielinski@example.pl'), '605909808', 95);

INSERT INTO species (name) VALUES ('Pies'), ('Kot'), ('Królik'), ('Chomik'), ('Papuga');

INSERT INTO pets (client_id, species_id, name, breed, sex, birth_date, weight_kg) VALUES
((SELECT id FROM users WHERE email = 'robert.lis@example.pl'), (SELECT id FROM species WHERE name = 'Pies'), 'Luna', 'Golden Retriever', 'female', '2021-04-12', 28.50),
((SELECT id FROM users WHERE email = 'robert.lis@example.pl'), (SELECT id FROM species WHERE name = 'Pies'), 'Reksio', 'Owczarek niemiecki', 'male', '2019-09-01', 34.20),
((SELECT id FROM users WHERE email = 'k.wojcik@example.pl'), (SELECT id FROM species WHERE name = 'Kot'), 'Mruczek', 'Dachowiec', 'male', '2020-06-20', 4.80),
((SELECT id FROM users WHERE email = 'jan.kowalczyk@example.pl'), (SELECT id FROM species WHERE name = 'Królik'), 'Coco', 'Baran francuski', 'female', '2022-02-10', 2.10),
((SELECT id FROM users WHERE email = 'jan.kowalczyk@example.pl'), (SELECT id FROM species WHERE name = 'Pies'), 'Maks', 'Labrador', 'male', '2018-11-05', 31.00),
((SELECT id FROM users WHERE email = 'sara.jankowska@example.pl'), (SELECT id FROM species WHERE name = 'Pies'), 'Tofik', 'Beagle', 'male', '2023-01-30', 12.40),
((SELECT id FROM users WHERE email = 'sara.jankowska@example.pl'), (SELECT id FROM species WHERE name = 'Kot'), 'Pusia', 'Brytyjski krótkowłosy', 'female', '2021-07-22', 5.30),
((SELECT id FROM users WHERE email = 'm.zielinski@example.pl'), (SELECT id FROM species WHERE name = 'Papuga'), 'Gucio', 'Nimfa', 'male', '2022-05-18', 0.10),
((SELECT id FROM users WHERE email = 'm.zielinski@example.pl'), (SELECT id FROM species WHERE name = 'Chomik'), 'Filip', 'Syryjski', 'male', '2024-03-03', 0.15);

INSERT INTO procedures (name, description, type, base_price) VALUES
('Przegląd ogólny', 'Coroczne badanie fizykalne i kontrola stanu zdrowia', 'treatment', 80.00),
('Szczepienie przeciw wściekliźnie', 'Obowiązkowa dawka przypominająca', 'medication', 60.00),
('Szczepienie DHPP', 'Nosówka, parwowiroza, zapalenie wątroby', 'medication', 90.00),
('Odrobaczanie', 'Tabletka przeciw pasożytom wewnętrznym', 'medication', 45.00),
('Leczenie przeciw pchłom', 'Preparat na pchły i kleszcze', 'medication', 50.00),
('Czyszczenie zębów', 'Usuwanie kamienia nazębnego w narkozie', 'treatment', 150.00),
('Zabieg chirurgiczny', 'Standardowy zabieg operacyjny', 'treatment', 400.00),
('USG jamy brzusznej', 'Badanie ultrasonograficzne', 'treatment', 120.00),
('Badanie krwi', 'Morfologia i biochemia', 'treatment', 70.00),
('Konsultacja dermatologiczna', 'Diagnostyka chorób skóry', 'treatment', 100.00);

INSERT INTO appointments (pet_id, vet_id, starts_at, ends_at, reason, status, notes) VALUES
((SELECT id FROM pets WHERE name = 'Luna'), (SELECT id FROM users WHERE email = 'p.nowak@vetclinic.pl'), '2026-06-15 09:00+02', '2026-06-15 10:00+02', 'Coroczny przegląd', 'completed', 'Pacjent w dobrej kondycji.'),
((SELECT id FROM pets WHERE name = 'Mruczek'), (SELECT id FROM users WHERE email = 'p.nowak@vetclinic.pl'), '2026-06-15 10:30+02', '2026-06-15 11:00+02', 'Szczepienie', 'completed', NULL),
((SELECT id FROM pets WHERE name = 'Coco'), (SELECT id FROM users WHERE email = 'm.wisniewska@vetclinic.pl'), '2026-06-15 11:00+02', '2026-06-15 11:30+02', 'Kontrola dermatologiczna', 'confirmed', 'Świąd skóry, podejrzenie alergii.'),
((SELECT id FROM pets WHERE name = 'Maks'), (SELECT id FROM users WHERE email = 't.lewandowski@vetclinic.pl'), '2026-06-16 09:00+02', '2026-06-16 09:45+02', 'Czyszczenie zębów', 'completed', NULL),
((SELECT id FROM pets WHERE name = 'Tofik'), (SELECT id FROM users WHERE email = 'p.nowak@vetclinic.pl'), '2026-06-16 12:00+02', '2026-06-16 12:30+02', 'Szczepienie przeciw wściekliźnie', 'confirmed', NULL),
((SELECT id FROM pets WHERE name = 'Reksio'), (SELECT id FROM users WHERE email = 'm.wisniewska@vetclinic.pl'), '2026-06-17 08:30+02', '2026-06-17 09:30+02', 'Zabieg chirurgiczny', 'scheduled', 'Wymagane badania przedoperacyjne.'),
((SELECT id FROM pets WHERE name = 'Pusia'), (SELECT id FROM users WHERE email = 't.lewandowski@vetclinic.pl'), '2026-06-17 14:00+02', '2026-06-17 14:30+02', 'Przegląd ogólny', 'scheduled', NULL),
((SELECT id FROM pets WHERE name = 'Gucio'), (SELECT id FROM users WHERE email = 'p.nowak@vetclinic.pl'), '2026-06-18 10:00+02', '2026-06-18 10:30+02', 'Konsultacja', 'scheduled', NULL),
((SELECT id FROM pets WHERE name = 'Filip'), (SELECT id FROM users WHERE email = 'm.wisniewska@vetclinic.pl'), '2026-06-18 13:00+02', '2026-06-18 13:30+02', 'Badanie krwi', 'cancelled', 'Odwołane przez klienta.'),
((SELECT id FROM pets WHERE name = 'Luna'), (SELECT id FROM users WHERE email = 't.lewandowski@vetclinic.pl'), '2026-06-19 11:00+02', '2026-06-19 12:00+02', 'USG jamy brzusznej', 'scheduled', NULL);

INSERT INTO appointment_procedures (appointment_id, procedure_id, quantity, unit_price) VALUES
((SELECT id FROM appointments WHERE starts_at = TIMESTAMPTZ '2026-06-15 09:00+02'), (SELECT id FROM procedures WHERE name = 'Przegląd ogólny'), 1, 80.00),
((SELECT id FROM appointments WHERE starts_at = TIMESTAMPTZ '2026-06-15 09:00+02'), (SELECT id FROM procedures WHERE name = 'Szczepienie przeciw wściekliźnie'), 1, 60.00),
((SELECT id FROM appointments WHERE starts_at = TIMESTAMPTZ '2026-06-15 09:00+02'), (SELECT id FROM procedures WHERE name = 'Leczenie przeciw pchłom'), 1, 50.00),
((SELECT id FROM appointments WHERE starts_at = TIMESTAMPTZ '2026-06-15 10:30+02'), (SELECT id FROM procedures WHERE name = 'Szczepienie DHPP'), 1, 90.00),
((SELECT id FROM appointments WHERE starts_at = TIMESTAMPTZ '2026-06-16 09:00+02'), (SELECT id FROM procedures WHERE name = 'Czyszczenie zębów'), 1, 150.00),
((SELECT id FROM appointments WHERE starts_at = TIMESTAMPTZ '2026-06-16 09:00+02'), (SELECT id FROM procedures WHERE name = 'Badanie krwi'), 1, 70.00);

INSERT INTO invoices (appointment_id, invoice_number, status, payment_method, paid_at) VALUES
((SELECT id FROM appointments WHERE starts_at = TIMESTAMPTZ '2026-06-15 09:00+02'), 'FV-2026-0001', 'paid', 'card', '2026-06-15 10:05+02'),
((SELECT id FROM appointments WHERE starts_at = TIMESTAMPTZ '2026-06-15 10:30+02'), 'FV-2026-0002', 'pending', NULL, NULL),
((SELECT id FROM appointments WHERE starts_at = TIMESTAMPTZ '2026-06-16 09:00+02'), 'FV-2026-0003', 'paid', 'cash', '2026-06-16 09:50+02');

INSERT INTO vaccinations (pet_id, vaccine_name, administered_at, expires_at, administered_by, external_clinic) VALUES
((SELECT id FROM pets WHERE name = 'Luna'), 'Wścieklizna', '2025-06-01', '2026-06-01', (SELECT id FROM users WHERE email = 'p.nowak@vetclinic.pl'), NULL),
((SELECT id FROM pets WHERE name = 'Luna'), 'DHPP', '2025-12-10', '2026-12-10', (SELECT id FROM users WHERE email = 'p.nowak@vetclinic.pl'), NULL),
((SELECT id FROM pets WHERE name = 'Mruczek'), 'Wścieklizna', '2025-08-15', '2026-08-15', (SELECT id FROM users WHERE email = 'm.wisniewska@vetclinic.pl'), NULL),
((SELECT id FROM pets WHERE name = 'Maks'), 'Wścieklizna', '2024-05-15', '2025-05-15', NULL, 'Przychodnia City Pets'),
((SELECT id FROM pets WHERE name = 'Reksio'), 'DHPP', '2026-01-10', '2027-01-10', (SELECT id FROM users WHERE email = 'p.nowak@vetclinic.pl'), NULL),
((SELECT id FROM pets WHERE name = 'Coco'), 'Myksomatoza', '2025-03-01', '2026-03-01', (SELECT id FROM users WHERE email = 'm.wisniewska@vetclinic.pl'), NULL),
((SELECT id FROM pets WHERE name = 'Tofik'), 'Wścieklizna', '2025-11-20', '2026-11-20', (SELECT id FROM users WHERE email = 'p.nowak@vetclinic.pl'), NULL);
