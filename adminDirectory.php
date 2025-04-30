<?php
include "db.php";

// Fetch retailer data
$retailerSql = "SELECT * FROM retailer";
$retailerResult = $conn->query($retailerSql);

// Fetch wholesaler data
$wholesalerSql = "SELECT * FROM wholesaler";
$wholesalerResult = $conn->query($wholesalerSql);

// Fetch farm data
$farmSql = "SELECT * FROM farm";
$farmResult = $conn->query($farmSql);

// Fetch customer data
$customerSql = "SELECT * FROM customer";
$customerResult = $conn->query($customerSql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directory</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f4f6f9;
        margin: 20px;
        color: #333;
    }

    .container {
        max-width: 1200px;
        margin: auto;
    }

    h2 {
        text-align: center;
        margin-bottom: 2rem;
        color: #222;
    }

    .table-container {
        margin-bottom: 3rem;
        background: #ffffff;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0px 8px 24px rgba(0, 0, 0, 0.1);
    }

    h3 {
        margin-bottom: 1rem;
        color: #007bff;
    }

    .search-bar,
    .toolbar {
        margin-bottom: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    .search-bar input {
        padding: 10px 15px;
        width: 300px;
        border: 1px solid #ccc;
        border-radius: 8px;
        outline: none;
    }

    .toolbar button {
        margin-left: 10px;
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        background: #007bff;
        color: white;
        cursor: pointer;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table th,
    table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #e0e0e0;
    }

    table th {
        background-color: #007bff;
        color: #fff;
        text-transform: uppercase;
        font-size: 14px;
    }

    table tr:hover {
        background-color: #e3f2fd;
        transition: background 0.3s ease;
    }

    .btn-container {
        text-align: center;
        margin-top: 20px;
    }

    .btn {
        padding: 12px 25px;
        font-size: 16px;
        text-align: center;
        color: white;
        background-color: #28a745;
        border-radius: 8px;
        text-decoration: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
        border: none;
        margin-top: 1rem;
    }

    .btn:hover {
        background-color: #218838;
    }

    .row-count {
        margin-top: 10px;
        font-style: italic;
    }

    .pagination {
        text-align: center;
        margin-top: 20px;
    }

    .pagination button {
        margin: 0 5px;
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        background-color: #007bff;
        color: white;
        cursor: pointer;
    }

    .pagination button.active {
        background-color: #0056b3;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Stakeholder Directory</h2>

        <?php
        $tables = [
            'Retailer' => $retailerResult,
            'Wholesaler' => $wholesalerResult,
            'Farm' => $farmResult,
            'Customer' => $customerResult
        ];

        foreach ($tables as $title => $result) {
            $tableId = strtolower($title);
            echo "<div class='table-container'>
                    <h3>{$title}s</h3>
                    <div class='search-bar'>
                        <input type='text' id='{$tableId}Search' onkeyup=\"filterTable('{$tableId}')\" placeholder='Search Name...'>
                        <div class='toolbar'>
                            <button onclick=\"refreshPage()\">Refresh</button>
                            <button onclick=\"exportCSV('{$tableId}')\">Export to CSV</button>
                        </div>
                    </div>
                    <div class='row-count'><span id='{$tableId}Count'></span></div>
                    <table id='{$tableId}Table'>
                        <thead>
                            <tr><th>ID</th><th>Name</th><th>Area</th><th>City</th><th>Contact</th><th>Action</th></tr>
                        </thead>
                        <tbody>";

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id = $row[$title . 'ID'];
                    $name = $row['name'] ?? $row['Name'] ?? $row['FarmName'] ?? '';
                    $area = $row['area'] ?? $row['Area'] ?? '';
                    $city = $row['city'] ?? $row['City'] ?? '';
                    $contact = $row['contact'] ?? $row['Contact'] ?? $row['ContactNumber'] ?? '';
                    echo "<tr><td>{$id}</td><td>{$name}</td><td>{$area}</td><td>{$city}</td><td>{$contact}</td>
                          <td><a href='update{$title}.php?id={$id}'>Update</a> | 
                          <a href='delete{$title}.php?id={$id}' onclick=\"return confirm('Are you sure?')\">Delete</a></td></tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No {$title}s found.</td></tr>";
            }

            echo "</tbody></table>
                  <div class='btn-container'>
                    <a href='add{$title}.php' class='btn'>Add {$title}</a>
                  </div>
                  <div class='pagination' id='{$tableId}Pagination'></div>
              </div>";
        }
        ?>
    </div>

    <script>
    function refreshPage() {
        location.reload();
    }

    function filterTable(id) {
        let input = document.getElementById(id + 'Search').value.toLowerCase();
        let table = document.getElementById(id + 'Table');
        let rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        let visible = 0;

        for (let i = 0; i < rows.length; i++) {
            let nameCell = rows[i].getElementsByTagName('td')[1];
            if (nameCell && nameCell.textContent.toLowerCase().indexOf(input) > -1) {
                rows[i].style.display = '';
                visible++;
            } else {
                rows[i].style.display = 'none';
            }
        }
        document.getElementById(id + 'Count').innerText = visible + ' rows displayed';
    }

    function exportCSV(tableId) {
        let table = document.getElementById(tableId + 'Table');
        let rows = table.querySelectorAll('tr');
        let csv = [];

        rows.forEach(row => {
            let cols = row.querySelectorAll('td, th');
            let rowData = [];
            cols.forEach(col => rowData.push('"' + col.innerText + '"'));
            csv.push(rowData.join(','));
        });

        let csvContent = csv.join('\n');
        let blob = new Blob([csvContent], {
            type: 'text/csv'
        });
        let url = URL.createObjectURL(blob);
        let a = document.createElement('a');
        a.href = url;
        a.download = tableId + '.csv';
        a.click();
        URL.revokeObjectURL(url);
    }

    // Basic client-side pagination
    window.onload = function() {
        const pageSize = 5;
        ['retailer', 'wholesaler', 'farm', 'customer'].forEach(id => paginate(id, pageSize));
    }

    function paginate(tableId, pageSize) {
        let table = document.getElementById(tableId + 'Table');
        let rows = table.querySelectorAll('tbody tr');
        let totalPages = Math.ceil(rows.length / pageSize);
        let container = document.getElementById(tableId + 'Pagination');
        container.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            let btn = document.createElement('button');
            btn.innerText = i;
            btn.onclick = function() {
                rows.forEach((row, idx) => {
                    row.style.display = (idx >= (i - 1) * pageSize && idx < i * pageSize) ? '' : 'none';
                });
                container.querySelectorAll('button').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            };
            container.appendChild(btn);
        }
        container.querySelector('button').click();
    }
    </script>

</body>

</html>

<?php $conn->close(); ?>