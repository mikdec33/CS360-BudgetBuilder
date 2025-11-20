<?php
require 'config.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s = [
        'student_id' => (int)$_POST['student_id'],
        'semester' => $_POST['semester'],
        'tuition_type' => $_POST['tuition_type'],
        'stipend' => (float)$_POST['stipend'],
        'fte_percent' => (float)$_POST['fte_percent'],
        'justification' => $_POST['justification']
    ];
    if ($s['fte_percent'] > 50) $s['fte_percent'] = 50;
    if (!isset($_SESSION['students'])) $_SESSION['students'] = [];
    $_SESSION['students'][] = $s;
    header('Location: wizard_step3_students.php');
    exit;
}
$students = $_SESSION['students'] ?? [];
require 'templates/header.php';
?>
<h1>Step 3 — Students</h1>
<form method="post" class="row g-3">
  <div class="col-md-4"><label class="form-label">Student ID</label><input name="student_id" type="number" class="form-control" required></div>
  <div class="col-md-4"><label class="form-label">Semester</label><input name="semester" class="form-control" required></div>
  <div class="col-md-4"><label class="form-label">Tuition Type</label><select name="tuition_type" class="form-select"><option value="in-state">in-state</option><option value="out-of-state">out-of-state</option></select></div>
  <div class="col-md-4"><label class="form-label">Stipend</label><input name="stipend" type="number" step="0.01" class="form-control" required></div>
  <div class="col-md-4"><label class="form-label">FTE Percent (max 50)</label><input name="fte_percent" type="number" step="0.01" class="form-control" required></div>
  <div class="col-12"><label class="form-label">Justification</label><textarea name="justification" class="form-control"></textarea></div>
  <div class="col-12"><button class="btn btn-primary">Add Student Support</button> <a class="btn btn-secondary" href="wizard_step4_travel.php">Next — Travel</a></div>
</form>
<h2 class="mt-4">Current Student Support</h2>
<ul class="list-group">
<?php foreach ($students as $s): ?>
  <li class="list-group-item">Student ID <?=htmlspecialchars($s['student_id'])?> — <?=htmlspecialchars($s['fte_percent'])?>% — $<?=htmlspecialchars($s['stipend'])?></li>
<?php endforeach; ?>
</ul>
<?php require 'templates/footer.php'; ?>
