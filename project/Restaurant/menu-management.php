<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <style>
        /* Force the placeholder color to be white for both input and textarea fields */
        input::placeholder, textarea::placeholder {
            color: #ffffff !important; /* White placeholder text with high specificity */
            opacity: 1 !important; /* Ensure visibility */
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include "navbar.php";
            include "db.php";

            // Insert new item if form is submitted
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $name = $_POST["menuItemName"];
                $description = $_POST["menuItemDescription"];
                $price = $_POST["menuItemPrice"];
                $specialty = isset($_POST["menuItemSpecialty"]) ? "yes" : "no";
                $restid = $_SESSION['id']; // Assuming the restaurant ID is stored in session
            
                // Prepared statement to insert menu item
                $insertQuery = "INSERT INTO items (item_name, description, price, specialty, restaurant_id) 
                                VALUES ($1, $2, $3, $4, $5)";
                $result = pg_query_params($conn, $insertQuery, [$name, $description, $price, $specialty, $restid]);
                
                if ($result) {
                    // Redirect to the same page to prevent form resubmission
                    header("Location: menu-management.php");
                    exit();  // Make sure to exit after the redirect
                } else {
                    echo "Error: " . pg_last_error($conn);
                }
            }
            ?>

            <!-- Main Content -->
            <main class="col-md-10 main-content">
                <div class="page-header">
                    <h3>Menu Management</h3>
                </div>

                <!-- Menu Form -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Add New Menu Item</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="menu-management.php">
                                    <div class="form-group">
                                        <label for="menuItemName">Menu Item Name</label>
                                        <input type="text" class="form-control" name="menuItemName" id="menuItemName" placeholder="Enter menu item name" required style="color:white;">
                                    </div>
                                    <div class="form-group">
                                        <label for="menuItemDescription">Description</label>
                                        <textarea class="form-control" name="menuItemDescription" id="menuItemDescription" rows="3" placeholder="Enter item description" required style="color:white;"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="menuItemPrice">Price</label>
                                        <input type="number" class="form-control" name="menuItemPrice" id="menuItemPrice" placeholder="Enter item price" required style="color:white;">
                                    </div>
                                    <div class="form-group">
                                        <label for="menuItemSpecialty">Specialty</label>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="menuItemSpecialty" id="menuItemSpecialty">
                                            <label class="form-check-label" for="menuItemSpecialty">Is this a specialty of the restaurant?</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Add Item</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Items List -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Menu Items List</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group" id="menuItemsList">
                                    <?php
                                    $query = "SELECT * FROM items WHERE restaurant_id = $1 ORDER BY item_name";
                                    $result = pg_query_params($conn, $query, [$_SESSION['id']]);
                                    if ($result && pg_num_rows($result) > 0) {
                                        while ($row = pg_fetch_assoc($result)) {
                                            echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                                            echo $row["item_name"] . " -  ₹" . $row["price"];
                                            echo "<br>".$row["description"];
                                            echo "<form method='POST' action='delete-item.php' style='display:inline;' onsubmit='return confirmDelete();'>";
                                            echo "<input type='hidden' name='item_name' value='" . $row["item_name"] . "'>";
                                            echo "<button class='btn btn-danger btn-sm'>Delete</button>";
                                            echo "</form>";
                                            echo "</li>";
                                        }
                                    } else {
                                        echo "<li class='list-group-item'>No items available</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="../js/bootstrap.bundle.min.js"></script>

    <script>
        function confirmDelete() {
            // Show a confirmation box before submitting the form
            return confirm("Are you sure you want to delete this item?");
        }
    </script>

</body>
</html>

