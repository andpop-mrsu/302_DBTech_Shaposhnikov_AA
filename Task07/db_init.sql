PRAGMA foreign_keys = ON;
 
CREATE TABLE employee (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL,
    salary_percent REAL NOT NULL CHECK (salary_percent BETWEEN 0 AND 100),
    hired_at DATE NOT NULL DEFAULT CURRENT_DATE,
    fired_at DATE,
    is_active INTEGER NOT NULL DEFAULT 1
);
 
CREATE TABLE specialization (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE employee_specialization (
    employee_id INTEGER,
    specialization_id INTEGER,
    PRIMARY KEY (employee_id, specialization_id),
    FOREIGN KEY (employee_id) REFERENCES employee(id),
    FOREIGN KEY (specialization_id) REFERENCES specialization(id)
);
 
CREATE TABLE service_category (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE service (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    duration_minutes INTEGER NOT NULL CHECK (duration_minutes > 0),
    price REAL NOT NULL CHECK (price >= 0),
    category_id INTEGER NOT NULL,
    FOREIGN KEY (category_id) REFERENCES service_category(id)
);

CREATE TABLE appointment (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    appointment_time DATETIME NOT NULL,
    status TEXT NOT NULL CHECK (status IN ('planned','completed','cancelled')),
    patient_name TEXT NOT NULL,
    employee_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    FOREIGN KEY (employee_id) REFERENCES employee(id),
    FOREIGN KEY (service_id) REFERENCES service(id)
); 
CREATE TABLE procedure (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    performed_at DATETIME NOT NULL,
    final_price REAL NOT NULL CHECK (final_price >= 0),
    employee_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    FOREIGN KEY (employee_id) REFERENCES employee(id),
    FOREIGN KEY (service_id) REFERENCES service(id)
);
 
INSERT INTO employee (full_name, salary_percent)
VALUES
('Иванов И.И.', 30),
('Петров П.П.', 25);

INSERT INTO specialization (name)
VALUES ('Терапевт'), ('Хирург'), ('Ортодонт');

INSERT INTO employee_specialization
VALUES (1,1), (2,2);

INSERT INTO service_category (name)
VALUES ('Терапия'), ('Хирургия');

INSERT INTO service (name, duration_minutes, price, category_id)
VALUES
('Лечение кариеса', 60, 3500, 1),
('Удаление зуба', 45, 5000, 2);

INSERT INTO appointment
(appointment_time, status, patient_name, employee_id, service_id)
VALUES
('2025-10-01 10:00', 'planned', 'Сидоров А.А.', 1, 1);

INSERT INTO procedure
(performed_at, final_price, employee_id, service_id)
VALUES
('2025-09-20 11:30', 3500, 1, 1);
