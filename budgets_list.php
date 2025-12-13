<?php
require_once 'functions.php';
require_login();
include 'partials/header.php';

$stmt = $pdo->prepare("
  SELECT b.id, b.title, b.start_year, b.num_years, f.last_name, f.first_name, b.created_at
  FROM budgets b
  LEFT JOIN faculty_staff f ON b.pi_id = f.id
  WHERE b.created_by = ?
  ORDER BY b.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$budgets = $stmt->fetchAll();
?>
<div class="row">
  <div class="col-12 mb-3 d-flex justify-content-between align-items-center">
    <div>
      <div class="bb-section-title mb-1">Budgets</div>
      <h1 class="h5 mb-0">My budgets</h1>
    </div>
    <a href="wizard_overview.php" class="btn btn-primary btn-sm">New budget</a>
  </div>
  <div class="col-12">
    <div class="bb-card">
      <div class="card-body p-3 p-md-4">
        <?php if (!$budgets): ?>
          <p class="bb-subtle mb-0">No budgets yet. <a href="wizard_overview.php">Create your first budget</a>.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table mb-0">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>PI</th>
                  <th>Years</th>
                  <th>Created</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($budgets as $b): ?>
                  <tr>
                    <td><?= h($b['title']) ?></td>
                    <td><?= h(trim(($b['last_name'] ?? '') . ', ' . ($b['first_name'] ?? ''), ' ,')) ?></td>
                    <td>Y<?= (int)$b['start_year'] ?>–Y<?= (int)$b['start_year'] + (int)$b['num_years'] - 1 ?></td>
                    <td><?= h($b['created_at']) ?></td>
                    <td class="text-end">
                      <a class="btn btn-sm btn-outline-secondary" href="export.php?budget_id=<?= (int)$b['id'] ?>">Download XLSX</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
