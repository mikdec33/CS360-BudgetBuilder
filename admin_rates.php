<?php
require_once 'functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'add_fringe') {
        $label = trim($_POST['label'] ?? '');
        $rate  = (float)($_POST['rate_percent'] ?? 0);
        $date  = $_POST['effective_date'] ?? date('Y-m-d');
        if ($label) {
            $stmt = $pdo->prepare("INSERT INTO fringe_rates (label, rate_percent, effective_date) VALUES (?, ?, ?)");
            $stmt->execute([$label, $rate, $date]);
        }
    } elseif (($_POST['action'] ?? '') === 'add_fa') {
        $label = trim($_POST['label'] ?? '');
        $rate  = (float)($_POST['rate_percent'] ?? 0);
        $base  = trim($_POST['base_type'] ?? 'MTDC');
        $date  = $_POST['effective_date'] ?? date('Y-m-d');
        if ($label) {
            $stmt = $pdo->prepare("INSERT INTO fa_rates (label, rate_percent, base_type, effective_date) VALUES (?, ?, ?, ?)");
            $stmt->execute([$label, $rate, $base, $date]);
        }
    }
    header('Location: admin_rates.php');
    exit;
}

if (isset($_GET['delete_fringe'])) {
    $id = (int)$_GET['delete_fringe'];
    $stmt = $pdo->prepare("DELETE FROM fringe_rates WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: admin_rates.php');
    exit;
}
if (isset($_GET['delete_fa'])) {
    $id = (int)$_GET['delete_fa'];
    $stmt = $pdo->prepare("DELETE FROM fa_rates WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: admin_rates.php');
    exit;
}

$fringe = $pdo->query("SELECT * FROM fringe_rates ORDER BY effective_date DESC")->fetchAll();
$fa     = $pdo->query("SELECT * FROM fa_rates ORDER BY effective_date DESC")->fetchAll();

include 'partials/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="bb-card mb-3">
      <div class="bb-card-header border-0 p-4 d-flex justify-content-between align-items-center">
        <div>
          <div class="bb-section-title mb-1">Admin · Institutional</div>
          <h1 class="h5 mb-0">Fringe &amp; F&amp;A rates</h1>
        </div>
        <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-sm">Back to admin</a>
      </div>
      <div class="card-body p-4">
        <div class="row g-4">
          <div class="col-md-6">
            <h2 class="h6 mb-2">Fringe rates</h2>
            <form method="post" class="row g-2 align-items-end mb-3">
              <input type="hidden" name="action" value="add_fringe">
              <div class="col-5">
                <label class="form-label">Label</label>
                <input type="text" name="label" class="form-control form-control-sm" required>
              </div>
              <div class="col-3">
                <label class="form-label">Rate %</label>
                <input type="number" step="0.1" name="rate_percent" class="form-control form-control-sm">
              </div>
              <div class="col-3">
                <label class="form-label">Effective date</label>
                <input type="date" name="effective_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
              </div>
              <div class="col-1 d-grid">
                <button class="btn btn-primary btn-sm">Add</button>
              </div>
            </form>
            <div class="table-responsive">
              <table class="table table-sm mb-0">
                <thead><tr><th>Label</th><th>Rate</th><th>Date</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                  <?php foreach ($fringe as $r): ?>
                    <tr>
                      <td><?= h($r['category']) ?></td>
                      <td><?= h($r['rate_percent']) ?>%</td>
                      <td><?= h($r['effective_date']) ?></td>
                      <td class="text-end">
                        <a href="?delete_fringe=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Delete this fringe rate?')">Delete</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                  <?php if (!$fringe): ?>
                    <tr><td colspan="4" class="bb-subtle">No fringe rates yet.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

          <div class="col-md-6">
            <h2 class="h6 mb-2">F&amp;A rates</h2>
            <form method="post" class="row g-2 align-items-end mb-3">
              <input type="hidden" name="action" value="add_fa">
              <div class="col-4">
                <label class="form-label">Label</label>
                <input type="text" name="label" class="form-control form-control-sm" required>
              </div>
              <div class="col-2">
                <label class="form-label">Rate %</label>
                <input type="number" step="0.1" name="rate_percent" class="form-control form-control-sm">
              </div>
              <div class="col-3">
                <label class="form-label">Base type</label>
                <input type="text" name="base_type" class="form-control form-control-sm" value="MTDC">
              </div>
              <div class="col-2">
                <label class="form-label">Effective date</label>
                <input type="date" name="effective_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
              </div>
              <div class="col-1 d-grid">
                <button class="btn btn-primary btn-sm">Add</button>
              </div>
            </form>
            <div class="table-responsive">
              <table class="table table-sm mb-0">
                <thead><tr><th>Label</th><th>Rate</th><th>Base</th><th>Date</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                  <?php foreach ($fa as $r): ?>
                    <tr>
                      <td><?= h($r['label']) ?></td>
                      <td><?= h($r['rate_percent']) ?>%</td>
                      <td><?= h($r['base_type']) ?></td>
                      <td><?= h($r['effective_date']) ?></td>
                      <td class="text-end">
                        <a href="?delete_fa=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Delete this F&A rate?')">Delete</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                  <?php if (!$fa): ?>
                    <tr><td colspan="5" class="bb-subtle">No F&amp;A rates yet.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
