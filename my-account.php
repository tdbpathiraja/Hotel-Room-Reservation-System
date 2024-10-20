<?php
session_start();

if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    header('Location: visitor-login.php');
    exit();
}

include 'assets/database-functions/dbconnection.php';

$username = $_SESSION['username'] ?? $_COOKIE['username'];

$stmt = $conn->prepare('SELECT full_name, email, birth_date, profile_image FROM users WHERE username = ?');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    header('Location: visitor-login.php');
    exit();
}

$stmt->bind_result($full_name, $email, $birth_date, $profile_image);
$stmt->fetch();
$stmt->close();

$_SESSION['name'] = $full_name;

// Profile Image Control
if (empty($profile_image)) {
    $profile_image = 'assets/img/visitor-profiles/male-default.jpg';
} else {
    $profile_image = 'assets/img/visitor-profiles/' . $profile_image;
}

// Function to delete expired bookings
function deleteExpiredBookings($conn)
{
    $current_date = date('Y-m-d H:i:s');

    // Delete bookings that are still in "Pending Payment" status after 3 days
    $stmt = $conn->prepare("DELETE FROM booking_details WHERE booking_status = 'Pending Payment' AND created_at < DATE_SUB(?, INTERVAL 3 DAY)");
    $stmt->bind_param('s', $current_date);
    $stmt->execute();

    // Delete bookings with no 3-day gap and check-in date is today
    $stmt = $conn->prepare("DELETE FROM booking_details WHERE booking_status = 'Pending Payment' AND check_in = CURDATE() AND created_at > DATE_SUB(CURDATE(), INTERVAL 3 DAY)");
    $stmt->execute();
}

deleteExpiredBookings($conn);

// Fetch bookings
$stmt = $conn->prepare('SELECT booking_id, room_name, room_id, check_in, check_out, guest_count, adult_count, child_count, payment_method, special_notes, booking_status, payment_slip_img, advanced_payment, created_at FROM booking_details WHERE username = ?');
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

// Check for pending bookings
$stmt = $conn->prepare('SELECT COUNT(*) FROM booking_details WHERE username = ? AND booking_status = "Pending Payment"');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($pending_bookings_count);
$stmt->fetch();

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

    <!-- Navigation bar -->
    <?php include 'nav-bar.php';?>

    <!--=========== Visitor Hero ===========-->
    <section class="greeting-section">
        <div class="greeting-content">
            <h1 id="greeting-message"></h1>
            <p>Welcome!! <?php echo htmlspecialchars($full_name); ?></p>
        </div>
    </section>


    <!--=========== 1. Visitor Profile ===========-->
    <section class="visitor-profile">
        <div class="container">
            <div class="profile-header">
                <h2>My Account</h2>
            </div>
            <div class="profile-details">
                <div class="profile-image">
                    <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Image" />
                </div>
                <div class="profile-info">
                    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($full_name); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <p><strong>Birth Date:</strong> <?php echo htmlspecialchars(date('F j, Y', strtotime($birth_date))); ?></p>
                </div>
            </div>

            <!-- Profile Action Buttons -->
            <div class="profile-actions">
            <button class="btn btn-primary" id="edit-profile-trigger"><i class="fa fa-edit"></i>Edit Details</button>
              <button class="btn btn-warning" id="reset-password-trigger"><i class="fa fa-key"></i>Reset Password</button>
              <a href="assets/database-functions/visitor/delete-profile.php" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete your profile? This action cannot be undone.');"><i class="fa fa-trash"></i>Delete Profile</a>
              <a href="assets/database-functions/visitor/logout.php" class="btn btn-secondary"><i class="fa fa-sign-out-alt"></i>Logout</a>

            </div>
        </div>
    </section>



<!--******** 1.1 Visitor Profile | Password Reset Popup Modal ********-->
<div id="reset-password-modal" style="display: none;">
    <form method="POST" action="assets/database-functions/visitor/reset-password.php">
        <h2>Reset Password</h2>
        <label for="new-password">New Password</label>
        <input type="password" id="new-password" name="new-password" required>
        <button type="submit">Reset Password</button>
        <button type="button" id="close-modal">X</button>
    </form>
</div>

<script>
    document.getElementById('reset-password-trigger').addEventListener('click', function() {
        document.getElementById('reset-password-modal').style.display = 'block';
    });

    document.getElementById('close-modal').addEventListener('click', function() {
        document.getElementById('reset-password-modal').style.display = 'none';
    });
</script>


<!--******** 1.2 Visitor Profile | Edit Profile Popup Modal ********-->
<div id="edit-profile-modal" style="display: none;">
    <form method="POST" action="assets/database-functions/visitor/update-profile.php" id="edit-profile-form">
        <h2>Edit Profile</h2>
        <input type="hidden" name="current-username" value="<?php echo htmlspecialchars($username); ?>">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>
        </div>
        <div class="form-group">
            <label for="full-name">Full Name:</label>
            <input type="text" id="full-name" name="full-name" value="<?php echo htmlspecialchars($full_name); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <button type="submit">Update Profile</button>
        <button type="button" id="close-edit-modal">X</button>
    </form>
</div>


<script>

document.getElementById('edit-profile-trigger').addEventListener('click', function() {
    document.getElementById('edit-profile-modal').style.display = 'block';
});

document.getElementById('close-edit-modal').addEventListener('click', function() {
    document.getElementById('edit-profile-modal').style.display = 'none';
});

document.getElementById('edit-profile-form').addEventListener('submit', function(event) {
    event.preventDefault();

    var formData = new FormData(this);

    fetch('assets/database-functions/visitor/update-profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);
        if (data.includes('success')) {

            document.getElementById('edit-profile-modal').style.display = 'none';
            location.reload();
        } else {
            alert('Update failed. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

</script>


<section class="user-bookings">
    <div class="container">
        <h2 class="bookingtable-heading">My Bookings</h2>
        <?php
// Currency Conversion API Connection
$api_url = 'https://v6.exchangerate-api.com/v6/dcc1e5d38b9c1254ecef1b28/latest/USD';
$response = file_get_contents($api_url);
$data = json_decode($response, true);
$exchange_rate = $data['conversion_rates']['LKR'];

$current_date = new DateTime();

if ($result->num_rows > 0):
    $has_valid_bookings = false;
    ?>
	            <div class="booking-cards">
	                <?php while ($row = $result->fetch_assoc()):
        $check_in_date = new DateTime($row['check_in']);
        $created_date = new DateTime($row['created_at']);
        $check_out_date = new DateTime($row['check_out']);
        $interval = $current_date->diff($check_in_date);
        $days_gap = $interval->days;

        // Set payment deadline and time remaining
        $payment_deadline = clone $created_date;
        $time_remaining = null;

        if ($days_gap <= 3) {
            $time_remaining = $current_date->diff($check_in_date);
        } else {
            $payment_deadline->modify('+3 days');
            $time_remaining = $current_date->diff($payment_deadline);
        }

        // Skip reviewed bookings
        if (strtolower($row['booking_status']) === 'reviewed') {
            continue;
        }

        $has_valid_bookings = true;

        // Fetch advanced payment amount
        $advanced_payment = $row['advanced_payment'] ?? 0; // Default value if no data found
        $advanced_payment_usd = $advanced_payment / $exchange_rate;
        ?>
		                    <div class="booking-card">
		                        <div class="card-header">
		                            <span class="cancel-button <?php echo $row['booking_status'] === 'Pending Payment' ? '' : 'disabled'; ?>"
		                                data-booking-id="<?php echo htmlspecialchars($row['booking_id']); ?>"
		                                title="Cancel Booking">&times;
		                            </span>
		                            <i class="fas fa-bookmark"></i> Booking ID: <?php echo htmlspecialchars($row['booking_id']); ?>
		                        </div>
		                        <div class="card-body">
		                            <div class="card-info">
		                                <!-- Booking Information -->
		                                <p><strong><i class="fas fa-bed"></i> Room:</strong> <?php echo htmlspecialchars($row['room_name']); ?></p>
		                                <p><strong><i class="fas fa-calendar-check"></i> Check-In:</strong> <?php echo htmlspecialchars($row['check_in']); ?></p>
		                                <p><strong><i class="fas fa-calendar-times"></i> Check-Out:</strong> <?php echo htmlspecialchars($row['check_out']); ?></p>
		                                <p><strong><i class="fas fa-user"></i> Adults:</strong> <?php echo htmlspecialchars($row['adult_count']); ?></p>
		                                <p><strong><i class="fas fa-child"></i> Children:</strong> <?php echo htmlspecialchars($row['child_count']); ?></p>
		                                <p><strong><i class="fas fa-sticky-note"></i> Special Notes:</strong> <?php echo htmlspecialchars($row['special_notes']); ?></p>
		                                <p><strong><i class="fas fa-info-circle"></i> Status:</strong> <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $row['booking_status'])); ?>"><?php echo htmlspecialchars($row['booking_status']); ?></span></p>

		                                <!-- Display Advanced Payment Amount Preview and Function -->
		                                <?php if ($row['booking_status'] === 'Pending Payment'): ?>
		                                    <p class="advanced-pay">
		                                        <strong><i class="fas fa-money-bill-wave"></i> Advanced Payment Amount:</strong>
		                                        <span class="advanced-payment-amount">
		                                            <?php echo number_format($advanced_payment, 2); ?> LKR
		                                        </span>
		                                        <span class="advanced-payment-amount">
		                                            <?php echo number_format($advanced_payment_usd, 2); ?> USD
		                                        </span>
		                                    </p>
		                                <?php endif;?>

	                                <!-- Countdown Timer -->
	                                <?php if ($row['booking_status'] === 'Pending Payment'): ?>
	                                    <?php if ($time_remaining->invert == 0): ?>
	                                        <div class="countdown-timer" data-deadline="<?php echo $days_gap <= 3 ? $check_in_date->format('Y-m-d H:i:s') : $payment_deadline->format('Y-m-d H:i:s'); ?>" data-booking-id="<?php echo htmlspecialchars($row['booking_id']); ?>">
	                                            <i class="fas fa-hourglass-half"></i> Time remaining to pay: <span class="timer"></span>
	                                        </div>
	                                    <?php else: ?>
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-triangle"></i> Pay Immediately to Keep your Booking
                                        </div>
                                    <?php endif;?>
                                <?php endif;?>
                            </div>
                        </div>
                        <div class="card-footer">

                            <!-- Booking Action Buttons -->
                            <?php
if ($row['booking_status'] === 'Pending Payment') {
    echo '<p><i class="fas fa-info-circle"></i> To confirm your booking, we kindly request a 50% advance payment. If the advance is not received, the booking may be subject to cancellation.</p>';
    echo '<button class="btn-custom btn-payment" data-booking-id="' . htmlspecialchars($row['booking_id']) . '" data-room-name="' . htmlspecialchars($row['room_name']) . '" data-check-in="' . htmlspecialchars($row['check_in']) . '" data-check-out="' . htmlspecialchars($row['check_out']) . '" data-payment-method="' . htmlspecialchars($row['payment_method']) . '"><i class="fas fa-credit-card"></i> Make Advanced Payment</button>';
} elseif ($row['booking_status'] === 'Pending Verification' || $row['booking_status'] === 'Booked') {
    if ($current_date == $check_in_date) {
        echo '<button class="btn-custom btn-waiting"><i class="fas fa-smile"></i> Enjoy your Stay</button>';
    } elseif ($current_date > $check_in_date && $current_date < $check_out_date) {
        echo '<button class="btn-custom btn-waiting"><i class="fas fa-smile"></i> Enjoy your Stay</button>';
    } elseif ($days_gap < 2) {
        echo '<button class="btn-custom btn-waiting"><i class="fas fa-clock"></i> We are waiting for your arrival</button>';
    }
}

if ($current_date >= $check_out_date || strtolower($row['booking_status']) === 'checked out') {
    echo '<button class="btn-custom btn-review" data-booking-id="' . htmlspecialchars($row['booking_id']) . '" data-room-name="' . htmlspecialchars($row['room_name']) . '"><i class="fas fa-star"></i> Leave a Review to us</button>';
}
?>
                        </div>
                    </div>
                <?php endwhile;?>
            </div>
        <?php
if (!$has_valid_bookings): ?>
            <p class="no-bookings-message"><i class="fas fa-info-circle"></i> You have no bookings yet. Go to the <a href="booking-room.php">Booking Room</a> portal to book a room or visit <a href="rooms.php">Room Details</a> to learn more about our rooms.</p>
        <?php endif;?>
        <?php else: ?>
            <p class="no-bookings-message"><i class="fas fa-info-circle"></i> You have no bookings yet. Go to the <a href="booking-room.php">Booking Room</a> portal to book a room or visit <a href="rooms.php">Room Details</a> to learn more about our rooms.</p>
        <?php endif;?>
        <?php
$stmt->close();
?>
    </div>
</section>


   <!--******** 2.1 Booking Details | Bank Transfer Model ********-->
<div id="advanced-payment-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Make Advanced Payment</h2>
        <p>Please transfer the advanced payment amount to the following bank account:</p>
        <div class="bank-details">
            <p><strong>Bank Name:</strong> Commercial Bank</p>
            <p><strong>Account Number:</strong> 8022188874</p>
            <p><strong>Account Name:</strong> T.G.D.T.C GAMAGE</p>
            <p><strong>Branch:</strong> Peradeniya</p>
            <p><strong>Country:</strong> Sri Lanka </p>
        </div>

        <p>Please note that the remaining balance can be paid after the check-out date.</p>
        <form id="payment-form">
            <input type="hidden" id="booking-id" name="booking-id">
            <button type="button" id="continue-payment" class="btn btn-primary">Upload Paid Bank Slip</button>
        </form>
    </div>
</div>

<!-- payment Models Contolling and Price Showing -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bankModal = document.getElementById('advanced-payment-modal');

        function openModal(paymentMethod, bookingId) {
            if (paymentMethod === 'bank') {
                document.getElementById('booking-id').value = bookingId;
                bankModal.style.display = 'block';
            } else {
                alert('Invalid payment method.');
            }
        }

        document.querySelectorAll('.btn-payment').forEach(button => {
            button.addEventListener('click', function () {
                const paymentMethod = this.getAttribute('data-payment-method');
                const bookingId = this.getAttribute('data-booking-id');
                openModal(paymentMethod, bookingId);
            });
        });

        document.querySelector('.close').onclick = function () {
            bankModal.style.display = 'none';
        };

        window.onclick = function (event) {
            if (event.target == bankModal) {
                bankModal.style.display = 'none';
            }
        };

        document.getElementById('continue-payment').addEventListener('click', function () {
            const bookingId = document.getElementById('booking-id').value;
            window.location.href = 'bank-transfer-confirm.php?booking_id=' + bookingId;
        });
    });
    </script>


<!--******** 2.3 Booking Details | Review Model ********-->
<div id="reviewModal" class="review-modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Leave a Review</h2>
        <div id="review-header">
            <p id="review-room-name">Review for: <span></span></p>
        </div>
        <?php if (isset($_SESSION['review_status']) && isset($_SESSION['review_message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['review_status'] === 'success' ? 'success' : 'danger'; ?>" role="alert">
                <?php echo htmlspecialchars($_SESSION['review_message']); ?>
            </div>
            <?php unset($_SESSION['review_status']); ?>
            <?php unset($_SESSION['review_message']); ?>
        <?php endif; ?>
        <form id="reviewForm">
            <input type="hidden" id="username" name="username" value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>">
            <input type="hidden" id="name" name="name" value="<?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : ''; ?>">
            <input type="hidden" id="room-names" name="room-names">
            <input type="hidden" id="booking_id" name="booking_id">
            <input type="hidden" id="current_room_name" name="room-name">
            <div class="rating">
                <input type="radio" id="star5" name="rating" value="5" required>
                <label for="star5" title="5 stars"><i class="fas fa-star"></i></label>
                <input type="radio" id="star4" name="rating" value="4">
                <label for="star4" title="4 stars"><i class="fas fa-star"></i></label>
                <input type="radio" id="star3" name="rating" value="3">
                <label for="star3" title="3 stars"><i class="fas fa-star"></i></label>
                <input type="radio" id="star2" name="rating" value="2">
                <label for="star2" title="2 stars"><i class="fas fa-star"></i></label>
                <input type="radio" id="star1" name="rating" value="1">
                <label for="star1" title="1 star"><i class="fas fa-star"></i></label>
            </div>
            <textarea id="review-text" name="review-text" placeholder="Write your review here..." required></textarea>
            <button type="submit" class="btn-custom">Submit Review</button>
        </form>
    </div>
</div>

<!-- Review model functioning script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const reviewModal = document.getElementById('reviewModal');
        const closeModalButton = reviewModal.querySelector('.close');
        const reviewForm = document.getElementById('reviewForm');
        const reviewButtons = document.querySelectorAll('.btn-review');

        const bookingIdField = document.getElementById('booking_id');
        const reviewRoomNameDisplay = document.getElementById('review-room-name').querySelector('span');
        const currentRoomNameField = document.getElementById('current_room_name');
        const roomNamesField = document.getElementById('room-names');
        const totalRoomsField = document.createElement('input');
        totalRoomsField.type = 'hidden';
        totalRoomsField.id = 'total_rooms';
        totalRoomsField.name = 'total_rooms';
        reviewForm.appendChild(totalRoomsField);

        const currentReviewCountField = document.createElement('input');
        currentReviewCountField.type = 'hidden';
        currentReviewCountField.id = 'current_review_count';
        currentReviewCountField.name = 'current_review_count';
        reviewForm.appendChild(currentReviewCountField);

        let roomNamesArray = [];
        let currentRoomIndex = 0;

        // Check sessionStorage to determine if the modal should reopen after a page refresh
        if (sessionStorage.getItem('reopenReviewModal') === 'true') {
            roomNamesArray = sessionStorage.getItem('roomNames').split(',');
            currentRoomIndex = parseInt(sessionStorage.getItem('currentRoomIndex'), 10);
            const bookingId = sessionStorage.getItem('bookingId');

            bookingIdField.value = bookingId;
            roomNamesField.value = roomNamesArray.join(',');
            currentRoomNameField.value = roomNamesArray[currentRoomIndex];
            reviewRoomNameDisplay.textContent = roomNamesArray[currentRoomIndex];
            totalRoomsField.value = roomNamesArray.length;
            currentReviewCountField.value = currentRoomIndex;

            reviewModal.style.display = 'block';
        }

        // Event listener for review buttons
        reviewButtons.forEach(button => {
            button.addEventListener('click', function () {
                const bookingId = this.dataset.bookingId;
                const roomNames = this.dataset.roomName.split(',');

                roomNamesArray = roomNames.map(name => name.trim());
                currentRoomIndex = 0;

                bookingIdField.value = bookingId;
                roomNamesField.value = roomNamesArray.join(',');
                currentRoomNameField.value = roomNamesArray[currentRoomIndex];
                reviewRoomNameDisplay.textContent = roomNamesArray[currentRoomIndex];
                totalRoomsField.value = roomNamesArray.length;
                currentReviewCountField.value = currentRoomIndex;

                reviewModal.style.display = 'block';
            });
        });

        // Submit review form
        reviewForm.addEventListener('submit', function (event) {
    event.preventDefault();
    const formData = new FormData(reviewForm);

    console.log('Form Data:', Object.fromEntries(formData.entries()));

    fetch('assets/database-functions/visitor/submit-review.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            console.log('Server Response:', data);
            if (data.status === 'success') {
                currentRoomIndex++;

                if (currentRoomIndex < roomNamesArray.length) {
                    sessionStorage.setItem('reopenReviewModal', 'true');
                    sessionStorage.setItem('roomNames', roomNamesArray.join(','));
                    sessionStorage.setItem('currentRoomIndex', currentRoomIndex);
                    sessionStorage.setItem('bookingId', bookingIdField.value);

                    currentReviewCountField.value = currentRoomIndex;

                    location.reload();
                } else {
                    sessionStorage.removeItem('reopenReviewModal');
                    sessionStorage.removeItem('roomNames');
                    sessionStorage.removeItem('currentRoomIndex');
                    sessionStorage.removeItem('bookingId');

                    reviewModal.style.display = 'none';
                    setTimeout(() => location.reload(), 500);
                }
            } else {
                alert('Review submission failed. Please try again.');
                console.log('Error:', data.message); // Log error details
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            alert('An error occurred while submitting the review.');
        });
});


        // Close review modal
        closeModalButton.onclick = function () {
            reviewModal.style.display = 'none';
        };

        // Close the modal when clicking outside of it
        window.onclick = function (event) {
            if (event.target == reviewModal) {
                reviewModal.style.display = 'none';
            }
        };
    });
</script>



<!-- Booking Cancel and X button behaviour -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.cancel-button').forEach(button => {
        button.addEventListener('click', function() {
            const bookingId = this.getAttribute('data-booking-id');

            // Check if the button is disabled
            if (this.classList.contains('disabled')) {
                alert('This booking cannot be canceled because it is not in the Pending Payment status.');
                return;
            }

            if (confirm('Are you sure you want to cancel this booking? We do not offer any refunds for this booking.')) {
                cancelBooking(bookingId);
            }
        });
    });

    function cancelBooking(bookingId) {
        fetch('assets/database-functions/visitor/cancel-booking.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'booking_id': bookingId,
            }),
        })
        .then(response => response.text())
        .then(data => {
            alert('Booking cancelled successfully.');
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while cancelling the booking.');
            location.reload();
        });
    }
});
</script>



<!-- Count Down Timer and Delete function -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const countdownTimers = document.querySelectorAll('.countdown-timer');

    countdownTimers.forEach(timer => {
        const deadline = new Date(timer.dataset.deadline).getTime();
        const timerSpan = timer.querySelector('.timer');
        const bookingId = timer.dataset.bookingId;

        const updateTimer = setInterval(function() {
            const now = new Date().getTime();
            const timeLeft = deadline - now;

            if (timeLeft < 0) {
                clearInterval(updateTimer);
                timer.innerHTML = '<div class="alert alert-danger">Booking expired. It will be removed shortly.</div>';

                fetch('assets/database-functions/room-booking/general/auto-delete_booking.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'booking_id=' + bookingId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        timer.closest('.booking-card').remove();
                    }
                })
                .catch(error => console.error('Error:', error));
            } else {
                const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                timerSpan.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
            }
        }, 1000);
    });
});
</script>


<?php if ($pending_bookings_count > 0): ?>
<div id="pendingBookingsModal" class="pending-bookings-modal">
    <div class="pending-bookings-modal-content">
        <span class="pending-bookings-close">&times;</span>
        <h2 class="pending-bookings-title">Attention: You Have Pending Bookings!</h2>
        <p class="pending-bookings-description">Please complete your payment to confirm your booking. Watch this video for more information.</p>
        <small>This popup message will automatically disappear when you don't have pending payments for the bookings.</small>
        <div class="pending-bookings-video-container">
            <video id="pendingBookingsVideo" controls>
                <source src="assets/video/Varsity Tutorial.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    </div>
</div>
<?php endif;?>


<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('pendingBookingsModal');
    var span = document.getElementsByClassName('pending-bookings-close')[0];
    var video = document.getElementById('pendingBookingsVideo');

    // Open the modal when the page loads
    modal.style.display = "block";

    // Close the modal when clicking the close button
    span.onclick = function() {
        modal.style.display = "none";
        video.pause();
    }

    // Close the modal when clicking outside of it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
            video.pause();
        }
    }
});
</script>
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

    <!-- User Greeting Function -->
    <script>
        function getGreeting() {
            const now = new Date();
            const hours = now.getHours();
            let greeting = '';

            if (hours < 12) {
                greeting = 'Good Morning!!';
            } else if (hours < 18) {
                greeting = 'Good Afternoon!!';
            } else {
                greeting = 'Good Evening!!';
            }

            return greeting;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const greetingMessage = document.getElementById('greeting-message');
            greetingMessage.textContent = getGreeting();
        });
    </script>

