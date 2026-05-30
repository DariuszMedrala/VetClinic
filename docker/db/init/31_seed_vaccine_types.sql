INSERT INTO vaccine_types (clinic_id, name, price, validity_months)
SELECT c.id, v.name, v.price, v.months
FROM clinics c
CROSS JOIN (VALUES
    ('Szczepionka przeciw wściekliźnie', 60.00, 36),
    ('Szczepionka DHPP', 90.00, 12),
    ('Odrobaczanie', 45.00, 6)
) AS v(name, price, months);
