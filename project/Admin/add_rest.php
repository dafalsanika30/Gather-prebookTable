<?php
include "db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Rname = $_POST['Rname'];
    $email = $_POST['Remail'];
    $password = $_POST['password'];
    $number=$_POST['mobileNumber'];
    $address=$_POST['address'];

    $query = "INSERT INTO restaurants (email,password,restaurant_name,mobile_number,address) VALUES ($1, $2, $3, $4, $5)";
    $result = pg_query_params($conn, $query, [$email ,$password ,$Rname ,$number,$address]);

    if ($result) {
        header("Location: rest.php");
        exit();
    } else {
        echo "Error: " . pg_last_error($conn);
    }
}
?>

