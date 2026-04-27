<?php
// Template downloads
if (isset($_GET['template'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    if ($_GET['template'] === 'lecturer') {
        header('Content-Disposition: attachment; filename="lecturer_template.csv"');
        $out = fopen('php://output', 'w');
        fputs($out, "\xEF\xBB\xBF");
        fputcsv($out, ['lecturer_id','lecturer_name','lecturer_email','lecturer_password']);
        fputcsv($out, ['L001','Dr. Ahmad Razali','ahmad.razali@uni.edu.my','pass123']);
        fputcsv($out, ['L002','Prof. Siti Rohani','siti.rohani@uni.edu.my','pass123']);
        fclose($out); exit();
    }
    if ($_GET['template'] === 'supervisor') {
        header('Content-Disposition: attachment; filename="supervisor_template.csv"');
        $out = fopen('php://output', 'w');
        fputs($out, "\xEF\xBB\xBF");
        fputcsv($out, ['supervisor_id','supervisor_name','supervisor_email','supervisor_password','company_name']);
        fputcsv($out, ['S001','Encik Hafiz Zainudin','hafiz@petronas.com','pass123','Petronas Berhad']);
        fputcsv($out, ['S002','Puan Noraini Khalid','noraini@maybank.com','pass123','Maybank Berhad']);
        fclose($out); exit();
    }
}
 
session_start();
require_once "../connection.php";
$pageTitle = "Manage Users";
include "header.php";
 
// State variables
$msg      = "";
$csvLog   = [];
$csvTab   = "";   // 'lec' or 'sup'
 
// DELETE (GET → redirect to clear URL, prevent stale messages)
if (isset($_GET['del_lec'])) {
    $id = mysqli_real_escape_string($conn, $_GET['del_lec']);
    mysqli_query($conn, "DELETE FROM lecturer WHERE lecturer_id='$id'");
    header("Location: users.php?flash=lec_deleted");
    exit();
}
if (isset($_GET['del_sup'])) {
    $id = mysqli_real_escape_string($conn, $_GET['del_sup']);
    mysqli_query($conn, "DELETE FROM supervisor WHERE supervisor_id='$id'");
    header("Location: users.php?flash=sup_deleted&tab=sup");
    exit();
}
 
// ADD / EDIT lecturer (POST -> redirect)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
 
    if ($action === 'add_lec') {
        $lid    = mysqli_real_escape_string($conn, trim($_POST['lecturer_id']));
        $lname  = mysqli_real_escape_string($conn, trim($_POST['lecturer_name']));
        $lemail = mysqli_real_escape_string($conn, trim($_POST['lecturer_email']));
        $lpass  = mysqli_real_escape_string($conn, trim($_POST['lecturer_password']));
        $chk    = mysqli_query($conn, "SELECT lecturer_id FROM lecturer WHERE lecturer_id='$lid'");
        if (mysqli_num_rows($chk) > 0) {
            $msg = "ERROR: Lecturer ID already exists.";
        } else {
            mysqli_query($conn, "INSERT INTO lecturer (lecturer_id,lecturer_name,lecturer_email,lecturer_password)
                                  VALUES ('$lid','$lname','$lemail','$lpass')");
            header("Location: users.php?flash=lec_added");
            exit();
        }
    }
 
    elseif ($action === 'edit_lec') {
        $lid    = mysqli_real_escape_string($conn, trim($_POST['lecturer_id']));
        $lname  = mysqli_real_escape_string($conn, trim($_POST['lecturer_name']));
        $lemail = mysqli_real_escape_string($conn, trim($_POST['lecturer_email']));
        $lpass  = mysqli_real_escape_string($conn, trim($_POST['lecturer_password']));
        mysqli_query($conn, "UPDATE lecturer SET lecturer_name='$lname',lecturer_email='$lemail',
                              lecturer_password='$lpass' WHERE lecturer_id='$lid'");
        header("Location: users.php?flash=lec_updated");
        exit();
    }
 
    elseif ($action === 'add_sup') {
        $sid    = mysqli_real_escape_string($conn, trim($_POST['supervisor_id']));
        $sname  = mysqli_real_escape_string($conn, trim($_POST['supervisor_name']));
        $semail = mysqli_real_escape_string($conn, trim($_POST['supervisor_email']));
        $spass  = mysqli_real_escape_string($conn, trim($_POST['supervisor_password']));
        $chk    = mysqli_query($conn, "SELECT supervisor_id FROM supervisor WHERE supervisor_id='$sid'");
        if (mysqli_num_rows($chk) > 0) {
            $msg    = "ERROR: Supervisor ID already exists.";
            // fall through to render page on supervisor tab
            $_GET['tab'] = 'sup';
        } else {
            $scid     = mysqli_real_escape_string($conn, trim($_POST['supervisor_company_id'] ?? ''));
            $scid_val = $scid ? "'$scid'" : 'NULL';
            mysqli_query($conn, "INSERT INTO supervisor (supervisor_id,supervisor_name,supervisor_email,supervisor_password,company_id)
                                  VALUES ('$sid','$sname','$semail','$spass',$scid_val)");
            header("Location: users.php?flash=sup_added&tab=sup");
            exit();
        }
    }
 
    elseif ($action === 'edit_sup') {
        $sid       = mysqli_real_escape_string($conn, trim($_POST['supervisor_id']));
        $sname     = mysqli_real_escape_string($conn, trim($_POST['supervisor_name']));
        $semail    = mysqli_real_escape_string($conn, trim($_POST['supervisor_email']));
        $spass     = mysqli_real_escape_string($conn, trim($_POST['supervisor_password']));
        $scid2     = mysqli_real_escape_string($conn, trim($_POST['supervisor_company_id'] ?? ''));
        $scid2_val = $scid2 ? "'$scid2'" : 'NULL';
        mysqli_query($conn, "UPDATE supervisor SET supervisor_name='$sname',supervisor_email='$semail',
                              supervisor_password='$spass',company_id=$scid2_val WHERE supervisor_id='$sid'");
        header("Location: users.php?flash=sup_updated&tab=sup");
        exit();
    }
 
    // CSV: lecturers 
    elseif ($action === 'csv_lec') {
        $csvTab = 'lec';
        if (!empty($_FILES['csv_file']['tmp_name'])) {
            $handle      = fopen($_FILES['csv_file']['tmp_name'], 'r');
            $rawHdr      = fgetcsv($handle);
            $rawHdr[0]   = ltrim($rawHdr[0], "\xEF\xBB\xBF");
            $header      = array_map(fn($h) => strtolower(trim($h)), $rawHdr);
            $required    = ['lecturer_id','lecturer_name','lecturer_email','lecturer_password'];
            $missing     = array_diff($required, $header);
            if ($missing) {
                $msg = "ERROR: CSV missing columns: " . implode(', ', $missing);
            } else {
                $added = $updated = $errors = 0; $rowNum = 1;
                while (($row = fgetcsv($handle)) !== false) {
                    $rowNum++;
                    if (count($row) < count($header)) continue;
                    $data   = array_combine($header, $row);
                    $lid    = mysqli_real_escape_string($conn, trim($data['lecturer_id'] ?? ''));
                    $lname  = mysqli_real_escape_string($conn, trim($data['lecturer_name'] ?? ''));
                    $lemail = mysqli_real_escape_string($conn, trim($data['lecturer_email'] ?? ''));
                    $lpass  = mysqli_real_escape_string($conn, trim($data['lecturer_password'] ?? ''));
                    if (!$lid || !$lname || !$lpass || !$lemail) {
                        $csvLog[] = ['row'=>$rowNum,'id'=>$lid?:'(empty)','status'=>'error','msg'=>'Missing required field'];
                        $errors++; continue;
                    }
                    $chk    = mysqli_query($conn, "SELECT lecturer_id FROM lecturer WHERE lecturer_id='$lid'");
                    $exists = mysqli_num_rows($chk) > 0;
                    if (mysqli_query($conn, "INSERT INTO lecturer (lecturer_id,lecturer_name,lecturer_email,lecturer_password)
                                             VALUES ('$lid','$lname','$lemail','$lpass')
                                             ON DUPLICATE KEY UPDATE lecturer_name='$lname',lecturer_email='$lemail',lecturer_password='$lpass'")) {
                        if (!$exists) { $csvLog[] = ['row'=>$rowNum,'id'=>$lid,'status'=>'ok',     'msg'=>"Added: $lname"];   $added++; }
                        else          { $csvLog[] = ['row'=>$rowNum,'id'=>$lid,'status'=>'updated','msg'=>"Updated: $lname"]; $updated++; }
                    } else {
                        $csvLog[] = ['row'=>$rowNum,'id'=>$lid,'status'=>'error','msg'=>mysqli_error($conn)]; $errors++;
                    }
                }
                fclose($handle);
                $msg = "Lecturer CSV: {$added} added, {$updated} updated, {$errors} error(s).";
            }
        } else {
            $msg = "ERROR: No file uploaded.";
        }
    }
 
    // CSV: supervisors 
    elseif ($action === 'csv_sup') {
        $csvTab = 'sup';
        if (!empty($_FILES['csv_file']['tmp_name'])) {
            $handle    = fopen($_FILES['csv_file']['tmp_name'], 'r');
            $rawHdr    = fgetcsv($handle);
            $rawHdr[0] = ltrim($rawHdr[0], "\xEF\xBB\xBF");
            $header    = array_map(fn($h) => strtolower(trim($h)), $rawHdr);
            $required  = ['supervisor_id','supervisor_name','supervisor_password','supervisor_email','company_name'];
            $missing   = array_diff($required, $header);
            if ($missing) {
                $msg = "ERROR: CSV missing columns: " . implode(', ', $missing);
            } else {
                $added = $updated = $errors = 0; $rowNum = 1;
                while (($row = fgetcsv($handle)) !== false) {
                    $rowNum++;
                    if (count($row) < count($header)) continue;
                    $data   = array_combine($header, $row);
                    $sid    = mysqli_real_escape_string($conn, trim($data['supervisor_id'] ?? ''));
                    $sname  = mysqli_real_escape_string($conn, trim($data['supervisor_name'] ?? ''));
                    $semail = mysqli_real_escape_string($conn, trim($data['supervisor_email'] ?? ''));
                    $spass  = mysqli_real_escape_string($conn, trim($data['supervisor_password'] ?? ''));
                    $cname  = trim($data['company_name'] ?? '');
                    if (!$sid || !$sname || !$spass || !$semail || !$cname) {
                        $csvLog[] = ['row'=>$rowNum,'id'=>$sid?:'(empty)','status'=>'error','msg'=>'Missing required field'];
                        $errors++; continue;
                    }
                    $cn      = mysqli_real_escape_string($conn, $cname);
                    $cRes    = mysqli_query($conn, "SELECT company_id FROM company WHERE company_name='$cn'");
                    $cid_val = ($cRow = mysqli_fetch_assoc($cRes))
                               ? (int)$cRow['company_id']
                               : (mysqli_query($conn, "INSERT INTO company (company_name) VALUES ('$cn')") ? mysqli_insert_id($conn) : 'NULL');
                    $chk    = mysqli_query($conn, "SELECT supervisor_id FROM supervisor WHERE supervisor_id='$sid'");
                    $exists = mysqli_num_rows($chk) > 0;
                    if (mysqli_query($conn, "INSERT INTO supervisor (supervisor_id,supervisor_name,supervisor_email,supervisor_password,company_id)
                                             VALUES ('$sid','$sname','$semail','$spass',$cid_val)
                                             ON DUPLICATE KEY UPDATE supervisor_name='$sname',supervisor_email='$semail',
                                             supervisor_password='$spass',company_id=$cid_val")) {
                        $label = "$sname @ $cname";
                        if (!$exists) { $csvLog[] = ['row'=>$rowNum,'id'=>$sid,'status'=>'ok',     'msg'=>"Added: $label"];   $added++; }
                        else          { $csvLog[] = ['row'=>$rowNum,'id'=>$sid,'status'=>'updated','msg'=>"Updated: $label"]; $updated++; }
                    } else {
                        $csvLog[] = ['row'=>$rowNum,'id'=>$sid,'status'=>'error','msg'=>mysqli_error($conn)]; $errors++;
                    }
                }
                fclose($handle);
                $msg = "Supervisor CSV: {$added} added, {$updated} updated, {$errors} error(s).";
            }
        } else {
            $msg = "ERROR: No file uploaded.";
        }
    }
}
 
// Flash messages from redirects 
$flashMap = [
    'lec_deleted' => "Lecturer deleted.",
    'lec_added'   => "Lecturer added.",
    'lec_updated' => "Lecturer updated.",
    'sup_deleted' => "Supervisor deleted.",
    'sup_added'   => "Supervisor added.",
    'sup_updated' => "Supervisor updated.",
];
if (isset($_GET['flash']) && isset($flashMap[$_GET['flash']])) {
    $msg    = $flashMap[$_GET['flash']];
    $csvTab = ''; // never show a CSV log from flash messages
}
 
// Which tab to show on page load 
// Priority: explicit ?tab=sup, or supervisor action, or CSV was for supervisor
$activeTab = 'lec';
if ((isset($_GET['tab']) && $_GET['tab'] === 'sup') || $csvTab === 'sup') {
    $activeTab = 'sup';
}
 
// Auto-suggest next IDs
$nextLecId = 'L001';
$res = mysqli_query($conn, "SELECT lecturer_id FROM lecturer ORDER BY lecturer_id DESC LIMIT 1");
if ($row = mysqli_fetch_assoc($res)) {
    preg_match('/(\D+)(\d+)$/', $row['lecturer_id'], $m);
    $nextLecId = $m ? $m[1] . str_pad((int)$m[2]+1, strlen($m[2]), '0', STR_PAD_LEFT) : $row['lecturer_id'];
}
$nextSupId = 'S001';
$res2 = mysqli_query($conn, "SELECT supervisor_id FROM supervisor ORDER BY supervisor_id DESC LIMIT 1");
if ($row2 = mysqli_fetch_assoc($res2)) {
    preg_match('/(\D+)(\d+)$/', $row2['supervisor_id'], $m2);
    $nextSupId = $m2 ? $m2[1] . str_pad((int)$m2[2]+1, strlen($m2[2]), '0', STR_PAD_LEFT) : $row2['supervisor_id'];
}
 
// Edit mode 
$editLec = $editSup = null;
if (isset($_GET['edit_lec'])) {
    $r = mysqli_query($conn, "SELECT * FROM lecturer WHERE lecturer_id='".mysqli_real_escape_string($conn, $_GET['edit_lec'])."'");
    $editLec = mysqli_fetch_assoc($r);
}
if (isset($_GET['edit_sup'])) {
    $r = mysqli_query($conn, "SELECT * FROM supervisor WHERE supervisor_id='".mysqli_real_escape_string($conn, $_GET['edit_sup'])."'");
    $editSup = mysqli_fetch_assoc($r);
    $activeTab = 'sup';
}
?>
 
<div class="topbar">
    <h1>Manage Users</h1>
    <div class="tab-btn-group">
        <button class="btn tab-btn <?= $activeTab==='lec'?'active':'' ?>" onclick="switchTab('lecturers',this)">🧑‍🏫 Manage Lecturers</button>
        <button class="btn tab-btn <?= $activeTab==='sup'?'active':'' ?>" onclick="switchTab('supervisors',this)">👔 Manage Supervisors</button>
    </div>
</div>
 
<div class="page-body">
 
<?php if ($msg): ?>
<div class="alert <?= strpos($msg,'ERROR')===0?'alert-danger':'alert-success' ?>">
    <?= strpos($msg,'ERROR')===0?'⚠️':'✅' ?> <?= htmlspecialchars($msg) ?>
</div>
<?php endif; ?>
 
<div id="tab-lecturers" class="tab-panel" <?= $activeTab==='sup'?'style="display:none"':'' ?>>
<div class="section-label-row">🧑‍🏫 Lecturers</div>

<?php if ($csvLog && $csvTab === 'lec'): ?>
<div class="card">
    <div class="card-title flex-between">
        <span>📋 CSV Import Report — Lecturers</span>
        <span class="text-muted text-sm"><?= count($csvLog) ?> row(s) processed</span>
    </div>
    <div class="table-wrap">
    <table>
        <thead><tr><th>Row</th><th>ID</th><th>Name / Note</th><th>Result</th></tr></thead>
        <tbody>
        <?php foreach ($csvLog as $log): ?>
        <tr>
            <td class="text-muted"><?= $log['row'] ?></td>
            <td><strong><?= htmlspecialchars($log['id']) ?></strong></td>
            <td><?= htmlspecialchars($log['msg']) ?></td>
            <td>
                <?php if ($log['status']==='ok'): ?>
                    <span class="badge badge-pass">✅ Added</span>
                <?php elseif ($log['status']==='updated'): ?>
                    <span class="badge badge-pending">🔄 Updated</span>
                <?php elseif ($log['status']==='skipped'): ?>
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
        <span>📤 Bulk Upload Lecturer via CSV</span>
        <a href="users.php?template=lecturer" class="btn btn-outline btn-sm">⬇️ Download Template</a>
    </div>
    <div class="csv-info-box">
        <div class="csv-info-icon">💡</div>
        <div>Required columns:
            <span class="csv-col">lecturer_id</span>
            <span class="csv-col">lecturer_name</span>
            <span class="csv-col">lecturer_email</span>
            <span class="csv-col">lecturer_password</span>
        </div>
    </div>
    <form action="users.php" method="POST" enctype="multipart/form-data" style="margin-top:14px;">
        <input type="hidden" name="action" value="csv_lec">
        <div class="csv-upload-area" id="csvDropLec">
            <input type="file" name="csv_file" id="csvFileLec" accept=".csv" required style="display:none;">
            <div class="csv-upload-icon">📁</div>
            <p class="csv-upload-label">Drag &amp; drop your CSV file here, or <span class="csv-browse-link" onclick="document.getElementById('csvFileLec').click()">browse</span></p>
            <p class="csv-upload-hint" id="csvFileNameLec">Accepted format: .csv only</p>
        </div>
        <div style="margin-top:12px;"><button type="submit" class="btn btn-success">📤 Upload Lecturers</button></div>
    </form>
</div>
 
<div class="card" id="lec-form">
    <div class="card-title"><?= $editLec?'✏️ Edit Lecturer':'➕ Add Single Lecturer' ?></div>
    <form action="users.php" method="POST">
        <input type="hidden" name="action" value="<?= $editLec?'edit_lec':'add_lec' ?>">
        <div class="form-row">
            <div class="form-group">
                <label>Lecturer ID</label>
                <input type="text" name="lecturer_id"
                       value="<?= htmlspecialchars($editLec['lecturer_id'] ?? $nextLecId) ?>"
                       <?= $editLec?'readonly':'' ?> required>
            </div>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="lecturer_name"
                       value="<?= htmlspecialchars($editLec['lecturer_name'] ?? '') ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="lecturer_email"
                       value="<?= htmlspecialchars($editLec['lecturer_email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="text" name="lecturer_password"
                       value="<?= htmlspecialchars($editLec['lecturer_password'] ?? '') ?>" required>
            </div>
        </div>
        <div class="flex-gap">
            <button type="submit" class="btn btn-primary"><?= $editLec?'💾 Update Lecturer':'➕ Add Lecturer' ?></button>
            <?php if ($editLec): ?><a href="users.php" class="btn btn-outline">Cancel</a><?php endif; ?>
        </div>
    </form>
</div>
 
<div class="card">
    <div class="card-title">Lecturer Accounts</div>
    <div class="table-wrap">
    <table>
        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Actions</th></tr></thead>
        <tbody>
        <?php $res = mysqli_query($conn, "SELECT * FROM lecturer ORDER BY lecturer_id");
        while ($row = mysqli_fetch_assoc($res)): ?>
        <tr>
            <td><strong><?= htmlspecialchars($row['lecturer_id']) ?></strong></td>
            <td><?= htmlspecialchars($row['lecturer_name']) ?></td>
            <td><span class="text-muted"><?= htmlspecialchars($row['lecturer_email']) ?></span></td>
            <td>
                <a href="users.php?edit_lec=<?= urlencode($row['lecturer_id']) ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                <a href="users.php?del_lec=<?= urlencode($row['lecturer_id']) ?>"
                   class="btn btn-danger btn-sm confirm-delete"
                   data-msg="Delete lecturer <?= htmlspecialchars($row['lecturer_name']) ?>?">🗑️ Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    </div>
</div>
</div><div id="tab-supervisors" class="tab-panel" <?= $activeTab==='lec'?'style="display:none"':'' ?>>
<div class="section-label-row">👔 Supervisors</div>

<?php if ($csvLog && $csvTab === 'sup'): ?>
<div class="card">
    <div class="card-title flex-between">
        <span>📋 CSV Import Report — Supervisors</span>
        <span class="text-muted text-sm"><?= count($csvLog) ?> row(s) processed</span>
    </div>
    <div class="table-wrap">
    <table>
        <thead><tr><th>Row</th><th>ID</th><th>Name / Note</th><th>Result</th></tr></thead>
        <tbody>
        <?php foreach ($csvLog as $log): ?>
        <tr>
            <td class="text-muted"><?= $log['row'] ?></td>
            <td><strong><?= htmlspecialchars($log['id']) ?></strong></td>
            <td><?= htmlspecialchars($log['msg']) ?></td>
            <td>
                <?php if ($log['status']==='ok'): ?>
                    <span class="badge badge-pass">✅ Added</span>
                <?php elseif ($log['status']==='updated'): ?>
                    <span class="badge badge-pending">🔄 Updated</span>
                <?php elseif ($log['status']==='skipped'): ?>
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
        <span>📤 Bulk Upload Supervisors via CSV</span>
        <a href="users.php?template=supervisor" class="btn btn-outline btn-sm">⬇️ Download Template</a>
    </div>
    <div class="csv-info-box">
        <div class="csv-info-icon">💡</div>
        <div>Required columns:
            <span class="csv-col">supervisor_id</span>
            <span class="csv-col">supervisor_name</span>
            <span class="csv-col">supervisor_email</span>
            <span class="csv-col">supervisor_password</span>
            <span class="csv-col">company_name</span>
        </div>
    </div>
    <form action="users.php" method="POST" enctype="multipart/form-data" style="margin-top:14px;">
        <input type="hidden" name="action" value="csv_sup">
        <div class="csv-upload-area" id="csvDropSup">
            <input type="file" name="csv_file" id="csvFileSup" accept=".csv" required style="display:none;">
            <div class="csv-upload-icon">📁</div>
            <p class="csv-upload-label">Drag &amp; drop your CSV file here, or <span class="csv-browse-link" onclick="document.getElementById('csvFileSup').click()">browse</span></p>
            <p class="csv-upload-hint" id="csvFileNameSup">Accepted format: .csv only</p>
        </div>
        <div style="margin-top:12px;"><button type="submit" class="btn btn-success">📤 Upload Supervisors</button></div>
    </form>
</div>
 
<div class="card" id="sup-form">
    <div class="card-title"><?= $editSup?'✏️ Edit Supervisor':'➕ Add Single Supervisor' ?></div>
    <form action="users.php" method="POST">
        <input type="hidden" name="action" value="<?= $editSup?'edit_sup':'add_sup' ?>">
        <div class="form-row">
            <div class="form-group">
                <label>Supervisor ID</label>
                <input type="text" name="supervisor_id"
                       value="<?= htmlspecialchars($editSup['supervisor_id'] ?? $nextSupId) ?>"
                       <?= $editSup?'readonly':'' ?> required>
            </div>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="supervisor_name"
                       value="<?= htmlspecialchars($editSup['supervisor_name'] ?? '') ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="supervisor_email"
                       value="<?= htmlspecialchars($editSup['supervisor_email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="text" name="supervisor_password"
                       value="<?= htmlspecialchars($editSup['supervisor_password'] ?? '') ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label>Company (where this supervisor works)</label>
            <select name="supervisor_company_id">
                <option value="">— Not linked to a company —</option>
                <?php $cRes2 = mysqli_query($conn, "SELECT company_id,company_name FROM company ORDER BY company_name");
                while ($cr = mysqli_fetch_assoc($cRes2)): ?>
                <option value="<?= $cr['company_id'] ?>"
                    <?= ($editSup && $editSup['company_id']==$cr['company_id'])?'selected':'' ?>>
                    <?= htmlspecialchars($cr['company_name']) ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="flex-gap">
            <button type="submit" class="btn btn-primary"><?= $editSup?'💾 Update Supervisor':'➕ Add Supervisor' ?></button>
            <?php if ($editSup): ?><a href="users.php?tab=sup" class="btn btn-outline">Cancel</a><?php endif; ?>
        </div>
    </form>
</div>
 
<div class="card">
    <div class="card-title">Supervisor Accounts</div>
    <div class="table-wrap">
    <table>
        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Company</th><th>Actions</th></tr></thead>
        <tbody>
        <?php $res = mysqli_query($conn, "SELECT sv.*,c.company_name FROM supervisor sv LEFT JOIN company c ON sv.company_id=c.company_id ORDER BY sv.supervisor_id");
        while ($row = mysqli_fetch_assoc($res)): ?>
        <tr>
            <td><strong><?= htmlspecialchars($row['supervisor_id']) ?></strong></td>
            <td><?= htmlspecialchars($row['supervisor_name']) ?></td>
            <td><span class="text-muted"><?= htmlspecialchars($row['supervisor_email']) ?></span></td>
            <td><?= htmlspecialchars($row['company_name'] ?? '—') ?></td>
            <td>
                <a href="users.php?edit_sup=<?= urlencode($row['supervisor_id']) ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                <a href="users.php?del_sup=<?= urlencode($row['supervisor_id']) ?>"
                   class="btn btn-danger btn-sm confirm-delete"
                   data-msg="Delete supervisor <?= htmlspecialchars($row['supervisor_name']) ?>?">🗑️ Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    </div>
</div>
</div></div><style>
.tab-btn-group{display:flex;gap:10px}
.tab-btn{background:#e9ecef;color:#495057;border:none;border-radius:8px;padding:8px 18px;font-weight:600;cursor:pointer;transition:background .2s,color .2s}
.tab-btn.active{background:var(--accent);color:#fff}
.tab-btn:not(.active):hover{background:#dee2e6}
</style>
 
<script>
function switchTab(name, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).style.display = '';
    btn.classList.add('active');
}
</script>
 
<?php include "footer.php"; ?>