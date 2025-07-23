<?php
include "db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Fname = $_POST['Fname'];
    $Lname = $_POST['Lname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $number=$_POST['mobileNumber'];

    $query = "INSERT INTO customers (email,password,first_name,last_name,mobile_number) VALUES ($1, $2, $3, $4, $5)";
    $result = pg_query_params($conn, $query, [$email ,$password ,$Fname ,$Lname ,$number]);

    if ($result) {
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Error: " . pg_last_error($conn);
    }
}
?>
