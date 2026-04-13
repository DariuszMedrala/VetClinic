CREATE TRIGGER trg_prevent_double_booking
BEFORE INSERT OR UPDATE ON appointments
FOR EACH ROW
EXECUTE FUNCTION fn_prevent_double_booking();
