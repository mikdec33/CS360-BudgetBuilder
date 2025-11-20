<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $u = $stmt->fetch();
    if ($u && $password === $u['password']) {
        $_SESSION['user_id'] = $u['id'];
        header("Location: wizard_step1.php");
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
require 'templates/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h3 class="card-title mb-3">Login</h3>
        <?php if (!empty($error)) echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>'; ?>
        <form method="post">
          <div class="mb-3"><label>Username</label><input name="username" class="form-control" required></div>
          <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
          <button class="btn btn-primary">Login</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require 'templates/footer.php'; ?>
