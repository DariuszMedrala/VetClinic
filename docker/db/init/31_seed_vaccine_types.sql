INSERT INTO vaccine_types (clinic_id, name, price, validity_months)
SELECT c.id, v.name, v.price, v.months
FROM clinics c
CROSS JOIN (VALUES
    ('Wścieklizna', 60.00, 12),
    ('DHPP', 90.00, 12),
    ('Myksomatoza', 50.00, 12)
) AS v(name, price, months);
