<?php
include('partial/header.php');
include('sidebar.php');
$mysqli = require __DIR__ . "/database.php";
$loadChart=false;

$stmt = $mysqli->prepare("SELECT LotName FROM parkingLots");
$stmt->execute();
$result = $stmt->get_result();
$parkingLot = $result->fetch_all(MYSQLI_ASSOC);

// Determine if the user is an admin
$isAdmin = intval($user["IsAdmin"]) > 0;

// Prepare the appropriate SQL query
if ($isAdmin):
    $query = "SELECT timestart, timeend FROM booking";  // Admins see all bookings

if (!$isAdmin){
    die("you are not authorized to access this page");
}
// Prepare the SQL statement
$stmt = $mysqli->prepare($query);

if ($stmt === false) {
    die("Error preparing SQL statement: " . $mysqli->error);  // SQL preparation failed
}



// Execute the SQL statement
if (!$stmt->execute()) {
    die("Error executing SQL statement: " . $stmt->error);  // SQL execution failed
}

// Retrieve the results
$result = $stmt->get_result();

if ($result === false) {
    die("Error retrieving results: " . $stmt->error);  // Fetching results failed
}
$times = [];
// Check if there are any results
if ($result->num_rows === 0) {
    echo "No bookings found.";  // No results, likely no bookings for this user
} else {
    // Store the results in an array
    while ($row = $result->fetch_assoc()) {
        $times[] = $row;  // Store each booking record
    }

    // Output the results for debugging or processing
//    print_r($times);  // Output the bookings data (for debugging or further processing)
}

// Close the statement
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selectedParkingLot = isset($_POST['parkingLot']) ? $_POST['parkingLot'] : ""; // Get the selected value
    $stmt = $mysqli->prepare("SELECT TotalSpaces FROM parkingLots where LotName=?");
    $stmt->bind_param("s", $_POST['parkingLot']);
    $stmt->execute();
    $result1 = $stmt->get_result();
    $parkingSpaces = null;
    if ($row = $result1->fetch_assoc()) {
        $parkingSpaces = intval($row['TotalSpaces']);
        $loadChart = true;

    }

        // Fetch the booking data for the selected parking lot
    $stmt = $mysqli->prepare("SELECT timestart, timeend FROM booking WHERE LotName = ?");
    $stmt->bind_param("s", $_POST['parkingLot']);
    if (!$stmt->execute()) {
        die("Error executing SQL statement: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $times = [];
    while ($row = $result->fetch_assoc()) {
        $times[] = $row;
    }

// Function to get occupied spaces per day for a given month
    function getOccupiedSpacesPerDay($times, $year, $month)
    {
        $occupiedSpaces = [];

        // Get the number of days in the specified month
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Initialize occupied spaces for each day in the month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf("%04d-%02d-%02d", $year, $month, $day);
            $occupiedSpaces[$date] = 0;
        }

        // Update occupied spaces based on bookings
        foreach ($times as $booking) {
            $bookingStart = strtotime(date('Y-m-d', strtotime($booking['timestart'])));
            $bookingEnd = strtotime(date('Y-m-d', strtotime($booking['timeend'])));

            foreach (array_keys($occupiedSpaces) as $date) {
                $dayTimestamp = strtotime($date);

                if ($bookingStart <= $dayTimestamp && $dayTimestamp <= $bookingEnd) {
                    $occupiedSpaces[$date]++;
                }
            }
        }

        return $occupiedSpaces;
    }

// Get the current year
    $currentYear = date("Y");
    if ($loadChart) {
    $chartHTML = ""; // Reset the chart HTML
    for ($month = 1; $month <= 12; $month++) {
        $occupiedSpaces = getOccupiedSpacesPerDay($times, $currentYear, $month);

        $monthName = date("F", mktime(0, 0, 0, $month, 1));
        $dates = array_keys($occupiedSpaces);
        $counts = array_values($occupiedSpaces);

        // Create a canvas for each chart with a unique ID
        $chartHTML .= "
       <div id='chart-container-$month' class='chart-container' style='display: none;'>
        <canvas id='occupiedSpacesChart-$month' width='400' height='200'></canvas>
        <script>
            var ctx = document.getElementById('occupiedSpacesChart-$month').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: " . json_encode($dates) . ",
                    datasets: [{
                        label: 'Occupied Spaces in $monthName',
                        data: " . json_encode($counts) . ",
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: $parkingSpaces // Maximum number of spaces in the selected parking lot
                        }
                    }
                }
            });
        </script>
    </div>";
    }
    }
    }

    endif;

?>

<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<!-- Buttons to navigate between graphs -->
<div class='dashContent'>
    <div id="displayBookingForm">
    <form method="post" id="bookingForm">
<!--        <label for="parkingLot">Parking Lot:</label>-->
        <select name="parkingLot" id="parkingLot">
            <option value="">Select a Parking Lot</option>
            <?php
            foreach ($parkingLot as $lot) {
                $isSelected = $selectedParkingLot === $lot["LotName"] ? "selected" : ""; // Check if it's the selected lot
                echo "<option value=\"" . htmlspecialchars($lot["LotName"]) . "\" $isSelected>" . htmlspecialchars($lot["LotName"]) . "</option>";
            }
            ?>
        </select>

        <button>Submit</button>
        </form>
    </div>
<button id="previous" class="topSpace" onclick="showPrevious()">Previous</button>
<button id="next" class="topSpace" onclick="showNext()">Next</button>
</div>

<!-- Container for all chart canvases -->
<div class='dashContent'>
<div id="chart-area">
    <?php echo $chartHTML; ?>
</div>
</div>

<script>
    let currentChartIndex = 0;
    const chartContainers = document.querySelectorAll('.chart-container');

    // Function to show the specified chart
    function showChart(index) {
        // Hide all charts
        chartContainers.forEach((container) => {
            container.style.display = 'none';
        });

        // Display the specified chart
        if (index >= 0 && index < chartContainers.length) {
            chartContainers[index].style.display = 'block';
            currentChartIndex = index;
        }
    }

    // Show the first chart initially
    showChart(0);

    // Function to navigate to the previous chart
    function showPrevious() {
        if (currentChartIndex > 0) {
            showChart(currentChartIndex - 1);
        }
    }

    // Function to navigate to the next chart
    function showNext() {
        if (currentChartIndex < chartContainers.length - 1) {
            showChart(currentChartIndex + 1);
        }
    }
</script>
</body>
</html>
