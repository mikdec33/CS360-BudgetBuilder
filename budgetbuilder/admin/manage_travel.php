<?php
require '../config.php'; require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s = $pdo->prepare('INSERT INTO travel_profiles (profile_name, destination_type, per_diem, airfare_estimate, lodging_cap) VALUES (?, ?, ?, ?, ?)');
    $s->execute([$_POST['profile_name'], $_POST['destination_type'], $_POST['per_diem'], $_POST['airfare_estimate'], $_POST['lodging_cap']]);
    header('Location: manage_travel.php'); exit;
}
$profiles = $pdo->query('SELECT * FROM travel_profiles')->fetchAll();
require '../templates/header.php';
?>
<h1>Travel Profiles</h1>
<form method="post" class="row g-3 mb-3">
  <div class="col-md-4"><input name="profile_name" class="form-control" placeholder="Profile name"></div>
  <div class="col-md-2"><select name="destination_type" class="form-select"><option>Domestic</option><option>International</option></select></div>
  <div class="col-md-2"><input name="per_diem" class="form-control" placeholder="Per diem"></div>
  <div class="col-md-2"><input name="airfare_estimate" class="form-control" placeholder="Airfare"></div>
  <div class="col-md-2"><input name="lodging_cap" class="form-control" placeholder="Lodging cap"></div>
</form>
<table class="table"><thead><tr><th>Name</th><th>Type</th><th>Per Diem</th><th>Airfare</th><th>Lodging</th></tr></thead><tbody>
<?php foreach($profiles as $p): ?><tr><td><?=htmlspecialchars($p['profile_name'])?></td><td><?=htmlspecialchars($p['destination_type'])?></td><td><?=htmlspecialchars($p['per_diem'])?></td><td><?=htmlspecialchars($p['airfare_estimate'])?></td><td><?=htmlspecialchars($p['lodging_cap'])?></td></tr><?php endforeach; ?>
</tbody></table>
<?php require '../templates/footer.php'; ?>
