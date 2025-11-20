const express = require("express");
const router = express.Router();
const { createBudget, getBudget, calculateBudget } = require("../controllers/budgetController");

router.post("/", createBudget);       // Create new budget entry
router.get("/:id", getBudget);        // Retrieve budget by ID
router.post("/calculate", calculateBudget); // Apply rules

module.exports = router;
