<?php
include ("db.php");

if (isset($_GET['id'])) {
    $retailerId = $_GET['id'];
    
    // Delete the retailer record
    $deleteQuery = "DELETE FROM retailer WHERE RetailerID = '$retailerId'";
    if ($conn->query($deleteQuery) === TRUE) {
        // Redirect to admin directory page after deletion
        header("Location: adminDirectory.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>
