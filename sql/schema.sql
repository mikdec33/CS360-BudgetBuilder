CREATE DATABASE IF NOT EXISTS budgetbuilder;
USE budgetbuilder;

-- =========================
-- USERS
-- =========================
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100) UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- FACULTY/STAFF
-- =========================
CREATE TABLE faculty_staff (
  staff_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  role ENUM('PI','Co-PI','Staff') NOT NULL,
  base_salary DECIMAL(12,2) NOT NULL CHECK (base_salary >= 0),
  appointment_percent DECIMAL(5,2) NOT NULL CHECK (appointment_percent BETWEEN 0 AND 100)
);

-- POSTDOCS
CREATE TABLE postdocs (
  postdoc_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  stipend DECIMAL(12,2) NOT NULL CHECK (stipend >= 0),
  appointment_percent DECIMAL(5,2) NOT NULL CHECK (appointment_percent BETWEEN 0 AND 100)
);

-- BUDGET OVERVIEW
CREATE TABLE budget_overview (
  budget_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  pi_id INT NOT NULL,
  start_year INT NOT NULL,
  end_year INT NOT NULL,
  f_and_a_rate DECIMAL(5,2) NOT NULL CHECK (f_and_a_rate >= 0),
  FOREIGN KEY (pi_id) REFERENCES users(id) ON DELETE CASCADE
);

-- BUDGET PERIODS
CREATE TABLE budget_periods (
  period_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT NOT NULL,
  year INT NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id) ON DELETE CASCADE
);

-- COST CATEGORIES
CREATE TABLE cost_categories (
  category_id INT AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(100) NOT NULL UNIQUE
);

-- BUDGET ITEMS
CREATE TABLE budget_items (
  item_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT NOT NULL,
  category_id INT NOT NULL,
  description TEXT NOT NULL,
  quantity INT NOT NULL CHECK (quantity > 0),
  unit_cost DECIMAL(10,2) NOT NULL CHECK (unit_cost >= 0),
  total_cost DECIMAL(12,2) GENERATED ALWAYS AS (quantity * unit_cost) STORED,
  justification TEXT,
  notes TEXT,
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES cost_categories(category_id) ON DELETE RESTRICT
);

-- =========================
-- PERSONNEL EFFORT
-- =========================
CREATE TABLE personnel_effort (
  effort_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT NOT NULL,
  staff_id INT NOT NULL,
  period_id INT NOT NULL,
  effort_percent DECIMAL(5,2) NOT NULL CHECK (effort_percent BETWEEN 0 AND 100),
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id) ON DELETE CASCADE,
  FOREIGN KEY (staff_id) REFERENCES faculty_staff(staff_id) ON DELETE CASCADE,
  FOREIGN KEY (period_id) REFERENCES budget_periods(period_id) ON DELETE CASCADE
);

-- STUDENT SUPPORT
CREATE TABLE student_support (
  support_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT NOT NULL,
  student_id INT NOT NULL,
  semester VARCHAR(10) NOT NULL,
  tuition_type ENUM('in-state','out-of-state') NOT NULL,
  tuition_amount DECIMAL(10,2) NOT NULL CHECK (tuition_amount >= 0),
  stipend_amount DECIMAL(10,2) NOT NULL CHECK (stipend_amount >= 0),
  fte_percent DECIMAL(5,2) NOT NULL CHECK (fte_percent BETWEEN 0 AND 50),
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

-- POSTDOC SUPPORT
CREATE TABLE postdoc_support (
  support_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT NOT NULL,
  postdoc_id INT NOT NULL,
  stipend_amount DECIMAL(10,2) NOT NULL CHECK (stipend_amount >= 0),
  fte_percent DECIMAL(5,2) NOT NULL CHECK (fte_percent BETWEEN 0 AND 100),
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id) ON DELETE CASCADE,
  FOREIGN KEY (postdoc_id) REFERENCES postdocs(postdoc_id) ON DELETE CASCADE
);

-- TUITION & FEES
CREATE TABLE tuition_fees (
  tuition_id INT AUTO_INCREMENT PRIMARY KEY,
  semester VARCHAR(10) NOT NULL,
  tuition_type ENUM('in-state','out-of-state') NOT NULL,
  base_tuition DECIMAL(10,2) NOT NULL CHECK (base_tuition >= 0),
  fee_amount DECIMAL(10,2) NOT NULL CHECK (fee_amount >= 0),
  annual_increase_percent DECIMAL(5,2) CHECK (annual_increase_percent >= 0)
);

-- TRAVEL
CREATE TABLE travel_profiles (
  travel_id INT AUTO_INCREMENT PRIMARY KEY,
  destination_type ENUM('Domestic','International') NOT NULL,
  per_diem DECIMAL(10,2) NOT NULL CHECK (per_diem >= 0),
  airfare_estimate DECIMAL(10,2) NOT NULL CHECK (airfare_estimate >= 0),
  lodging_cap DECIMAL(10,2) NOT NULL CHECK (lodging_cap >= 0)
);

CREATE TABLE travel_requests (
  request_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT NOT NULL,
  travel_id INT NOT NULL,
  duration_days INT NOT NULL CHECK (duration_days > 0),
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id) ON DELETE CASCADE,
  FOREIGN KEY (travel_id) REFERENCES travel_profiles(travel_id) ON DELETE CASCADE
);

-- INSTITUTIONAL RATES
CREATE TABLE institutional_rates (
  rate_id INT AUTO_INCREMENT PRIMARY KEY,
  rate_type VARCHAR(50) NOT NULL,
  rate_value DECIMAL(5,2) NOT NULL CHECK (rate_value >= 0),
  effective_date DATE NOT NULL
);

-- SUBAWARDS
CREATE TABLE subawards (
  subaward_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT NOT NULL,
  institution_name VARCHAR(255) NOT NULL,
  subaward_total DECIMAL(12,2) NOT NULL CHECK (subaward_total >= 0),
  subaward_f_and_a_rate DECIMAL(5,2) NOT NULL CHECK (subaward_f_and_a_rate >= 0),
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id) ON DELETE CASCADE
);
