const db = require("../db/db");

exports.createBudget = async (req, res) => {
  const { userId, title, startYear, endYear } = req.body;
  await db.query("INSERT INTO budget_overview (title, pi_id, start_year, end_year) VALUES (?, ?, ?, ?)", 
    [title, userId, startYear, endYear]);
  res.json({ success: true });
};

exports.getBudget = async (req, res) => {
  const budgetId = req.params.id;
  const [rows] = await db.query("SELECT * FROM budget_overview WHERE budget_id=?", [budgetId]);
  res.json(rows[0]);
};

exports.calculateBudget = async (req, res) => {
  const { staffId, effortPercent, year } = req.body;

  // Example pseudocode: Salary calculation
  const [staff] = await db.query("SELECT base_salary FROM faculty_staff WHERE staff_id=?", [staffId]);
  const salary = staff[0].base_salary * (effortPercent / 100);

  // Apply fringe
  const [fringe] = await db.query("SELECT rate_value FROM institutional_rates WHERE rate_type='Fringe' ORDER BY effective_date DESC LIMIT 1");
  const fringeCost = salary * (fringe[0].rate_value / 100);

  // Apply F&A
  const [fa] = await db.query("SELECT rate_value FROM institutional_rates WHERE rate_type='F&A' ORDER BY effective_date DESC LIMIT 1");
  const overhead = (salary + fringeCost) * (fa[0].rate_value / 100);

  res.json({ salary, fringeCost, overhead });
};
