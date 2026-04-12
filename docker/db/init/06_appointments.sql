CREATE TABLE appointments (
    id         BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    pet_id     BIGINT NOT NULL REFERENCES pets (id) ON DELETE CASCADE ON UPDATE CASCADE,
    vet_id     BIGINT NOT NULL REFERENCES vet_profiles (user_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    starts_at  TIMESTAMPTZ NOT NULL,
    ends_at    TIMESTAMPTZ NOT NULL,
    reason     VARCHAR(255) NOT NULL,
    status     appointment_status NOT NULL DEFAULT 'scheduled',
    notes      TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT appointments_time_order CHECK (ends_at > starts_at)
);
