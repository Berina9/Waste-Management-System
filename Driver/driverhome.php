<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigate to Namedriverhome</title>
</head>
<body>
<?php
include 'nav_driver.php';
echo "<link rel='stylesheet' href='viewtable.css'>";

function displayData() {
    // Database connection
    $conn = new mysqli("localhost", "root", "", "waste");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetching data from the table
    $result = $conn->query("SELECT * FROM tbl_container");

    // Check if there are records to display
    if ($result->num_rows > 0) {
        echo "<table border='1'>
                <tr>
                    <th>Id</th>
                    <th>Area</th>
                    <th>Locality</th>
                    <th>Landmark</th>
                    <th>Waste Quantity</th>
                    <th>Total Capacity</th>
                </tr>";

        // Loop through each row and display the data
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row["Id"]) . "</td>
                    <td>" . htmlspecialchars($row["Area"]) . "</td>
                    <td>" . htmlspecialchars($row["Locality"]) . "</td>
                    <td>" . htmlspecialchars($row["Landmark"]) . "</td>
                    <td>" . htmlspecialchars($row["wastequantity"]) . "</td>
                    <td>" . htmlspecialchars($row["Totalcapacity"]) . "</td>
                </tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No records found</p>";
    }

    // Close connection
    $conn->close();
}

// Display the table
displayData();
?>

</body>
</html>
