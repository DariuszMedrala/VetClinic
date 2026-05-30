CREATE TABLE vaccine_types (
    id              BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    clinic_id       BIGINT NOT NULL REFERENCES clinics (id) ON DELETE CASCADE ON UPDATE CASCADE,
    name            VARCHAR(150) NOT NULL,
    price           NUMERIC(10, 2) NOT NULL CHECK (price >= 0),
    validity_months INT NOT NULL CHECK (validity_months > 0),
    is_active       BOOLEAN NOT NULL DEFAULT TRUE,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT now(),
    UNIQUE (clinic_id, name)
);
