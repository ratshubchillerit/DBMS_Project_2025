<?php
include "db.php"; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Collect form data and insert into the farm table
  $farmName = $_POST['farmName'];
  $area = $_POST['area'];
  $city = $_POST['city'];
  $contactNumber = $_POST['contactNumber'];
  $farmSize = $_POST['farmSize'];

  $sql = "INSERT INTO farm (FarmName, Area, City, ContactNumber, FarmSize) 
            VALUES ('$farmName', '$area', '$city', '$contactNumber', '$farmSize')";

  if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Farm added successfully!'); window.location.href = 'directory.php';</script>";
  } else {
    echo "<script>alert('Error: " . $conn->error . "');</script>";
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Farm</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f4f6f9;
      margin: 0;
      color: #333;
    }

    .container {
      max-width: 800px;
      margin: auto;
      padding: 2rem;
      background-color: #fff;
      box-shadow: 0px 8px 24px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
      margin-top: 50px;
    }

    h2 {
      text-align: center;
      margin-bottom: 2rem;
      color: #007bff;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    label {
      display: block;
      font-size: 1rem;
      font-weight: 600;
      color: #333;
      margin-bottom: 0.5rem;
    }

    input,
    select {
      width: 100%;
      padding: 12px;
      font-size: 1rem;
      border-radius: 8px;
      border: 1px solid #ccc;
      outline: none;
      transition: border-color 0.3s ease;
    }

    input:focus,
    select:focus {
      border-color: #007bff;
    }

    button {
      padding: 12px 25px;
      font-size: 1rem;
      background-color: #28a745;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      width: 100%;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #218838;
    }

    .back-link {
      text-align: center;
      margin-top: 1.5rem;
    }

    .back-link a {
      text-decoration: none;
      color: #007bff;
      font-size: 1rem;
    }
  </style>
</head>

<body>
  <div class="container">
    <h2>Add New Farm</h2>

    <form method="POST" action="addFarm.php">
      <div class="form-group">
        <label for="farmName">Farm Name</label>
        <input type="text" id="farmName" name="farmName" required placeholder="Enter farm name">
      </div>

      <div class="form-group">
        <label for="area">Farm Area</label>
        <input type="text" id="area" name="area" required placeholder="Enter farm area">
      </div>

      <div class="form-group">
        <label for="city">Farm City</label>
        <input type="text" id="city" name="city" required placeholder="Enter farm city">
      </div>

      <div class="form-group">
        <label for="contactNumber">Contact Number</label>
        <input type="text" id="contactNumber" name="contactNumber" required placeholder="Enter contact number">
      </div>

      <div class="form-group">
        <label for="farmSize">Farm Size (in acres)</label>
        <input type="number" id="farmSize" name="farmSize" required step="0.1" placeholder="Enter farm size">
      </div>

      <button type="submit">Add Farm</button>
    </form>

    <div class="back-link">
      <a href="adminDirectory.php">Back to Directory</a>
    </div>
  </div>
</body>

</html>