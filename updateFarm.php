<?php
include("db.php");
if (isset($_GET['id'])) {
    $farmId = $_GET['id'];
    $query = "SELECT * FROM farm WHERE FarmID = '$farmId'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        die("No farm found with that ID.");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get updated values from the form.
    $farmName = $_POST['farmName'];
    $area = $_POST['area'];
    $city = $_POST['city'];
    $contact = $_POST['contact'];
    $farmSize = $_POST['farmSize'];

    $updateQuery = "UPDATE farm SET FarmName='$farmName', Area='$area', City='$city', ContactNumber='$contact', FarmSize='$farmSize' WHERE FarmID='$farmId'";
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
    <title>Update Farm</title>
    <link rel="stylesheet" href="updatestyles.css">
</head>
<body>
    <div class="container">
      <h2>Update Farm</h2>
      <form action="updateFarm.php?id=<?php echo $farmId; ?>" method="POST">
        <label for="farmName">Farm Name:</label>
        <input type="text" name="farmName" value="<?php echo $row['FarmName']; ?>" required><br><br>

        <label for="area">Area:</label>
        <input type="text" name="area" value="<?php echo $row['Area']; ?>" required><br><br>

        <label for="city">City:</label>
        <input type="text" name="city" value="<?php echo $row['City']; ?>" required><br><br>

        <label for="contact">Phone:</label>
        <input type="text" name="contact" value="<?php echo $row['ContactNumber']; ?>" required><br><br>

        <label for="farmSize">Farm Size (acres):</label>
        <input type="text" name="farmSize" value="<?php echo $row['FarmSize']; ?>" required><br><br>

        <input type="submit" value="Update Farm">
       </form>
    </div>
    
</body>
</html>
