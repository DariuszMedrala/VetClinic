CREATE TABLE appointment_procedures (
    id             BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    appointment_id BIGINT NOT NULL REFERENCES appointments (id) ON DELETE CASCADE ON UPDATE CASCADE,
    procedure_id   BIGINT REFERENCES procedures (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    quantity       INTEGER NOT NULL DEFAULT 1 CHECK (quantity > 0),
    unit_price     NUMERIC(10, 2) NOT NULL CHECK (unit_price >= 0)
);
