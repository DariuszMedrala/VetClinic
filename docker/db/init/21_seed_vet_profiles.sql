INSERT INTO vet_profiles (user_id, license_number, title, room, specialization) VALUES
((SELECT id FROM users WHERE email = 'p.nowak@vetclinic.pl'), 'LIC-100234', 'Dr', 'Gabinet 1', 'Chirurgia'),
((SELECT id FROM users WHERE email = 'm.wisniewska@vetclinic.pl'), 'LIC-100871', 'Dr', 'Gabinet 2', 'Dermatologia'),
((SELECT id FROM users WHERE email = 't.lewandowski@vetclinic.pl'), 'LIC-101455', 'Dr', 'Gabinet 3', 'Stomatologia'),
((SELECT id FROM users WHERE email = 'a.zajac@vetclinic.pl'), 'LIC-200500', 'Dr', 'Gabinet A', 'Interna');
