<?php
    include "db.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Common fields
        $role = $_POST['role'];
        print_r($_POST);

        // Directory to store uploaded files
        $targetDir = "uploads/";

        // Process based on the role
        if ($role === "restaurant") {
            // Collect restaurant-specific fields
            $email = $_POST['emailRestaurant'];
            $restaurantName = $_POST['restaurantName'];
            $address = $_POST['address'];
            $mobileNumber = $_POST['mobileNumberRestaurant'];
            $cuisineType = $_POST['cuisineType'];
            $features = $_POST['features'];
            $rpassword = $_POST['rpassword'];
            $description = $_POST['description'];


            // Handle menu image upload
            $menuImageExtension = pathinfo($_FILES['menuImage']['name'], PATHINFO_EXTENSION);
            $menuImageName = $restaurantName . "_menuImage." . $menuImageExtension;
            $menuImagePath = $targetDir . $menuImageName;
            $menuUploadSuccess = move_uploaded_file($_FILES['menuImage']['tmp_name'], $menuImagePath);

            // Handle restaurant image upload
            $restaurantImageExtension = pathinfo($_FILES['restaurantImage']['name'], PATHINFO_EXTENSION);
            $restaurantImageName = $restaurantName . "_restaurantImage." . $restaurantImageExtension;
            $restaurantImagePath=$targetDir . $restaurantImageName;
            $restaurantUploadSuccess = move_uploaded_file($_FILES['restaurantImage']['tmp_name'], $restaurantImagePath);

            if (!$menuUploadSuccess || !$restaurantUploadSuccess) {
                echo "Error uploading images.";
                exit;
            }
            

            // Insert into the restaurants table
            $query = "INSERT INTO restaurants (email, password, restaurant_name, address, mobile_number, cuisine_type, features, menu_image_path, restaurant_image_path ,description) 
                      VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9,$10)";
            $result = pg_query_params($conn, $query, [
                $email, 
                $rpassword, 
                $restaurantName, 
                $address, 
                $mobileNumber, 
                $cuisineType, 
                $features, 
                $menuImagePath, 
                $restaurantImagePath,
                $description
            ]);

        } elseif ($role === "customer") {
            // Collect customer-specific fields
            $email = $_POST['emailCustomer'];
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            $mobileNumber = $_POST['mobileNumberCustomer'];
            $cpassword = $_POST['cpassword'];


            // Insert into the customers table
            $query = "INSERT INTO customers (email, password, first_name, last_name, mobile_number) 
                      VALUES ($1, $2, $3, $4, $5)";
            $result = pg_query_params($conn, $query, [$email, $cpassword, $firstName, $lastName, $mobileNumber]);
        } else {
            echo "Invalid role selected.";
            exit;
        }

        // Check if the query was successful
        if ($result) {
            header("Location: register-success.php?message=Registration successful!");
            exit;    
        } else {
            // Redirect to an error page with an error message
            header("Location: register-success.php?message=Error: Registration failed, please try again.");
            exit;
        }

        // Close the connection
        pg_close($conn);
    }
?>
