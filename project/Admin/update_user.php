<?php
session_start();
include "db.php"; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $userId = $_POST['user_id'];
    $firstName = $_POST['firstname'];
    $lastName = $_POST['lastname'];
    $email = $_POST['email'];
    $mobileNumber = $_POST['mobile_number'];

    // Validate data
    if (strlen($mobileNumber) != 10 || !is_numeric($mobileNumber)) {
        echo "<script>
                alert('Invalid mobile number. Please provide a 10-digit numeric mobile number.');
                window.history.back();
              </script>";
        exit;
    }

    // Update query
    $query = "UPDATE customers 
              SET first_name = '$firstName', 
                  last_name = '$lastName', 
                  email = '$email', 
                  mobile_number = '$mobileNumber' 
              WHERE id = $userId";

    $result = pg_query($conn, $query);

    if ($result) {
        echo "<script>
                alert('User updated successfully.');
                window.location.href = 'manage_users.php';
              </script>";
    } else {
        echo "<script>
                alert('Error updating user: " . pg_last_error($conn) . "');
                window.history.back();
              </script>";
    }
}
?>
