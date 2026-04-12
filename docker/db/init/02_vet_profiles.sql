CREATE TABLE vet_profiles (
    user_id        BIGINT PRIMARY KEY REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
    license_number VARCHAR(50) NOT NULL UNIQUE,
    title          VARCHAR(20) NOT NULL DEFAULT 'Dr',
    room           VARCHAR(50),
    specialization VARCHAR(100)
);
