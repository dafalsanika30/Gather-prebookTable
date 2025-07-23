<?php
include "db.php";
$query = "SELECT menu_image_path, restaurant_image_path FROM restaurants WHERE id = 2";
$result = pg_query($conn, $query);
$row = pg_fetch_assoc($result);

if ($row) {
    echo '<img src="' . $row['menu_image_path'] . '" alt="Menu Image" width="100" height="100">';
    echo '<img src="' . $row['restaurant_image_path'] . '" alt="Restaurant Image" width="100" height="100">';
}
?>
