<?php
session_start();
require_once "connection.php";

// Already logged in
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin')            header("Location: admin/dashboard.php");
    elseif ($_SESSION['role'] == 'lecturer')     header("Location: assessor/dashboard.php");
    elseif ($_SESSION['role'] == 'supervisor')   header("Location: assessor/dashboard.php");
    elseif ($_SESSION['role'] == 'student')      header("Location: student/dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id   = mysqli_real_escape_string($conn, trim($_POST['user_id']));
    $pass = $_POST['password'];

    // Auto-detect role – try each table in priority order
    $checks = [
        ['admin',      'admin_id',      'admin_password',      'admin_name',      'admin/dashboard.php'],
        ['lecturer',   'lecturer_id',   'lecturer_password',   'lecturer_name',   'assessor/dashboard.php'],
        ['supervisor', 'supervisor_id', 'supervisor_password', 'supervisor_name', 'assessor/dashboard.php'],
        ['student',    'student_id',    'student_password',    'student_name',    'student/dashboard.php'],
    ];

    $matched = false;
    foreach ($checks as [$table, $id_col, $pass_col, $name_col, $redirect]) {
        $res = mysqli_query($conn, "SELECT * FROM `$table` WHERE `$id_col` = '$id'");
        $row = mysqli_fetch_assoc($res);
        if ($row && $row[$pass_col] == $pass) {
            $role = ($table === 'lecturer' || $table === 'supervisor') ? $table : $table;
            $_SESSION['role']    = $role;
            $_SESSION['user_id'] = $row[$id_col];
            $_SESSION['name']    = $row[$name_col];
            header("Location: $redirect");
            exit();
        }
    }
    if (!$matched) {
        $error = "Invalid credentials. Please check your ID and password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In – InternTrack</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

<!-- LEFT: Brand panel -->
<div class="login-brand-panel">
    <div class="brand-logo-wrap">
        <!-- InternTrack Logo -->
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="68" height="68">
          <defs>
            <linearGradient id="bk" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" style="stop-color:#1e3a5f"/>
              <stop offset="100%" style="stop-color:#2563eb"/>
            </linearGradient>
          </defs>
          <circle cx="100" cy="100" r="96" fill="url(#bk)"/>
          <circle cx="100" cy="100" r="96" fill="none" stroke="#60a5fa" stroke-width="2" opacity="0.4"/>
          <ellipse cx="100" cy="118" rx="28" ry="20" fill="#f0f9ff" opacity="0.9"/>
          <circle cx="100" cy="88" r="16" fill="#f0f9ff" opacity="0.9"/>
          <rect x="76" y="69" width="48" height="7" rx="2" fill="#1e3a5f"/>
          <polygon points="100,58 130,69 100,80 70,69" fill="#1e3a5f"/>
          <line x1="116" y1="69" x2="122" y2="82" stroke="#fbbf24" stroke-width="2.5"/>
          <circle cx="122" cy="84" r="2.5" fill="#fbbf24"/>
          <path d="M66,132 Q83,128 100,132 L100,155 Q83,151 66,155 Z" fill="#bfdbfe"/>
          <path d="M100,132 Q117,128 134,132 L134,155 Q117,151 100,155 Z" fill="#bfdbfe" opacity="0.8"/>
          <rect x="98" y="132" width="4" height="23" fill="#93c5fd"/>
          <line x1="73" y1="139" x2="93" y2="137" stroke="#93c5fd" stroke-width="1.2" opacity="0.7"/>
          <line x1="73" y1="143" x2="93" y2="141" stroke="#93c5fd" stroke-width="1.2" opacity="0.7"/>
          <line x1="73" y1="147" x2="93" y2="145" stroke="#93c5fd" stroke-width="1.2" opacity="0.7"/>
          <line x1="107" y1="137" x2="127" y2="139" stroke="#93c5fd" stroke-width="1.2" opacity="0.7"/>
          <line x1="107" y1="141" x2="127" y2="143" stroke="#93c5fd" stroke-width="1.2" opacity="0.7"/>
          <line x1="107" y1="145" x2="127" y2="147" stroke="#93c5fd" stroke-width="1.2" opacity="0.7"/>
        </svg>
    </div>

    <div class="brand-wordmark">
        <h1>InternTrack</h1>
        <span class="brand-sub">Internship Management</span>
    </div>

    <p class="brand-tagline">
        A unified platform to <strong>evaluate, track and manage</strong> student internship performance across your faculty.
    </p>

    <div class="brand-features">
        <div class="brand-feature">
            <div class="feat-icon">📋</div>
            <span class="feat-text">Structured assessment with standardised weightages</span>
        </div>
        <div class="brand-feature">
            <div class="feat-icon">📊</div>
            <span class="feat-text">Auto-calculated final marks, no spreadsheets needed</span>
        </div>
        <div class="brand-feature">
            <div class="feat-icon">🔒</div>
            <span class="feat-text">Role-based access for admins and assessors</span>
        </div>
    </div>
</div>

<!-- RIGHT: Form panel -->
<div class="login-form-panel">
    <div class="login-form-inner">
        <div class="form-heading">
            <h2>Welcome back</h2>
            <p>Sign in to your InternTrack account to continue.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="user_id">Staff / Student ID</label>
                <input type="text" name="user_id" id="user_id"
                       placeholder="e.g. ADM001 or STU042"
                       value="<?= htmlspecialchars($_POST['user_id'] ?? '') ?>"
                       autocomplete="username" required>
            </div>

            <div class="form-group" style="margin-bottom:24px">
                <label for="password">Password</label>
                <input type="password" name="password" id="password"
                       placeholder="Enter your password"
                       autocomplete="current-password" required>
            </div>

            <button type="submit" class="btn-login">Sign In &rarr;</button>
        </form>

        <p style="margin-top:24px;font-size:12px;color:#94a3b8;text-align:center">
            Forgot your credentials? Contact your faculty administrator.
        </p>
    </div>
</div>

</body>
</html>
