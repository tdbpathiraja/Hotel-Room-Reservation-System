<?php
session_start();

include '../assets/database-functions/dbconnection.php';

if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    header('Location:login.php');
    exit();
}


// Get pending bookings count
function getPendingBookingsCount($conn)
{
    $sql = "SELECT COUNT(*) as count FROM booking_details WHERE booking_status = 'Pending Payment' OR booking_status = 'Pending Verification'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Get total users count
function getTotalUsersCount($conn)
{
    $sql = "SELECT COUNT(*) as count FROM users";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Get running promotions count
function getRunningPromotionsCount($conn)
{
    $today = date('Y-m-d');
    $sql = "SELECT COUNT(*) as count FROM promotions WHERE start_date <= '$today' AND end_date >= '$today'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Get average rating
function getAverageRating($conn)
{
    $sql = "SELECT AVG(rating) as avg_rating FROM reviews";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return number_format($row['avg_rating'], 1);
}

$pendingBookings = getPendingBookingsCount($conn);
$totalUsers = getTotalUsersCount($conn);
$runningPromotions = getRunningPromotionsCount($conn);
$averageRating = getAverageRating($conn);

// Get available years for bookings
function getAvailableYears($conn)
{
    $sql = "SELECT DISTINCT YEAR(check_in) as year 
            FROM booking_details 
            WHERE booking_status IN ('Booked', 'Reviewed', 'checked out') 
            ORDER BY year ASC";
    $result = $conn->query($sql);
    $years = array();
    while ($row = $result->fetch_assoc()) {
        $years[] = $row['year'];
    }
    return $years;
}


// Get monthly booking counts for a specific year
function getMonthlyBookings($conn, $year)
{
    $sql = "SELECT MONTH(check_in) as month, COUNT(*) as count
                FROM booking_details
                WHERE YEAR(check_in) = ? AND booking_status IN ('Booked', 'Reviewed', 'checked out')
                GROUP BY MONTH(check_in)
                ORDER BY month ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $monthlyData = array_fill(1, 12, 0);
    while ($row = $result->fetch_assoc()) {
        $monthlyData[$row['month']] = $row['count'];
    }
    return array_values($monthlyData);
}

$availableYears = getAvailableYears($conn);
$currentYear = date('Y');
if (!in_array($currentYear, $availableYears)) {
    $currentYear = end($availableYears);
}
$monthlyBookings = getMonthlyBookings($conn, $currentYear);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredPassword = $_POST['password'];

    // Fetch the hashed password from the database
    $sql = "SELECT password_hash FROM safe_password LIMIT 1";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $hashedPassword = $row['password_hash'];

    // Verify the entered password against the hashed password
    if (password_verify($enteredPassword, $hashedPassword)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}

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

        

            <!-- Main Content -->
            <main class="col-md-8 ms-sm-auto col-lg-10 px-md-3 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    
                </div>

                <!-- Quick Stats -->
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Pending Bookings</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pendingBookings; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Users</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalUsers; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Running Promotions</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $runningPromotions; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Rating Average</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $averageRating; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-star fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bookings Overview -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Bookings Overview</h6>
                        <div class="dropdown no-arrow">
                            <select id="yearFilter" class="form-select form-select-sm" aria-label="Year filter">
                                <?php foreach ($availableYears as $year): ?>
                                    <option value="<?php echo $year; ?>" <?php echo $year == $currentYear ? 'selected' : ''; ?>><?php echo $year; ?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="bookingsChart"></canvas>
                        </div>
                    </div>
                </div>

                
            </main>

            


        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script src="../assets/js/disable-click.js"></script>

    <script>
        // Bookings Overview Chart
        var ctx = document.getElementById('bookingsChart').getContext('2d');
        var myChart;

        function updateChart(year, data) {
            if (myChart) {
                myChart.destroy();
            }

            myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Bookings',
                        data: data,
                        backgroundColor: 'rgba(78, 115, 223, 0.5)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Monthly Bookings for ' + year
                        }
                    }
                }
            });
        }

        // Initial chart update
        updateChart(<?php echo $currentYear; ?>, <?php echo json_encode($monthlyBookings); ?>);

        // Year filter change event
        document.getElementById('yearFilter').addEventListener('change', function() {
            var selectedYear = this.value;
            fetch('../assets/database-functions/admin/get_monthly_bookings.php?year=' + selectedYear)
                .then(response => response.json())
                .then(data => {
                    updateChart(selectedYear, data);
                });
        });
    </script>

    <script>
        document.querySelectorAll('.danger-zone-option').forEach(function (button) {
    button.addEventListener('click', function (event) {
        event.preventDefault();
        var actionUrl = this.getAttribute('href');

        // Show confirmation dialog
        if (confirm('Are you sure you want to perform this action? This is a dangerous operation.')) {
            var password = prompt('Please enter the safe password to proceed:');

            if (password) {
                // Validate password
                fetch('../assets/database-functions/admin/validate_safepassword.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'password': password
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        
                        fetch(actionUrl)
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    alert(result.message);
                                } else {
                                    alert(result.message);
                                }
                            });
                    } else {
                        alert('Invalid password. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }
    });
});

    </script>


<script>
    
    document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();

    var formData = new FormData(this);
    fetch('../assets/database-functions/admin/login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.href = data.redirect;
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
});

</script>

