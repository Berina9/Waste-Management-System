<?php

function handleFormSubmission() {
    $conn = new mysqli("localhost", "root", "", "waste");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset(
        $_POST['bin_id'], $_POST['location_id'], $_POST['capacity_liters'], 
        $_POST['avg_generation_rate'], $_POST['holding_cost_per_unit'], 
        $_POST['trip_cost'], $_POST['optimal_collection_interval'],
        $_POST['last_collection_date'], $_POST['last_calculated_date'], 
        $_POST['current_fill_percent']
    )) {

       $stmt = $conn->prepare(
    "INSERT INTO bin_data (
        location_id, capacity_liters, avg_generation_rate, 
        holding_cost_per_unit, trip_cost, optimal_collection_interval, 
        last_collection_date, last_calculated_date, current_fill_percent
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "sddddddss",
    $_POST['location_id'],            
    $_POST['capacity_liters'],        
    $_POST['avg_generation_rate'],    
    $_POST['holding_cost_per_unit'],  
    $_POST['trip_cost'],              
    $_POST['optimal_collection_interval'], 
    $_POST['last_collection_date'],   
    $_POST['last_calculated_date'],   
    $_POST['current_fill_percent']    
);

        if ($stmt->execute()) {
            header("Location: fetchdata.php"); // redirect on success
            exit();
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }

    $conn->close();
}

handleFormSubmission();
?>


 <link rel="stylesheet" href="form.css">

 <div class="form-container">
       <h2>Bin Assumption</h2>
       <form action="bin_data.php" method="post">
        <label for="bin_id">Bin ID</label>
           <input type="text" id="bin_id" name="bin_id" required><br><br>

           <label for="location_id">Location ID</label>
           <input type="text" id="location_id" name="location_id" required><br><br>

           <label for="capacity_liters">Capacity (Liters)</label>
           <input type="text" id="capacity_liters" name="capacity_liters" required><br><br>

           <label for="avg_generation_rate">Average Generation Rate</label>
           <input type="text" id="avg_generation_rate" name="avg_generation_rate" required><br><br>

           <label for="holding_cost_per_unit">Holding Cost per Unit</label>
           <input type="text" id="holding_cost_per_unit" name="holding_cost_per_unit" required><br><br>

           <label for="trip_cost">Trip Cost</label>
           <input type="text" id="trip_cost" name="trip_cost" required><br><br>

           <label for="optimal_collection_interval">Optimal Collection Interval</label>
           <input type="text" id="optimal_collection_interval" name="optimal_collection_interval" required><br><br>

           <label for="last_collection_date">Last Collection Date</label>
           <input type="date" id="last_collection_date" name="last_collection_date" required><br><br>

           <label for="last_calculated_date">Last Calculated Date</label>
           <input type="date" id="last_calculated_date" name="last_calculated_date" required><br><br>

           <label for="current_fill_percent">Current Fill Percentage</label>
           <input type="text" id="current_fill_percent" name="current_fill_percent" required><br><br>

        
            <button type="submit">Register</button>   
             </form>
    </div> 