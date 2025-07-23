<?php
include "db.php";
session_start();

$restid = $_SESSION['id'];

// Query to fetch the order details
$query = "
    SELECT 
    bookings.booking_id,
    bookings.booking_date,
    bookings.booking_time,
    customers.first_name,
    customers.last_name,
    bookings.status,
    bookings.num_guests,
    bookings.customer_started_order,
    STRING_AGG(item_details.item_info, ', ') AS item_names_with_quantities
FROM 
    bookings
JOIN 
    restaurants ON bookings.rid = restaurants.id
JOIN 
    customers ON bookings.cid = customers.id
JOIN (
    SELECT
        booking_details.booking_id,
        items.item_name,
        SUM(booking_details.quantity) AS total_quantity,
        items.item_name || ' (' || SUM(booking_details.quantity) || ')' AS item_info
    FROM
        booking_details
    JOIN 
        items ON booking_details.item_id = items.item_id
    GROUP BY
        booking_details.booking_id,
        items.item_name
) AS item_details ON bookings.booking_id = item_details.booking_id
WHERE 
    bookings.rid = $1 
GROUP BY 
    bookings.booking_id, 
    bookings.booking_date, 
    bookings.booking_time, 
    customers.first_name, 
    customers.last_name, 
    bookings.status, 
    bookings.num_guests,
    bookings.customer_started_order
ORDER BY 
    bookings.booking_time desc;
";


// Execute the query
$result = pg_query_params($conn, $query, array($restid));

if (!$result) {
    echo "Error in SQL query: " . pg_last_error();
    exit;
}

// Fetch the results
$orders = pg_fetch_all($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <style>
        .order-table th, .order-table td {
            text-align: center;
        }
        .order-status {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .status-preparing { background-color: orange; color: white; }
        .status-ready { background-color: lightgreen; color: black; }
        .status-served { background-color: green; color: white; }
        .status-completed { background-color: darkgreen; color: white; }
        .status-cancelled { background-color: red; color: white; }
        .status-confirm { background-color: blue; color: white; }
        .status-button { margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include "navbar.php";?> 

            <!-- Main Content -->
            <main class="col-md-10 main-content">
                <div class="page-header mt-4">
                    <h3>Order Management</h3>
                </div>

                <!-- Order Search and Filter -->
                <div class="row mb-4">
                    <div class="col-md-6" style="color:white;" >
                        <input type="text" class="form-control" id="searchOrder" placeholder="Search ..." >
                    </div>
                    <div class="col-md-6">
                        <select class="form-control" id="orderStatusFilter">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="preparing">Food Preparing</option>
                            <option value="ready">Ready to Serve</option>
                            <option value="served">Served</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="confirm">Confirm</option>
                        </select>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>All Orders</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped order-table">
                                    <thead>
                                        <tr>
                                            <th>Customer Name</th>
                                            <th>Date & Time</th>
                                            <th>Items</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="orderList">
                                        <?php
                                        // Loop through orders and display them
                                        foreach ($orders as $order) {
                                            $order_id = $order['booking_id'];
                                            $items = $order['item_names_with_quantities'];
                                            $status = $order['status'];
                                            $custname = $order['first_name'] . " " . $order['last_name'];
                                            $datetime = date('d-m-Y', strtotime($order['booking_date'])) . " " . date('h:i A', strtotime($order['booking_time']));
                                            // Set status class based on the status value
                                            $status_class = "status-" . strtolower($status);
                                        ?>
                                            <tr id="order-<?php echo $order_id; ?>">
                                                <td><?php echo $custname; ?></td>
                                                <td><?php echo $datetime; ?></td>
                                                <td><?php echo $items; ?></td>
                                                <td><span class="order-status <?php echo $status_class; ?>" id="status-<?php echo $order_id; ?>"><?php echo ucfirst($status); ?></span></td>
                                                <td>
                    <!-- Status update buttons -->
                    <button class="btn btn-primary btn-sm status-button" onclick="updateOrderStatus('<?php echo $order_id; ?>', 'confirm')">Confirm</button>

<button class="btn btn-warning btn-sm status-button" id="btnPreparing-<?php echo $order_id; ?>" 
    onclick="updateOrderStatus('<?php echo $order_id; ?>', 'preparing')" 
    <?php if ($order['customer_started_order'] == 'f' && $status == 'completed') echo 'disabled'; ?>>
    Preparing
</button>

<button class="btn btn-success btn-sm status-button" 
    onclick="updateOrderStatus('<?php echo $order_id; ?>', 'ready')" 
    <?php if ($order['customer_started_order'] == 'f' && $status == 'completed') echo 'disabled'; ?>>
    Ready
</button>

<button class="btn btn-success btn-sm status-button" 
    onclick="updateOrderStatus('<?php echo $order_id; ?>', 'served')" 
    <?php if ($order['customer_started_order'] == 'f' && $status == 'completed') echo 'disabled'; ?>>
    Served
</button>

<button class="btn btn-dark btn-sm status-button" 
    onclick="updateOrderStatus('<?php echo $order_id; ?>', 'completed')" 
    <?php if ($order['customer_started_order'] == 'f') echo 'disabled'; ?>>
    Completed
</button>

<button class="btn btn-danger btn-sm status-button" 
    onclick="updateOrderStatus('<?php echo $order_id; ?>', 'cancelled')">
    Cancelled
</button>


                                    </td>

                                            </tr>
                                        <?php } ?>
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

    <script>
        // Function to update order status
        function updateOrderStatus(orderID, status) {
            // Make AJAX request to update the status
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "update_order.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (xhr.status == 200) {
                    // Update the order status on the frontend
                    const statusText = document.querySelector(`#status-${orderID}`);
                    const row = document.querySelector(`#order-${orderID}`);
                    const statusClasses = ['status-preparing', 'status-ready', 'status-served', 'status-completed', 'status-cancelled', 'status-confirm'];
                    
                    // Remove existing status classes
                    statusClasses.forEach(cls => statusText.classList.remove(cls));
                    
                    // Set new status class and text
                    statusText.classList.add('status-' + status);
                    statusText.innerText = status.charAt(0).toUpperCase() + status.slice(1);

                    // Display success message
                    alert("Order status updated to " + statusText.innerText);
                } else {
                    alert("Failed to update status.");
                }
            };
            
            xhr.send("order_id=" + orderID + "&new_status=" + status);
        }

        document.getElementById('searchOrder').addEventListener('input', function(e) {
            let searchTerm = e.target.value.toLowerCase();
            let orders = document.querySelectorAll('#orderList tr');
            orders.forEach(function(row) {
                let orderID = row.querySelector('td').innerText;
                if (orderID.toLowerCase().includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Filter by order status
        document.getElementById('orderStatusFilter').addEventListener('change', function(e) {
            let statusFilter = e.target.value;
            let orders = document.querySelectorAll('#orderList tr');
            orders.forEach(function(row) {
                let status = row.querySelector('.order-status').innerText.toLowerCase();
                if (statusFilter === '' || status.includes(statusFilter.toLowerCase())) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
