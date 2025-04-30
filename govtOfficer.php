<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Government Officer Dashboard</title>
    <link rel="stylesheet" href="govtOfficer.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- For Graphs -->
</head>
<body>
    <!-- Sidebar Navigation -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h2>Government Officer Dashboard</h2>
        </div>
        <ul class="nav-links">
            <li><a href="#overview">Dashboard Overview</a></li>
            <li><a href="#farm-recommendations">Farm Recommendations</a></li>
            <li><a href="#nutrition-recommendations">Nutrition Value Recommendations</a></li>
            <li><a href="#compliance">Regulatory Compliance</a></li>
            <li><a href="#reports">Reports & Analytics</a></li>
            <li><a href="#feedback">Feedback</a></li>
        </ul>
    </nav>

    <!-- Main Content Area -->
    <div class="dashboard-content">
        <header>
            <h1>Government Officer Dashboard</h1>
        </header>

        <!-- Dashboard Overview Section -->
        <section id="overview" class="dashboard-section">
            <h2>Dashboard Overview</h2>
            <p>Overview of farm data, nutritional value, and compliance metrics.</p>
            <canvas id="overviewGraph"></canvas>
        </section>

        <!-- Farm Recommendations Section -->
        <section id="farm-recommendations" class="dashboard-section">
            <h2>Farm Recommendations</h2>
            <p>Provide actionable recommendations based on farm data for improving productivity and sustainability.</p>
            
            <!-- Form to provide farm recommendations -->
            <form id="farmRecommendationsForm">
                <label for="farmId">Farm ID:</label>
                <input type="text" id="farmId" name="farmId" required>

                <label for="recommendation">Recommendation:</label>
                <textarea id="recommendation" name="recommendation" rows="4" required></textarea>

                <label for="priority">Priority:</label>
                <select id="priority" name="priority">
                    <option value="High">High</option>
                    <option value="Medium">Medium</option>
                    <option value="Low">Low</option>
                </select>

                <button type="submit">Submit Recommendation</button>
            </form>

            <!-- Display recommendations -->
            <h3>Existing Recommendations</h3>
            <table>
                <thead>
                    <tr>
                        <th>Farm ID</th>
                        <th>Recommendation</th>
                        <th>Priority</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>001</td>
                        <td>Improve pasture rotation</td>
                        <td>High</td>
                        <td>Pending</td>
                    </tr>
                    <tr>
                        <td>002</td>
                        <td>Increase feed quality</td>
                        <td>Medium</td>
                        <td>In Progress</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Nutrition Value Recommendations Section -->
        <section id="nutrition-recommendations" class="dashboard-section">
            <h2>Nutrition Value Recommendations</h2>
            <p>Track and analyze the nutritional value of various meat products, and make recommendations for improvement.</p>

            <!-- Form to provide nutrition value recommendations -->
            <form id="nutritionRecommendationsForm">
                <label for="productName">Product Name:</label>
                <input type="text" id="productName" name="productName" required>

                <label for="nutritionRecommendation">Recommendation:</label>
                <textarea id="nutritionRecommendation" name="nutritionRecommendation" rows="4" required></textarea>

                <button type="submit">Submit Nutrition Recommendation</button>
            </form>

            <!-- Display nutrition recommendations -->
            <h3>Existing Nutrition Recommendations</h3>
            <ul>
                <li>Beef: Increase protein content, reduce fat.</li>
                <li>Chicken: Maintain high protein levels, reduce fat content.</li>
            </ul>
        </section>

        <!-- Regulatory Compliance Section -->
        <section id="compliance" class="dashboard-section">
            <h2>Regulatory Compliance</h2>
            <p>Ensure that farms comply with national and international food safety regulations.</p>
            <table>
                <thead>
                    <tr>
                        <th>Farm ID</th>
                        <th>Compliance Status</th>
                        <th>Last Inspection Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>001</td>
                        <td>Compliant</td>
                        <td>Jan 10, 2025</td>
                    </tr>
                    <tr>
                        <td>002</td>
                        <td>Non-Compliant</td>
                        <td>Feb 1, 2025</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Reports & Analytics Section -->
        <section id="reports" class="dashboard-section">
            <h2>Reports & Analytics</h2>
            <p>Generate and download detailed reports on farm performance, compliance, and nutritional data.</p>
            <button class="generate-report-btn">Generate Report</button>
        </section>

        <!-- Feedback Section -->
        <section id="feedback" class="dashboard-section">
            <h2>Feedback</h2>
            <p>View feedback from farms and provide actionable recommendations.</p>
            <button class="feedback-btn">View Feedback</button>
        </section>
    </div>

    <script src="govtOfficer.js"></script>
</body>
</html>
