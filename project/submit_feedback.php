<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $restaurant_id = $_POST['restaurant_id'];
    $customer_id = $_POST['customer_id'];
    $feedback_text = $_POST['feedback_text'];
    $rating = $_POST['rating'];

    echo $restaurant_id;
    echo $customer_id;
    echo $feedback_text;
    echo $rating;
    // Insert feedback into the database
    $insertQuery = "INSERT INTO feedback (restaurant_id, customer_id, feedback_text, rating) 
                    VALUES ($1, $2, $3, $4)";
    $result = pg_query_params($conn, $insertQuery, array($restaurant_id, $customer_id, $feedback_text, $rating));

    if ($result) {
        header("Location: restaurant.php?id=" . $restaurant_id);
        exit();
    } else {
        echo "Error submitting feedback.";
    }
}
?>
