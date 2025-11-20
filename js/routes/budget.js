const express = require("express");
const router = express.Router();
const { 
  createBudget, 
  getBudget, 
  calculateBudget, 
  submitBudgetWizard 
} = require("../controllers/budgetController");

router.post("/", createBudget);             // Create new budget entry
router.get("/:id", getBudget);              // Retrieve budget by ID
router.post("/calculate", calculateBudget); // Apply rules
router.post("/submit", submitBudgetWizard); // Wizard submission

module.exports = router;
