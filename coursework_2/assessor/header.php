<?php
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['lecturer','supervisor'])) {
    header("Location: ../login.php");
    exit();
}
$assessorRole = ucfirst($_SESSION['role']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' – InternTrack' : 'Assessor – InternTrack' ?></title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-logo">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="24" height="24">
              <defs><linearGradient id="sl" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#1e3a5f"/><stop offset="100%" style="stop-color:#2563eb"/></linearGradient></defs>
              <circle cx="100" cy="100" r="96" fill="url(#sl)"/>
              <ellipse cx="100" cy="118" rx="28" ry="20" fill="#f0f9ff" opacity="0.9"/>
              <circle cx="100" cy="88" r="16" fill="#f0f9ff" opacity="0.9"/>
              <rect x="76" y="69" width="48" height="7" rx="2" fill="#1e3a5f"/>
              <polygon points="100,58 130,69 100,80 70,69" fill="#1e3a5f"/>
              <line x1="116" y1="69" x2="122" y2="82" stroke="#fbbf24" stroke-width="2.5"/>
              <circle cx="122" cy="84" r="2.5" fill="#fbbf24"/>
              <path d="M66,132 Q83,128 100,132 L100,155 Q83,151 66,155 Z" fill="#bfdbfe"/>
              <path d="M100,132 Q117,128 134,132 L134,155 Q117,151 100,155 Z" fill="#bfdbfe" opacity="0.8"/>
              <rect x="98" y="132" width="4" height="23" fill="#93c5fd"/>
            </svg>
        </div>
        <div class="sidebar-brand-text">
            <h2>InternTrack</h2>
            <p><?= $assessorRole ?> Panel</p>
        </div>
    </div>
    <div class="sidebar-user">
        <span>Logged in as <?= $assessorRole ?></span>
        <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>
    </div>
    <nav>
        <a href="dashboard.php"    class="<?= basename($_SERVER['PHP_SELF'])=='dashboard.php'    ? 'active' : '' ?>">
            <span class="nav-icon">🏠</span> Dashboard
        </a>
        <a href="marks.php"        class="<?= basename($_SERVER['PHP_SELF'])=='marks.php'        ? 'active' : '' ?>">
            <span class="nav-icon">✏️</span> Enter Marks
        </a>
        <a href="view_results.php" class="<?= basename($_SERVER['PHP_SELF'])=='view_results.php' ? 'active' : '' ?>">
            <span class="nav-icon">📊</span> View Results
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="../logout.php">🚪 Sign Out</a>
    </div>
</aside>

<div class="main-content">
