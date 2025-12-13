<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_login();

if (!isset($_SESSION['budget_overview'])) { header('Location: wizard_overview.php'); exit; }
if (!isset($_SESSION['personnel']) || empty($_SESSION['personnel'])) { header('Location: wizard_personnel.php'); exit; }
if (!isset($_SESSION['students'])) { header('Location: wizard_students.php'); exit; }

$ov = $_SESSION['budget_overview'];
$years = max(1, min(5, (int)$ov['num_years']));

$travel_profiles = $pdo->query("SELECT * FROM travel_profiles ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profile_id = (int)($_POST['travel_profile_id'] ?? 0);
    $project_year = max(1, min($years, (int)($_POST['project_year'] ?? 1)));
    $trips = max(0, (int)($_POST['trips'] ?? 1));
    $days = max(0, (int)($_POST['days'] ?? 3));
    $travelers = max(1, (int)($_POST['travelers'] ?? 1));

    $profile = null;
    foreach ($travel_profiles as $p) {
        if ((int)$p['id'] === $profile_id) { $profile = $p; break; }
    }

    $total = 0;
    if ($profile) {
        $total = ((float)$profile['per_diem'] * $days * $travelers)
              + ((float)$profile['airfare_estimate'] * $travelers)
              + ((float)$profile['lodging_cap'] * $days * $travelers);
        $total *= $trips;
    }

    $_SESSION['travel'] = [
        'travel_profile_id' => $profile_id,
        'project_year' => $project_year,
        'profile_name' => $profile ? $profile['name'] : 'Travel',
        'trips' => $trips,
        'days' => $days,
        'travelers' => $travelers,
        'total_cost' => round($total, 2)
    ];

    header('Location: wizard_review.php');
    exit;
}

include 'partials/header.php';
?>

<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="card shadow-sm p-4">
      <h2 class="fw-semibold mb-1">Travel</h2>
      <p class="muted mb-4">Pick a travel profile and enter trip details. We’ll store a per-year total.</p>

      <form method="post">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Travel Profile</label>
            <select name="travel_profile_id" class="form-select" required>
              <option value="">— Select —</option>
              <?php foreach ($travel_profiles as $p): ?>
                <option value="<?= (int)$p['id'] ?>"><?= h($p['name']) ?> (<?= h($p['type']) ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-2">
            <label class="form-label">Project Year</label>
            <select name="project_year" class="form-select">
              <?php for ($y=1;$y<=$years;$y++): ?>
                <option value="<?= $y ?>">Y<?= $y ?></option>
              <?php endfor; ?>
            </select>
          </div>

          <div class="col-md-1">
            <label class="form-label">Trips</label>
            <input type="number" name="trips" class="form-control" min="0" value="1">
          </div>

          <div class="col-md-1">
            <label class="form-label">Days</label>
            <input type="number" name="days" class="form-control" min="0" value="3">
          </div>

          <div class="col-md-2">
            <label class="form-label">Travelers</label>
            <input type="number" name="travelers" class="form-control" min="1" value="1">
          </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
          <a class="btn btn-outline-dark px-4" href="wizard_students.php">Back</a>
          <button class="btn btn-primary px-4">Next · Review</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'partials/footer.php'; ?>
