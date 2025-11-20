const express = require("express");
const router = express.Router();
const { exportExcel } = require("../controllers/exportController");

router.get("/:budgetId", exportExcel);

module.exports = router;
