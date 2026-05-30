INSERT INTO visit_reasons (clinic_id, name)
SELECT c.id, r.name
FROM clinics c
CROSS JOIN (VALUES
    ('Przegląd ogólny'),
    ('Szczepienie'),
    ('Konsultacja'),
    ('Kontrola pooperacyjna'),
    ('Zabieg chirurgiczny')
) AS r(name);
