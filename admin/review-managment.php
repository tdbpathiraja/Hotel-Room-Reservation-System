<?php
session_start();

include '../assets/database-functions/dbconnection.php';

if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    header('Location:login.php');
    exit();
}

// Get all reviews
function getAllReviews($conn)
{
    $sql = "SELECT * FROM reviews ORDER BY review_date DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get room categories with review statistics
function getRoomCategoryStats($conn)
{
    $sql = "SELECT 
                r.room_name,
                COUNT(*) as total_reviews,
                AVG(r.rating) as average_rating,
                SUM(CASE WHEN r.rating = 1 THEN 1 ELSE 0 END) as one_star,
                SUM(CASE WHEN r.rating = 2 THEN 1 ELSE 0 END) as two_star,
                SUM(CASE WHEN r.rating = 3 THEN 1 ELSE 0 END) as three_star,
                SUM(CASE WHEN r.rating = 4 THEN 1 ELSE 0 END) as four_star,
                SUM(CASE WHEN r.rating = 5 THEN 1 ELSE 0 END) as five_star
            FROM reviews r
            GROUP BY r.room_name";
    $result = $conn->query($sql);
    $stats = $result->fetch_all(MYSQLI_ASSOC);
    
    // Ensure all star ratings exist, even if they're zero
    foreach ($stats as &$stat) {
        for ($i = 1; $i <= 5; $i++) {
            $key = $i . '_star';
            if (!isset($stat[$key])) {
                $stat[$key] = 0;
            }
        }
    }
    
    return $stats;
}

$reviews = getAllReviews($conn);
$roomStats = getRoomCategoryStats($conn);

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
    <link href="../assets/css/admin-dashboard.css" rel="stylesheet">
    
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="home-section">
    <div class="home-content">
        <div class="dashboard-header d-flex justify-content-between align-items-center">
            <h1 class="dashboard-title"> Client Reviews</h1>
            <!-- <button type="button" class="add-btn" data-bs-toggle="modal" data-bs-target="#addGuestModal">
                <i class="fas fa-plus"></i> Add New Guest
            </button> -->
        </div>
        
        <!-- Room Category Statistics -->
        <div class="room-stats mb-5">
            <h2 class="mb-3">Statistics</h2>
            <div class="row">
                <?php if (!empty($roomStats)): ?>
                    <?php foreach ($roomStats as $stat): ?>
                        <div class="col-md-4 mb-3">
                            <div class="stat-card">
                                <h3><?php echo htmlspecialchars($stat['room_name']); ?></h3>
                                <p>Total Reviews: <?php echo $stat['total_reviews']; ?></p>
                                <p>Average Rating: <?php echo number_format($stat['average_rating'], 1); ?></p>
                                
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col">
                        <p>No room statistics available.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Star Rating Filter -->
        <div class="filter-section mb-4">
            <h2>Filter Reviews</h2>
            <div class="star-filter">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <label class="star-checkbox">
                        <input type="checkbox" value="<?php echo $i; ?>" checked> <?php echo $i; ?> Star
                    </label>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Review Cards -->
        <div class="row" id="reviewContainer">
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="col-md-4 mb-4 review-item" data-rating="<?php echo $review['rating']; ?>">
                        <div class="review-card">
                            <div class="review-header">
                                <h5 class="room-name"><?php echo htmlspecialchars($review['room_name']); ?></h5>
                                <div class="rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'filled' : ''; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="review-body">
                                <p class="review-text"><?php echo htmlspecialchars($review['review_text']); ?></p>
                            </div>
                            <div class="review-footer">
                                <span class="reviewer-name"><?php echo htmlspecialchars($review['name']); ?></span>
                                <span class="review-date"><?php echo date('M d, Y', strtotime($review['review_date'])); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col">
                    <p>No reviews available.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/disable-click.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterCheckboxes = document.querySelectorAll('.star-filter input[type="checkbox"]');
    const reviewItems = document.querySelectorAll('.review-item');

    function filterReviews() {
        const checkedRatings = Array.from(filterCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => parseInt(checkbox.value));

        reviewItems.forEach(item => {
            const rating = parseInt(item.getAttribute('data-rating'));
            if (checkedRatings.includes(rating)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    filterCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', filterReviews);
    });
});
</script>
</body>
</html>