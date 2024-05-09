<?php
include('partial/header.php');
include('sidebar.php');
$mysqli = require __DIR__ . "/database.php";
$loadChart = false;

$parkingLots = [];
$selectedParkingLot = null;
$totalSpaces = 0;
$times = [];
$selectedDate = null;

// Fetch available parking lots
$stmt = $mysqli->prepare("SELECT LotName FROM parkingLots");
$stmt->execute();
$result = $stmt->get_result();
$parkingLots = $result->fetch_all(MYSQLI_ASSOC);

// Check if the user is authorized
$isAdmin = intval($user["IsAdmin"]) > 0;

if (!$isAdmin) {
    die("You are not authorized to access this page");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Handle parking lot selection
    if (isset($_POST['parkingLot'])) {
        $selectedParkingLot = $_POST['parkingLot'];

        if ($selectedParkingLot) {
            // Fetch total spaces for the selected parking lot
            $stmt = $mysqli->prepare("SELECT TotalSpaces FROM parkingLots WHERE LotName = ?");
            $stmt->bind_param("s", $selectedParkingLot);
            $stmt->execute();
            $result = $stmt->get_result();
            $totalSpaces = intval($result->fetch_assoc()["TotalSpaces"]);

            // Fetch booking data for the selected parking lot
            $stmt = $mysqli->prepare("SELECT timestart, timeend FROM booking WHERE LotName = ?");
            $stmt->bind_param("s", $selectedParkingLot);
            if (!$stmt->execute()) {
                die("Error executing SQL statement: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $times = $result->fetch_all(MYSQLI_ASSOC);
            $loadChart = true;
        }
    }

    // Handle date selection for occupied spaces
    if (isset($_POST['selectedDate'])) {
        $selectedDate = $_POST['selectedDate'];
        if ($selectedDate && $selectedParkingLot) {
            $occupiedSpacesOnSelectedDate = 0;
            $selectedDateStart = strtotime($selectedDate); // Start of selected day
            $selectedDateEnd = strtotime("+1 day", $selectedDateStart); // End of selected day

            foreach ($times as $booking) {
                $bookingStart = strtotime($booking['timestart']);
                $bookingEnd = strtotime($booking['timeend']);

                // Check if the selected date is within the booking period
                if ($bookingStart < $selectedDateEnd && $selectedDateStart < $bookingEnd) {
                    $occupiedSpacesOnSelectedDate++;
                }
            }
        }
    }
}

$currentYear = date("Y");

// ccupied spaces per day for a given month
function getOccupiedSpacesPerDay($times, $year, $month) {
    // Initialize an array with all dates in the month set to zero bookings
    $occupiedSpaces = [];
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = sprintf("%04d-%02d-%02d", $year, $month, $day);
        $occupiedSpaces[$date] = 0;
    }

    // Loop over each booking to check if it falls within any of the days in the month
    foreach ($times as $booking) {
        $bookingStart = strtotime($booking['timestart']);
        $bookingEnd = strtotime($booking['timeend']);

        foreach (array_keys($occupiedSpaces) as $date) {
            $dayStart = strtotime($date);  // Beginning of the day
            $dayEnd = $dayStart + 86400;   // End of the day (midnight of the next day)

            // Check if the booking overlaps with the current day
            if ($bookingStart < $dayEnd && $dayStart < $bookingEnd) {
                $occupiedSpaces[$date] += 1;  // Increment the count for that day
            }
        }
    }

    return $occupiedSpaces;
}



$chartHTML = "";

if ($selectedParkingLot) {
    for ($month = 1; $month <= 12; $month++) {
        $occupiedSpaces = getOccupiedSpacesPerDay($times, $currentYear, $month);

        $monthName = date("F", mktime(0, 0, 0, $month, 1));
        $dates = array_keys($occupiedSpaces);
        $counts = array_values($occupiedSpaces);

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
                                    max: $totalSpaces
                                }
                            }
                        }
                    });
                </script>
            </div>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container { width: 100%; }
    </style>
</head>
<body>
<div class="dashContent">
    <form method="post" id="bookingForm">
        <select name="parkingLot" id="parkingLot">
            <option value="">Select a Parking Lot</option>
            <?php
            foreach ($parkingLots as $lot) {
                $isSelected = $selectedParkingLot === $lot["LotName"] ? "selected" : "";
                echo "<option value=\"" . htmlspecialchars($lot["LotName"]) . "\" $isSelected>" . htmlspecialchars($lot["LotName"]) . "</option>";
            }
            ?>
        </select>
        <button type="submit">Submit</button>
    </form>
</div>

<div class="dashContent">
    <button id="previous" class="topSpace" onclick="showPrevious()">Previous</button>
    <button id="next" class="topSpace" onclick="showNext()">Next</button>
</div>

<div class="dashContent">
    <div id="chart-area">
        <?php echo $chartHTML; ?>
    </div>
</div>

<div class="dashContent">
    <?php
    if ($selectedParkingLot) {
        echo "<p>Total Spaces: $totalSpaces</p>";

        // Display date selection form
        echo "<form method='post' id='dateForm'>";
        echo "<input type='hidden' name='parkingLot' value='" . htmlspecialchars($selectedParkingLot) . "'>";
        echo "<label for='selectedDate'>Select a Date:</label>";
        echo "<input type='date' name='selectedDate' id='selectedDate' value='" . htmlspecialchars($selectedDate) . "'>";
        echo "<button type='submit'>Submit</button>";
        echo "</form>";

        // Display occupied spaces for the selected date
        if ($selectedDate) {
            echo "<p>Occupied Spaces on " . htmlspecialchars($selectedDate) . ": $occupiedSpacesOnSelectedDate</p>";
        }
    } else {
        echo "<p>Select a parking lot to view details.</p>";
    }
    ?>
</div>

<script>
    let currentChartIndex = 0;
    const chartContainers = document.querySelectorAll('.chart-container');

    function showChart(index) {
        chartContainers.forEach(container => {
            container.style.display = 'none';
        });

        if (index >= 0 && index < chartContainers.length) {
            chartContainers[index].style.display = 'block';
            currentChartIndex = index;
        }
    }

    showChart(0);

    function showPrevious() {
        if (currentChartIndex > 0) {
            showChart(currentChartIndex - 1);
        }
    }

    function showNext() {
        if (currentChartIndex < chartContainers.length - 1) {
            showChart(currentChartIndex + 1);
        }
    }
</script>
</body>
</html>
