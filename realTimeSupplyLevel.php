<?php
include("db.php");

// Fetch product and storage data
$sql = "SELECT 
          wp.ProductType,
          csp.quantity AS AvailableQuantity,
          csp.InTransitQty,
          cs.Area AS StorageLocation,
          cs.Capacity AS StorageCapacity,
          csp.entryDate AS EntryDate,
          csp.expiryDate AS ExpiryDate,
          csp.status
        FROM coldstorageproduct csp
        JOIN wholesaler_product wp 
          ON csp.wholesalerid = wp.wholesalerID 
         AND csp.ProductType = wp.ProductType
        JOIN cold_storage cs ON csp.storageid = cs.StorageID";

$result = $conn->query($sql);

$supplyData = [];
$productTotals = [];     // Total quantity per product type
$storageUsage = [];      // Total quantity per storage location

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $supplyData[] = $row; // Save for table display

    $productType = $row['ProductType'];
    $quantity = $row['AvailableQuantity'];
    $storageLocation = $row['StorageLocation'];

    // Combine quantities for same product
    if (isset($productTotals[$productType])) {
        $productTotals[$productType] += $quantity;
    } else {
        $productTotals[$productType] = $quantity;
    }

    // Combine storage usage per location
    if (isset($storageUsage[$storageLocation])) {
        $storageUsage[$storageLocation] += $quantity;
    } else {
        $storageUsage[$storageLocation] = $quantity;
    }
  }
}

// Prepare data for graphs
$productLabels = array_keys($productTotals);
$inventoryQuantities = array_values($productTotals);

$storageLabels = array_keys($storageUsage);
$storageQuantities = array_values($storageUsage);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-Time Supply Overview</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sortable/0.8.0/js/sortable.min.js"></script>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: 20px;
        background-color: #f7f9fc;
    }

    h2 {
        text-align: center;
        margin-bottom: 10px;
        color: #333;
    }

    p.timestamp {
        text-align: center;
        color: #777;
        font-size: 0.9em;
        margin-bottom: 30px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 40px;
    }

    th,
    td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #4CAF50;
        color: white;
        font-weight: bold;
        cursor: pointer;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    .badge {
        padding: 5px 10px;
        border-radius: 20px;
        color: white;
        font-size: 12px;
        font-weight: bold;
    }

    .normal {
        background-color: #28a745;
    }

    .low {
        background-color: #dc3545;
    }

    .overstock {
        background-color: #ffc107;
        color: black;
    }

    .expiry-soon {
        background-color: #ff5722;
    }

    .progress-bar {
        background-color: #e0e0e0;
        border-radius: 20px;
        position: relative;
        height: 20px;
        overflow: hidden;
    }

    .progress {
        height: 100%;
        text-align: center;
        color: white;
        border-radius: 20px;
        line-height: 20px;
        transition: width 0.5s ease;
    }

    .chart-container {
        display: flex;
        justify-content: space-around;
        align-items: flex-start;
        flex-wrap: wrap;
        width: 90%;
        margin: 0 auto;
        gap: 20px;
    }

    .chart-container canvas {
        width: 600px;
        height: 600px;
    }


    @media(max-width: 768px) {
        .chart-container {
            width: 100%;
        }

        table,
        thead,
        tbody,
        th,
        td,
        tr {
            display: block;
        }

        th {
            position: sticky;
            top: 0;
            background-color: #4CAF50;
        }

        td {
            text-align: right;
            position: relative;
            padding-left: 50%;
        }

        td::before {
            content: attr(data-label);
            position: absolute;
            left: 10px;
            font-weight: bold;
        }
    }
    </style>
</head>

<body>

    <h2>Real-Time Supply Overview</h2>
    <p class="timestamp">Last Updated: <?php echo date('Y-m-d H:i:s'); ?></p>

    <table class="sortable">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Current Inventory (Kg)</th>
                <th>Cold Storage Capacity</th>
                <th>Storage Location</th>
                <th>In-Transit Quantity (Kg)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($supplyData as $item): ?>
            <?php
      $fillPercent = ($item['AvailableQuantity'] / $item['StorageCapacity']) * 100;
      $fillPercent = round($fillPercent);
      $expiryWarning = (strtotime($item['ExpiryDate']) - time()) < (5 * 86400); // 5 days

      if ($expiryWarning) {
        $statusClass = 'expiry-soon';
        $statusText = 'Expiring Soon';
      } elseif ($fillPercent > 85) {
        $statusClass = 'overstock';
        $statusText = 'Overstock';
      } elseif ($fillPercent < 50) {
        $statusClass = 'low';
        $statusText = 'Low Stock';
      } else {
        $statusClass = 'normal';
        $statusText = 'Normal';
      }

      // Dynamic progress color
      if ($fillPercent > 85) $progressColor = '#ffc107';
      elseif ($fillPercent < 50) $progressColor = '#dc3545';
      else $progressColor = '#4CAF50';
    ?>
            <tr>
                <td data-label="Product Name" title="<?php echo htmlspecialchars($item['ProductType']); ?>">
                    <?php echo htmlspecialchars($item['ProductType']); ?></td>
                <td data-label="Current Inventory (Kg)"><?php echo htmlspecialchars($item['AvailableQuantity']); ?></td>
                <td data-label="Cold Storage Capacity">
                    <div class="progress-bar">
                        <div class="progress"
                            style="background-color: <?php echo $progressColor; ?>; width: <?php echo $fillPercent; ?>%;">
                            <?php echo $fillPercent; ?>%
                        </div>
                    </div>
                </td>
                <td data-label="Storage Location"><?php echo htmlspecialchars($item['StorageLocation']); ?></td>
                <td data-label="In-Transit Quantity (Kg)"><?php echo htmlspecialchars($item['InTransitQty']); ?></td>
                <td data-label="Status"><span
                        class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="chart-container">
        <canvas id="productChart" style="margin-bottom: 50px;"></canvas>
        <canvas id="storageChart"></canvas>
    </div>

    <script>
    // Chart 1: Product Wise Inventory
    const productLabels = <?php echo json_encode($productLabels); ?>;
    const inventoryQuantities = <?php echo json_encode($inventoryQuantities); ?>;

    const ctx1 = document.getElementById('productChart').getContext('2d');
    const productChart = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: productLabels,
            datasets: [{
                label: 'Total Inventory (Kg)',
                backgroundColor: '#4CAF50',
                data: inventoryQuantities
            }]
        },
        options: {
            responsive: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Total Inventory per Product'
                },
                legend: {
                    display: false
                }
            }
        }
    });

    // Chart 2: Storage Location Usage
    const storageLabels = <?php echo json_encode($storageLabels); ?>;
    const storageQuantities = <?php echo json_encode($storageQuantities); ?>;

    const ctx2 = document.getElementById('storageChart').getContext('2d');
    const storageChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: storageLabels,
            datasets: [{
                label: 'Storage Usage (Kg)',
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#8BC34A', '#E91E63', '#9C27B0'],
                data: storageQuantities
            }]
        },
        options: {
            responsive: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Storage Location Usage'
                }
            }
        }
    });
    </script>

</body>

</html>