<?php
require_once 'functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $first = trim($_POST['first_name'] ?? '');
    $last  = trim($_POST['last_name'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $salary = (float)($_POST['base_salary'] ?? 0);
    if ($first && $last) {
        $stmt = $pdo->prepare("INSERT INTO faculty_staff (first_name, last_name, title, base_salary, is_pi_eligible) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$first, $last, $title, $salary]);
    }
    header('Location: admin_faculty.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM faculty_staff WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: admin_faculty.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM faculty_staff ORDER BY last_name, first_name");
$rows = $stmt->fetchAll();

include 'partials/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-9">
    <div class="bb-card">
      <div class="bb-card-header border-0 p-4 d-flex justify-content-between align-items-center">
        <div>
          <div class="bb-section-title mb-1">Admin · People</div>
          <h1 class="h5 mb-0">Faculty &amp; staff</h1>
        </div>
        <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-sm">Back to admin</a>
      </div>
      <div class="card-body p-4">
        <form method="post" class="row g-2 align-items-end mb-4">
          <input type="hidden" name="action" value="add">
          <div class="col-md-3">
            <label class="form-label">First name</label>
            <input type="text" name="first_name" class="form-control form-control-sm" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Last name</label>
            <input type="text" name="last_name" class="form-control form-control-sm" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control form-control-sm">
          </div>
          <div class="col-md-2">
            <label class="form-label">Base salary</label>
            <input type="number" step="0.01" name="base_salary" class="form-control form-control-sm" value="0">
          </div>
          <div class="col-md-1 d-grid">
            <button class="btn btn-primary btn-sm">Add</button>
          </div>
        </form>

        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead><tr><th>Name</th><th>Title</th><th>Base salary</th><th>PI eligible</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              <?php foreach ($rows as $r): ?>
                <tr>
                  <td><?= h($r['last_name'] . ', ' . $r['first_name']) ?></td>
                  <td><?= h($r['title']) ?></td>
                  <td>$<?= number_format($r['base_salary'], 2) ?></td>
                  <td><?= $r['is_pi_eligible'] ? 'Yes' : 'No' ?></td>
                  <td class="text-end">
                    <a href="?delete=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Delete this faculty/staff record?')">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (!$rows): ?>
                <tr><td colspan="5" class="bb-subtle">No records yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
