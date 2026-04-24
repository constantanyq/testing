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

<div class="dash-list">
    <div class="card dash-list-item border-blue">
        <div class="dash-item-left">
            <div class="dash-item-icon">👨‍🎓</div>
            <div class="dash-item-text">
                <h3>Manage Students</h3>
                <p>Total Registered: <strong><?= $totalStudents ?></strong></p>
            </div>
        </div>
        <div class="dash-item-action">
            <a href="students.php" class="btn btn-primary">Manage Students ➔</a>
        </div>
    </div>

    <div class="card dash-list-item border-green">
        <div class="dash-item-left">
            <div class="dash-item-icon">🏢</div>
            <div class="dash-item-text">
                <h3>Manage Internships</h3>
                <p>Active Internships: <strong><?= $totalInternships ?></strong></p>
            </div>
        </div>
        <div class="dash-item-action">
            <a href="internships.php" class="btn btn-success">View Internships ➔</a>
        </div>
    </div>

    <div class="card dash-list-item border-amber">
        <div class="dash-item-left">
            <div class="dash-item-icon">👥</div>
            <div class="dash-item-text">
                <h3>Manage Users</h3>
                <p>Lecturers & Supervisors: <strong><?= $totalLecturers + $totalSupervisors ?></strong></p>
            </div>
        </div>
        <div class="dash-item-action">
            <a href="users.php" class="btn btn-warning">Manage Users ➔</a>
        </div>
    </div>

    <div class="card dash-list-item border-indigo">
        <div class="dash-item-left">
            <div class="dash-item-icon">📊</div>
            <div class="dash-item-text">
                <h3>View Results</h3>
                <p>Graded Internships: <strong><?= $graded ?></strong></p>
            </div>
        </div>
        <div class="dash-item-action">
            <a href="results.php" class="btn btn-primary" style="background: #6366f1;">Check Results ➔</a>
        </div>
    </div>

    <div class="card dash-list-item border-teal">
        <div class="dash-item-left">
            <div class="dash-item-icon">🖨️</div>
            <div class="dash-item-text">
                <h3>Print Report</h3>
                <p>Generate summary documents</p>
            </div>
        </div>
        <div class="dash-item-action">
            <a href="print_results.php" class="btn btn-teal">Generate Report ➔</a>
        </div>
    </div>

    <div class="card dash-list-item border-purple">
        <div class="dash-item-left">
            <div class="dash-item-icon">📂</div>
            <div class="dash-item-text">
                <h3>Import Data</h3>
                <p>Bulk upload via CSV</p>
            </div>
        </div>
        <div class="dash-item-action">
            <a href="import_hub.php" class="btn btn-purple">Import Data ➔</a>
        </div>
    </div>
</div>

</div>
<?php include "footer.php"; ?>
