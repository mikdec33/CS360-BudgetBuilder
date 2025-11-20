-- database.sql
-- Full schema + sample reference data and sample admin (username: admin, password: admin123)

CREATE DATABASE IF NOT EXISTS budgetbuilder CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE budgetbuilder;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(255),
  role ENUM('pi','researcher','admin') DEFAULT 'researcher'
);

CREATE TABLE IF NOT EXISTS faculty (
  faculty_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  first_name VARCHAR(100),
  last_name VARCHAR(100),
  title VARCHAR(100),
  department VARCHAR(100),
  email VARCHAR(255),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS students (
  student_id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100),
  last_name VARCHAR(100),
  in_state BOOLEAN DEFAULT 1
);

CREATE TABLE IF NOT EXISTS tuition_fees (
  tuition_id INT AUTO_INCREMENT PRIMARY KEY,
  semester VARCHAR(10),
  tuition_type VARCHAR(20),
  base_tuition DECIMAL(10,2),
  fee_amount DECIMAL(10,2),
  annual_increase_percent DECIMAL(5,2)
);

CREATE TABLE IF NOT EXISTS travel_profiles (
  travel_id INT AUTO_INCREMENT PRIMARY KEY,
  profile_name VARCHAR(255),
  destination_type VARCHAR(20),
  per_diem DECIMAL(10,2),
  airfare_estimate DECIMAL(10,2),
  lodging_cap DECIMAL(10,2)
);

CREATE TABLE IF NOT EXISTS institutional_rates (
  rate_id INT AUTO_INCREMENT PRIMARY KEY,
  rate_type VARCHAR(50),
  rate_value DECIMAL(5,2),
  effective_date DATE
);

CREATE TABLE IF NOT EXISTS budget_overview (
  budget_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  pi_id INT,
  start_year INT,
  end_year INT,
  f_and_a_rate DECIMAL(5,2),
  created_by INT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pi_id) REFERENCES faculty(faculty_id),
  FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS budget_items (
  item_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT,
  category VARCHAR(100),
  description TEXT,
  quantity INT DEFAULT 1,
  unit_cost DECIMAL(12,2) DEFAULT 0,
  total_cost DECIMAL(14,2) DEFAULT 0,
  justification TEXT,
  notes TEXT,
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS student_support (
  support_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT,
  student_id INT,
  semester VARCHAR(10),
  tuition_type VARCHAR(20),
  tuition_amount DECIMAL(12,2),
  stipend_amount DECIMAL(12,2),
  fte_percent DECIMAL(5,2),
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS travel_requests (
  request_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT,
  travel_id INT,
  duration_days INT,
  travelers INT DEFAULT 1,
  total_cost DECIMAL(14,2),
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id) ON DELETE CASCADE,
  FOREIGN KEY (travel_id) REFERENCES travel_profiles(travel_id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS subawards (
  subaward_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT,
  institution_name VARCHAR(255),
  subaward_total DECIMAL(14,2),
  subaward_f_and_a_rate DECIMAL(5,2),
  notes TEXT,
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id) ON DELETE CASCADE
);

-- sample reference data
INSERT IGNORE INTO tuition_fees (semester, tuition_type, base_tuition, fee_amount, annual_increase_percent)
VALUES ('Fall','in-state',5000.00,200.00,5.00),
       ('Fall','out-of-state',15000.00,300.00,5.00),
       ('Spring','in-state',5000.00,200.00,5.00),
       ('Spring','out-of-state',15000.00,300.00,5.00);

INSERT IGNORE INTO travel_profiles (profile_name, destination_type, per_diem, airfare_estimate, lodging_cap)
VALUES ('Domestic Standard','Domestic',75.00,400.00,150.00),
       ('International Standard','International',150.00,1200.00,250.00);

INSERT IGNORE INTO institutional_rates (rate_type, rate_value, effective_date)
VALUES ('FRINGE', 30.00, '2024-01-01'),
       ('F&A', 55.00, '2024-01-01');

-- sample admin user (username: admin, password: admin123)
INSERT INTO users (username, password, email, role)
VALUES ('admin', 'admin123', 'admin@example.com', 'admin')
ON DUPLICATE KEY UPDATE username=username;
