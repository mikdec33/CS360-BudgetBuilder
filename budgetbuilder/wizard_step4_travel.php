<?php
require 'config.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $t = [
        'travel_id' => (int)$_POST['travel_id'],
        'duration_days' => (int)$_POST['duration_days'],
        'travelers' => (int)$_POST['travelers'],
        'purpose' => $_POST['purpose']
    ];
    if (!isset($_SESSION['travel'])) $_SESSION['travel'] = [];
    $_SESSION['travel'][] = $t;
    header('Location: wizard_step4_travel.php');
    exit;
}
$travel = $_SESSION['travel'] ?? [];
$profiles = $pdo->query("SELECT travel_id, profile_name FROM travel_profiles")->fetchAll();
require 'templates/header.php';
?>
<h1>Step 4 — Travel</h1>
<form method="post" class="row g-3">
  <div class="col-md-6"><label class="form-label">Profile</label>
    <select name="travel_id" class="form-select">
      <?php foreach ($profiles as $p): ?>
        <option value="<?=$p['travel_id']?>"><?=htmlspecialchars($p['profile_name'])?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-3"><label class="form-label">Duration (days)</label><input name="duration_days" type="number" class="form-control" required></div>
  <div class="col-md-3"><label class="form-label">Travelers</label><input name="travelers" type="number" value="1" class="form-control" required></div>
  <div class="col-12"><label class="form-label">Purpose</label><input name="purpose" class="form-control"></div>
  <div class="col-12"><button class="btn btn-primary">Add Travel</button> <a class="btn btn-secondary" href="wizard_review.php">Next — Review</a></div>
</form>
<h2 class="mt-4">Current Travel Requests</h2>
<ul class="list-group">
<?php foreach ($travel as $t): ?>
  <li class="list-group-item">Profile <?=htmlspecialchars($t['travel_id'])?> — <?=htmlspecialchars($t['duration_days'])?> days — <?=htmlspecialchars($t['travelers'])?> travelers</li>
<?php endforeach; ?>
</ul>
<?php require 'templates/footer.php'; ?>
