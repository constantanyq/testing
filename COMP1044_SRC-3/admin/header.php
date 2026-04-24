<?php
// Protect page – only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
$initials = strtoupper(substr($_SESSION['name'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' – IRMS' : 'Admin – IRMS' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">🎓</div>
        <div class="brand-text">
            <h2>IRMS</h2>
            <p>Admin Panel</p>
        </div>
    </div>
    <div class="sidebar-user">
        <div class="avatar"><?= htmlspecialchars($initials) ?></div>
        <div class="user-info">
            <span>Logged in as Admin</span>
            <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>
        </div>
    </div>
    <nav>
        <!-- Dashboard is ABOVE the Management section -->
        <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF'])=='dashboard.php' ? 'active' : '' ?>">
            <span class="nav-icon">🏠</span> Dashboard
        </a>

        <div class="nav-section-label" style="margin-top:12px;">Management</div>
        <a href="students.php" class="<?= basename($_SERVER['PHP_SELF'])=='students.php' ? 'active' : '' ?>">
            <span class="nav-icon">👨‍🎓</span> Students
        </a>
        <a href="internships.php" class="<?= basename($_SERVER['PHP_SELF'])=='internships.php' ? 'active' : '' ?>">
            <span class="nav-icon">🏢</span> Internships
        </a>
        <a href="users.php" class="<?= basename($_SERVER['PHP_SELF'])=='users.php' ? 'active' : '' ?>">
            <span class="nav-icon">👥</span> Users
        </a>
        <a href="results.php" class="<?= basename($_SERVER['PHP_SELF'])=='results.php' ? 'active' : '' ?>">
            <span class="nav-icon">📊</span> Results
        </a>
        <a href="print_report.php" class="<?= basename($_SERVER['PHP_SELF'])=='print_report.php' ? 'active' : '' ?>">
            <span class="nav-icon">🖨️</span> Print Report
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="../logout.php">🚪 Logout</a>
    </div>
</aside>

<!-- Main content starts -->
<div class="main-content">
