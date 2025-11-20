<?php
require 'config.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $person = [
        'name' => $_POST['name'],
        'role' => $_POST['role'],
        'annual_salary' => (float)$_POST['annual_salary'],
        'percent_effort' => (float)$_POST['percent_effort'],
        'justification' => $_POST['justification']
    ];
    if (!isset($_SESSION['personnel'])) $_SESSION['personnel'] = [];
    $_SESSION['personnel'][] = $person;
    header('Location: wizard_step2_personnel.php');
    exit;
}
$personnel = $_SESSION['personnel'] ?? [];
require 'templates/header.php';
?>
<h1>Step 2 — Personnel</h1>
<form method="post" class="row g-3">
  <div class="col-md-6"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
  <div class="col-md-6"><label class="form-label">Role</label><input name="role" class="form-control" required></div>
  <div class="col-md-4"><label class="form-label">Annual Salary</label><input name="annual_salary" type="number" step="0.01" class="form-control" required></div>
  <div class="col-md-4"><label class="form-label">Percent Effort</label><input name="percent_effort" type="number" step="0.01" class="form-control" required></div>
  <div class="col-12"><label class="form-label">Justification</label><textarea name="justification" class="form-control"></textarea></div>
  <div class="col-12"><button class="btn btn-primary">Add Person</button> <a class="btn btn-secondary" href="wizard_step3_students.php">Next — Students</a></div>
</form>
<h2 class="mt-4">Current Personnel</h2>
<ul class="list-group">
<?php foreach ($personnel as $p): ?>
  <li class="list-group-item"><?=htmlspecialchars($p['name'])?> — <?=htmlspecialchars($p['role'])?> — <?=htmlspecialchars($p['percent_effort'])?>%</li>
<?php endforeach; ?>
</ul>
<?php require 'templates/footer.php'; ?>
