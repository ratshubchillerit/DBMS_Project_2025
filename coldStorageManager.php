<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cold Storage Manager Dashboard</title>
    <link rel="stylesheet" href="coldStorageManager.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- For Graphs -->
</head>
<body>
    <!-- Sidebar Navigation -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h2>Cold Storage Dashboard</h2>
        </div>
        <ul class="nav-links">
            <li><a href="#overview">Dashboard Overview</a></li>
            <li><a href="#inventory">Inventory Management</a></li>
            <li><a href="#temperature-humidity">Temperature & Humidity</a></li>
            <li><a href="#capacity">Storage Capacity</a></li>
            <li><a href="#expiry-rotation">Product Expiry & Rotation</a></li>
            <li><a href="#reports">Reports</a></li>
        </ul>
    </nav>

    <!-- Main Content Area -->
    <div class="dashboard-content">
        <header>
            <h1>Cold Storage Manager Dashboard</h1>
        </header>

        <!-- Dashboard Overview Section -->
        <section id="overview" class="dashboard-section">
            <h2>Dashboard Overview</h2>
            <p>Overview of key metrics such as available storage capacity, product status, and environmental conditions.</p>
            <canvas id="overviewGraph"></canvas>
        </section>

        <!-- Inventory Management Section -->
        <section id="inventory" class="dashboard-section">
            <h2>Inventory Management</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Storage Location</th>
                        <th>Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>001</td>
                        <td>Beef</td>
                        <td>1000 kg</td>
                        <td>Storage 1A</td>
                        <td>Feb 10, 2025</td>
                    </tr>
                    <tr>
                        <td>002</td>
                        <td>Chicken</td>
                        <td>500 kg</td>
                        <td>Storage 1B</td>
                        <td>Mar 15, 2025</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Temperature and Humidity Section -->
        <section id="temperature-humidity" class="dashboard-section">
            <h2>Temperature & Humidity</h2>
            <canvas id="temperatureHumidityGraph"></canvas>
            <p>Monitor the temperature and humidity levels of the cold storage areas in real-time.</p>
        </section>

        <!-- Storage Capacity Section -->
        <section id="capacity" class="dashboard-section">
            <h2>Storage Capacity</h2>
            <canvas id="capacityGraph"></canvas>
            <p>Track the storage capacity usage and available space for products.</p>
        </section>

        <!-- Product Expiry & Rotation Section -->
        <section id="expiry-rotation" class="dashboard-section">
            <h2>Product Expiry & Rotation</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Expiry Date</th>
                        <th>Days Until Expiry</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>001</td>
                        <td>Beef</td>
                        <td>Feb 10, 2025</td>
                        <td>20 days</td>
                    </tr>
                    <tr>
                        <td>002</td>
                        <td>Chicken</td>
                        <td>Mar 15, 2025</td>
                        <td>55 days</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Reports Section -->
        <section id="reports" class="dashboard-section">
            <h2>Reports</h2>
            <p>Generate and download detailed reports on cold storage performance, inventory turnover, and product management.</p>
            <button class="generate-report-btn">Generate Report</button>
        </section>
    </div>

    <script src="coldStorageManager.js"></script>
</body>
</html>
