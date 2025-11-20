const db = require("../db/db");

exports.login = async (req, res) => {
  const { username, password } = req.body;
  const [rows] = await db.query("SELECT * FROM users WHERE username=? AND password=?", [username, password]);

  if (rows.length > 0) {
    res.json({ success: true, userId: rows[0].id });
  } else {
    res.json({ success: false });
  }
};
