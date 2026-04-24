<?php
session_start();
require_once "../connection.php";
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}

$type = $_GET['type'] ?? '';

$templates = [
    'students' => [
        'filename' => 'students_template.csv',
        'header'   => ['student_id','student_name','student_email','programme','student_password'],
        'sample'   => [
            ['STU001','Ahmad Hafizi','ahmad@example.com','Diploma in IT','pass123'],
            ['STU002','Nurul Ain','nurul@example.com','Diploma in Business','pass123'],
            ['STU003','Lee Wei Liang','lee@example.com','Diploma in Engineering','pass123'],
        ]
    ],
    'lecturers' => [
        'filename' => 'lecturers_template.csv',
        'header'   => ['lecturer_id','lecturer_name','lecturer_email','lecturer_password'],
        'sample'   => [
            ['L001','Dr. Siti Hawa','siti@example.com','pass123'],
            ['L002','Mr. Raj Kumar','raj@example.com','pass123'],
        ]
    ],
    'supervisors' => [
        'filename' => 'supervisors_template.csv',
        'header'   => ['supervisor_id','supervisor_name','supervisor_email','supervisor_password','company_name'],
        'sample'   => [
            ['S001','Tan Ah Kow','tan@petronas.com','pass123','Petronas Berhad'],
            ['S002','Wong Li Hua','wong@maybank.com','pass123','Maybank Berhad'],
        ],
        'note' => 'company_name must exactly match an existing company. Leave blank if not linked.'
    ],
    'companies' => [
        'filename' => 'companies_template.csv',
        'header'   => ['company_name'],
        'sample'   => [
            ['Petronas Berhad'],
            ['Maybank Berhad'],
            ['Telekom Malaysia'],
            ['Axiata Group'],
        ]
    ],
];

if (!array_key_exists($type, $templates)) {
    die("Invalid template type.");
}

$tpl = $templates[$type];

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $tpl['filename'] . '"');
header('Cache-Control: no-cache, no-store, must-revalidate');

$out = fopen('php://output', 'w');

// BOM for Excel UTF-8 compatibility
fputs($out, "\xEF\xBB\xBF");

// Column header row
fputcsv($out, $tpl['header']);

// Sample data rows
foreach ($tpl['sample'] as $row) {
    fputcsv($out, $row);
}

fclose($out);
exit();
