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
