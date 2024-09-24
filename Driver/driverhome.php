<?php
session_start(); // Start the session

include 'nav_driver.php';

// Database connection
$conn = new mysqli("localhost", "root", "", "waste");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $id = intval($_POST["id"]);

    // Fetch Driverid from session
    if (isset($_SESSION['Driverid'])) {
        $driverId = $_SESSION['Driverid']; // Store the session Driverid in a variable
    } else {
        die("Driver ID not found in session.");
    }

    // Insert data into 'tbl_container_archive' from 'tbl_container' with specified columns and Driverid from session
    $insertQuery = "INSERT INTO tbl_container_archive (Id, Area, Locality, Landmark, wastequantity, Totalcapacity, Driverid)
                    SELECT Id, Area, Locality, Landmark, wastequantity, Totalcapacity, ?
                    FROM tbl_container WHERE Id = ?";
    
    // Prepare statement to avoid SQL injection
    if ($stmt = $conn->prepare($insertQuery)) {
        $stmt->bind_param("ii", $driverId, $id); // Bind Driverid and container Id
        $stmt->execute();
        $stmt->close();
    }

    // Remove data from the existing table
    $deleteQuery = "DELETE FROM tbl_container WHERE Id = ?";
    if ($stmt = $conn->prepare($deleteQuery)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

function displayData($conn) {
    // Fetching data from the table
    $result = $conn->query("SELECT * FROM tbl_container");

    // Check if there are records to display
    if ($result->num_rows > 0) {
        echo "<table class='table table-bordered'>
                <thead class='thead-dark'>
                    <tr>
                        <th>Id</th>
                        <th>Area</th>
                        <th>Locality</th>
                        <th>Landmark</th>
                        <th>Waste Quantity</th>
                        <th>Total Capacity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>";

        // Loop through each row and display the data
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row["Id"]) . "</td>
                    <td>" . htmlspecialchars($row["Area"]) . "</td>
                    <td>" . htmlspecialchars($row["Locality"]) . "</td>
                    <td>" . htmlspecialchars($row["Landmark"]) . "</td>
                    <td>" . htmlspecialchars($row["wastequantity"]) . "</td>
                    <td>" . htmlspecialchars($row["Totalcapacity"]) . "</td>
                    
                    <td><button class='btn btn-warning' onclick='confirmAction(" . htmlspecialchars($row["Id"]) . ")'>Confirm</button></td>
                </tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "<p>No records found</p>";
    }
}

// Display the table
displayData($conn);

// Fetching data from the archive table
$resultArchive = $conn->query("SELECT * FROM tbl_container_archive");

// Check if there are records to display
if ($resultArchive->num_rows > 0) {
    echo "<h2>Done Containers</h2>";
    echo "<table class='table table-bordered'>
            <thead class='thead-dark'>
                <tr>
                    <th>Id</th>
                    <th>Area</th>
                    <th>Locality</th>
                    <th>Landmark</th>
                    <th>Waste Quantity</th>
                    <th>Total Capacity</th>
                    <th>Driverid</th>
                </tr>
            </thead>
            <tbody>";

    // Loop through each row and display the data
    while ($row = $resultArchive->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row["Id"]) . "</td>
                <td>" . htmlspecialchars($row["Area"]) . "</td>
                <td>" . htmlspecialchars($row["Locality"]) . "</td>
                <td>" . htmlspecialchars($row["Landmark"]) . "</td>
                <td>" . htmlspecialchars($row["wastequantity"]) . "</td>
                <td>" . htmlspecialchars($row["Totalcapacity"]) . "</td>
                <td>" . htmlspecialchars($row["Driverid"]) . "</td>
            </tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<p>No archived records found</p>";
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Container Management</title>
    <!-- Link to custom CSS file -->
    <link rel="stylesheet" href="viewtable.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function confirmAction(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to confirm?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, confirm it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a form and submit it to move the data
                    var form = document.createElement("form");
                    form.method = "POST";
                    form.action = "";

                    var input = document.createElement("input");
                    input.type = "hidden";
                    input.name = "id";
                    input.value = id;

                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                }
            })
        }
    </script>
</head>
<body>
    <!-- The PHP code will dynamically generate content here -->
</body>
</html>
