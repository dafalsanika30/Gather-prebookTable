<?php
include 'db.php'; // Include the database connection file
session_start(); // Start the session

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve email and password from POST request
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Sanitize inputs to prevent SQL injection
    $email = pg_escape_string($conn, $email);
    $password = pg_escape_string($conn, $password);

    $admin_email = 'admin@gmail.com';
    $admin_password = 'admin';

    // Check for admin login
    if ($email === $admin_email && $password === $admin_password) {
        // Set session variables for admin
        $_SESSION['id'] = 0; // You can assign a specific ID for the admin
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'admin';
        $_SESSION['logged_in'] = true;

        // Redirect to the admin dashboard
        header("Location: Admin/admin.php");
        exit();
    }
    // Define roles and their redirection pages
    $roles = [
        'restaurants' => 'Restaurant/restaurant-dashboard.php',
        'customers' => 'index.php'
    ];

    // Check credentials for each role (restaurants and customers)
    foreach ($roles as $role => $redirect) {
        $sql = "SELECT * FROM $role WHERE email = '$email' AND password = '$password'";
        $result = pg_query($conn, $sql);

        if ($result && pg_num_rows($result) > 0) {
            $row = pg_fetch_assoc($result);

            // Set session variables
            $_SESSION['id'] = $row['id'];
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;

            if ($role === 'restaurants') {
                $_SESSION['restaurant_name'] = $row['restaurant_name'];
                $_SESSION['address'] = $row['address'];
                $_SESSION['mobile_number'] = $row['mobile_number'];
                $_SESSION['cuisine_type'] = $row['cuisine_type'];
                $_SESSION['features'] = $row['features'];
                $_SESSION['menu_image_path'] = $row['menu_image_path'];
                $_SESSION['restaurant_image_path'] = $row['restaurant_image_path'];
                $_SESSION['seating_capacity']=$row['seating_capacity'];
                $_SESSION['availability_status']=$row['availability_status'];
                $_SESSION['business_hours']=$row['business_hours'];
                
            } elseif ($role === 'customers') {
                $_SESSION['first_name'] = $row['first_name'];
                $_SESSION['last_name'] = $row['last_name'];
                $_SESSION['mobile_number'] = $row['mobile_number'];
                $_SESSION['logged_in'] = true;

            }

            // Redirect to the appropriate dashboard
            header("Location: $redirect");
            exit();
        }
    }

    // If no match found, display an error message
    echo '<script>alert("Invalid Email or Password!"); location.href="login.html";</script>';
}

pg_close($conn); // Close the database connection
?>
