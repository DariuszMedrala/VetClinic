INSERT INTO vet_availability (vet_id, weekday, start_time, end_time)
SELECT vp.user_id, d.wd, TIME '09:00', TIME '17:00'
FROM vet_profiles vp
CROSS JOIN (VALUES (1), (2), (3), (4), (5)) AS d (wd);
