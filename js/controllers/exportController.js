const db = require("../db/db");
const ExcelJS = require("exceljs");

exports.exportExcel = async (req, res) => {
  const budgetId = req.params.budgetId;

  // Query budget items
  const [items] = await db.query("SELECT * FROM budget_items WHERE budget_id=?", [budgetId]);

  // Create workbook
  const workbook = new ExcelJS.Workbook();
  const sheet = workbook.addWorksheet("Budget");

  // Add headers
  sheet.addRow(["Category", "Description", "Quantity", "Unit Cost", "Total Cost"]);

  // Add rows
  items.forEach(item => {
    sheet.addRow([item.category, item.description, item.quantity, item.unit_cost, item.total_cost]);
  });

  // Stream file to client
  res.setHeader("Content-Type", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
  res.setHeader("Content-Disposition", `attachment; filename=budget_${budgetId}.xlsx`);

  await workbook.xlsx.write(res);
  res.end();
};
