<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Feedback Management</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/dashboard.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include "navbar.html"; ?>            


            <!-- Main Content -->
            <main class="col-md-10 main-content">
                <div class="page-header">
                    <h3>Manage Feedback</h3>
                </div>

                <!-- Feedback Table -->
                <div class="container mt-4">
                    <h4>Feedback Records</h4>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer Name</th>
                                    <th>Restaurant</th>
                                    <th>Feedback</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Database connection
                                include 'db.php'; // Replace with your connection file

                                $query = "
                                    SELECT 
                                        feedback.feedback_id, 
                                        customers.first_name || ' ' || customers.last_name AS customer_name, 
                                        restaurants.restaurant_name, 
                                        feedback.feedback_text, 
                                        feedback.created_at 
                                    FROM 
                                        feedback
                                    INNER JOIN 
                                        customers ON feedback.customer_id = customers.id
                                    INNER JOIN 
                                        restaurants ON feedback.restaurant_id = restaurants.id
                                    ORDER BY 
                                        feedback.created_at DESC;
                                ";

                                $result = pg_query($conn, $query);

                                if (pg_num_rows($result) > 0) {
                                    while ($row = pg_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['feedback_id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['restaurant_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['feedback_text']) . "</td>";
                                        echo "<td>" . date("d M Y", strtotime($row['created_at'])) . "</td>";
                                        echo "<td><button class='btn btn-sm btn-danger delete-btn' data-id='" . htmlspecialchars($row['feedback_id']) . "'>Delete</button></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No feedback records found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="../js/bootstrap.bundle.js"></script>
    <script>
        // Handle feedback deletion
        document.querySelector("tbody").addEventListener("click", function (e) {
            if (e.target.classList.contains("delete-btn")) {
                const feedbackId = e.target.getAttribute("data-id");
                const confirmation = confirm("Are you sure you want to delete this feedback?");
                if (confirmation) {
                    fetch(`delete_feedback.php?id=${feedbackId}`, {
                        method: "GET"
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (data.trim() === "success") {
                            alert("Feedback deleted successfully.");
                            e.target.closest("tr").remove();
                        } else {
                            alert("Failed to delete feedback.");
                        }
                    });
                }
            }
        });
    </script>
</body>
</html>
