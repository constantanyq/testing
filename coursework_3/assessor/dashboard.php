<?php
session_start();
require_once "../connection.php";
$pageTitle = "Dashboard";
include "header.php";

$uid  = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role == 'lecturer') {
    $countRes  = mysqli_query($conn, "SELECT COUNT(*) FROM internship WHERE lecturer_id='$uid'");
    $markedRes = mysqli_query($conn, "SELECT COUNT(*) FROM internship WHERE lecturer_id='$uid' AND l_marks_id IS NOT NULL");
} else {
    $countRes  = mysqli_query($conn, "SELECT COUNT(*) FROM internship WHERE supervisor_id='$uid'");
    $markedRes = mysqli_query($conn, "SELECT COUNT(*) FROM internship WHERE supervisor_id='$uid' AND s_marks_id IS NOT NULL");
}
$totalAssigned = mysqli_fetch_row($countRes)[0];
$totalMarked   = mysqli_fetch_row($markedRes)[0];
$pending       = $totalAssigned - $totalMarked;
$pct           = $totalAssigned > 0 ? round(($totalMarked / $totalAssigned) * 100) : 0;
?>

<div class="topbar">
    <h1>Dashboard</h1>
</div>

<div class="page-body">

<?php if ($pending > 0): ?>
<div class="alert alert-info" style="margin-bottom:24px">
    ⚠️ <strong><?= $pending ?></strong> student<?= $pending > 1 ? 's' : '' ?> still awaiting your marks.
</div>
<?php endif; ?>

<div class="dash-grid" style="grid-template-columns: repeat(2, 1fr);">
    <a href="marks.php"        class="dash-card dc-blue">
        <div class="dc-icon">✏️</div>
        <div class="dc-label">Enter / Edit Marks</div>
        <?php if ($pending > 0): ?>
        <div class="dc-count dc-count-red"><?= $pending ?> pending</div>
        <?php endif; ?>
    </a>
    <a href="view_results.php" class="dash-card dc-green">
        <div class="dc-icon">📊</div>
        <div class="dc-label">View Results</div>
        <div class="dc-count"><?= $totalMarked ?> marked</div>
    </a>
</div>

<div class="progress-card">
    <div class="progress-card-top">
        <span class="progress-label">Assessment Progress</span>
        <span class="progress-pct"><?= $pct ?>%</span>
    </div>
    <div class="progress-track">
        <div class="progress-fill" style="width:<?= $pct ?>%"></div>
    </div>
    <div class="progress-sub"><?= $totalMarked ?> of <?= $totalAssigned ?> students marked</div>
</div>

</div>
<?php include "footer.php"; ?>
