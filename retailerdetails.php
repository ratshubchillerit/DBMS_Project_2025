<?php
include "./db.php";

// Assuming you're using vendor_id to represent the retailer
$retailerID = $_GET['retailer_id'] ?? 0;  // Changed vendor_id to retailer_id

// SQL query to fetch retailer details
$sql = "SELECT *
        FROM retailer r
        WHERE r.RetailerID = ?";  // Now directly working with retailer

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $retailerID);  // Binding retailerID to the query
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Retailer Details</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <div class="bg-white shadow-md rounded-lg p-8 max-w-lg w-full">
    <?php if ($row): ?>
      <h2 class="text-2xl font-bold mb-4 text-red-600">Retailer Details</h2>
      <ul class="space-y-3 text-gray-700">
        <li><span class="font-semibold">Name:</span> <?= htmlspecialchars($row['name']) ?></li>
        <li><span class="font-semibold">Contact:</span> <?= htmlspecialchars($row['contact']) ?></li>
        <li><span class="font-semibold">Area:</span> <?= htmlspecialchars($row['area']) ?></li>
        <li><span class="font-semibold">City:</span> <?= htmlspecialchars($row['city']) ?></li>
        <li><span class="font-semibold">Minimum Delivery Days:</span> <?= $row['MinimumDeliveryDays'] ?> day(s)</li>
      </ul>
      <div class="mt-6 text-right">
        <a href="javascript:history.back()" class="text-red-600 hover:underline">← Back</a>
      </div>
    <?php else: ?>
      <p class="text-center text-red-500 text-lg">Retailer not found.</p>
    <?php endif; ?>
  </div>
</body>

</html>