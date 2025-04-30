<?php
include './db.php';

$retailerID = $_GET['retailer_id'] ?? '';
$productType = $_GET['product_type'] ?? '';
$currentPrice = 0;

if (!empty($retailerID) && !empty($productType)) {
  $stmt = $conn->prepare("SELECT PricePerUnit FROM retailer_product WHERE retailerID = ? AND ProductType = ?");
  $stmt->bind_param("is", $retailerID, $productType);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($row = $result->fetch_assoc()) {
    $currentPrice = $row['PricePerUnit'];
  } else {
    echo "<script>alert('Product not found.'); window.history.back();</script>";
    exit;
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $retailerID = intval($_POST['retailer_id']);
  $productType = $_POST['product_type'];
  $newPrice = floatval($_POST['new_price']);

  $sql = "UPDATE retailer_product SET PricePerUnit = ? WHERE retailerID = ? AND ProductType = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("dis", $newPrice, $retailerID, $productType);

  if ($stmt->execute()) {
    echo "<script>alert('Price updated successfully.'); window.location.href='retailer.php#inventory';</script>";
  } else {
    echo "<script>alert('Failed to update price.'); window.history.back();</script>";
  }
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Update Product Price</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      background: #f5f7fa;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .form-container {
      background: #fff;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 450px;
    }

    h2 {
      margin-bottom: 10px;
      color: #333;
    }

    p {
      color: #555;
      font-size: 16px;
      margin-bottom: 25px;
    }

    label {
      display: block;
      font-weight: bold;
      margin-bottom: 8px;
      color: #222;
    }

    input[type="number"] {
      width: 100%;
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-bottom: 20px;
      transition: border 0.3s ease;
    }

    input[type="number"]:focus {
      border-color: #007bff;
      outline: none;
    }

    button {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      font-weight: bold;
      background: #28a745;
      border: none;
      border-radius: 6px;
      color: white;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background: #218838;
    }

    .back-link {
      margin-top: 20px;
      display: inline-block;
      text-align: center;
      width: 100%;
      color: #007bff;
      text-decoration: none;
      font-size: 14px;
    }

    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <div class="form-container">
    <h2>Update Price for <?= htmlspecialchars($productType) ?></h2>
    <p>Current Price: <strong>$<?= number_format($currentPrice, 2) ?></strong> per kg</p>
    <form method="POST">
      <input type="hidden" name="retailer_id" value="<?= htmlspecialchars($retailerID) ?>">
      <input type="hidden" name="product_type" value="<?= htmlspecialchars($productType) ?>">

      <label for="new_price">New Price (per kg)</label>
      <input type="number" step="0.01" name="new_price" id="new_price" required placeholder="Enter new price">

      <button type="submit">Update Price</button>
    </form>
    <a href="retailer.php" class="back-link">← Back to Dashboard</a>
  </div>
</body>

</html>