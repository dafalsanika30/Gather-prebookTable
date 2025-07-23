<?php include 'db.php'; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gather - Restaurant Booking</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="CSS/home.css" rel="stylesheet">
    <script src="../js/bootstrap.bundle.js"></script>
    <script>
            function showHint(str) {
                if (str.length == 0) {
                    document.getElementById("txtHint").style.display = "none";  // Hide suggestions when input is empty
                    return;
                } else {
                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function () {
                        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                            var response = xmlhttp.responseText;
                            var hintContainer = document.getElementById("txtHint");

                            // If response is empty, hide the suggestion box
                            if (response.trim() === "") {
                                hintContainer.style.display = "none";
                            } else {
                                hintContainer.innerHTML = response;  // Set the suggestions
                                hintContainer.style.display = "block";  // Show the suggestions dropdown
                            }
                        }
                    }
                    xmlhttp.open("GET", "search_ajax.php?q=" + str, true);  // Make AJAX request to PHP
                    xmlhttp.send();
                }
            }

            

    </script>
    <style>
    #txtHint {
        position: absolute;
        top: 100%;   /* Position directly below the input box */
        left: 0;
        width: 100%;  /* Ensure it matches the width of the input box */
        max-height: 200px; /* Adjust this value to your preference */
        overflow-y: auto;  /* Adds a scrollbar if the list exceeds max-height */
        border: 1px solid #ccc; /* Optional: Adds a border around the suggestion box */
        border-radius: 5px;
        position: absolute; /* Position it below the search box */
        background-color: #fff; /* Optional: Sets the background color */
        z-index: 10; /* Ensure the suggestion box appears above other content */
        font-size: 25px;

    }

    .suggestions-list {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    .suggestion-item {
        padding: 10px;
        cursor: pointer;
    }

    .suggestion-item:hover {
        background-color: #f0f0f0; /* Highlight on hover */
    }
    </style>
</head>
<body>
<?php include "navbar.php"; ?>


<section class="image"
style="background-image: url(Photos/iamge4.png); margin-top: 0;">
<div class="searchtext">
    <div class="text">
        <h1>Crave, Book, Enjoy – <span>All in No Time!</span></h1>
        <form id="searchForm" method="GET" action="restaurant_search.php">
            <div class="restsearch">
                <span class="icon">
                    <img src="Photos/magnifier.png" width="100%" height="30px" viewBox="0 0 20 20">
                </span>
                <input type="text" id="restaurantSearch" name="search_query" placeholder="Search for Restaurants, Cuisines, Location..." autocomplete="off" maxlength="50" onkeyup="showHint(this.value)">
                <button class="searchbtn" id="searchBtn" type="submit">Search</button>
            </div>
        </form>

        <div id="txtHint"></div> <!-- Suggestions will appear here -->        
        <!-- Container for displaying search suggestions -->
    </div>
</div>
</section>

<div class="cards">
    <h1>
        <img src="Photos/restaurant.png" alt="Icon" width="30" height="30" class="icon">
        Restaurants Near You

    </h1>
    <div class="row">
        <?php
        // Fetch restaurant details from the database
        $query = "SELECT * FROM restaurants";
        $result = pg_query($conn, $query);
        
        if ($result) {
            while ($row = pg_fetch_assoc($result)) {
                $menuImageExtension = pathinfo($row['menu_image_path'], PATHINFO_EXTENSION);
                echo '<div class="col-md-3 col-sm-6 col-xs-12 mb-4">';
                echo '    <a href="restaurant.php?id=' .$row['id']. '" style="text-decoration: none; color: inherit;">';
                echo '        <div class="card">';
                echo '            <img src="uploads/' . ($row['restaurant_name']) . '_restaurantImage.'.$menuImageExtension.'" class="card-img-top" alt="Restaurant Image" width="500" height="250">';
                echo '            <div class="card-body">';
                echo '                <h5 class="card-title"><b>' . ($row['restaurant_name']) . '</b></h5>';
                echo '                <h6 class="card-text" style="text-transform: capitalize;">' . ($row['description']) . '</h6>';
                echo '                <p class="card-text">' . ($row['address']) . '</p>';
                echo '                <div class="imclass">';
                // echo '                    <span class="btn btn-success">' . ($row['rating']);
                echo '                        <img src="Photos/star.png" width="15px" height="15px" alt="Star">';
                echo '                    </span>';
                echo '                </div>';
                echo '            </div>';
                echo '        </div>';
                echo '    </a>';
                echo '</div>';
            }
        } else {
            echo '<p>No restaurants found.</p>';
        }
        ?>
    <a href="bookatable.php" class="see-more-link">See More....</a>
    </div>
</div>

</div>
 
<!-- Footer Section -->
        <?php include "footer.php"; ?>
</body>

</html>