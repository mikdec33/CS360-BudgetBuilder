<?php
require 'config.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['budget_overview'] = [
        'title' => $_POST['title'],
        'start_year' => (int)$_POST['start_year'],
        'end_year' => (int)$_POST['end_year'],
        'f_and_a_rate' => (float)$_POST['f_and_a_rate'],
        'pi_id' => (int)$_POST['pi_id']
    ];
    header('Location: wizard_step2_personnel.php');
    exit;
}
$pis = $pdo->query("SELECT faculty_id, first_name, last_name FROM faculty ORDER BY last_name")->fetchAll();
require 'templates/header.php';
?>
<h1>Step 1 — Five Year Plan</h1>
<form method="post" class="row g-3">
  <div class="col-md-6"><label class="form-label">Title</label><input name="title" class="form-control" required></div>
  <div class="col-md-6"><label class="form-label">PI</label>
    <select name="pi_id" class="form-select" required>
      <?php foreach ($pis as $p): ?>
        <option value="<?= $p['faculty_id'] ?>"><?= htmlspecialchars($p['last_name'] . ', ' . $p['first_name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-3"><label class="form-label">Start Year</label><input type="number" name="start_year" value="<?=date('Y')?>" class="form-control" required></div>
  <div class="col-md-3"><label class="form-label">End Year</label><input type="number" name="end_year" value="<?=date('Y')+4?>" class="form-control" required></div>
  <div class="col-md-3"><label class="form-label">Default F&A %</label><input type="number" step="0.01" name="f_and_a_rate" value="55.00" class="form-control" required></div>
  <div class="col-12"><button class="btn btn-primary">Next — Personnel</button></div>
</form>
<?php require 'templates/footer.php'; ?>
