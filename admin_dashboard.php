<?php
require_once 'functions.php';
require_admin();
include 'partials/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-9">
    <div class="bb-card mb-3">
      <div class="bb-card-header border-0 p-4">
        <div class="bb-section-title mb-1">Administration</div>
        <h1 class="h5 mb-0">Admin dashboard</h1>
      </div>
      <div class="card-body p-4">
        <p class="bb-subtle">
          Manage institutional reference data and user accounts. Changes here immediately affect all new budgets.
        </p>
        <div class="row g-3">
          <div class="col-md-6">
            <a href="admin_faculty.php" class="text-decoration-none">
              <div class="bb-card p-3">
                <div class="bb-section-title mb-1">People</div>
                <h2 class="h6 mb-1">Faculty &amp; staff</h2>
                <p class="bb-subtle mb-0">Manage PIs, co-PIs, and staff records and base salaries.</p>
              </div>
            </a>
          </div>
          <div class="col-md-6">
            <a href="admin_students.php" class="text-decoration-none">
              <div class="bb-card p-3">
                <div class="bb-section-title mb-1">People</div>
                <h2 class="h6 mb-1">Students</h2>
                <p class="bb-subtle mb-0">Maintain the student roster for assistantships.</p>
              </div>
            </a>
          </div>
          <div class="col-md-6">
            <a href="admin_travel.php" class="text-decoration-none">
              <div class="bb-card p-3">
                <div class="bb-section-title mb-1">Costs</div>
                <h2 class="h6 mb-1">Travel profiles</h2>
                <p class="bb-subtle mb-0">Define domestic and international travel cost templates.</p>
              </div>
            </a>
          </div>
          <div class="col-md-6">
            <a href="admin_tuition.php" class="text-decoration-none">
              <div class="bb-card p-3">
                <div class="bb-section-title mb-1">Costs</div>
                <h2 class="h6 mb-1">Tuition &amp; fees</h2>
                <p class="bb-subtle mb-0">Set semester-based tuition and projected increases.</p>
              </div>
            </a>
          </div>
          <div class="col-md-6">
            <a href="admin_rates.php" class="text-decoration-none">
              <div class="bb-card p-3">
                <div class="bb-section-title mb-1">Institutional</div>
                <h2 class="h6 mb-1">Fringe &amp; F&amp;A rates</h2>
                <p class="bb-subtle mb-0">Update institutional fringe and F&amp;A rates.</p>
              </div>
            </a>
          </div>
          <div class="col-md-6">
            <a href="admin_users.php" class="text-decoration-none">
              <div class="bb-card p-3">
                <div class="bb-section-title mb-1">Access</div>
                <h2 class="h6 mb-1">User accounts</h2>
                <p class="bb-subtle mb-0">Create standard accounts with valid email addresses.</p>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
