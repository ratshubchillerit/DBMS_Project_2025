<?php
include("db.php"); 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $SuggestedAction = $_POST['SuggestedAction'];
    $Reasoning = $_POST['Reasoning'];
    $RecommendationDate = $_POST['RecommendationDate'];
    $FarmID = $_POST['FarmID'];
    $OfficerID = $_POST['OfficerID'];


    $sql = "INSERT INTO recommendation (SuggestedAction, Reasoning, RecommendationDate, FarmID, OfficerID) 
            VALUES ('$SuggestedAction', '$Reasoning', '$RecommendationDate', '$FarmID', '$OfficerID')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Recommendation added successfully');</script>";
        header("Location: admin.php");
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Recommendation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f4f6f8;
        margin: 0;
        padding: 20px;
    }

    .form-container {
        background: #fff;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        width: 500px;
        margin: 0 auto;
    }

    .form-container h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #2c3e50;
    }

    .form-container label {
        font-size: 16px;
        color: #333;
        margin-bottom: 8px;
        display: block;
    }

    .form-container input,
    .form-container select,
    .form-container textarea {
        width: 100%;
        padding: 12px;
        margin: 8px 0;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 16px;
    }

    .form-container textarea {
        height: 100px;
        resize: vertical;
    }

    .form-container button {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        width: 100%;
        font-size: 18px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .form-container button:hover {
        background-color: #45a049;
    }

    .form-container button:active {
        background-color: #388e3c;
    }

    .form-container .back-btn {
        background-color: #f44336;
        color: white;
        text-align: center;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        display: block;
        margin-top: 20px;
    }

    .form-container .back-btn:hover {
        background-color: #e53935;
    }
    </style>
</head>

<body>

    <div class="form-container">
        <h2>Add Recommendation</h2>

        <!-- Recommendation Form -->
        <form action="addRecommendation.php" method="POST">
            <label for="SuggestedAction">Suggested Action</label>
            <textarea id="SuggestedAction" name="SuggestedAction" required></textarea>

            <label for="Reasoning">Reasoning</label>
            <textarea id="Reasoning" name="Reasoning" required></textarea>

            <label for="RecommendationDate">Recommendation Date</label>
            <input type="date" id="RecommendationDate" name="RecommendationDate" required>

            <label for="FarmID">Farm ID</label>
            <select id="FarmID" name="FarmID" required>
                <option value="">Select Farm</option>
                <?php
                $farm_query = "SELECT FarmID, FarmName FROM farm"; 
                $farm_result = $conn->query($farm_query);
                while ($farm = $farm_result->fetch_assoc()) {
                    echo "<option value='" . $farm['FarmID'] . "'>" . $farm['FarmName'] . "</option>";
                }
                ?>
            </select>

            <label for="OfficerID">Officer ID</label>
            <select id="OfficerID" name="OfficerID" required>
                <option value="">Select Officer</option>
                <?php
                $officer_query = "SELECT OfficerID, Name FROM government_officer"; 
                $officer_result = $conn->query($officer_query);
                while ($officer = $officer_result->fetch_assoc()) {
                    echo "<option value='" . $officer['OfficerID'] . "'>" . $officer['Name'] . "</option>";
                }
                ?>
            </select>

            <button type="submit" name="submit">Add Recommendation</button>
        </form>

        <a href="admin.php" class="back-btn">Back to Dashboard</a>
    </div>

</body>

</html>
<?php 

$conn->close();
?>