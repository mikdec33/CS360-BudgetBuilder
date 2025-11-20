const db = require("../db/db");

exports.createBudget = async (req, res) => {
  const { userId, title, startYear, endYear } = req.body;
  await db.query(
    "INSERT INTO budget_overview (title, pi_id, start_year, end_year) VALUES (?, ?, ?, ?)", 
    [title, userId, startYear, endYear]
  );
  res.json({ success: true });
};

exports.getBudget = async (req, res) => {
  const budgetId = req.params.id;
  const [rows] = await db.query("SELECT * FROM budget_overview WHERE budget_id=?", [budgetId]);
  res.json(rows[0]);
};

exports.calculateBudget = async (req, res) => {
  const { staffId, effortPercent, year } = req.body;

  // Salary calculation
  const [staff] = await db.query("SELECT base_salary FROM faculty_staff WHERE staff_id=?", [staffId]);
  const salary = staff[0].base_salary * (effortPercent / 100);

  // Apply fringe
  const [fringe] = await db.query(
    "SELECT rate_value FROM institutional_rates WHERE rate_type='Fringe' ORDER BY effective_date DESC LIMIT 1"
  );
  const fringeCost = salary * (fringe[0].rate_value / 100);

  // Apply F&A
  const [fa] = await db.query(
    "SELECT rate_value FROM institutional_rates WHERE rate_type='F&A' ORDER BY effective_date DESC LIMIT 1"
  );
  const overhead = (salary + fringeCost) * (fa[0].rate_value / 100);

  res.json({ salary, fringeCost, overhead });
};

// =========================
// WIZARD SUBMISSION HANDLER
// =========================
exports.submitBudgetWizard = async (req, res) => {
  const {
    title,
    startYear,
    endYear,
    piEffort,
    coPiEffort,
    semester,
    tuitionType,
    studentFTE,
    destinationType,
    durationDays,
    subInstitution,
    subTotal,
    subRate,
    userId // ideally passed from session or frontend
  } = req.body;

  const conn = await db.getConnection();
  try {
    await conn.beginTransaction();

    // 1. Insert budget overview
    const [budgetResult] = await conn.query(
      `INSERT INTO budget_overview (title, pi_id, start_year, end_year, f_and_a_rate)
       VALUES (?, ?, ?, ?, ?)`,
      [title, userId, startYear, endYear, 55] // F&A rate demo value
    );
    const budgetId = budgetResult.insertId;

    // 2. Insert personnel effort (PI + Co-PI)
    if (piEffort) {
      await conn.query(
        `INSERT INTO personnel_effort (budget_id, staff_id, period_id, effort_percent)
         VALUES (?, ?, ?, ?)`,
        [budgetId, 1, 1, piEffort] // staff_id/period_id demo values
      );
    }
    if (coPiEffort) {
      await conn.query(
        `INSERT INTO personnel_effort (budget_id, staff_id, period_id, effort_percent)
         VALUES (?, ?, ?, ?)`,
        [budgetId, 2, 1, coPiEffort]
      );
    }

    // 3. Insert student support
    if (semester) {
      await conn.query(
        `INSERT INTO student_support (budget_id, student_id, semester, tuition_type, tuition_amount, stipend_amount, fte_percent)
         VALUES (?, ?, ?, ?, ?, ?, ?)`,
        [budgetId, userId, semester, tuitionType, 10000, 20000, studentFTE] // demo tuition/stipend
      );
    }

    // 4. Insert travel request
    if (destinationType && durationDays) {
      await conn.query(
        `INSERT INTO travel_requests (budget_id, travel_id, duration_days)
         VALUES (?, ?, ?)`,
        [budgetId, 1, durationDays] // travel_id demo value
      );
    }

    // 5. Insert subaward
    if (subInstitution) {
      await conn.query(
        `INSERT INTO subawards (budget_id, institution_name, subaward_total, subaward_f_and_a_rate)
         VALUES (?, ?, ?, ?)`,
        [budgetId, subInstitution, subTotal, subRate]
      );
    }

    await conn.commit();
    res.json({ success: true, budgetId });
  } catch (err) {
    await conn.rollback();
    console.error("Error submitting budget:", err);
    res.status(500).json({ success: false, error: "Failed to submit budget" });
  } finally {
    conn.release();
  }
};
