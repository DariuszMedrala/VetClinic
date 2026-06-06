CREATE TABLE loyalty_tiers (
    id               BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    clinic_id        BIGINT NOT NULL REFERENCES clinics (id) ON DELETE CASCADE ON UPDATE CASCADE,
    min_points       INT NOT NULL CHECK (min_points >= 0),
    discount_percent NUMERIC(5, 2) NOT NULL CHECK (discount_percent >= 0 AND discount_percent <= 100),
    UNIQUE (clinic_id, min_points)
);
