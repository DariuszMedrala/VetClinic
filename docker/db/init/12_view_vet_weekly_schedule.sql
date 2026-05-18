CREATE VIEW vw_vet_weekly_schedule AS
SELECT
    a.id AS appointment_id,
    a.starts_at,
    a.ends_at,
    a.status,
    a.reason,
    vu.id AS vet_id,
    vp.title || ' ' || vu.first_name || ' ' || vu.last_name AS vet_name,
    vp.room,
    p.id AS pet_id,
    p.name AS pet_name,
    s.name AS species,
    cu.id AS client_id,
    cu.first_name || ' ' || cu.last_name AS client_name,
    c.phone AS client_phone,
    vu.clinic_id AS clinic_id
FROM appointments a
JOIN vet_profiles vp ON vp.user_id = a.vet_id
JOIN users vu ON vu.id = vp.user_id
JOIN pets p ON p.id = a.pet_id
JOIN species s ON s.id = p.species_id
JOIN clients c ON c.user_id = p.client_id
JOIN users cu ON cu.id = c.user_id;
