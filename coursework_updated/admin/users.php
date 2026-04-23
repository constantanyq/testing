<?php
session_start();
require_once "../connection.php";
$pageTitle = "Manage Users";
include "header.php";

$msg = "";

// ---- DELETE LECTURER ----
if (isset($_GET['del_lec'])) {
    $id = mysqli_real_escape_string($conn, $_GET['del_lec']);
    mysqli_query($conn, "DELETE FROM lecturer WHERE lecturer_id='$id'");
    $msg = "Lecturer deleted.";
}

// ---- DELETE SUPERVISOR ----
if (isset($_GET['del_sup'])) {
    $id = mysqli_real_escape_string($conn, $_GET['del_sup']);
    mysqli_query($conn, "DELETE FROM supervisor WHERE supervisor_id='$id'");
    $msg = "Supervisor deleted.";
}

// ---- ADD LECTURER ----
if (isset($_POST['action']) && $_POST['action'] == 'add_lec') {
    $lid   = mysqli_real_escape_string($conn, trim($_POST['lecturer_id']));
    $lname = mysqli_real_escape_string($conn, trim($_POST['lecturer_name']));
    $lemail= mysqli_real_escape_string($conn, trim($_POST['lecturer_email']));
    $lpass = mysqli_real_escape_string($conn, trim($_POST['lecturer_password']));

    $chk = mysqli_query($conn, "SELECT lecturer_id FROM lecturer WHERE lecturer_id='$lid'");
    if (mysqli_num_rows($chk) > 0) {
        $msg = "ERROR: Lecturer ID already exists.";
    } else {
        mysqli_query($conn, "INSERT INTO lecturer (lecturer_id,lecturer_name,lecturer_email,lecturer_password)
                              VALUES ('$lid','$lname','$lemail','$lpass')");
        $msg = "Lecturer added.";
    }
}

// ---- EDIT LECTURER ----
if (isset($_POST['action']) && $_POST['action'] == 'edit_lec') {
    $lid   = mysqli_real_escape_string($conn, trim($_POST['lecturer_id']));
    $lname = mysqli_real_escape_string($conn, trim($_POST['lecturer_name']));
    $lemail= mysqli_real_escape_string($conn, trim($_POST['lecturer_email']));
    $lpass = mysqli_real_escape_string($conn, trim($_POST['lecturer_password']));

    mysqli_query($conn, "UPDATE lecturer SET lecturer_name='$lname', lecturer_email='$lemail',
                          lecturer_password='$lpass' WHERE lecturer_id='$lid'");
    $msg = "Lecturer updated.";
}

// ---- ADD SUPERVISOR ----
if (isset($_POST['action']) && $_POST['action'] == 'add_sup') {
    $sid   = mysqli_real_escape_string($conn, trim($_POST['supervisor_id']));
    $sname = mysqli_real_escape_string($conn, trim($_POST['supervisor_name']));
    $semail= mysqli_real_escape_string($conn, trim($_POST['supervisor_email']));
    $spass = mysqli_real_escape_string($conn, trim($_POST['supervisor_password']));

    $chk = mysqli_query($conn, "SELECT supervisor_id FROM supervisor WHERE supervisor_id='$sid'");
    if (mysqli_num_rows($chk) > 0) {
        $msg = "ERROR: Supervisor ID already exists.";
    } else {
        $scid = mysqli_real_escape_string($conn, trim($_POST['supervisor_company_id'] ?? ''));
        $scid_val = $scid ? "'$scid'" : 'NULL';
        mysqli_query($conn, "INSERT INTO supervisor (supervisor_id,supervisor_name,supervisor_email,supervisor_password,company_id)
                              VALUES ('$sid','$sname','$semail','$spass',$scid_val)");
        $msg = "Supervisor added.";
    }
}

// ---- EDIT SUPERVISOR ----
if (isset($_POST['action']) && $_POST['action'] == 'edit_sup') {
    $sid   = mysqli_real_escape_string($conn, trim($_POST['supervisor_id']));
    $sname = mysqli_real_escape_string($conn, trim($_POST['supervisor_name']));
    $semail= mysqli_real_escape_string($conn, trim($_POST['supervisor_email']));
    $spass = mysqli_real_escape_string($conn, trim($_POST['supervisor_password']));

    $scid2 = mysqli_real_escape_string($conn, trim($_POST['supervisor_company_id'] ?? ''));
    $scid2_val = $scid2 ? "'$scid2'" : 'NULL';
    mysqli_query($conn, "UPDATE supervisor SET supervisor_name='$sname', supervisor_email='$semail',
                          supervisor_password='$spass', company_id=$scid2_val
                          WHERE supervisor_id='$sid'");
    $msg = "Supervisor updated.";
}

// ---- Suggest next IDs ----
$nextLecId = 'L001';
$res = mysqli_query($conn, "SELECT lecturer_id FROM lecturer ORDER BY lecturer_id DESC LIMIT 1");
if ($row = mysqli_fetch_assoc($res)) {
    preg_match('/(\D+)(\d+)$/', $row['lecturer_id'], $m);
    $nextLecId = $m ? $m[1] . str_pad((int)$m[2] + 1, strlen($m[2]), '0', STR_PAD_LEFT) : $row['lecturer_id'];
}

$nextSupId = 'S001';
$res2 = mysqli_query($conn, "SELECT supervisor_id FROM supervisor ORDER BY supervisor_id DESC LIMIT 1");
if ($row2 = mysqli_fetch_assoc($res2)) {
    preg_match('/(\D+)(\d+)$/', $row2['supervisor_id'], $m2);
    $nextSupId = $m2 ? $m2[1] . str_pad((int)$m2[2] + 1, strlen($m2[2]), '0', STR_PAD_LEFT) : $row2['supervisor_id'];
}

// Edit mode – fetch record
$editLec = $editSup = null;
if (isset($_GET['edit_lec'])) {
    $res = mysqli_query($conn, "SELECT * FROM lecturer WHERE lecturer_id='".mysqli_real_escape_string($conn,$_GET['edit_lec'])."'");
    $editLec = mysqli_fetch_assoc($res);
}
if (isset($_GET['edit_sup'])) {
    $res = mysqli_query($conn, "SELECT * FROM supervisor WHERE supervisor_id='".mysqli_real_escape_string($conn,$_GET['edit_sup'])."'");
    $editSup = mysqli_fetch_assoc($res);
}
?>

<div class="topbar">
    <h1>Manage Users</h1>
    <span class="breadcrumb">Admin &rsaquo; Users</span>
</div>

<div class="page-body">

<?php if ($msg): ?>
    <div class="alert <?= strpos($msg,'ERROR') === 0 ? 'alert-danger' : 'alert-success' ?>">
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<!-- ========== LECTURERS ========== -->
<div class="card">
    <div class="card-title"><?= $editLec ? 'Edit Lecturer' : 'Add Lecturer' ?></div>
    <div class="card-body">
    <form method="POST">
        <input type="hidden" name="action" value="<?= $editLec ? 'edit_lec' : 'add_lec' ?>">
        <div class="form-row">
            <div class="form-group">
                <label>Lecturer ID</label>
                <input type="text" name="lecturer_id"
                       value="<?= htmlspecialchars($editLec['lecturer_id'] ?? $nextLecId) ?>"
                       <?= $editLec ? 'readonly' : '' ?> required>
                <?php if (!$editLec): ?>
                <small class="text-muted">💡 Suggested next ID: <strong><?= htmlspecialchars($nextLecId) ?></strong></small>
                <?php endif; ?>
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
        <div style="display:flex; gap:10px;">
            <button type="submit" class="btn btn-primary">
                <?= $editLec ? '💾 Update Lecturer' : '➕ Add Lecturer' ?>
            </button>
            <?php if ($editLec): ?><a href="users.php" class="btn btn-outline">Cancel</a><?php endif; ?>
        </div>
    </form>
</div>

<!-- Lecturer List -->
<div class="card">
    <div class="card-title">Lecturer Accounts</div>
    <div class="table-wrap">
    <table>
        <thead>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php
        $res = mysqli_query($conn, "SELECT * FROM lecturer ORDER BY lecturer_id");
        while ($row = mysqli_fetch_assoc($res)):
        ?>
        <tr>
            <td><?= htmlspecialchars($row['lecturer_id']) ?></td>
            <td><?= htmlspecialchars($row['lecturer_name']) ?></td>
            <td><?= htmlspecialchars($row['lecturer_email']) ?></td>
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

<hr>

<!-- ========== SUPERVISORS ========== -->
<div class="card">
    <div class="card-title"><?= $editSup ? 'Edit Supervisor' : 'Add Supervisor' ?></div>
    <div class="card-body">
    <form method="POST">
        <input type="hidden" name="action" value="<?= $editSup ? 'edit_sup' : 'add_sup' ?>">
        <div class="form-row">
            <div class="form-group">
                <label>Supervisor ID</label>
                <input type="text" name="supervisor_id"
                       value="<?= htmlspecialchars($editSup['supervisor_id'] ?? $nextSupId) ?>"
                       <?= $editSup ? 'readonly' : '' ?> required>
                <?php if (!$editSup): ?>
                <small class="text-muted">💡 Suggested next ID: <strong><?= htmlspecialchars($nextSupId) ?></strong></small>
                <?php endif; ?>
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
                <option value="">-- Not linked to a company --</option>
                <?php
                $cRes2 = mysqli_query($conn, "SELECT company_id, company_name FROM company ORDER BY company_name");
                while ($cr = mysqli_fetch_assoc($cRes2)):
                ?>
                <option value="<?= $cr['company_id'] ?>"
                    <?= ($editSup && $editSup['company_id'] == $cr['company_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cr['company_name']) ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div style="display:flex; gap:10px;">
            <button type="submit" class="btn btn-primary">
                <?= $editSup ? '💾 Update Supervisor' : '➕ Add Supervisor' ?>
            </button>
            <?php if ($editSup): ?><a href="users.php" class="btn btn-outline">Cancel</a><?php endif; ?>
        </div>
    </form>
</div>

<!-- Supervisor List -->
<div class="card">
    <div class="card-title">Supervisor Accounts</div>
    <div class="table-wrap">
    <table>
        <thead>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Company</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php
        $res = mysqli_query($conn, "SELECT sv.*, c.company_name FROM supervisor sv LEFT JOIN company c ON sv.company_id=c.company_id ORDER BY sv.supervisor_id");
        while ($row = mysqli_fetch_assoc($res)):
        ?>
        <tr>
            <td><?= htmlspecialchars($row['supervisor_id']) ?></td>
            <td><?= htmlspecialchars($row['supervisor_name']) ?></td>
            <td><?= htmlspecialchars($row['supervisor_email']) ?></td>
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

</div>
<?php include "footer.php"; ?>
