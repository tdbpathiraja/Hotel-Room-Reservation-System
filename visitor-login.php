
<!DOCTYPE html>
<html lang="zxx">
  <head>
    <meta charset="UTF-8" />
    <meta name="description" content="Varsity Lodge" />
    <meta name="keywords" content="Varsity Lodge, Varsity" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Varsity Lodge</title>

    <link
      rel="icon"
      href="assets/img/varsity-lodge-logo.png"
      type="image/x-icon"
    />

    <!-- Google Font -->
    <link
      href="https://fonts.googleapis.com/css?family=Lora:400,700&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700&display=swap"
      rel="stylesheet"
    />

    <!-- Css Styles -->
    <link
      rel="stylesheet"
      href="https://cdn-uicons.flaticon.com/2.5.1/uicons-regular-rounded/css/uicons-regular-rounded.css"
    />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <link
      rel="stylesheet"
      href="assets/css/bootstrap.min.css"
      type="text/css"
    />
    <link
      rel="stylesheet"
      href="assets/css/font-awesome.min.css"
      type="text/css"
    />
    <link
      rel="stylesheet"
      href="assets/css/elegant-icons.css"
      type="text/css"
    />
    <link rel="stylesheet" href="assets/css/flaticon.css" type="text/css" />
    <link
      rel="stylesheet"
      href="assets/css/owl.carousel.min.css"
      type="text/css"
    />
    <link rel="stylesheet" href="assets/css/nice-select.css" type="text/css" />
    <link
      rel="stylesheet"
      href="assets/css/jquery-ui.min.css"
      type="text/css"
    />
    <link
      rel="stylesheet"
      href="assets/css/magnific-popup.css"
      type="text/css"
    />
    <link rel="stylesheet" href="assets/css/slicknav.min.css" type="text/css" />
    <link rel="stylesheet" href="assets/css/style.css" type="text/css" />
  </head>

  <body>
    <!--=========== Pre Loader ===========-->
    <div id="preloder">
      <img
        src="assets/img/Pre-Loder.png"
        alt="Preloader Image"
        class="preloader-image"
      />
      <div class="loader"></div>
    </div>

    <!--=========== Navigation Bar ===========-->
    <?php include 'nav-bar.php';?>


    <!--=========== Login and Register Forms ===========-->
    <section class="auth-section">
    <div class="auth-container">

      <!-- login Screen -->
      <div id="loginSection" class="auth-form">
        <h2>Login</h2>
        <div id="login-message" class="form-message"></div>
        <form id="loginForm" method="POST" action="assets/database-functions/visitor/login.php">
          <div class="form-group">
            <label for="loginUsername">Username:</label>
            <input type="text" id="loginUsername" name="username" required />
          </div>
          <div class="form-group">
            <label for="loginPassword">Password:</label>
            <input type="password" id="loginPassword" name="password" required />
          </div>
          <div class="form-group">
            <input type="checkbox" id="rememberMe" name="rememberMe" />
            <label for="rememberMe">Remember Me</label>
          </div>
          <button type="submit" class="btn-login">Login</button>
        </form>
        <p class="signup-link">
          Don't have an account? <a href="javascript:void(0);" onclick="showSignup()">Sign Up</a>
        </p>
      </div>

      <!-- Signup Screen -->
      <div id="signupSection" class="auth-form" style="display: none;">
  <h2>Signup</h2>
  <div id="signup-message" class="form-message"></div>
  <form id="signupForm" method="POST" action="assets/database-functions/visitor/register.php" enctype="multipart/form-data">
    <div class="form-group">
      <label for="fullName">Full Name:</label>
      <input type="text" id="fullName" name="fullName" required />
    </div>
    <div class="form-group">
      <label for="signupUsername">Username:</label>
      <input type="text" id="signupUsername" name="username" required />
    </div>
    <div class="form-group">
      <label for="email">Email Address:</label>
      <input type="email" id="email" name="email" required />
    </div>
    <div class="form-group">
      <label for="password">Password:</label>
      <input type="password" id="signupPassword" name="password" required />
    </div>
    <div class="form-group">
      <label for="confirmPassword">Confirm Password:</label>
      <input type="password" id="confirmPassword" name="confirmPassword" required />
    </div>
    <div class="form-group">
      <label for="birthDate">Birth Date:</label>
      <input type="date" id="birthDate" name="birthDate" required />
    </div>
    <div class="form-group">
      <label for="gender">Gender:</label>
      <select id="gender" name="gender" required>
        <option value="" disabled selected>Select Gender</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="other">Other</option>
      </select>
    </div><br>
    <div class="form-group">
      <label for="profileImage">Profile Image (Optional):</label>
      <input type="file" id="profileImage" name="profileImage" accept="image/*" />
    </div>
    <button type="submit" class="btn-login">Sign Up</button>
  </form>
  <p class="login-link">
    Already have an account? <a href="javascript:void(0);" onclick="showLogin()">Login</a>
  </p>
</div>

  </section>


  <!--=========== Footer ===========-->
  <?php include 'footer.php';?>

    <!-- Js Plugins -->
    <script src="assets/js/jquery-3.3.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <script src="assets/js/jquery.nice-select.min.js"></script>
    <script src="assets/js/jquery-ui.min.js"></script>
    <script src="assets/js/jquery.slicknav.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script
      src="https://kit.fontawesome.com/bc9230b11f.js"
      crossorigin="anonymous"
    ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/disable-click.js"></script>


    <!-- Signin and Signup Function -->
    <script>
function showSignup() {
    document.getElementById('loginSection').style.display = 'none';
    document.getElementById('signupSection').style.display = 'block';
  }

  function showLogin() {
    document.getElementById('signupSection').style.display = 'none';
    document.getElementById('loginSection').style.display = 'block';
  }

  document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);

    fetch('assets/database-functions/visitor/login.php', {
      method: 'POST',
      body: formData
    }).then(response => response.json()).then(data => {
      var messageElement = document.getElementById('login-message');
      messageElement.innerHTML = data.message;
      messageElement.className = data.status;

      if (data.status === 'success') {
        setTimeout(function() {
          window.location.href = 'index.php';
        }, 5000);
      }
    });
  });

  document.getElementById('signupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);

    fetch('assets/database-functions/visitor/register.php', {
      method: 'POST',
      body: formData
    }).then(response => response.text()).then(data => {
      document.getElementById('signup-message').innerHTML = data;
    });
  });
</script>

  </body>
</html>
