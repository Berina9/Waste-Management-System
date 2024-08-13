<?php
function fetchAndDisplayData() {
    $conn = new mysqli("localhost", "root", "", "waste");

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = $conn->real_escape_string($_POST["id"]);

        if ($_POST["action"] == "update") {
            $area = $conn->real_escape_string($_POST["area"]);
            $locality = $conn->real_escape_string($_POST["locality"]);
            $landmark = $conn->real_escape_string($_POST["landmark"]);
            $Totalcapacity = $conn->real_escape_string($_POST["Totalcapacity"]);

            // Using a prepared statement for security
            $stmt = $conn->prepare("UPDATE tbl_container SET Area=?, Locality=?, Landmark=?, Totalcapacity=? WHERE Id=?");
            $stmt->bind_param("ssssi", $area, $locality, $landmark, $Totalcapacity, $id);

            if ($stmt->execute()) {
                echo "<script>alert('Record updated successfully');</script>";
            } else {
                echo "<script>alert('Error updating record: " . $stmt->error . "');</script>";
            }

            $stmt->close();
        }

        if ($_POST["action"] == "delete") {
            // Using a prepared statement for security
            $stmt = $conn->prepare("DELETE FROM tbl_container WHERE Id=?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                echo "<script>alert('Record deleted successfully');</script>";
            } else {
                echo "<script>alert('Error deleting record: " . $stmt->error . "');</script>";
            }

            $stmt->close();
        }
    }

    echo "<link rel='stylesheet' href='fetchcont.css'>";

    $result = $conn->query("SELECT * FROM tbl_container");
    if ($result->num_rows > 0) {
        echo "<table border='1'>
                <tr>
                    <th>ID</th>
                    <th>Area</th>
                    <th>Locality</th>
                    <th>Landmark</th>
                    <th>Total Capacity</th>
                    <th>Waste Quantity</th>
                    <th>Action</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            $wastequantity = isset($row["Wastequantity"]) ? htmlspecialchars($row["Wastequantity"]) : ''; // Check if 'Wastequantity' exists
            echo "<tr>
                    <form action='' method='post'>
                    <td><input type='hidden' name='id' value='" . htmlspecialchars($row["Id"]) . "'>" . htmlspecialchars($row["Id"]) . "</td>
                    <td><input type='text' name='area' value='" . htmlspecialchars($row["Area"]) . "'></td>
                    <td><input type='text' name='locality' value='" . htmlspecialchars($row["Locality"]) . "'></td>
                    <td><input type='text' name='landmark' value='" . htmlspecialchars($row["Landmark"]) . "'></td>
                    <td><input type='text' name='Totalcapacity' value='" . htmlspecialchars($row["Totalcapacity"]) . "'></td>
                    <td>$wastequantity</td>
                    <td>
                        <button type='submit' name='action' value='update' onclick='return confirm(\"Are you sure you want to update this record?\")'>Update</button>
                        <button type='submit' name='action' value='delete' onclick='return confirm(\"Are you sure you want to delete this record?\")'>Delete</button>
                    </td>
                    </form>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No records found</p>";
    }

    $conn->close();
}

fetchAndDisplayData();
?>
