<?php

function fetchAndDisplayData() {

    $conn = new mysqli("localhost", "root", "", "Waste");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $result = $conn->query("SELECT bin_id, location_id, capacity_liters, avg_generation_rate, 
    holding_cost_per_unit, trip_cost, optimal_collection_interval, last_collection_date, last_calculated_date, 
    current_fill_percent FROM bin_data");
    if ($result->num_rows > 0) {
          echo "<link rel='stylesheet' href='fetchdata.css'>";
        echo "<table border='1'>
                <tr>
                    <th>Bin ID</th>
                    <th>Location ID</th>
                    <th>Capacity (Liters)</th>
                    <th>Average Generation Rate</th>
                    <th>Holding Cost per Unit</th>
                    <th>Trip Cost</th>
                    <th>Optimal Collection Interval</th>
                    <th>Last Collection Date</th>
                    <th>Last Calculated Date</th>
                    <th>Current Fill Percentage</th>
                
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row["bin_id"]) . "</td>
                    <td>" . htmlspecialchars($row["location_id"]) . "</td>
                    <td>" . htmlspecialchars($row["capacity_liters"]) . "</td>
                    <td>" . htmlspecialchars($row["avg_generation_rate"]) . "</td>
                    <td>" . htmlspecialchars($row["holding_cost_per_unit"]) . "</td>
                    <td>" . htmlspecialchars($row["trip_cost"]) . "</td>
                    <td>" . htmlspecialchars($row["optimal_collection_interval"]) . "</td>
                    <td>" . htmlspecialchars($row["last_collection_date"]) . "</td>
                    <td>" . htmlspecialchars($row["last_calculated_date"]) . "</td>
                    <td>" . htmlspecialchars($row["current_fill_percent"]) . "</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No records found</p>";
    }

    $conn->close();

    echo '</body>
          </html>';
}

fetchAndDisplayData();
?>
