<nav>
      <div class="logo">
        <i class="bx bx-menu menu-icon"></i>
        <span class="logo-name">Varsity Lodge Admin Dashboard</span>
      </div>
      <div class="sidebar">
        <div class="logo">
          <i class="bx bx-menu menu-icon"></i>
          <span class="logo-name">Varsity Lodge</span>
        </div>
        <div class="sidebar-content">
          <ul class="lists">
            <li class="list">
              <a href="index.php" class="nav-link">
                <i class="bx bxs-dashboard icon"></i>
                <span class="link">Dashboard</span>
              </a>
            </li>
            <li class="list">
              <a href="reservation-managment.php" class="nav-link">
              <i class='bx bx-calendar-check icon'></i>
                <span class="link">Reservation</span>
              </a>
            </li>
            <li class="list">
              <a href="guest-managment.php" class="nav-link">
                <i class="bx bx-user icon"></i>
                <span class="link">Guest Management</span>
              </a>
            </li>
            <li class="list">
              <a href="room-managment.php" class="nav-link">
                <i class="bx bx-home-smile icon"></i>
                <span class="link">Rooms Management</span>
              </a>
            </li>
            <li class="list">
              <a href="gallery-managment.php" class="nav-link">
                <i class="bx bx-image-alt icon"></i>
                <span class="link">Lodge Gallery</span>
              </a>
            </li>
            <li class="list">
              <a href="review-managment.php" class="nav-link">
                <i class="bx bx-heart icon"></i>
                <span class="link">Reviews</span>
              </a>
            </li>
            <li class="list">
              <a href="promotions-managment.php " class="nav-link">
                <i class="bx bx-gift icon"></i>
                <span class="link">Promotions</span>
              </a>
            </li>
          </ul>
          <div class="bottom-cotent">
            <li class="list">
              <a href="reports.php" class="nav-link">
                <i class="bx bx-line-chart icon"></i>
                <span class="link">Reports</span>
              </a>
            </li>
            
            
          </div>
        </div>
      </div>
    </nav>


    <script>
        const navBar = document.querySelector("nav"),
       menuBtns = document.querySelectorAll(".menu-icon"),
       overlay = document.querySelector(".overlay");

     menuBtns.forEach((menuBtn) => {
       menuBtn.addEventListener("click", () => {
         navBar.classList.toggle("open");
       });
     });

     overlay.addEventListener("click", () => {
       navBar.classList.remove("open");
     });
    </script>