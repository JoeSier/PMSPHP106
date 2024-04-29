<?php
include('partial/header.php');
$mysqli = require __DIR__ . "/database.php";

// Fetch all bookings from the database
$ssel = "SELECT timestart, timeend FROM booking";
$times = $mysqli->query($ssel);

// Check if the query was successful
if ($times === false) {
    die("Database query failed: " . $mysqli->error);
}

// Collect all bookings in an array
$bookings = [];
while ($row = $times->fetch_assoc()) {
    $bookings[] = $row;
}

// Function to get occupied spaces per day for a given month
function getOccupiedSpacesPerDay($bookings, $year, $month) {
    $occupiedSpaces = [];

    // Get the number of days in the specified month
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    // Initialize occupied spaces for each day in the month
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = sprintf("%04d-%02d-%02d", $year, $month, $day);
        $occupiedSpaces[$date] = 0;
    }

    // Update occupied spaces based on bookings
    foreach ($bookings as $booking) {
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
    $occupiedSpaces = getOccupiedSpacesPerDay($bookings, $currentYear, $month);

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
                                max: 50 // max spaces, change if we change parking space amount
                            }
                        }
                    }
                });
            </script>
        </div>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<!-- Buttons to navigate between graphs -->
<button id="previous" onclick="showPrevious()">Previous</button>
<button id="next" onclick="showNext()">Next</button>

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
