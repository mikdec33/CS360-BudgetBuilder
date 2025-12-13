<?php
require_once 'functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username && $email && $password) {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->execute([$username, $email, $password]);
    }
    header('Location: admin_users.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id !== (int)$_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->execute([$id]);
    }
    header('Location: admin_users.php');
    exit;
}

$stmt = $pdo->query("SELECT id, username, email, role FROM users ORDER BY role DESC, username");
$rows = $stmt->fetchAll();

include 'partials/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="bb-card">
      <div class="bb-card-header border-0 p-4 d-flex justify-content-between align-items-center">
        <div>
          <div class="bb-section-title mb-1">Admin · Access</div>
          <h1 class="h5 mb-0">User accounts</h1>
        </div>
        <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-sm">Back to admin</a>
      </div>
      <div class="card-body p-4">
        <form method="post" class="row g-2 align-items-end mb-4">
          <input type="hidden" name="action" value="add">
          <div class="col-md-4">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control form-control-sm" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control form-control-sm" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Password</label>
            <input type="text" name="password" class="form-control form-control-sm" required>
          </div>
          <div class="col-md-1 d-grid">
            <button class="btn btn-primary btn-sm">Add</button>
          </div>
        </form>

        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead><tr><th>Username</th><th>Email</th><th>Role</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              <?php foreach ($rows as $r): ?>
                <tr>
                  <td><?= h($r['username']) ?></td>
                  <td><?= h($r['email']) ?></td>
                  <td><?= h(ucfirst($r['role'])) ?></td>
                  <td class="text-end">
                    <?php if ($r['role'] !== 'admin'): ?>
                      <a href="?delete=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Delete this user account?')">Delete</a>
                    <?php else: ?>
                      <span class="bb-subtle">Admin</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (!$rows): ?>
                <tr><td colspan="4" class="bb-subtle">No user accounts yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
