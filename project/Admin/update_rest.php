<?php
session_start();
include "db.php"; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $userId = $_POST['user_id'];
    $RName = $_POST['Restname'];
    $address = $_POST['Raddress'];
    $email = $_POST['Remail'];
    $mobileNumber = $_POST['mobileNumber'];

    // Validate data
    if (strlen($mobileNumber) != 10 || !is_numeric($mobileNumber)) {
        echo "<script>
                alert('Invalid mobile number. Please provide a 10-digit numeric mobile number.');
                window.history.back();
              </script>";
        exit;
    }

    // Update query
    $query = "UPDATE restaurants
              SET restaurant_name = '$RName', 
                  address = '$address', 
                  email = '$email', 
                  mobile_number = '$mobileNumber' 
              WHERE id = $userId";
    
    $result = pg_query($conn, $query);

    if ($result) {
        echo "<script>
                alert('Updated Successfully');
                window.location.href = 'rest.php';
              </script>";
    } else {
        echo "<script>
                alert('Error updating record: " . pg_last_error($conn) . "');
                window.history.back();
              </script>";
    }
}
?>
