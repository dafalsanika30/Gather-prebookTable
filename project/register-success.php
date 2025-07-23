<?php
// Check if there's a message in the query string
$message = isset($_GET['message']) ? $_GET['message'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Result</title>
    <script>
        // Show an alert with the success or error message and immediately redirect to login after the alert
        window.onload = function() {
            <?php if ($message) { ?>
                alert("<?php echo $message; ?>");

                // Immediately redirect to the login page
                window.location.href = "login.html";  // Replace "login.html" with your actual login page URL
            <?php } ?>
        }
    </script>
</head>
</html>
