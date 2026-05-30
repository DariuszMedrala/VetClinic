INSERT INTO procedures (clinic_id, name, description, type, base_price)
SELECT c.id, p.name, p.description, p.type::procedure_type, p.base_price
FROM clinics c
CROSS JOIN (VALUES
    ('Przegląd ogólny', 'Coroczne badanie fizykalne i kontrola stanu zdrowia', 'treatment', 80.00),
    ('Szczepienie przeciw wściekliźnie', 'Obowiązkowa dawka przypominająca', 'medication', 60.00),
    ('Szczepienie DHPP', 'Nosówka, parwowiroza, zapalenie wątroby', 'medication', 90.00),
    ('Odrobaczanie', 'Tabletka przeciw pasożytom wewnętrznym', 'medication', 45.00),
    ('Leczenie przeciw pchłom', 'Preparat na pchły i kleszcze', 'medication', 50.00),
    ('Czyszczenie zębów', 'Usuwanie kamienia nazębnego w narkozie', 'treatment', 150.00),
    ('Zabieg chirurgiczny', 'Standardowy zabieg operacyjny', 'treatment', 400.00),
    ('USG jamy brzusznej', 'Badanie ultrasonograficzne', 'treatment', 120.00),
    ('Badanie krwi', 'Morfologia i biochemia', 'treatment', 70.00),
    ('Konsultacja dermatologiczna', 'Diagnostyka chorób skóry', 'treatment', 100.00)
) AS p(name, description, type, base_price);
