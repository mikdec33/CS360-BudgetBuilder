DELIMITER $$

CREATE PROCEDURE calculate_indirect_costs(IN budgetId INT)
BEGIN
  DECLARE direct_costs DECIMAL(12,2);
  DECLARE rate DECIMAL(5,2);

  -- Sum direct costs
  SELECT COALESCE(SUM(total_cost),0) INTO direct_costs
  FROM budget_items
  WHERE budget_id = budgetId;

  -- Get F&A rate
  SELECT f_and_a_rate INTO rate
  FROM budget_overview
  WHERE budget_id = budgetId;

  -- Return results
  SELECT budgetId AS Budget,
         direct_costs AS Direct_Costs,
         rate AS FA_Rate,
         direct_costs * (rate/100) AS Indirect_Costs,
         direct_costs + direct_costs * (rate/100) AS Grand_Total;
END$$

DELIMITER ;
