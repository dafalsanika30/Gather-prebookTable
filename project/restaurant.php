
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Website</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="CSS/home.css" rel="stylesheet">
    
    <style>
        .carousel {
            max-width: 100%; /* Full width for column layout */
            margin-bottom: 20px;
        }
        
        .form-container, .amenities-container, .reviews-container {
            max-width: 400px;
            margin: auto;
        }
        
        .menu-photos img {
            width: 100%;
            height: auto;
            margin-bottom: 10px;
            border-radius: 10px;
        }
        .star-rating input {
        display: none;
        }

        .star-rating label {
        font-size: 30px;
        color: #ddd;
        cursor: pointer;
        }

        .star-rating input:checked ~ label,
        .star-rating input:hover ~ label,
        .star-rating input:focus ~ label {
            color: #FFD700;
        }

        .star-rating input:checked ~ label:hover,
        .star-rating input:checked ~ label:hover ~ label {
            color: #FFD700;
        }
        
        
        @media (min-width: 768px) {
            .main-container {
                display: flex;
                gap: 30px;
            }
            
            .left-column, .right-column {
                flex: 1;
            }
        }
        
        @media (min-width: 768px) {
            .main-container {
                display: flex;
                gap: 30px;
            }

            .left-column, .right-column {
                flex: 1;
            }
        }
    </style>
</head>
<body>
        <?php include "navbar.php";?>
            <?php include 'db.php'; 
            $id = $_GET['id']; // Get the ID from the URL
            // session_start();

            $query = "SELECT * FROM restaurants WHERE id = $1";
            $result = pg_query_params($conn, $query, array($id));


            $row = pg_fetch_assoc($result);
            $restImageExtension = pathinfo($row['restaurant_image_path'], PATHINFO_EXTENSION);
            $menuImageExtension = pathinfo($row['menu_image_path'], PATHINFO_EXTENSION);
            $imagePath = "uploads/".($row['restaurant_name']) . "_restaurantImage." . $restImageExtension;
            $menuimagePath = "uploads/".($row['restaurant_name']) . "_menuImage." . $menuImageExtension;
            $features = explode(",", $row['features']); // Split the string into an array
            $business_hours=$row['business_hours'];

            list($startTime, $endTime) = explode(" - ", $business_hours);

            // Convert times to timestamps
            $startTimestamp = strtotime($startTime);
            $endTimestamp = strtotime($endTime);

            ?>
    <section class="p-5 text-center bg-light" style="margin-top: 50px;">
        <div class="container">
        <h2>Welcome to <?php echo $row['restaurant_name'];?></h2>        
        <p style="text-transform: capitalize;"><?php echo $row['description'];?></p>
    </div>
    </section>

    <div class="container my-5 main-container">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Image Slider -->
            <div class="row">
                <div class="col-12">
                    <img src="<?php echo $imagePath; ?>" class="d-block w-100" alt="Restaurant Image">
                </div>
            </div>


            <!-- Restaurant Description -->
            <div class="description-section">
                <h3>Description</h3>
                <p><?php echo $row['description'];?></p>
                <p><strong>Seating Capacity:</strong> <?php echo $row['seating_capacity']; ?></p>
                <p><strong>Availability Status:</strong> <?php echo $row['availability_status']; ?></p>
                <p><strong>Business Hours:</strong> <?php echo $row['business_hours']; ?></p>
            </div>


            <!-- Facilities & Amenities -->
            <div class="amenities-container bg-light p-3 rounded" style="margin-left:-5px;">
                <h4>Amenities</h4>
                <ul class="list-unstyled">
                <?php
                    // Loop through the amenities array and display each one
                    foreach ($features as $feature) {
                        echo '<li>✔ ' . trim($feature) . '</li>';
                    }
                ?>

                </ul>
            </div>

            
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <div class="form-container p-4 border rounded bg-light">
                <h4 class="text-center">Book Your Table</h4>
                <form method="POST" action="mybooking.php">
                    
                    <input type="hidden" name="restaurant_id" value="<?php echo $id; ?>">

                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" name="date"
                        min="<?php echo date('Y-m-d'); ?>" 
                        id="date">
                    </div>

                    <div class="mb-3">
                    <label for="seats" class="form-label">Total Number of Seats</label>
                    <input type="number" class="form-control" 
                        min="1" 
                        id="seats" 
                        name="seats" 
                        required>
                    </div>
                    <div class="mb-3">
                    <label for="text" class="form-label">Speacial Requests</label>
                    <input type="text" class="form-control"
                        id="seats" 
                        name="special_requests" 
                        required>
                    </div>

                    <div class="mb-3">
                        <label for="time" class="form-label">Time</label>
                        <select class="form-control" id="time" name="time">
                            <?php
                            for ($time = $startTimestamp; $time <= $endTimestamp; $time += 60 * 60) {
                                $formattedTime = date("h:i A", $time);
                                echo "<option value='" . $formattedTime . "'>" . $formattedTime . "</option>";
                            }
                            ?>
                        </select>
                    </div>


                    <hr>
                    <h5 class="text-center">Select Your Menu</h5>

                    <?php
                    // Query to fetch items for the restaurant
                    $query1 = "SELECT * FROM items WHERE restaurant_id = $1";
                    $result1 = pg_query_params($conn, $query1, array($id));

                    // Check if menu items exist
                    if (pg_num_rows($result1) > 0) {
                        echo '<table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Dish</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>';
                        
                        // Loop through the items and generate rows
                        while ($row1 = pg_fetch_assoc($result1)) {
                            echo '<tr>
                                    <td>' . $row1['item_name'] . '<br>' . $row1['description'] . '<br>';
                            
                            if ($row1['specialty'] == 'yes') {
                                echo '<button class="btn btn-primary btn-sm">Specialty</button>';
                            }
                            
                            echo '</td>
                                    <td>₹ ' . $row1['price'] . '</td>
                                    <td>
                                        <input type="number" name="menu_items[' . $row1['item_name'] . '][quantity]" 
                                               class="form-control item-quantity" data-price="' . $row1['price'] . '" 
                                               min="0" max="20" placeholder="0">
                                        <!-- Hidden input to pass the price of the item -->
                                        <input type="hidden" name="menu_items[' . $row1['item_name'] . '][price]" value="' . $row1['price'] . '">
                                        <input type="hidden" name="menu_items[' . $row1['item_name'] . '][item_id]" value="' . $row1['item_id'] . '">

                                    </td>
                                  </tr>';
                        }
                        
                        echo '</tbody></table>';
                        
                    } else {
                        echo '<p>No menu items available.</p>';
                    }
                    ?>
                    <div class="text-end">
                        <h5>Total: ₹ <span id="totalPrice" name="price" >0</span></h5>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Submit</button>
                </form>
            </div>
        </div>
    </div>
    

    <!-- Menu Card Photos -->
    <section class="p-5 bg-white">
        <div class="container">
            <h3 class="text-center">Menu Card Photos</h3>
            <div class="row menu-photos">
                <div class="col-md-4"><img src="<?php echo $menuimagePath; ?>" alt="Menu 1"></div>
                <div class="col-md-4"><img src="<?php echo $menuimagePath; ?>" alt="Menu 2"></div>
                <div class="col-md-4"><img src="<?php echo $menuimagePath; ?>"  alt="Menu 3"></div>
            </div>
        </div>
    </section>

    <!-- Menu List -->
    <section class="p-5 bg-light">
        <div class="container">
            <h3 class="text-center">Our Menu</h3>
            <?php
            $query1 = "SELECT * FROM items WHERE restaurant_id = $1";
            $result1 = pg_query_params($conn, $query1, array($id));

            // Check if there are any items
            if (pg_num_rows($result1) > 0) {
                // Start the table
                echo '<table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Dish</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>';
                
                // Loop through the result and create rows for each item
                while ($row1 = pg_fetch_assoc($result1)) {
                    echo '<tr>
                            <td>' . $row1['item_name'] . '<br>Description: '.$row1['description'].'<br>';
                                if ($row1['specialty'] == 'yes') {
                                    echo '<button class="btn btn-primary btn-sm">Speciality</button>';
                                }
                                    echo '</td>                        
                            <td>₹ ' . $row1['price'] . '</td>
                        </tr>';
                }

                // End the table
                echo '</tbody></table>';
            } else {
                // No items found message
                echo '<p>No menu items available.</p>';
            }
            ?>
        </div>
    </section>

    <!-- Reviews Section -->
    <!-- <section class="p-5 bg-white">
        <div class="container">
            <h3 class="text-center">Customer Reviews</h3>
            <div class="reviews-container">
                <p>⭐️⭐️⭐️⭐️⭐️ - "Amazing food and great ambiance!" - John Doe</p>
                <p>⭐️⭐️⭐️⭐️ - "Loved the desserts. A must-visit!" - Jane Smith</p>
            </div>
        </div>
    </section>
    <section class="p-5 bg-white">
        <div class="container">
            <h3 class="text-center">Feedback</h3>
            <div class="feedback-container bg-light p-4 rounded">
                <form>
                    <div class="mb-3">
                        <label for="feedback" class="form-label">Your Feedback</label>
                        <textarea class="form-control" id="feedback" rows="4" placeholder="Share your experience"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rate Us</label>
                        <div class="star-rating">
                            <input type="radio" name="rating" id="star5" value="5">
                            <label for="star5">&#9733;</label>
                            <input type="radio" name="rating" id="star4" value="4">
                            <label for="star4">&#9733;</label>
                            <input type="radio" name="rating" id="star3" value="3">
                            <label for="star3">&#9733;</label>
                            <input type="radio" name="rating" id="star2" value="2">
                            <label for="star2">&#9733;</label>
                            <input type="radio" name="rating" id="star1" value="1">
                            <label for="star1">&#9733;</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Submit Feedback</button>
                </form>
            </div>
        </div>
    </section> -->
    <!-- Feedback Section -->
    <section class="p-5 bg-white">
    <div class="container">
        <h3 class="text-center">Customer Reviews</h3>
        <div class="reviews-container">
            <?php
            // print_r($_SESSION);
            // Fetch feedback for the current restaurant
            $feedbackQuery = "SELECT 
            customers.first_name, 
            customers.last_name, 
            feedback.feedback_text, 
            feedback.rating, 
            feedback.created_at 
        FROM 
            feedback
        INNER JOIN 
            customers 
        ON 
            feedback.customer_id = customers.id
        WHERE 
            feedback.restaurant_id = $1
        ORDER BY 
            feedback.created_at DESC";
        
            $feedbackResult = pg_query_params($conn, $feedbackQuery, array($id));

            // Check if feedback exists
            if (pg_num_rows($feedbackResult) > 0) {
                while ($feedbackRow = pg_fetch_assoc($feedbackResult)) {
                    echo '<p>';
                    // Display star ratings
                    for ($i = 0; $i < $feedbackRow['rating']; $i++) {
                        echo '⭐️';
                    }
                    // Display feedback text and customer details
                    echo " - \"" . htmlspecialchars($feedbackRow['feedback_text']) . "\" - " 
                        . htmlspecialchars($feedbackRow['first_name'])." ".$feedbackRow['last_name'] . " (" 
                        . date("d M Y", strtotime($feedbackRow['created_at'])) . ")";
                    echo '</p>';
                }
            } else {
                echo '<p>No feedback available yet. Be the first to leave a review!</p>';
            }
            ?>
        </div>
    </div>
</section>


<!-- Feedback Form -->
<!-- <section class="p-5 bg-light">
    <div class="container">
        <h3 class="text-center">Feedback</h3>
        <div class="feedback-container bg-light p-4 rounded">
            <form method="POST" action="submit_feedback.php">
                <!-- 
             -->
                
                <!-- <input type="hidden" name="restaurant_id" value="<?php echo $id; ?>">
                <input type="hidden" name="customer_id" value="<?php echo $_SESSION['id']; ?>">

                   
                
                <div class="mb-3">
                    <label for="feedback_text" class="form-label">Your Feedback</label>
                    <textarea class="form-control" id="feedback_text" name="feedback_text" rows="4" placeholder="Share your experience" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Rate Us</label>
                    <div class="star-rating">
                        <input type="radio" name="rating" id="star5" value="5" required>
                        <label for="star5">&#9733;</label>
                        <input type="radio" name="rating" id="star4" value="4">
                        <label for="star4">&#9733;</label>
                        <input type="radio" name="rating" id="star3" value="3">
                        <label for="star3">&#9733;</label>
                        <input type="radio" name="rating" id="star2" value="2">
                        <label for="star2">&#9733;</label>
                        <input type="radio" name="rating" id="star1" value="1">
                        <label for="star1">&#9733;</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Submit Feedback</button>
            </form>
        </div>
    </div>
</section> --> -->
<section class="p-5 bg-light">
    <div class="container">
        <h3 class="text-center">Feedback</h3>
        <div class="feedback-container bg-light p-4 rounded">
            <form method="POST" action="submit_feedback.php">
                <input type="hidden" name="restaurant_id" value="<?php echo $id; ?>">
                <input type="hidden" name="customer_id" value="<?php echo $_SESSION['id']; ?>">

                <div class="mb-3">
                    <label for="feedback_text" class="form-label">Your Feedback</label>
                    <textarea class="form-control" id="feedback_text" name="feedback_text" rows="4" placeholder="Share your experience" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Rate Us</label>
                    <div class="star-rating" style="direction: rtl; text-align: left;">
                        <input type="radio" name="rating" id="star5" value="5" required>
                        <label for="star5">&#9733;</label>
                        <input type="radio" name="rating" id="star4" value="4">
                        <label for="star4">&#9733;</label>
                        <input type="radio" name="rating" id="star3" value="3">
                        <label for="star3">&#9733;</label>
                        <input type="radio" name="rating" id="star2" value="2">
                        <label for="star2">&#9733;</label>
                        <input type="radio" name="rating" id="star1" value="1">
                        <label for="star1">&#9733;</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Submit Feedback</button>
            </form>
        </div>
    </div>
</section>

    <?php include "footer.php"; ?>

    <script src="../js/bootstrap.bundle.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('menuForm');
    const quantityInputs = document.querySelectorAll('.item-quantity');
    const totalPriceElement = document.getElementById('totalPrice');

    // Function to update totals
    function updateTotals() {
        let totalPrice = 0;

        quantityInputs.forEach(input => {
            const price = parseFloat(input.getAttribute('data-price'));
            const quantity = parseInt(input.value) || 0;

            // Calculate subtotal for the item
            const subtotal = price * quantity;

            // Add to total price
            totalPrice += subtotal;
        });

        // Update total price display
        totalPriceElement.textContent = totalPrice.toFixed(2);
    }

    // Add event listener to quantity inputs
    quantityInputs.forEach(input => {
        input.addEventListener('input', function () {
            const value = parseInt(this.value);

            // Ensure value is within range
            if (value < 0 || value > 20) {
                alert('Quantity must be between 0 and 20.');
                this.value = '';
            }

            updateTotals();
        });
    });

    // Initial total calculation
    updateTotals();
});
</script>
