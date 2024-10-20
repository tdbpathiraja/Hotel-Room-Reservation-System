<?php
include 'assets/database-functions/dbconnection.php';

$roomID = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT * FROM roomdetails WHERE RoomID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $roomID);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();

$imagePaths = $room['CoverImage'] ?? '';
$imageArray = $imagePaths ? explode(',', $imagePaths) : [];

//Rating Showing Function
$roomName = $room['RoomName'];

// Query to calculate the average rating for the room
$stmt = $conn->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE room_name = ?");
$stmt->bind_param('s', $roomName);
$stmt->execute();
$result = $stmt->get_result();
$avgRating = 0;

if ($row = $result->fetch_assoc()) {
    $avgRating = round($row['avg_rating'], 1);
}

$stmt->close();

// Function to display stars based on rating
function displayStars($rating)
{
    $fullStars = floor($rating);
    $halfStar = $rating - $fullStars >= 0.5 ? true : false;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

    for ($i = 0; $i < $fullStars; $i++) {
        echo '<i class="icon_star"></i>';
    }

    if ($halfStar) {
        echo '<i class="icon_star-half_alt"></i>';
    }

    for ($i = 0; $i < $emptyStars; $i++) {
        echo '<i class="icon_star_alt"></i>';
    }
}

// Currency Conversiton API Connection
$api_url = 'https://v6.exchangerate-api.com/v6/dcc1e5d38b9c1254ecef1b28/latest/USD';
$response = file_get_contents($api_url);
$data = json_decode($response, true);
$exchange_rate = $data['conversion_rates']['LKR'];

$priceLKR = $room['PriceLKR'];
$priceUSD = $priceLKR / $exchange_rate;
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
    <?php include 'nav-bar.php';?>

    <!--=========== Room Details Breadcrumb ===========-->
    <div class="breadcrumb-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-text">
                        <h2>Room Details</h2>
                        <div class="bt-option">
                            <a href="/">Home</a>
                            <span>Rooms</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--=========== Room Details Section ===========-->
    <section class="room-details-section spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="room-details-item">
                    <!-- Main Image -->
                    <div class="main-image-container">
                        <img id="main-image" src="assets/img/gallery/rooms/<?php echo !empty($imageArray) ? $imageArray[0] : 'default.jpg'; ?>" alt="Room Image">
                    </div>

                    <!-- Thumbnails -->
                    <div class="thumbnails-container">
                        <?php if (!empty($imageArray)): ?>
                            <?php foreach ($imageArray as $image): ?>
                                <img class="thumbnail" src="assets/img/gallery/rooms/<?php echo $image; ?>" alt="Room Image Thumbnail" onclick="changeMainImage('<?php echo $image; ?>')">
                            <?php endforeach;?>
                        <?php else: ?>
                            <p>No images available.</p>
                        <?php endif;?>
                    </div>

                    <div class="rd-text">
                        <div class="rd-title">
                            <h3><?php echo $room['RoomName']; ?></h3>
                            <div class="rdt-right">
                                <div class="rating">
                                    <?php displayStars($avgRating);?>
                                </div>
                                <a href="booking-room.php">Booking Now</a>
                            </div>
                        </div>

                        <?php
//Assume $exchange_rate is already defined and $conn is your MySQLi connection object
$current_date = date('Y-m-d');

//Retrieve active promotions for today
$promo_sql = "SELECT * FROM promotions WHERE start_date <= '$current_date' AND end_date >= '$current_date'";
$promo_result = $conn->query($promo_sql);

$active_promotions = [];
while ($promo_row = $promo_result->fetch_assoc()) {
    $promo_rooms = explode(',', $promo_row['promo_applied_rooms']);
    $active_promotions[] = [
        'discount_percentage' => (float) $promo_row['discount_percentage'],
        'rooms' => $promo_rooms,
    ];
}

//Room price calculations
$priceLKR = $room['PriceLKR'];
$priceUSD = $priceLKR / $exchange_rate;

//Check for applicable promotions and calculate discounted price
foreach ($active_promotions as $promotion) {
    if (in_array($room['RoomID'], $promotion['rooms'])) {
        $discount = $promotion['discount_percentage'];
        $discounted_price = $priceLKR - ($priceLKR * ($discount / 100));
        $priceLKR = $discounted_price;
        $priceUSD = $priceLKR / $exchange_rate;
        break;
    }
}
?>

                        <!-- Display New (Discounted) Price -->
                        <h2><?php echo number_format($priceLKR, 2); ?> LKR<span> /Pernight</span></h2>
                        <p class="usd-room"><?php echo number_format($priceUSD, 2); ?> USD</p>

                        <!-- New Tag-based Room Details -->
                        <div class="room-details-tags">
                            <span class="room-tag"><strong>Size:</strong> <?php echo $room['Size']; ?>Sqft</span>
                            <span class="room-tag"><strong>A/C:</strong> <?php echo $room['ACAvailable'] ? 'Yes Available' : 'Not Available'; ?></span>
                            <span class="room-tag"><strong>Capacity:</strong> Max person <?php echo $room['Capacity']; ?></span>
                            <span class="room-tag"><strong>Bed:</strong> <?php echo $room['BedType']; ?></span>
                        </div>

                        <p class="f-para"><?php echo $room['Description']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="room-facilities">
                    <h3>Room Facilities</h3>
                    <ul>
                        <li><i class="fa-solid fa-wifi"></i> Free WiFi</li>
                        <li><i class="fa-solid fa-mug-saucer"></i> Tea Coffee maker</li>
                        <li><i class="fa-solid fa-toilet-paper"></i> Toilet paper</li>
                        <li><i class="fa-solid fa-server"></i> Towel</li>
                        <li><i class="fa-solid fa-shoe-prints"></i> Slippers</li>
                        <li><i class="fa-solid fa-chair"></i> Desk and Chair</li>
                        <li><i class="fa-solid fa-volume-xmark"></i> Sound Proofed</li>
                        <li><i class="fa-solid fa-restroom"></i> Attached Bathroom</li>
                        <li><i class="fa-solid fa-shower"></i> Hot Water Shower</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

    <?php $conn->close();?>


    <script>
document.addEventListener('DOMContentLoaded', function() {
    var imageArray = <?php echo json_encode($imageArray); ?>;
    var currentIndex = 0;
    var imageElement = document.getElementById('main-image');
    var delay = 5000;
    var scrollInterval;

    function changeMainImage(imageSrc) {
        imageElement.src = 'assets/img/gallery/rooms/' + imageSrc;
    }

    function autoScrollImages() {
        currentIndex = (currentIndex + 1) % imageArray.length;
        changeMainImage(imageArray[currentIndex]);
    }

    function startAutoScroll() {
        scrollInterval = setInterval(autoScrollImages, delay);
    }

    function stopAutoScroll() {
        clearInterval(scrollInterval);
    }

    function handleThumbnailClick(imageSrc) {
        stopAutoScroll();
        changeMainImage(imageSrc);
        setTimeout(startAutoScroll, 5000);
    }


    startAutoScroll();

    document.querySelectorAll('.thumbnail').forEach(function(thumbnail) {
        thumbnail.addEventListener('click', function() {
            handleThumbnailClick(this.src.split('/').pop());
        });
    });
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
      <script src="assets/js/main.js"></script>
      <script src="assets/js/disable-click.js"></script>
    </body>
  </html>