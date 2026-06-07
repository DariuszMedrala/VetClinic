ALTER TABLE appointment_procedures
    ADD COLUMN vaccine_type_id BIGINT REFERENCES vaccine_types (id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE appointment_procedures
    ADD CONSTRAINT ap_line_kind CHECK ((procedure_id IS NOT NULL) <> (vaccine_type_id IS NOT NULL));
