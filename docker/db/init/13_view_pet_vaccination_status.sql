CREATE VIEW vw_pet_vaccination_status AS
SELECT
    v.id AS vaccination_id,
    p.id AS pet_id,
    p.name AS pet_name,
    s.name AS species,
    cu.first_name || ' ' || cu.last_name AS owner_name,
    v.vaccine_name,
    v.administered_at,
    v.expires_at,
    CASE WHEN v.expires_at < CURRENT_DATE THEN 'overdue' ELSE 'valid' END AS status,
    COALESCE(vu.first_name || ' ' || vu.last_name, v.external_clinic) AS administered_by
FROM vaccinations v
JOIN pets p ON p.id = v.pet_id
JOIN species s ON s.id = p.species_id
JOIN clients c ON c.user_id = p.client_id
JOIN users cu ON cu.id = c.user_id
LEFT JOIN users vu ON vu.id = v.administered_by;
