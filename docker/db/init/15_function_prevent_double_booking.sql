CREATE FUNCTION fn_prevent_double_booking()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    IF EXISTS (
        SELECT 1
        FROM appointments a
        WHERE a.vet_id = NEW.vet_id
          AND a.id <> NEW.id
          AND a.status <> 'cancelled'
          AND a.starts_at < NEW.ends_at
          AND a.ends_at > NEW.starts_at
    ) THEN
        RAISE EXCEPTION 'Lekarz ma juz wizyte w tym terminie (kolizja harmonogramu).';
    END IF;

    RETURN NEW;
END;
$$;
