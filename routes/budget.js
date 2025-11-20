const express = require("express");
const router = express.Router();
const { 
    createBudget, 
    getBudget, 
    calculateBudget, 
    submitBudgetWizard,
    calculateIndirectCosts,
    listBudgetsByUser,
    getBudgetOverview
  } = require("../controllers/budgetController");
  
  router.post("/", createBudget);
  router.get("/:id", getBudget);
  router.post("/calculate", calculateBudget);
  router.post("/submit", submitBudgetWizard);
  router.post("/indirect", calculateIndirectCosts);
  router.post("/list", listBudgetsByUser);
  router.get("/:id/overview", getBudgetOverview);
  
module.exports = router;
