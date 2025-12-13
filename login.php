<?php
require_once 'functions.php';
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT id, username, email, password, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && $password === $user['password']) { // simple plain-text check for lab
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
include 'partials/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-5 col-lg-4">
    <div class="bb-card">
      <div class="bb-card-header border-0 p-4">
        <h1 class="h5 mb-0">Sign in</h1>
      </div>
      <div class="card-body p-4">
        <?php if ($error): ?>
          <div class="alert alert-danger py-2"><?= h($error) ?></div>
        <?php endif; ?>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required autocomplete="username">
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required autocomplete="current-password">
          </div>
          <button class="btn btn-primary w-100">Continue</button>
        </form>
        <p class="text-center mt-3">
          <a href="register.php">Create a new account</a>
        </p>
        <?php if (!empty($_GET['registered'])): ?>
          <div class="alert alert-success mt-3">
            Account created successfully. You may now log in.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
