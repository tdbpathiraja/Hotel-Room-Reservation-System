<?php
include 'assets/database-functions/dbconnection.php';

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

   <!--=========== Promotions Breadcrumb ===========-->
    <div class="breadcrumb-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-text">
                        <h2>Promotions</h2>
                        <div class="bt-option">
                            <a href="/">Home</a>
                            <span>Promotions</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!--=========== Promotions ===========-->
<section class="varsity-promotions">
  <div class="container">
    <div class="row">
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
            if ($endDate < $currentDate) {
                $deleteSql = "DELETE FROM promotions WHERE id = " . $row["id"];
                $conn->query($deleteSql);
            }
            continue;
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



<!--=========== Footer ===========-->
    <?php include 'footer.php'?>



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
