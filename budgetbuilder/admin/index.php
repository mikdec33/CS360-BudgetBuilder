<?php require '../config.php'; require_login(); require '../templates/header.php'; ?>
<h1>Admin Console</h1>
<ul class="list-group">
  <li class="list-group-item"><a href="manage_faculty.php">Manage Faculty</a></li>
  <li class="list-group-item"><a href="manage_tuition.php">Manage Tuition & Fees</a></li>
  <li class="list-group-item"><a href="manage_travel.php">Manage Travel Profiles</a></li>
  <li class="list-group-item"><a href="manage_rates.php">Manage Institutional Rates</a></li>
</ul>
<?php require '../templates/footer.php'; ?>
