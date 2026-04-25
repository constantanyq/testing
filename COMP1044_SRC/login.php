<?php
session_start();
require_once "connection.php";

// ---- REDIRECT IF ALREADY LOGGED IN ----
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } elseif (in_array($_SESSION['role'], ['lecturer','supervisor'])) {
        header("Location: assessor/dashboard.php");
    } elseif ($_SESSION['role'] == 'student') {
        header("Location: student/dashboard.php");
    }
    exit();
}

$error = "";

// ---- LOGIN PROCESSING ----
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id   = mysqli_real_escape_string($conn, trim($_POST['user_id']));
    $pass = $_POST['password'];

    // List of tables and their respective columns/redirects to check
    $checks = [
        ['admin',      'admin_id',      'admin_password',      'admin_name',      'admin/dashboard.php'],
        ['lecturer',   'lecturer_id',   'lecturer_password',   'lecturer_name',   'assessor/dashboard.php'],
        ['supervisor', 'supervisor_id', 'supervisor_password', 'supervisor_name', 'assessor/dashboard.php'],
        ['student',    'student_id',    'student_password',    'student_name',    'student/dashboard.php'],
    ];

    foreach ($checks as [$table, $id_col, $pass_col, $name_col, $redirect]) {
        $res = mysqli_query($conn, "SELECT * FROM `$table` WHERE `$id_col` = '$id'");
        $row = mysqli_fetch_assoc($res);
        
        // Simple plain-text password check (update to password_verify if using hashes)
        if ($row && $row[$pass_col] == $pass) {
            $_SESSION['role']    = ($table === 'admin') ? 'admin' : $table;
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
    <style>
        .login-remember { margin: -6px 0 12px; }
    </style>
</head>
<body class="login-body"> <div class="login-card"> <div class="login-header">
        <div class="login-icon-wrap">🎓</div>
        <h1>IRMS</h1>
        <p>Internship Result Management System</p>
    </div>

    <?php if ($error): ?>
        <div class="login-error-box"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="" autocomplete="on" id="loginForm">
        <div class="login-field">
            <label>User ID</label>
            <input type="text" name="user_id" id="userIdInput"
                   placeholder="Enter your ID"
                   value="<?= htmlspecialchars($_POST['user_id'] ?? '') ?>"
                   autocomplete="username" required>
        </div>
        
        <div class="login-field">
            <label>Password</label>
            <input type="password" name="password" id="passwordInput"
                   placeholder="Enter your password"
                   autocomplete="current-password" required>
        </div>

        <div class="login-remember">
            <label style="display:flex; align-items:center; gap:8px; font-size:0.85rem; cursor:pointer;">
                <input type="checkbox" id="rememberMe" style="width:auto; margin:0;">
                Remember my ID
            </label>
        </div>
        
        <button type="submit" class="btn-login">Login</button>
    </form>

    <script>
    // Remember user ID via localStorage
    const rememberBox = document.getElementById('rememberMe');
    const userIdInput = document.getElementById('userIdInput');

    // On page load: restore saved ID if present
    const savedId = localStorage.getItem('irms_saved_uid');
    if (savedId) {
        userIdInput.value = savedId;
        rememberBox.checked = true;
    }

    document.getElementById('loginForm').addEventListener('submit', function() {
        if (rememberBox.checked) {
            localStorage.setItem('irms_saved_uid', userIdInput.value);
        } else {
            localStorage.removeItem('irms_saved_uid');
        }
    });
    </script>

</div>

</body>
</html>
