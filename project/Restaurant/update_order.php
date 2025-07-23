<?php
include "db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    
    // Check if the status is valid
    $valid_statuses = ['preparing', 'ready', 'served', 'completed', 'cancelled', 'confirm'];
    if (!in_array($new_status, $valid_statuses)) {
        echo "Invalid status.";
        exit;
    }

    // Add additional logic to handle the transition based on the current status if needed
    // For example, you may want to prevent going from 'completed' back to 'confirm'
    $query = "SELECT status FROM bookings WHERE booking_id = $1 AND rid = $2";
    $restid = $_SESSION['id']; // Restaurant ID
    $result = pg_query_params($conn, $query, array($order_id, $restid));

    if ($result) {
        $order = pg_fetch_assoc($result);
        $current_status = $order['status'];

        // Prevent certain transitions
        if ($current_status === 'completed' && $new_status !== 'completed') {
            echo "Cannot change status from completed.";
            exit;
        }

        // Update the order status in the database
        $query = "UPDATE bookings SET status = $1 WHERE booking_id = $2 AND rid = $3";
        $result = pg_query_params($conn, $query, array($new_status, $order_id, $restid));

        if ($result) {
            echo "Status updated successfully.";
        } else {
            echo "Error updating status: " . pg_last_error();
        }
    } else {
        echo "Order not found.";
    }
} else {
    echo "Invalid request method.";
}
?>
