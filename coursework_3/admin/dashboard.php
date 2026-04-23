<?php
session_start();
require_once "../connection.php";
$pageTitle = "Dashboard";
include "header.php";

$totalStudents    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM student"))[0];
$totalInternships = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM internship"))[0];
$totalLecturers   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM lecturer"))[0];
$totalSupervisors = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM supervisor"))[0];
$pendingMarks     = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM internship WHERE average_marks IS NULL"))[0];
$graded           = $totalInternships - $pendingMarks;
?>

<div class="topbar">
    <h1>Dashboard</h1>
</div>

<div class="page-body">

<?php if ($pendingMarks > 0): ?>
<div class="alert alert-info" style="margin-bottom:24px">
    ⚠️ <strong><?= $pendingMarks ?></strong> internship<?= $pendingMarks > 1 ? 's' : '' ?> still awaiting marks.
</div>
<?php endif; ?>

<div class="dash-grid">
    <a href="students.php"      class="dash-card dc-blue">
        <div class="dc-icon">👨‍🎓</div>
        <div class="dc-label">Manage Students</div>
        <div class="dc-count"><?= $totalStudents ?></div>
    </a>
    <a href="internships.php"   class="dash-card dc-green">
        <div class="dc-icon">🏢</div>
        <div class="dc-label">Manage Internships</div>
        <div class="dc-count"><?= $totalInternships ?></div>
    </a>
    <a href="users.php"         class="dash-card dc-amber">
        <div class="dc-icon">👥</div>
        <div class="dc-label">Manage Users</div>
        <div class="dc-count"><?= $totalLecturers + $totalSupervisors ?></div>
    </a>
    <a href="results.php"       class="dash-card dc-indigo">
        <div class="dc-icon">📊</div>
        <div class="dc-label">View Results</div>
        <div class="dc-count"><?= $graded ?> graded</div>
    </a>
    <a href="print_results.php" class="dash-card dc-slate">
        <div class="dc-icon">🖨️</div>
        <div class="dc-label">Print Report</div>
    </a>
    <a href="import_students.php" class="dash-card dc-slate">
        <div class="dc-icon">📂</div>
        <div class="dc-label">Import Students</div>
    </a>
</div>

</div>
<?php include "footer.php"; ?>
