<?php
session_start();
include "db.php"; // Include the database connection

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Fetch users from the database
$users = [];
$query = "SELECT * FROM restaurants ORDER BY created_at DESC";
$result = pg_query($conn, $query);

if ($result) {
    while ($row = pg_fetch_assoc($result)) {
        $users[] = $row;
    }
} else {
    echo "Error fetching users: " . pg_last_error($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Restaurants</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link href="../CSS/dashboard.css" rel="stylesheet">
    <script src="../../js/bootstrap.bundle.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include "navbar.html"; ?>            


            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div class="page-header">
                    <h3>Manage Restaurants</h3>
                </div>

                <!-- Restaurant Actions -->
                <div class="container mt-4">
                    <div class="mb-3">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRestaurantModal">
                            Add New Restaurant
                        </button>
                    </div>

                    <!-- Restaurant Table -->
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Password</th>
                                <th>Contact No</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= ($user['id']) ?></td>
                                <td><?= ($user['email']) ?></td>
                                <td><?= ($user['restaurant_name']) ?></td>
                                <td><?= ($user['address']) ?></td>
                                <td><?= ($user['password']) ?></td>
                                <td><?= ($user['mobile_number']) ?></td>
                                <td><?= date('d/m/Y h:i:s A', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <button 
                                        class="btn btn-sm btn-success" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editRestaurantModal" 
                                        data-id="<?= ($user['id']) ?>" 
                                        data-restname="<?= ($user['restaurant_name']) ?>" 
                                        data-email="<?= ($user['email']) ?>" 
                                        data-mobile="<?= ($user['mobile_number']) ?>"
                                        data-address="<?= ($user['address']) ?>">
                                        Edit
                                    </button>
                                    <form method="POST" action="delete_rest.php" style="display:inline-block" onsubmit="return confirmDelete()">
                                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']) ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Add Restaurant Modal -->
                <div class="modal fade" id="addRestaurantModal" tabindex="-1" aria-labelledby="addRestaurantModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addRestaurantModalLabel">Add New Restaurant</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="add_rest.php">
                                    <div class="mb-3">
                                        <label for="restaurantName" class="form-label">Restaurant Name</label>
                                        <input type="text" class="form-control" id="restaurantName" name="Rname" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="restaurantEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="restaurantEmail" name="Remail" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="mobileNumberRest" class="form-label">Mobile Number</label>
                                        <input type="tel" id="mobileNumberRest" name="mobileNumber" class="form-control" oninput="validateMobileNumber('mobileNumberRest', 'mobileErrorRest')">
                                        <div id="mobileErrorRest" class="error-message text-danger"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="RestPassword" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="RestPassword" name="password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="Location" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="Location" name="address" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Restaurant Modal -->
                <div class="modal fade" id="editRestaurantModal" tabindex="-1" aria-labelledby="editRestaurantModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editRestaurantModalLabel">Edit Restaurant</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="update_rest.php">
                                    <input type="hidden" id="editUserId" name="user_id">
                                    <div class="mb-3">
                                        <label for="editRestaurantName" class="form-label">Restaurant Name</label>
                                        <input type="text" class="form-control" id="editRestaurantName" name="Restname" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editRestaurantLocation" class="form-label">Location</label>
                                        <input type="text" class="form-control" id="editRestaurantLocation" name="Raddress" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editRestaurantEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="editRestaurantEmail" name="Remail" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editRestMobile" class="form-label">Mobile Number</label>
                                        <input type="tel" class="form-control" id="editRestMobile" name="mobileNumber" required>
                                    </div>
                                    <button type="submit" class="btn btn-success">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this user?");
        }

        function validateMobileNumber(id, errorId) {
            const mobileNumber = document.getElementById(id);
            const mobileError = document.getElementById(errorId);

            if (mobileNumber.value.length !== 10 || isNaN(mobileNumber.value)) {
                mobileError.textContent = "Invalid number! Enter a 10-digit number.";
            } else {
                mobileError.textContent = ""; // Clear the error message if valid
            }
        }

        var editRestaurantModal = document.getElementById('editRestaurantModal');
        editRestaurantModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Button that triggered the modal

            // Extract data attributes
            var userId = button.getAttribute('data-id');
            var RName = button.getAttribute('data-restname');
            var email = button.getAttribute('data-email');
            var address = button.getAttribute('data-address');
            var mobile = button.getAttribute('data-mobile');

            // Populate modal fields
            document.getElementById('editUserId').value = userId;
            document.getElementById('editRestaurantName').value = RName;
            document.getElementById('editRestaurantEmail').value = email;
            document.getElementById('editRestaurantLocation').value = address;
            document.getElementById('editRestMobile').value = mobile;
        });
    </script>
</body>
</html>