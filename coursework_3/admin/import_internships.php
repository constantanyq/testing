<?php
session_start();
require_once "../connection.php";
$pageTitle = "Import Internships";
include "header.php";

$msg = ''; $preview = []; $importDone = false;

// ---- CANCEL ----
if (isset($_GET['clear'])) {
    unset($_SESSION['int_preview']);
    header("Location: import_internships.php");
    exit();
}

// ---- PREVIEW ----
if (isset($_POST['action']) && $_POST['action'] === 'preview' && isset($_FILES['csvfile'])) {
    $tmp = $_FILES['csvfile']['tmp_name'];
    if ($tmp) {
        $handle = fopen($tmp, 'r');
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            if (strtolower(trim($data[0])) === 'student_id') continue;
            $preview[] = [
                'sid' => trim($data[0]),
                'cid' => trim($data[1]),
                'lid' => trim($data[2]),
                'svid' => trim($data[3]),
                'duration' => trim($data[4])
            ];
        }
        fclose($handle);
        $_SESSION['int_preview'] = $preview;
    }
}

if (isset($_POST['action']) && $_POST['action'] === 'confirm') {
    $preview = $_SESSION['int_preview'] ?? [];
    $success = 0; $errors = [];
    foreach ($preview as $row) {
        $sid = mysqli_real_escape_string($conn, $row['sid']);
        $input_company = trim($row['cid']); // This might be a Name or an ID
        $lid = mysqli_real_escape_string($conn, $row['lid']);
        $svid = mysqli_real_escape_string($conn, $row['svid']);
        $duration = (int)$row['duration'];

        $cid_val = "NULL";

        if ($input_company !== '') {
            // Smart Lookup: Try to find the ID by matching the name OR the ID
            $safe_comp = mysqli_real_escape_string($conn, $input_company);
            $lookRes = mysqli_query($conn, "SELECT company_id FROM company 
                                            WHERE company_name = '$safe_comp' 
                                            OR company_id = '$safe_comp' LIMIT 1");
            
            if ($cRow = mysqli_fetch_assoc($lookRes)) {
                $cid_val = "'" . $cRow['company_id'] . "'";
            } else {
                $errors[] = "Warning: Company '$input_company' not found for Student $sid. Linked as NULL.";
            }
        }

        $sql = "INSERT INTO internship (student_id, company_id, lecturer_id, supervisor_id, duration) 
                VALUES ('$sid', $cid_val, '$lid', '$svid', $duration)";
        if (mysqli_query($conn, $sql)) $success++;
        else $errors[] = "Error for $sid: " . mysqli_error($conn);
    }
    unset($_SESSION['int_preview']);
    $importDone = true;
    $msg = "✅ Successfully assigned $success students.";
}

$previewStaged = !$importDone && !empty($_SESSION['int_preview']);
?>

<div class="topbar">
    <div class="breadcrumb">Admin &rsaquo; Import Internships</div>
    <a href="import_hub.php" class="btn btn-outline">← Back to Import</a>
</div>

<div class="page-body">
    <?php if ($msg): ?>
    <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
    <?= implode('<br>', $errors) ?></div><?php endif; ?>

    <!-- ── Upload form ── -->
    <?php if (!$previewStaged): ?>
    <div class="card card-form">
        <div class="card-title">📂 Upload Internships CSV</div>
            <div class="card-body">
                <p style="font-size:13px;color:var(--text-soft);margin-bottom:18px;line-height:1.7">
                Upload a <strong>.csv</strong> file to bulk-add students. <br>You can use either the <strong>Company Name</strong> (e.g., Maybank IT Division) 
                or the <strong>Company ID</strong> (e.g., C001) in the second column.
            <br><br>Each row should follow this format:<br>
                <code style="background:#f1f5f9;padding:4px 10px;border-radius:5px;font-size:12px;display:inline-block;margin-top:6px">
                    student_id, company_id, lecturer_id, supervisor_id, duration
                </code><br><br>
            </p>

            <!-- Download sample -->
            <div style="margin-bottom:20px;">
                <a href="data:text/csv;charset=utf-8,student_id%2Ccompany_id%2Clecturer_id%2Csupervisor_id%2Cduration%0ASTU010%2CPetronas%20Digital%20Sdn%20Bhd%2CDr.%20Ahmad%20Farid%2CMr.%20Rajan%20Kumar%2C12%0ASTU011%2CMaybank%20IT%20Division%2CDr.%20Nurul%20Huda%2CMs.%20Lim%20Bee%20Ling%2C16"download="internship_template.csv" class="btn btn-outline btn-sm">⬇️ Download CSV Template</a>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="preview">
                <input type="file" name="csvfile" required><br><br>
                <button type="submit" class="btn btn-primary">📋 Preview Assignments</button>
            </form>

        </div>
    </div>
    <?php else: ?>
    <div class="card card-table">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Student</th><th>Company</th><th>Lecturer</th><th>Supervisor</th><th>Duration</th></tr></thead>
                <tbody>
                    <?php foreach ($_SESSION['int_preview'] as $r): ?>
                    <tr><td><?= $r['sid'] ?></td><td><?= $r['cid'] ?></td><td><?= $r['lid'] ?></td><td><?= $r['svid'] ?></td><td><?= $r['duration'] ?> wks</td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div style="display:flex;gap:12px;margin-top:-10px;">
        <form method="POST" action="">
            <input type="hidden" name="action" value="confirm">
            <button type="submit" class="btn btn-success">✅ Confirm &amp; Import <?= count($preview) ?> Internships</button>
        </form>
        <a href="import_internships.php?clear=1" class="btn btn-outline">✕ Cancel</a>
    </div>
    <?php endif; ?>
</div>
<?php include "footer.php"; ?>