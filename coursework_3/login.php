<?php
session_start();
require_once "connection.php";

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin')          header("Location: admin/dashboard.php");
    elseif (in_array($_SESSION['role'], ['lecturer','supervisor'])) header("Location: assessor/dashboard.php");
    elseif ($_SESSION['role'] == 'student')    header("Location: student/dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id   = mysqli_real_escape_string($conn, trim($_POST['user_id']));
    $pass = $_POST['password'];

    $checks = [
        ['admin',      'admin_id',      'admin_password',      'admin_name',      'admin/dashboard.php'],
        ['lecturer',   'lecturer_id',   'lecturer_password',   'lecturer_name',   'assessor/dashboard.php'],
        ['supervisor', 'supervisor_id', 'supervisor_password', 'supervisor_name', 'assessor/dashboard.php'],
        ['student',    'student_id',    'student_password',    'student_name',    'student/dashboard.php'],
    ];

    foreach ($checks as [$table, $id_col, $pass_col, $name_col, $redirect]) {
        $res = mysqli_query($conn, "SELECT * FROM `$table` WHERE `$id_col` = '$id'");
        $row = mysqli_fetch_assoc($res);
        if ($row && $row[$pass_col] == $pass) {
            $_SESSION['role']    = $table === 'admin' ? 'admin' : $table;
            $_SESSION['user_id'] = $row[$id_col];
            $_SESSION['name']    = $row[$name_col];
            header("Location: $redirect");
            exit();
        }
    }
    $error = "Invalid ID or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternTrack — Sign In</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

<div class="login-box">

    <div class="login-logo">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="52" height="52">
          <circle cx="100" cy="100" r="96" fill="rgba(255,255,255,0.12)"/>
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

    <h1 class="login-title">InternTrack</h1>
    <p class="login-subtitle">Internship Management System</p>

    <?php if ($error): ?>
        <div class="login-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="" autocomplete="off">
        <div class="login-field">
            <label>User ID</label>
            <input type="text" name="user_id"
                   value="<?= htmlspecialchars($_POST['user_id'] ?? '') ?>"
                   autocomplete="off" required>
        </div>
        <div class="login-field">
            <label>Password</label>
            <input type="password" name="password"
                   autocomplete="current-password" required>
        </div>
        <button type="submit" class="login-btn">Sign In</button>
    </form>

</div>

</body>
</html>
