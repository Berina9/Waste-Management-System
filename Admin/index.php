
    <title>Waste Management Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="side.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<body style="background-color:white">
<header>
    <?php include 'header.php'; ?>
   
</header>

<div class="index">

<?php include 'side.php'; ?>

<div class="container">
<div style="text-align: center;">
    <h1>Waste Management Dashboard</h1>
</div>

    <main>
        <div class="dashboard">
            <div class="card">
                <h2>Solid Waste</h2>
                <canvas id="solidWasteChart"></canvas>
            </div>
            <div class="card">
                <h2>Organic Waste</h2>
                <canvas id="organicWasteChart"></canvas>
            </div>
            <div class="card">
                <h2>Hazardous Waste</h2>
                <canvas id="hazardousWasteChart"></canvas>
                <style>
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    th, td {
                        padding: 8px;
                        text-align: left;
                        border-bottom: 1px solid #ddd;
                    }
                    th {
                        background-color: #f2f2f2;
                    }
                    tr:hover {
                        background-color: #f5f5f5;
                    }
                </style>
            </div>
            <div style="text-align: center;">
   
</div>
            
        </div>
        <h1>Done Containers</h1>
        <?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "waste";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT Id, Area, Locality, Landmark, wastequantity, Totalcapacity FROM tbl_container_archive";
$result = $conn->query($sql);

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
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["Id"]. "</td>
                <td>" . $row["Area"]. "</td>
                <td>" . $row["Locality"]. "</td>
                <td>" . $row["Landmark"]. "</td>
                <td>" . $row["wastequantity"]. "</td>
                <td>" . $row["Totalcapacity"]. "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}
$conn->close();
?>

    </main>
</div>
</div>
<script src="script.js"></script>

