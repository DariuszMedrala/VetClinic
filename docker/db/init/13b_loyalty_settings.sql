CREATE TABLE loyalty_settings (
    clinic_id  BIGINT PRIMARY KEY REFERENCES clinics (id) ON DELETE CASCADE ON UPDATE CASCADE,
    points_per INT NOT NULL DEFAULT 1 CHECK (points_per >= 0),
    per_amount NUMERIC(10, 2) NOT NULL DEFAULT 10 CHECK (per_amount > 0)
);
