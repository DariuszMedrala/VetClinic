CREATE TABLE invoices (
    id             BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    appointment_id BIGINT NOT NULL UNIQUE REFERENCES appointments (id) ON DELETE CASCADE ON UPDATE CASCADE,
    invoice_number VARCHAR(30) NOT NULL UNIQUE,
    status         invoice_status NOT NULL DEFAULT 'pending',
    payment_method payment_method,
    issued_at      TIMESTAMPTZ NOT NULL DEFAULT now(),
    paid_at        TIMESTAMPTZ
);
