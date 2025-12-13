<?php
require_once 'functions.php';
require_login();
$id = (int)($_GET['id'] ?? 0);
include 'partials/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="bb-card">
      <div class="bb-card-header border-0 p-4">
        <h1 class="h5 mb-0">Budget saved</h1>
      </div>
      <div class="card-body p-4">
        <p>Your budget has been saved successfully.</p>
        <p class="mb-3">
          <a href="export.php?id=<?= $id ?>" class="btn btn-primary w-100">Download Excel (XLSX)</a>
        </p>
        <a href="budgets_list.php" class="btn btn-outline-secondary w-100">Back to budgets list</a>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
