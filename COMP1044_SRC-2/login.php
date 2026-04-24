<?php
session_start();
require_once "connection.php";

// If already logged in, redirect to correct dashboard
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') header("Location: admin/dashboard.php");
    elseif ($_SESSION['role'] == 'lecturer') header("Location: assessor/dashboard.php");
    elseif ($_SESSION['role'] == 'supervisor') header("Location: assessor/dashboard.php");
    elseif ($_SESSION['role'] == 'student') header("Location: student/dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role  = $_POST['role'];
    $id    = mysqli_real_escape_string($conn, trim($_POST['user_id']));
    $pass  = $_POST['password'];

    if ($role == "admin") {
        $sql = "SELECT * FROM admin WHERE admin_id = '$id'";
        $res = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($res);
        if ($row && $row['admin_password'] == $pass) {
            $_SESSION['role']    = 'admin';
            $_SESSION['user_id'] = $row['admin_id'];
            $_SESSION['name']    = $row['admin_name'];
            header("Location: admin/dashboard.php");
            exit();
        } else {
            $error = "Invalid admin ID or password.";
        }

    } elseif ($role == "lecturer") {
        $sql = "SELECT * FROM lecturer WHERE lecturer_id = '$id'";
        $res = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($res);
        if ($row && $row['lecturer_password'] == $pass) {
            $_SESSION['role']    = 'lecturer';
            $_SESSION['user_id'] = $row['lecturer_id'];
            $_SESSION['name']    = $row['lecturer_name'];
            header("Location: assessor/dashboard.php");
            exit();
        } else {
            $error = "Invalid lecturer ID or password.";
        }

    } elseif ($role == "supervisor") {
        $sql = "SELECT * FROM supervisor WHERE supervisor_id = '$id'";
        $res = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($res);
        if ($row && $row['supervisor_password'] == $pass) {
            $_SESSION['role']    = 'supervisor';
            $_SESSION['user_id'] = $row['supervisor_id'];
            $_SESSION['name']    = $row['supervisor_name'];
            header("Location: assessor/dashboard.php");
            exit();
        } else {
            $error = "Invalid supervisor ID or password.";
        }

    } elseif ($role == "student") {
        $sql = "SELECT * FROM student WHERE student_id = '$id'";
        $res = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($res);
        if ($row && $row['student_password'] == $pass) {
            $_SESSION['role']    = 'student';
            $_SESSION['user_id'] = $row['student_id'];
            $_SESSION['name']    = $row['student_name'];
            header("Location: student/dashboard.php");
            exit();
        } else {
            $error = "Invalid student ID or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Internship Result Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <div class="login-icon">🎓</div>
            <h1>IRMS</h1>
            <p>Internship Result Management System</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="" id="loginForm">
            <div class="form-group">
                <label for="role">Login As</label>
                <select name="role" id="role" required>
                    <option value="">-- Select Role --</option>
                    <option value="admin">Admin</option>
                    <option value="lecturer">Lecturer</option>
                    <option value="supervisor">Supervisor</option>
                    <option value="student">Student</option>
                </select>
            </div>

            <div class="form-group">
                <label for="user_id">User ID</label>
                <input type="text" name="user_id" id="user_id" placeholder="Enter your ID" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
