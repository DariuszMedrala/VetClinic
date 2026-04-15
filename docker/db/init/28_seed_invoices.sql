INSERT INTO invoices (appointment_id, invoice_number, status, payment_method, paid_at) VALUES
((SELECT id FROM appointments WHERE starts_at = TIMESTAMPTZ '2026-06-15 09:00+02'), 'FV-2026-0001', 'paid', 'card', '2026-06-15 10:05+02'),
((SELECT id FROM appointments WHERE starts_at = TIMESTAMPTZ '2026-06-15 10:30+02'), 'FV-2026-0002', 'pending', NULL, NULL),
((SELECT id FROM appointments WHERE starts_at = TIMESTAMPTZ '2026-06-16 09:00+02'), 'FV-2026-0003', 'paid', 'cash', '2026-06-16 09:50+02');
