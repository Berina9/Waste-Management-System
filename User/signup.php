<?php
session_start();

// Handle signup form submission
if (isset($_POST['submit'])) {
    // Get input data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Connect to database (replace with your database connection details)
    $conn = mysqli_connect("localhost", "root", "", "waste");

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Escape input data to prevent SQL injection
    $username = mysqli_real_escape_string($conn, $username);
    $email = mysqli_real_escape_string($conn, $email);

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check if the username or email already exists
        $sql = "SELECT * FROM tbl_person WHERE username='$username' OR email='$email'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $error_message = "Username or email already exists.";
        } else {
            // Hash the password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert user data into the database with default role as 'user'
            $sql = "INSERT INTO tbl_person (username, email, password_hash, role, timestamp) VALUES ('$username', '$email', '$password_hash', 'driver', NOW())";

            if (mysqli_query($conn, $sql)) {
                echo "<script>
                        alert('Signup successful!\\nUsername: $username\\nEmail: $email');
                        window.location.href = 'login.php';
                      </script>";
                exit();
            } else {
                $error_message = "Error: " . mysqli_error($conn);
            }
        }
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="../User/style.css">
</head>
<body>
    <?php
    include "navbar.php";
    ?>
    <div id="form">
        <h1>Signup</h1>
        <?php if(isset($error_message)) { echo "<p>$error_message</p>"; } ?>
        <?php if(isset($success_message)) { echo "<p>$success_message</p>"; } ?>
        <form name="form" action="" method="post">
            <label>Enter Username</label>
            <input type="text" id="username" name="username" required><br>
            <label>Enter Email</label>
            <input type="email" id="email" name="email" required><br>
            <label>Enter Password</label>
            <input type="password" id="password" name="password" required><br>
            <label>Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required><br><br>
            <input type="submit" id="btn" value="Signup" name="submit"/><br>
        </form>
       
    </div>
</body>
</html>
