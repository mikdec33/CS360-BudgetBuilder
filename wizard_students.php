<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_login();

if (!isset($_SESSION['budget_overview'])) { header('Location: wizard_overview.php'); exit; }
if (!isset($_SESSION['personnel']) || empty($_SESSION['personnel'])) { header('Location: wizard_personnel.php'); exit; }

$ov = $_SESSION['budget_overview'];
$years = max(1, min(5, (int)$ov['num_years']));

$students = $pdo->query("SELECT id, first_name, last_name, level FROM students ORDER BY last_name, first_name")->fetchAll();

function tuition_for_year(PDO $pdo, int $year_index, string $semester, string $residency, int $start_year): float {
    $st = $pdo->prepare("
      SELECT base_tuition, fees, annual_increase_percent, effective_year
      FROM tuition_fees
      WHERE semester=? AND residency=? AND effective_year <= ?
      ORDER BY effective_year DESC
      LIMIT 1
    ");
    $st->execute([$semester, $residency, $start_year]);
    $row = $st->fetch();
    if (!$row) return 0.0;
    $base = (float)$row['base_tuition'] + (float)$row['fees'];
    $inc = ((float)$row['annual_increase_percent'])/100.0;
    $mult = pow(1+$inc, max(0,$year_index-1));
    return round($base*$mult, 2);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)($_POST['student_id'] ?? 0);
    $external = trim($_POST['external_name'] ?? '');
    $fte = min(50, (float)($_POST['fte_percent'] ?? 0));

    $semester = $_POST['semester'] ?? 'Fall';
    $residency = $_POST['residency'] ?? 'in-state';
    $semesters_per_year = max(0, min(3, (int)($_POST['semesters_per_year'] ?? 2)));

    $name = $external;
    if ($student_id) {
        foreach ($students as $s) {
            if ((int)$s['id'] === $student_id) { $name = $s['first_name'].' '.$s['last_name']; break; }
        }
    }
    if ($name === '') $name = 'Student';

    $request = !empty($_POST['request_tuition']);

    $tuition = [];
    for ($y=1;$y<=5;$y++) {
        $tuition[$y] = 0;
        if ($request && $y <= $years) {
            $amt = tuition_for_year($pdo, $y, $semester, $residency, (int)$ov['start_year']);
            $tuition[$y] = round($amt * $semesters_per_year, 2);
        }
    }

    $_SESSION['students'] = [
        'student_id' => $student_id ?: null,
        'external_name' => $name,
        'fte_percent' => $fte,
        'semester' => $semester,
        'residency' => $residency,
        'semesters_per_year' => $semesters_per_year,
        'tuition' => $tuition
    ];

    header('Location: wizard_travel.php');
    exit;
}

include 'partials/header.php';
?>

<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="card shadow-sm p-4">
      <h2 class="fw-semibold mb-1">Students & Tuition</h2>
      <p class="muted mb-4">Student appointments are limited to a maximum of 50% FTE. Tuition is computed from the stored tuition tables.</p>

      <form method="post">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Student (optional)</label>
            <select class="form-select" name="student_id">
              <option value="">—</option>
              <?php foreach ($students as $s): ?>
                <option value="<?= (int)$s['id'] ?>"><?= h($s['last_name'].', '.$s['first_name'].' ('.$s['level'].')') ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">External student name (optional)</label>
            <input class="form-control" name="external_name" placeholder="Student name if not in the database">
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-3">
            <label class="form-label">Appointment FTE % (max 50%)</label>
            <input class="form-control" type="number" step="0.1" name="fte_percent" value="50" min="0" max="50">
          </div>

          <div class="col-md-3">
            <label class="form-label">Residency</label>
            <select class="form-select" name="residency">
              <option value="in-state">In-state</option>
              <option value="out-of-state">Out-of-state</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Semester rate to use</label>
            <select class="form-select" name="semester">
              <option value="Fall">Fall</option>
              <option value="Spring">Spring</option>
              <option value="Summer">Summer</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Semesters per year</label>
            <input class="form-control" type="number" name="semesters_per_year" min="0" max="3" value="2">
          </div>
        </div>

        <div class="form-check form-switch mt-4">
          <input class="form-check-input" type="checkbox" role="switch" id="req" name="request_tuition" checked>
          <label class="form-check-label" for="req">Request tuition support (auto-calculated)</label>
        </div>

        <div class="d-flex justify-content-between mt-4">
          <a class="btn btn-outline-dark px-4" href="wizard_personnel.php">Back</a>
          <button class="btn btn-primary px-4">Next · Travel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'partials/footer.php'; ?>
