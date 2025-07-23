<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seating Capacity | Availability Status | Business Hours</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/dashboard.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include "navbar.php";
            include "db.php";

            // Check if the form is submitted
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Debugging: Print POST values to check if data is being submitted
                // echo '<pre>';
                // print_r($_POST); // Debugging: Print POST values
                // echo '</pre>';
            
                // Check if session is set
                if (!isset($_SESSION['id'])) {
                    echo "Restaurant ID is not set!";
                    exit; // Stop script execution if session is not set
                }
            
                // Update Seating Capacity
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // Update Seating Capacity
                    if (isset($_POST['seatingCapacity'])) {
                        $seatingCapacity = $_POST['seatingCapacity'];
                        $query = "UPDATE restaurants SET seating_capacity = $1 WHERE id = $2";
                        $result = pg_query_params($conn, $query, [$seatingCapacity, $_SESSION['id']]);
                        if ($result) {
                            $_SESSION['seating_capacity']=$seatingCapacity;
                            echo "<script>alert('Seating capacity updated successfully!');</script>";
                        } else {
                            echo "<script>alert('Error updating seating capacity!');</script>";
                        }
                    }
                
                    // Update Availability Status
                    if (isset($_POST['availabilityStatus'])) {
                        $availabilityStatus = $_POST['availabilityStatus'];
                        $query = "UPDATE restaurants SET availability_status = $1 WHERE id = $2";
                        $result = pg_query_params($conn, $query, [$availabilityStatus, $_SESSION['id']]);
                        if ($result) {
                            $_SESSION['availability_status']=$availabilityStatus;
                            echo "<script>alert('Availability status updated successfully!');</script>";
                        } else {
                            echo "<script>alert('Error updating availability status!');</script>";
                        }
                    }
                
                    // Update Business Hours
                    if (isset($_POST['businessHours'])) {
                        $businessHours = $_POST['businessHours'];
                        $query = "UPDATE restaurants SET business_hours = $1 WHERE id = $2";
                        $result = pg_query_params($conn, $query, [$businessHours, $_SESSION['id']]);
                        if ($result) {
                            $_SESSION['business_hours']=$businessHours;
                            echo "<script>alert('Business hours updated successfully!');</script>";
                        } else {
                            echo "<script>alert('Error updating business hours!');</script>";
                        }
                    }
                }
            };?>

            <!-- Main Content -->
            <main class="col-md-10 main-content">
                <div class="page-header">
                    <h3>Seating Capacity | Availability Status | Business Hours</h3>
                </div>

                <!-- Seating Capacity Form -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Update Seating Capacity</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="seating-capacity.php">
                                    <div class="form-group">
                                        <label for="seatingCapacity">Seating Capacity</label>
                                        <input type="number" class="form-control" id="seatingCapacity" name="seatingCapacity" value="<?= $_SESSION['seating_capacity'] ?>" required style="color:white;">
                                    </div>
                                    <button type="submit" class="btn btn-success">Update Capacity</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Availability Status Form -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Update Availability Status</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="seating-capacity.php">
                                    <div class="form-group">
                                        <label for="availabilityStatus">Restaurant Status</label>
                                        <select class="form-control" id="availabilityStatus" name="availabilityStatus" required style="color:white;">
                                            <option value="Open" <?php echo (isset($_SESSION['availability_status']) && $_SESSION['availability_status'] == 'Open') ? 'selected' : ''; ?>>Open</option>
                                            <option value="Closed" <?php echo (isset($_SESSION['availability_status']) && $_SESSION['availability_status'] == 'Closed') ? 'selected' : ''; ?>>Closed</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-success">Update Status</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Hours Form -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Update Business Hours</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="seating-capacity.php">
                                    <div class="form-group">
                                        <label for="businessHours">Business Hours  Format:(00:00 AM - 00:00 PM)</label>
                                        <input type="text" class="form-control" id="businessHours" name="businessHours" value="<?= $_SESSION['business_hours'] ?>" required style="color:white;">
                                    </div>
                                    <button type="submit" class="btn btn-success">Update Hours</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Start session to access session variables
// session_start();

// Include database connection

    ?>
    