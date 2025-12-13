<?php
require_once 'functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $sem   = trim($_POST['semester'] ?? '');
    $res   = trim($_POST['residency'] ?? '');
    $base  = (float)($_POST['base_tuition'] ?? 0);
    $fees  = (float)($_POST['fees'] ?? 0);
    $inc   = (float)($_POST['annual_increase_percent'] ?? 3.0);
    $year  = (int)($_POST['effective_year'] ?? date('Y'));
    if ($sem && $res) {
        $stmt = $pdo->prepare("INSERT INTO tuition_fees (semester, residency, base_tuition, fees, annual_increase_percent, effective_year) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$sem, $res, $base, $fees, $inc, $year]);
    }
    header('Location: admin_tuition.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM tuition_fees WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: admin_tuition.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM tuition_fees ORDER BY effective_year DESC, semester, residency");
$rows = $stmt->fetchAll();

include 'partials/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="bb-card">
      <div class="bb-card-header border-0 p-4 d-flex justify-content-between align-items-center">
        <div>
          <div class="bb-section-title mb-1">Admin · Costs</div>
          <h1 class="h5 mb-0">Tuition &amp; fees</h1>
        </div>
        <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-sm">Back to admin</a>
      </div>
      <div class="card-body p-4">
        <form method="post" class="row g-2 align-items-end mb-4">
          <input type="hidden" name="action" value="add">
          <div class="col-md-2">
            <label class="form-label">Semester</label>
            <select name="semester" class="form-select form-select-sm">
              <option value="Fall">Fall</option>
              <option value="Spring">Spring</option>
              <option value="Summer">Summer</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Residency</label>
            <select name="residency" class="form-select form-select-sm">
              <option value="in-state">In-state</option>
              <option value="out-of-state">Out-of-state</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Base tuition</label>
            <input type="number" step="0.01" name="base_tuition" class="form-control form-control-sm">
          </div>
          <div class="col-md-2">
            <label class="form-label">Fees</label>
            <input type="number" step="0.01" name="fees" class="form-control form-control-sm">
          </div>
          <div class="col-md-2">
            <label class="form-label">Increase %</label>
            <input type="number" step="0.1" name="annual_increase_percent" class="form-control form-control-sm" value="3.0">
          </div>
          <div class="col-md-1">
            <label class="form-label">Year</label>
            <input type="number" name="effective_year" class="form-control form-control-sm" value="<?= (int)date('Y') ?>">
          </div>
          <div class="col-md-1 d-grid">
            <button class="btn btn-primary btn-sm">Add</button>
          </div>
        </form>

        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead><tr><th>Semester</th><th>Residency</th><th>Base tuition</th><th>Fees</th><th>Increase %</th><th>Year</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              <?php foreach ($rows as $r): ?>
                <tr>
                  <td><?= h($r['semester']) ?></td>
                  <td><?= h($r['residency']) ?></td>
                  <td>$<?= number_format($r['base_tuition'], 2) ?></td>
                  <td>$<?= number_format($r['fees'], 2) ?></td>
                  <td><?= h($r['annual_increase_percent']) ?>%</td>
                  <td><?= h($r['effective_year']) ?></td>
                  <td class="text-end">
                    <a href="?delete=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Delete this tuition row?')">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (!$rows): ?>
                <tr><td colspan="7" class="bb-subtle">No tuition rows yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
