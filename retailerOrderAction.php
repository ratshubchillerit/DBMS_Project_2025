<?php
include './db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $orderID = $_POST['order_id'];
  $action = $_POST['action'];
  $status = ($action === 'Deliver') ? 'Delivered' : 'Rejected';

  // Update order status
  $updateOrderSQL = "UPDATE customer_order SET OrderStatus = ? WHERE OrderID = ?";
  $stmt = $conn->prepare($updateOrderSQL);
  $stmt->bind_param("si", $status, $orderID);
  $stmt->execute();

  // If delivered, update inventory
  if ($action === 'Deliver') {
    // Fetch product type, quantity, and retailerID from the order
    $fetchOrderSQL = "SELECT ProductType, OrderQuantity, RetailerID FROM customer_order WHERE OrderID = ?";
    $stmt = $conn->prepare($fetchOrderSQL);
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    $productType = $result['ProductType'];
    $orderQty = $result['OrderQuantity'];
    $retailerID = $result['RetailerID'];

    // Subtract from inventory
    $updateInventorySQL = "UPDATE retailer_product 
                               SET AvailableQuantity = AvailableQuantity - ? 
                               WHERE RetailerID = ? AND ProductType = ?";
    $stmt = $conn->prepare($updateInventorySQL);
    $stmt->bind_param("iis", $orderQty, $retailerID, $productType);
    $stmt->execute();
  }

  header("Location: retailer.php#new-orders");
  exit;
} else {
  echo "Invalid request.";
}
