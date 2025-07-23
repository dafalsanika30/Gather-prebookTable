<?php session_start();?>
<script>
    function confirmLogout() {
                // Display confirmation alert
                var confirmLogout = confirm("Are you sure you want to logout?");
                if (confirmLogout) {
                    // Redirect to logout.php for session destruction
                    window.location.href = "logout.php";
                }
            }
</script>
<nav class="navbar navbar-expand-lg fixed-top" style="background-color: black;padding: 15px 15px;font-size: 15px;width: auto;">
    <div class="container-fluid">
        <!-- Brand Logo -->
        <a class="navbar-brand" href="#" style="color: white;">
            <img src="Photos/dinner.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top" >
            Gather
        </a>
  
        <!-- Toggler for Mobile View -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon" style="border-color:grey ;"></span>
        </button>
  
        <!-- Collapsible Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Left Aligned Links -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="bookatable.php">Book a Table</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="aboutus.php">About Us</a>
                </li>

                <?php
                    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
                        // User is logged in, show Logout button
                        echo '<li class="nav-item">
                                <a class="nav-link" href="bookingdetails.php">My Bookings</a>
                            </li>';
                    }
                ?>

            </ul>
  
            <!-- Right Aligned Login/Logout Button -->
             
            <ul class="navbar-nav ms-auto">
            <?php
                if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
                    // User is logged in, show Logout button
                    echo '<li class="nav-item">
                            <a class="nav-link btn btn-light btn-lg" onclick="confirmLogout()">Logout</a>
                            </li>';

                } else {
                    // User is not logged in, show Login/Signup button with the applied styles
                    echo '<li class="nav-item dropdown">
                            <a class="nav-link btn btn-light btn-lg" href="login.html"">
                                Login/Sign Up
                            </a>
                        </li>';
                }
                ?>


            </ul>
        </div>
    </div>
</nav>