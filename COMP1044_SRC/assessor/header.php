<?php
// Protect (only lecturer or supervisor)
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['lecturer', 'supervisor'])) {
    header("Location: ../login.php");
    exit();
}
$assessorRole = ucfirst($_SESSION['role']); // "Lecturer" or "Supervisor"
$initials = strtoupper(substr($_SESSION['name'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' – IRMS' : 'Assessor – IRMS' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <div class="layout">

        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon">🎓</div>
                <div class="brand-text">
                    <h2>IRMS</h2>
                    <p><?= $assessorRole ?> Panel</p>
                </div>
            </div>
            <div class="sidebar-user">
                <div class="avatar"><?= htmlspecialchars($initials) ?></div>
                <div class="user-info">
                    <span>Logged in as <?= $assessorRole ?></span>
                    <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>
                </div>
            </div>
            <nav>
                <div class="nav-section-label">Main Menu</div>
                <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                    <span class="nav-icon">🏠</span> Dashboard
                </a>
                <a href="marks.php" class="<?= basename($_SERVER['PHP_SELF']) == 'marks.php' ? 'active' : '' ?>">
                    <span class="nav-icon">✏️</span> Enter Marks
                </a>
                <a href="view_results.php"
                    class="<?= basename($_SERVER['PHP_SELF']) == 'view_results.php' ? 'active' : '' ?>">
                    <span class="nav-icon">📊</span> View Results
                </a>
            </nav>
            <div class="sidebar-footer">
                <a href="../logout.php">🚪 Logout</a>
            </div>
        </aside>

        <div class="main-content">