<?php

session_start();
include './db.php';

// Fetch wholesaler info joined with vendor data
$retailerID = $_GET['retailer_id'] ?? 0;
$wholesalerID = $_GET['wholesaler_id'] ?? 0;
$productType = $_GET['product_type'] ?? '';
$minQty = $_GET['min_qty'] ?? 0;
$pricePerUnit = $_GET['price'] ?? 0.00;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $quantity = (int) $_POST['quantity'];

  if ($quantity < $minQty) {
    $error = "Quantity must be at least {$minQty} kg.";
  } else {
    $totalPrice = $quantity * $pricePerUnit;
    $orderDate = date('Y-m-d');
    $deliveryDate = date('Y-m-d', strtotime('+4 days'));
    $orderStatus = 'Pending';

    $insertSQL = "INSERT INTO retailer_wholesaler_order 
(RetailerID, WholesalerID, ProductType, OrderQuantity, OrderDate, DeliveryDate, PricePerUnit, TotalPrice,OrderStatus) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)";

    $stmt = $conn->prepare($insertSQL);

    // Fix the types here (OrderDate and DeliveryDate are strings)
    $stmt->bind_param("iisisssds", $retailerID, $wholesalerID, $productType, $quantity, $orderDate, $deliveryDate, $pricePerUnit, $totalPrice, $orderStatus);


    if ($stmt->execute()) {
      // Redirect after successful order
      header("Location: retailer.php");
      exit();
    } else {
      $error = "Failed to place order: " . $stmt->error;
    }
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Place Product Request</title>
  <link rel="stylesheet" href="retailer.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

    body {
      background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%);
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }

    .form-container {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      border-radius: 20px;
      padding: 40px 30px;
      width: 100%;
      max-width: 500px;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.3);
      border: 1px solid rgba(255, 255, 255, 0.3);
      animation: fadeIn 0.8s ease-in-out;
    }

    h2 {
      text-align: center;
      color: #fff;
      margin-bottom: 30px;
      font-weight: 600;
      font-size: 28px;
    }

    label {
      font-weight: 600;
      color: #fff;
      display: block;
      margin-bottom: 6px;
      font-size: 16px;
      text-shadow: 0 0 6px rgba(0, 0, 0, 0.4);
      transition: all 0.3s ease;
    }

    .form-container input[type="text"],
    .form-container input[type="number"] {
      width: 100%;
      padding: 12px 15px;
      margin-bottom: 20px;
      border: none;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.25);
      color: black;
      font-size: 15px;
      backdrop-filter: blur(5px);
      -webkit-backdrop-filter: blur(5px);
      transition: 0.3s;
    }

    .form-container input[type="text"]:focus,
    .form-container input[type="number"]:focus {
      background: rgba(255, 255, 255, 0.35);
      outline: none;
      box-shadow: 0 0 8px rgba(255, 255, 255, 0.3);
    }

    .form-container input[type="submit"] {
      width: 100%;
      background: rgba(0, 153, 255, 0.8);
      color: white;
      padding: 14px;
      font-size: 16px;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .form-container input[type="submit"]:hover {
      background: rgba(0, 122, 204, 0.9);
    }

    .error,
    .success {
      text-align: center;
      font-size: 14px;
      padding: 12px;
      border-radius: 10px;
      margin-bottom: 20px;
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
    }

    .error {
      background: rgba(255, 0, 0, 0.3);
      color: #fff;
      border: 1px solid rgba(255, 0, 0, 0.5);
    }

    .success {
      background: rgba(0, 255, 0, 0.3);
      color: #fff;
      border: 1px solid rgba(0, 255, 0, 0.5);
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>

</head>

<body>

  <div class="form-container">
    <h2>Place Product Request</h2>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
      <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Product Type:</label>
      <input type="text" name="product_type" value="<?= htmlspecialchars($productType) ?>" readonly>

      <label>Minimum Quantity (kg):</label>
      <input type="text" name="min_qty" value="<?= htmlspecialchars($minQty) ?>" readonly>

      <label>Price per Unit:</label>
      <input type="text" name="price" value="<?= htmlspecialchars($pricePerUnit) ?>" readonly>

      <label>Order Quantity (kg):</label>
      <input type="number" name="quantity" min="<?= htmlspecialchars($minQty) ?>" required>

      <input type="submit" value="Place Order">
    </form>
  </div>

</body>

</html>