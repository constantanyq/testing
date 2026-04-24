<?php
// Protect – only lecturer or supervisor
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['lecturer','supervisor'])) {
    header("Location: ../login.php");
    exit();
}
$assessorRole = ucfirst($_SESSION['role']); // "Lecturer" or "Supervisor"
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' – IRMS' : 'Assessor – IRMS' ?></title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">

<aside class="sidebar">
    <div class="sidebar-brand">
        <h2>🎓 IRMS</h2>
        <p><?= $assessorRole ?> Panel</p>
    </div>
    <div class="sidebar-user">
        <span>Logged in as <?= $assessorRole ?></span>
        <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>
    </div>
    <nav>
        <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF'])=='dashboard.php' ? 'active' : '' ?>">
            <span class="nav-icon">🏠</span> Dashboard
        </a>
        <a href="marks.php" class="<?= basename($_SERVER['PHP_SELF'])=='marks.php' ? 'active' : '' ?>">
            <span class="nav-icon">✏️</span> Enter Marks
        </a>
        <a href="view_results.php" class="<?= basename($_SERVER['PHP_SELF'])=='view_results.php' ? 'active' : '' ?>">
            <span class="nav-icon">📊</span> View Results
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="../logout.php">🚪 Logout</a>
    </div>
</aside>

<div class="main-content">
