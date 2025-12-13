<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_login();

require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$budget_id = 0;
if (!empty($_GET['budget_id'])) $budget_id = (int)$_GET['budget_id'];
if (!$budget_id && !empty($_GET['id'])) $budget_id = (int)$_GET['id'];
if (!$budget_id) die("Missing budget_id");

$stmt = $pdo->prepare("
    SELECT b.*, f.first_name, f.last_name
    FROM budgets b
    JOIN faculty_staff f ON b.pi_id = f.id
    WHERE b.id = ?
");
$stmt->execute([$budget_id]);
$budget = $stmt->fetch();
if (!$budget) die("Budget not found");

$title = $budget['title'];
$start_year = (int)$budget['start_year'];
$num_years = (int)$budget['num_years'];
$pi_name = trim(($budget['first_name'] ?? '') . ' ' . ($budget['last_name'] ?? ''));

$templatePath = __DIR__ . '/UI-Budget-Template.xlsx';
$spreadsheet = IOFactory::load($templatePath);
$sheet = $spreadsheet->getSheetByName('UI AY24-25 SF424');
if (!$sheet) die("Template sheet not found");

$sheet->setCellValue('B1', $title);
$sheet->setCellValue('B2', $budget['funding_source']);
$sheet->setCellValue('B3', $pi_name);
$sheet->setCellValue('B4', $start_year . ' – ' . ($start_year + $num_years - 1));

$personnelStmt = $pdo->prepare("
    SELECT p.*
    FROM budget_personnel p
    WHERE p.budget_id = ?
    ORDER BY p.external_name, p.project_year
");
$personnelStmt->execute([$budget_id]);
$rows = $personnelStmt->fetchAll();

$grouped = [];
foreach ($rows as $r) {
    $key = ($r['external_name'] ?? 'Personnel') . '|' . ($r['category'] ?? '') . '|' . ($r['hourly_rate'] ?? 0);
    if (!isset($grouped[$key])) {
        $grouped[$key] = [
            'name' => $r['external_name'] ?? 'Personnel',
            'category' => $r['category'] ?? '',
            'hourly_rate' => (float)$r['hourly_rate'],
            'hours' => [1=>0,2=>0,3=>0,4=>0,5=>0],
        ];
    }
    $y = (int)$r['project_year'];
    if ($y>=1 && $y<=5) $grouped[$key]['hours'][$y] += (float)$r['hours'];
}

$currentRow = 7;
foreach ($grouped as $p) {
    $sheet->insertNewRowBefore($currentRow, 1);
    $sheet->setCellValue("A$currentRow", $p['name']);
    $sheet->setCellValue("C$currentRow", round($p['hourly_rate'], 2));
    for ($y=1;$y<=5;$y++) {
        $col = chr(ord('C') + $y);
        $sheet->setCellValue("{$col}{$currentRow}", ($y <= $num_years) ? round($p['hours'][$y], 1) : 0);
    }
    $currentRow++;
}

$fringeStmt = $pdo->query("SELECT category, rate_percent FROM fringe_rates ORDER BY effective_date DESC");
$fringes = $fringeStmt->fetchAll();
foreach ($fringes as $f) {
    switch ($f['category']) {
        case 'Faculty': $sheet->setCellValue('C16', ((float)$f['rate_percent'])/100); break;
        case 'Staff':   $sheet->setCellValue('C17', ((float)$f['rate_percent'])/100); break;
        case 'Student': $sheet->setCellValue('C18', ((float)$f['rate_percent'])/100); break;
    }
}

$tuitionStmt = $pdo->prepare("
    SELECT project_year, SUM(amount) AS total
    FROM budget_students
    WHERE budget_id = ?
    GROUP BY project_year
");
$tuitionStmt->execute([$budget_id]);
while ($t = $tuitionStmt->fetch()) {
    $col = chr(ord('C') + (int)$t['project_year']);
    $sheet->setCellValue("{$col}28", (float)$t['total']);
}

$travelStmt = $pdo->prepare("
    SELECT project_year, SUM(total_cost) AS total
    FROM budget_travel
    WHERE budget_id = ?
    GROUP BY project_year
");
$travelStmt->execute([$budget_id]);
while ($tr = $travelStmt->fetch()) {
    $col = chr(ord('C') + (int)$tr['project_year']);
    $sheet->setCellValue("{$col}35", (float)$tr['total']);
}

$subStmt = $pdo->prepare("
    SELECT project_year, SUM(total_cost) AS total
    FROM subawards
    WHERE budget_id = ?
    GROUP BY project_year
");
$subStmt->execute([$budget_id]);
while ($s = $subStmt->fetch()) {
    $col = chr(ord('C') + (int)$s['project_year']);
    $sheet->setCellValue("{$col}42", (float)$s['total']);
}

$filename = "UI_Budget_{$budget_id}.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
