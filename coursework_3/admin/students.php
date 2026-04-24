<?php
session_start();
require_once "../connection.php";
$pageTitle = "Students";
include "header.php";

$msg = "";

// ---- DELETE ----
if (isset($_GET['delete'])) {
    $del_id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM student WHERE student_id = '$del_id'");
    $msg = "Student deleted successfully.";
}

// ---- ADD ----
if (isset($_POST['action']) && $_POST['action'] == 'add') {
    $sid   = mysqli_real_escape_string($conn, trim($_POST['student_id']));
    $sname = mysqli_real_escape_string($conn, trim($_POST['student_name']));
    $spass = mysqli_real_escape_string($conn, trim($_POST['student_password']));
    $semail= mysqli_real_escape_string($conn, trim($_POST['student_email']));
    $sprog = mysqli_real_escape_string($conn, trim($_POST['programme']));

    // Check if ID already exists
    $check = mysqli_query($conn, "SELECT student_id FROM student WHERE student_id='$sid'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "ERROR: Student ID already exists.";
    } else {
        $sql = "INSERT INTO student (student_id, student_name, student_password, student_email, programme)
                VALUES ('$sid','$sname','$spass','$semail','$sprog')";
        mysqli_query($conn, $sql);
        $msg = "Student added successfully.";
    }
}

// ---- EDIT ----
if (isset($_POST['action']) && $_POST['action'] == 'edit') {
    $sid   = mysqli_real_escape_string($conn, trim($_POST['student_id']));
    $sname = mysqli_real_escape_string($conn, trim($_POST['student_name']));
    $spass = mysqli_real_escape_string($conn, trim($_POST['student_password']));
    $semail= mysqli_real_escape_string($conn, trim($_POST['student_email']));
    $sprog = mysqli_real_escape_string($conn, trim($_POST['programme']));

    $sql = "UPDATE student SET student_name='$sname', student_password='$spass',
            student_email='$semail', programme='$sprog'
            WHERE student_id='$sid'";
    mysqli_query($conn, $sql);
    $msg = "Student updated successfully.";
}

// Fetch student for editing if edit_id is in URL
$editStudent = null;
if (isset($_GET['edit'])) {
    $eid = mysqli_real_escape_string($conn, $_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM student WHERE student_id='$eid'");
    $editStudent = mysqli_fetch_assoc($res);
}

// Suggest next student ID
$nextStuId = 'STU001';
$sRes = mysqli_query($conn, "SELECT student_id FROM student ORDER BY student_id DESC LIMIT 1");
if ($sRow = mysqli_fetch_assoc($sRes)) {
    preg_match('/(\D+)(\d+)$/', $sRow['student_id'], $sm);
    $nextStuId = $sm ? $sm[1] . str_pad((int)$sm[2] + 1, strlen($sm[2]), '0', STR_PAD_LEFT) : $sRow['student_id'];
}
?>

<div class="topbar">
    <h1>Manage Students</h1>
    
</div>

<div class="page-body">

<?php if ($msg): ?>
    <div class="alert <?= strpos($msg,'ERROR') === 0 ? 'alert-danger' : 'alert-success' ?>">
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<!-- Add / Edit Form -->
<div class="card card-form">
    <div class="card-title"><?= $editStudent ? '✏️ Edit Student' : '➕ Add New Student' ?></div>
    <div class="card-body">
    <div class="card-body">
    <form method="POST" action="">
        <input type="hidden" name="action" value="<?= $editStudent ? 'edit' : 'add' ?>">

        <div class="form-row">
            <div class="form-group">
                <label>Student ID</label>
                <input type="text" name="student_id"
                       value="<?= htmlspecialchars($editStudent['student_id'] ?? $nextStuId) ?>"
                       <?= $editStudent ? 'readonly' : '' ?> required>
                <?php if (!$editStudent): ?>
                <small class="text-muted">💡 Suggested next ID: <strong><?= htmlspecialchars($nextStuId) ?></strong></small>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="student_name"
                       value="<?= htmlspecialchars($editStudent['student_name'] ?? '') ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="student_email"
                       value="<?= htmlspecialchars($editStudent['student_email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Programme</label>
                <input type="text" name="programme"
                       value="<?= htmlspecialchars($editStudent['programme'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="text" name="student_password"
                   value="<?= htmlspecialchars($editStudent['student_password'] ?? '') ?>" required>
        </div>

        <div style="display:flex; gap:10px;">
            <button type="submit" class="btn btn-primary">
                <?= $editStudent ? '💾 Update Student' : '➕ Add Student' ?>
            </button>
            <?php if ($editStudent): ?>
                <a href="students.php" class="btn btn-outline">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>
</div><!-- /card-body -->

<div class="section-gap"><span>Student Records</span></div>

<!-- Student List -->
<div class="card card-table">
    <div class="card-title flex-between">
        <span>Student List</span>
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search by name or ID...">
        </div>
    </div>
    <div class="table-wrap">
    <table id="mainTable">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Programme</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $res = mysqli_query($conn, "SELECT * FROM student ORDER BY student_id");
        while ($row = mysqli_fetch_assoc($res)):
        ?>
        <tr>
            <td><?= htmlspecialchars($row['student_id']) ?></td>
            <td><?= htmlspecialchars($row['student_name']) ?></td>
            <td><?= htmlspecialchars($row['student_email']) ?></td>
            <td><?= htmlspecialchars($row['programme']) ?></td>
            <td>
                <a href="students.php?edit=<?= urlencode($row['student_id']) ?>"
                   class="btn btn-warning btn-sm">✏️ Edit</a>
                <a href="students.php?delete=<?= urlencode($row['student_id']) ?>"
                   class="btn btn-danger btn-sm confirm-delete"
                   data-msg="Delete student <?= htmlspecialchars($row['student_name']) ?>? This cannot be undone.">
                   🗑️ Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    </div>
</div>

</div><!-- page-body -->
<?php include "footer.php"; ?>
