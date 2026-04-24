<?php
session_start();
require_once "../connection.php";
$pageTitle = "Dashboard";
include "header.php";

$uid  = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Count students assigned to this assessor
if ($role == 'lecturer') {
    $countRes = mysqli_query($conn, "SELECT COUNT(*) FROM internship WHERE lecturer_id='$uid'");
} else {
    $countRes = mysqli_query($conn, "SELECT COUNT(*) FROM internship WHERE supervisor_id='$uid'");
}
$totalAssigned = mysqli_fetch_row($countRes)[0];

// Count students already marked
if ($role == 'lecturer') {
    $markedRes = mysqli_query($conn, "SELECT COUNT(*) FROM internship WHERE lecturer_id='$uid' AND l_marks_id IS NOT NULL");
} else {
    $markedRes = mysqli_query($conn, "SELECT COUNT(*) FROM internship WHERE supervisor_id='$uid' AND s_marks_id IS NOT NULL");
}
$totalMarked = mysqli_fetch_row($markedRes)[0];
$pending = $totalAssigned - $totalMarked;
?>

<div class="topbar">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h1>
    <span class="breadcrumb">Assessor &rsaquo; Dashboard</span>
</div>

<div class="page-body">
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-num"><?= $totalAssigned ?></div>
            <div class="stat-label">Students Assigned</div>
        </div>
        <div class="stat-box">
            <div class="stat-num"><?= $totalMarked ?></div>
            <div class="stat-label">Students Marked</div>
        </div>
        <div class="stat-box">
            <div class="stat-num" style="color:<?= $pending > 0 ? '#e74c3c' : '#27ae60' ?>"><?= $pending ?></div>
            <div class="stat-label">Pending</div>
        </div>
    </div>

    <?php if ($pending > 0): ?>
    <div class="alert alert-info">
        ⚠️ You have <strong><?= $pending ?></strong> student(s) with no marks yet.
        <a href="marks.php">Enter marks now →</a>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-title">Quick Actions</div>
        <div style="display:flex; gap:12px;">
            <a href="marks.php"        class="btn btn-primary">✏️ Enter / Edit Marks</a>
            <a href="view_results.php" class="btn btn-outline">📊 View Results</a>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
