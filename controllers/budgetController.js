const db = require("../db/db");

// =========================
// CREATE NEW BUDGET
// =========================
exports.createBudget = async (req, res) => {
  const { userId, title, startYear, endYear } = req.body;
  await db.query(
    "INSERT INTO budget_overview (title, pi_id, start_year, end_year) VALUES (?, ?, ?, ?)", 
    [title, userId, startYear, endYear]
  );
  res.json({ success: true });
};

// =========================
// GET BUDGET (simple overview)
// =========================
exports.getBudgetOverview = async (req, res) => {
  const budgetId = req.params.id;
  const [rows] = await db.query("SELECT * FROM budget_overview WHERE budget_id=?", [budgetId]);
  res.json(rows[0]);
};

// =========================
// GET BUDGET (expanded with related tables)
// =========================
exports.getBudget = async (req, res) => {
  const budgetId = req.params.id;

  try {
    // 1. Overview
    const [overviewRows] = await db.query(
      "SELECT * FROM budget_overview WHERE budget_id=?",
      [budgetId]
    );
    if (overviewRows.length === 0) {
      return res.status(404).json({ success: false, message: "Budget not found" });
    }
    const overview = overviewRows[0];

    // 2. Personnel Effort
    const [personnelRows] = await db.query(
      "SELECT staff_id, period_id, effort_percent FROM personnel_effort WHERE budget_id=?",
      [budgetId]
    );

    // 3. Student Support
    const [studentRows] = await db.query(
      "SELECT student_id, semester, tuition_type, tuition_amount, stipend_amount, fte_percent FROM student_support WHERE budget_id=?",
      [budgetId]
    );

    // 4. Travel Requests
    const [travelRows] = await db.query(
      "SELECT travel_id, duration_days FROM travel_requests WHERE budget_id=?",
      [budgetId]
    );

    // 5. Subawards
    const [subawardRows] = await db.query(
      "SELECT institution_name, subaward_total, subaward_f_and_a_rate FROM subawards WHERE budget_id=?",
      [budgetId]
    );

    // Combine into one object
    const budget = {
      ...overview,
      personnel: personnelRows,
      students: studentRows,
      travel: travelRows,
      subawards: subawardRows
    };

    res.json({ success: true, budget });
  } catch (err) {
    console.error("Error fetching budget:", err);
    res.status(500).json({ success: false, message: "Failed to fetch budget" });
  }
};

// =========================
// LIST BUDGETS BY USER
// =========================
exports.listBudgetsByUser = async (req, res) => {
  const { userId } = req.body; 
  try {
    const [rows] = await db.query(
      "SELECT budget_id, title, start_year, end_year FROM budget_overview WHERE pi_id=?",
      [userId]
    );
    res.json({ success: true, budgets: rows });
  } catch (err) {
    console.error("Error fetching budgets:", err);
    res.status(500).json({ success: false, message: "Failed to fetch budgets" });
  }
};

// =========================
// INDIRECT COST CALCULATIONS
// =========================
exports.calculateIndirectCosts = async (req, res) => {
  const { budgetId } = req.body;
  try {
    const [rows] = await db.query("CALL calculate_indirect_costs(?)", [budgetId]);
    const result = rows[0][0]; 
    res.json({
      budgetId: result.Budget,
      directCosts: result.Direct_Costs,
      faRate: result.FA_Rate,
      indirectCosts: result.Indirect_Costs,
      grandTotal: result.Grand_Total
    });
  } catch (err) {
    console.error("Error calling procedure:", err);
    res.status(500).json({ error: "Failed to calculate indirect costs" });
  }
};

exports.calculateBudget = exports.calculateIndirectCosts; // alias

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
    userId
  } = req.body;

  const conn = await db.getConnection();
  try {
    await conn.beginTransaction();

    // 1. Insert budget overview
    const [budgetResult] = await conn.query(
      `INSERT INTO budget_overview (title, pi_id, start_year, end_year, f_and_a_rate)
       VALUES (?, ?, ?, ?, ?)`,
      [title, userId, startYear, endYear, 55]
    );
    const budgetId = budgetResult.insertId;

    // 2. Personnel effort
    if (piEffort) {
      await conn.query(
        `INSERT INTO personnel_effort (budget_id, staff_id, period_id, effort_percent)
         VALUES (?, ?, ?, ?)`,
        [budgetId, 1, 1, piEffort]
      );
    }
    if (coPiEffort) {
      await conn.query(
        `INSERT INTO personnel_effort (budget_id, staff_id, period_id, effort_percent)
         VALUES (?, ?, ?, ?)`,
        [budgetId, 2, 1, coPiEffort]
      );
    }

    // 3. Student support
    if (semester) {
      await conn.query(
        `INSERT INTO student_support (budget_id, student_id, semester, tuition_type, tuition_amount, stipend_amount, fte_percent)
         VALUES (?, ?, ?, ?, ?, ?, ?)`,
        [budgetId, userId, semester, tuitionType, 10000, 20000, studentFTE]
      );
    }

    // 4. Travel
    if (destinationType && durationDays) {
      await conn.query(
        `INSERT INTO travel_requests (budget_id, travel_id, duration_days)
         VALUES (?, ?, ?)`,
        [budgetId, 1, durationDays]
      );
    }

    // 5. Subawards
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
