<?php
include "db.php";
session_start();

// Check if the 'booking_id' is passed in the URL using GET method
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['booking_id'])) {
    // Fetch the parameters from the GET request
    $booking_id = $_GET['booking_id'];

    // Sanitize input to prevent SQL injection (if necessary)
    // You can use pg_escape_string or better use parameterized queries (which you're already doing here)

    // Update the booking status and the customer_started_order flag in the database
    $query = "UPDATE bookings 
              SET customer_started_order = 't', status = 'preparing' 
              WHERE booking_id = $1";
    $result = pg_query_params($conn, $query, array($booking_id));

    // Check if the query was successful
    if ($result) {
        // Display a success message and redirect to the booking details page
        echo "<script type='text/javascript'>
                alert('Status updated successfully.');
                window.location.href = 'bookingdetails.php';
              </script>";
    } else {
        echo "Error updating status: " . pg_last_error($conn);
    }
} else {
    echo "Invalid request or missing parameters.";
}
?>
