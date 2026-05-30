CREATE TABLE procedures (
    id          BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    clinic_id   BIGINT NOT NULL REFERENCES clinics (id) ON DELETE CASCADE ON UPDATE CASCADE,
    name        VARCHAR(150) NOT NULL,
    description TEXT,
    type        procedure_type NOT NULL DEFAULT 'treatment',
    base_price  NUMERIC(10, 2) NOT NULL CHECK (base_price >= 0),
    is_active   BOOLEAN NOT NULL DEFAULT TRUE,
    UNIQUE (clinic_id, name)
);
