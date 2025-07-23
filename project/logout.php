<?php
session_start();
session_unset(); // Unsets all session variables
session_destroy(); // Destroys the session
header("Location: index.php"); // Redirects to the home page after logging out
exit;
?>
