<?php
require 'config.php';
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (empty($_GET['budget_id'])) die('budget_id required');
$budget_id = (int)$_GET['budget_id'];

$ov = $pdo->prepare("SELECT bo.*, f.first_name, f.last_name FROM budget_overview bo LEFT JOIN faculty f ON bo.pi_id = f.faculty_id WHERE bo.budget_id = ?");
$ov->execute([$budget_id]);
$overview = $ov->fetch();
if (!$overview) die('Budget not found');

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Overview');
$sheet->fromArray(['Budget ID','Title','PI','Start Year','End Year','F&A Rate','Created At'], NULL, 'A1');
$piName = trim(($overview['last_name'] ?? '') . ', ' . ($overview['first_name'] ?? ''));
$sheet->fromArray([$overview['budget_id'],$overview['title'],$piName,$overview['start_year'],$overview['end_year'],$overview['f_and_a_rate'],$overview['created_at']], NULL, 'A2');

$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle('Items');
$sheet2->fromArray(['Item ID','Category','Description','Quantity','Unit Cost','Total Cost','Justification','Notes'], NULL, 'A1');
$items = $pdo->prepare("SELECT * FROM budget_items WHERE budget_id = ?");
$items->execute([$budget_id]);
$r = 2;
while ($it = $items->fetch()) {
    $sheet2->fromArray([$it['item_id'],$it['category'],$it['description'],$it['quantity'],$it['unit_cost'],$it['total_cost'],$it['justification'],$it['notes']], NULL, 'A'.$r);
    $r++;
}

$sheet3 = $spreadsheet->createSheet();
$sheet3->setTitle('Students');
$sheet3->fromArray(['Support ID','Student ID','Semester','Tuition Type','Tuition Amount','Stipend','FTE%'], NULL, 'A1');
$ss = $pdo->prepare("SELECT * FROM student_support WHERE budget_id = ?");
$ss->execute([$budget_id]);
$r=2;
while ($s = $ss->fetch()) {
    $sheet3->fromArray([$s['support_id'],$s['student_id'],$s['semester'],$s['tuition_type'],$s['tuition_amount'],$s['stipend_amount'],$s['fte_percent']], NULL, 'A'.$r);
    $r++;
}

$sheet4 = $spreadsheet->createSheet();
$sheet4->setTitle('Travel');
$sheet4->fromArray(['Request ID','Travel Profile','Duration Days','Travelers','Total Cost'], NULL, 'A1');
$tr = $pdo->prepare("SELECT tr.*, tp.profile_name FROM travel_requests tr LEFT JOIN travel_profiles tp ON tr.travel_id = tp.travel_id WHERE tr.budget_id = ?");
$tr->execute([$budget_id]);
$r=2;
while ($t = $tr->fetch()) {
    $sheet4->fromArray([$t['request_id'],$t['profile_name'],$t['duration_days'],$t['travelers'],$t['total_cost']], NULL, 'A'.$r);
    $r++;
}

$sheet5 = $spreadsheet->createSheet();
$sheet5->setTitle('Subawards');
$sheet5->fromArray(['Subaward ID','Institution','Total','F&A Rate','Notes'], NULL, 'A1');
$sa = $pdo->prepare("SELECT * FROM subawards WHERE budget_id = ?");
$sa->execute([$budget_id]);
$r=2;
while ($s = $sa->fetch()) {
    $sheet5->fromArray([$s['subaward_id'],$s['institution_name'],$s['subaward_total'],$s['subaward_f_and_a_rate'],$s['notes']], NULL, 'A'.$r);
    $r++;
}

$writer = new Xlsx($spreadsheet);
$fname = 'budget_' . $budget_id . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'. $fname .'"');
$writer->save('php://output');
exit;
?>
