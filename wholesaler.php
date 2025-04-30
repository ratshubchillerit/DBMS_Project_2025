<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}


$wholesalerID = $_SESSION['user_id'];
// Query to get total quantity by product type
$query = "SELECT ProductType, SUM(OrderQuantity) AS total_quantity FROM retailer_wholesaler_order GROUP BY ProductType";
$result = $conn->query($query);
$totalOrders = [];
while ($row = $result->fetch_assoc()) {
    $totalOrders[$row['ProductType']] = $row['total_quantity'];
}

// Query to get total sales by product type (totalPrice is calculated as OrderQuantity * PricePerUnit)
$query_sales = "SELECT ProductType, SUM(OrderQuantity * PricePerUnit) AS total_sales FROM retailer_wholesaler_order GROUP BY ProductType";
$result_sales = $conn->query($query_sales);
$totalSales = [];
while ($row = $result_sales->fetch_assoc()) {
    $totalSales[$row['ProductType']] = $row['total_sales'];
}

// Query to get order status distribution
$query_status = "SELECT OrderStatus, COUNT(*) AS status_count FROM retailer_wholesaler_order GROUP BY OrderStatus";
$result_status = $conn->query($query_status);
$orderStatus = [];
while ($row = $result_status->fetch_assoc()) {
    $orderStatus[$row['OrderStatus']] = $row['status_count'];
}

// Query to get order volume by date
$query_volume = "SELECT OrderDate, COUNT(*) AS volume_count FROM retailer_wholesaler_order GROUP BY OrderDate ORDER BY OrderDate";
$result_volume = $conn->query($query_volume);
$orderVolume = [];
$orderDates = [];
while ($row = $result_volume->fetch_assoc()) {
    $orderDates[] = $row['OrderDate'];
    $orderVolume[] = $row['volume_count'];
}


// Fetch wholesaler info
$wholesalerQuery = "SELECT name, contact,area,city FROM wholesaler WHERE WholesalerID = ?";
$stmt = $conn->prepare($wholesalerQuery);
$stmt->bind_param("i", $wholesalerID);
$stmt->execute();
$result = $stmt->get_result();
$wholesalerInfo = $result->fetch_assoc();


$query = "SELECT ProductType, AvailableQuantity, MinimumOrderQty, PricePerUnit 
          FROM wholesaler_product 
          WHERE wholesalerID = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $wholesalerID);
$stmt->execute();
$result = $stmt->get_result();
$inventoryData = $result->fetch_all(MYSQLI_ASSOC);


$query = "SELECT 
            rwo.OrderID,
            r.name,
            rwo.ProductType,
            rwo.OrderQuantity,
            rwo.OrderDate,
            rwo.DeliveryDate,
            rwo.OrderStatus,
            rwo.RetailerID,
            wp.AvailableQuantity
          FROM retailer_wholesaler_order rwo
          JOIN retailer r ON rwo.RetailerID = r.RetailerID
          JOIN wholesaler_product wp 
            ON rwo.WholesalerID = wp.wholesalerID 
            AND rwo.ProductType = wp.ProductType
          WHERE rwo.WholesalerID = ?
          ORDER BY rwo.OrderDate DESC";


$stmt = $conn->prepare($query);
$stmt->bind_param("i", $wholesalerID);
$stmt->execute();
$result = $stmt->get_result();
$retailerOrders = $result->fetch_all(MYSQLI_ASSOC);

$inventoryData = [];

$sql = "SELECT ProductType, AvailableQuantity, MinimumOrderQty, PricePerUnit 
        FROM wholesaler_product 
        WHERE wholesalerID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $wholesalerID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $inventoryData[] = $row;
}

$stmt->close();


?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wholesaler Dashboard</title>
    <link rel="stylesheet" href="wholesaler.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- For Graphs -->
</head>

<body>
    <!-- Sidebar Navigation -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h2>Wholesaler Dashboard</h2>
        </div>
        <ul class="nav-links">
            <li><a href="#overview">Dashboard Overview</a></li>
            <li><a href="#sales">Sales Performance</a></li>
            <li><a href="#inventory">Inventory Management</a></li>
            <li><a href="#orders">Customer Orders</a></li>
            <li><a href="#pricing">Pricing Trends</a></li>
            <li><a href="#supply-chain">Supply Chain</a></li>
            <li><a href="#reports">Reports</a></li>
        </ul>
        <div class="logout-container">
            <form action="index.html" method="post">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>

    </nav>

    <!-- Main Content Area -->
    <div class="dashboard-content">
        <header class="dashboard-header">
            <div class="wholesaler-info">
                <h1>Welcome, <?= htmlspecialchars($wholesalerInfo['name']) ?></h1>
                <p><strong>ID:</strong> <?= $wholesalerID ?></p>
                <p><strong>Area:</strong> <?= htmlspecialchars($wholesalerInfo['area']) ?></p>
                <p><strong>City:</strong> <?= htmlspecialchars($wholesalerInfo['city']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($wholesalerInfo['contact']) ?></p>
            </div>
        </header>


        <!-- Dashboard Section Container for Side-by-Side Layout -->
        <div class="dashboard-section-container">
            <!-- Total Orders by Product Type Section -->
            <section id="totalOrders" class="dashboard-section">
                <h2>Total Orders by Product Type</h2>
                <canvas id="totalOrdersGraph"></canvas>
                <p>Total quantity of each product ordered by retailers.</p>
            </section>


            <!-- Total Sales Value by Product Type Section -->
            <section id="totalSales" class="dashboard-section">
                <h2>Total Sales Value by Product Type</h2>
                <canvas id="totalSalesGraph"></canvas>
                <p>Total sales value generated by each product.</p>
            </section>
            <!-- Order Status Distribution Section -->
            <section id="orderStatus" class="dashboard-section">
                <h2>Order Status Distribution</h2>
                <canvas id="orderStatusGraph"></canvas>
                <p>Distribution of order statuses (Delivered, Shipped, etc.).</p>
            </section>
            <!-- Order Volume Over Time Section -->
            <section id="orderVolume" class="dashboard-section">
                <h2>Order Volume Over Time</h2>
                <canvas id="orderVolumeGraph"></canvas>
                <p>Track the volume of orders placed over time.</p>
            </section>



        </div>


        <!-- Inventory Management Section -->
        <section id="inventory" class="dashboard-section">
            <h2>Inventory Management</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Stock Level (kg)</th>
                        <th>Reorder Level (Min Order Qty)</th>
                        <th>Price Per Unit (৳)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inventoryData as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['ProductType']) ?></td>
                            <td><?= htmlspecialchars($item['AvailableQuantity']) ?></td>
                            <td><?= htmlspecialchars($item['MinimumOrderQty']) ?></td>
                            <td><?= htmlspecialchars($item['PricePerUnit']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </section>


        <!-- Customer Orders Section -->
        <section id="orders" class="dashboard-section">
            <h2>Retailer Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Retailer</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Order Date</th>
                        <th>Delivery Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($retailerOrders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['OrderID']) ?></td>
                            <td><?= htmlspecialchars($order['name']) ?></td>
                            <td><?= htmlspecialchars($order['ProductType']) ?></td>
                            <td><?= htmlspecialchars($order['OrderQuantity']) ?> kg</td>
                            <td><?= htmlspecialchars($order['OrderDate']) ?></td>
                            <td><?= htmlspecialchars($order['DeliveryDate']) ?></td>
                            <td><?= htmlspecialchars($order['OrderStatus']) ?></td>
                            <td>
                                <?php
                                $canDeliver = ($order['AvailableQuantity'] >= $order['OrderQuantity']);
                                $isDelivered = ($order['OrderStatus'] === 'Delivered');

                                $disableDeliver = !$canDeliver || $isDelivered;
                                $disableReject = $isDelivered;
                                ?>

                                <form method="post" action="wholesalerAcceptOrder.php" style="display:inline;">
                                    <input type="hidden" name="retailerID" value="<?= $order['RetailerID'] ?>">
                                    <input type="hidden" name="orderID" value="<?= $order['OrderID'] ?>">
                                    <input type="hidden" name="product" value="<?= $order['ProductType'] ?>">
                                    <input type="hidden" name="quantity" value="<?= $order['OrderQuantity'] ?>">
                                    <input type="hidden" name="action" value="deliver">
                                    <button type="submit" class="deliver-btn" <?= $disableDeliver ? 'disabled' : '' ?>>Deliver</button>
                                </form>


                                <form method="post" action="wholesalerRejectOrder.php" style="display:inline;">
                                    <input type="hidden" name="orderID" value="<?= $order['OrderID'] ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="reject-btn" <?= $disableReject ? 'disabled' : '' ?>>Reject</button>
                                </form>
                            </td>


                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>




        <!-- Supply Chain Section -->
        <section id="supply-chain" class="dashboard-section">
            <h2>Supply Chain</h2>
            <p>Monitor incoming shipments and deliveries.</p>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Shipment Date</th>
                        <th>Quantity</th>
                        <th>Delivery Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Beef</td>
                        <td>Jan 18, 2025</td>
                        <td>1000 kg</td>
                        <td>Delivered</td>
                    </tr>
                    <tr>
                        <td>Pork</td>
                        <td>Jan 19, 2025</td>
                        <td>500 kg</td>
                        <td>In Transit</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Reports Section -->
        <section id="reports" class="dashboard-section">
            <h2>Reports</h2>
            <p>Generate sales, inventory, and order reports.</p>
            <button class="generate-report-btn">Generate Report</button>
        </section>
    </div>

    <script src="wholesaler.js"></script>
    <script>
        const inventoryData = <?= json_encode($inventoryData) ?>;
    </script>
    <script>
        // Get the PHP data into JavaScript
        const totalOrdersData = <?php echo json_encode($totalOrders); ?>;
        const totalSalesData = <?php echo json_encode($totalSales); ?>;
        const orderStatusData = <?php echo json_encode($orderStatus); ?>;
        const orderVolumeData = <?php echo json_encode($orderVolume); ?>;
        const orderDates = <?php echo json_encode($orderDates); ?>;

        // Total Orders by Product Type (Bar Chart)
        const totalOrdersGraph = document.getElementById('totalOrdersGraph').getContext('2d');
        const totalOrdersChart = new Chart(totalOrdersGraph, {
            type: 'bar',
            data: {
                labels: Object.keys(totalOrdersData),
                datasets: [{
                    label: 'Total Orders',
                    data: Object.values(totalOrdersData),
                    backgroundColor: 'rgba(26, 188, 156, 0.6)',
                    borderColor: 'rgba(26, 188, 156, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Total Sales Value by Product Type (Bar Chart)
        const totalSalesGraph = document.getElementById('totalSalesGraph').getContext('2d');
        const totalSalesChart = new Chart(totalSalesGraph, {
            type: 'bar',
            data: {
                labels: Object.keys(totalSalesData),
                datasets: [{
                    label: 'Total Sales Value',
                    data: Object.values(totalSalesData),
                    backgroundColor: 'rgba(46, 204, 113, 0.6)',
                    borderColor: 'rgba(46, 204, 113, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Order Status Distribution (Pie Chart)
        const orderStatusGraph = document.getElementById('orderStatusGraph').getContext('2d');
        const orderStatusChart = new Chart(orderStatusGraph, {
            type: 'pie',
            data: {
                labels: Object.keys(orderStatusData),
                datasets: [{
                    label: 'Order Status Distribution',
                    data: Object.values(orderStatusData),
                    backgroundColor: ['rgba(52, 152, 219, 0.6)', 'rgba(231, 76, 60, 0.6)'],
                    borderColor: ['rgba(52, 152, 219, 1)', 'rgba(231, 76, 60, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                }
            }
        });

        // Order Volume Over Time (Line Chart)
        const orderVolumeGraph = document.getElementById('orderVolumeGraph').getContext('2d');
        const orderVolumeChart = new Chart(orderVolumeGraph, {
            type: 'line',
            data: {
                labels: orderDates, // Dates on the X axis
                datasets: [{
                    label: 'Order Volume',
                    data: orderVolumeData,
                    borderColor: 'rgba(41, 128, 185, 1)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>


</body>

</html>