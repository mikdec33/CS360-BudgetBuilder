<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BudgetBuilder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand" href="wizard_step1.php">BudgetBuilder</a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
          <?php if(!empty($_SESSION['user_id'])): ?>
            <li class="nav-item"><a class="nav-link" href="wizard_step1.php">Wizard</a></li>
            <li class="nav-item"><a class="nav-link" href="admin/index.php">Admin</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container mt-4">
