<?php
require_once 'functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['budget_overview'] = [
        'title' => trim($_POST['title']),
        'funding_source' => trim($_POST['funding_source']),
        'pi_id' => (int)$_POST['pi_id'],
        'start_year' => (int)$_POST['start_year'],
        'num_years' => (int)$_POST['num_years'],
        'fa_rate_id' => (int)$_POST['fa_rate_id']
    ];
    header('Location: wizard_personnel.php');
    exit;
}

$pis = $pdo->query("SELECT id, first_name, last_name FROM faculty_staff ORDER BY last_name")->fetchAll();
$fa_rates = $pdo->query("SELECT id, label, rate_percent FROM fa_rates ORDER BY rate_percent DESC")->fetchAll();

include 'partials/header.php';
?>

<h2>Project Overview</h2>

<form method="post" class="card p-4 shadow-sm">

  <div class="mb-3">
    <label class="form-label">Project Title</label>
    <input name="title" class="form-control" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Funding Source</label>
    <input name="funding_source" class="form-control" placeholder="NSF, NIH, DOE, etc." required>
  </div>

  <div class="mb-3">
    <label class="form-label">Principal Investigator</label>
    <select name="pi_id" class="form-select" required>
      <?php foreach ($pis as $p): ?>
        <option value="<?= $p['id'] ?>">
          <?= h($p['last_name'] . ', ' . $p['first_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">Start Year</label>
      <input type="number" name="start_year" class="form-control" value="<?= date('Y') ?>" required>
    </div>

    <div class="col-md-6 mb-3">
      <label class="form-label">Number of Years (max 5)</label>
      <input type="number" name="num_years" class="form-control" min="1" max="5" value="3" required>
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">F&A Rate</label>
    <select name="fa_rate_id" class="form-select">
      <?php foreach ($fa_rates as $r): ?>
        <option value="<?= $r['id'] ?>">
          <?= h($r['label']) ?> (<?= $r['rate_percent'] ?>%)
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <button class="btn btn-primary">Next · Personnel</button>
</form>

<?php include 'partials/footer.php'; ?>
