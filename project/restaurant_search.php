<?php
include 'db.php';  // Include your database connection file

// Get the search query from the URL
$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';

// If there is a search query, process the search
if ($search_query) {
    // Escape the search query for security
    $search_query = pg_escape_string($search_query);

    // Query the database for restaurants with a name like the search query
    $query = "SELECT id, restaurant_name FROM restaurants WHERE restaurant_name ILIKE '%$search_query%' LIMIT 1"; // Limiting to 1 match
    $result = pg_query($conn, $query);

    // Check if a restaurant was found
    if ($result && pg_num_rows($result) > 0) {
        // Fetch the first matching restaurant
        $row = pg_fetch_assoc($result);
        $restaurant_id = $row['id'];

        // Redirect to the restaurant page using the restaurant's ID
        header("Location: restaurant.php?id=" . $restaurant_id);
        exit();
    } else {
        // If no matching restaurant is found, show a message or redirect to an error page
        echo "No matching restaurant found!";
    }
}
?>
