const db = require("../db/db");
const bcrypt = require("bcrypt");

// =========================
// REGISTER NEW USER
// =========================
exports.register = async (req, res) => {
  const { username, password, email } = req.body;

  try {
    // Check if username already exists
    const [existing] = await db.query("SELECT id FROM users WHERE username=?", [username]);
    if (existing.length > 0) {
      return res.status(400).json({ success: false, message: "Username already taken" });
    }

    // Hash password
    const hashedPassword = await bcrypt.hash(password, 10);

    // Insert new user
    await db.query(
      "INSERT INTO users (username, password, email) VALUES (?, ?, ?)",
      [username, hashedPassword, email]
    );

    res.json({ success: true, message: "User registered successfully" });
  } catch (err) {
    console.error("Error registering user:", err);
    res.status(500).json({ success: false, message: "Server error" });
  }
};

// =========================
// LOGIN EXISTING USER
// =========================
exports.login = async (req, res) => {
  const { username, password } = req.body;

  try {
    // Find user
    const [rows] = await db.query("SELECT * FROM users WHERE username=?", [username]);
    if (rows.length === 0) {
      return res.status(400).json({ success: false, message: "Invalid credentials" });
    }

    const user = rows[0];

    // Compare password
    const match = await bcrypt.compare(password, user.password);
    if (!match) {
      return res.status(400).json({ success: false, message: "Invalid credentials" });
    }

    // Success
    res.json({ success: true, userId: user.id });
  } catch (err) {
    console.error("Error logging in:", err);
    res.status(500).json({ success: false, message: "Server error" });
  }
};
