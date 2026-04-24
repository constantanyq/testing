<?php
// TEMPLATE DOWNLOAD
if (isset($_GET['template']) && $_GET['template'] === 'student') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="student_template.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['student_id', 'student_name', 'student_password', 'student_email', 'programme']);
    fputcsv($out, ['STU006', 'Ahmad Bin Ali', 'password123', 'ahmad@student.edu', 'BSc Computer Science']);
    fputcsv($out, ['STU007', 'Siti Binti Hassan', 'password123', 'siti@student.edu', 'BSc Information Technology']);
    fclose($out);
    exit();
}

session_start();
require_once "../connection.php";
$pageTitle = "Import Data";
include "header.php";

$msg = "";
$csvLog = [];

// CSV UPLOAD
if (isset($_POST['action']) && $_POST['action'] === 'csv_upload') {
    if (!empty($_FILES['csv_file']['tmp_name'])) {
        $handle = fopen($_FILES['csv_file']['tmp_name'], 'r');
        $header = fgetcsv($handle);
        $header = array_map(fn($h) => strtolower(trim($h)), $header);
        $required = ['student_id', 'student_name', 'student_password', 'student_email', 'programme'];
        $missing = array_diff($required, $header);

        if ($missing) {
            $msg = "ERROR: CSV is missing required columns: " . implode(', ', $missing) . ". Please use the provided template.";
        } else {
            $added = $skipped = $errors = 0;
            $rowNum = 1;

            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;
                if (count($row) < count($header))
                    continue;

                $data = array_combine($header, $row);
                $sid = mysqli_real_escape_string($conn, trim($data['student_id'] ?? ''));
                $sname = mysqli_real_escape_string($conn, trim($data['student_name'] ?? ''));
                $spass = mysqli_real_escape_string($conn, trim($data['student_password'] ?? ''));
                $semail = mysqli_real_escape_string($conn, trim($data['student_email'] ?? ''));
                $sprog = mysqli_real_escape_string($conn, trim($data['programme'] ?? ''));

                if (!$sid || !$sname || !$spass || !$semail || !$sprog) {
                    $csvLog[] = ['row' => $rowNum, 'id' => $sid ?: '(empty)', 'status' => 'error', 'msg' => 'Missing required field'];
                    $errors++;
                    continue;
                }

                $chk = mysqli_query($conn, "SELECT student_id FROM student WHERE student_id='$sid'");
                if (mysqli_num_rows($chk) > 0) {
                    $csvLog[] = ['row' => $rowNum, 'id' => $sid, 'status' => 'skipped', 'msg' => 'ID already exists'];
                    $skipped++;
                    continue;
                }

                $sql = "INSERT INTO student (student_id,student_name,student_password,student_email,programme)
                        VALUES ('$sid','$sname','$spass','$semail','$sprog')";
                if (mysqli_query($conn, $sql)) {
                    $csvLog[] = ['row' => $rowNum, 'id' => $sid, 'status' => 'ok', 'msg' => $sname];
                    $added++;
                } else {
                    $csvLog[] = ['row' => $rowNum, 'id' => $sid, 'status' => 'error', 'msg' => mysqli_error($conn)];
                    $errors++;
                }
            }
            fclose($handle);
            $msg = "CSV Import: {$added} added, {$skipped} skipped (duplicate), {$errors} error(s).";
        }
    } else {
        $msg = "ERROR: No file uploaded or file is empty.";
    }
}
?>

<div class="topbar">
    <h1>Import Data</h1>
    <span class="breadcrumb">Admin &rsaquo; Import Data</span>
</div>

<div class="page-body">

    <?php if ($msg): ?>
        <div class="alert <?= strpos($msg, 'ERROR') === 0 ? 'alert-danger' : 'alert-success' ?>">
            <?= strpos($msg, 'ERROR') === 0 ? '⚠️' : '✅' ?>     <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <?php if ($csvLog): ?>
        <div class="card">
            <div class="card-title flex-between">
                <span>📋 CSV Import Report</span>
                <span class="text-muted text-sm"><?= count($csvLog) ?> row(s) processed</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Row</th>
                            <th>ID</th>
                            <th>Name / Note</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($csvLog as $log): ?>
                            <tr>
                                <td class="text-muted"><?= $log['row'] ?></td>
                                <td><strong><?= htmlspecialchars($log['id']) ?></strong></td>
                                <td><?= htmlspecialchars($log['msg']) ?></td>
                                <td>
                                    <?php if ($log['status'] === 'ok'): ?>
                                        <span class="badge badge-pass">✅ Added</span>
                                    <?php elseif ($log['status'] === 'skipped'): ?>
                                        <span class="badge badge-pending">⏭ Skipped</span>
                                    <?php else: ?>
                                        <span class="badge badge-fail">❌ Error</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-title flex-between">
            <span>📤 Bulk Upload Students via CSV</span>
            <a href="import_data.php?template=student" class="btn btn-outline btn-sm">
                ⬇️ Download Template
            </a>
        </div>

        <div class="csv-info-box">
            <div class="csv-info-icon">💡</div>
            <div>
                <strong>How it works:</strong> Download the template, fill in student data (one row per student), then
                upload the file. Duplicate IDs are automatically skipped.
                <div class="csv-columns">
                    Required columns:
                    <span class="csv-col">student_id</span>
                    <span class="csv-col">student_name</span>
                    <span class="csv-col">student_password</span>
                    <span class="csv-col">student_email</span>
                    <span class="csv-col">programme</span>
                </div>
            </div>
        </div>

        <form method="POST" enctype="multipart/form-data" style="margin-top:16px;">
            <input type="hidden" name="action" value="csv_upload">
            <div class="csv-upload-area" id="csvDropZone">
                <input type="file" name="csv_file" id="csvFile" accept=".csv" required style="display:none;">
                <div class="csv-upload-icon">📁</div>
                <p class="csv-upload-label">Drag &amp; drop your CSV file here, or <span class="csv-browse-link"
                        onclick="document.getElementById('csvFile').click()">browse</span></p>
                <p class="csv-upload-hint" id="csvFileName">Accepted format: .csv only</p>
            </div>
            <div style="margin-top:12px;">
                <button type="submit" class="btn btn-success">📤 Upload &amp; Import</button>
            </div>
        </form>
    </div>

</div><!-- page-body -->

<?php include "footer.php"; ?>