CREATE TABLE procedures (
    id          BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name        VARCHAR(150) NOT NULL UNIQUE,
    description TEXT,
    type        procedure_type NOT NULL DEFAULT 'treatment',
    base_price  NUMERIC(10, 2) NOT NULL CHECK (base_price >= 0)
);
