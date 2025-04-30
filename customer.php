<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

$customerID = $_SESSION['user_id'];

// Fetch customer info
$sql_customer = "SELECT CustomerID, Name, Contact, Area, City FROM customer WHERE CustomerID = $customerID";
$result_customer = $conn->query($sql_customer);
$customer = $result_customer->fetch_assoc();

// Fetch customer orders
$sql_orders = "SELECT OrderID, RetailerID, ProductType, OrderQuantity, PricePerUnit, OrderDate, DeliveryDate, OrderStatus 
               FROM customer_order WHERE CustomerID = $customerID";
$result_orders = $conn->query($sql_orders);

// Fetch retailer product catalog
$sql_catalog = "SELECT 
                    r.RetailerID, 
                    r.Name AS RetailerName, 
                    r.MinimumDeliveryDays,
                    rp.ProductType, 
                    rp.PricePerUnit, 
                    rp.AvailableQuantity 
                FROM retailer_product rp
                JOIN retailer r ON rp.retailerID = r.RetailerID
                ORDER BY r.RetailerID";
$result_catalog = $conn->query($sql_catalog);
$retailers = [];
while ($row = $result_catalog->fetch_assoc()) {
    $retailerId = $row['RetailerID'];
    $RetailerName = $row['RetailerName'];
    if (!isset($retailers[$retailerId])) {
        $retailers[$retailerId] = [
            'RetailerName' => $row['RetailerName'],
            'MinimumDeliveryDays' => $row['MinimumDeliveryDays'],
            'Products' => []
        ];
    }
    $retailers[$retailerId]['Products'][] = [
        'ProductType' => $row['ProductType'],
        'PricePerUnit' => $row['PricePerUnit'],
        'AvailableQuantity' => $row['AvailableQuantity']
    ];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="customer.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div>
            <div class="sidebar-header">
                <h2>Customer Dashboard</h2>
            </div>
            <ul class="nav-links">
                <li><a href="#overview">Dashboard Overview</a></li>
                <li><a href="#catalog">Product Catalog</a></li>
                <li><a href="#order-history">Order History</a></li>
                <li><a href="#pricing">Pricing & Offers</a></li>
                <li><a href="#delivery">Delivery Tracking</a></li>
                <li><a href="#account">Account Management</a></li>
            </ul>
        </div>
        <a href="index.html" class="logout-button">Logout</a>
    </nav>

    <!-- Main Content -->
    <div class="dashboard-content">
        <div class="header-bar">
            <h1>Welcome, <?php echo $customer['Name']; ?>!</h1>
        </div>

        <!-- Sections -->
        <section id="overview" class="dashboard-section">
            <h2>Dashboard Overview</h2>
            <p>View a summary of your orders, current deliveries, and account details.</p>
            <canvas id="overviewGraph"></canvas>
        </section>

        <section id="catalog" class="dashboard-section">
            <h2>Product Catalog</h2>

            <?php if (!empty($retailers)): ?>
                <?php foreach ($retailers as $retailerID => $data): ?>
                    <div class="mb-6">
                        <h3 class="mt-6 text-lg font-semibold text-red-700">
                            <a href="retailerdetails.php?retailer_id=<?= $retailerID ?>" class="hover:underline">
                                <?= htmlspecialchars($data['RetailerName']) ?>
                            </a>
                        </h3>
                        <p><strong>Minimum Delivery Days:</strong> <?= $data['MinimumDeliveryDays'] ?> day(s)</p>
                        <table class="mt-2 w-full border border-gray-300">
                            <thead class="bg-red-100">
                                <tr>
                                    <th class="p-2 border">Product Type</th>
                                    <th class="p-2 border">Price Per Unit</th>
                                    <th class="p-2 border">Available Quantity</th>
                                    <th class="p-2 border">Order</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['Products'] as $product): ?>
                                    <tr class="text-center hover:bg-red-50">
                                        <td class="p-2 border"><?= $product['ProductType'] ?></td>
                                        <td class="p-2 border">$<?= $product['PricePerUnit'] ?></td>
                                        <td class="p-2 border"><?= $product['AvailableQuantity'] ?> kg</td>
                                        <td class="p-2 border">
                                            <form action="customerOrder.php" method="GET">
                                                <input type="hidden" name="customerID" value="<?= $customerID ?>">
                                                <input type="hidden" name="retailer_id" value="<?= $retailerID ?>">
                                                <input type="hidden" name="retailer_name" value="<?= $RetailerName ?>">
                                                <input type="hidden" name="product_type" value="<?= $product['ProductType'] ?>">
                                                <input type="hidden" name="unit_price" value="<?= $product['PricePerUnit'] ?>">
                                                <input type="hidden" name="min_days" value="<?= $data['MinimumDeliveryDays'] ?>">
                                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Order</button>
                                            </form>

                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No products available from retailers at this time.</p>
            <?php endif; ?>
        </section>


        <section id="order-history" class="dashboard-section">
            <h3>Order History</h3>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Retailer ID</th>
                        <th>Quantity</th>
                        <th>Price/Unit</th>
                        <th>Order Date</th>
                        <th>Delivery Date</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($result_orders->num_rows > 0): ?>
                        <?php while ($order = $result_orders->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $order['OrderID']; ?></td>
                                <td><?php echo $order['RetailerID']; ?></td>
                                <td><?php echo $order['OrderQuantity']; ?></td>
                                <td><?php echo $order['PricePerUnit']; ?></td>
                                <td><?php echo $order['OrderDate']; ?></td>
                                <td><?php echo $order['DeliveryDate']; ?></td>
                                <td><?php echo $order['OrderStatus']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <section id="pricing" class="dashboard-section">
            <h2>Pricing & Offers</h2>
            <p>View current product prices and any ongoing discounts or offers.</p>
            <ul>
                <li>Beef - $5 per kg (10% off)</li>
                <li>Chicken - $3 per kg (5% off)</li>
            </ul>
        </section>

        <section id="account" class="dashboard-section">
            <h2>Account Management</h2>
            <p>Manage your personal details, payment methods, and preferences.</p>
            <button>Edit Profile</button>
        </section>
    </div>

    <script src="customer.js"></script>
</body>

</html>