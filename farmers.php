<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard</title>
    <link rel="stylesheet" href="farmers.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- For Graphs -->
</head>
<body>
    <!-- Sidebar Navigation -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h2>Farmer Dashboard</h2>
        </div>
        <ul class="nav-links">
            <li><a href="#livestock">Livestock Overview</a></li>
            <li><a href="#production">Production Tracking</a></li>
            <li><a href="#market-prices">Market Prices</a></li>
            <li><a href="#supply-chain">Supply Chain</a></li>
            <li><a href="#reports">Reports</a></li>
        </ul>
    </nav>

    <!-- Main Content Area -->
    <div class="dashboard-content">
        <header>
            <h1>Farmer Dashboard</h1>
        </header>

        <!-- Livestock Overview Section -->
        <section id="livestock" class="dashboard-section">
            <h2>Livestock Overview</h2>
            <table>
                <thead>
                    <tr>
                        <th>Animal ID</th>
                        <th>Species</th>
                        <th>Health Status</th>
                        <th>Age</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>001</td>
                        <td>Cattle</td>
                        <td>Healthy</td>
                        <td>2 years</td>
                    </tr>
                    <tr>
                        <td>002</td>
                        <td>Sheep</td>
                        <td>Sick</td>
                        <td>1 year</td>
                    </tr>
                    <tr>
                        <td>003</td>
                        <td>Goat</td>
                        <td>Healthy</td>
                        <td>3 years</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Production Tracking Section -->
        <section id="production" class="dashboard-section">
            <h2>Production Tracking</h2>
            <canvas id="productionGraph"></canvas>
            <p>Track your farm's production, including milk, meat, or eggs produced over the last few months.</p>
        </section>

        <!-- Market Prices Section -->
        <section id="market-prices" class="dashboard-section">
            <h2>Market Prices</h2>
            <canvas id="marketPricesGraph"></canvas>
            <p>Get real-time market prices for various livestock products like meat, milk, and eggs.</p>
        </section>

        <!-- Supply Chain Section -->
        <section id="supply-chain" class="dashboard-section">
            <h2>Supply Chain</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Stock Level</th>
                        <th>Last Shipment</th>
                        <th>Next Delivery</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Milk</td>
                        <td>500 liters</td>
                        <td>Jan 20, 2025</td>
                        <td>Feb 5, 2025</td>
                    </tr>
                    <tr>
                        <td>Beef</td>
                        <td>200 kg</td>
                        <td>Jan 15, 2025</td>
                        <td>Feb 10, 2025</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Reports Section -->
        <section id="reports" class="dashboard-section">
            <h2>Reports</h2>
            <p>Generate farm performance reports for yield, expenses, and productivity.</p>
            <button class="generate-report-btn">Generate Report</button>
        </section>
    </div>

    <script src="farmers.js"></script>
</body>
</html>
