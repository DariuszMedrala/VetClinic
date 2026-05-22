CREATE TABLE vet_availability (
    id         BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    vet_id     BIGINT NOT NULL REFERENCES vet_profiles (user_id) ON DELETE CASCADE ON UPDATE CASCADE,
    weekday    SMALLINT NOT NULL CHECK (weekday BETWEEN 1 AND 7),
    start_time TIME NOT NULL,
    end_time   TIME NOT NULL,
    CONSTRAINT vet_availability_order CHECK (end_time > start_time),
    UNIQUE (vet_id, weekday)
);
