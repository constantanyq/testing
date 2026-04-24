<?php
session_start();
require_once "../connection.php";
$pageTitle = "Dashboard";
include "header.php";

$totalStudents     = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM student"))[0];
$totalInternships  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM internship"))[0];
$totalLecturers    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM lecturer"))[0];
$totalSupervisors  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM supervisor"))[0];
$totalUsers        = $totalLecturers + $totalSupervisors;
$gradedInternships = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM internship WHERE average_marks IS NOT NULL"))[0];
?>

<div class="topbar">
    <h1>Dashboard</h1>
    <span class="breadcrumb">Admin &rsaquo; Dashboard</span>
</div>

<div class="page-body">

    <div class="dashboard-cards">

        <div class="dash-card" style="--card-accent:#3b6ee8;">
            <div class="dash-card-left">
                <div class="dash-card-icon">👨‍🎓</div>
                <div class="dash-card-body">
                    <div class="dash-card-title">Manage Students</div>
                    <div class="dash-card-sub">Total Registered: <strong><?= $totalStudents ?></strong></div>
                </div>
            </div>
            <a href="students.php" class="btn btn-primary">Manage Students &rarr;</a>
        </div>

        <div class="dash-card" style="--card-accent:#0e9f6e;">
            <div class="dash-card-left">
                <div class="dash-card-icon">🏢</div>
                <div class="dash-card-body">
                    <div class="dash-card-title">Manage Internships</div>
                    <div class="dash-card-sub">Active Internships: <strong><?= $totalInternships ?></strong></div>
                </div>
            </div>
            <a href="internships.php" class="btn btn-success">View Internships &rarr;</a>
        </div>

        <div class="dash-card" style="--card-accent:#d97706;">
            <div class="dash-card-left">
                <div class="dash-card-icon">👥</div>
                <div class="dash-card-body">
                    <div class="dash-card-title">Manage Users</div>
                    <div class="dash-card-sub">Lecturers &amp; Supervisors: <strong><?= $totalUsers ?></strong></div>
                </div>
            </div>
            <a href="users.php" class="btn btn-warning">Manage Users &rarr;</a>
        </div>

        <div class="dash-card" style="--card-accent:#7c3aed;">
            <div class="dash-card-left">
                <div class="dash-card-icon">📊</div>
                <div class="dash-card-body">
                    <div class="dash-card-title">View Results</div>
                    <div class="dash-card-sub">Graded Internships: <strong><?= $gradedInternships ?></strong></div>
                </div>
            </div>
            <a href="results.php" class="btn btn-purple">Check Results &rarr;</a>
        </div>

        <div class="dash-card" style="--card-accent:#0891b2;">
            <div class="dash-card-left">
                <div class="dash-card-icon">🖨️</div>
                <div class="dash-card-body">
                    <div class="dash-card-title">Print Report</div>
                    <div class="dash-card-sub">Generate summary documents</div>
                </div>
            </div>
            <a href="print_report.php" class="btn btn-teal">Generate Report &rarr;</a>
        </div>

    </div>

</div><!-- page-body -->

<?php include "footer.php"; ?>
