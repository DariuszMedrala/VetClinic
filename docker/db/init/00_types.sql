CREATE TYPE user_role AS ENUM ('client', 'vet', 'admin');

CREATE TYPE animal_sex AS ENUM ('male', 'female', 'unknown');

CREATE TYPE appointment_status AS ENUM ('scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled');

CREATE TYPE procedure_type AS ENUM ('treatment', 'medication');

CREATE TYPE invoice_status AS ENUM ('pending', 'paid', 'cancelled');

CREATE TYPE payment_method AS ENUM ('card', 'cash', 'insurance');
