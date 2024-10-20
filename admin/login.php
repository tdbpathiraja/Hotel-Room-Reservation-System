<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f8f9fa;
        }

        .card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 300px;
            padding: 20px;
            border-radius: 15px;
            background: #e3e3e3;
            box-shadow: 16px 16px 32px #c8c8c8, -16px -16px 32px #fefefe;
            border-radius: 8px;
        }

        .singup {
            color: #000;
            text-transform: uppercase;
            letter-spacing: 2px;
            display: block;
            font-weight: bold;
            font-size: x-large;
            margin-bottom: 1.5em;
        }

        .inputBox {
            position: relative;
            width: 100%;
            margin-bottom: 3em;
        }

        .inputBox input {
            width: 100%;
            padding: 10px;
            outline: none;
            border: none;
            color: #000;
            font-size: 1em;
            background: transparent;
            border-left: 2px solid #000;
            border-bottom: 2px solid #000;
            transition: 0.1s;
            border-bottom-left-radius: 8px;
        }

        .inputBox span {
            position: absolute;
            left: 0;
            transform: translateY(-4px);
            margin-left: 10px;
            padding: 10px;
            pointer-events: none;
            font-size: 12px;
            color: #000;
            text-transform: uppercase;
            transition: 0.5s;
            letter-spacing: 3px;
            border-radius: 8px;
        }

        .inputBox input:valid ~ span,
        .inputBox input:focus ~ span {
            transform: translateX(0) translateY(-15px);
            font-size: 0.8em;
            padding: 5px 10px;
            background: #000;
            letter-spacing: 0.2em;
            color: #fff;
        }

        .enter {
            height: 45px;
            width: 100%;
            border-radius: 5px;
            border: 2px solid #000;
            cursor: pointer;
            background-color: transparent;
            transition: 0.5s;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 2px;
            color: #000;
        }

        .enter:hover {
            background-color: rgb(0, 0, 0);
            color: white;
        }

        .feedback {
            margin-top: 1em;
            font-weight: bold;
        }

        .feedback.error {
            color: red;
        }

        .feedback.success {
            color: green;
        }

        .hidden {
            display: none;
        }

        #countdown {
            font-size: 1.2em;
            font-weight: bold;
            margin-top: 1em;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <a class="singup">Signin</a>

        <form id="loginForm" action="../assets/database-functions/admin/login.php" method="post">
            <div class="inputBox">
                <input type="text" name="username" required>
                <span>Username</span>
            </div>

            <div class="inputBox">
                <input type="password" name="password" required>
                <span>Password</span>
            </div>

            <button type="submit" class="enter">Enter</button>
        </form>

        <div id="feedback" class="feedback"></div>
        <div id="countdown" class="hidden"></div>
    </div>
</div>

<script src="../assets/js/disable-click.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const feedbackElement = document.getElementById("feedback");
        const formElement = document.getElementById("loginForm");
        const countdownElement = document.getElementById("countdown");
        
        
        const lockoutTime = localStorage.getItem("lockoutTime");
        if (lockoutTime) {
            const currentTime = new Date().getTime();
            const lockoutEndTime = new Date(lockoutTime).getTime();
            
            if (currentTime < lockoutEndTime) {
                const remainingTime = lockoutEndTime - currentTime;
                countdownElement.classList.remove("hidden");
                formElement.classList.add("hidden");
                updateCountdown(remainingTime);

                const timerInterval = setInterval(() => {
                    const now = new Date().getTime();
                    const distance = lockoutEndTime - now;

                    if (distance <= 0) {
                        clearInterval(timerInterval);
                        localStorage.removeItem("lockoutTime");
                        countdownElement.classList.add("hidden");
                        formElement.classList.remove("hidden");
                        feedbackElement.textContent = "You can now attempt to log in again.";
                        feedbackElement.classList.remove("error");
                    } else {
                        updateCountdown(distance);
                    }
                }, 1000);
            }
        }

        formElement.addEventListener("submit", function(event) {
            event.preventDefault();
            
            const xhr = new XMLHttpRequest();
            xhr.open("POST", formElement.action);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                const response = JSON.parse(xhr.responseText);
                feedbackElement.textContent = response.message;
                feedbackElement.classList.remove("error", "success");
                
                if (response.status === 'locked') {
                    feedbackElement.classList.add("error");
                    
                    
                    const currentTime = new Date().getTime();
                    const lockoutEndTime = new Date(currentTime + 10 * 60000);
                    localStorage.setItem("lockoutTime", lockoutEndTime);
                    
                    
                    const remainingTime = lockoutEndTime - currentTime;
                    countdownElement.classList.remove("hidden");
                    formElement.classList.add("hidden");
                    updateCountdown(remainingTime);

                    const timerInterval = setInterval(() => {
                        const now = new Date().getTime();
                        const distance = lockoutEndTime - now;

                        if (distance <= 0) {
                            clearInterval(timerInterval);
                            localStorage.removeItem("lockoutTime");
                            countdownElement.classList.add("hidden");
                            formElement.classList.remove("hidden");
                            feedbackElement.textContent = "You can now attempt to log in again.";
                            feedbackElement.classList.remove("error");
                        } else {
                            updateCountdown(distance);
                        }
                    }, 1000);
                } else if (response.status === 'success') {
                    feedbackElement.classList.add("success");
                    feedbackElement.textContent = response.message;
                    if (response.redirect) {
                        window.location.href = response.redirect;  
                    }
                } else {
                    feedbackElement.classList.add("error");
                }
            };
            xhr.send(new URLSearchParams(new FormData(formElement)).toString());
        });

        function updateCountdown(distance) {
            const minutes = Math.floor((distance % (1000 * 3600)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            countdownElement.textContent = `Retry in ${minutes}m ${seconds}s`;
        }
    });
</script>


</body>
</html>
