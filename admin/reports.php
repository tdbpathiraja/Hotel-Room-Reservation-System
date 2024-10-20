<?php
session_start();
include '../assets/database-functions/dbconnection.php';

if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    header('Location: login.php');
    exit();
}

// Initialize filter variables
$filter = '';
$filter_value = '';

// Check if filter is applied
if (isset($_POST['filter'])) {
    $filter = $_POST['filter'];
    $filter_value = $_POST['filter_value'];
}

// Build the query based on the filter
$sql = "SELECT * FROM booking_details WHERE booking_status IN ('Booked', 'checked out', 'reviewed')";

if ($filter === 'year') {
    $sql .= " AND YEAR(check_in) = '$filter_value'";
} elseif ($filter === 'month') {
    $sql .= " AND YEAR(check_in) = YEAR(CURDATE()) AND MONTH(check_in) = '$filter_value'";
} elseif ($filter === 'date') {
    $sql .= " AND check_in = '$filter_value'";
}

// Execute the query
$result = $conn->query($sql);

// Calculate the total advanced payment
$total_payment_sql = "SELECT SUM(advanced_payment) AS total_payment FROM booking_details WHERE booking_status IN ('Booked', 'checked out', 'reviewed')";
if ($filter === 'year') {
    $total_payment_sql .= " AND YEAR(check_in) = '$filter_value'";
} elseif ($filter === 'month') {
    $total_payment_sql .= " AND YEAR(check_in) = YEAR(CURDATE()) AND MONTH(check_in) = '$filter_value'";
} elseif ($filter === 'date') {
    $total_payment_sql .= " AND check_in = '$filter_value'";
}

$total_payment_result = $conn->query($total_payment_sql);
$total_payment_row = $total_payment_result->fetch_assoc();
$total_advanced_payment = $total_payment_row['total_payment'] ? $total_payment_row['total_payment'] : 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Varsity Lodge | Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #f9f9f9;
            margin: 5% auto;
            padding: 30px;
            border: 1px solid #ddd;
            width: 80%;
            max-width: 1000px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal-content h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .modal-content button {
            background-color: #28a745;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            display: block;
            width: 100%;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .modal-content button:hover {
            background-color: #218838;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        #printArea table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        #printArea th,
        #printArea td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        #printArea th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        #printArea tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .filter-info {
            background-color: #e9ecef;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 16px;
            color: #495057;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #printArea, #printArea * {
                visibility: visible;
            }

            #printArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            #printArea table {
                width: 100%;
                border-collapse: collapse;
            }

            #printArea th,
            #printArea td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }

            #printArea th {
                background-color: #f2f2f2 !important;
                -webkit-print-color-adjust: exact;
            }

            .modal-content button {
                display: none;
            }

            .filter-info {
                border: 1px solid #ddd;
                margin-bottom: 20px;
                padding: 10px;
            }
        }

        @media (max-width: 768px) {
            .filter-section form {
                flex-direction: column;
                align-items: stretch;
            }
            
            .booking-reports {
                overflow-x: auto;
            }
            
            .booking-table {
                min-width: 1000px;
            }
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="report-container">
    <section class="filter-section">
        <form method="POST" action="">
            <label for="filter">Filter by:</label>
            <select name="filter" id="filter">
                <option value="year">Year</option>
                <option value="month">Month</option>
                <option value="date">Check-In Date</option>
            </select>
            <input type="text" name="filter_value" placeholder="Enter Year/Month/Checking Date(Date in yyyy-mm-dd)">
            <input type="submit" value="Apply Filter">
            <button type="button" onclick="resetFilter()">Reset Filter</button>
        </form>
    </section>

    <?php if ($filter && $filter_value): ?>
        <div class="filter-info">
            Currently filtered by <?php echo $filter; ?>: <?php echo $filter_value; ?>
        </div>
    <?php endif; ?>

    <section class="booking-reports">
    <table class="booking-table">
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Room Name</th>
                <th>Username</th>
                <th>Name</th>
                <th>Email</th>
                <th>Telephone</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Guest Count</th>
                <th>Adult Count</th>
                <th>Child Count</th>
                <th>Payment Method</th>
                <th>Special Notes</th>
                <th>Booking Status</th>
                <th>Paid Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['booking_id']; ?></td>
                        <td><?php echo $row['room_name']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['telephone']; ?></td>
                        <td><?php echo $row['check_in']; ?></td>
                        <td><?php echo $row['check_out']; ?></td>
                        <td><?php echo $row['guest_count']; ?></td>
                        <td><?php echo $row['adult_count']; ?></td>
                        <td><?php echo $row['child_count']; ?></td>
                        <td><?php echo $row['payment_method']; ?></td>
                        <td><?php echo $row['special_notes']; ?></td>
                        <td><?php echo $row['booking_status']; ?></td>
                        <td><?php echo $row['advanced_payment']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="15">No bookings found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    
</section>

    <div class="total-advanced-payment">
        Total Amount: <?php echo number_format($total_advanced_payment, 2); ?>
    </div>

    <div class="print-options">
        <button onclick="showPrintPreview('full')">Full Print</button>
        <button onclick="showPrintPreview('payments')">Payments Print</button>
    </div>
</div>

<!-- Print Preview Modal -->
<div id="printPreviewModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Print Preview</h2>
        <div id="printArea"></div>
        <button onclick="confirmAndPrint()">Confirm and Print</button>
    </div>
</div>

<script>
function showPrintPreview(printType) {
    var modal = document.getElementById("printPreviewModal");
    var printArea = document.getElementById("printArea");
    var closeBtn = document.getElementsByClassName("close")[0];

    var content = generatePrintContent(printType);
    printArea.innerHTML = content;

    modal.style.display = "block";

    closeBtn.onclick = function() {
        modal.style.display = "none";
    };

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
}

function generatePrintContent(printType) {
    var content = "";
    var table = document.querySelector(".booking-table");
    var filterValue = "<?php echo $filter_value; ?>";
    var filterType = "<?php echo $filter; ?>";

    if (filterValue) {
        content += "<div class='filter-info'>Filtered by " + filterType + ": " + filterValue + "</div>";
    }

    if (printType === 'full') {
        content += "<table><thead><tr><th>Booking ID</th><th>Name</th><th>Check-In</th><th>Check-Out</th><th>Room Name</th></tr></thead><tbody>";
        var rows = table.querySelectorAll("tbody tr");
        rows.forEach(function(row) {
            var bookingId = row.cells[0].textContent;
            var name = row.cells[3].textContent;
            var checkIn = row.cells[6].textContent;
            var checkOut = row.cells[7].textContent;
            var roomName = row.cells[1].textContent;
            content += "<tr><td>" + bookingId + "</td><td>" + name + "</td><td>" + checkIn + "</td><td>" + checkOut + "</td><td>" + roomName + "</td></tr>";
        });
        content += "</tbody></table>";
    } else if (printType === 'payments') {
        content += "<table><thead><tr><th>Check-In Date</th><th>Check-Out Date</th><th>Booking ID</th><th>Total Payment</th><th>Paid Amount</th></tr></thead><tbody>";
        var rows = table.querySelectorAll("tbody tr");
        rows.forEach(function(row) {
            var checkIn = row.cells[6].textContent;
            var checkOut = row.cells[7].textContent;
            var bookingId = row.cells[0].textContent;
            var totalPayment = "<?php echo number_format($total_advanced_payment, 2); ?>";
            var paidAmount = row.cells[14].textContent;
            content += "<tr><td>" + checkIn + "</td><td>" + checkOut + "</td><td>" + bookingId + "</td><td>" + totalPayment + "</td><td>" + paidAmount + "</td></tr>";
        });
        content += "</tbody></table>";
    }

    return content;
}

function confirmAndPrint() {
    window.print();
}

function resetFilter() {
    window.location.href = window.location.pathname;
}
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="../assets/js/disable-click.js"></script>
</body>
</html>
