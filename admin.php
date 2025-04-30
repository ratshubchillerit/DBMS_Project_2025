<?php
include "db.php";
$sql = "SELECT COUNT(*) AS TotalCustomers FROM customer";
$result = $conn->query($sql);

// Check if the query was successful
if ($result->num_rows > 0) {
  // Fetch the result
  $row = $result->fetch_assoc();
  $totalCustomers = $row['TotalCustomers'];
} else {
  $totalCustomers = 0;
}

// Fetch total sales
$sql_sales = "SELECT SUM(OrderQuantity * PricePerUnit) AS TotalSales FROM customer_order";
$result_sales = $conn->query($sql_sales);
$totalSales = 0;
if ($result_sales->num_rows > 0) {
  $row = $result_sales->fetch_assoc();
  $totalSales = $row['TotalSales'];
}
// Query to fetch total customer orders
$sql_customer_orders = "SELECT COUNT(*) AS TotalCustomerOrders FROM customer_order";
$result_customer_orders = $conn->query($sql_customer_orders);
$totalCustomerOrders = 0;
if ($result_customer_orders->num_rows > 0) {
  $row = $result_customer_orders->fetch_assoc();
  $totalCustomerOrders = $row['TotalCustomerOrders'];
}



// Fetch total livestock
$sql_orders = "SELECT COUNT(LivestockID) AS TotalLivestock FROM livestock";
$result_orders = $conn->query($sql_orders);
$totalOrders = 0;
if ($result_orders->num_rows > 0) {
  $row = $result_orders->fetch_assoc();
  $totalOrders = $row['TotalLivestock'];
}

// Fetch total vendors (wholesalers + retailers)
$sql_vendors = "SELECT (SELECT COUNT(*) FROM wholesaler) + (SELECT COUNT(*) FROM retailer) AS TotalVendors";
$result_vendors = $conn->query($sql_vendors);
$totalVendors = 0;
if ($result_vendors->num_rows > 0) {
  $row = $result_vendors->fetch_assoc();
  $totalVendors = $row['TotalVendors'];

}
$trendSQL = "SELECT ProductType, 
                    MAX(PricePerUnit) AS MaxPrice, 
                    MIN(PricePerUnit) AS MinPrice, 
                    ROUND(AVG(PricePerUnit), 2) AS AvgPrice 
             FROM retailer_product 
             GROUP BY ProductType";

$trendResult = $conn->query($trendSQL);

$productTypes = [];
$maxPrices = [];
$minPrices = [];
$avgPrices = [];

if ($trendResult->num_rows > 0) {
    while($row = $trendResult->fetch_assoc()) {
        $productTypes[] = $row['ProductType'];
        $maxPrices[] = (int)$row['MaxPrice'];
        $minPrices[] = (int)$row['MinPrice'];
        $avgPrices[] = (float)$row['AvgPrice'];
    }
}


// Query to get combined demand and supply data
$sql = "
    SELECT 
        demand.ProductType,
        IFNULL(demand.total_demand, 0) AS total_demand,
        IFNULL(supply.total_supply, 0) AS total_supply
    FROM 
        (SELECT 
            ProductType,
            SUM(OrderQuantity) AS total_demand
        FROM 
            customer_order
        GROUP BY 
            ProductType) AS demand
    LEFT JOIN 
        (SELECT 
            ProductType,
            SUM(AvailableQuantity) AS total_supply
        FROM 
            wholesaler_product
        GROUP BY 
            ProductType) AS supply
    ON demand.ProductType = supply.ProductType;
";

$result = $conn->query($sql);

// Prepare data for Chart.js
$product_names = [];
$demand_values = [];
$supply_values = [];

while ($row = $result->fetch_assoc()) {
  $product_names[] = $row['ProductType'];
  $demand_values[] = $row['total_demand'];
  $supply_values[] = $row['total_supply'];
}


// Fetch livestock data
$sql_livestock = "SELECT * FROM livestock";
$result_livestock = $conn->query($sql_livestock);

$sql = "SELECT * FROM meat_product";
$result_meat = $conn->query($sql);

// Fetch customer orders
$sql_orders = "SELECT * FROM customer_order";
$result_orders = $conn->query($sql_orders);
// Fetch recommendation records
$sqlrec = "SELECT * FROM recommendation";
$resultrec = $conn->query($sqlrec);

// Pagination settings
$ordersPerPage = 5; // You can change this to show more/less per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $ordersPerPage;

// Count total orders
$countResult = $conn->query("SELECT COUNT(*) AS total FROM customer_order");
$rowCount = $countResult->fetch_assoc();
$totalOrders = $rowCount['total'];
$totalPages = ceil($totalOrders / $ordersPerPage);

// Get orders with LIMIT
$sql = "SELECT * FROM customer_order LIMIT $offset, $ordersPerPage";
$result_orders = $conn->query($sql);


$sql_citywise = "SELECT c.City, co.ProductType, SUM(co.OrderQuantity) AS TotalQuantity
                 FROM customer_order co
                 JOIN customer c ON co.CustomerID = c.CustomerID
                 GROUP BY c.City, co.ProductType
                 ORDER BY c.City";

$result_citywise = mysqli_query($conn, $sql_citywise);

$data = [];
while ($row = mysqli_fetch_assoc($result_citywise)) {
    $data[] = $row;
}

// Close the database connection
$conn->close();



?>







<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MeatVision Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
    /* General Styles */
    body {
        margin: 0;
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #0D1B2A, #1B263B);
        color: #fff;
        overflow-x: hidden;
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 220px;
        height: 100vh;
        background: rgba(13, 27, 42, 0.7);
        backdrop-filter: blur(10px);
        padding: 2rem 1rem;
        display: flex;
        flex-direction: column;
        gap: 2rem;
        border-right: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar h1 {
        font-size: 1.5rem;
        color: #00FFFF;
        text-align: center;
        margin-bottom: 2rem;
    }

    .sidebar a {
        color: #ccc;
        text-decoration: none;
        font-weight: 600;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .sidebar a:hover {
        background: rgba(0, 255, 255, 0.2);
        color: #00FFFF;
    }

    .sidebar .logout-button {
        background-color: #e74c3c;
        /* Red color */
        color: white;
        border: none;
        padding: 1rem;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: bold;
        transition: background-color 0.3s ease;
        text-align: center;
        margin-top: auto;
        /* Ensures button stays at the bottom but doesn't go too low */
        margin-bottom: 20px;
        /* Adjusts the button's position slightly higher */
    }

    .sidebar .logout-button:hover {
        background-color: #c0392b;
        /* Darker red on hover */
    }

    .sidebar .logout-button:active {
        background-color: #b03a2e;
        /* Even darker red when clicked */
    }

    .content {
        margin-left: 240px;
        padding: 2rem;
        animation: fadeIn 1s ease forwards;
    }


    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .btn {
        padding: 12px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        text-align: center;
        display: inline-block;
        font-size: 16px;
        cursor: pointer;
        text-decoration: none;
        width: 200px;
        margin-top: 20px;
    }

    .btn:hover {
        background-color: #0056b3;
    }

    .btn-primary {
        background-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        transition: transform 0.3s;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    }

    .card:hover {
        transform: translateY(-10px);
        background: rgba(0, 255, 255, 0.1);
    }

    .card h2 {
        font-size: 2rem;
        color: #00FFFF;
    }

    .card p {
        margin-top: 0.5rem;
        font-size: 1rem;
        color: #bbb;
    }

    .charts {
        display: block;
        width: 50%;
    }



    .chart-container {
        margin-bottom: 30px;
        /* Space between each chart */
        display: flex;
        align-items: center;
        /* Aligns the chart and dropdown in a row */
        justify-content: space-between;
        /* Ensures the dropdown is aligned to the right */
    }

    .section-title {
        text-align: center;
        margin-bottom: 10px;
        width: 70%;
        /* Occupy the left portion of the container */
    }

    .dropdown-container {
        width: 25%;
        /* Gives space for the dropdown */
    }

    .chart-select {
        width: 100%;
        padding: 5px;
        font-size: 16px;
    }


    .charts>div {
        margin-bottom: 20px;
        /* Add space between the charts */
        width: 100%;
        /* Ensure the charts take full width */
    }


    canvas {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 16px;
        padding: 1rem;
    }

    .section-title {
        font-size: 2rem;
        color: #fff;
        margin: 3rem 0 1.5rem;
    }

    .table-container {
        margin-bottom: 3rem;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        overflow: hidden;
    }

    table th,
    table td {
        padding: 1rem;
        text-align: left;
    }

    table th {
        background-color: rgba(0, 255, 255, 0.2);
        color: #00FFFF;
    }

    table tr:nth-child(even) {
        background: rgba(0, 255, 255, 0.1);
    }

    table tr:hover {
        background: rgba(0, 255, 255, 0.3);
    }

    /* Search Bar */
    .search-bar {
        margin-bottom: 1rem;
        display: flex;
        justify-content: flex-end;
    }

    .search-bar input {
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #ccc;
        width: 250px;
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .pagination {
        margin-top: 20px;
        text-align: center;
    }

    .pagination a {
        display: inline-block;
        padding: 8px 16px;
        margin: 0 4px;
        text-decoration: none;
        border: 1px solid #ddd;
        color: #333;
        border-radius: 4px;
    }

    .pagination a.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .pagination a:hover:not(.active) {
        background-color: #f1f1f1;
    }
    </style>

</head>

<body>
    <div class="sidebar">
        <h1>Admin Dashboard</h1>
        <a href="#">Dashboard</a>
        <a href="#charts">Demand Heatmap</a>
        <a href="realTimeSupplyLevel.php">Supply Tracker</a>
        <a href="#charts">Market Prices</a>
        <a href="historicalProduction.php">Historical Production Data</a>
        <a href="#livestock-management">Product Info</a>
        <a href="adminDirectory.php">Directory</a>
        <a href="#recommendation">Recommendations</a>
        <button class="logout-button" onclick="window.location.href='index.html';">Logout</button>

    </div>

    <div class="content">
        <div class="cards">
            <div class="card">
                <h2><?php echo $totalCustomers; ?></h2>
                <p>Total Customers</p>
            </div>

            <div class="card">
                <h2><?php echo $totalSales; ?></h2>
                <p>Total Sales</p>
            </div>
            <div class="card">
                <h2><?php echo $totalCustomerOrders; ?></h2>
                <p>Total Customers</p>
            </div>

            <div class="card">
                <h2><?php echo $totalVendors; ?></h2>
                <p>Total Vendors</p>
            </div>
            <div class="card">
                <h2><?php echo $totalOrders; ?></h2>
                <p>Total Livestock</p>
            </div>

        </div>

        <div class="charts" id="charts">
            <div>
                <h2 class="section-title">Market Price Trend</h2>
                <canvas id="priceTrendChart"></canvas>
            </div>
            <h2 class="section-title">Supply vs Demand</h2>
            <div class="chart-container">
                <canvas id="demandSupplyChart"></canvas>
                <div class="dropdown-container">
                    <select id="demandSupplySelect" class="chart-select">
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
            </div>
        </div>
        <!-- Livestock Management Section -->
        <div class="section" id="livestock-management">
            <h2>Livestock Management</h2>
            <!-- Search bar for Livestock Management -->
            <div class="search-bar">
                <input type="text" id="livestockSearch" placeholder="Search Livestock...">
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Livestock ID</th>
                        <th>Type</th>
                        <th>Color</th>
                        <th>Weight (kg)</th>
                        <th>Birthdate</th>
                        <th>Age (Years)</th>
                        <th>Farm ID</th>
                        <th>Vaccination Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
          if ($result_livestock->num_rows > 0) {
            // Output data of each row
            while ($row = $result_livestock->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row["LivestockID"] . "</td>";
              echo "<td>" . $row["Type"] . "</td>";
              echo "<td>" . $row["Color"] . "</td>";
              echo "<td>" . $row["Weight"] . "</td>";
              echo "<td>" . $row["Birthdate"] . "</td>";
              echo "<td>" . $row["Age"] . "</td>";
              echo "<td>" . $row["FarmID"] . "</td>";
              echo "<td>" . $row["VaccinationStatus"] . "</td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='8'>No livestock data available</td></tr>";
          }
          ?>
                </tbody>
            </table>

            <!-- Button to go to Add Livestock Page -->
            <div class="section" id="livestock-management">
                <a href="adminAddLiveStock.php" class="btn btn-primary">Add Livestock</a>
            </div>


        </div>


        <div class="section">
            <h2>Product Information</h2>
            <!-- Search bar for Product Info -->
            <div class="search-bar">
                <input type="text" id="productSearch" placeholder="Search Product Info...">
            </div>

            <div class="table-container">

                <table>
                    <thead>
                        <tr>
                            <th>ProductID</th>
                            <th>Product Type</th>
                            <th>Cut</th>
                            <th>Origin</th>
                            <th>Seasonality</th>
                            <th>Average Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
          // Check if the query returned any results
          if ($result_meat->num_rows > 0) {
            // Output data of each row
            while ($row = $result_meat->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row['ProductID'] . "</td>";
              echo "<td>" . $row['type'] . "</td>";
              echo "<td>" . $row['cut'] . "</td>";
              echo "<td>" . $row['origin'] . "</td>";
              echo "<td>" . $row['seasonality'] . "</td>";
              echo "<td>" . $row['Price'] . "</td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='4'>No data available</td></tr>";
          }
          ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Customer Orders Section -->
        <div class="section" id="customer-orders">
            <h2>Customer Orders</h2>

            <!-- Search bar for Customer Orders -->
            <div class="search-bar">
                <input type="text" id="orderSearch" placeholder="Search Orders...">
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer ID</th>
                        <th>Retailer ID</th>
                        <th>Product Type</th>
                        <th>Order Quantity</th>
                        <th>Price Per Unit</th>
                        <th>Order Date</th>
                        <th>Delivery Date</th>
                        <th>Order Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
          if ($result_orders->num_rows > 0) {
            // Output data of each order row
            while ($row = $result_orders->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row["OrderID"] . "</td>";
              echo "<td>" . $row["CustomerID"] . "</td>";
              echo "<td>" . $row["RetailerID"] . "</td>";
              echo "<td>" . $row["ProductType"] . "</td>";
              echo "<td>" . $row["OrderQuantity"] . "</td>";
              echo "<td>" . $row["PricePerUnit"] . "</td>";
              echo "<td>" . $row["OrderDate"] . "</td>";
              echo "<td>" . $row["DeliveryDate"] . "</td>";
              echo "<td>" . $row["OrderStatus"] . "</td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='9'>No orders found</td></tr>";
          }
          ?>
                </tbody>
            </table>
            <!-- Pagination Controls -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">&laquo; Prev</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php if ($i == $page) echo 'active'; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
                <?php endif; ?>
            </div>

        </div>

        <div>
            <h2>City-wise Consumption Patterns</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>City</th>
                        <th>Product Type</th>
                        <th>Total Quantity Ordered</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['City']) ?></td>
                        <td><?= htmlspecialchars($row['ProductType']) ?></td>
                        <td><?= $row['TotalQuantity'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div>
            <h2 class="recommendation">Recommendations List</h2>

            <div class="search-bar">
                <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search recommendations...">
            </div>

            <table id="recommendationTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Farm ID</th>
                        <th>Officer ID</th>
                        <th>Suggested Action</th>
                        <th>Reasoning</th>
                        <th>Date</th>

                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultrec->num_rows > 0): ?>
                    <?php while($rowrec = $resultrec->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($rowrec['RecommendationID']); ?></td>
                        <td><?php echo htmlspecialchars($rowrec['FarmID']); ?></td>
                        <td><?php echo htmlspecialchars($rowrec['OfficerID']); ?></td>
                        <td><?php echo htmlspecialchars($rowrec['SuggestedAction']); ?></td>
                        <td><?php echo htmlspecialchars($rowrec['Reasoning']); ?></td>
                        <td><?php echo htmlspecialchars($rowrec['RecommendationDate']); ?></td>

                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No recommendations found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="section" id="livestock-management">
                <a href="addRecommendation.php" class="btn btn-primary">Add Recommendation</a>
            </div>
        </div>









    </div>
    <script>
    var ctx = document.getElementById('demandSupplyChart').getContext('2d');
    var demandSupplyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($product_names); ?>,
            datasets: [{
                label: 'Demand',
                data: <?php echo json_encode($demand_values); ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }, {
                label: 'Supply',
                data: <?php echo json_encode($supply_values); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        },
        options: {
            plugins: {
                legend: {
                    display: true,
                    position: 'top', // Adjust the legend's position
                    labels: {
                        boxWidth: 20,
                        padding: 15
                    }
                }
            }
        }

    });
    </script>




    <script>
    // Livestock table search
    document.getElementById('livestockSearch').addEventListener('keyup', function() {
        let filter = this.value.toUpperCase();
        let rows = document.querySelectorAll("#livestock-management tbody tr");
        rows.forEach(row => {
            let text = row.textContent.toUpperCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });

    // Product table search
    document.getElementById('productSearch').addEventListener('keyup', function() {
        let filter = this.value.toUpperCase();
        let rows = document.querySelectorAll(".table-container tbody tr");
        rows.forEach(row => {
            let text = row.textContent.toUpperCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });
    </script>
    <script>
    const productTypes = <?php echo json_encode($productTypes); ?>;
    const maxPrices = <?php echo json_encode($maxPrices); ?>;
    const minPrices = <?php echo json_encode($minPrices); ?>;
    const avgPrices = <?php echo json_encode($avgPrices); ?>;

    const ctxTrend = document.getElementById('priceTrendChart').getContext('2d');
    const priceTrendChart = new Chart(ctxTrend, {
        type: 'line', // You can change to 'bar' if preferred
        data: {
            labels: productTypes,
            datasets: [{
                    label: 'Max Price',
                    data: maxPrices,
                    borderColor: '#e53935',
                    backgroundColor: '#ffcdd2',
                    fill: false,
                    tension: 0.3
                },
                {
                    label: 'Min Price',
                    data: minPrices,
                    borderColor: '#43a047',
                    backgroundColor: '#c8e6c9',
                    fill: false,
                    tension: 0.3
                },
                {
                    label: 'Avg Price',
                    data: avgPrices,
                    borderColor: '#1e88e5',
                    backgroundColor: '#bbdefb',
                    fill: false,
                    borderDash: [5, 5],
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Product Price Trends Across Retailers'
                },
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    title: {
                        display: true,
                        text: 'Price Per Kg (BDT)'
                    },
                    beginAtZero: false
                }
            }
        }
    });
    </script>




</body>

</html>