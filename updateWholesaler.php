<?php
include ("db.php");

if (isset($_GET['id'])) {
    $wholesalerId = $_GET['id'];
    $query = "SELECT * FROM wholesaler WHERE WholesalerID = '$wholesalerId'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        die("No wholesaler found with that ID.");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get updated values from the form.
    $name = $_POST['name'];
    $distributionArea = $_POST['distributionArea'];
    $area = $_POST['area'];
    $city = $_POST['city'];
    $contact = $_POST['contact'];

    $updateQuery = "UPDATE wholesaler SET name='$name', DistributionArea='$distributionArea', area='$area', city='$city', contact='$contact' WHERE WholesalerID='$wholesalerId'";
    if ($conn->query($updateQuery) === TRUE) {
        // Redirect to admin directory page after update
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
    <title>Update Wholesaler</title>
    <link rel="stylesheet" href="updatestyles.css">
</head>
<body>
    <div class="container">
      <h2>Update Wholesaler</h2>
      <form action="updateWholesaler.php?id=<?php echo $wholesalerId; ?>" method="POST">
        <label for="name">Wholesaler Name:</label>
        <input type="text" name="name" value="<?php echo $row['name']; ?>" required><br><br>

        <label for="distributionArea">Distribution Area:</label>
        <input type="text" name="distributionArea" value="<?php echo $row['DistributionArea']; ?>" required><br><br>
        <label for="area">Area:</label>
        <input type="text" name="area" value="<?php echo $row['area']; ?>" required><br><br>

        <label for="city">City:</label>
        <input type="text" name="city" value="<?php echo $row['city']; ?>" required><br><br>

        <label for="contact">Phone:</label>
        <input type="text" name="contact" value="<?php echo $row['contact']; ?>" required><br><br>

        <input type="submit" value="Update Wholesaler">
      </form>
    </div>
 
</body>
</html>
