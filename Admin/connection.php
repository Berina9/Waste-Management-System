<?php

header('Content-Type: application/json');


$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "Waste"; 

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT solid, organic, hazardous FROM waste_data";
$result = $conn->query($sql);

$data = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    echo json_encode([]);
}

echo json_encode($data);

$conn->close();

?>
