-- Placeholder schema for BudgetBuilder

CREATE DATABASE IF NOT EXISTS budgetbuilder;

USE budgetbuilder;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100)
);

CREATE TABLE budgets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  category VARCHAR(100),
  amount DECIMAL(10,2),
  FOREIGN KEY (user_id) REFERENCES users(id)
);
