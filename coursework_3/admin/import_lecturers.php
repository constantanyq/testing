<?php
session_start();
require_once "../connection.php";
$pageTitle = "Import Lecturers";
include "header.php";

$msg = ''; $msgType = 'success'; $preview = []; $importDone = false;

// ---- CANCEL ----
if (isset($_GET['clear'])) {
    unset($_SESSION['lec_preview']);
    header("Location: import_lecturers.php");
    exit();
}

// ---- PREVIEW ----
if (isset($_POST['action']) && $_POST['action'] === 'preview' && isset($_FILES['csvfile'])) {
    $tmp = $_FILES['csvfile']['tmp_name'];
    if ($tmp) {
        $handle = fopen($tmp, 'r');
        $line = 0;
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $line++;
            if ($line === 1 && strtolower(trim($data[0])) === 'id') continue; 
            if (count($data) < 2) continue;
            $preview[] = [
                'id'       => trim($data[0]),
                'name'     => trim($data[1]),
                'email'    => isset($data[2]) ? trim($data[2]) : '',
                'password' => isset($data[3]) ? trim($data[3]) : 'lec123',
            ];
        }
        fclose($handle);
        $_SESSION['lec_preview'] = $preview;
    }
}

// ---- CONFIRM ----
if (isset($_POST['action']) && $_POST['action'] === 'confirm') {
    $preview = $_SESSION['lec_preview'] ?? [];
    $success = 0; $skip = 0;
    foreach ($preview as $row) {
        $id = mysqli_real_escape_string($conn, $row['id']);
        $name = mysqli_real_escape_string($conn, $row['name']);
        $email = mysqli_real_escape_string($conn, $row['email']);
        $pass = mysqli_real_escape_string($conn, $row['password']);
        
        $chk = mysqli_query($conn, "SELECT lecturer_id FROM lecturer WHERE lecturer_id='$id'");
        if (mysqli_num_rows($chk) > 0) { $skip++; continue; }
        
        $sql = "INSERT INTO lecturer (lecturer_id, lecturer_name, lecturer_email, lecturer_password) 
                VALUES ('$id','$name','$email','$pass')";
        if (mysqli_query($conn, $sql)) $success++;
    }
    unset($_SESSION['lec_preview']);
    $importDone = true;
    $msg = "✅ Imported $success lecturers." . ($skip > 0 ? " Skipped $skip duplicates." : "");
}

$previewStaged = !$importDone && !empty($_SESSION['lec_preview']);
if ($previewStaged) $preview = $_SESSION['lec_preview'];
?>

<div class="topbar">
    <div class="breadcrumb">Admin &rsaquo; Import Lecturers</div>
    <a href="import_hub.php" class="btn btn-outline">← Back to Hub</a>
</div>

<div class="page-body">
    <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

    <?php if (!$previewStaged): ?>
    <div class="card card-form">
        <div class="card-title">📂 Upload Lecturer CSV</div>
        <div class="card-body">
            <p style="font-size:13px;color:var(--text-soft);margin-bottom:18px;line-height:1.7">
            Upload a <strong>.csv</strong> file to bulk-add lecturers. <br><br>Each row should follow this format:<br>
            <code style="background:#f1f5f9;padding:4px 10px;border-radius:5px;font-size:12px;display:inline-block;margin-top:6px">
                lecturer_id, lecturer_name, lecturer_email, lecturer_password
            </code><br><br>
            </p>

            <!-- Download sample -->
            <div style="margin-bottom:20px;">
                <a href="data:text/csv;charset=utf-8,lecturer_id%2Clecturer_name%2Clecturer_email%2Clecturer_password%0ALEC001%2CDr.%20John%20Doe%2Cjohn.doe%40example.com%2Clec123%0ALEC002%2CProf.%20Jane%20Smith%2Cjane.smith%40example.com%2Clec123"
                download="lecturers_template.csv"
                class="btn btn-outline btn-sm">⬇️ Download CSV Template</a>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="preview">
                <div class="form-group"><input type="file" name="csvfile" accept=".csv" required></div>
                <button type="submit" class="btn btn-primary">📋 Preview</button>
            </form>
        </div>
    </div>
    <?php else: ?>
    <div class="card card-table">
        <div class="card-title">📋 Preview - Ready to Import</div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>ID</th><th>Name</th><th>Email</th></tr></thead>
                <tbody>
                    <?php foreach ($preview as $r): ?>
                    <tr><td><?= htmlspecialchars($r['id']) ?></td><td><?= htmlspecialchars($r['name']) ?></td><td><?= htmlspecialchars($r['email']) ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div style="display:flex;gap:12px;margin-top:-10px;">
        <form method="POST" action="">
            <input type="hidden" name="action" value="confirm">
            <button type="submit" class="btn btn-success">✅ Confirm &amp; Import <?= count($preview) ?> Lecturers</button>
        </form>
        <a href="import_lecturers.php?clear=1" class="btn btn-outline">✕ Cancel</a>
    </div>
    <?php endif; ?>
</div>
<?php include "footer.php"; ?>