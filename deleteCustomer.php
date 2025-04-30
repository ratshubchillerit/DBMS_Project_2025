<?php
include("db.php");

if (isset($_GET['id'])) {
    $customerId = $_GET['id'];
    
    // Delete the customer record
    $deleteQuery = "DELETE FROM customer WHERE CustomerID = '$customerId'";
    if ($conn->query($deleteQuery) === TRUE) {
        // Redirect to admin directory page after deletion
        header("Location: adminDirectory.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>


