CREATE TABLE vaccinations (
    id              BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    pet_id          BIGINT NOT NULL REFERENCES pets (id) ON DELETE CASCADE ON UPDATE CASCADE,
    vaccine_name    VARCHAR(150) NOT NULL,
    administered_at DATE NOT NULL,
    expires_at      DATE NOT NULL,
    administered_by BIGINT REFERENCES vet_profiles (user_id) ON DELETE SET NULL ON UPDATE CASCADE,
    external_clinic VARCHAR(150),
    CONSTRAINT vaccinations_validity_order CHECK (expires_at > administered_at)
);
