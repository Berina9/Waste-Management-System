<?php
include '../User/connection.php'; // Include your existing connection

try {
    // Fetch coordinates from tbl_admin
    $adminStmt = $conn->query("SELECT location FROM tbl_admin");
    $adminCoordinates = [];
    while ($row = $adminStmt->fetch_assoc()) {
        $adminCoordinates[] = $row['location'];
    }

    // Fetch coordinates from tbl_driver
    $driverStmt = $conn->query("SELECT location FROM tbl_driver where status='0'");
    $driverCoordinates = [];
    while ($row = $driverStmt->fetch_assoc()) {
        $driverCoordinates[] = $row['location'];
    }

    $drivercomStmt = $conn->query("SELECT location FROM tbl_driver where status='1'");
    $drivercomCoordinates = [];
    while ($row = $drivercomStmt->fetch_assoc()) {
        $drivercomCoordinates[] = $row['location'];
    }

    // Fetch coordinates from tbl_container
    $containerStmt = $conn->query("SELECT location, Totalcapacity FROM tbl_container");
    $containerCoordinates = [];
    while ($row = $containerStmt->fetch_assoc()) {
        $containerCoordinates[] = [
            'location' => $row['location'],
            'capacity' => (int)$row['Totalcapacity']
        ];
    }

    // Execute the query to select Driverid from tbl_container_archive
$diveStmt = $conn->query("SELECT Driverid FROM tbl_container_archive");

$driverid = [];

// Fetch the result and store it in the $driverid array
while ($row = $diveStmt->fetch_assoc()) {
    $driverid[] = $row['Driverid'];
}


    // Prepare coordinates in an array
    $coordinates = [
        'adminCoordinates' => $adminCoordinates,
        'driverCoordinates' => $driverCoordinates,
        'containerCoordinates' => $containerCoordinates,
        'driverid' => $driverid,
        'drivercomCoordinates'=>$drivercomCoordinates,
    ];

    // Return the coordinates as JSON
    echo json_encode($coordinates, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error fetching data: ' . $e->getMessage()]);
}
?>
