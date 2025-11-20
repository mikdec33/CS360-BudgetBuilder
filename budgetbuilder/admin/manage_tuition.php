<?php
require '../config.php'; require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s = $pdo->prepare('INSERT INTO tuition_fees (semester, tuition_type, base_tuition, fee_amount, annual_increase_percent) VALUES (?, ?, ?, ?, ?)');
    $s->execute([$_POST['semester'], $_POST['tuition_type'], $_POST['base_tuition'], $_POST['fee_amount'], $_POST['annual_increase_percent']]);
    header('Location: manage_tuition.php'); exit;
}
$rows = $pdo->query('SELECT * FROM tuition_fees')->fetchAll();
require '../templates/header.php';
?>
<h1>Tuition & Fees</h1>
<form method="post" class="row g-3 mb-3">
  <div class="col-md-2"><input name="semester" class="form-control" placeholder="Semester"></div>
  <div class="col-md-2"><select name="tuition_type" class="form-select"><option value="in-state">in-state</option><option value="out-of-state">out-of-state</option></select></div>
  <div class="col-md-2"><input name="base_tuition" class="form-control" placeholder="Base tuition"></div>
  <div class="col-md-2"><input name="fee_amount" class="form-control" placeholder="Fee"></div>
  <div class="col-md-2"><input name="annual_increase_percent" class="form-control" placeholder="Annual %"></div>
  <div class="col-md-2"><button class="btn btn-primary">Add</button></div>
</form>
<table class="table"><thead><tr><th>Semester</th><th>Type</th><th>Base</th><th>Fee</th><th>Annual %</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><?=htmlspecialchars($r['semester'])?></td><td><?=htmlspecialchars($r['tuition_type'])?></td><td><?=htmlspecialchars($r['base_tuition'])?></td><td><?=htmlspecialchars($r['fee_amount'])?></td><td><?=htmlspecialchars($r['annual_increase_percent'])?></td></tr><?php endforeach; ?>
</tbody></table>
<?php require '../templates/footer.php'; ?>
