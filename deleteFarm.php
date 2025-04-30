<?php
include("db.php");
if (isset($_GET['id'])) {
    $farmId = $_GET['id'];
    
    // Delete the farm record
    $deleteQuery = "DELETE FROM farm WHERE FarmID = '$farmId'";
    if ($conn->query($deleteQuery) === TRUE) {
        // Redirect to admin directory page after deletion
        header("Location: adminDirectory.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>
