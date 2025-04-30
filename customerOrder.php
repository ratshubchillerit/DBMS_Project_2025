<?php
session_start();
include "./db.php";

$customerID = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $retailerID = $_POST['RetailerID'] ?? '';
  $productType = $_POST['product_type'] ?? '';
  $orderQty = floatval($_POST['OrderQuantity'] ?? 0);
  $pricePerUnit = floatval($_POST['PricePerUnit'] ?? 0);
  $minDays = intval($_POST['MinimumDeliveryDays'] ?? 0);
  $orderDate = date('Y-m-d');
  $deliveryDate = date('Y-m-d', strtotime("+$minDays days"));

  $sql = "INSERT INTO customer_order 
        (CustomerID, RetailerID, ProductType, OrderQuantity, PricePerUnit, OrderDate, DeliveryDate, OrderStatus)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("iisdsss", $customerID, $retailerID, $productType, $orderQty, $pricePerUnit, $orderDate, $deliveryDate);

  if ($stmt->execute()) {
    echo "<script>alert('Order placed successfully!'); window.location.href='customer.php';</script>";
  } else {
    echo "<script>alert('Failed to place order.'); window.history.back();</script>";
  }
  exit;
}


$retailerID = $_GET['retailer_id'] ?? '';
$retailerName = $_GET['retailer_name'] ?? '';
$productType = $_GET['product_type'] ?? '';
$unitPrice = $_GET['unit_price'] ?? '';
$minDays = isset($_GET['min_days']) ? intval($_GET['min_days']) : 0;

if ($minDays > 0) {
  $deliveryDate = date('Y-m-d', strtotime("+$minDays days"));
} else {
  $deliveryDate = 'N/A';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Place Order</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 40px;
    }

    .form-container {
      max-width: 500px;
      margin: auto;
      border: 1px solid #ddd;
      padding: 20px;
      border-radius: 8px;
    }

    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }

    input[type="number"] {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
    }

    button {
      margin-top: 20px;
      padding: 10px 20px;
      background-color: green;
      color: white;
      border: none;
      border-radius: 4px;
    }

    .info {
      margin-top: 20px;
    }
  </style>
</head>

<body>
  <div class="form-container">
    <h2>Place Your Order</h2>
    <form action="customerOrder.php" method="POST">
      <input type="hidden" name="RetailerID" value="<?= htmlspecialchars($retailerID) ?>">
      <input type="hidden" name="PricePerUnit" value="<?= htmlspecialchars($unitPrice) ?>">
      <input type="hidden" name="MinimumDeliveryDays" value="<?= htmlspecialchars($minDays) ?>">
      <input type="hidden" name="product_type" value="<?= htmlspecialchars($productType) ?>">

      <p><strong>Retailer:</strong> <?= htmlspecialchars($retailerName) ?></p>
      <p><strong>Product:</strong> <?= htmlspecialchars($productType) ?></p>
      <p><strong>Price per unit:</strong> $<?= number_format((float)$unitPrice, 2) ?></p>
      <p><strong>Expected delivery date:</strong> <?= $deliveryDate ?></p>

      <label for="OrderQuantity">Enter Quantity:</label>
      <input type="number" name="OrderQuantity" id="OrderQuantity" required min="1" oninput="calculateTotal()">

      <div class="info">
        <strong>Total Price:</strong> $<span id="TotalPrice">0.00</span>
      </div>

      <button type="submit">Confirm Order</button>
    </form>
  </div>

  <script>
    function calculateTotal() {
      const quantity = document.getElementById("OrderQuantity").value;
      const unitPrice = <?= json_encode((float)$unitPrice) ?>;
      const total = quantity * unitPrice;
      document.getElementById("TotalPrice").textContent = total.toFixed(2);
    }
  </script>
</body>

</html>