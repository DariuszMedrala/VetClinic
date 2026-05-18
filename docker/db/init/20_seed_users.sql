INSERT INTO users (email, password_hash, first_name, last_name, role, clinic_id) VALUES
('recepcja@vetclinic.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Anna', 'Kowalska', 'admin', (SELECT id FROM clinics WHERE name = 'Przychodnia Centrum')),
('p.nowak@vetclinic.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Piotr', 'Nowak', 'vet', (SELECT id FROM clinics WHERE name = 'Przychodnia Centrum')),
('m.wisniewska@vetclinic.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Magdalena', 'Wiśniewska', 'vet', (SELECT id FROM clinics WHERE name = 'Przychodnia Centrum')),
('t.lewandowski@vetclinic.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Tomasz', 'Lewandowski', 'vet', (SELECT id FROM clinics WHERE name = 'Przychodnia Centrum')),
('robert.lis@example.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Robert', 'Lis', 'client', (SELECT id FROM clinics WHERE name = 'Przychodnia Centrum')),
('k.wojcik@example.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Katarzyna', 'Wójcik', 'client', (SELECT id FROM clinics WHERE name = 'Przychodnia Centrum')),
('jan.kowalczyk@example.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Jan', 'Kowalczyk', 'client', (SELECT id FROM clinics WHERE name = 'Przychodnia Centrum')),
('sara.jankowska@example.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Sara', 'Jankowska', 'client', (SELECT id FROM clinics WHERE name = 'Przychodnia Centrum')),
('m.zielinski@example.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Marek', 'Zieliński', 'client', (SELECT id FROM clinics WHERE name = 'Przychodnia Centrum')),
('recepcja2@vetclinic.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Ewa', 'Nowicka', 'admin', (SELECT id FROM clinics WHERE name = 'Lecznica Wesoła Łapa')),
('a.zajac@vetclinic.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Adam', 'Zając', 'vet', (SELECT id FROM clinics WHERE name = 'Lecznica Wesoła Łapa')),
('t.mazur@example.pl', '$2y$10$8H26s9GUQXyYdou5thnAP.2cXtebkhtK6DWPKujzZLKwRQg6zn4CK', 'Tomasz', 'Mazur', 'client', (SELECT id FROM clinics WHERE name = 'Lecznica Wesoła Łapa'));
