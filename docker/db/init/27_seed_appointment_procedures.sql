INSERT INTO appointment_procedures (appointment_id, procedure_id, quantity, unit_price) VALUES
((SELECT id FROM appointments WHERE starts_at = TIMESTAMPTZ '2026-06-15 09:00+02'), (SELECT id FROM procedures WHERE name = 'Przegląd ogólny'), 1, 80.00),
((SELECT id FROM appointments WHERE starts_at = TIMESTAMPTZ '2026-06-15 09:00+02'), (SELECT id FROM procedures WHERE name = 'Szczepienie przeciw wściekliźnie'), 1, 60.00),
((SELECT id FROM appointments WHERE starts_at = TIMESTAMPTZ '2026-06-15 09:00+02'), (SELECT id FROM procedures WHERE name = 'Leczenie przeciw pchłom'), 1, 50.00),
((SELECT id FROM appointments WHERE starts_at = TIMESTAMPTZ '2026-06-15 10:30+02'), (SELECT id FROM procedures WHERE name = 'Szczepienie DHPP'), 1, 90.00),
((SELECT id FROM appointments WHERE starts_at = TIMESTAMPTZ '2026-06-16 09:00+02'), (SELECT id FROM procedures WHERE name = 'Czyszczenie zębów'), 1, 150.00),
((SELECT id FROM appointments WHERE starts_at = TIMESTAMPTZ '2026-06-16 09:00+02'), (SELECT id FROM procedures WHERE name = 'Badanie krwi'), 1, 70.00);
