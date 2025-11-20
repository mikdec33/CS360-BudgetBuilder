<?php
require '../config.php'; require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s = $pdo->prepare('INSERT INTO faculty (user_id, first_name, last_name, title, department, email) VALUES (?, ?, ?, ?, ?, ?)');
    $s->execute([$_POST['user_id'] ?: null, $_POST['first_name'], $_POST['last_name'], $_POST['title'], $_POST['department'], $_POST['email']]);
    header('Location: manage_faculty.php'); exit;
}
$rows = $pdo->query('SELECT * FROM faculty')->fetchAll();
require '../templates/header.php';
?>
<h1>Faculty</h1>
<form method="post" class="row g-3 mb-3">
  <div class="col-md-2"><input name="user_id" class="form-control" placeholder="User ID (optional)"></div>
  <div class="col-md-2"><input name="first_name" class="form-control" placeholder="First"></div>
  <div class="col-md-2"><input name="last_name" class="form-control" placeholder="Last"></div>
  <div class="col-md-2"><input name="title" class="form-control" placeholder="Title"></div>
  <div class="col-md-2"><input name="department" class="form-control" placeholder="Department"></div>
  <div class="col-md-2"><input name="email" class="form-control" placeholder="Email"></div>
</form>
<table class="table"><thead><tr><th>Name</th><th>Dept</th><th>Email</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><?=htmlspecialchars($r['last_name'] . ', ' . $r['first_name'])?></td><td><?=htmlspecialchars($r['department'])?></td><td><?=htmlspecialchars($r['email'])?></td></tr><?php endforeach; ?>
</tbody></table>
<?php require '../templates/footer.php'; ?>
