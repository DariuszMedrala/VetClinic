INSERT INTO loyalty_settings (clinic_id, points_per, per_amount)
SELECT id, 1, 10 FROM clinics;

INSERT INTO loyalty_tiers (clinic_id, min_points, discount_percent)
SELECT c.id, t.min_points, t.discount_percent
FROM clinics c
CROSS JOIN (VALUES
    (100, 10.00),
    (200, 15.00),
    (500, 20.00)
) AS t(min_points, discount_percent);
