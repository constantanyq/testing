<?php
session_start();
require_once "../connection.php";
$pageTitle = "Import Students";
include "header.php";

$msg = '';
$msgType = 'success';
$preview = [];
$importDone = false;

// ---- PREVIEW ----
if (isset($_POST['action']) && $_POST['action'] === 'preview' && isset($_FILES['csvfile'])) {
    $tmp = $_FILES['csvfile']['tmp_name'];
    if ($tmp) {
        $handle = fopen($tmp, 'r');
        $line = 0;
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $line++;
            if ($line === 1 && strtolower(trim($data[0])) === 'student_id') continue; // skip header
            if (count($data) < 3) continue;
            $preview[] = [
                'student_id'       => trim($data[0]),
                'student_name'     => trim($data[1]),
                'student_email'    => isset($data[2]) ? trim($data[2]) : '',
                'programme'        => isset($data[3]) ? trim($data[3]) : '',
                'student_password' => isset($data[4]) ? trim($data[4]) : 'pass123',
            ];
        }
        fclose($handle);
        // Store in session for confirm step
        $_SESSION['import_preview'] = $preview;
    }
}

// ---- CONFIRM INSERT ----
if (isset($_POST['action']) && $_POST['action'] === 'confirm') {
    $preview = $_SESSION['import_preview'] ?? [];
    $success = 0; $skip = 0; $errors = [];
    foreach ($preview as $row) {
        $sid   = mysqli_real_escape_string($conn, $row['student_id']);
        $sname = mysqli_real_escape_string($conn, $row['student_name']);
        $semail= mysqli_real_escape_string($conn, $row['student_email']);
        $sprog = mysqli_real_escape_string($conn, $row['programme']);
        $spass = mysqli_real_escape_string($conn, $row['student_password']);
        // Check duplicate
        $chk = mysqli_query($conn, "SELECT student_id FROM student WHERE student_id='$sid'");
        if (mysqli_num_rows($chk) > 0) { $skip++; continue; }
        $sql = "INSERT INTO student (student_id, student_name, student_email, programme, student_password)
                VALUES ('$sid','$sname','$semail','$sprog','$spass')";
        if (mysqli_query($conn, $sql)) $success++;
        else $errors[] = $sid . ': ' . mysqli_error($conn);
    }
    unset($_SESSION['import_preview']);
    $importDone = true;
    if ($success > 0) {
        $msg = "✅ Import complete: <strong>$success</strong> student(s) added" .
               ($skip > 0 ? ", <strong>$skip</strong> skipped (duplicate ID)" : '') . '.';
    } else {
        $msg = "⚠️ No new records inserted." . ($skip > 0 ? " $skip duplicate(s) skipped." : '');
        $msgType = 'info';
    }
    if ($errors) $msg .= '<br><small style="color:var(--danger)">Errors: ' . implode('; ', $errors) . '</small>';
    $preview = [];
}

$previewStaged = !$importDone && !empty($_SESSION['import_preview'] ?? []);
if ($previewStaged) $preview = $_SESSION['import_preview'];
?>

<div class="topbar">
    <div>
        <div class="topbar-greeting">Bulk Import Students</div>
        <div class="breadcrumb">Admin &rsaquo; Import Students</div>
    </div>
    <a href="students.php" class="btn btn-outline">← Back to Students</a>
</div>

<div class="page-body">

<?php if ($msg): ?>
<div class="alert alert-<?= $msgType ?>"><?= $msg ?></div>
<?php endif; ?>

<!-- ── Upload form ── -->
<?php if (!$previewStaged): ?>
<div class="card card-form">
    <div class="card-title">📂 Upload CSV File</div>
    <div class="card-body">
        <p style="font-size:13px;color:var(--text-soft);margin-bottom:18px;line-height:1.7">
            Upload a <strong>.csv</strong> file to bulk-add students. Each row should follow this format:<br>
            <code style="background:#f1f5f9;padding:4px 10px;border-radius:5px;font-size:12px;display:inline-block;margin-top:6px">
                student_id, student_name, student_email, programme, password
            </code><br><br>
            The first row may optionally be a header row (starting with <code>student_id</code>) — it will be skipped automatically.
            Students with duplicate IDs will be skipped without error.
        </p>

        <!-- Download sample -->
        <div style="margin-bottom:20px;">
            <a href="data:text/csv;charset=utf-8,student_id%2Cstudent_name%2Cstudent_email%2Cprogramme%2Cpassword%0ASTU010%2CJohn%20Doe%2Cjohn%40example.com%2CComputer%20Science%2Cpass123%0ASTU011%2CJane%20Smith%2Cjane%40example.com%2CBusiness%2Cpass123"
               download="students_template.csv"
               class="btn btn-outline btn-sm">⬇️ Download CSV Template</a>
        </div>

        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" value="preview">
            <div class="form-group" style="max-width:400px;">
                <label>CSV File</label>
                <input type="file" name="csvfile" accept=".csv,.txt" required>
            </div>
            <button type="submit" class="btn btn-primary">📋 Preview Import</button>
        </form>
    </div>
</div>

<?php else: ?>

<!-- ── Preview table ── -->
<div class="card card-table">
    <div class="card-title flex-between">
        <span>📋 Preview — <?= count($preview) ?> record(s) ready to import</span>
    </div>
    <div class="table-wrap">
    <table>
        <thead>
            <tr><th>#</th><th>Student ID</th><th>Name</th><th>Email</th><th>Programme</th><th>Password</th></tr>
        </thead>
        <tbody>
        <?php foreach ($preview as $i => $row): ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td><strong><?= htmlspecialchars($row['student_id']) ?></strong></td>
            <td><?= htmlspecialchars($row['student_name']) ?></td>
            <td><?= htmlspecialchars($row['student_email']) ?></td>
            <td><?= htmlspecialchars($row['programme']) ?></td>
            <td><code style="font-size:11px;background:#f1f5f9;padding:2px 6px;border-radius:3px"><?= htmlspecialchars($row['student_password']) ?></code></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<div style="display:flex;gap:12px;margin-top:-10px;">
    <form method="POST" action="">
        <input type="hidden" name="action" value="confirm">
        <button type="submit" class="btn btn-success">✅ Confirm &amp; Import <?= count($preview) ?> Students</button>
    </form>
    <a href="import_students.php" class="btn btn-outline">✕ Cancel</a>
</div>

<?php endif; ?>

</div><!-- page-body -->
<?php include "footer.php"; ?>
