CREATE TABLE clients (
    user_id        BIGINT PRIMARY KEY REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
    phone          VARCHAR(20),
    loyalty_points INTEGER NOT NULL DEFAULT 0 CHECK (loyalty_points >= 0),
    created_at     TIMESTAMPTZ NOT NULL DEFAULT now()
);
