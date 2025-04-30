<?php
include 'db.php';

if (isset($_GET['id'])) {
  $retailerId = $_GET['id'];
  $query = "SELECT * FROM retailer WHERE RetailerID = '$retailerId'";
  $result = $conn->query($query);

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
  } else {
    die("No retailer found with that ID.");
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get updated values from the form.
  $name = $_POST['name'];
  $area = $_POST['area'];
  $city = $_POST['city'];
  $contact = $_POST['contact'];

  $updateQuery = "UPDATE retailer SET name='$name', area='$area', city='$city', contact='$contact' WHERE RetailerID='$retailerId'";
  if ($conn->query($updateQuery) === TRUE) {
    header("Location: adminDirectory.php");
    exit();
  } else {
    echo "Error updating record: " . $conn->error;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Retailer</title>
  <link rel="stylesheet" href="updatestyles.css">
</head>

<body>
  <div class="container">
    <h2>Update Retailer</h2>
    <form action="updateRetailer.php?id=<?php echo $retailerId; ?>" method="POST">
      <label for="name">Retailer Name:</label>
      <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required><br><br>

      <label for="area">Area:</label>
      <input type="text" name="area" value="<?php echo htmlspecialchars($row['area']); ?>" required><br><br>

      <label for="city">City:</label>
      <input type="text" name="city" value="<?php echo htmlspecialchars($row['city']); ?>" required><br><br>

      <label for="contact">Phone:</label>
      <input type="text" name="contact" value="<?php echo htmlspecialchars($row['contact']); ?>" required><br><br>

      <input type="submit" value="Update Retailer">
    </form>
  </div>
</body>

</html>