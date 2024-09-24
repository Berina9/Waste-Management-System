<?php
session_start();

// Handle login form submission
if (isset($_POST['submit'])) {
    // Get input data
    $usernameOrEmail = $_POST['usernameOrEmail'];
    $password = $_POST['password'];

    // Connect to the database (replace with your database connection details)
    $conn = mysqli_connect("localhost", "root", "", "waste");

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Escape input data to prevent SQL injection
    $usernameOrEmail = mysqli_real_escape_string($conn, $usernameOrEmail);

    // Prepare SQL statement to fetch user data
    $sql = "SELECT * FROM tbl_person WHERE username='$usernameOrEmail' OR email='$usernameOrEmail'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // User found, verify password
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password_hash'])) {
            // Store user ID and role in session
            $_SESSION['userId'] = $row['Id'];
            $_SESSION['role'] = $row['role'];
            
            // Redirect based on role
            if ($row["role"] == "driver") {
                $_SESSION['Driverid'] = $row['Id']; // Storing Driverid in session
                header("Location: ../Driver/driverhome.php");
                exit();
            } elseif ($row["role"] == "admin") {
                header("Location: ../admin/index.php");
                exit();
            } else {
                $error_message = "No role found.";
            }
        } else {
            $error_message = "Incorrect password.";
        }
    } else {
        $error_message = "User not found.";
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../User/style.css">
</head>
<body>
    <?php include "navbar.php"; ?>
    
    <div id="form">
        <h1>Login</h1>
        <?php if(isset($error_message)) { echo "<p>$error_message</p>"; } ?>
        <form name="form" action="" method="post">
            <label>Enter Username/Email</label>
            <input type="text" id="usernameOrEmail" name="usernameOrEmail" required><br><br>
            <label>Enter Password</label>
            <input type="password" id="password" name="password" required><br><br>
            <input type="submit" id="btn" value="Login" name="submit"/><br>
        </form>
    </div>
</body>
</html>
