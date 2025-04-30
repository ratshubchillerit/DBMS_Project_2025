<?php
include "db.php";

if (isset($_POST['submit'])) {

  $type = $_POST['type'];
  $color = $_POST['color'];
  $weight = $_POST['weight'];
  $birthdate = $_POST['birthdate'];
  $age = $_POST['age'];
  $farm_id = $_POST['farm_id'];
  $vaccination_status = $_POST['vaccination_status'];

  $sql = "INSERT INTO livestock (Type, Color, Weight, Birthdate, Age, FarmID, `VaccinationStatus`)
          VALUES ('$type', '$color', '$weight', '$birthdate', '$age', '$farm_id', '$vaccination_status')";

  $result = $conn->query($sql);

  if ($result == TRUE) {
    echo '<div class="alert alert-success" role="alert">Livestock added successfully!</div>';
    echo "<script>console.log('Livestock added successfully!');</script>";
    header("refresh:2; url=farmManager.php");
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }

  $conn->close();
}

$farm_id = $_GET['farm_id'];
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Add Livestock</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }

    body {
      background: #f0f2f5;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .form-container {
      background: #fff;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      max-width: 600px;
      width: 100%;
    }

    .form-container h2 {
      margin-bottom: 20px;
      color: #333;
      text-align: center;
    }

    .form-group {
      margin-bottom: 15px;
    }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 6px;
      color: #444;
    }

    input,
    select {
      width: 100%;
      padding: 10px 12px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 1rem;
      transition: border-color 0.3s;
    }

    input:focus,
    select:focus {
      border-color: #4caf50;
      outline: none;
    }

    button {
      width: 100%;
      padding: 12px;
      font-size: 1.1rem;
      background-color: #4caf50;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    button:hover {
      background-color: #43a047;
    }

    .error {
      color: red;
      font-size: 0.9rem;
      margin-top: 5px;
    }
  </style>
</head>

<body>

  <div class="form-container">
    <h2>Add New Livestock</h2>
    <form id="livestockForm" action="addLivestock.php" method="POST">
      <div class="form-group">
        <label for="type">Type</label>
        <select name="type" id="type" required>
          <option value="">Select Type</option>
          <option value="Cattle">Cattle</option>
          <option value="Sheep">Sheep</option>
          <option value="Goat">Goat</option>
          <option value="Poultry">Poultry</option>
        </select>
      </div>

      <div class="form-group">
        <label for="color">Color</label>
        <input type="text" name="color" id="color" required>
      </div>

      <div class="form-group">
        <label for="weight">Weight (kg)</label>
        <input type="number" name="weight" id="weight" min="1" required>
      </div>

      <div class="form-group">
        <label for="birthdate">Birthdate</label>
        <input type="date" name="birthdate" id="birthdate" required>
      </div>

      <div class="form-group">
        <label for="age">Age</label>
        <input type="number" name="age" id="age" min="0" required>
      </div>

      <div class="form-group">
        <label for="farm_id">Farm ID</label>
        <input type="number" name="farm_id" id="farm_id" value=<?php echo $farm_id ?> readonly>
      </div>

      <div class="form-group">
        <label for="vaccination_status">Vaccination Status</label>
        <select name="vaccination_status" id="vaccination_status" required>
          <option value="">Select Status</option>
          <option value="Vaccinated">Vaccinated</option>
          <option value="Not Vaccinated">Not Vaccinated</option>
        </select>
      </div>

      <button type="submit" name="submit" class="btn btn-success btn-block">Add Livestock</button>

    </form>
  </div>

  <script>
    const form = document.getElementById("livestockForm");
    form.addEventListener("submit", function(e) {
      const weight = document.getElementById("weight").value;
      const age = document.getElementById("age").value;

      if (weight <= 0 || age < 0) {
        e.preventDefault();
        alert("Please enter valid weight and age values.");
      }
    });
  </script>

</body>

</html>