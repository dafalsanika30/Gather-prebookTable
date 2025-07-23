<?php
// Database connection details

include 'db.php';

// Query to retrieve restaurant details
$query = "
    SELECT * 
    FROM restaurants
    ORDER BY restaurant_name;
";

$result = pg_query($conn, $query);

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

  <?php include "navbar.php";?>

  <section class="container-fluid" style="margin-top: 80px;">
    <div class="row">
      <!-- Sidebar for Filters and Sort -->
      <div class="col-md-3 col-sm-12">
        <div class="filters">
          <h4>Filter & Sort</h4>
          <!-- Sort by -->
          <div class="form-group">
            <label for="sortSelect">Sort by</label>
            <select class="form-control" id="sortSelect" onchange="applyFilters()">
              <option value="name">Name</option>
              <option value="rating">Rating</option>
            </select>
          </div>

          <!-- Cuisine Filter -->
          <div class="form-group">
            <label for="cuisineSelect">Cuisine</label>
            <select class="form-control" id="cuisineSelect" onchange="applyFilters()">
              <option value="">All</option>
              <option value="Italian">Italian</option>
              <option value="Chinese">Chinese</option>
              <option value="Indian">Indian</option>
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
                              <p class="card-text" style="  text-transform: capitalize;">' . htmlspecialchars($row['description']) . '</p>
                              <p class="card-text">' . htmlspecialchars($row['address']) . '</p>
                      </div>
                    </a>
                  </div>
              ';
          }
          ?>
        </div>
      </div>
    </div>
        </section>

  
  <!-- Bootstrap and JS Scripts -->
  <script src="../js/bootstrap.bundle.min.js"></script>
  <script>
    function confirmLogout() {
    // Display confirmation alert
    var confirmLogout = confirm("Are you sure you want to logout?");
    if (confirmLogout) {
        // Redirect to logout.php for session destruction
        window.location.href = "logout.php";
    }
}
  </script> 
<?php include "footer.php";?>
</body>
</html>
