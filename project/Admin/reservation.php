<?php
include 'db.php'; // Include the database connection file

// Fetch the reservation data
$query = "SELECT bookings.booking_id, customers.first_name, customers.last_name, restaurants.restaurant_name, bookings.booking_date, bookings.booking_time, bookings.status 
          FROM bookings 
          JOIN customers ON bookings.cid = customers.id 
          JOIN restaurants ON bookings.rid = restaurants.id";
$result = pg_query($conn, $query);
$reservations = pg_fetch_all($result);



// Add reservation logic (for demonstration purposes, it's simple)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_reservation'])) {
    $customerName = $_POST['customer_name'];
    $restaurantName = $_POST['restaurant_name'];
    $reservationDate = $_POST['reservation_date'];
    $reservationTime = $_POST['reservation_time'];

    // Get the restaurant ID based on the name
    $restaurantQuery = "SELECT id FROM restaurants WHERE restaurant_name = '$restaurantName'";
    $restaurantResult = pg_query($conn, $restaurantQuery);
    $restaurantId = pg_fetch_result($restaurantResult, 0, 'id');

    // Insert the reservation into the bookings table
    $customerQuery = "SELECT id FROM customers WHERE first_name = '$customerName' LIMIT 1";
    $customerResult = pg_query($conn, $customerQuery);
    $customerId = pg_fetch_result($customerResult, 0, 'id');

    $insertQuery = "INSERT INTO bookings (cid, rid, booking_date, booking_time, status) 
                    VALUES ($customerId, $restaurantId, '$reservationDate', '$reservationTime', 'Pending')";
    pg_query($conn, $insertQuery);


}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reservations</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <script src="../../js/bootstrap.bundle.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include "navbar.html"; 
            // print_r($users);
            ?>            

            <!-- Main Content -->
            <main class="col-md-10 main-content">
                <div class="page-header">
                    <h3>Manage Reservations</h3>
                </div>

                <!-- Add Reservation Button -->
                <div class="container mt-4">
                    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addReservationModal">
                        Add New Reservation
                    </button>

                    <!-- Reservation Table -->
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer Name</th>
                                    <th>Restaurant</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $reservation): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($reservation['booking_id']); ?></td>
                                        <td><?php echo htmlspecialchars($reservation['first_name'] . ' ' . $reservation['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($reservation['restaurant_name']); ?></td>
                                        <td><?php echo htmlspecialchars($reservation['booking_date']); ?></td>
                                        <td><?php echo htmlspecialchars($reservation['booking_time']); ?></td>
                                        <td><?php echo htmlspecialchars($reservation['status']); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#editReservationModal">Edit</button>
                                            <button class="btn btn-sm btn-danger">Cancel</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add Reservation Modal -->
                <div class="modal fade" id="addReservationModal" tabindex="-1" aria-labelledby="addReservationModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addReservationModalLabel">Add Reservation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="addCustomerName" class="form-label">Customer Name</label>
                                        <input type="text" class="form-control" id="addCustomerName" name="customer_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="addRestaurantName" class="form-label">Restaurant</label>
                                        <input type="text" class="form-control" id="addRestaurantName" name="restaurant_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="addReservationDate" class="form-label">Date</label>
                                        <input type="date" class="form-control" id="addReservationDate" name="reservation_date" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="addReservationTime" class="form-label">Time</label>
                                        <input type="time" class="form-control" id="addReservationTime" name="reservation_time" required>
                                    </div>
                                    <button type="submit" name="add_reservation" class="btn btn-primary">Add Reservation</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Reservation Modal -->
                <div class="modal fade" id="editReservationModal" tabindex="-1" aria-labelledby="editReservationModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editReservationModalLabel">Edit Reservation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
            
                                <form>
                                    <div class="mb-3">
                                        <label for="editCustomerName" class="form-label">Customer Name</label>
                                        <input type="text" class="form-control" id="editCustomerName"  required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editRestaurantName" class="form-label">Restaurant</label>
                                        <input type="text" class="form-control" id="editRestaurantName"  required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editReservationDate" class="form-label">Date</label>
                                        <input type="date" class="form-control" id="editReservationDate" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editReservationTime" class="form-label">Time</label>
                                        <input type="time" class="form-control" id="editReservationTime" required>
                                    </div>
                                    <button type="submit" class="btn btn-success">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
