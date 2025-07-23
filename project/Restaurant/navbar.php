<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <script>
        function confirmLogout() {
            // Show confirmation message
            if (confirm("Are you sure you want to log out?")) {
                // Redirect to logout.php if confirmed
                window.location.href = "logout.php";
            }
        }
    </script>
</head>
<!-- <?php session_start();?> -->
<body>
    <nav class="col-md-2 sidebar d-flex flex-column">
        <h4 class="text-center"><?=$_SESSION['restaurant_name']?></h4>
        <a href="restaurant-dashboard.php">Dashboard</a>
        <a href="menu-management.php">Menu Management</a>
        <a href="seating-capacity.php">Seating Capacity<br>Availability Status<br>Business Hours</a>
        <a href="ordermange.php">Order Management</a>
        <a href="manage_feedback.php">Feedback</a>
        <!-- Add the JavaScript confirmation to the logout link -->
        <a href="javascript:void(0);" onclick="confirmLogout()">Logout</a>
    </nav>
</body>
</html>
