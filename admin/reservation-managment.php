<?php
session_start();

include '../assets/database-functions/dbconnection.php';

if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    header('Location:login.php');
    exit();
}

function getAllReservations($conn)
{
    $sql = "SELECT booking_id, room_name, name, username, check_in, check_out, telephone, booking_status, payment_method, payment_slip_img, advanced_payment FROM booking_details ORDER BY check_in DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllUsers($conn)
{
    $sql = "SELECT id, full_name, username FROM users ORDER BY full_name ASC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch all reservations
$reservations = getAllReservations($conn);

// Fetch all users for the dropdown
$users = getAllUsers($conn);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Varsity Lodge | Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link
      href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css"
      rel="stylesheet"
    />
    <link href="../assets/css/admin-dashboard.css" rel="stylesheet">
    
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="container">
    <div class="row">
        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="dashboard-header d-flex justify-content-between align-items-center">
                <h1 class="h2">Reservation Management</h1>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#lockDatesModal">
                    <i class="fas fa-lock"></i> Lock Dates
                </button>
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#cancelBookingsModal">
                    <i class="fas fa-book"></i> View Canceled Bookings
                </button>
            </div>

            <!-- Reservations Card View -->
            <div id="reservationsContainer" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 reservation-cards">
                <?php 
                $current_date = new DateTime();
                foreach ($reservations as $reservation):
                    $check_in_date = new DateTime($reservation['check_in']);
                    $check_out_date = new DateTime($reservation['check_out']);
                    
                    $days_until_checkin = $current_date->diff($check_in_date)->days;
                    $stay_duration = $check_in_date->diff($check_out_date)->days;
                    
                    
                    $hide_reservation = false;
                    $one_day_after_checkout = clone $check_out_date;
                    $one_day_after_checkout->modify('+1 day');
                    
                    if ($current_date >= $one_day_after_checkout || $reservation['booking_status'] === 'Checked Out' || $reservation['booking_status'] === 'Reviewed') {
                        $hide_reservation = true;
                    }
                    
                    
                    if ($hide_reservation) {
                        continue;
                    }
                    
                    $status_message = '';
                    if ($current_date < $check_in_date) {
                        $status_message = "$days_until_checkin days left until check-in";
                    } elseif ($current_date >= $check_in_date && $current_date < $check_out_date) {
                        $days_elapsed = $current_date->diff($check_in_date)->days;
                        $days_remaining = $stay_duration - $days_elapsed;
                        $status_message = "Currently staying: Day $days_elapsed of $stay_duration (${days_remaining} days remaining)";
                    } elseif ($current_date >= $check_out_date) {
                        $status_message = "Check-out date has passed";
                    }
                ?>
                <div class="col">
                    <div class="card h-100 reservation-info">
                        <div class="card-body">
                            <h5 class="reservation-title"><?php echo $reservation['booking_id']; ?></h5>
                            <p class="reservation-text">
                                <div class="guest-info">
                                    <strong>Guest Name:</strong> <?php echo $reservation['name']; ?><br>
                                    <strong>Username:</strong> <?php echo $reservation['username']; ?><br>
                                    <strong>Guest Contact Number:</strong> <?php echo $reservation['telephone']; ?><br><br>
                                </div>
                                <div class="booking-details">
                                    <strong>Room:</strong> <?php echo $reservation['room_name']; ?><br>
                                    <strong>Check-in:</strong> <?php echo $reservation['check_in']; ?><br>
                                    <strong>Check-out:</strong> <?php echo $reservation['check_out']; ?><br>
                                    <strong>Payment Method:</strong> <?php echo $reservation['payment_method']; ?><br>
                                    <strong>Booking Status:</strong> <?php echo $reservation['booking_status']; ?><br><br><br>
                                </div>
                                <div class="payment-message">
    <?php if ($reservation['booking_status'] === 'Pending Verification'): ?>
        <strong>Visitor Paid</strong> Rs.<?php echo $reservation['advanced_payment']; ?>. Please check the payment slip and approve or reject this.
    <?php elseif ($reservation['booking_status'] === 'Booked'): ?>
        <?php 
            
            $paymentDue = $reservation['advanced_payment'] / 2;
        ?>
        <strong>Visitor needs to pay Rs.<?php echo number_format($paymentDue, 2); ?> when checking out from the hotel.</strong>
    <?php else: ?>
        <strong>Need to Pay:</strong> Rs.<?php echo $reservation['advanced_payment']; ?>
    <?php endif; ?>
</div>

                                <div class="status-message">
                                    <strong>Status:</strong> <?php echo $status_message; ?>
                                </div>
                            </p>
                            <div class="button-group">
                                <button class="payment-slip" onclick="viewPaymentSlip('<?php echo $reservation['payment_slip_img']; ?>', '<?php echo $reservation['booking_id']; ?>')">View Payment Slip</button>
                                
                                <select class="status-selection" data-current-status="<?php echo $reservation['booking_status']; ?>" onchange="handleStatusChange('<?php echo $reservation['booking_id']; ?>', this)">
                                    <option value="Pending Payment">Pending Payment</option>
                                    <option value="Pending Verification">Pending Verification</option>
                                    <option value="Booked">Booked</option>
                                    <option value="checked out">Checked Out Visitor</option>
                                </select>

                                <?php if ($reservation['booking_status'] === 'Pending Payment' || $reservation['booking_status'] === 'Pending Verification'): ?>
                                    <button type="button" class="reservation-deletebtn" onclick="deleteReservation('<?php echo $reservation['booking_id']; ?>')">Cancel</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Payment Slip Modal -->
            <div class="modal fade" id="paymentSlipModal" tabindex="-1" aria-labelledby="paymentSlipModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="paymentSlipModalLabel">Payment Slip</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <img id="paymentSlipImage" src="" alt="Payment Slip" class="img-fluid">
                        </div>
                        <div class="modal-footer">
                            <a id="downloadPaymentSlip" href="#" class="btn btn-primary" download>Download Payment Slip</a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>



<!-- Lock Dates Modal -->
<div class="modal fade" id="lockDatesModal" tabindex="-1" aria-labelledby="lockDatesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lockDatesModalLabel">Lock Dates</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="lockDatesForm">
                    <div class="mb-3">
                        <label for="roomSelect" class="form-label">Room</label>
                        <select class="form-select" id="roomSelect" required>
                            <option value="">Select a room</option>
                            <?php
                            $roomsQuery = "SELECT RoomID, RoomName FROM roomdetails ORDER BY RoomName ASC";
                            $roomsResult = $conn->query($roomsQuery);
                            while ($room = $roomsResult->fetch_assoc()) {
                                echo "<option value='{$room['RoomID']}'>{$room['RoomName']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="checkInDate" class="form-label">Check-in Date</label>
                        <input type="date" class="form-control" id="checkInDate" required>
                    </div>
                    <div class="mb-3">
                        <label for="checkOutDate" class="form-label">Check-out Date</label>
                        <input type="date" class="form-control" id="checkOutDate" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Lock this Date</button>
                </form>
                <hr>
                <h6>Locked Dates</h6>
                <ul id="lockedDatesList" class="list-group">
                    <!-- Locked dates preview -->
                </ul>
            </div>
        </div>
    </div>
</div>


<!-- Cancel Bookings Modal -->
<div class="modal fade" id="cancelBookingsModal" tabindex="-1" aria-labelledby="cancelBookingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelBookingsModalLabel">Canceled Bookings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body cancel-bookings-body">
                <ul id="cancelBookingsList" class="list-group">
                    <?php
                    // Fetch canceled bookings from cancel_log table
                    $canceledBookingsQuery = "SELECT * FROM cancel_log ORDER BY canceled_at DESC";
                    $canceledBookingsResult = $conn->query($canceledBookingsQuery);
                    
                    if ($canceledBookingsResult->num_rows > 0) {
                        while ($booking = $canceledBookingsResult->fetch_assoc()) {
                            echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                            echo '<div class="cancel-booking-info">';
                            echo '<strong>Booking ID:</strong> ' . $booking['booking_id'] . '<br>';
                            echo '<strong>Guest Name:</strong> ' . $booking['guest_name'] . '<br>';
                            echo '<strong>Check-in:</strong> ' . $booking['check_in'] . '<br>';
                            echo '<strong>Check-out:</strong> ' . $booking['check_out'] . '<br>';
                            echo '</div>';
                            echo '<button class="btn btn-primary btn-sm print-transcript" onclick="printTranscript(\'' . $booking['booking_id'] . '\')">';
                            echo '<i class="fas fa-print"></i> Print Transcript';
                            echo '</button>';
                            echo '</li>';
                        }
                    } else {
                        echo '<li class="list-group-item">No canceled bookings found.</li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/disable-click.js"></script>

    <script>
    
function viewPaymentSlip(imageSrc, bookingId) {
    if (!imageSrc) {
        showAlert('No payment slip available for this reservation', 'warning');
        return;
    }

    const modal = new bootstrap.Modal(document.getElementById('paymentSlipModal'));
    const paymentSlipImage = document.getElementById('paymentSlipImage');
    const downloadButton = document.getElementById('downloadPaymentSlip');

    paymentSlipImage.src = `../assets/img/payments/banktransfers/${imageSrc}`;
    downloadButton.href = `../assets/img/payments/banktransfers/${imageSrc}`;
    downloadButton.download = `payment_slip_${bookingId}.jpg`;

    modal.show();
}


// Function to delete reservations
function deleteReservation(bookingId) {
    if (confirm("Are you sure you want to delete this reservation?")) {

        fetch('../assets/database-functions/admin/delete-reservation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `booking_id=${bookingId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Reservation deleted successfully', 'success');
                location.reload();
            } else {
                showAlert('Error deleting reservation', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error deleting reservation', 'error');
        });
    }
}


// Function to show alert messages
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    document.querySelector('main').prepend(alertDiv);

    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}


// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
    </script>



<!-- Booking Status Changing Function -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    document.querySelectorAll('.status-selection').forEach(selectElement => {
        const currentStatus = selectElement.getAttribute('data-current-status');
        const options = selectElement.querySelectorAll('option');

        
        selectElement.value = currentStatus;

        
        options.forEach(option => {
            const optionValue = option.value;
            option.disabled = !shouldEnableOption(currentStatus, optionValue);
        });
    });
});

function shouldEnableOption(currentStatus, optionValue) {
    const statusRules = {
        "Pending Payment": ["Pending Payment"],
        "Pending Verification": ["Pending Verification", "Booked", "checked out"],
        "Booked": ["checked out"],
        "checked out": []  
    };

    return statusRules[currentStatus].includes(optionValue);
}

function handleStatusChange(bookingId, selectElement) {
    const newStatus = selectElement.value;
    const currentStatus = selectElement.getAttribute('data-current-status');

   
    if (newStatus === "Booked" && !confirm("Are you sure you want to change the status to 'Booked'?")) {
        
        selectElement.value = currentStatus;
        return;
    }

    
    updateReservationStatus(bookingId, newStatus);
}

function updateReservationStatus(bookingId, newStatus) {
    fetch('../assets/database-functions/admin/update-reservation-status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `booking_id=${bookingId}&new_status=${newStatus}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Reservation status updated successfully', 'success');
    
            window.location.reload();
        } else {
            showAlert('Error updating reservation status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error updating reservation status', 'error');
    });
}

</script>


<script>
$(document).ready(function() {
    function loadLockedDates() {
        $.post('../assets/database-functions/admin/lock-booking-dates.php', { action: 'getlocked' }, function(data) {
            const lockedDates = JSON.parse(data);
            const list = $('#lockedDatesList');
            list.empty();
            lockedDates.forEach(function(date) {
                list.append(`
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        ${date.room_name}: ${date.check_in} to ${date.check_out}
                        <button class="btn btn-danger btn-sm delete-locked-date" data-booking-id="${date.booking_id}">
                            <i class="fas fa-times"></i>
                        </button>
                    </li>
                `);
            });
        });
    }

    $('#lockDatesForm').submit(function(e) {
        e.preventDefault();
        const roomId = $('#roomSelect').val();
        const checkIn = $('#checkInDate').val();
        const checkOut = $('#checkOutDate').val();

        $.post('../assets/database-functions/admin/lock-booking-dates.php', {
            action: 'lock',
            roomId: roomId,
            checkIn: checkIn,
            checkOut: checkOut
        }, function(data) {
            const result = JSON.parse(data);
            if (result.success) {
                alert('Dates locked successfully');
                loadLockedDates();
                $('#lockDatesForm')[0].reset();
            } else {
                alert('Error locking dates');
            }
        });
    });

    $(document).on('click', '.delete-locked-date', function() {
        const bookingId = $(this).data('booking-id');
        if (confirm('Are you sure you want to delete this locked date?')) {
            $.post('../assets/database-functions/admin/lock-booking-dates.php', {
                action: 'delete',
                bookingId: bookingId
            }, function(data) {
                const result = JSON.parse(data);
                if (result.success) {
                    alert('Locked date deleted successfully');
                    loadLockedDates();
                } else {
                    alert('Error deleting locked date');
                }
            });
        }
    });

    $('#lockDatesModal').on('show.bs.modal', function () {
        loadLockedDates();
    });
});
</script>

<script>
    function printTranscript(bookingId) {

    fetch(`../assets/database-functions/admin/cancelled_booking.php?booking_id=${bookingId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const booking = data.booking;
                
                const newWindow = window.open('', '_blank');
                
                newWindow.document.write(`
                    <html>
                        <head>
                            <title>Cancel Transcript</title>
                            <style>
                                /* Basic styles for print */
                                body { font-family: Arial, sans-serif; padding: 20px; }
                                h2 { text-align: center; }
                                .transcript-details { margin-top: 20px; }
                                .transcript-details strong { display: inline-block; width: 150px; }
                                /* Watermark styles */
                                .watermark {
                                    position: fixed;
                                    top: 50%;
                                    left: 50%;
                                    transform: translate(-50%, -50%);
                                    opacity: 0.1;
                                    font-size: 100px;
                                    color: red;
                                    z-index: -1;
                                }
                            </style>
                        </head>
                        <body>
                            <div class="watermark">Canceled</div>
                            <h2>Canceled Booking Transcript</h2>
                            <div class="transcript-details">
                                <strong>Booking ID:</strong> ${booking.booking_id}<br>
                                <strong>Guest Name:</strong> ${booking.guest_name}<br>
                                <strong>Username:</strong> ${booking.username}<br>
                                <strong>Contact Number:</strong> ${booking.contact_number}<br>
                                <strong>Room Name:</strong> ${booking.room_name}<br>
                                <strong>Check-in Date:</strong> ${booking.check_in}<br>
                                <strong>Check-out Date:</strong> ${booking.check_out}<br>
                                <strong>Payment Method:</strong> ${booking.payment_method}<br>
                                <strong>Advanced Payment:</strong> Rs.${booking.advanced_payment}<br>
                                <strong>Canceled At:</strong> ${booking.canceled_at}<br>
                            </div>
                        </body>
                    </html>
                `);

                // Close the document and trigger the print dialog
                newWindow.document.close();
                newWindow.print();
            } else {
                alert('Failed to fetch booking details.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error fetching booking details.');
        });
}
</script>

</body>
</html>