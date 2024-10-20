<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Varsity Lodge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .thank-you-container {
            max-width: 600px;
            margin: 80px auto;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .thank-you-title {
            color: #d4af37;
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .thank-you-message {
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        .upload-form {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .upload-title {
            margin-bottom: 15px;
            font-weight: bold;
            text-align: center;
            color: #333;
        }
        .file-input {
            margin-bottom: 20px;
        }
        .submit-button {
            background-color: #d4af37;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            width: 100%;
        }
        .submit-button:hover {
            background-color: #c19b2e;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            display: none;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        /* New styles for the loading animation */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
        }
        .loading-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #d4af37;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
    </style>
</head>
<body>

<?php
session_start();
$booking_id = $_GET['booking_id'] ?? '';
?>

<!-- Payment Confirmation Section -->
<div class="container">
    <div class="thank-you-container">
        <h1 class="thank-you-title">Thank You for Your Payment</h1>
        <p class="thank-you-message">We appreciate your business. Please upload your bank transfer slip below to complete your booking.</p>

        <form id="upload-form" class="upload-form" enctype="multipart/form-data">
            <h2 class="upload-title">Upload Bank Transfer Slip</h2>
            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking_id); ?>">
            <small>Don't Upload PDF Formated Files. Only Support <b>JPG / PNG / JPEG</b> formats.</small>
            <div class="mb-3">
                <input type="file" name="payment-slip" class="form-control file-input" required>
            </div>
            <button type="submit" class="btn submit-button">Upload Slip</button>
        </form>

        <div id="message" class="message"></div>
    </div>
</div>

<!-- Loading overlay -->
<div id="loading-overlay" class="loading-overlay">
    <div class="loading-spinner"></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/disable-click.js"></script>

<script>
$(document).ready(function() {
    $('#upload-form').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        // Show loading overlay
        $('#loading-overlay').show();

        $.ajax({
            url: 'assets/database-functions/visitor/submit-advanced-payment.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                console.log("Server Response:", response);
                try {
                    var result = JSON.parse(response); 
                    if (result.success) {
                        alert("Success: " + result.message);
                    } else {
                        alert("Failure: " + result.message);
                    }
                } catch (e) {
                    console.error('Error parsing JSON response:', e, response);  
                    alert('Payment Slip Updated Successfully!!');
                } finally {
                    // Hide loading overlay
                    $('#loading-overlay').hide();
                    window.location.href = 'my-account.php';
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('Payment Slip Updated Successfully!!');
                // Hide loading overlay
                $('#loading-overlay').hide();
                window.location.href = 'my-account.php';
            }
        });
    });
});
</script>

</body>
</html>