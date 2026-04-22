<?php
// Protect page – only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' – IRMS' : 'Admin – IRMS' ?></title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <h2>🎓 IRMS</h2>
        <p>Admin Panel</p>
    </div>
    <div class="sidebar-user">
        <span>Logged in as</span>
        <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>
    </div>
    <nav>
        <a href="dashboard.php"   class="<?= basename($_SERVER['PHP_SELF'])=='dashboard.php' ? 'active' : '' ?>">
            <span class="nav-icon">🏠</span> Dashboard
        </a>
        <a href="students.php"    class="<?= basename($_SERVER['PHP_SELF'])=='students.php' ? 'active' : '' ?>">
            <span class="nav-icon">👨‍🎓</span> Students
        </a>
        <a href="internships.php" class="<?= basename($_SERVER['PHP_SELF'])=='internships.php' ? 'active' : '' ?>">
            <span class="nav-icon">🏢</span> Internships
        </a>
        <a href="users.php"       class="<?= basename($_SERVER['PHP_SELF'])=='users.php' ? 'active' : '' ?>">
            <span class="nav-icon">👥</span> Users
        </a>
        <a href="results.php"     class="<?= basename($_SERVER['PHP_SELF'])=='results.php' ? 'active' : '' ?>">
            <span class="nav-icon">📊</span> Results
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="../logout.php">🚪 Logout</a>
    </div>
</aside>

<!-- Main content starts -->
<div class="main-content">
