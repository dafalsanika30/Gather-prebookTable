<?php
session_start();
include "db.php"; // Include the database connection

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Fetch users from the database
$users = [];
$query = "SELECT * FROM customers order by created_at";
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
    <title>Manage Users</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <script src="../../js/bootstrap.bundle.js"></script>
    <link href="../CSS/dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include "navbar.html"; ?>            


            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div class="page-header">
                    <h3>Manage Users</h3>
                </div>

                <!-- User Actions -->
                <div class="container mt-4">
                    <div class="mb-3">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            Add New User
                        </button>
                    </div>

                    <!-- User Table -->
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th>Mobile Number</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id']?></td>
                                    <td><?= $user['first_name']." ".$user['last_name']  ?></td>
                                    <td><?= $user['email'] ?></td>
                                    <td><?= $user['password'] ?></td>
                                    <td><?= $user['mobile_number'] ?></td>
                                    <td><?= date('d/m/Y h:i:s A', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <button 
                                            class="btn btn-sm btn-success" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editUserModal" 
                                            data-id="<?= $user['id'] ?>" 
                                            data-firstname="<?= $user['first_name'] ?>" 
                                            data-lastname="<?= $user['last_name'] ?>" 
                                            data-email="<?= $user['email'] ?>" 
                                            data-mobile="<?= $user['mobile_number'] ?>">
                                            Edit
                                        </button>
                                        <form method="POST" action="delete_user.php" style="display:inline-block" onsubmit="return confirmDelete()">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Add User Modal -->
                <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="add_user.php">
                                    <div class="mb-3">
                                        <label for="userFName" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="userFName" name="Fname" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="userLName" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="userLName" name="Lname" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="userEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="userEmail" name="email" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="mobileNumberCustomer" class="form-label">Mobile Number</label>
                                        <input type="tel" id="mobileNumberCustomer" name="mobileNumber" class="form-control" oninput="validateMobileNumber('mobileNumberCustomer', 'mobileErrorCustomer')" >
                                        <div id="mobileErrorCustomer" class="error-message"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="userPassword" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="userPassword" name="password" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="update_user.php">
                                    <!-- Hidden field to store user ID -->
                                    <input type="hidden" id="editUserId" name="user_id">

                                    <div class="mb-3">
                                        <label for="editUserFirstName" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="editUserFirstName" name="firstname" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editUserLastName" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="editUserLastName" name="lastname" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editUserEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="editUserEmail" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editUserMobile" class="form-label">Mobile Number</label>
                                        <input type="tel" class="form-control" id="editUserMobile" name="mobile_number" required>
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
</body>
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

        
    var editUserModal = document.getElementById('editUserModal');
    editUserModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; // Button that triggered the modal

        // Extract data attributes
        var userId = button.getAttribute('data-id');
        var firstName = button.getAttribute('data-firstname');
        var lastName = button.getAttribute('data-lastname');
        var email = button.getAttribute('data-email');
        var mobile = button.getAttribute('data-mobile');

        // Populate modal fields
        document.getElementById('editUserId').value = userId;
        document.getElementById('editUserFirstName').value = firstName;
        document.getElementById('editUserLastName').value = lastName;
        document.getElementById('editUserEmail').value = email;
        document.getElementById('editUserMobile').value = mobile;
    });
</script>
</html>
