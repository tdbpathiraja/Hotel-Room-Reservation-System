<?php

include 'assets/database-functions/dbconnection.php';

// Currency Conversiton API Connection
$api_url = 'https://v6.exchangerate-api.com/v6/dcc1e5d38b9c1254ecef1b28/latest/USD';
$response = file_get_contents($api_url);
$data = json_decode($response, true);
$exchange_rate = $data['conversion_rates']['LKR'];
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
    <!-- <div id="preloder">
      <img
        src="assets/img/Pre-Loder.png"
        alt="Preloader Image"
        class="preloader-image"
      />
      <div class="loader"></div>
    </div> -->

    <!--=========== Navigation Bar ===========-->
    <?php include 'nav-bar.php';?>


    <!--=========== Hero Section ===========-->
<section class="hero-section">
  <div class="container">
    <div class="row">
      <div class="col-lg-6">
        <div class="hero-text">
          <h1>Varsity Lodge A Luxury Stay</h1>
          <p>
            Welcome to Varsity Lodge, where luxury and comfort meet. Nestled in the heart of Peradeniya, near Kandy, our hotel offers a serene escape for business or leisure.
          </p>
          <a href="booking-room.php" class="hero-btn">Book Your Stay</a>
        </div>
      </div>
    </div>
  </div>

  <div class="hero-slider owl-carousel">
    <div class="hs-item set-bg" data-setbg="assets/img/hero/hero-01.jpg"></div>
    <div class="hs-item set-bg" data-setbg="assets/img/hero/hero-02.jpg"></div>
    <div class="hs-item set-bg" data-setbg="assets/img/hero/hero-03.jpg"></div>
  </div>
</section>


    <!--=========== About Summery ===========-->
    <section class="aboutus-section spad">
      <div class="container">
        <div class="row">
          <div class="col-lg-6">
            <div class="about-text">
              <div class="section-title">
                <span>About Us</span>
                <h2>Varsity Lodge</h2>
                <h4>Peradeniya</h4>
              </div>
              <p class="f-para">
              This guest house provides free private parking and a
               24-hour front desk. The guest house features family rooms.
               Featuring a private bathroom, units at the guest house also provide guests with free WiFi, while some rooms will provide you with a balcony. At the guest house, some units are soundproof.
               Bogambara Stadium is 7 km from the guest house, while Kandy City Center Shopping Mall is 7.2 km away.
              </p>
              <p class="s-para">
                So when it comes to booking the perfect hotel, vacation rental,
                resort, apartment, guest house, or tree house, we’ve got you
                covered.
              </p>
              <a href="about-us.php" class="primary-btn about-btn">Read More</a>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="about-pic">
              <div class="row">
                <div class="col-sm-6">
                  <img src="assets/img/about/IMG_0064.jpg" alt="" />
                </div>
                <div class="col-sm-6">
                  <img src="assets/img/about/IMG_1978.jpg" alt="" />
                </div>
                
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!--=========== Services ===========-->
<section class="services-section spad">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="section-title">
          <span>What We Offer</span>
          <h2>Every Stay</h2>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-4 col-sm-6">
        <div class="service-item">
          <i
            class="fi fi-rr-wifi"
            style="
              font-family: Flaticon;
              font-size: 50px;
              font-style: normal;
              margin-left: 0;
            "
          ></i>
          <h4>FREE WIFI</h4>
          <p>
            Stay connected with our complimentary high-speed Wi-Fi available throughout the property. Whether you need to catch up on work or stream your favorite shows, we've got you covered.
          </p>
        </div>
      </div>
      <div class="col-lg-4 col-sm-6">
        <div class="service-item">
          <i
            class="fi fi-rr-car"
            style="
              font-family: Flaticon;
              font-size: 50px;
              font-style: normal;
              margin-left: 0;
            "
          ></i>
          <h4>Parking</h4>
          <p>
            Enjoy the convenience of free on-site parking. Whether you're arriving by car or renting one during your stay, rest assured that your vehicle is secure and easily accessible.
          </p>
        </div>
      </div>
      <div class="col-lg-4 col-sm-6">
        <div class="service-item">
          <i
            class="fi fi-rr-doctor"
            style="
              font-family: Flaticon;
              font-size: 50px;
              font-style: normal;
              margin-left: 0;
            "
          ></i>
          <h4>Medical Support</h4>
          <p>
            Your health and well-being are our priority. We provide access to on-call medical support for any health concerns that may arise during your stay, ensuring peace of mind for all our guests.
          </p>
        </div>
      </div>
      <div class="col-lg-4 col-sm-6">
        <div class="service-item">
          <i
            class="fi fi-rr-spray-can-sparkles"
            style="
              font-family: Flaticon;
              font-size: 50px;
              font-style: normal;
              margin-left: 0;
            "
          ></i>

          <h4>Clean Rooms</h4>
          <p>
            Experience the comfort of our meticulously cleaned rooms. We adhere to the highest standards of hygiene, ensuring a safe and pleasant environment for your stay.
          </p>
        </div>
      </div>
      <div class="col-lg-4 col-sm-6">
        <div class="service-item">
          <i
            class="fi fi-rr-leaf"
            style="
              font-family: Flaticon;
              font-size: 50px;
              font-style: normal;
              margin-left: 0;
            "
          ></i>
          <h4>Peaceful Nature</h4>
          <p>
            Unwind in the serene natural surroundings of Varsity Lodge. Our location offers a tranquil escape, perfect for relaxation and rejuvenation amidst the beauty of Peradeniya and Kandy.
          </p>
        </div>
      </div>
      <div class="col-lg-4 col-sm-6">
        <div class="service-item">
          <i
            class="fi fi-rr-map-marker"
            style="
              font-family: Flaticon;
              font-size: 50px;
              font-style: normal;
              margin-left: 0;
            "
          ></i>
          <h4>Near to All</h4>
          <p>
            Discover the convenience of being close to all major attractions and transportation hubs. Varsity Lodge is ideally located near Peradeniya and Kandy, making it easy to explore the rich culture and natural beauty of the area.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>


<!--=========== Rooms ===========-->
<section class="hp-room-section">
  <div class="container-fluid">
    <div class="hp-room-items">
      <div class="row">
        <?php

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

//Retrieve room details
$sql = "SELECT * FROM roomdetails";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $priceLKR = $row['PriceLKR'];
    $originalPriceLKR = $priceLKR;
    $priceUSD = $priceLKR / $exchange_rate;
    $capacity = $row['Capacity'];
    $roomType = '';

    if ($capacity == 2) {
        $roomType = 'Double Room';
    } elseif ($capacity == 3) {
        $roomType = 'Triple Room';
    }

    //Check for applicable promotions and calculate discounted price
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

        <div class="col-lg-3 col-md-6">
          <div class="hp-room-item set-bg" data-setbg="assets/img/gallery/rooms/<?php echo htmlspecialchars($row['RoomCardImg']); ?>">
            <div class="hr-text">
              <h3><?php echo htmlspecialchars($row['RoomName']); ?></h3>
              <span class="homeroom-tag"><?php echo $row['ACAvailable'] ? 'WITH A/C' : 'NON A/C'; ?></span>

              <?php if ($roomType): ?>
                <span class="homeroom-tag"><?php echo htmlspecialchars($roomType); ?></span>
              <?php endif;?>

              <!-- Display Old Price if Discount Applied -->
              <?php if ($priceLKR < $originalPriceLKR): ?>
                <p class="old-price" style="text-decoration: line-through; color: red;">
                  <?php echo number_format($originalPriceLKR, 2); ?> LKR
                </p>
              <?php endif;?>

              <!-- Display New (Discounted) Price -->
              <h2><?php echo number_format($priceLKR, 2); ?> LKR </h2>
              <p class="usd-room"><?php echo number_format($priceUSD, 2); ?> USD</p>

              <table>
                <tbody>
                  <tr>
                    <td class="r-o">Size:</td>
                    <td><?php echo htmlspecialchars($row['Size']); ?></td>
                  </tr>
                  <tr>
                    <td class="r-o">Capacity:</td>
                    <td>Max persons <?php echo htmlspecialchars($row['Capacity']); ?></td>
                  </tr>
                  <tr>
                    <td class="r-o">Bed:</td>
                    <td><?php echo htmlspecialchars($row['BedType']); ?></td>
                  </tr>
                </tbody>
              </table>
              <a href="room-details.php?id=<?php echo $row['RoomID']; ?>" class="primary-btn">More Details</a>
            </div>
          </div>
        </div>

        <?php
}
?>
      </div>
    </div>
  </div>
</section>



<!--=========== Promotions ===========-->
<section class="varsity-promotions">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="section-title">
          <span>Varsity Lodge</span>
          <h2>Promotions</h2>
        </div>
      </div>

      <?php
$currentDate = new DateTime();

// Query to select promotions
$sql = "SELECT * FROM promotions";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $startDate = new DateTime($row["start_date"]);
        $endDate = new DateTime($row["end_date"]);

        // Check if the promotion should be shown
        if ($endDate < $currentDate || $startDate > $currentDate) {
            // If the promotion has ended or hasn't started, delete or skip it
            if ($endDate < $currentDate) {
                $deleteSql = "DELETE FROM promotions WHERE id = " . $row["id"];
                $conn->query($deleteSql);
            }
            continue; // Skip this promotion
        }

        $interval = $currentDate->diff($endDate);
        $daysLeft = $interval->days;
        $isUpcoming = $endDate > $currentDate && $daysLeft <= 10;

        // Display promotion if it is currently running
        echo '<div class="col-md-4">';
        echo '  <div class="promotion-card">';
        echo '    <img src="assets/img/promotions/' . htmlspecialchars($row["image"]) . '" alt="Promotion Image" class="promotion-image">';
        echo '    <div class="promotion-content">';
        echo '      <h3 class="promotion-title">' . htmlspecialchars($row["title"]) . '</h3>';
        echo '      <p class="promotion-dates">';
        if ($isUpcoming) {
            echo '        <span class="countdown">Only ' . $daysLeft . ' days left!</span>';
        } else {
            echo '        <span class="end-date">Ends on ' . htmlspecialchars($row["end_date"]) . '</span>';
        }
        echo '      </p>';
        echo '      <p class="promotion-discount">' . htmlspecialchars($row["discount_percentage"]) . '% OFF</p>';
        echo '      <p class="promotion-description">' . htmlspecialchars($row["description"]) . '</p>';
        echo '    </div>';
        echo '  </div>';
        echo '</div>';
    }
} else {
    echo '<p style="text-align: center; font-weight: bold; color: #333;">No Promotions these days!!</p>';
}
?>
    </div>
  </div>
</section>


    <!--=========== Testimonials ===========-->
<section class="testimonial-section spad">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="section-title">
          <span>Testimonials</span>
          <h2>What Customers Say?</h2>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-8 offset-lg-2">
        <div class="testimonial-slider owl-carousel">
          <?php
// Fetching testimonials from the Reviews table
$sql = "SELECT name, rating, review_text FROM reviews ORDER BY review_date DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $name = htmlspecialchars($row['name']);
        $rating = intval($row['rating']);
        $review_text = htmlspecialchars($row['review_text']);

        // Display each testimonial
        echo '<div class="ts-item">';
        echo '<p>' . $review_text . '</p>';
        echo '<div class="ti-author">';
        echo '<div class="rating">';

        // Display star ratings
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                echo '<i class="icon_star"></i>';
            } else {
                echo '<i class="icon_star_alt"></i>';
            }
        }

        echo '</div>';
        echo '<h5>- ' . $name . '</h5>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo '<p>No testimonials available at the moment.</p>';
}
?>
        </div>
      </div>
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
    <script
      src="https://kit.fontawesome.com/bc9230b11f.js"
      crossorigin="anonymous"
    ></script>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/disable-click.js"></script>



  </body>
</html>
