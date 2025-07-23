<?php
// Connect to the database
include "db.php";

// Fetch total reservations
$totalReservationsQuery = "SELECT COUNT(*) as total FROM bookings";
$totalReservationsResult = pg_query($conn, $totalReservationsQuery);
$totalReservations = pg_fetch_assoc($totalReservationsResult)['total'];

// Fetch total restaurants
$totalRestaurantsQuery = "SELECT COUNT(*) as total FROM restaurants";
$totalRestaurantsResult = pg_query($conn, $totalRestaurantsQuery);
$totalRestaurants = pg_fetch_assoc($totalRestaurantsResult)['total'];

// Fetch reservations by month
$reservationsByMonthQuery = "
    SELECT TO_CHAR(booking_date, 'Mon') as month, COUNT(*) as count
    FROM bookings
    GROUP BY TO_CHAR(booking_date, 'Mon')
    ORDER BY MIN(booking_date)";
$reservationsByMonthResult = pg_query($conn, $reservationsByMonthQuery);

$reservationsData = [];
while ($row = pg_fetch_assoc($reservationsByMonthResult)) {
    $reservationsData[$row['month']] = $row['count'];
}

// Fetch top-performing restaurants
$topRestaurantsQuery = "
    SELECT r.restaurant_name, COUNT(b.booking_id) as reservations, AVG(i.price) as avg_price
    FROM restaurants r
    LEFT JOIN bookings b ON r.id = b.rid
    LEFT JOIN items i ON r.id = i.restaurant_id
    GROUP BY r.restaurant_name
    ORDER BY reservations DESC
    LIMIT 3";
$topRestaurantsResult = pg_query($conn, $topRestaurantsQuery);

$topRestaurants = [];
while ($row = pg_fetch_assoc($topRestaurantsResult)) {
    $topRestaurants[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include "navbar.html"; ?>            


            <!-- Main Content -->
            <main class="col-md-10 main-content">
                <div class="page-header">
                    <h3>Analytics Dashboard</h3>
                </div>

                <!-- Summary Cards -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5>Total Reservations</h5>
                                <h3><?= $totalReservations ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5>Total Restaurants</h5>
                                <h3><?= $totalRestaurants ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="row mt-5">
                    <!-- Reservations Chart -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Reservations by Month</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="reservationsChart"></canvas>
                            </div>
                        </div>
                    </div>

                <!-- Analytics Table -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Top Performing Restaurants</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-dark">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Restaurant Name</th>
                                                <th>Total Reservations</th>
                                                <th>Average Price per Item</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($topRestaurants as $index => $restaurant): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= $restaurant['restaurant_name'] ?></td>
                                                <td><?= $restaurant['reservations'] ?></td>
                                                <td><?= number_format($restaurant['avg_price'], 2) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Reservations Chart Data
        const reservationsData = <?= json_encode(array_values($reservationsData)) ?>;
        const reservationLabels = <?= json_encode(array_keys($reservationsData)) ?>;

        // Render Reservations Chart
        const reservationsChartCtx = document.getElementById('reservationsChart').getContext('2d');
        new Chart(reservationsChartCtx, {
            type: 'bar',
            data: {
                labels: reservationLabels,
                datasets: [{
                    label: 'Reservations',
                    data: reservationsData,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>
