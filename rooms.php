<?php
include 'assets/database-functions/dbconnection.php';

// Currency Conversiton API Connection
$api_url = 'https://v6.exchangerate-api.com/v6/dcc1e5d38b9c1254ecef1b28/latest/USD';
$response = file_get_contents($api_url);
$data = json_decode($response, true);
$exchange_rate = $data['conversion_rates']['LKR'];

$sql = "SELECT * FROM roomdetails";
$result = $conn->query($sql);
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

    <?php include 'nav-bar.php';?>

    <!--=========== Rooms Breadcrumb ===========-->
    <div class="breadcrumb-section">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <div class="breadcrumb-text">
              <h2>Our Rooms</h2>
              <div class="bt-option">
                <a href="/">Home</a>
                <span>Rooms</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


<section class="rooms-section spad">
  <div class="container">
    <div class="row">
      <?php
// Assume $exchange_rate is already defined and $conn is your MySQLi connection object
$current_date = date('Y-m-d');

// Retrieve active promotions for today
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

// Retrieve room details
$sql = "SELECT * FROM roomdetails";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $priceLKR = $row['PriceLKR'];
    $originalPriceLKR = $priceLKR;
    $priceUSD = $priceLKR / $exchange_rate;
    $capacity = $row['Capacity'];
    $roomType = '';

    // Determine room type based on capacity
    if ($capacity == 2) {
        $roomType = 'Double Room';
    } elseif ($capacity == 3) {
        $roomType = 'Triple Room';
    }

    // Check for applicable promotions and calculate discounted price
    foreach ($active_promotions as $promotion) {
        if (in_array($row['RoomID'], $promotion['rooms'])) {
            $discount = $promotion['discount_percentage'];
            $discounted_price = $priceLKR - ($priceLKR * ($discount / 100));
            $priceLKR = $discounted_price;
            $priceUSD = $priceLKR / $exchange_rate;
            break;
        }
    }
    ?>
        <div class="col-lg-4 col-md-6">
          <div class="room-item">
            <img src="assets/img/gallery/rooms/<?php echo htmlspecialchars($row['RoomCardImg']); ?>" alt="" />
            <div class="ri-text">
              <h4><?php echo htmlspecialchars($row['RoomName']); ?></h4>
              <span class="room-tag"><?php echo $row['ACAvailable'] ? 'WITH A/C' : 'NON A/C'; ?></span>

              <?php if ($roomType): ?>
                <span class="room-tag"><?php echo htmlspecialchars($roomType); ?></span>
              <?php endif;?>

              <!-- Display Old Price if Discount Applied -->
              <?php if ($priceLKR < $originalPriceLKR): ?>
                <p class="old-price" style="text-decoration: line-through; color: red;">
                  <?php echo number_format($originalPriceLKR, 2); ?> LKR
                </p>
              <?php endif;?>

              <!-- Display New (Discounted) Price -->
              <h3><?php echo number_format($priceLKR, 2); ?> LKR<span> /Pernight</span></h3>
              <p class="usd-room"><?php echo number_format($priceUSD, 2); ?> USD</p>

              <a href="room-details.php?id=<?php echo $row['RoomID']; ?>" class="roommore-btn">More Details</a>
              <br /><br />
              <a href="booking-room.php" class="roombook-btn">Book Now</a>
            </div>
          </div>
        </div>
      <?php
}
?>
    </div>
  </div>
</section>




    <?php $conn->close();?>


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
