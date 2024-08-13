<?php
function handleFormSubmission() {
    $conn = new mysqli("localhost", "root", "", "waste");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $message = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['area'], $_POST['locality'], $_POST['landmark'], $_POST['Totalcapacity'])) {
        $stmt = $conn->prepare("INSERT INTO tbl_container (Area, Locality, Landmark, Totalcapacity) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $_POST['area'], $_POST['locality'], $_POST['landmark'], $_POST['Totalcapacity']);

        if ($stmt->execute()) {
            $message = "Container has been created successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
    return $message;
}

$message = handleFormSubmission();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="form.css">
    <title>Create Container</title>
</head>
<body>
    <div class="form-container">
        <h2>Create Container</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="area">Area:</label>
                <input type="text" id="area" name="area" required>
            </div>
            <div class="form-group">
                <label for="locality">Locality:</label>
                <input type="text" id="locality" name="locality" required>
            </div>
            <div class="form-group">
                <label for="landmark">Landmark:</label>
                <input type="text" id="landmark" name="landmark" required>
            </div>
            <div class="form-group">
                <label for="Total-capacity">Totalcapacity:</label>
                <input type="text" id="Totalcapacity" name="Totalcapacity" required>
                
            </div>
            
            <button type="submit">Submit</button>
        </form>
    </div>

    <script>
        const message = "<?php echo $message; ?>";
        if (message) {
            alert(message);
        }
    </script>
</body>
</html>
