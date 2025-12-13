<?php
require_once 'functions.php';
include 'partials/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="bb-card mb-4">
      <div class="bb-card-header border-0 p-4">
        <h1 class="h4 mb-1">Budget Builder</h1>
        <p class="mb-0 bb-subtle">
          Design compliant multi-year budgets for federal research proposals.
        </p>
      </div>
      <div class="card-body p-4">
        <p class="mb-4">
          BudgetBuilder walks users through a structured budget wizard. It pulls
          institutional rates, tuition schedules, and travel profiles from a central database and generates
          an Excel file for submission.
        </p>
        <?php if (empty($_SESSION['user_id'])): ?>
          <div class="d-flex flex-column flex-md-row gap-2">
            <a href="login.php" class="btn btn-primary w-100 mb-2">Sign In</a>
            <a href="register.php" class="btn btn-outline-dark w-100 mb-2">Create Account</a>
          </div>
          </p>
        <?php else: ?>
          <div class="d-flex flex-column flex-md-row gap-2">
            <a href="wizard_overview.php" class="btn btn-primary flex-fill">Start a new budget</a>
            <a href="budgets_list.php" class="btn btn-outline-secondary flex-fill">View existing budgets</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
