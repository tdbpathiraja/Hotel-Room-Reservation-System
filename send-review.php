<?php
include 'assets/database-functions/dbconnection.php';

?>
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

    <style>
        /* Luxurious Hotel Review Form Styles */
.send-review {
    padding: 60px 0;
    background: linear-gradient(135deg, #f6f6f6 0%, #ffffff 100%);
}

.review-form-container {
    max-width: 600px;
    margin: 0 auto;
    background-color: #ffffff;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 40px;
    position: relative;
    overflow: hidden;
}

.review-form-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #ffd700, #ff9900);
}

.review-section {
    margin-bottom: 30px;
}

.review-label {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
    display: block;
    font-family: 'Playfair Display', serif;
}

.review-input,
.review-select,
.review-textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 16px;
    color: #333;
    transition: all 0.3s ease;
    background-color: #f9f9f9;
}

.review-input:focus,
.review-select:focus,
.review-textarea:focus {
    border-color: #ffd700;
    box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2);
    outline: none;
    background-color: #ffffff;
}

.review-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 15px center;
    padding-right: 35px;
}

.star-rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-start;
    gap: 10px;
}

.star-rating input {
    display: none;
}

.star-rating label {
    font-size: 35px;
    color: #e0e0e0;
    cursor: pointer;
    transition: color 0.3s ease;
}

.star-rating label:before {
    content: '\2605';
}

.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label {
    color: #ffd700;
}

.review-submit-button {
    background: linear-gradient(90deg, #ffd700, #ff9900);
    color: #fff;
    padding: 15px 30px;
    border: none;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 18px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
    display: block;
    margin: 30px auto 0;
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}

.review-submit-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4);
}

.countdown-timer {
    font-size: 16px;
    color: #666;
    text-align: center;
    margin-top: 20px;
    font-style: italic;
}

@media (max-width: 768px) {
    .review-form-container {
        padding: 30px 20px;
    }

    .review-label {
        font-size: 16px;
    }

    .review-input,
    .review-select,
    .review-textarea {
        font-size: 14px;
    }

    .star-rating label {
        font-size: 30px;
    }

    .review-submit-button {
        font-size: 16px;
        padding: 12px 25px;
    }
}


@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.review-section {
    animation: fadeInUp 0.5s ease-out forwards;
    opacity: 0;
}

.review-section:nth-child(1) { animation-delay: 0.1s; }
.review-section:nth-child(2) { animation-delay: 0.2s; }
.review-section:nth-child(3) { animation-delay: 0.3s; }
.review-section:nth-child(4) { animation-delay: 0.4s; }
.review-section:nth-child(5) { animation-delay: 0.5s; }
    </style>
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

    <!--=========== Review Us Breadcrumb ===========-->
    <div class="breadcrumb-section">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <div class="breadcrumb-text">
              <h2>Review Portal</h2>
              <div class="bt-option">
                <a href="/">Home</a>
                <span>Review Us</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <section class="send-review">
  <div class="container">
    <div class="review-form-container">
      <!-- Countdown Timer -->
      <div class="review-section">
        <div id="timer" class="countdown-timer">02:00</div>
      </div>

      <form
        id="reviewForm"
        action="assets/database-functions/visitor/submit-outside-visitor-review.php"
        method="POST"
      >
        <!-- Name and Date Section -->
        <div class="review-section">
          <label for="name" class="review-label">Name:</label>
          <input
            type="text"
            id="name"
            name="name"
            class="review-input"
            required
          />
        </div>

        <div class="review-section">
          <label for="date" class="review-label">Date:</label>
          <input
            type="text"
            id="date"
            name="review_date"
            class="review-input"
            value="<?php echo date('Y-m-d'); ?>"
            readonly
          />
        </div>

        <!-- Room Selection Dropdown -->
        <div class="review-section">
          <label for="room_name" class="review-label">Room that you stayed:</label>
          <select id="room_name" name="room_name" class="review-select" required>
            <?php
// PHP to Fetch Room Names from roomdetails table
$result = mysqli_query($conn, "SELECT RoomName FROM roomdetails");
while ($row = mysqli_fetch_assoc($result)) {
    echo '<option value="' . htmlspecialchars($row['RoomName']) . '">' . htmlspecialchars($row['RoomName']) . '</option>';
}
?>
          </select>
        </div>

        <!-- Star Rating Section -->
        <div class="review-section">
          <label class="review-label">Rating:</label>
          <div class="star-rating">
            <input
              type="radio"
              id="star5"
              name="rating"
              value="5"
              required
            /><label for="star5"></label>
            <input
              type="radio"
              id="star4"
              name="rating"
              value="4"
              required
            /><label for="star4"></label>
            <input
              type="radio"
              id="star3"
              name="rating"
              value="3"
              required
            /><label for="star3"></label>
            <input
              type="radio"
              id="star2"
              name="rating"
              value="2"
              required
            /><label for="star2"></label>
            <input
              type="radio"
              id="star1"
              name="rating"
              value="1"
              required
            /><label for="star1"></label>
          </div>
        </div>


        <div class="review-section">
          <label for="review_text" class="review-label">Your Review:</label>
          <textarea
            id="review_text"
            name="review_text"
            class="review-textarea"
            rows="4"
            required
          ></textarea>
        </div>


        <button type="submit" class="review-submit-button">Submit Review</button>
      </form>
    </div>
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
<script src="https://kit.fontawesome.com/bc9230b11f.js"crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/disable-click.js"></script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
  // Countdown Timer
  let timerDisplay = document.getElementById("timer");
  let timeLeft = 120; // 2 minutes in seconds

  function startTimer() {
    const countdown = setInterval(function () {
      let minutes = Math.floor(timeLeft / 60);
      let seconds = timeLeft % 60;
      seconds = seconds < 10 ? "0" + seconds : seconds;

      timerDisplay.textContent = `${minutes}:${seconds}`;

      timeLeft--;

      if (timeLeft < 0) {
        clearInterval(countdown);
        location.reload(); // Reload the page when the timer ends
      }
    }, 1000);
  }

  startTimer();
});

</script>

</body>
</html>