<?php
session_start();
include './db.php';

$retailerID = $_GET['retailer_id'] ?? 0;

// Fetch wholesaler info
$sql = "
    SELECT WholesalerID, name, area, city, BulkDiscountRate, DistributionArea, contact
    FROM wholesaler
";
$result = $conn->query($sql);
$wholesalers = $result->fetch_all(MYSQLI_ASSOC);

// Fetch wholesaler product details
$productSQL = "
    SELECT wholesalerID, ProductType, MinimumOrderQty, PricePerUnit
    FROM wholesaler_product
";
$productResult = $conn->query($productSQL);
$products = $productResult->fetch_all(MYSQLI_ASSOC);

// Organize products under each wholesaler
$productsByWholesaler = [];
foreach ($products as $p) {
  $productsByWholesaler[$p['wholesalerID']][] = $p;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Request Products</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- FontAwesome -->
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f0f4f7, #d9e2ec);
      margin: 0;
      padding: 30px;
    }

    .dashboard-content {
      max-width: 1200px;
      margin: auto;
      padding: 20px;
    }

    h1 {
      text-align: center;
      color: #333;
      margin-bottom: 40px;
      font-weight: 600;
    }

    .wholesaler-card {
      background: #ffffff;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
      padding: 25px;
      margin-bottom: 30px;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .wholesaler-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 25px rgba(0, 0, 0, 0.12);
    }

    .wholesaler-card h3 {
      margin-top: 0;
      color: #0077b6;
      font-size: 24px;
    }

    .info-line {
      margin: 8px 0;
      color: #555;
      font-size: 16px;
    }

    .info-line i {
      color: #0077b6;
      margin-right: 8px;
    }

    .info-line span {
      margin-right: 20px;
    }

    .product-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      animation: fadeIn 1s ease;
    }

    .product-table th,
    .product-table td {
      border: 1px solid #e0e0e0;
      padding: 12px;
      text-align: center;
      font-size: 15px;
    }

    .product-table th {
      background-color: #f1f5f9;
      color: #333;
    }

    .product-table td {
      background: #fafafa;
    }

    .product-table tr:hover td {
      background-color: #eef6fb;
      transition: background-color 0.3s;
    }

    .place-order-btn {
      background-color: #0077b6;
      color: #fff;
      padding: 8px 16px;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      cursor: pointer;
      text-decoration: none;
      transition: background-color 0.3s, transform 0.2s;
      display: inline-block;
    }

    .place-order-btn:hover {
      background-color: #005f8a;
      transform: scale(1.05);
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>

<body>

  <div class="dashboard-content">
    <h1>Available Wholesalers</h1>

    <?php foreach ($wholesalers as $wholesaler): ?>
      <div class="wholesaler-card">
        <h3><?= htmlspecialchars($wholesaler['name']) ?></h3>

        <div class="info-line">
          <span title="Contact Number">
            <i class="fas fa-phone"></i> <?= htmlspecialchars($wholesaler['contact']) ?>
          </span>
          <span title="City">
            <i class="fas fa-city"></i> <?= htmlspecialchars($wholesaler['city']) ?>
          </span>
          <span title="Area">
            <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($wholesaler['area']) ?>
          </span>
        </div>

        <div class="info-line">
          <span title="Distribution Area">
            <i class="fas fa-truck"></i> <?= htmlspecialchars($wholesaler['DistributionArea']) ?>
          </span>
          <span title="Bulk Discount Rate">
            <i class="fas fa-percent"></i> <?= htmlspecialchars($wholesaler['BulkDiscountRate']) ?>%
          </span>
        </div>

        <?php if (isset($productsByWholesaler[$wholesaler['WholesalerID']])): ?>
          <table class="product-table">
            <thead>
              <tr>
                <th>Product Type</th>
                <th>Minimum Order Quantity</th>
                <th>Price Per Unit</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($productsByWholesaler[$wholesaler['WholesalerID']] as $product): ?>
                <tr>
                  <td><?= htmlspecialchars($product['ProductType']) ?></td>
                  <td><?= htmlspecialchars($product['MinimumOrderQty']) ?> kg</td>
                  <td>$<?= htmlspecialchars($product['PricePerUnit']) ?></td>
                  <td>
                    <a class="place-order-btn"
                      href="retailerPlaceRequest.php?retailer_id=<?= $retailerID ?>&wholesaler_id=<?= $wholesaler['WholesalerID'] ?>&product_type=<?= urlencode($product['ProductType']) ?>&min_qty=<?= $product['MinimumOrderQty'] ?>&price=<?= $product['PricePerUnit'] ?>">
                      Place Order
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p>No products listed.</p>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>

  </div>

</body>

</html>