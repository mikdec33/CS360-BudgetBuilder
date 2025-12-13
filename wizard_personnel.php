<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_login();

if (!isset($_SESSION['budget_overview'])) {
    header('Location: wizard_overview.php');
    exit;
}

$years = (int)($_SESSION['budget_overview']['num_years'] ?? 1);
$years = max(1, min(5, $years));

if (!isset($_SESSION['personnel']) || !is_array($_SESSION['personnel'])) {
    $_SESSION['personnel'] = [];
}

$faculty = $pdo->query("SELECT id, first_name, last_name, base_salary FROM faculty_staff ORDER BY last_name, first_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Add personnel row (repeatable)
    if (isset($_POST['add_personnel'])) {

        $faculty_id = (int)($_POST['faculty_id'] ?? 0);
        $external_name = trim($_POST['external_name'] ?? '');
        $category = $_POST['category'] ?? 'Faculty';

        // Hourly rate: user can override; default from base_salary/2080 if faculty selected
        $hourly_rate = (float)($_POST['hourly_rate'] ?? 0);
        if ($hourly_rate <= 0 && $faculty_id > 0) {
            foreach ($faculty as $f) {
                if ((int)$f['id'] === $faculty_id) {
                    $hourly_rate = round(((float)$f['base_salary']) / 2080.0, 2);
                    break;
                }
            }
        }

        $hours = [];
        $hours_in = $_POST['hours'] ?? [];
        if (is_array($hours_in)) {
            foreach ($hours_in as $y => $hrs) {
                $y = (int)$y;
                if ($y < 1 || $y > 5) continue;
                $hours[$y] = max(0, (float)$hrs);
            }
        }

        // Determine display name
        $name = $external_name;
        if ($name === '' && $faculty_id > 0) {
            foreach ($faculty as $f) {
                if ((int)$f['id'] === $faculty_id) {
                    $name = $f['first_name'] . ' ' . $f['last_name'];
                    break;
                }
            }
        }
        if ($name === '') $name = 'Personnel';

        $_SESSION['personnel'][] = [
            'faculty_id' => $faculty_id ?: null,
            'external_name' => $name,
            'category' => $category,
            'hourly_rate' => $hourly_rate,
            'hours' => $hours
        ];
    }

    // Remove action
    if (isset($_POST['remove_idx'])) {
        $idx = (int)$_POST['remove_idx'];
        if (isset($_SESSION['personnel'][$idx])) {
            array_splice($_SESSION['personnel'], $idx, 1);
        }
    }

    // Continue to next step
    if (isset($_POST['continue'])) {
        if (empty($_SESSION['personnel'])) {
            $error = "Add at least one personnel entry before continuing.";
        } else {
            header('Location: wizard_students.php');
            exit;
        }
    }
}

include 'partials/header.php';
?>

<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="card shadow-sm p-4">
      <h2 class="fw-semibold mb-1">Personnel (Hours-Based)</h2>
      <p class="muted mb-4">Add personnel and enter hourly rate and hours per project year.</p>

      <?php if (!empty($error)): ?>
        <div class="alert alert-warning"><?= h($error) ?></div>
      <?php endif; ?>

      <form method="post">
        <div class="row g-3">
          <div class="col-md-5">
            <label class="form-label">Faculty/Staff (optional)</label>
            <select name="faculty_id" class="form-select">
              <option value="">—</option>
              <?php foreach ($faculty as $f): ?>
                <option value="<?= (int)$f['id'] ?>"><?= h($f['last_name'] . ', ' . $f['first_name']) ?></option>
              <?php endforeach; ?>
            </select>
            <div class="muted small mt-1">If selected, hourly rate can auto-fill from base salary / 2080.</div>
          </div>

          <div class="col-md-4">
            <label class="form-label">Or external name</label>
            <input name="external_name" class="form-control" placeholder="Name if not listed">
          </div>

          <div class="col-md-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-select">
              <option value="Faculty">Faculty</option>
              <option value="Staff">Staff</option>
              <option value="Student">Student</option>
              <option value="Temp">Temp</option>
            </select>
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-3">
            <label class="form-label">Hourly Rate ($/hr)</label>
            <input type="number" step="0.01" min="0" name="hourly_rate" class="form-control" placeholder="e.g. 45.00">
          </div>

          <?php for ($y=1; $y<=$years; $y++): ?>
            <div class="col">
              <label class="form-label">Y<?= $y ?> Hours</label>
              <input type="number" step="0.1" min="0" name="hours[<?= $y ?>]" class="form-control" placeholder="e.g. 400">
            </div>
          <?php endfor; ?>
        </div>

        <div class="mt-3 d-flex gap-2">
          <button type="submit" name="add_personnel" class="btn btn-outline-primary">+ Add Personnel</button>
          <button type="submit" name="continue" class="btn btn-primary ms-auto">Next · Students</button>
        </div>
      </form>

      <?php if (!empty($_SESSION['personnel'])): ?>
        <hr class="my-4">
        <h6 class="mb-3">Current personnel</h6>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Name</th><th>Category</th><th>Hourly</th>
                <?php for ($y=1;$y<=$years;$y++): ?><th>Y<?= $y ?> hrs</th><?php endfor; ?>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($_SESSION['personnel'] as $i => $p): ?>
                <tr>
                  <td><?= h($p['external_name'] ?? 'Personnel') ?></td>
                  <td><?= h($p['category'] ?? '') ?></td>
                  <td>$<?= number_format((float)($p['hourly_rate'] ?? 0), 2) ?></td>
                  <?php for ($y=1;$y<=$years;$y++): ?>
                    <td><?= h($p['hours'][$y] ?? 0) ?></td>
                  <?php endfor; ?>
                  <td class="text-end">
                    <form method="post" style="display:inline">
                      <input type="hidden" name="remove_idx" value="<?= (int)$i ?>">
                      <button class="btn btn-sm btn-outline-danger">Remove</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <div class="mt-3">
        <a class="btn btn-outline-dark" href="wizard_overview.php">Back · Overview</a>
      </div>
    </div>
  </div>
</div>

<?php include 'partials/footer.php'; ?>
