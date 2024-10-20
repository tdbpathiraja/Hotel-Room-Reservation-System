<?php
session_start();

include "assets/database-functions/dbconnection.php";

$username = "";
$fullName = "";
$telephone = "";

$rooms = [];
$roomDetails = [];

$isLoggedIn = isset($_SESSION["username"]) || isset($_COOKIE["username"]);

if ($isLoggedIn) {
    $username = $_SESSION["username"] ?? $_COOKIE["username"];

    $stmt = $conn->prepare(
        "SELECT full_name, email FROM users WHERE username = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($fullName, $email);
    $stmt->fetch();
    $telephone = $email;
    $stmt->close();
}

$conn->close();

// Currency Conversiton API Connection
$api_url =
    "https://v6.exchangerate-api.com/v6/dcc1e5d38b9c1254ecef1b28/latest/USD";
$response = file_get_contents($api_url);
$data = json_decode($response, true);
$exchange_rate = $data["conversion_rates"]["LKR"];

// Booking Form Alerts
if (isset($_SESSION['message'])) {
    $message = htmlspecialchars($_SESSION['message']);
    unset($_SESSION['message']);
} else {
    $message = '';
}
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
    <?php include "nav-bar.php";?>

    <!--=========== Breadcrumb Room Booking ===========-->
    <div class="breadcrumb-section">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <div class="breadcrumb-text">
              <h2>Room Booking Portal</h2>
              <div class="bt-option">
                <a href="/">Home</a>
                <span>Booking</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

   <!--=========== Booking Form ===========-->
<section class="roombooking-form">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php if ($message): ?>
                    <!-- Display Session Message if present -->
                    <div class="alert alert-info" id="session-message">
                        <?php echo $message; ?>
                    </div>
                    <script>
                        setTimeout(function() {
                            window.location.href = 'my-account.php';
                        }, 5000);
                    </script>
                <?php else: ?>
                    <?php if ($isLoggedIn): ?>
                        <h3 class="text-center">Booking Form</h3>

                        <!-- Timer Display -->
                        <div id="timer" class="text-center mb-4">
                            <h5>Time remaining: <span id="timer-display">05:00</span></h5>
                        </div>

                        <form id="booking-form" action="assets/database-functions/room-booking/general/save_booking.php" method="post">

                            <!-- Personal Information Section -->
                            <div class="personal-info mb-4">
                                <h4>Personal Information</h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="username">Username</label>
                                            <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" readonly />
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($fullName); ?>" readonly />
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="email">Email Address</label>
                                            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($telephone); ?>" readonly />
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="telephone">Mobile Number <span style="color: red;">*</span></label>
                                            <input type="tel" id="telephone" name="telephone" placeholder="+94766273376" class="form-control" required />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Room Booking Information -->
                            <div class="booking-details mb-4">
                                <h4>Booking Room Details</h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="check-in">Check-In Date<span style="color: red;">*</span></label>
                                            <input type="date" id="check-in" name="check-in" class="form-control" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="check-out">Check-Out Date<span style="color: red;">*</span></label>
                                            <input type="date" id="check-out" name="check-out" class="form-control" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="adult-count">Adult Count<span style="color: red;">*</span></label>
                                            <input type="number" id="adult-count" name="adult-count" class="form-control" min="1" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="child-count">Child Count</label>
                                            <input type="number" id="child-count" name="child-count" class="form-control" min="0" />
                                        </div>
                                        <small id="child-count-message" class="form-text text-muted mt-2" style="display: none;">
                                            Children must be under 12 years old to be eligible for the child rate.
                                        </small>
                                    </div>
                                </div>

                                <!-- Check Availability Button -->
                                <div class="check-availability mb-4">
                                    <button type="button" id="check-availability" class="btn btn-primary">Check Availability</button>
                                </div>

                                <!-- Available Rooms Section -->
                                <div id="available-rooms" class="available-rooms mb-4" style="display: none;">
                                    <h4>Available Rooms</h4>
                                    <div class="row" id="rooms-list"></div>
                                    <button type="button" id="reset-button" class="btn btn-light">
                                        <i class="fa fa-refresh" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Pricing Details Section -->
<div id="pricing-details" class="pricing-details mb-4" style="display: none;">
    <h4>Room Price Details</h4>
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="price-column">
                <h5>LKR Prices</h5>
                <p><span>Nightly Price (Before Tax):</span> <span id="nightly-price-before-tax-lkr">0.00</span></p>
                <p><span>Tax (3%):</span> <span id="tax-amount-lkr">0.00</span></p>
                <p><span>Nightly Price (After Tax):</span> <span id="nightly-price-after-tax-lkr">0.00</span></p>
                <p><span>Total Price (After Tax):</span> <span id="total-price-after-tax-lkr">0.00</span></p>
                <p><span>Advance Payment (50%):</span> <span id="advance-payment-lkr">0.00</span></p>
                <input type="hidden" id="advance-payment" name="advance_payment" value="0.00">
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="price-column">
                <h5>USD Prices</h5>
                <p><span>Nightly Price (Before Tax):</span> <span id="nightly-price-before-tax-usd">0.00</span></p>
                <p><span>Tax (3%):</span> <span id="tax-amount-usd">0.00</span></p>
                <p><span>Nightly Price (After Tax):</span> <span id="nightly-price-after-tax-usd">0.00</span></p>
                <p><span>Total Price (After Tax):</span> <span id="total-price-after-tax-usd">0.00</span></p>
                <p><span>Advance Payment (50%):</span> <span id="advance-payment-usd">0.00</span></p>
            </div>
        </div>
    </div>
</div>


                            <!-- Additional Details Section -->
                            <div id="additional-details" style="display: none;">
                                <div class="payment-method mb-4">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="payment-method">Preferred Payment Method<span style="color: red;">*</span></label>
                                                <select id="payment-method" name="payment-method" class="form-control" required>
                                                    <option value="">Select Payment Method</option>
                                                    <option value="bank">Bank Transfer</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="special-notes">Special Notes (Optional)</label>
                                                <textarea id="special-notes" name="special-notes" class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Meal Plan Selection -->
                                <div class="meal-plan mb-4">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="meal-plan">Meal Plan<span style="color: red;">*</span></label>
                                                <select id="meal-plan" name="meal-plan" class="form-control" required>
                                                    <option value="">Select Meal Plan</option>
                                                    <option value="half-board">Half Board (Breakfast and Dinner)</option>
                                                    <option value="full-board">Full Board (All Meals Included)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Acceptance Checkbox -->
                                <div class="form-group form-check mb-4">
                                    <input type="checkbox" id="accept-terms" name="accept-terms" class="form-check-input" required />
                                    <label for="accept-terms" class="form-check-label">
                                        I accept the <a href="refund-policies.php">refund policies</a>, <a href="terms-and-conditions.php">terms and conditions</a>, and <a href="privacy-policies.php">privacy policies</a>. I understand that if the advance payment is not made at least 07 days before the check-in date, my booking will be canceled.
                                    </label>
                                </div>

                                <button type="submit" id="submit-booking" class="btn btn-primary" disabled>Submit Booking</button>
                            </div>
                        </form>

                    <?php else: ?>
                        <p>You need to sign in to book a room. <a href="visitor-login.php">Sign In</a></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>



<!--=========== Footer ===========-->
<?php include "footer.php";?>


<!-- Js Plugins -->
<script src="assets/js/jquery-3.3.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.magnific-popup.min.js"></script>
<script src="assets/js/jquery.nice-select.min.js"></script>
<script src="assets/js/jquery-ui.min.js"></script>
<script src="assets/js/jquery.slicknav.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>
<script src="https://kit.fontawesome.com/bc9230b11f.js" crossorigin="anonymous"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/disable-click.js"></script>


<script>
// Timer Functionality
let timeLeft = 300;

function updateTimerDisplay() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    document.getElementById('timer-display').textContent =
        (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
}

function startTimer() {
    const timerInterval = setInterval(function() {
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            location.reload();
        } else {
            timeLeft--;
            updateTimerDisplay();
        }
    }, 1000);
}

updateTimerDisplay();
startTimer();

$('#check-availability').on('click', function() {
    const checkInDate = $('#check-in').val();
    const checkOutDate = $('#check-out').val();
    const adultCount = parseInt($('#adult-count').val());
    const childCount = parseInt($('#child-count').val());

    if (checkInDate && checkOutDate && adultCount) {
        // Disable input fields after checking availability only if rooms are found
        $('#check-in').prop('readonly', true);
        $('#check-out').prop('readonly', true);
        $('#adult-count').prop('readonly', true);
        $('#child-count').prop('readonly', true);

        $.ajax({
            url: 'assets/database-functions/room-booking/general/check_availability.php',
            type: 'POST',
            data: {
                check_in: checkInDate,
                check_out: checkOutDate,
                adult_count: adultCount,
                child_count: childCount
            },
            success: function(response) {
                const data = JSON.parse(response);
                const rooms = data.availableRooms;
                const suggestions = data.suggestions;
                let roomsHtml = '';

                if (rooms.length > 0) {
                    // Populate available rooms section
                    rooms.forEach(room => {
                        roomsHtml += `
                            <div class="col-md-6 mb-4">
                                <div class="room-card">
                                    <img src="assets/img/gallery/rooms/${room.RoomCardImg}" alt="${room.RoomName}" class="room-image">
                                    <div class="room-details">
                                        <div>
                                            <h5 class="room-name" onclick="redirectToRoomDetails(${room.RoomID})" style="cursor: pointer; text-decoration: none;">
                                                ${room.RoomName}
                                            </h5>
                                            <p class="room-info">Can Stay ${room.Capacity} persons</p>
                                            <p class="room-info">Adults: ${room.AdultCount}, Children: ${room.ChildCount}</p>
                                            <p class="room-price">Per Night: LKR ${room.PriceLKR} / USD ${(room.PriceLKR / <?php echo $exchange_rate; ?>).toFixed(2)}</p>
                                        </div>
                                        <div class="room-select">
                                            <input type="checkbox" class="room-checkbox" name="room[]" value="${room.RoomID}" data-price="${room.PriceLKR}" id="room-${room.RoomID}">
                                            <label for="room-${room.RoomID}">Click here to select room</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    $('#rooms-list').html(roomsHtml);
                    $('#available-rooms').show();
                    $('#additional-details').show(); 
                    attachCheckboxEvents();
                } else {
                    let availableRooms = "No rooms available for the selected dates and guest count.";
                    
                    alert(availableRooms);

                    // Clear the input fields for check-in, check-out, adult count, and child count
                    $('#check-in').val('').prop('readonly', false);
                    $('#check-out').val('').prop('readonly', false);
                    $('#adult-count').val('').prop('readonly', false);
                    $('#child-count').val('').prop('readonly', false);
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred while checking availability. Please try again.');
            }
        });
    } else {
        alert('Please fill in all required fields.');
    }
});


// Reset Button Functionality
$('#reset-button').on('click', function() {
    // Clear input fields
    $('#check-in').val('').prop('readonly', false);
    $('#check-out').val('').prop('readonly', false);
    $('#adult-count').val('').prop('readonly', false);
    $('#child-count').val('').prop('readonly', false);

    // Hide available rooms section and clear content
    $('#available-rooms').hide();
    $('#rooms-list').html('');

    // Hide additional details and pricing details sections
    $('#additional-details').hide();
    $('#pricing-details').hide();
});

// Room Details page redirection
function redirectToRoomDetails(roomID) {
    window.open(`room-details.php?id=${roomID}`, '_blank');
}


// Attach Checkbox Events
function attachCheckboxEvents() {
    $('.room-checkbox').on('change', function() {
        updatePricingDetails();
    });
}

// Meal plan prices
const mealPlanPrices = {
    'half-board': { adult: 1500, child: 750 },
    'full-board': { adult: 2500, child: 1250 } 
};

// Function to update the pricing details when room is selected or meal plan is changed
function updatePricingDetails() {
    const checkInDate = new Date($('#check-in').val());
    const checkOutDate = new Date($('#check-out').val());
    const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));

    if (nights > 0) {
        let totalPriceBeforeTaxLKR = 0;

        // Calculate room price for selected rooms
        $('.room-checkbox:checked').each(function () {
            totalPriceBeforeTaxLKR += parseFloat($(this).data('price')) * nights;
        });

        // Get the selected meal plan and calculate meal plan price per day for the total stay
        const selectedMealPlan = $('#meal-plan').val();
        const adultCount = parseInt($('#adult-count').val());
        const childCount = $('#child-count').val() ? parseInt($('#child-count').val()) : 0; 

        if (selectedMealPlan && mealPlanPrices[selectedMealPlan]) {
            const mealPlanCostForAdults = mealPlanPrices[selectedMealPlan].adult * adultCount * nights;
            const mealPlanCostForChildren = mealPlanPrices[selectedMealPlan].child * childCount * nights;

            // Add meal plan cost for the entire stay (based on number of days) to the total price
            totalPriceBeforeTaxLKR += mealPlanCostForAdults + mealPlanCostForChildren;
        }

        // Calculate tax amount
        const taxRate = 0.03;
        const taxAmountLKR = totalPriceBeforeTaxLKR * taxRate;
        const totalPriceAfterTaxLKR = totalPriceBeforeTaxLKR + taxAmountLKR;

        // Calculate equivalent USD prices
        const totalPriceBeforeTaxUSD = totalPriceBeforeTaxLKR / <?php echo $exchange_rate; ?>;
        const taxAmountUSD = totalPriceBeforeTaxUSD * taxRate;
        const totalPriceAfterTaxUSD = totalPriceBeforeTaxUSD + taxAmountUSD;

        // Update LKR Pricing
        $('#nightly-price-before-tax-lkr').text((totalPriceBeforeTaxLKR / nights).toFixed(2));
        $('#tax-amount-lkr').text(taxAmountLKR.toFixed(2));
        $('#nightly-price-after-tax-lkr').text((totalPriceAfterTaxLKR / nights).toFixed(2));
        $('#total-price-after-tax-lkr').text(totalPriceAfterTaxLKR.toFixed(2));
        $('#advance-payment-lkr').text((totalPriceAfterTaxLKR * 0.5).toFixed(2));
        $('#advance-payment').val((totalPriceAfterTaxLKR * 0.5).toFixed(2));

        // Update USD Pricing
        $('#nightly-price-before-tax-usd').text((totalPriceBeforeTaxUSD / nights).toFixed(2));
        $('#tax-amount-usd').text(taxAmountUSD.toFixed(2));
        $('#nightly-price-after-tax-usd').text((totalPriceAfterTaxUSD / nights).toFixed(2));
        $('#total-price-after-tax-usd').text(totalPriceAfterTaxUSD.toFixed(2));
        $('#advance-payment-usd').text((totalPriceAfterTaxUSD * 0.5).toFixed(2));

        // Show the pricing details section
        $('#pricing-details').show();
    } else {
        alert('Invalid check-in or check-out dates.');
    }
}

// Attach change event to meal plan dropdown to recalculate prices when changed
$('#meal-plan').on('change', function () {
    updatePricingDetails();
});



// Child Count Warning Message
$('#child-count').on('input', function () {
    const childCount = parseInt($(this).val());
    const messageElement = $('#child-count-message');

    if (childCount > 0) {
        messageElement.show();
    } else {
        messageElement.hide();
    }
});

// Acceptance Checkbox
$('#accept-terms').on('change', function() {
    $('#submit-booking').prop('disabled', !this.checked);
});
</script>


<script>
// Disable previous dates for check-in and check-out inputs
document.addEventListener('DOMContentLoaded', function () {
    const today = new Date().toISOString().split('T')[0];

    // Disable past dates for check-in and check-out fields
    document.getElementById('check-in').setAttribute('min', today);
    document.getElementById('check-out').setAttribute('min', today);

    // Event listeners for real-time validation
    $('#check-in').on('change', function () {
        validateCheckInDate();
    });

    $('#check-out').on('change', function () {
        validateCheckOutDate();
    });
});

function validateCheckInDate() {
    const checkInDate = new Date($('#check-in').val());
    const today = new Date();

    // Reset time components for accurate comparison
    today.setHours(0, 0, 0, 0);
    checkInDate.setHours(0, 0, 0, 0);

    if (checkInDate < today) {
        alert("Check-in date cannot be in the past. Please select a valid date.");
        $('#check-in').val('');
    } else {
        // Adjust the minimum value for the check-out date
        const minCheckOutDate = new Date(checkInDate);
        minCheckOutDate.setDate(minCheckOutDate.getDate() + 1);
        $('#check-out').attr('min', minCheckOutDate.toISOString().split('T')[0]);
    }
}

function validateCheckOutDate() {
    const checkInDate = new Date($('#check-in').val());
    const checkOutDate = new Date($('#check-out').val());

    // Reset time components for accurate comparison
    checkInDate.setHours(0, 0, 0, 0);
    checkOutDate.setHours(0, 0, 0, 0);

    if (checkOutDate <= checkInDate) {
        alert("Check-out date must be after the check-in date. Please select a valid date.");
        $('#check-out').val('');
    }
}
</script>


<script>
// Real-time Mobile Number Validation
document.addEventListener('DOMContentLoaded', function () {
    const telephoneInput = document.getElementById('telephone');

    telephoneInput.addEventListener('input', function () {
        const value = telephoneInput.value;

        // Check if the first character is '+'
        if (value.length > 0 && value[0] !== '+') {
            alert("Please start the mobile number with a country code using '+' symbol.");
            telephoneInput.value = '';
            return;
        }

        
        const regex = /^\+\d*$/;
        if (!regex.test(value)) {
            alert("Only numbers are allowed after the '+' symbol. Please remove any letters or symbols.");
            telephoneInput.value = '';
            return;
        }

        
        const withoutPlus = value.substring(1);
        if (withoutPlus.length > 0 && withoutPlus[0] === '0') {
            alert("No leading zero allowed after the country code. Please remove the leading zero.");
            telephoneInput.value = '';
            return;
        }
    });
});
</script>


  </body>
</html>
