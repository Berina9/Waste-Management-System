<?php

function fetchAndDisplayData() {

    $conn = new mysqli("localhost", "root", "", "Waste");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $result = $conn->query("SELECT Id, Name, Email, Password, PhoneNo FROM tbl_profile");
    if ($result->num_rows > 0) {
        echo "<table border='1'>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row["Id"]) . "</td>
                    <td>" . htmlspecialchars($row["Name"]) . "</td>
                    <td>" . htmlspecialchars($row["Email"]) . "</td>
                    <td>" . htmlspecialchars($row["PhoneNo"]) . "</td>
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
