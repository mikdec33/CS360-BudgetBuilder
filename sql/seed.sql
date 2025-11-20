-- Users
INSERT INTO users (username, password, email)
VALUES ('pi_user', 'hashed_pw1', 'pi@example.edu'),
       ('co_pi_user', 'hashed_pw2', 'co_pi@example.edu');

-- Faculty/Staff
INSERT INTO faculty_staff (name, role, base_salary, appointment_percent)
VALUES ('Dr. Alice Smith', 'PI', 120000, 100),
       ('Dr. Bob Johnson', 'Co-PI', 95000, 50),
       ('Jane Doe', 'Staff', 60000, 100);

-- Postdocs
INSERT INTO postdocs (name, stipend, appointment_percent)
VALUES ('John Postdoc', 48000, 100),
       ('Emily Researcher', 50000, 50);

-- Budget Overview
INSERT INTO budget_overview (title, pi_id, start_year, end_year, f_and_a_rate)
VALUES ('NSF Grant 2025-2028', 1, 2025, 2028, 55);

-- Budget Periods
INSERT INTO budget_periods (budget_id, year, start_date, end_date)
VALUES (1, 2025, '2025-01-01', '2025-12-31'),
       (1, 2026, '2026-01-01', '2026-12-31');

-- Subawards
INSERT INTO subawards (budget_id, institution_name, subaward_total, subaward_f_and_a_rate)
VALUES (1, 'University of Example', 250000, 50),
       (1, 'Research Institute XYZ', 150000, 45);
