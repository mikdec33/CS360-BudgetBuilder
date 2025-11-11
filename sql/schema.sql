CREATE DATABASE IF NOT EXISTS budgetbuilder;

USE budgetbuilder;

-- Users table
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100)
);

-- Budgets table (basic linkage to users)
CREATE TABLE budgets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  category VARCHAR(100),
  amount DECIMAL(10,2),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Budget Overview
CREATE TABLE budget_overview (
  budget_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  pi_id INT,
  start_year INT,
  end_year INT,
  f_and_a_rate DECIMAL(5,2),
  FOREIGN KEY (pi_id) REFERENCES users(id)
);

-- Budget Items
CREATE TABLE budget_items (
  item_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT,
  category VARCHAR(100),
  description TEXT,
  quantity INT,
  unit_cost DECIMAL(10,2),
  total_cost DECIMAL(12,2),
  justification TEXT,
  notes TEXT,
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id)
);

-- Student Support
CREATE TABLE student_support (
  support_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT,
  student_id INT,
  semester VARCHAR(10),
  tuition_type VARCHAR(20),
  tuition_amount DECIMAL(10,2),
  stipend_amount DECIMAL(10,2),
  fte_percent DECIMAL(5,2),
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id),
  FOREIGN KEY (student_id) REFERENCES users(id)
);

-- Tuition and Fees
CREATE TABLE tuition_fees (
  tuition_id INT AUTO_INCREMENT PRIMARY KEY,
  semester VARCHAR(10),
  tuition_type VARCHAR(20),
  base_tuition DECIMAL(10,2),
  fee_amount DECIMAL(10,2),
  annual_increase_percent DECIMAL(5,2)
);

-- Travel Profiles
CREATE TABLE travel_profiles (
  travel_id INT AUTO_INCREMENT PRIMARY KEY,
  destination_type VARCHAR(20), -- Domestic or International
  per_diem DECIMAL(10,2),
  airfare_estimate DECIMAL(10,2),
  lodging_cap DECIMAL(10,2)
);

-- Travel Requests
CREATE TABLE travel_requests (
  request_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT,
  travel_id INT,
  duration_days INT,
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id),
  FOREIGN KEY (travel_id) REFERENCES travel_profiles(travel_id)
);

-- Fringe and Institutional Rates
CREATE TABLE institutional_rates (
  rate_id INT AUTO_INCREMENT PRIMARY KEY,
  rate_type VARCHAR(50), -- Fringe, F&A, etc.
  rate_value DECIMAL(5,2),
  effective_date DATE
);

-- Subawards
CREATE TABLE subawards (
  subaward_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT,
  institution_name VARCHAR(255),
  subaward_total DECIMAL(12,2),
  subaward_f_and_a_rate DECIMAL(5,2),
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id)
);
