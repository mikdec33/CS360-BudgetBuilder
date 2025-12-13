<?php
require_once __DIR__ . '/config.php';

function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function get_faculty(PDO $pdo) {
    $stmt = $pdo->query("SELECT id, first_name, last_name, title FROM faculty_staff ORDER BY last_name, first_name");
    return $stmt->fetchAll();
}

function get_students(PDO $pdo) {
    $stmt = $pdo->query("SELECT id, first_name, last_name, level FROM students ORDER BY last_name, first_name");
    return $stmt->fetchAll();
}

function get_travel_profiles(PDO $pdo) {
    $stmt = $pdo->query("SELECT id, name, type, per_diem, airfare_estimate, lodging_cap FROM travel_profiles ORDER BY name");
    return $stmt->fetchAll();
}

function get_fa_rates(PDO $pdo) {
    $stmt = $pdo->query("SELECT id, label, rate_percent FROM fa_rates ORDER BY effective_date DESC");
    return $stmt->fetchAll();
}

function get_default_fa_rate(PDO $pdo) {
    $stmt = $pdo->query("SELECT id, label, rate_percent FROM fa_rates ORDER BY effective_date DESC LIMIT 1");
    return $stmt->fetch();
}

function get_default_fringe_rate(PDO $pdo) {
    $stmt = $pdo->query("SELECT rate_percent FROM fringe_rates ORDER BY effective_date DESC LIMIT 1");
    return $stmt->fetchColumn() ?: 0;
}

function calc_salary($base_salary, $effort_percent) {
    return round(($base_salary * ($effort_percent / 100.0)), 2);
}

function calc_fringe($salary, $rate_percent) {
    return round($salary * ($rate_percent / 100.0), 2);
}

function calc_tuition(PDO $pdo, $semester, $residency, $project_year_offset) {
    $stmt = $pdo->prepare("SELECT base_tuition, fees, annual_increase_percent FROM tuition_fees WHERE semester = ? AND residency = ? ORDER BY effective_year DESC LIMIT 1");
    $stmt->execute([$semester, $residency]);
    $row = $stmt->fetch();
    if (!$row) return 0.0;

    $base = $row['base_tuition'] + $row['fees'];
    $rate = $row['annual_increase_percent'] / 100.0;
    $years = max(0, (int)$project_year_offset);
    $amount = $base * pow(1 + $rate, $years);
    return round($amount, 2);
}

function calc_travel_cost($profile, $duration_days, $travelers) {
    $days = max(1, (int)$duration_days);
    $trav = max(1, (int)$travelers);
    $per_diem = (float)$profile['per_diem'];
    $airfare = (float)$profile['airfare_estimate'];
    $lodging_cap = (float)$profile['lodging_cap'];

    $daily = $per_diem + $lodging_cap;
    $total = ($daily * $days + $airfare) * $trav;
    return round($total, 2);
}

function current_user() {
    global $pdo;
    if (empty($_SESSION['user_id'])) return null;
    $st = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $st->execute([$_SESSION['user_id']]);
    return $st->fetch();
}
?>