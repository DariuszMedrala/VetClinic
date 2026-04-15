INSERT INTO vaccinations (pet_id, vaccine_name, administered_at, expires_at, administered_by, external_clinic) VALUES
((SELECT id FROM pets WHERE name = 'Luna'), 'Wścieklizna', '2025-06-01', '2026-06-01', (SELECT id FROM users WHERE email = 'p.nowak@vetclinic.pl'), NULL),
((SELECT id FROM pets WHERE name = 'Luna'), 'DHPP', '2025-12-10', '2026-12-10', (SELECT id FROM users WHERE email = 'p.nowak@vetclinic.pl'), NULL),
((SELECT id FROM pets WHERE name = 'Mruczek'), 'Wścieklizna', '2025-08-15', '2026-08-15', (SELECT id FROM users WHERE email = 'm.wisniewska@vetclinic.pl'), NULL),
((SELECT id FROM pets WHERE name = 'Maks'), 'Wścieklizna', '2024-05-15', '2025-05-15', NULL, 'Przychodnia City Pets'),
((SELECT id FROM pets WHERE name = 'Reksio'), 'DHPP', '2026-01-10', '2027-01-10', (SELECT id FROM users WHERE email = 'p.nowak@vetclinic.pl'), NULL),
((SELECT id FROM pets WHERE name = 'Coco'), 'Myksomatoza', '2025-03-01', '2026-03-01', (SELECT id FROM users WHERE email = 'm.wisniewska@vetclinic.pl'), NULL),
((SELECT id FROM pets WHERE name = 'Tofik'), 'Wścieklizna', '2025-11-20', '2026-11-20', (SELECT id FROM users WHERE email = 'p.nowak@vetclinic.pl'), NULL);
