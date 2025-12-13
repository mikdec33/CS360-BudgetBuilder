<?php
require_once 'functions.php';

if (!empty($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm = trim($_POST['confirm'] ?? '');

    if ($username === '') $errors[] = "Username is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email address is required.";
    if ($password === '') $errors[] = "Password is required.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = "Username or email already exists.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role)
                                   VALUES (?, ?, ?, 'user')");
            $stmt->execute([$username, $email, $password]);

            header("Location: login.php?registered=1");
            exit;
        }
    }
}

include 'partials/header.php';
?>

<div class="row justify-content-center">
  <div class="col-lg-5">
    <div class="card shadow">
      <div class="card-header bg-white border-0 text-center">
        <h3 class="mb-0">Create an Account</h3>
      </div>
      <div class="card-body">

        <?php if ($errors): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach ($errors as $e): ?>
                <li><?= h($e) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="POST">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Email address</label>
            <input type="email" name="email" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Confirm password</label>
            <input type="password" name="confirm" class="form-control" required>
          </div>

          <button class="btn btn-primary w-100">Create Account</button>
        </form>

        <p class="text-center mt-3">
          <a href="login.php">Already have an account? Log in</a>
        </p>

      </div>
    </div>
  </div>
</div>

<?php include 'partials/footer.php'; ?>
