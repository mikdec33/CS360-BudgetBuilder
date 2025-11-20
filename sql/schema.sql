CREATE DATABASE IF NOT EXISTS budgetbuilder;
USE budgetbuilder;

-- Core User Management
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100)
);

-- Faculty/Staff & Personnel
CREATE TABLE faculty_staff (
  staff_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  role VARCHAR(50), -- PI, co-PI, staff
  base_salary DECIMAL(12,2),
  appointment_percent DECIMAL(5,2) -- appointment % (e.g., 100% FTE)
);

CREATE TABLE postdocs (
  postdoc_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  stipend DECIMAL(12,2),
  appointment_percent DECIMAL(5,2)
);

-- Budgets & Periods
CREATE TABLE budget_overview (
  budget_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  pi_id INT,
  start_year INT,
  end_year INT,
  f_and_a_rate DECIMAL(5,2),
  FOREIGN KEY (pi_id) REFERENCES users(id)
);

CREATE TABLE budget_periods (
  period_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT,
  year INT,
  start_date DATE,
  end_date DATE,
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id)
);

-- Budget Items & Categories
CREATE TABLE cost_categories (
  category_id INT AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(100) UNIQUE
);

CREATE TABLE budget_items (
  item_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT,
  category_id INT,
  description TEXT,
  quantity INT,
  unit_cost DECIMAL(10,2),
  total_cost DECIMAL(12,2),
  justification TEXT,
  notes TEXT,
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id),
  FOREIGN KEY (category_id) REFERENCES cost_categories(category_id)
);

-- Personnel Effort Tracking
CREATE TABLE personnel_effort (
  effort_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT,
  staff_id INT,
  period_id INT,
  effort_percent DECIMAL(5,2),
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id),
  FOREIGN KEY (staff_id) REFERENCES faculty_staff(staff_id),
  FOREIGN KEY (period_id) REFERENCES budget_periods(period_id)
);

-- Student & Postdoc Support
CREATE TABLE student_support (
  support_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT,
  student_id INT,
  semester VARCHAR(10),
  tuition_type VARCHAR(20), -- in-state or out-of-state
  tuition_amount DECIMAL(10,2),
  stipend_amount DECIMAL(10,2),
  fte_percent DECIMAL(5,2), -- capped at 50%
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id),
  FOREIGN KEY (student_id) REFERENCES users(id)
);

CREATE TABLE postdoc_support (
  support_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT,
  postdoc_id INT,
  stipend_amount DECIMAL(10,2),
  fte_percent DECIMAL(5,2),
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id),
  FOREIGN KEY (postdoc_id) REFERENCES postdocs(postdoc_id)
);

-- Tuition & Fees
CREATE TABLE tuition_fees (
  tuition_id INT AUTO_INCREMENT PRIMARY KEY,
  semester VARCHAR(10),
  tuition_type VARCHAR(20),
  base_tuition DECIMAL(10,2),
  fee_amount DECIMAL(10,2),
  annual_increase_percent DECIMAL(5,2)
);

-- Travel
CREATE TABLE travel_profiles (
  travel_id INT AUTO_INCREMENT PRIMARY KEY,
  destination_type VARCHAR(20), -- Domestic or International
  per_diem DECIMAL(10,2),
  airfare_estimate DECIMAL(10,2),
  lodging_cap DECIMAL(10,2)
);

CREATE TABLE travel_requests (
  request_id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT,
  travel_id INT,
  duration_days INT,
  FOREIGN KEY (budget_id) REFERENCES budget_overview(budget_id),
  FOREIGN KEY (travel_id) REFERENCES travel_profiles(travel_id)
);


-- Institutional Rates
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
