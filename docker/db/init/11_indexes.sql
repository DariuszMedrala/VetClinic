CREATE INDEX idx_pets_client ON pets (client_id);

CREATE INDEX idx_pets_species ON pets (species_id);

CREATE INDEX idx_appointments_vet ON appointments (vet_id);

CREATE INDEX idx_appointments_pet ON appointments (pet_id);

CREATE INDEX idx_appointments_starts_at ON appointments (starts_at);

CREATE INDEX idx_vaccinations_pet ON vaccinations (pet_id);

CREATE INDEX idx_appointment_procedures_procedure ON appointment_procedures (procedure_id);
