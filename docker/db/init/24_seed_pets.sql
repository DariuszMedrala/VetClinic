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
