<?php
include('partial/header.php');
include('sidebar.php');
$mysqli = require __DIR__ . "/database.php";

// Determine if the user is an admin
$isAdmin = intval($user["IsAdmin"]) > 0;

// Prepare the appropriate SQL query
if ($isAdmin):
    $query = "SELECT timestart, timeend FROM booking";  // Admins see all bookings


// Prepare the SQL statement
$stmt = $mysqli->prepare($query);

if ($stmt === false) {
    die("Error preparing SQL statement: " . $mysqli->error);  // SQL preparation failed
}

// Bind the userID parameter if the user is not an admin
if (!$isAdmin) {
    if (!$stmt->bind_param("i", $user["userID"])) {
        die("Error binding parameters: " . $stmt->error);  // Parameter binding failed
    }
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

// Generate HTML for each month's chart
$chartHTML = "";
for ($month = 1; $month <= 12; $month++) {
    $occupiedSpaces = getOccupiedSpacesPerDay($times, $currentYear, $month);

    $monthName = date("F", mktime(0, 0, 0, $month, 1));
    $dates = array_keys($occupiedSpaces);
    $counts = array_values($occupiedSpaces);

    // Create a canvas for each chart with a unique ID
    $chartHTML .= "
        <div class='dashContent'>
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
                                max: 50 // max spaces, change if we change parking space amount
                            }
                        }
                    }
                });
            </script>
        </div>
        </div>";
}
else:
    die ("You are not authorized to access this page.");
endif;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<!-- Buttons to navigate between graphs -->
<div class='dashContent'>
<button id="previous" onclick="showPrevious()">Previous</button>
<button id="next" onclick="showNext()">Next</button>
</div>

<!-- Container for all chart canvases -->
<div id="chart-area">
    <?php echo $chartHTML; ?>
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
