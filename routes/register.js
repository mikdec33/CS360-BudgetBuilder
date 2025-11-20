const express = require("express");
const router = express.Router();
const { register } = require("../controllers/authController");

// POST /register
router.post("/", register);

module.exports = router;
