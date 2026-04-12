CREATE TABLE pets (
    id         BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    client_id  BIGINT NOT NULL REFERENCES clients (user_id) ON DELETE CASCADE ON UPDATE CASCADE,
    species_id SMALLINT NOT NULL REFERENCES species (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    name       VARCHAR(100) NOT NULL,
    breed      VARCHAR(100),
    sex        animal_sex NOT NULL DEFAULT 'unknown',
    birth_date DATE,
    weight_kg  NUMERIC(5, 2) CHECK (weight_kg IS NULL OR weight_kg > 0),
    photo_path VARCHAR(255),
    created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);
