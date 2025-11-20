<?php
require_once 'config.php';

function calc_salary($annual_salary, $percent_effort, $months=12) {
    $fte = $percent_effort / 100.0;
    return round($annual_salary * $fte * ($months/12.0), 2);
}

function apply_fringe($base_amount, $fringe_percent) {
    return round($base_amount * ($fringe_percent / 100.0), 2);
}

function calc_tuition($base_tuition, $annual_increase_pct, $years_forward=0) {
    $factor = pow(1 + ($annual_increase_pct/100.0), $years_forward);
    return round($base_tuition * $factor, 2);
}

function calc_travel_cost($profile, $days, $travelers=1) {
    $perdiem_total = $profile['per_diem'] * $days * $travelers;
    $airfare_total = $profile['airfare_estimate'] * $travelers;
    $lodging_total = $profile['lodging_cap'] * $days * $travelers;
    return round($perdiem_total + $airfare_total + $lodging_total, 2);
}

function apply_fa($base_amount, $fa_percent) {
    return round($base_amount * ($fa_percent / 100.0), 2);
}

function get_current_rate($pdo, $type) {
    $s = $pdo->prepare("SELECT rate_value FROM institutional_rates WHERE rate_type = ? ORDER BY effective_date DESC LIMIT 1");
    $s->execute([$type]);
    $r = $s->fetch();
    return $r ? (float)$r['rate_value'] : 0.0;
}
?>
