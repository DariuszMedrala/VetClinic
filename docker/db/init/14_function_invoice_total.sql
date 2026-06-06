CREATE FUNCTION fn_calculate_invoice_total(p_appointment_id BIGINT)
RETURNS NUMERIC
LANGUAGE plpgsql
AS $$
DECLARE
    v_subtotal      NUMERIC(10, 2);
    v_points        INTEGER;
    v_clinic        BIGINT;
    v_discount_rate NUMERIC(6, 4) := 0;
BEGIN
    SELECT COALESCE(SUM(ap.unit_price * ap.quantity), 0)
    INTO v_subtotal
    FROM appointment_procedures ap
    WHERE ap.appointment_id = p_appointment_id;

    SELECT c.loyalty_points, u.clinic_id
    INTO v_points, v_clinic
    FROM appointments a
    JOIN pets p ON p.id = a.pet_id
    JOIN clients c ON c.user_id = p.client_id
    JOIN users u ON u.id = c.user_id
    WHERE a.id = p_appointment_id;

    SELECT COALESCE(MAX(discount_percent), 0) / 100
    INTO v_discount_rate
    FROM loyalty_tiers
    WHERE clinic_id = v_clinic AND min_points <= COALESCE(v_points, 0);

    RETURN ROUND(v_subtotal * (1 - v_discount_rate), 2);
END;
$$;
