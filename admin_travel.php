<?php
require_once 'functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $name  = trim($_POST['name'] ?? '');
    $type  = trim($_POST['type'] ?? 'domestic');
    $pd    = (float)($_POST['per_diem'] ?? 0);
    $air   = (float)($_POST['airfare_estimate'] ?? 0);
    $lod   = (float)($_POST['lodging_cap'] ?? 0);
    if ($name) {
        $stmt = $pdo->prepare("INSERT INTO travel_profiles (name, type, per_diem, airfare_estimate, lodging_cap) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $type, $pd, $air, $lod]);
    }
    header('Location: admin_travel.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM travel_profiles WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: admin_travel.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM travel_profiles ORDER BY name");
$rows = $stmt->fetchAll();

include 'partials/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-9">
    <div class="bb-card">
      <div class="bb-card-header border-0 p-4 d-flex justify-content-between align-items-center">
        <div>
          <div class="bb-section-title mb-1">Admin · Costs</div>
          <h1 class="h5 mb-0">Travel profiles</h1>
        </div>
        <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-sm">Back to admin</a>
      </div>
      <div class="card-body p-4">
        <form method="post" class="row g-2 align-items-end mb-4">
          <input type="hidden" name="action" value="add">
          <div class="col-md-3">
            <label class="form-label">Profile name</label>
            <input type="text" name="profile_name" class="form-control form-control-sm" required>
          </div>
          <div class="col-md-2">
            <label class="form-label">Type</label>
            <select name="travel_type" class="form-select form-select-sm">
              <option value="domestic">Domestic</option>
              <option value="international">International</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Per diem</label>
            <input type="number" step="0.01" name="per_diem" class="form-control form-control-sm">
          </div>
          <div class="col-md-2">
            <label class="form-label">Airfare</label>
            <input type="number" step="0.01" name="airfare_estimate" class="form-control form-control-sm">
          </div>
          <div class="col-md-2">
            <label class="form-label">Lodging cap</label>
            <input type="number" step="0.01" name="lodging_cap" class="form-control form-control-sm">
          </div>
          <div class="col-md-1 d-grid">
            <button class="btn btn-primary btn-sm">Add</button>
          </div>
        </form>

        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead><tr><th>Name</th><th>Type</th><th>Per diem</th><th>Airfare</th><th>Lodging cap</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              <?php foreach ($rows as $r): ?>
                <tr>
                  <td><?= h($r['name']) ?></td>
                  <td><?= h($r['type']) ?></td>
                  <td>$<?= number_format($r['per_diem'], 2) ?></td>
                  <td>$<?= number_format($r['airfare_estimate'], 2) ?></td>
                  <td>$<?= number_format($r['lodging_cap'], 2) ?></td>
                  <td class="text-end">
                    <a href="?delete=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Delete this travel profile?')">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (!$rows): ?>
                <tr><td colspan="6" class="bb-subtle">No profiles yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
