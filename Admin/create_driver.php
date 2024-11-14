< <?php

function handleFormSubmission() {
    $conn = new mysqli("localhost", "root", "", "waste");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset( $_POST['name'], $_POST['email'], $_POST['password'], $_POST['area'], $_POST['phoneno'])) {
        $stmt = $conn->prepare("INSERT INTO tbl_driver ( usernamer, Email, Password, address) VALUES (?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("isssss", $_POST['name'], $_POST['email'], $_POST['password'], $_POST['area'], $_POST['phoneno']);
        if ($stmt->execute()) {
          
            exit();
        } else {
            "<p>Error: " . $stmt->error . "</p>";
        }
        header("Location: fetchdriver.php");
        $stmt->close();
    }

    $conn->close();
}

handleFormSubmission();
 ?>

 <link rel="stylesheet" href="form.css">

 <div class="form-container">
       <h2>Driver Registration</h2>
       <form action="register.php" method="post">
        <label for="name">Name</label>
           <input type="text" id="name" name="name" required><br><br>

           <label for="email">Email</label>
           <input type="email" id="email" name="email" required><br><br>

           <label for="password">Password</label><br>
            <input type="password" id="password" name="password" required><br><br>

           <label for="area">Area</label>
             <input type="text" id="area" name="area" required><br><br>

           <label for="phone">Phone Number</label>
             <input type="tel" id="phone" name="phone" required pattern="[0-9]{10}"><br><br>

            <button type="submit">Register</button>   
             </form>
    </div> 