<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_login();

if (!isset($_SESSION['budget_overview'])) { header('Location: wizard_overview.php'); exit; }
if (!isset($_SESSION['personnel']) || empty($_SESSION['personnel'])) { header('Location: wizard_personnel.php'); exit; }
if (!isset($_SESSION['students'])) { header('Location: wizard_students.php'); exit; }
if (!isset($_SESSION['travel'])) { header('Location: wizard_travel.php'); exit; }

$ov = $_SESSION['budget_overview'];
$personnel = $_SESSION['personnel'];
$students = $_SESSION['students'];
$travel = $_SESSION['travel'];

include 'partials/header.php';
?>

<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="card shadow-sm p-4">
      <h2 class="fw-semibold mb-3">Review</h2>

      <h5>Overview</h5>
      <ul class="list-unstyled mb-4">
        <li><strong>Title:</strong> <?= h($ov['title']) ?></li>
        <li><strong>Funding Source:</strong> <?= h($ov['funding_source']) ?></li>
        <li><strong>Start Year:</strong> <?= (int)$ov['start_year'] ?></li>
        <li><strong>Years:</strong> <?= (int)$ov['num_years'] ?></li>
      </ul>

      <h5>Personnel</h5>
      <div class="table-responsive mb-4">
        <table class="table">
          <thead><tr><th>Name</th><th>Category</th><th>Hourly</th><th>Hours (by year)</th></tr></thead>
          <tbody>
          <?php foreach ($personnel as $p): ?>
            <tr>
              <td><?= h($p['external_name'] ?? 'Personnel') ?></td>
              <td><?= h($p['category'] ?? '') ?></td>
              <td>$<?= number_format((float)($p['hourly_rate'] ?? 0), 2) ?></td>
              <td>
                <?php
                  $parts=[];
                  if (is_array($p['hours'] ?? null)) {
                    foreach ($p['hours'] as $y=>$hrs) $parts[] = "Y{$y}: ".(float)$hrs;
                  }
                  echo h(implode(', ', $parts));
                ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <h5>Student / Tuition</h5>
      <p class="mb-4">
        <?= h($students['external_name'] ?? 'Student') ?> — <?= (float)($students['fte_percent'] ?? 0) ?>% FTE
      </p>

      <h5>Travel</h5>
      <p class="mb-4">
        <?= h($travel['profile_name'] ?? 'Travel') ?> (Y<?= (int)($travel['project_year'] ?? 1) ?>) — $<?= number_format((float)($travel['total_cost'] ?? 0), 2) ?>
      </p>

      <form method="post" action="submit_budget.php">
        <div class="d-flex justify-content-between">
          <a class="btn btn-outline-dark px-4" href="wizard_travel.php">Back</a>
          <button class="btn btn-success px-4">Submit & Save Budget</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'partials/footer.php'; ?>
