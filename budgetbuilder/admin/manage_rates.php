<?php
require '../config.php'; require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['rate_type']) && is_numeric($_POST['rate_value'])) {
        $s = $pdo->prepare('INSERT INTO institutional_rates (rate_type, rate_value, effective_date) VALUES (?, ?, ?)');
        $s->execute([$_POST['rate_type'], $_POST['rate_value'], $_POST['effective_date'] ?: date('Y-m-d')]);
        header('Location: manage_rates.php'); exit;
    }
}
$rates = $pdo->query('SELECT * FROM institutional_rates ORDER BY effective_date DESC')->fetchAll();
require '../templates/header.php';
?>
<h1>Manage Rates</h1>
<form method="post" class="row g-3 mb-3">
  <div class="col-md-4"><input name="rate_type" class="form-control" placeholder="Rate Type (FRINGE/F&A)"></div>
  <div class="col-md-3"><input name="rate_value" class="form-control" placeholder="Value (e.g., 30.00)"></div>
  <div class="col-md-3"><input name="effective_date" type="date" class="form-control"></div>
  <div class="col-md-2"><button class="btn btn-primary">Add Rate</button></div>
</form>
<table class="table"><thead><tr><th>Type</th><th>Value</th><th>Effective</th></tr></thead><tbody>
<?php foreach($rates as $r): ?><tr><td><?=htmlspecialchars($r['rate_type'])?></td><td><?=htmlspecialchars($r['rate_value'])?></td><td><?=htmlspecialchars($r['effective_date'])?></td></tr><?php endforeach; ?>
</tbody></table>
<?php require '../templates/footer.php'; ?>
