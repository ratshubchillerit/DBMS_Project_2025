<?php
include "db.php";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get form data
  $type = $_POST['type'];
  $color = $_POST['color'];
  $weight = $_POST['weight'];
  $birthdate = $_POST['birthdate'];
  $age = $_POST['age'];
  $farmID = $_POST['farmID'];
  $vaccinationStatus = $_POST['vaccinationStatus'];

  // Insert data into livestock table
  $sql = "INSERT INTO livestock (Type, Color, Weight, Birthdate, Age, FarmID, VaccinationStatus)
            VALUES ('$type', '$color', '$weight', '$birthdate', '$age', '$farmID', '$vaccinationStatus')";

  if ($conn->query($sql) === TRUE) {
    // Redirect to admin.php after successful insertion
    echo "<script>alert('New livestock added successfully!'); window.location.href = 'admin.php';</script>";
  } else {
    echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
  }


  // Close connection
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Livestock</title>
  <style>
    /* General Body and Page Styling */
    body {
      font-family: 'Roboto', sans-serif;
      background: #f0f4f8;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 800px;
      margin: 50px auto;
      padding: 20px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    /* Form Wrapper Styling */
    .form-wrapper {
      padding: 40px;
      border-radius: 12px;
      background-color: #fff;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    h2 {
      text-align: center;
      color: #333;
      font-size: 28px;
      margin-bottom: 20px;
    }

    /* Form Elements */
    .form-group {
      margin-bottom: 20px;
    }

    label {
      display: block;
      font-size: 16px;
      color: #333;
      margin-bottom: 8px;
    }

    input,
    select {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      border: 2px solid #ccc;
      border-radius: 8px;
      background-color: #f9f9f9;
      transition: border-color 0.3s;
    }

    input:focus,
    select:focus {
      border-color: #007bff;
      outline: none;
    }

    button {
      padding: 12px 20px;
      font-size: 18px;
      color: white;
      background-color: #007bff;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      width: 100%;
      transition: background-color 0.3s;
    }

    button:hover {
      background-color: #0056b3;
    }

    /* Responsive Styling */
    @media (max-width: 768px) {
      .container {
        margin: 20px;
      }

      .form-wrapper {
        padding: 30px;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <h2>Add Livestock</h2>
    <form method="POST" action="adminAddLiveStock.php">
      <label for="type">Type:</label>
      <input type="text" id="type" name="type" required>

      <label for="color">Color:</label>
      <input type="text" id="color" name="color" required>

      <label for="weight">Weight (kg):</label>
      <input type="number" id="weight" name="weight" required>

      <label for="birthdate">Birthdate:</label>
      <input type="date" id="birthdate" name="birthdate" required>

      <label for="age">Age (years):</label>
      <input type="number" id="age" name="age" required>

      <label for="farmID">Farm ID:</label>
      <input type="number" id="farmID" name="farmID" required>

      <label for="vaccinationStatus">Vaccination Status:</label>
      <select name="vaccinationStatus" id="vaccinationStatus" required>
        <option value="Vaccinated">Vaccinated</option>
        <option value="Not Vaccinated">Not Vaccinated</option>
      </select>

      <button type="submit" class="btn btn-primary">Add Livestock</button>
    </form>
  </div>
</body>

</html>