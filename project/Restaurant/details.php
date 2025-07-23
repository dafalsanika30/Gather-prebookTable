<?php
// Include the database connection file
include "db.php";

// Start the session
session_start();

// Retrieve restaurant data (seating capacity, availability, business hours)
$query = "SELECT seating_capacity, availability_status, business_hours FROM restaurants WHERE id = $1";
$result = pg_query_params($conn, $query, [$_SESSION['id']]);

// Fetch the restaurant data
$row = pg_fetch_assoc($result);

// Retrieve the total number of menu items from the `items` table
$itemsQuery = "SELECT COUNT(*) AS total_items FROM items WHERE restaurant_id = $1";
$itemsResult = pg_query_params($conn, $itemsQuery, [$_SESSION['id']]);
$itemsData = pg_fetch_assoc($itemsResult);

// Retrieve the total sales data from the `sales` table
// $salesQuery = "SELECT item_name, quantity_sold, total_sales FROM sales WHERE restaurant_id = $1";
// $salesResult = pg_query_params($conn, $salesQuery, [$_SESSION['restaurant_id']]);

?>