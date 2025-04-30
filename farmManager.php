<?php
session_start();
include "db.php";


if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

$farmManagerID = $_SESSION['user_id'];

$query = "SELECT * FROM farm WHERE FarmID='$farmManagerID'";
$result = mysqli_query($conn, $query);
$row = $result->fetch_assoc();
$farmName = $row['FarmName'];



// Get total orders (demand)
$sql_total_orders = "SELECT SUM(OrderQuantity) AS total_orders FROM customer_order";
$result_orders = $conn->query($sql_total_orders);
$row_orders = $result_orders->fetch_assoc();
$totalOrders = (int)$row_orders['total_orders'];

// Get total inventory (supply)
$sql_total_inventory = "SELECT SUM(Quantity) AS total_inventory FROM meat_product";
$result_inventory = $conn->query($sql_total_inventory);
$row_inventory = $result_inventory->fetch_assoc();
$totalInventory = (int)$row_inventory['total_inventory'];

//livestock data
$livestockSql = "SELECT * FROM livestock WHERE FarmID='$farmManagerID'";
$result_livestock = $conn->query($livestockSql);

// Fetch farmers with their respective farm info
$sql_farmers = "SELECT
farmer.FarmerID,
farmer.FarmerName,
farmer.Area AS FarmerArea,
farmer.City AS FarmerCity,
farm.FarmName,
farm.Area AS FarmArea,
farm.City AS FarmCity,
farm.ContactNumber
FROM farmer
LEFT JOIN farm ON farmer.FarmID = farm.FarmID WHERE farm.FarmID='$farmManagerID'";
$result_farmers = $conn->query($sql_farmers);


// Fetch meat products with their batch info
$sql_meat_products = "
SELECT
meat_product.ProductID,
meat_product.Quantity,
meat_product.Price,
meat_product.BatchID,
production_batch.ProductionDate,
livestock.Type AS MeatType,
livestock.FarmID AS ID
FROM meat_product
LEFT JOIN processing ON meat_product.ProductID = processing.ProductID
LEFT JOIN livestock ON processing.LivestockID = livestock.LivestockID
LEFT JOIN production_batch ON meat_product.BatchID = production_batch.BatchID
WHERE livestock.FarmID='$farmManagerID'
";
$result_meat_products = $conn->query($sql_meat_products);


// Fetch production batches with summarized meat product data
$sql_batches = "
SELECT
    pb.BatchID,
    pb.ProductionDate,
    pb.ExpiryDate,
    pb.ProductionCost,
    SUM(mp.Quantity) AS TotalQuantity,
    COUNT(DISTINCT mp.ProductID) AS ProductCount,
    livestock.Type AS MeatType,
    livestock.FarmID
FROM production_batch pb
LEFT JOIN meat_product mp ON pb.BatchID = mp.BatchID
LEFT JOIN processing ON mp.ProductID = processing.ProductID
LEFT JOIN livestock ON processing.LivestockID = livestock.LivestockID
WHERE livestock.FarmID = '$farmManagerID'
GROUP BY pb.BatchID, pb.ProductionDate, pb.ExpiryDate, pb.ProductionCost, livestock.Type, livestock.FarmID
";
$result_batches = $conn->query($sql_batches);

// Fetch meat products grouped by batch (for details modal/section)
$sql_products_by_batch = "
SELECT
mp.ProductID,
mp.BatchID,
mp.Quantity,
mp.Price,
l.Type AS MeatType
FROM meat_product mp
JOIN processing p ON mp.ProductID = p.ProductID
JOIN livestock l ON p.LivestockID = l.LivestockID
ORDER BY mp.BatchID
";
$result_products_by_batch = $conn->query($sql_products_by_batch);

// Organize meat products
$productsByBatch = [];
if ($result_products_by_batch->num_rows > 0) {
    while ($row = $result_products_by_batch->fetch_assoc()) {
        $productsByBatch[$row['BatchID']][] = $row;
    }
}
// Fetch transfers with animal type
$sql_transfers = "
SELECT
p.LivestockID,
l.Type AS Species,
p.SlaughterDate
FROM processing p
JOIN livestock l ON p.LivestockID = l.LivestockID
ORDER BY p.SlaughterDate ASC;
";
$result_transfers = $conn->query($sql_transfers);
//product chart
$batches = [];
$sql = "SELECT BatchID, BatchQuantity FROM production_batch ORDER BY BatchID";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $batches[] = $row;
    }
}

// Monthly Supply (Production)
$sql_supply = "
SELECT DATE_FORMAT(ProductionDate, '%M %Y') AS MonthYear, SUM(BatchQuantity) AS TotalSupply
FROM production_batch
GROUP BY MonthYear
ORDER BY MIN(ProductionDate);
";
$result_supply = $conn->query($sql_supply);
$supplyLabels = [];
$supplyData = [];
while ($row = $result_supply->fetch_assoc()) {
    $supplyLabels[] = $row['MonthYear'];
    $supplyData[] = $row['TotalSupply'];
}

// Monthly Demand (Customer Orders)
$sql_demand = "
SELECT DATE_FORMAT(OrderDate, '%M %Y') AS MonthYear, SUM(OrderQuantity) AS TotalDemand
FROM customer_order
GROUP BY MonthYear
ORDER BY MIN(OrderDate);
";
$result_demand = $conn->query($sql_demand);
$demandMap = [];
while ($row = $result_demand->fetch_assoc()) {
    $demandMap[$row['MonthYear']] = $row['TotalDemand'];
}

// Match demand to supply label months
$demandData = [];
foreach ($supplyLabels as $month) {
    $demandData[] = $demandMap[$month] ?? 0;
}

$conn->close();


?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Manager Dashboard</title>
    <link rel="stylesheet" href="farmManager.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <!-- Sidebar Navigation -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h2>Farm Manager</h2>
        </div>
        <ul class="nav-links">
            <li><a href="#overview">Dashboard Overview</a></li>
            <li><a href="#livestock">Livestock Management</a></li>
            <li><a href="#production">Production Data</a></li>
            <li><a href="#supply-demand">Supply and Demand</a></li>
            <li><a href="#farmers">Farm Workers</a></li>
            <li><a href="#recommendations">Recommendations</a></li>
            <li><a href="#analytics">Reports & Analytics</a></li>
            <li><a href="#settings">Settings</a></li>
        </ul>
    </nav>

    <!-- Main Dashboard Content -->
    <div class="dashboard-content">
        <header>
            <h1>Farm Manager Dashboard</h1>
            <h2>Welcome, <?php echo $farmName; ?></h>
                <h2>ID:<span><?php echo $farmManagerID; ?></span></h2>
        </header>

        <section id="overview" class="dashboard-section">
            <h2>Dashboard Overview</h2>
            <p>High-level metrics: livestock count, production, and supply status.</p>
            <canvas id="overviewChart" width="400" height="400"></canvas>
        </section>

        <section id="livestock" class="dashboard-section">
            <h2>Livestock Management</h2>
            <table>
                <thead>
                    <tr>
                        <th>Livestock ID</th>
                        <th>Type</th>
                        <th>Color</th>
                        <th>Weight (kg)</th>
                        <th>Birthdate</th>
                        <th>Age</th>
                        <th>Vaccination Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_livestock->num_rows > 0) {
                        while ($row = $result_livestock->fetch_assoc()) {
                            echo "<tr>
                        <td>{$row['LivestockID']}</td>
                        <td>{$row['Type']}</td>
                        <td>{$row['Color']}</td>
                        <td>{$row['Weight']}</td>
                        <td>{$row['Birthdate']}</td>
                        <td>{$row['Age']}</td>
                        <td>{$row['VaccinationStatus']}</td>
                    </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No livestock data found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <!-- Add Livestock Button -->
            <a href="addLivestock.php?farm_id=<?php echo $farmManagerID ?>" class="add-livestock-btn">+ Add Livestock</a>


        </section>

        <section id="production" class="dashboard-section">
            <h2>Production Data</h2>
            <canvas id="productionGraph" width="500" height="500"></canvas>
            <p>Overview of total yield and costs in the past 6 months.</p>
        </section>

        <section id="supply-demand" class="dashboard-section">
            <h2>Supply and Demand Insights</h2>
            <canvas id="supplyDemandGraph" width="500" height="500"></canvas>
            <p>Trends in demand and supply with forecasts.</p>
        </section>

        <!-- NEW SECTION -->
        <section id="farmers" class="dashboard-section">
            <h2>Farm Workers</h2>
            <table>
                <thead>
                    <tr>
                        <th>Farmer ID</th>
                        <th>Name</th>
                        <th>Area</th>
                        <th>City</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_farmers->num_rows > 0) {
                        while ($row = $result_farmers->fetch_assoc()) {
                            echo "<tr>
                        <td>{$row['FarmerID']}</td>
                        <td>{$row['FarmerName']}</td>
                        <td>{$row['FarmerArea']}</td>
                        <td>{$row['FarmerCity']}</td>

                    </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No farm workers found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

        </section>
        <!-- Slaughterhouse Transfers Section -->
        <section>
            <h2>Slaughterhouse Transfers</h2>
            <p>Records of livestock transferred for slaughtering.</p>
            <table>
                <thead>
                    <tr>
                        <th>Transfer ID</th>
                        <th>Animal ID</th>
                        <th>Species</th>
                        <th>Transfer Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = 1;
                    if ($result_transfers->num_rows > 0) {
                        while ($row = $result_transfers->fetch_assoc()) {
                            $transferID = 'TR' . str_pad($counter++, 3, '0', STR_PAD_LEFT);
                            echo "<tr>
                        <td>{$transferID}</td>
                        <td>{$row['LivestockID']}</td>
                        <td>{$row['Species']}</td>
                        <td>{$row['SlaughterDate']}</td>
                    </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No transfer records found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>


        <!-- Meat Production Section -->
        <section id="meat-production" class="dashboard-section">
            <h2>Meat Production</h2>
            <p>Overview of meat products generated from livestock, linked to their batch.</p>
            <table>
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Meat Type</th>
                        <th>Quantity (kg)</th>
                        <th>Price (per kg)</th>
                        <th>Batch ID</th>
                        <th>Production Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_meat_products->num_rows > 0) {
                        while ($row = $result_meat_products->fetch_assoc()) {
                            echo "<tr>
                        <td>{$row['ProductID']}</td>
                        <td>{$row['MeatType']}</td> <!-- Meat Type Column -->
                        <td>{$row['Quantity']}</td>
                        <td>৳{$row['Price']}</td>
                        <td>{$row['BatchID']}</td>
                        <td>{$row['ProductionDate']}</td>
                    </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No meat product data found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>



        <!-- Production Batch Section -->
        <section id="production-batch" class="dashboard-section">
            <h2>Production Batches</h2>
            <p>Grouped meat production batches with key tracking details.</p>
            <table>
                <thead>
                    <tr>
                        <th>Batch ID</th>
                        <th>Production Date</th>
                        <th>Expiry Date</th>
                        <th>Total Products</th>
                        <th>Total Quantity (kg)</th>
                        <th>Production Cost</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_batches->num_rows > 0) {
                        while ($batch = $result_batches->fetch_assoc()) {
                            echo "<tr>
                        <td>{$batch['BatchID']}</td>
                        <td>{$batch['ProductionDate']}</td>
                        <td>{$batch['ExpiryDate']}</td>
                        <td>{$batch['ProductCount']}</td>
                        <td>{$batch['TotalQuantity']}</td>
                        <td>৳{$batch['ProductionCost']}</td>
                        <td><button onclick=\"toggleBatchDetails({$batch['BatchID']})\">View Details</button></td>
                    </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </section>
        <section id="batch-details" class="dashboard-section">
            <h2>Batch Product Details</h2>
            <?php foreach ($productsByBatch as $batchID => $products): ?>
                <div class="batch-detail-group" id="batch-<?= $batchID ?>" style="display:none;">
                    <h3>Batch ID: <?= $batchID ?></h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Product ID</th>
                                <th>Meat Type</th>
                                <th>Quantity (kg)</th>
                                <th>Price (৳)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $p): ?>
                                <tr>
                                    <td><?= $p['ProductID'] ?></td>
                                    <td><?= $p['MeatType'] ?></td>
                                    <td><?= $p['Quantity'] ?></td>
                                    <td><?= $p['Price'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        </section>

    </div>

    <script src="farmManager2.js"></script>
    <script>
        function toggleBatchDetails(batchID) {
            // Hide all
            document.querySelectorAll('.batch-detail-group').forEach(el => el.style.display = 'none');
            // Show selected
            const section = document.getElementById('batch-' + batchID);
            if (section) {
                section.style.display = 'block';
                section.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        }
        const overviewData = {
            labels: ['Orders', 'Inventory'],
            datasets: [{
                label: 'Overview',
                data: [<?= $totalOrders ?>, <?= $totalInventory ?>],
                backgroundColor: ['rgba(75, 192, 192, 0.5)', 'rgba(255, 159, 64, 0.5)'],
                borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 159, 64, 1)'],
                borderWidth: 1
            }]
        };

        const overviewChart = new Chart(document.getElementById('overviewChart'), {
            type: 'doughnut',
            data: overviewData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        //production chart
        const productionGraph = new Chart(document.getElementById('productionGraph'), {
            type: 'bar',
            data: {
                labels: [<?php foreach ($batches as $batch) echo "'Batch " . $batch['BatchID'] . "',"; ?>],
                datasets: [{
                    label: 'Batch Quantity (kg)',
                    data: [<?php foreach ($batches as $batch) echo $batch['BatchQuantity'] . ","; ?>],
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Production Quantities per Batch'
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Quantity (kg)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Production Batches'
                        }
                    }
                }
            }
        });
        //demand and supply
        const supplyLabels = <?php echo json_encode($supplyLabels); ?>;
        const supplyData = <?php echo json_encode($supplyData); ?>;
        const demandData = <?php echo json_encode($demandData); ?>;

        const supplyDemandGraph = new Chart(document.getElementById('supplyDemandGraph'), {
            type: 'line',
            data: {
                labels: supplyLabels,
                datasets: [{
                        label: 'Supply (kg)',
                        data: supplyData,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Demand (kg)',
                        data: demandData,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Monthly Supply vs Demand (kg)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>

</html>