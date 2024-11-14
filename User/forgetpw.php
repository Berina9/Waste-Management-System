<?php
session_start();
include('connection.php'); 

// If the form is submitted, handle the password reset
if (isset($_POST['submit'])) {
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($new_password != $confirm_password) {
        $error_message = "Passwords do not match!";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Check if user ID (id from tbl_person) is present
        if (isset($_POST['user_id'])) {
            $user_id = $_POST['user_id']; // Fetch value from form
        } else {
            $error_message = "Form data is missing. Please try again.";
        }   

        // Update the password in the database
        $update_query = "UPDATE tbl_person SET password_hash = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $hashed_password, $user_id);

        if ($stmt->execute()) {
            $success_message = "Password has been successfully updated!";
        } else {
            $error_message = "Failed to update password. Please try again.";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        #form {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            max-width: 400px;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        p {
            color: red;
        }
    </style>
</head>
<body>

<div id="form">
    <h1>Reset Password</h1>

    <?php if(isset($error_message)) { echo "<p>$error_message</p>"; } ?>
    <?php if(isset($success_message)) { echo "<p style='color:green;'>$success_message</p>"; } ?>

    <form action="" method="post">
        <label>New Password</label>
        <input type="password" name="password" required><br>
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required><br><br>
        <input type="submit" value="Reset Password" name="submit">
        
    </form>
</div>

</body>
</html>
