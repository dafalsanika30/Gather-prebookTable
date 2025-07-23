<?php 
include "db.php"; 
session_start();

// if (!isset($_SESSION['id'])) {
//     echo "You are not logged in. Please log in to view your bookings.";
//     exit;
// }

$userId = $_SESSION['id'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Booking</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="CSS/home.css" rel="stylesheet">
</head>
<body>
    <?php include "navbar.php"; ?>
    
    <section class="container mt-5" style="max-width: 100%; margin-top: 100px;">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow" style="margin-top: 80px; max-width: 100%;">
                    <div class="card-header text-center bg-black text-white">
                        <h4>My Booking Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-bordered table-striped text-center" style="width: 100%;">
                                <thead class="bg-secondary text-white">
                                    <tr>
                                        <th>Day</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Menu Items</th>
                                        <th>Restaurant</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Database connection for PostgreSQL
                                    
                                        // Fetch booking details
                                        $query = "
                                            SELECT 
                                            bookings.booking_id,
                                            bookings.booking_date,
                                            bookings.booking_time,
                                            restaurants.restaurant_name,
                                            bookings.status,
                                            STRING_AGG(items.item_name, ', ') AS item_names
                                        FROM 
                                            bookings
                                        JOIN 
                                            restaurants ON bookings.rid = restaurants.id
                                        JOIN 
                                            booking_details ON bookings.booking_id = booking_details.booking_id
                                        JOIN 
                                            items ON booking_details.item_id = items.item_id
                                        WHERE 
                                            bookings.cid = $userId
                                        GROUP BY 
                                            bookings.booking_id, 
                                            bookings.booking_date, 
                                            bookings.booking_time, 
                                            restaurants.restaurant_name, 
                                            bookings.status;";
                                        
                                        $result = pg_query($conn, $query);
                                        
                                        if ($result && pg_num_rows($result) > 0) {
                                            while ($row = pg_fetch_assoc($result)) {
                                                $day = date('l', strtotime($row['booking_date']));
                                                $time = date("h:i A", strtotime($row['booking_time']));
                                                $date = date("d-m-Y", strtotime($row['booking_date']));
                                                
                                                // Set badge class based on status
                                                switch ($row['status']) {
                                                    case 'confirm':
                                                        $badgeClass = 'bg-primary';
                                                        $statusText = 'Confirmed';
                                                        $startOrderButton = "<a href='start_order.php?booking_id={$row['booking_id']}' class='btn btn-primary btn-sm'>Start My Order</a>";
                                                        break;
                                                    case 'preparing':
                                                        $badgeClass = 'bg-warning';
                                                        $statusText = 'Preparing';
                                                        $startOrderButton = '';
                                                        break;
                                                    case 'ready':
                                                        $badgeClass = 'bg-info';
                                                        $statusText = 'Ready';
                                                        $startOrderButton = '';
                                                        break;
                                                    case 'served':
                                                        $badgeClass = 'bg-success';
                                                        $statusText = 'Served';
                                                        $startOrderButton = '';
                                                        break;
                                                    case 'completed':
                                                        $badgeClass = 'bg-dark';
                                                        $statusText = 'Completed';
                                                        $startOrderButton = '';
                                                        break;
                                                    case 'cancelled':
                                                        $badgeClass = 'bg-danger';
                                                        $statusText = 'Cancelled';
                                                        $startOrderButton = '';
                                                        break;
                                                    case 'Pending':
                                                        $badgeClass = 'bg-secondary';
                                                        $statusText = 'Pending';
                                                        $startOrderButton = '';
                                                        break;
                                                    default:
                                                        $badgeClass = 'bg-light';
                                                        $statusText = 'Unknown';
                                                        $startOrderButton = '';
                                                        break;
                                                }
                                                
                                                // Output the row with the status and items
                                                echo "<tr>
                                                    <td>{$day}</td>
                                                    <td>{$date}</td>
                                                    <td>{$time}</td>
                                                    <td>{$row['item_names']}</td>
                                                    <td>{$row['restaurant_name']}</td>
                                                    <td>
                                                        <span class='badge {$badgeClass}'>{$statusText}</span>
                                                        <span class='mt-2'>{$startOrderButton}</span>
                                                    </td>
                                                </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='6'>No bookings found.</td></tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include "footer.php"; ?>
</body>
</html>
