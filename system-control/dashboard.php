<?php
session_start();

// Define the idle timeout in seconds
$timeout_duration = 15;

// Check if the user has entered the developer password this session
if (!isset($_SESSION['developer_authenticated']) || $_SESSION['developer_authenticated'] !== true) {
    // User hasn't entered the developer password this session, redirect to idle screen
    header('Location: idle.php');
    exit();
}

// Check if the user has been idle
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // User has been idle, redirect to idle screen
    header('Location: idle.php');
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - User Registration and Safe Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .dashboard-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .dashboard-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            color: red;
            text-align: center;
        }
        .form-label {
            font-weight: bold;
            color: #495057;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .nav-tabs .nav-link.active {
            background-color: #007bff;
            color: #fff;
        }
        .nav-tabs .nav-link {
            color: #007bff;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .idle-container {
            display: none;
            text-align: center;
            padding: 20px;
            border: 2px solid #007bff;
            border-radius: 8px;
            background-color: #fff;
        }
        .idle-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="dashboard-title">User Dashboard</div>

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="true">Register User</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="safe-password-tab" data-bs-toggle="tab" data-bs-target="#safe-password" type="button" role="tab" aria-controls="safe-password" aria-selected="false">Create Safe Password</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <!-- Register User Section -->
        <div class="tab-pane fade show active" id="register" role="tabpanel" aria-labelledby="register-tab">
            <form id="registerForm" method="POST" action="register-superadmin.php">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
            <button id="deleteSuperAdminBtn" class="btn btn-danger mt-3">Delete Super Admin</button>
        </div>

        <!-- Safe Password Section -->
        <div class="tab-pane fade" id="safe-password" role="tabpanel" aria-labelledby="safe-password-tab">
            <form id="safePasswordForm" method="POST" action="register-safepassword.php">
                <div class="form-group">
                    <label for="safePassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="safePassword" name="safePassword" required>
                </div>
                <div class="form-group">
                    <label for="safeConfirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="safeConfirmPassword" name="safeConfirmPassword" required>
                </div>
                <button type="submit" class="btn btn-primary">Create Safe Password</button>
            </form>
            <button id="deleteSafePasswordBtn" class="btn btn-danger mt-3">Delete Safe Password</button>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript for Idle Detection and Persistent Idle Screen -->
<script>
     let idleTime = 0;
    const timeoutDuration = 15; // 15 seconds

    // Increment the idle time counter every second
    setInterval(timerIncrement, 1000);

    // Reset idle timer on mouse movement or keypress
    document.addEventListener('mousemove', resetTimer);
    document.addEventListener('keypress', resetTimer);

    function timerIncrement() {
        idleTime++;
        if (idleTime >= timeoutDuration) { // 15 seconds
            window.location.href = 'idle.php'; // Redirect to idle page
        }
    }

    function resetTimer() {
        idleTime = 0;
    }

    document.getElementById('deleteSuperAdminBtn').addEventListener('click', function() {
        if (confirm('Are you sure you want to delete all Super Admins?')) {
            window.location.href = 'delete-superadmin.php';
        }
    });

    document.getElementById('deleteSafePasswordBtn').addEventListener('click', function() {
        if (confirm('Are you sure you want to delete all Safe Passwords?')) {
            window.location.href = 'delete-safepassword.php';
        }
    });
</script>

</body>
</html>
