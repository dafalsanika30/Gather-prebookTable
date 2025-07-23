<?php
include 'db.php'; // Replace with your connection file

if (isset($_GET['id'])) {
    $feedbackId = intval($_GET['id']);
    $deleteQuery = "DELETE FROM feedback WHERE feedback_id = $1";
    $result = pg_query_params($conn, $deleteQuery, array($feedbackId));

    if ($result) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
