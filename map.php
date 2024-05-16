<?php
include('partial/header.php');
include('sidebar.php');

// Function to convert GPS coordinates to latitude and longitude
function parseGPSCoordinate($coordinateString)
{
    $coordinates = explode(",", $coordinateString);
    return array('lat' => $coordinates[0], 'lng' => $coordinates[1]);
}

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
<div class="dashContent">
    <h2 class="h2">Where is my parking lot?</h2>

    <div id="map" style="height: 700px; width: 100%; margin-bottom: 20px;"></div>
</div>

<script>
    var map;
    var directionsService;
    var directionsRenderer;

    // Initialize and add the map
    function initMap() {
        // Create an empty LatLngBounds object
        var bounds = new google.maps.LatLngBounds();

        // The map
        map = new google.maps.Map(
            document.getElementById('map'), { zoom: 10 });

        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer();

        directionsRenderer.setMap(map);

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

            // Add click event listener to marker to open info window and display directions
            marker.addListener('click', function() {
                calculateAndDisplayRoute(marker.getPosition());
                infoWindow.open(map, marker);
            });
        })();
        <?php endforeach; ?>

        // Fit the map to the bounds
        map.fitBounds(bounds);
    }

    // Function to calculate and display route from current location to destination
    function calculateAndDisplayRoute(destination) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var origin = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };

            var request = {
                origin: origin,
                destination: destination,
                travelMode: 'DRIVING'
            };

            directionsService.route(request, function(result, status) {
                if (status === 'OK') {
                    directionsRenderer.setDirections(result);
                } else {
                    window.alert('Directions request failed due to ' + status);
                }
            });
        }, function() {
            handleLocationError(true);
        });
    }

    function handleLocationError(browserHasGeolocation) {
        window.alert(browserHasGeolocation ?
            'Error: The Geolocation service failed.' :
            'Error: Your browser doesn\'t support geolocation.');
    }
</script>

<!-- Load the Google Maps JavaScript API -->
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFB259S-So1zSXe1lXakzMGoe5VTluE7Q&callback=initMap">
</script>
</body>
