INSERT INTO clients (user_id, phone, loyalty_points) VALUES
((SELECT id FROM users WHERE email = 'robert.lis@example.pl'), '600100200', 150),
((SELECT id FROM users WHERE email = 'k.wojcik@example.pl'), '512333444', 40),
((SELECT id FROM users WHERE email = 'jan.kowalczyk@example.pl'), '698777888', 220),
((SELECT id FROM users WHERE email = 'sara.jankowska@example.pl'), '731222111', 0),
((SELECT id FROM users WHERE email = 'm.zielinski@example.pl'), '605909808', 95),
((SELECT id FROM users WHERE email = 't.mazur@example.pl'), '501601701', 60);
