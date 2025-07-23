<?php
include "db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];

    $query = "DELETE FROM restaurants WHERE id = $1";
    $result = pg_query_params($conn, $query, [$user_id]);

    if ($result) {
        header("Location: rest.php");
        exit();
    } else {
        echo "Error: " . pg_last_error($conn);
    }
}
?>
