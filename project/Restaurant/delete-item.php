<?php
session_start();
include "db.php";  // Include the database connection

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Check if the item name is passed
if (isset($_POST['item_name'])) {
    $item_name = $_POST['item_name'];

    // Prepare the query to delete the item from the database
    $deleteQuery = "DELETE FROM items WHERE item_name = $1 AND restaurant_id = $2";  // Ensure the item belongs to the logged-in restaurant
    $result = pg_query_params($conn, $deleteQuery, [$item_name, $_SESSION['id']]);

    if ($result) {
        // Redirect back to the menu management page
        header("Location: demo1.php");
        exit();
    } else {
        echo "Error: " . pg_last_error($conn);
    }
} else {
    echo "Item name not provided.";
}

pg_close($conn);  // Close the database connection
?>
