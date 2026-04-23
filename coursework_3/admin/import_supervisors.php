<?php
session_start();
require_once "../connection.php";
$pageTitle = "Import Supervisors";
include "header.php";

$msg = ''; $preview = []; $importDone = false;

// ---- CANCEL ----
if (isset($_GET['clear'])) {
    unset($_SESSION['sup_preview']);
    header("Location: import_supervisors.php");
    exit();
}

if (isset($_POST['action']) && $_POST['action'] === 'preview' && isset($_FILES['csvfile'])) {
    $tmp = $_FILES['csvfile']['tmp_name'];
    if ($tmp) {
        $handle = fopen($tmp, 'r');
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            if (strtolower(trim($data[0])) === 'id') continue;
            $preview[] = [
                'id' => trim($data[0]),
                'name' => trim($data[1]),
                'email' => trim($data[2]),
                'password' => trim($data[3]),
                'company_id' => trim($data[4] ?? '')
            ];
        }
        fclose($handle);
        $_SESSION['sup_preview'] = $preview;
    }
}

if (isset($_POST['action']) && $_POST['action'] === 'confirm') {
    $preview = $_SESSION['sup_preview'] ?? [];
    $success = 0; $errors = [];

    foreach ($preview as $row) {
        $id = mysqli_real_escape_string($conn, $row['id']);
        $name = mysqli_real_escape_string($conn, $row['name']);
        $email = mysqli_real_escape_string($conn, $row['email']);
        $pass = mysqli_real_escape_string($conn, $row['password']);
        $input_company = trim($row['company_id']); // This might be a Name or an ID

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
                $errors[] = "Warning: Company '$input_company' not found for Supervisor $id. Linked as NULL.";
            }
        }

        $sql = "INSERT INTO supervisor (supervisor_id, supervisor_name, supervisor_email, supervisor_password, company_id) 
                VALUES ('$id', '$name', '$email', '$pass', $cid_val) 
                ON DUPLICATE KEY UPDATE company_id = $cid_val";

        if (mysqli_query($conn, $sql)) $success++;
    }
    unset($_SESSION['sup_preview']);
    $importDone = true;
    $msg = "✅ Imported $success supervisors.";
}

$previewStaged = !$importDone && !empty($_SESSION['sup_preview']);
?>

<div class="topbar">
    <div class="breadcrumb">Admin &rsaquo; Import Supervisors</div>
    <a href="import_hub.php" class="btn btn-outline">← Back</a>
</div>

<div class="page-body">
    <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

    <?php if (!$previewStaged): ?>
    <div class="card card-form">
        <div class="card-title">📂 Upload Supervisors CSV</div>
        <div class="card-body">
            <p style="font-size:13px;color:var(--text-soft);margin-bottom:18px;line-height:1.7">
            Upload a <strong>.csv</strong> file to bulk-add supervisors. <br>You can use either the <strong>Company Name</strong> (e.g., Maybank IT Division) 
                or the <strong>Company ID</strong> (e.g., C001) in the last column.
            <br><br>Each row should follow this format:<br>
            <code style="background:#f1f5f9;padding:4px 10px;border-radius:5px;font-size:12px;display:inline-block;margin-top:6px">
                supervisor_id, supervisor_name, supervisor_email, supervisor_password, company_id
            </code><br><br>
            </p>

            <!-- Download sample -->
            <div style="margin-bottom:20px;">
                <a href="data:text/csv;charset=utf-8,supervisor_id%2Csupervisor_name%2Csupervisor_email%2Csupervisor_password%2Ccompany_id%0ASUP001%2CDr.%20John%20Doe%2Cjohn.doe%40example.com%2Csup123%0ASUP002%2CProf.%20Jane%20Smith%2Cjane.smith%40example.com%2Csup123"
                download="supervisors_template.csv"
                class="btn btn-outline btn-sm">⬇️ Download CSV Template</a>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="preview">
                <input type="file" name="csvfile" required><br><br>
                <button type="submit" class="btn btn-primary">📋 Preview</button>
            </form>
        </div>
    </div>
    <?php else: ?>
    <div class="card card-table">
        <div class="table-wrap">
            <table>
                <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Comp ID</th></tr></thead>
                <tbody>
                    <?php foreach ($_SESSION['sup_preview'] as $r): ?>
                    <tr><td><?= $r['id'] ?></td><td><?= $r['name'] ?></td><td><?= $r['email'] ?></td><td><?= $r['company_id'] ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div style="display:flex;gap:12px;margin-top:-10px;">
        <form method="POST" action="">
            <input type="hidden" name="action" value="confirm">
            <button type="submit" class="btn btn-success">✅ Confirm &amp; Import <?= count($preview) ?> Supervisors</button>
        </form>
        <a href="import_supervisors.php?clear=1" class="btn btn-outline">✕ Cancel</a>
    </div>
    <?php endif; ?>
</div>
<?php include "footer.php"; ?>