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
    <div>
        <div class="topbar-greeting">Good <?= (date('H') < 12) ? 'morning' : ((date('H') < 18) ? 'afternoon' : 'evening') ?>, <?= htmlspecialchars(explode(' ', $_SESSION['name'])[0]) ?> 👋</div>
        <div class="breadcrumb"><?= ucfirst($role) ?> Panel &rsaquo; Dashboard</div>
    </div>
    <span class="topbar-date"><?= date('l, d F Y') ?></span>
</div>

<div class="page-body">

<!-- ── Welcome banner ── -->
<div class="welcome-banner">
    <div class="welcome-banner-text">
        <h2>What would you like to do today?</h2>
        <p>You are logged in as <strong><?= ucfirst($role) ?></strong>. You can enter marks for your assigned students or review submitted results.</p>
    </div>
    <?php if ($pending > 0): ?>
    <div class="banner-alert">
        <span class="banner-alert-icon">⚠️</span>
        <span><strong><?= $pending ?></strong> student<?= $pending > 1 ? 's' : '' ?> still awaiting your marks</span>
    </div>
    <?php else: ?>
    <div class="banner-alert banner-alert-green">
        <span class="banner-alert-icon">✅</span>
        <span>All assigned students have been marked</span>
    </div>
    <?php endif; ?>
</div>

<!-- ── Action cards ── -->
<div class="action-grid">
    <a href="marks.php" class="action-card action-blue">
        <div class="action-icon">✏️</div>
        <div class="action-content">
            <div class="action-title">Enter / Edit Marks</div>
            <div class="action-desc">Select a student and fill in scores for each assessment component</div>
        </div>
        <?php if ($pending > 0): ?>
        <div class="action-count action-count-red"><?= $pending ?> pending</div>
        <?php endif; ?>
        <div class="action-arrow">→</div>
    </a>

    <a href="view_results.php" class="action-card action-green">
        <div class="action-icon">📊</div>
        <div class="action-content">
            <div class="action-title">View Results</div>
            <div class="action-desc">Review all submitted assessments and detailed mark breakdowns</div>
        </div>
        <div class="action-count"><?= $totalMarked ?> done</div>
        <div class="action-arrow">→</div>
    </a>
</div>

<!-- ── Progress strip ── -->
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

<!-- ── Stats ── -->
<div class="stats-grid">
    <div class="stat-box">
        <div class="stat-num"><?= $totalAssigned ?></div>
        <div class="stat-label">Assigned to Me</div>
    </div>
    <div class="stat-box">
        <div class="stat-num"><?= $totalMarked ?></div>
        <div class="stat-label">Marked</div>
    </div>
    <div class="stat-box">
        <div class="stat-num" style="color:<?= $pending > 0 ? 'var(--danger)' : 'var(--success)' ?>"><?= $pending ?></div>
        <div class="stat-label">Pending</div>
    </div>
</div>

</div><!-- page-body -->
<?php include "footer.php"; ?>
