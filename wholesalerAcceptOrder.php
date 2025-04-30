<?php
include './db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $orderID = $_POST['orderID'] ?? null;
  $action = $_POST['action'] ?? '';
  $retailerID = $_POST['retailerID'];

  if (!$orderID || $action !== 'deliver') {
    die('Invalid request.');
  }

  // Fetch order details securely using OrderID
  $stmt = $conn->prepare("SELECT ProductType, OrderQuantity, WholesalerID FROM retailer_wholesaler_order WHERE OrderID = ?");
  $stmt->bind_param("i", $orderID);
  $stmt->execute();
  $stmt->bind_result($product, $quantity, $wholesalerID);
  if (!$stmt->fetch()) {
    die("Order not found.");
  }
  $stmt->close();

  // Update order status to "Delivered"
  $stmt = $conn->prepare("UPDATE retailer_wholesaler_order SET OrderStatus = 'Delivered' WHERE OrderID = ?");
  $stmt->bind_param("i", $orderID);
  $stmt->execute();
  $stmt->close();

  // Reduce inventory
  $stmt = $conn->prepare("UPDATE wholesaler_product SET AvailableQuantity = AvailableQuantity - ? WHERE wholesalerID = ? AND ProductType = ?");
  $stmt->bind_param("dis", $quantity, $wholesalerID, $product);
  $stmt->execute();
  $stmt->close();
  // Add quantity to retailer's inventory
  // Check if the retailer has the product in their inventory
  $stmt = $conn->prepare("SELECT AvailableQuantity FROM retailer_product WHERE retailerID = ? AND ProductType = ?");
  $stmt->bind_param("is", $retailerID, $product);
  $stmt->execute();
  $stmt->bind_result($availableQuantity);
  $stmt->fetch();
  $stmt->close();

  if ($availableQuantity !== null) {
    // Retailer has the product, so update the inventory
    $stmt = $conn->prepare("UPDATE retailer_product SET AvailableQuantity = AvailableQuantity + ? WHERE retailerID = ? AND ProductType = ?");
    $stmt->bind_param("dis", $quantity, $retailerID, $product);
    $stmt->execute();
    if ($stmt->affected_rows === 0) {
      die("Failed to update retailer inventory.");
    }
  } else {
    // Retailer doesn't have the product, so insert it
    $stmt = $conn->prepare("INSERT INTO retailer_product (retailerID, ProductType, AvailableQuantity) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $retailerID, $product, $quantity);
    $stmt->execute();
    if ($stmt->affected_rows === 0) {
      die("Failed to add product to retailer inventory.");
    }
  }

  $stmt->close();



  // Redirect back
  header("Location: wholesaler.php");
  exit;
} else {
  echo "Invalid request method.";
}
