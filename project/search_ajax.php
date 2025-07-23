<?php
include 'db.php';  // Include your database connection file

// Get search query from the request
$q = $_REQUEST['q'];

if (strlen($q) > 0) {
    // Escape the search query for security
    $q = pg_escape_string($q);

    // Query the database for matching restaurants
    $query = "SELECT * FROM restaurants WHERE restaurant_name ILIKE '%$q%' LIMIT 10";
    $result = pg_query($conn, $query);

    if ($result) {
        $hint = "";
        while ($row = pg_fetch_assoc($result)) {
            $restaurant_name = htmlspecialchars($row['restaurant_name']);
            $restaurant_id = $row['id'];
            $hint .= "<a href='restaurant.php?id=$restaurant_id' style='text-decoration: none;color:black'>$restaurant_name</a><br>";
        }
        if ($hint == "") {
            echo "No Suggestions";
        } else {
            echo $hint;
        }
    }
}
?>
