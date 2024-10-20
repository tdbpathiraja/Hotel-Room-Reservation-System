<?php
session_start();

$developerPassword = "daviqinnovationaccess";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredPassword = $_POST['developerPassword'];
    if ($enteredPassword === $developerPassword) {
        
        $_SESSION['developer_authenticated'] = true;
        $_SESSION['last_activity'] = time(); 
        header('Location: dashboard.php');
        exit();
    } else {
        
        $error = "Incorrect password. Please try again.";
    }
}
?>

<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Idle Screen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .idle-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 2px solid #007bff;
            border-radius: 8px;
            background-color: #fff;
            text-align: center;
        }
        .idle-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
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
    </style>
</head>
<body>

<div class="idle-container">
    <div class="idle-title">Authentication Required</div>
    <p>Please enter the developer password to access the dashboard:</p>
    <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
    <form method="POST">
        <div class="form-group">
            <label for="developerPassword" class="form-label">Developer Password</label>
            <input type="password" class="form-control" id="developerPassword" name="developerPassword" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Submit</button>
    </form>
</div>

</body>
</html>