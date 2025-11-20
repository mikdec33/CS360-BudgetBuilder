<?php
require 'config.php';
require_login();
$overview = $_SESSION['budget_overview'] ?? null;
$personnel = $_SESSION['personnel'] ?? [];
$students = $_SESSION['students'] ?? [];
$travel = $_SESSION['travel'] ?? [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Location: submit_budget.php');
    exit;
}
require 'templates/header.php';
?>
<h1>Review Budget</h1>
<div class="card mb-3"><div class="card-body"><h5>Overview</h5><pre><?php print_r($overview); ?></pre></div></div>
<div class="card mb-3"><div class="card-body"><h5>Personnel</h5><pre><?php print_r($personnel); ?></pre></div></div>
<div class="card mb-3"><div class="card-body"><h5>Students</h5><pre><?php print_r($students); ?></pre></div></div>
<div class="card mb-3"><div class="card-body"><h5>Travel</h5><pre><?php print_r($travel); ?></pre></div></div>
<form method="post"><button class="btn btn-success">Submit Budget</button></form>
<?php require 'templates/footer.php'; ?>
