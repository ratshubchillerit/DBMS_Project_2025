// Data for Overview Graph (example: orders and inventory)
const overviewData = {
    labels: ['Orders', 'Inventory'],
    datasets: [{
        label: 'Overview',
        data: [70, 30],
        backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(255, 159, 64, 0.2)'],
        borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 159, 64, 1)'],
        borderWidth: 1
    }]
};

// Data for Sales Overview Graph
const salesData = {
    labels: ['January', 'February', 'March', 'April', 'May', 'June'],
    datasets: [{
        label: 'Orders (units)',
        data: [100, 200, 150, 300, 250, 400],
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1
    }]
};

// Create Overview Graph
const ctxOverview = document.getElementById('overviewGraph').getContext('2d');
const overviewGraph = new Chart(ctxOverview, {
    type: 'pie',
    data: overviewData,
    options: {
        responsive: true
    }
});

// Create Sales Performance Graph
const ctxSales = document.getElementById('salesGraph').getContext('2d');
const salesGraph = new Chart(ctxSales, {
    type: 'line',
    data: salesData,
    options: {
        responsive: true
    }
});
