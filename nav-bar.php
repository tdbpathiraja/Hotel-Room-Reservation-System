<?php
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>


<div class="offcanvas-menu-overlay"></div>
<div class="canvas-open">
  <i class="icon_menu"></i>
</div>
<div class="offcanvas-menu-wrapper">
  <div class="canvas-close">
    <i class="icon_close"></i>
  </div>

  <div class="header-configure-area">
    <a href="booking-room" class="bk-btn">Booking Now</a>
    <i class="fa-regular fa-user" id="profile-icon" style="margin-left: 30px; cursor: pointer" onclick="checkLoginStatus();"></i>

  </div>
  <nav class="mainmenu mobile-menu">
    <ul>
      <li class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>"><a href="index">Home</a></li>
      <li class="<?php echo $current_page == 'rooms.php' ? 'active' : ''; ?>"><a href="rooms">Rooms</a></li>
      <li class="<?php echo $current_page == 'about-us.php' ? 'active' : ''; ?>"><a href="about-us">About Us</a></li>
      <li class="<?php echo $current_page == 'promotions.php' ? 'active' : ''; ?>"><a href="promotions">Promotions</a></li>
      <li class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>"><a href="contact">Contact</a></li>
    </ul>
  </nav>
  <div id="mobile-menu-wrap"></div>
  <div class="top-social">
    <a href="https://www.facebook.com/stayvarsity"><i class="fa fa-facebook"></i></a>
    <a href="https://www.instagram.com/stayvarsity" target="_blank"><i class="fa fa-instagram"></i></a>
    <a href="#"><i class="fa fa-youtube-play"></i></a>
  </div>
  <ul class="top-widget">
    <li><i class="fa fa-whatsapp"></i> (+94) 760302151</li>
    <li><i class="fa fa-envelope"></i> hello@varsitylodge.com</li>
  </ul>
</div>



<header class="header-section">
  <div class="top-nav">
    <div class="container">
      <div class="row">
        <div class="col-lg-6">
          <ul class="tn-left">
            <li><i class="fa fa-whatsapp"></i> (+94) 760302151</li>
            <li><i class="fa fa-envelope"></i> hello@varsitylodge.com</li>
          </ul>
        </div>
        
      
        <div class="col-lg-6">
          <div class="tn-right">

            <div class="top-social">
              <a href="https://www.facebook.com/stayvarsity" target="_blank"><i class="fa fa-facebook"></i></a>
              <a href="https://www.instagram.com/stayvarsity" target="_blank"><i class="fa fa-instagram"></i></a>
              <a href="#"><i class="fa fa-youtube-play"></i></a>
            </div>

            

            <a href="booking-room.php" class="bk-btn">Booking Now</a>
            <i class="fa-regular fa-user" id="profile-icon" style="margin-left: 30px; cursor: pointer" onclick="checkLoginStatus();"></i>
            
            

          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="menu-item">
    <div class="container">
      <div class="row">
        <div class="col-lg-2">
          <div class="logo">
            <a href="index.php">
              <img src="assets/img/versity-lodge-main.png" alt="Veristy Lodge Logo" />
            </a>
          </div>
        </div>
        <div class="col-lg-10">
          <div class="nav-menu">
            <nav class="mainmenu">
              <ul>
                <li class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>"><a href="index">Home</a></li>
                <li class="<?php echo $current_page == 'rooms.php' ? 'active' : ''; ?>"><a href="rooms">Rooms</a></li>
                <li class="<?php echo $current_page == 'about-us.php' ? 'active' : ''; ?>"><a href="about-us">About Us</a></li>
                <li class="<?php echo $current_page == 'promotions.php' ? 'active' : ''; ?>"><a href="promotions">Promotions</a></li>
                <li class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>"><a href="contact">Contact</a></li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>


<script>
  function checkLoginStatus() {

    if (document.cookie.includes('username=')) {
      window.location.href = 'my-account';
    } else {
      window.location.href = 'visitor-login';
    }
  }
</script>
