<?php
include "db.php";
session_start(); // Start the session to check if the user is logged in

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // If not logged in, redirect to the login page
    echo "<script type='text/javascript'>
            alert('Please log in before booking.');
            window.location.href = 'login.html';
          </script>";
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $restaurant_id = $_POST['restaurant_id']; // Get the restaurant ID from the URL
    $user_id = $_SESSION['id']; // Assuming user is logged in and user_id is stored in session
    $booking_date = $_POST['date'];
    $booking_time = $_POST['time'];
    $num_guests = $_POST['seats'];
    $special_requests = isset($_POST['special_requests']) ? $_POST['special_requests'] : null;
    $total_price = 0;

    // Calculate the total price from selected menu items
    if (isset($_POST['menu_items'])) {
        foreach ($_POST['menu_items'] as $item_id => $item_details) {
            $quantity = $item_details['quantity'];
            $price = $item_details['price']; // price is passed in the form
            $total_price += $quantity * $price;
        }
    }

    // Start a transaction

    try {
        // Insert into the bookings table
        $query_booking = "INSERT INTO bookings (cid, rid, booking_date, booking_time, num_guests, special_requests) 
                        VALUES ($1, $2, $3, $4, $5, $6) RETURNING booking_id";
        $result_booking = pg_query_params($conn, $query_booking, array($user_id, $restaurant_id, $booking_date, $booking_time, $num_guests, $special_requests));
        $booking = pg_fetch_assoc($result_booking);
        $booking_id = $booking['booking_id'];

        // Insert selected menu items into booking details
        if (isset($_POST['menu_items'])) {
            foreach ($_POST['menu_items'] as $item_id => $item_details) {
                $quantity = $item_details['quantity'];
                $price = $item_details['price'];
                $item_id = $item_details['item_id'];

                // Insert each item into the booking details table
                $query_menu = "INSERT INTO booking_details (booking_id, item_id, quantity, price) 
                               VALUES ($1, $2, $3, $4)";
                pg_query_params($conn, $query_menu, array($booking_id, $item_id, $quantity, $price));
            }
        }

        // Commit the transaction
        echo "<script type='text/javascript'>
                alert('Booking Successful!');
                window.location.href = 'bookingdetails.php?booking_id=" . $booking_id . "';
              </script>";
        exit();
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        pg_query($conn, "ROLLBACK");
        echo "Error: " . $e->getMessage();
    }
}
?>
