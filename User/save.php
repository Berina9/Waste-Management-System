
<?php

session_start();
include "connection.php";
// Debugging statement to check if the script starts correctly
echo '<script>console.log("hello 2");</script>';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo '<script>console.log("hello");</script>';

    // Retrieve POST data
    $username = trim($_POST['user']);
    $email = trim($_POST['email']);
    $password = $_POST['pass'];
    $cpassword = $_POST['cpass'];
    $role = $_POST['role'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        echo '<script>alert("Invalid email format!"); </script>';
        exit();
    }   

    if (strlen($password) < 8) {
        echo '<script>alert("Password must be at least 8 characters long!"); </script>';
        exit();
    }

    if ($role == 'driver') {
        $stmt = $conn->prepare("SELECT * FROM tbl_driver WHERE username = ? OR email = ?");
    } elseif ($role == 'admin') {
        $stmt = $conn->prepare("SELECT * FROM tbl_admin WHERE username = ? OR email = ?");
    } else {
        die("Invalid role specified.");
    }

    if ($stmt) {
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<script>alert("Username or email already exists!"); </script>';
            $stmt->close();
            $conn->close();
            exit();
        }
        $stmt->close();
    } else {
        echo '<script>alert("Error preparing statement!"); </script>';
        $conn->close();
        exit();
    }

    // Check if passwords match
    if ($password !== $cpassword) {
        echo '<script>alert("Passwords do not match!"); </script>';
        $conn->close();
        exit();
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);

    
    if ($role == 'driver') {
        $stmt = $conn->prepare("INSERT INTO tbl_driver (username, email, password) VALUES (?, ?, ?)");
    } elseif ($role == 'admin') {
        $stmt = $conn->prepare("INSERT INTO tbl_admin (username, email, password) VALUES (?, ?, ?)");
    } else {
        die("Invalid role specified.");
    }

    if ($stmt) {
        $stmt->bind_param("sss", $username, $email, $hash);
        if ($stmt->execute()) {
            echo '<script>alert("Signup successful!"); </script>';
            // Redirect to signup page with a success message
            header("Location: ../landingp/index.php" );
        } else {
            error_log("Error in signup: " . $stmt->error);
            echo '<script>alert("Error in signup!"); </script>';
        }
        $stmt->close();
    } else {
        echo '<script>alert("Error preparing statement!"); </script>';
    }

    $conn->close();
}
?>
