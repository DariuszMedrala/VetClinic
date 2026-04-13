CREATE FUNCTION fn_calculate_invoice_total(p_appointment_id BIGINT)
RETURNS NUMERIC
LANGUAGE plpgsql
AS $$
DECLARE
    v_subtotal      NUMERIC(10, 2);
    v_points        INTEGER;
    v_discount_rate NUMERIC(4, 2) := 0;
BEGIN
    SELECT COALESCE(SUM(ap.unit_price * ap.quantity), 0)
    INTO v_subtotal
    FROM appointment_procedures ap
    WHERE ap.appointment_id = p_appointment_id;

    SELECT c.loyalty_points
    INTO v_points
    FROM appointments a
    JOIN pets p ON p.id = a.pet_id
    JOIN clients c ON c.user_id = p.client_id
    WHERE a.id = p_appointment_id;

    IF COALESCE(v_points, 0) >= 100 THEN
        v_discount_rate := 0.10;
    END IF;

    RETURN ROUND(v_subtotal * (1 - v_discount_rate), 2);
END;
$$;
