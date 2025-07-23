<?php
// Database connection details
include 'db.php';

// Get filter and sort parameters from the URL
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'restaurant_name'; // Default sort by name
$cuisineFilter = isset($_GET['cuisine']) ? $_GET['cuisine'] : '';

// Base SQL query to retrieve restaurant details
$sql = "SELECT * FROM restaurants WHERE 1=1";

// Initialize the params array
$params = array();

// Apply cuisine filter if selected
if ($cuisineFilter) {
    $sql .= " AND cuisine_type = $1"; // Filter by cuisine
    $params[] = $cuisineFilter; // Add cuisine filter parameter
}

// Apply sorting
if ($sortBy == 'rating') {
    $sql .= " ORDER BY rating DESC"; // Assuming there's a 'rating' column
} else {
    $sql .= " ORDER BY restaurant_name ASC"; // Default sorting by name
}

// Execute the query with parameters
$result = pg_query_params($conn, $sql, $params);

if (!$result) {
    die("Error in SQL query: " . pg_last_error());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Restaurant Finder</title>
  <!-- Bootstrap CSS -->
  <link href="../css/bootstrap.min.css" rel="stylesheet">
  <link href="CSS/home.css" rel="stylesheet">
  
  <style>
    body {
      background-color: #f8f9fa;
    }

    .filters {
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .filters h4 {
      margin-bottom: 20px;
    }

    .card {
      margin-bottom: 20px;
      transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .card:hover {
      transform: scale(1.05);
      box-shadow: 0 15px 25px rgba(0, 0, 0, 0.2);
    }

    .card img {
      max-height: 200px;
      object-fit: cover;
      border-radius: 8px;
    }

    .card-body {
      text-align: center;
    }

    @media (max-width: 768px) {
      .filters {
        margin-bottom: 20px;
      }
    }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg fixed-top" style="background-color: black;padding: 15px 15px;font-size: 15px;width: auto;">
    <div class="container-fluid">
        <!-- Brand Logo -->
        <a class="navbar-brand" href="#" style="color: white;">
            <img src="Photos/dinner.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top" >
            Gather
        </a>

        <!-- Toggler for Mobile View -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon" style="border-color:grey ;"></span>
        </button>

        <!-- Collapsible Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.html">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="bookatable.html" >Book a Table</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="aboutus.html" >About Us</a>
                </li>
            </ul>
        </div>
    </div>
  </nav>

  <div class="container-fluid" style="margin-top: 80px;">
    <div class="row">
      <!-- Sidebar for Filters and Sort -->
      <div class="col-md-3 col-sm-12">
        <div class="filters">
          <h4>Filter & Sort</h4>
          <!-- Sort by -->
          <div class="form-group">
            <label for="sortSelect">Sort by</label>
            <select class="form-control" id="sortSelect" onchange="applyFilters()">
              <option value="name" <?php echo ($sortBy == 'restaurant_name') ? 'selected' : ''; ?>>Name</option>
              <option value="rating" <?php echo ($sortBy == 'rating') ? 'selected' : ''; ?>>Rating</option>
            </select>
          </div>

          <!-- Cuisine Filter -->
          <div class="form-group">
            <label for="cuisineSelect">Cuisine</label>
            <select class="form-control" id="cuisineSelect" onchange="applyFilters()">
              <option value="">All</option>
              <option value="Italian" <?php echo ($cuisineFilter == 'Italian') ? 'selected' : ''; ?>>Italian</option>
              <option value="Chinese" <?php echo ($cuisineFilter == 'Chinese') ? 'selected' : ''; ?>>Chinese</option>
              <option value="Indian" <?php echo ($cuisineFilter == 'Indian') ? 'selected' : ''; ?>>Indian</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Restaurant Cards Display -->
      <div class="col-md-9 col-sm-12">
        <div id="restaurantCards" class="row">
          <?php
            while ($row = pg_fetch_assoc($result)) {
              $menuImageExtension = pathinfo($row['menu_image_path'], PATHINFO_EXTENSION);
              echo '
                  <div class="col-md-4 col-sm-6 col-12">
                  <a href="restaurant.php?id=' .$row['id']. '" style="text-decoration: none; color: inherit;">
                      <div class="card">
                          <img src="uploads/' . ($row['restaurant_name']) . '_restaurantImage.'.$menuImageExtension.'" class="card-img-top" alt="' . htmlspecialchars($row['restaurant_name']) . '">
                          <div class="card-body">
                              <h5 class="card-title">' . htmlspecialchars($row['restaurant_name']) . '</h5>
                              <p class="card-text">Address: ' . htmlspecialchars($row['address']) . '</p>
                              <p class="card-text">Cuisine: ' . htmlspecialchars($row['cuisine_type']) . '</p>
                              <p class="card-text">Seating Capacity: ' . htmlspecialchars($row['seating_capacity']) . '</p>
                              <p class="card-text">Availability: ' . htmlspecialchars($row['availability_status']) . '</p>
                              <p class="card-text">Business Hours: ' . htmlspecialchars($row['business_hours']) . '</p>
                          </div>
                      </div>
                    </a>
                  </div>
              ';
          }
          ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap and JS Scripts -->
  <script src="../js/bootstrap.bundle.min.js"></script>

  <script>
    function applyFilters() {
      var sortBy = document.getElementById('sortSelect').value;
      var cuisine = document.getElementById('cuisineSelect').value;
      var url = new URL(window.location.href);
      url.searchParams.set('sort', sortBy);
      url.searchParams.set('cuisine', cuisine);
      window.location.href = url.toString();
    }
  </script>

</body>
</html>
