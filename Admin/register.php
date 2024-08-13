<?php

header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Waste";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'], $_POST['email'], $_POST['password'], $_POST['phone'])) {
    $stmt = $conn->prepare("INSERT INTO tbl_profile (Name, Email, Password, PhoneNo) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $_POST['name'], $_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT), $_POST['phone']);

   
    if ($stmt->execute()) {
        echo json_encode(["success" => "User registered successfully"]);
    } else {
        echo json_encode(["error" => "Error: " . $stmt->error]);
    }

   
    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid input data"]);
}
$conn->close();
exit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Registration</title>
    <link rel="stylesheet" href="form.css">
</head>
<body>
    <div class="form-container">
        <h2>Driver Registration</h2>
        <form action="register.php" method="post">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required><br><br>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Password</label><br>
            <input type="password" id="password" name="password" required><br><br>

            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" required pattern="[0-9]{10}"><br><br>

            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
