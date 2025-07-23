<?php
include '../db.php'; // Include the database connection file
session_start(); // Start the session

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

// Fetch data from the database
$totalUsers = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM customers"), 0, 0);
$totalReservations = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM bookings"), 0, 0);
$totalFeedback = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM feedback"), 0, 0); // Assuming feedback table exists
$totalRestaurants = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM restaurants"), 0, 0);

// Fetch recent reservations
$recentReservations = pg_query($conn, "
    SELECT 
        bookings.booking_date, 
        bookings.booking_time, 
        restaurants.restaurant_name, 
        bookings.num_guests,
        bookings.status
    FROM 
        bookings
    JOIN 
        restaurants ON bookings.rid = restaurants.id
    ORDER BY 
        bookings.created_at DESC
    LIMIT 3
");

// Fetch recent feedback (assuming a feedback table exists)
$recentFeedback = pg_query($conn, "
    SELECT 
        restaurants.restaurant_name, 
        feedback_text 
    FROM 
        feedback
    JOIN 
        restaurants ON feedback.restaurant_id = restaurants.id
    ORDER BY 
        feedback.created_at DESC
    LIMIT 3
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 sidebar d-flex flex-column">
                <h4 class="text-center">Admin Dashboard</h4>
                <a href="admin.html">Dashboard</a>
                <a href="users.php">Manage Users</a>
                <a href="rest.html">Manage Restaurants</a>
                <a href="reservation.html">Reservations</a>
                <a href="feedback.html">Feedback</a>
                <a href="analytics.html">Analytics</a>
                <a href="../logout.php">Logout</a>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 main-content">
                <div class="page-header">
                    <h3>Welcome to Admin Dashboard!</h3>
                </div>

                <!-- Overview Cards -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5>Total Users</h5>
                                <h3><?php echo $totalUsers; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5>Total Reservations</h5>
                                <h3><?php echo $totalReservations; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5>Total Feedback</h5>
                                <h3><?php echo $totalFeedback; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5>Total Restaurants</h5>
                                <h3><?php echo $totalRestaurants; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Reservations -->
                <div class="row mt-5">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Recent Reservations</h5>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <?php while ($reservation = pg_fetch_assoc($recentReservations)): ?>
                                        <li>
                                            Reservation at <?php echo $reservation['restaurant_name']; ?> 
                                            for <?php echo $reservation['num_guests']; ?> guests at 
                                            <?php echo date('g:i A', strtotime($reservation['booking_time'])); ?> 
                                            on <?php echo $reservation['booking_date']; ?> 
                                            (Status: <?php echo $reservation['status']; ?>)
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Feedback -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Recent Feedback</h5>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <?php while ($feedback = pg_fetch_assoc($recentFeedback)): ?>
                                        <li>
                                            <?php echo $feedback['restaurant_name']; ?>: 
                                            "<?php echo $feedback['feedback_text']; ?>"
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <button class="navbar-toggler d-md-none" type="button" id="sidebarToggle">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Bootstrap JS -->
    <script src="../js/bootstrap.bundle.min.js"></script>

    <script>
        // Sidebar Toggle Functionality
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');

        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('d-none');
        });
    </script>

</body>
</html>

<?php
pg_close($conn); // Close the database connection
?>
