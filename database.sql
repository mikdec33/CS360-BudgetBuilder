-- ============================================================
-- Guided Research Budget Builder (WORKING MATCHED SCHEMA)
-- Import this file into phpMyAdmin (budgetbuilder database)
-- ============================================================
DROP DATABASE IF EXISTS budgetbuilder;
CREATE DATABASE budgetbuilder;
USE budgetbuilder;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS budget_personnel;
DROP TABLE IF EXISTS budget_students;
DROP TABLE IF EXISTS budget_travel;
DROP TABLE IF EXISTS subawards;
DROP TABLE IF EXISTS budgets;
DROP TABLE IF EXISTS tuition_fees;
DROP TABLE IF EXISTS travel_profiles;
DROP TABLE IF EXISTS fringe_rates;
DROP TABLE IF EXISTS fa_rates;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS faculty_staff;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- Users
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user'
);

INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@example.com', 'admin123', 'admin');

-- Faculty & Staff
CREATE TABLE faculty_staff (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(50) NOT NULL,
  last_name VARCHAR(50) NOT NULL,
  title VARCHAR(100),
  base_salary DECIMAL(12,2) NOT NULL,
  is_pi_eligible TINYINT(1) DEFAULT 1
);

INSERT INTO faculty_staff (first_name, last_name, title, base_salary) VALUES
('Alex', 'Principal', 'Professor', 110000),
('Jamie', 'CoPI', 'Associate Professor', 95000),
('Morgan', 'Researcher', 'Assistant Professor', 85000);

-- Students
CREATE TABLE students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(50) NOT NULL,
  last_name VARCHAR(50) NOT NULL,
  level VARCHAR(20),
  department VARCHAR(100)
);

INSERT INTO students (first_name, last_name, level, department) VALUES
('Taylor', 'Grad', 'PhD', 'Computer Science'),
('Riley', 'MS', 'MS', 'Mechanical Engineering'),
('Jordan', 'Assistant', 'PhD', 'Civil Engineering');

-- Travel Profiles
CREATE TABLE travel_profiles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  type ENUM('Domestic','International') NOT NULL,
  per_diem DECIMAL(8,2),
  airfare_estimate DECIMAL(8,2),
  lodging_cap DECIMAL(8,2)
);

INSERT INTO travel_profiles (name, type, per_diem, airfare_estimate, lodging_cap) VALUES
('Domestic conference (3 days)', 'Domestic', 60, 600, 150),
('International workshop (5 days)', 'International', 90, 1400, 220);

-- Tuition & Fees
CREATE TABLE tuition_fees (
  id INT AUTO_INCREMENT PRIMARY KEY,
  semester ENUM('Fall','Spring','Summer') NOT NULL,
  residency ENUM('in-state','out-of-state') NOT NULL,
  base_tuition DECIMAL(10,2) NOT NULL,
  fees DECIMAL(10,2) NOT NULL,
  annual_increase_percent DECIMAL(5,2) NOT NULL,
  effective_year INT NOT NULL
);

INSERT INTO tuition_fees VALUES
(NULL,'Fall','in-state',6000,800,3.0,2025),
(NULL,'Fall','out-of-state',14000,800,3.0,2025),
(NULL,'Spring','in-state',6000,800,3.0,2025),
(NULL,'Spring','out-of-state',14000,800,3.0,2025);

-- Fringe Rates
CREATE TABLE fringe_rates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category ENUM('Faculty','Staff','Student','Temp') NOT NULL,
  rate_percent DECIMAL(5,3) NOT NULL,
  effective_date DATE NOT NULL
);

INSERT INTO fringe_rates (category, rate_percent, effective_date) VALUES
('Faculty', 31.0, '2024-07-01'),
('Staff', 41.3, '2024-07-01'),
('Student', 2.5, '2024-07-01'),
('Temp', 8.3, '2024-07-01');

-- F&A Rates
CREATE TABLE fa_rates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(100) NOT NULL,
  rate_percent DECIMAL(5,2) NOT NULL,
  base_type VARCHAR(50),
  effective_date DATE NOT NULL
);

INSERT INTO fa_rates (label, rate_percent, base_type, effective_date) VALUES
('On-campus research (MTDC)', 54.5, 'MTDC', '2024-07-01');

-- Budgets
CREATE TABLE budgets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  funding_source VARCHAR(100) NOT NULL,
  pi_id INT NOT NULL,
  start_year INT NOT NULL,
  num_years INT NOT NULL,
  fa_rate_id INT NOT NULL,
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pi_id) REFERENCES faculty_staff(id),
  FOREIGN KEY (fa_rate_id) REFERENCES fa_rates(id),
  FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Budget Personnel (HOURS-BASED)
CREATE TABLE budget_personnel (
  id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT NOT NULL,
  faculty_id INT NULL,
  external_name VARCHAR(255) NOT NULL,
  category ENUM('Faculty','Staff','Student','Temp') NOT NULL,
  project_year INT NOT NULL,
  hourly_rate DECIMAL(12,2) NOT NULL DEFAULT 0,
  hours DECIMAL(12,2) NOT NULL DEFAULT 0,
  FOREIGN KEY (budget_id) REFERENCES budgets(id),
  FOREIGN KEY (faculty_id) REFERENCES faculty_staff(id)
);

-- Budget Students (Tuition)
CREATE TABLE budget_students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT NOT NULL,
  student_id INT NULL,
  external_name VARCHAR(255),
  project_year INT NOT NULL,
  fte_percent DECIMAL(5,2) NOT NULL,
  semester ENUM('Fall','Spring','Summer') NOT NULL,
  residency ENUM('in-state','out-of-state') NOT NULL,
  amount DECIMAL(12,2),
  FOREIGN KEY (budget_id) REFERENCES budgets(id),
  FOREIGN KEY (student_id) REFERENCES students(id)
);

-- Budget Travel
CREATE TABLE budget_travel (
  id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT NOT NULL,
  travel_profile_id INT NOT NULL,
  project_year INT NOT NULL,
  trips INT NOT NULL,
  days INT NOT NULL,
  travelers INT NOT NULL,
  total_cost DECIMAL(12,2),
  FOREIGN KEY (budget_id) REFERENCES budgets(id),
  FOREIGN KEY (travel_profile_id) REFERENCES travel_profiles(id)
);

-- Subawards
CREATE TABLE subawards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT NOT NULL,
  institution_name VARCHAR(255) NOT NULL,
  project_year INT NOT NULL,
  direct_cost DECIMAL(12,2) NOT NULL,
  fa_rate_percent DECIMAL(5,2) NOT NULL,
  total_cost DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (budget_id) REFERENCES budgets(id)
);

-- ============================================================
-- End
-- ============================================================
