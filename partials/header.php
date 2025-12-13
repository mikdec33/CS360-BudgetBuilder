<?php
require_once __DIR__ . '/../functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Budget Builder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bb-bg: #f5f5f7;
            --bb-card: #ffffff;
            --bb-border: #e0e0e5;
            --bb-accent: #007aff;
            --bb-accent-soft: rgba(0,122,255,0.08);
            --bb-text-main: #111827;
            --bb-text-muted: #6b7280;
        }
        body {
            background: radial-gradient(circle at top, #ffffff 0, #f5f5f7 45%, #e5e7eb 100%);
            color: var(--bb-text-main);
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", system-ui, sans-serif;
            min-height: 100vh;
        }
        .navbar {
            backdrop-filter: blur(24px);
            background: rgba(255,255,255,0.84);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .navbar-brand {
            font-weight: 600;
            letter-spacing: -0.02em;
        }
        .bb-card {
            background: var(--bb-card);
            border-radius: 20px;
            border: 1px solid var(--bb-border);
            box-shadow: 0 18px 45px rgba(15,23,42,0.06);
        }
        .bb-card-header {
            background: linear-gradient(180deg, rgba(255,255,255,0.9), rgba(255,255,255,0.7));
            border-bottom: 1px solid var(--bb-border);
            border-radius: 20px 20px 0 0;
        }
        .bb-step-pill {
            border-radius: 999px;
            padding: 0.25rem 0.75rem;
            font-size: 0.72rem;
        }
        .bb-step-pill.active {
            background: var(--bb-accent);
            color: #fff;
        }
        .bb-step-pill.inactive {
            background: #f3f4f6;
            color: var(--bb-text-muted);
        }
        .btn-primary {
            background: var(--bb-accent);
            border-color: var(--bb-accent);
            border-radius: 999px;
            font-weight: 500;
        }
        .btn-primary:hover {
            background: #0056d6;
            border-color: #0056d6;
        }
        .btn-outline-light,
        .btn-outline-secondary {
            border-radius: 999px;
        }
        .bb-section-title {
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--bb-text-muted);
        }
        .table {
            border-radius: 14px;
            overflow: hidden;
        }
        .table thead th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--bb-text-muted);
            background-color: #f9fafb;
        }
        .table tbody td {
            vertical-align: middle;
            font-size: 0.84rem;
        }
        .bb-subtle {
            color: var(--bb-text-muted);
            font-size: 0.85rem;
        }
        .bb-badge {
            background: var(--bb-accent-soft);
            color: var(--bb-accent);
            border-radius: 999px;
            padding: 0.15rem 0.6rem;
            font-size: 0.7rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container py-2">
    <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
      <span class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width:26px;height:26px;background:var(--bb-accent-soft);">
        <span style="display:block;width:14px;height:14px;border-radius:6px;background:var(--bb-accent);"></span>
      </span>
      <span>Budget Builder</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if (!empty($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="wizard_overview.php">New Budget</a></li>
          <li class="nav-item"><a class="nav-link" href="budgets_list.php">My Budgets</a></li>
          <?php if (is_admin()): ?>
            <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin</a></li>
          <?php endif; ?>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav ms-auto align-items-center">
        <?php if (!empty($_SESSION['user_id'])): ?>
          <li class="nav-item me-2">
            <span class="bb-subtle">
              <?= h($_SESSION['email'] ?? $_SESSION['username'] ?? 'User') ?>
              <?php if (is_admin()): ?>
                <span class="bb-badge ms-1">Admin</span>
              <?php endif; ?>
            </span>
          </li>
          <li class="nav-item">
            <a class="btn btn-outline-secondary btn-sm" href="logout.php">Sign out</a>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="btn btn-primary btn-sm" href="login.php">Sign in</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container py-4 pb-5">
