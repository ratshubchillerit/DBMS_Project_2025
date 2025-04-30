<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

$retailerID = $_SESSION['user_id'];
// Fetch retailer details
$retailerInfoSQL = "SELECT name, area, city FROM retailer WHERE RetailerID = ?";
$stmt = $conn->prepare($retailerInfoSQL);
$stmt->bind_param("i", $retailerID);
$stmt->execute();
$retailerInfoResult = $stmt->get_result();
$retailerInfo = $retailerInfoResult->fetch_assoc();


// Fetch total orders
$totalOrderSQL = "SELECT COUNT(*) AS total_orders FROM customer_order WHERE RetailerID = ?";
$stmt = $conn->prepare($totalOrderSQL);
$stmt->bind_param("i", $retailerID);
$stmt->execute();
$totalOrderResult = $stmt->get_result();
$totalOrderss = $totalOrderResult->fetch_assoc()['total_orders'];

// Fetch new (pending) orders
$newOrderCountSQL = "SELECT COUNT(*) AS new_orders FROM customer_order WHERE RetailerID = ? AND OrderStatus = 'Pending'";
$stmt = $conn->prepare($newOrderCountSQL);
$stmt->bind_param("i", $retailerID);
$stmt->execute();
$newOrderCountResult = $stmt->get_result();
$newOrdersCount = $newOrderCountResult->fetch_assoc()['new_orders'];

// Fetch total revenue
$totalRevenueSQL = "SELECT SUM(OrderQuantity * PricePerUnit) AS total_revenue FROM customer_order WHERE RetailerID = ? AND OrderStatus = 'Delivered'";
$stmt = $conn->prepare($totalRevenueSQL);
$stmt->bind_param("i", $retailerID);
$stmt->execute();
$totalRevenueResult = $stmt->get_result();
$totalRevenue = $totalRevenueResult->fetch_assoc()['total_revenue'];
$totalRevenue = $totalRevenue ? $totalRevenue : 0; // in case it's NULL

// Fetch order status breakdown
$statusSQL = "SELECT OrderStatus, COUNT(*) as count FROM customer_order WHERE RetailerID = ? GROUP BY OrderStatus";
$stmt = $conn->prepare($statusSQL);
$stmt->bind_param("i", $retailerID);
$stmt->execute();
$statusResult = $stmt->get_result();

$statusData = [];
while ($row = $statusResult->fetch_assoc()) {
    $statusData[$row['OrderStatus']] = $row['count'];
}

// Fetch monthly revenue trend
$revenueSQL = "SELECT DATE_FORMAT(OrderDate, '%Y-%m') AS month, SUM(OrderQuantity * PricePerUnit) as revenue 
               FROM customer_order 
               WHERE RetailerID = ? AND OrderStatus = 'Delivered'
               GROUP BY month
               ORDER BY month ASC";
$stmt = $conn->prepare($revenueSQL);
$stmt->bind_param("i", $retailerID);
$stmt->execute();
$revenueResult = $stmt->get_result();

$months = [];
$revenues = [];
while ($row = $revenueResult->fetch_assoc()) {
    $months[] = $row['month'];
    $revenues[] = $row['revenue'];
}

// Fetch Total Orders and Total Sales grouped by ProductType
// Fetch Total Orders, Quantities and Sales grouped by ProductType
$productTypeStatsSQL = "
    SELECT 
        ProductType, 
        COUNT(*) AS TotalOrders,
        SUM(OrderQuantity) AS TotalQuantity,
        SUM(OrderQuantity * PricePerUnit) AS TotalSales
    FROM customer_order
    WHERE RetailerID = ?
    GROUP BY ProductType
";

$stmt = $conn->prepare($productTypeStatsSQL);
$stmt->bind_param("i", $retailerID);

$productTypes = [];
$totalOrders = [];
$totalQuantities = [];
$totalSales = [];

if ($stmt->execute()) {
    $productTypeStatsResult = $stmt->get_result();
    $productTypeStats = $productTypeStatsResult->fetch_all(MYSQLI_ASSOC);

    if (!empty($productTypeStats)) {
        foreach ($productTypeStats as $row) {
            $productTypes[] = $row['ProductType'];
            $totalOrders[] = $row['TotalOrders'];
            $totalQuantities[] = $row['TotalQuantity'];
            $totalSales[] = $row['TotalSales'];
        }
    }
} else {
    echo "Error executing Product Type Stats query: " . $stmt->error;
}





// Fetch all requested products by this retailer
$requestedSQL = "SELECT * FROM retailer_wholesaler_order WHERE RetailerID = ?";
$stmt = $conn->prepare($requestedSQL);
$stmt->bind_param("i", $retailerID);
$stmt->execute();
$requestedResult = $stmt->get_result();
$requestedOrders = $requestedResult->fetch_all(MYSQLI_ASSOC);


// Fetch all customer orders for this retailer
$orderSQL = "SELECT o.OrderID, c.Name, o.OrderDate, o.OrderStatus,o.ProductType, o.OrderQuantity
             FROM customer_order o
             JOIN customer c ON o.CustomerID = c.CustomerID
             WHERE o.RetailerID = ?
             ORDER BY o.OrderDate DESC";
$stmt = $conn->prepare($orderSQL);

// Bind the retailer ID to the SQL query
$stmt->bind_param("i", $retailerID); // Ensure $retailerID is a single parameter of type "integer"
$stmt->execute();
$orderResult = $stmt->get_result();
$orders = $orderResult->fetch_all(MYSQLI_ASSOC);

// Fetch new (pending) orders for this retailer
$newOrderSQL = "SELECT o.OrderID, c.Name, o.OrderQuantity, o.PricePerUnit, o.DeliveryDate, o.ProductType
                FROM customer_order o
                JOIN customer c ON o.CustomerID = c.CustomerID
                WHERE o.RetailerID = ? AND o.OrderStatus = 'Pending'";
$stmt = $conn->prepare($newOrderSQL);

// Bind the retailer ID to the SQL query
$stmt->bind_param("i", $retailerID); // Ensure $retailerID is a single parameter of type "integer"
$stmt->execute();
$newOrderResult = $stmt->get_result();
$newOrders = $newOrderResult->fetch_all(MYSQLI_ASSOC);

// Fetch products and stock levels for this retailer
$inventorySQL = "SELECT rp.retailerID, rp.ProductType, rp.AvailableQuantity, rp.PricePerUnit
                 FROM retailer_product rp
                 WHERE rp.retailerID = ?";
$stmt = $conn->prepare($inventorySQL);

// Bind the retailer ID to the SQL query
$stmt->bind_param("i", $retailerID); // Ensure $retailerID is a single parameter of type "integer"
$stmt->execute();
$inventoryResult = $stmt->get_result();
$inventory = $inventoryResult->fetch_all(MYSQLI_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retailer Dashboard</title>
    <link rel="stylesheet" href="retailer.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <!-- Sidebar Navigation -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h2>Retailer Dashboard</h2>
        </div>
        <ul class="nav-links">
            <li><a href="#overview">Dashboard Overview</a></li>
            <li><a href="#sales">Sales Performance</a></li>
            <li><a href="#orders">Customer Orders</a></li>
            <li><a href="#new-orders">New Orders</a></li>
            <li><a href="#inventory">Inventory Management</a></li>
        </ul>
        <div class="sidebar-footer">
            <form action="index.html" method="POST">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>

    </nav>

    <!-- Main Content Area -->
    <div class="dashboard-content">
        <header>
            <h1><?php echo htmlspecialchars($retailerInfo['name']); ?> (<?php echo htmlspecialchars($retailerInfo['area']) . ', ' . htmlspecialchars($retailerInfo['city']); ?>)</h1>
            <h2>ID: <?php echo $retailerID ?></h2>
            <div class="dashboard-metrics">
                <div class="metric-box">
                    <h3>Total Orders</h3>
                    <p><?php echo $totalOrderss; ?></p>
                </div>
                <div class="metric-box">
                    <h3>New Orders</h3>
                    <p><?php echo $newOrdersCount; ?></p>
                </div>
                <div class="metric-box">
                    <h3>Total Sales</h3>
                    <p>$ <?php echo number_format($totalRevenue, 2); ?></p>
                </div>
            </div>


        </header>

        <!-- Graphs Container -->
        <section id="overview-sales" class="dashboard-section graphs-container">
            <!-- Order Status Graph -->
            <div class="graph-box">
                <h2>Order Status Overview</h2>
                <canvas id="orderStatusChart" width="400" height="400"></canvas>
            </div>

            <!-- Revenue Trend Graph -->
            <div class="graph-box">
                <h2>Monthly Revenue Trend</h2>
                <canvas id="revenueTrendChart" width="400" height="400"></canvas>
            </div>
            <div class="graph-box">
                <h2>Orders & Sales by Product Type</h2>
                <canvas id="productTypeGraph" width="400" height="400"></canvas>
            </div>

        </section>



        <!-- Customer Orders Section -->
        <section id="orders" class="dashboard-section">
            <h2>Customer Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Quantity (kg)</th>
                        <th>Order Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['OrderID']) ?></td>
                            <td><?= htmlspecialchars($order['Name']) ?></td>
                            <td><?= htmlspecialchars($order['ProductType']) ?></td>
                            <td><?= htmlspecialchars($order['OrderQuantity']) ?> kg</td>
                            <td><?= htmlspecialchars($order['OrderDate']) ?></td>
                            <td><?= htmlspecialchars($order['OrderStatus']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- New Orders Section -->
        <section id="new-orders" class="dashboard-section">
            <h2>New Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Expected Delivery</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($newOrders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['OrderID']) ?></td>
                            <td><?= htmlspecialchars($order['Name']) ?></td>
                            <td><?= htmlspecialchars($order['ProductType']) ?></td>
                            <td><?= htmlspecialchars($order['OrderQuantity']) ?></td>
                            <td><?= number_format($order['OrderQuantity'] * $order['PricePerUnit'], 2) ?></td>
                            <td><?= htmlspecialchars($order['DeliveryDate']) ?></td>
                            <td>
                                <form method="POST" action="retailerOrderAction.php" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['OrderID']) ?>">
                                    <button type="submit" name="action" value="Deliver">Deliver</button>
                                    <button type="submit" name="action" value="Reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Inventory Management Section -->
        <section id="inventory" class="dashboard-section">
            <h2>Inventory Management</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Stock Level</th>
                        <th>Price (Per kg)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inventory as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['ProductType']) ?></td>
                            <td><?= htmlspecialchars($product['AvailableQuantity']) ?> kg</td>
                            <td>$<?= number_format($product['PricePerUnit'], 2) ?></td>
                            <td>
                                <a href="retailerPriceUpdate.php?retailer_id=<?= urlencode($product['retailerID']) ?>&product_type=<?= urlencode($product['ProductType']) ?>">
                                    <button>Update Price</button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
            <div style="text-align: right; margin-top: 20px;">
                <a href="retailerProductRequest.php?retailer_id=<?= urlencode($retailerID) ?>" class="btn-request-product">Request Product</a>

            </div>
            <h2 style="margin-top: 40px;">Requested Products From Wholesalers</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Wholesaler ID</th>
                        <th>Product</th>
                        <th>Quantity (kg)</th>
                        <th>Price per kg</th>
                        <th>Total Price</th>
                        <th>Order Date</th>
                        <th>Delivery Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($requestedOrders)): ?>
                        <?php foreach ($requestedOrders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['OrderID']) ?></td>
                                <td><?= htmlspecialchars($order['WholesalerID']) ?></td>
                                <td><?= htmlspecialchars($order['ProductType']) ?></td>
                                <td><?= htmlspecialchars($order['OrderQuantity']) ?></td>
                                <td>$<?= number_format($order['PricePerUnit'], 2) ?></td>
                                <td>$<?= number_format($order['TotalPrice'], 2) ?></td>
                                <td><?= htmlspecialchars($order['OrderDate']) ?></td>
                                <td><?= htmlspecialchars($order['DeliveryDate']) ?></td>
                                <td><?= htmlspecialchars($order['OrderStatus'] ?? 'Pending') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">No product requests found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>




        </section>

    </div>

    <script src="retailer.js"></script>
</body>
<script>
    // --- Order Status Doughnut Chart ---
    const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
    const orderStatusChart = new Chart(orderStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Delivered', 'Rejected'],
            datasets: [{
                label: 'Order Status',
                data: [
                    <?php echo isset($statusData['Pending']) ? $statusData['Pending'] : 0; ?>,
                    <?php echo isset($statusData['Delivered']) ? $statusData['Delivered'] : 0; ?>,
                    <?php echo isset($statusData['Rejected']) ? $statusData['Rejected'] : 0; ?>
                ],
                backgroundColor: ['#f9c74f', '#90be6d', '#f94144'],
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // --- Revenue Line Chart ---
    const revenueTrendCtx = document.getElementById('revenueTrendChart').getContext('2d');
    const revenueTrendChart = new Chart(revenueTrendCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Revenue (৳)',
                data: <?php echo json_encode($revenues); ?>,
                fill: true,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<!-- First: Output PHP arrays into JS -->
<script>
    const productTypes = <?php echo json_encode($productTypes); ?>;
    const totalOrders = <?php echo json_encode($totalOrders); ?>;
    const totalQuantities = <?php echo json_encode($totalQuantities); ?>;
    const totalSales = <?php echo json_encode($totalSales); ?>;
</script>

<!-- Then: your chart rendering code -->
<script>
    const productTypeCtx = document.getElementById('productTypeGraph').getContext('2d');
    const productTypeGraph = new Chart(productTypeCtx, {
        type: 'bar',
        data: {
            labels: productTypes,
            datasets: [{
                    label: 'Total Orders',
                    data: totalOrders,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    yAxisID: 'y1',
                },
                {
                    label: 'Total Quantity(kg)',
                    data: totalQuantities,
                    backgroundColor: 'rgba(255, 205, 86, 0.7)',
                    yAxisID: 'y1',
                },
                {
                    label: 'Total Sales (Tk)',
                    data: totalSales,
                    backgroundColor: 'rgba(153, 102, 255, 0.7)',
                    yAxisID: 'y2',
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y1: {
                    type: 'linear',
                    position: 'left',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Orders / Quantity'
                    }
                },
                y2: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Sales (Tk)'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    });
</script>




</html>