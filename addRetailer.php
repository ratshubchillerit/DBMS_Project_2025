<?php
include "db.php";  // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get the data from the form
  $name = $_POST['name'];
  $area = $_POST['area'];
  $city = $_POST['city'];
  $minimumDeliveryDays = $_POST['minimumDeliveryDays'];
  $contact = $_POST['contact'];

  // Insert the data into the retailer table
  $sql = "INSERT INTO retailer (name, area, city, MinimumDeliveryDays, contact) 
            VALUES ('$name', '$area', '$city', '$minimumDeliveryDays', '$contact')";

  if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Retailer added successfully!'); window.location.href='adminDirectory.php';</script>";
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Retailer</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    .form-container {
      max-width: 500px;
      margin: 0 auto;
      padding: 2rem;
      background: #f0f8ff;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .form-container h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }

    .form-container input {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border-radius: 8px;
      border: 1px solid #ddd;
      font-size: 16px;
    }

    .form-container input[type="submit"] {
      background-color: #4CAF50;
      color: white;
      border: none;
      cursor: pointer;
      font-size: 18px;
    }

    .form-container input[type="submit"]:hover {
      background-color: #45a049;
    }

    .form-container label {
      font-size: 16px;
      margin-bottom: 5px;
      display: block;
    }
  </style>
</head>

<body>
  <div class="container">
    <h2>Add Retailer</h2>
    <div class="form-container">
      <form action="addRetailer.php" method="POST">
        <label for="name">Retailer Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="area">Area:</label>
        <input type="text" id="area" name="area" required>

        <label for="city">City:</label>
        <input type="text" id="city" name="city" required>

        <label for="minimumDeliveryDays">Minimum Delivery Days:</label>
        <input type="number" id="minimumDeliveryDays" name="minimumDeliveryDays" required min="1">

        <label for="contact">Contact Number:</label>
        <input type="text" id="contact" name="contact" required>

        <input type="submit" value="Add Retailer">
      </form>
    </div>
  </div>
</body>

</html>

<?php
$conn->close();
?>