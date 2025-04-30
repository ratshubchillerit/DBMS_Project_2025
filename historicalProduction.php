<?php
include("db.php");

// Livestock count
$livestock_query = "SELECT Type, COUNT(*) AS Count FROM livestock GROUP BY Type";
$livestock_result = $conn->query($livestock_query);

// Production batches
$batch_query = "SELECT * FROM production_batch ORDER BY ProductionDate DESC";
$batch_result = $conn->query($batch_query);

// Meat products
$product_query = "SELECT type, origin, SUM(Quantity) AS TotalProduced, AVG(Price) AS AvgPrice FROM meat_product GROUP BY type, origin";
$product_result = $conn->query($product_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Historical Production Data</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f4f6f8;
        margin: 0;
        padding: 40px;
        color: #333;
        transition: background-color 0.3s ease;
    }

    .dashboard-title {
        font-size: 32px;
        font-weight: 600;
        color: #222;
        margin-bottom: 30px;
        text-transform: uppercase;
        letter-spacing: 2px;
        animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .section {
        background: #fff;
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 40px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        animation: slideUp 0.5s ease-in-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    h2 {
        margin-bottom: 20px;
        font-size: 24px;
        color: #2c3e50;
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
        position: relative;
        overflow: hidden;
    }

    h2::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 2px;
        background: #4CAF50;
        bottom: 0;
        left: -100%;
        animation: slideIn 0.5s forwards;
    }

    @keyframes slideIn {
        from {
            left: -100%;
        }

        to {
            left: 0;
        }
    }

    table {
        width: 100%;
        font-size: 16px;
        border-collapse: collapse;
        transition: transform 0.3s ease;
    }

    th,
    td {
        text-align: left;
        padding: 12px 16px;
        position: relative;
    }

    tr:hover {
        background-color: #f9f9f9;
        transform: scale(1.02);
        transition: transform 0.2s ease-in-out;
    }

    .expired {
        background-color: #ffe0e0 !important;
    }

    .near-expiry {
        background-color: #fff6e0 !important;
    }

    .button-export,
    .button-refresh {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s ease, transform 0.2s ease;
        margin-top: -40px;
    }

    .button-export:hover,
    .button-refresh:hover {
        background-color: #45a049;
        transform: scale(1.05);
    }

    .button-export:active,
    .button-refresh:active {
        background-color: #388e3c;
    }

    .button-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .button-container button {
        margin: 5px;
    }

    @media (max-width: 768px) {
        body {
            padding: 20px;
        }

        .section {
            padding: 20px;
        }

        .button-export,
        .button-refresh {
            font-size: 14px;
            padding: 8px 15px;
        }
    }
    </style>


</head>

<body>
    <div class="dashboard-title">📊 Historical Production Data Dashboard</div>

    <div class="section">
        <h2>🐄 Livestock Counts by Type</h2>
        <div class="button-container">
            <button class="button-export" onclick="exportTableToCSV('livestock.csv', 'livestockTable')">Export</button>
            <button class="button-refresh" onclick="refreshTable('livestockTable')">Refresh</button>
        </div>
        <table id="livestockTable" class="display">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $livestock_result->fetch_assoc()) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['Type']) ?></td>
                    <td><?= htmlspecialchars($row['Count']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>🏭 Production Batches</h2>
        <div class="button-container">
            <button class="button-export" onclick="exportTableToCSV('batches.csv', 'batchTable')">Export</button>
            <button class="button-refresh" onclick="refreshTable('batchTable')">Refresh</button>
        </div>
        <table id="batchTable" class="display">
            <thead>
                <tr>
                    <th>Batch ID</th>
                    <th>Production Date</th>
                    <th>Expiry Date</th>
                    <th>Batch Quantity (kg)</th>
                    <th>Production Cost (Tk)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
        $today = date('Y-m-d');
        while ($row = $batch_result->fetch_assoc()) :
          $expiry = $row['ExpiryDate'];
          $rowClass = ($expiry < $today) ? 'expired' : ((strtotime($expiry) - time()) < 7*86400 ? 'near-expiry' : '');
        ?>
                <tr class="<?= $rowClass ?>">
                    <td><?= htmlspecialchars($row['BatchID']) ?></td>
                    <td><?= htmlspecialchars($row['ProductionDate']) ?></td>
                    <td><?= htmlspecialchars($row['ExpiryDate']) ?></td>
                    <td><?= htmlspecialchars($row['BatchQuantity']) ?></td>
                    <td><?= htmlspecialchars($row['ProductionCost']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>🥩 Meat Products by Type & Origin</h2>
        <div class="button-container">
            <button class="button-export" onclick="exportTableToCSV('products.csv', 'productTable')">Export</button>
            <button class="button-refresh" onclick="refreshTable('productTable')">Refresh</button>
        </div>
        <table id="productTable" class="display">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Origin</th>
                    <th>Total Quantity (kg)</th>
                    <th>Average Price (Tk/kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $product_result->fetch_assoc()) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['type']) ?></td>
                    <td><?= htmlspecialchars($row['origin']) ?></td>
                    <td><?= number_format($row['TotalProduced'], 2) ?></td>
                    <td><?= number_format($row['AvgPrice'], 2) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
    $(document).ready(function() {
        $('table.display').DataTable({
            responsive: true,
            pageLength: 5,
            lengthMenu: [5, 10, 25, 50]
        });
    });

    function exportTableToCSV(filename, tableId) {
        const csv = [];
        const rows = document.querySelectorAll(`#${tableId} tr`);
        for (const row of rows) {
            const cols = row.querySelectorAll("td, th");
            const rowData = [...cols].map(col => '"' + col.innerText.replace(/\"/g, '""') + '"');
            csv.push(rowData.join(","));
        }
        const csvData = new Blob([csv.join("\n")], {
            type: 'text/csv'
        });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(csvData);
        link.download = filename;
        link.click();
    }

    function refreshTable(tableId) {
        $('#' + tableId).DataTable().ajax.reload();
    }
    </script>
</body>

</html>

<?php $conn->close(); ?>