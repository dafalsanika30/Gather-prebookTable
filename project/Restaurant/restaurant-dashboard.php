
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Dashboard</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/dashboard.css">
</head>
<body>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include "navbar.php";
            // Include the database connection file
            include "db.php";
            
            // Start the session
            // session_start();
            
            // Retrieve restaurant data (seating capacity, availability, business hours)
            $query = "SELECT seating_capacity, availability_status, business_hours FROM restaurants WHERE id = $1";
            $result = pg_query_params($conn, $query, [$_SESSION['id']]);
            
            // Fetch the restaurant data
            $row = pg_fetch_assoc($result);
            
            // Retrieve the total number of menu items from the `items` table
            $itemsQuery = "SELECT COUNT(*) AS total_items FROM items WHERE restaurant_id = $1";
            $itemsResult = pg_query_params($conn, $itemsQuery, [$_SESSION['id']]);
            $itemsData = pg_fetch_assoc($itemsResult);
            
            // Retrieve the total sales data from the `sales` table
            $salesQuery = "SELECT * FROM items WHERE restaurant_id = $1";
            $salesResult = pg_query_params($conn, $salesQuery, [$_SESSION['id']]);
            
            ?>

            <!-- Main Content -->
            <main class="col-md-10 main-content">
                <div class="page-header">
                    <h3>Welcome to the Restaurant Dashboard</h3>
                </div>

                <!-- Dashboard Overview -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5>Total Menu Items</h5>
                                <h3><?php echo $itemsData['total_items']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5>Seating Capacity</h5>
                                <h3><?php echo $row['seating_capacity']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5>Availability</h5>
                                <h3><?php echo $row['availability_status']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5>Business Hours</h5>
                                <h3><?php echo $row['business_hours']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table for Total Sale of Menu Items -->
                <div class="row mt-5">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Total Sale of Menu Items</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-black">
                                    <thead>
                                        <tr>
                                            <th>Item Name</th>
                                            <th>Quantity Sold</th>
                                            <th>Total Sales (₹)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($salesRow = pg_fetch_assoc($salesResult)): ?>
                                            <tr>
                                                <td><?php echo $salesRow['item_name']; ?></td>
                                                <td><?php echo $salesRow['description']; ?></td>
                                                <td>₹<?php echo $salesRow['price']; ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
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
