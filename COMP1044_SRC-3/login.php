<?php
session_start();
require_once "connection.php";

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin')      header("Location: admin/dashboard.php");
    elseif ($_SESSION['role'] == 'lecturer')   header("Location: assessor/dashboard.php");
    elseif ($_SESSION['role'] == 'supervisor') header("Location: assessor/dashboard.php");
    elseif ($_SESSION['role'] == 'student')    header("Location: student/dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $id   = mysqli_real_escape_string($conn, trim($_POST['user_id']));
    $pass = $_POST['password'];

    if ($role == "admin") {
        $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM admin WHERE admin_id='$id'"));
        if ($row && $row['admin_password'] == $pass) {
            $_SESSION['role'] = 'admin'; $_SESSION['user_id'] = $row['admin_id']; $_SESSION['name'] = $row['admin_name'];
            header("Location: admin/dashboard.php"); exit();
        } else { $error = "Invalid admin ID or password."; }

    } elseif ($role == "lecturer") {
        $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM lecturer WHERE lecturer_id='$id'"));
        if ($row && $row['lecturer_password'] == $pass) {
            $_SESSION['role'] = 'lecturer'; $_SESSION['user_id'] = $row['lecturer_id']; $_SESSION['name'] = $row['lecturer_name'];
            header("Location: assessor/dashboard.php"); exit();
        } else { $error = "Invalid lecturer ID or password."; }

    } elseif ($role == "supervisor") {
        $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM supervisor WHERE supervisor_id='$id'"));
        if ($row && $row['supervisor_password'] == $pass) {
            $_SESSION['role'] = 'supervisor'; $_SESSION['user_id'] = $row['supervisor_id']; $_SESSION['name'] = $row['supervisor_name'];
            header("Location: assessor/dashboard.php"); exit();
        } else { $error = "Invalid supervisor ID or password."; }

    } elseif ($role == "student") {
        $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM student WHERE student_id='$id'"));
        if ($row && $row['student_password'] == $pass) {
            $_SESSION['role'] = 'student'; $_SESSION['user_id'] = $row['student_id']; $_SESSION['name'] = $row['student_name'];
            header("Location: student/dashboard.php"); exit();
        } else { $error = "Invalid student ID or password."; }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – IRMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">

<div class="login-card">
    <div class="login-header">
        <div class="login-icon-wrap">🎓</div>
        <h1>IRMS</h1>
        <p>Internship Result Management System</p>
    </div>

    <?php if ($error): ?>
        <div class="login-error-box">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="login-field">
            <label for="role">Login As</label>
            <select name="role" id="role" required>
                <option value="">-- Select Role --</option>
                <option value="admin"      <?= ($_POST['role']??'')==='admin'      ? 'selected':'' ?>>Admin</option>
                <option value="lecturer"   <?= ($_POST['role']??'')==='lecturer'   ? 'selected':'' ?>>Lecturer</option>
                <option value="supervisor" <?= ($_POST['role']??'')==='supervisor' ? 'selected':'' ?>>Supervisor</option>
                <option value="student"    <?= ($_POST['role']??'')==='student'    ? 'selected':'' ?>>Student</option>
            </select>
        </div>

        <div class="login-field">
            <label for="user_id">User ID</label>
            <input type="text" name="user_id" id="user_id"
                   placeholder="Enter your ID"
                   value="<?= htmlspecialchars($_POST['user_id'] ?? '') ?>" required>
        </div>

        <div class="login-field">
            <label for="password">Password</label>
            <input type="password" name="password" id="password"
                   placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn-login">Login</button>
    </form>
</div>

</body>
</html>
