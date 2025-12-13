<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_login();

$required = ['budget_overview','personnel','students','travel'];
foreach ($required as $k) {
    if (!isset($_SESSION[$k])) { header('Location: wizard_overview.php'); exit; }
}

$ov = $_SESSION['budget_overview'];
$personnel = $_SESSION['personnel'];
$students = $_SESSION['students'];
$travel = $_SESSION['travel'];
$subawards = $_SESSION['subawards'] ?? [];

$pi_id = (int)($ov['pi_id'] ?? 0);
$fa_rate_id = (int)($ov['fa_rate_id'] ?? 0);
if ($pi_id <= 0) die('Invalid PI selected.');
if ($fa_rate_id <= 0) die('Invalid F&A rate selected.');

$created_by = (int)($_SESSION['user_id'] ?? 0);
if ($created_by <= 0) die('Not logged in.');

$pdo->beginTransaction();

try {
    $stmt = $pdo->prepare("
        INSERT INTO budgets (title, funding_source, pi_id, start_year, num_years, fa_rate_id, created_by, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $ov['title'],
        $ov['funding_source'],
        $pi_id,
        (int)$ov['start_year'],
        (int)$ov['num_years'],
        $fa_rate_id,
        $created_by
    ]);
    $budget_id = (int)$pdo->lastInsertId();

    $ps = $pdo->prepare("
        INSERT INTO budget_personnel
        (budget_id, faculty_id, external_name, category, project_year, hourly_rate, hours)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($personnel as $p) {
        $faculty_id = !empty($p['faculty_id']) ? (int)$p['faculty_id'] : null;
        $name = trim($p['external_name'] ?? '');
        if ($name === '') $name = 'Personnel';
        $category = $p['category'] ?? 'Faculty';
        $hourly = (float)($p['hourly_rate'] ?? 0);
        $hours = is_array($p['hours'] ?? null) ? $p['hours'] : [];
        foreach ($hours as $y => $hrs) {
            $y = (int)$y;
            $hrs = (float)$hrs;
            if ($y < 1 || $y > 5 || $hrs <= 0) continue;
            $ps->execute([$budget_id, $faculty_id, $name, $category, $y, $hourly, $hrs]);
        }
    }

    $st = $pdo->prepare("
        INSERT INTO budget_students
        (budget_id, student_id, external_name, project_year, fte_percent, semester, residency, amount)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $student_id = !empty($students['student_id']) ? (int)$students['student_id'] : null;
    $student_name = trim($students['external_name'] ?? 'Student');
    $fte = (float)($students['fte_percent'] ?? 0);
    $semester = $students['semester'] ?? 'Fall';
    $residency = $students['residency'] ?? 'in-state';
    $tuition = is_array($students['tuition'] ?? null) ? $students['tuition'] : [];

    foreach ($tuition as $y => $amt) {
        $y = (int)$y; $amt = (float)$amt;
        if ($y < 1 || $y > 5 || $amt <= 0) continue;
        $st->execute([$budget_id, $student_id, $student_name, $y, $fte, $semester, $residency, $amt]);
    }

    $tr = $pdo->prepare("
        INSERT INTO budget_travel
        (budget_id, travel_profile_id, project_year, trips, days, travelers, total_cost)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $tr->execute([
        $budget_id,
        (int)$travel['travel_profile_id'],
        (int)$travel['project_year'],
        (int)$travel['trips'],
        (int)$travel['days'],
        (int)$travel['travelers'],
        (float)$travel['total_cost']
    ]);

    if (!empty($subawards) && is_array($subawards)) {
        $sa = $pdo->prepare("
            INSERT INTO subawards (budget_id, institution_name, project_year, direct_cost, fa_rate_percent, total_cost)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        foreach ($subawards as $s) {
            $sa->execute([
                $budget_id,
                $s['institution_name'],
                (int)$s['project_year'],
                (float)$s['direct_cost'],
                (float)$s['fa_rate_percent'],
                (float)$s['total_cost'],
            ]);
        }
    }

    $pdo->commit();

    unset($_SESSION['budget_overview'], $_SESSION['personnel'], $_SESSION['students'], $_SESSION['travel'], $_SESSION['subawards']);

    header("Location: budget_success.php?id=" . $budget_id);
    exit;

} catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
}
