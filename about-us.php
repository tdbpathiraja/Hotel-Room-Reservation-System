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

    <!--=========== Navigation Bar ===========-->
    <?php include 'nav-bar.php';?>

    <!--=========== About Us Breadcrumb ===========-->
    <div class="breadcrumb-section">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <div class="breadcrumb-text">
              <h2>About Us</h2>
              <div class="bt-option">
                <a href="index.php">Home</a>
                <span>About Us</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!--=========== Lodge History and Highlights ===========-->
    <section class="aboutus-page-section spad">
      <div class="container">
        <div class="about-page-text">
          <div class="row">
            <div class="col-lg-6">
              <div class="ap-title">
                <h2>Welcome To Varsity Lodge.</h2>
                <p>
                This guest house provides free private parking and a
               24-hour front desk. The guest house features family rooms.
               Featuring a private bathroom, units at the guest house also provide guests with free WiFi, while some rooms will provide you with a balcony. At the guest house, some units are soundproof.
               Bogambara Stadium is 7 km from the guest house, while Kandy City Center Shopping Mall is 7.2 km away.
                </p>
              </div>
            </div>
            <div class="col-lg-5 offset-lg-1">
              <ul class="ap-services">
                <li><i class="icon_check"></i> Reasonable Prices</li>
                <li><i class="icon_check"></i> Valuble Promotions</li>
                <li><i class="icon_check"></i> 24/7 Customer Support</li>
                <li><i class="icon_check"></i> Prime Location Near Attractions</li>
                <li><i class="icon_check"></i> Spacious and Comfortable Rooms</li>
              </ul>
            </div>
          </div>
        </div>
        <!-- <div class="about-page-services">
          <div class="row">
            <div class="col-md-4">
              <div
                class="ap-service-item set-bg"
                data-setbg="assets/img/about/IMG_0065.jpg"
              >
                <div class="api-text">
                  <h3>Peaceful Location</h3>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div
                class="ap-service-item set-bg"
                data-setbg="assets/img/about/about-p2.jpg"
              >
                <div class="api-text">
                  <h3>Travel & Camping</h3>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div
                class="ap-service-item set-bg"
                data-setbg="assets/img/about/about-p3.jpg"
              >
                <div class="api-text">
                  <h3>Event & Party</h3>
                </div>
              </div>
            </div>
          </div>
        </div> -->
      </div>
    </section>

    <!--=========== Video Block ===========-->
    <section class="video-section set-bg" data-setbg="assets/img/video-bg.jpg">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <div class="video-text">
              <h2>Discover Our Hotel & Services.</h2>
              <p>
                We always try to give best what we have
              </p>
              <a
                href="https://youtu.be/yR0VJBZcsqI?si=_Xtu3I6hSiLSgNzR"
                class="play-btn video-popup"
                ><img src="assets/img/play.png" alt=""
              /></a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!--=========== Gallery ===========-->
    <section class="gallery-section spad">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="section-title">
          <span>Gallery</span>
          <h2>Our Background</h2>
        </div>
      </div>
    </div>
    <div class="row">
      <?php
include 'assets/database-functions/dbconnection.php';

$sql = "SELECT image_url, title FROM gallery";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="col-lg-6">';
        echo '  <div class="gallery-item set-bg" data-setbg="assets/img/gallery/' . htmlspecialchars($row['image_url']) . '">';
        echo '    <div class="gi-text">';
        echo '      <h3>' . htmlspecialchars($row['title']) . '</h3>';
        echo '    </div>';
        echo '  </div>';
        echo '</div>';
    }
} else {
    echo '<p style="text-align: center; font-weight: bold; color: #333;">Gallery still Updating...</p>';
}

$conn->close();
?>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/disable-click.js"></script>
  </body>
</html>
