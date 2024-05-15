<?php
include('partial/header.php');
include('sidebar.php');
$successMessage = null;

// Function to convert GPS coordinates to latitude and longitude
function parseGPSCoordinate($coordinateString)
{
    $coordinates = explode(",", $coordinateString);
    return array('lat' => $coordinates[0], 'lng' => $coordinates[1]);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (empty($_POST["TotalSpaces"])) {
        die("Total Spaces is required");
    }

    if (empty($_POST["LotName"])) {
        die("Name is required");
    }

    if (empty($_POST["GPSCoordinate"])) {
        die("coordinates are required");
    }

    $TotalSpaces = $_POST["TotalSpaces"];
    $Name = $_POST["LotName"];
    $GPSCoordinate = $_POST["GPSCoordinate"];


    $mysqli = require __DIR__ . "/database.php";
    $stmt = $mysqli->prepare("INSERT INTO parkinglots (TotalSpaces,LotName,GPSCoordinate) VALUES (?,?,?)");

    $stmt->bind_param("iss", $TotalSpaces,$Name, $GPSCoordinate);
    $success = $stmt->execute();

    if ($success) {
        $successMessage = "Parking lot added successfully!";
    } else {
        $successMessage = "Error adding parking lot.";
    }
    $stmt->close();
    $mysqli->close();
}

// Connect to the database
$mysqli = require __DIR__ . "/database.php";

// Retrieve parking lot data from the database
$result = $mysqli->query("SELECT LotName, GPSCoordinate FROM parkinglots");

// Initialize array to store parking lot data
$parkingLots = array();

// Iterate through each row in the result set
while ($row = $result->fetch_assoc()) {
    // Parse GPS coordinates to latitude and longitude
    $coordinates = parseGPSCoordinate($row['GPSCoordinate']);

    // Add parking lot data to the array
    $parkingLots[] = array(
        'name' => $row['LotName'],
        'lat' => $coordinates['lat'],
        'lng' => $coordinates['lng']
    );
}

// Close database connection
$mysqli->close();
?>

<body>
<div class="box_dash_other">
    <h1>Add a New Car</h1>
    <div id="loginform">
        <form method="post" id="addLotForm">
            <label class="label" for="Name">Enter name of parking lot:</label>
            <input class="input" type="text" name="LotName" id="LotName">
            <label class="label" for="TotalSpaces">Enter amount of spaces:</label>
            <input class="input" type="number" name="TotalSpaces" id="TotalSpaces">
            <input class="input" type="hidden" name="GPSCoordinate" id="GPSCoordinate">

            <button class="button" type="submit">Add Lot</button>
        </form>
        <?php if ($successMessage): ?>
            <div class="successMessage"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>
    </div>

    <h2 class="h2">Choose location:</h2>

    <div id="map" style="height: 400px; width: 70%; margin-bottom: 20px;"></div>
</div>

<script>
    var map;
    var marker;

    // Initialize and add the map
    function initMap() {
        // Create an empty LatLngBounds object
        var bounds = new google.maps.LatLngBounds();

        // The map
        map = new google.maps.Map(
            document.getElementById('map'), { zoom: 10 });

        // Iterate through each parking lot
        <?php foreach ($parkingLots as $parkingLot): ?>
        (function() { // Create a closure to encapsulate marker and infoWindow variables
            var position = { lat: <?php echo $parkingLot['lat']; ?>, lng: <?php echo $parkingLot['lng']; ?> };

            // Create marker
            var marker = new google.maps.Marker({
                position: position,
                map: map,
                title: '<?php echo $parkingLot['name']; ?>' // Set marker's title to parking lot name
            });

            // Extend the bounds to include marker's position
            bounds.extend(position);

            // Create info window
            var infoWindow = new google.maps.InfoWindow({
                content: '<?php echo $parkingLot['name']; ?>' // Set content of info window to parking lot name
            });

            // Add click event listener to marker to open info window
            marker.addListener('click', function() {
                infoWindow.open(map, marker);
            });
        })();
        <?php endforeach; ?>

        // Fit the map to the bounds
        map.fitBounds(bounds);

        // Add click event listener to the map
        map.addListener('click', function(event) {
            placeMarker(event.latLng);
        });
    }

    // Function to place a marker on the map
    function placeMarker(location) {
        // Remove previous marker if exists
        if (marker) {
            marker.setMap(null);
        }

        // Create a new marker
        marker = new google.maps.Marker({
            position: location,
            map: map
        });

        // Update hidden input field with new coordinates
        document.getElementById('GPSCoordinate').value = location.lat() + ',' + location.lng();
    }

    // Submit form when Add Lot button is clicked
    document.getElementById('addLotForm').addEventListener('submit', function(event) {
        // Prevent default form submission
        event.preventDefault();

        // Check if marker is placed on the map
        if (marker) {
            // If marker is placed, submit the form
            this.submit(); // Submit the form
        } else {
            // If marker is not placed, show an alert message
            alert("Please choose a location on the map.");
            // Optionally, you can focus on the map or any other action to prompt the user to select a location
        }
    });
</script>

<!-- Load the Google Maps JavaScript API -->
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFB259S-So1zSXe1lXakzMGoe5VTluE7Q&callback=initMap">
</script>
</body>
