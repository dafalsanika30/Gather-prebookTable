<?php

    $conn=pg_connect("host=localhost user=postgres password=postgres");
    // $con=pg_connect("host=192.168.16.1 port=5432 dbname=tya6 user=tya6");


    if (!$conn) {
        die("Connection failed: " . pg_last_error());
    }
?>