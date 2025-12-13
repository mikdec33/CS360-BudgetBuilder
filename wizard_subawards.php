<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_login();
if (!isset($_SESSION['budget_overview'])) { header('Location: wizard_overview.php'); exit; }
if (!isset($_SESSION['travel'])) { header('Location: wizard_travel.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') { $_SESSION['subawards']=[]; header('Location: wizard_review.php'); exit; }
include 'partials/header.php';
?>
<div class="row justify-content-center"><div class="col-lg-8"><div class="card shadow-sm p-4">
<h2 class="fw-semibold mb-2">Subawards (Optional)</h2>
<p class="muted">This simplified build skips subawards. Click continue.</p>
<form method="post"><div class="d-flex justify-content-between"><a class="btn btn-outline-dark" href="wizard_travel.php">Back</a><button class="btn btn-primary">Continue</button></div></form>
</div></div></div>
<?php include 'partials/footer.php'; ?>