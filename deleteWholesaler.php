<?php
include("db.php");

if (isset($_GET['id'])) {
    $wholesalerId = $_GET['id'];
    
    // Delete the wholesaler record
    $deleteQuery = "DELETE FROM wholesaler WHERE WholesalerID = '$wholesalerId'";
    if ($conn->query($deleteQuery) === TRUE) {
        // Redirect to admin directory page after deletion
        header("Location: adminDirectory.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>
