<?php
include("db.php");

if (isset($_GET['id'])) {
    $customerId = $_GET['id'];
    $query = "SELECT * FROM customer WHERE CustomerID = '$customerId'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        die("No customer found with that ID.");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get updated values from the form.
    $name = $_POST['name'];
    $area = $_POST['area'];
    $city = $_POST['city'];
    $contact = $_POST['contact'];

    $updateQuery = "UPDATE customer SET name='$name', area='$area', city='$city', contact='$contact' WHERE CustomerID='$customerId'";
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
    <title>Update Customer</title>
    <link rel="stylesheet" href="updatestyles.css"> 
</head>
<body>
    <div class="container">
      <h2>Update Customer</h2>
      <form action="updateCustomer.php?id=<?php echo $customerId; ?>" method="POST">
        <label for="name">Customer Name:</label>
        <input type="text" name="name" value="<?php echo $row['Name']; ?>" required><br><br>

        <label for="area">Area:</label>
        <input type="text" name="area" value="<?php echo $row['Area']; ?>" required><br><br>

        <label for="city">City:</label>
        <input type="text" name="city" value="<?php echo $row['City']; ?>" required><br><br>

        <label for="contact">Phone:</label>
        <input type="text" name="contact" value="<?php echo $row['Contact']; ?>" required><br><br>

        <input type="submit" value="Update Customer">
      </form>
    </div>
  
</body>
</html>
