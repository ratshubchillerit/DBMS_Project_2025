// Data for Overview Graph
const overviewData = {
    labels: ['Cattle', 'Sheep', 'Goat'],
    datasets: [{
        label: 'Animals Processed',
        data: [200, 150, 120],
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1
    }]
};

// Data for Inventory Graph
const inventoryData = {
    labels: ['Beef', 'Pork', 'Chicken', 'Lamb'],
    datasets: [{
        label: 'Inventory (kg)',
        data: [500, 300, 400, 200],
        backgroundColor: 'rgba(153, 102, 255, 0.2)',
        borderColor: 'rgba(153, 102, 255, 1)',
        borderWidth: 1
    }]
};

// Data for Market Trends Graph
const marketTrendsData = {
    labels: ['Beef', 'Pork', 'Chicken', 'Lamb'],
    datasets: [{
        label: 'Market Price (USD)',
        data: [5, 4, 3, 7],
        backgroundColor: 'rgba(255, 159, 64, 0.2)',
        borderColor: 'rgba(255, 159, 64, 1)',
        borderWidth: 1
    }]
};

// Create Overview Graph
const ctxOverview = document.getElementById('overviewGraph').getContext('2d');
const overviewGraph = new Chart(ctxOverview, {
    type: 'bar',
    data: overviewData,
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Create Inventory Graph
const ctxInventory = document.getElementById('inventoryGraph').getContext('2d');
const inventoryGraph = new Chart(ctxInventory, {
    type: 'pie',
    data: inventoryData,
    options: {
        responsive: true
    }
});

// Create Market Trends Graph
const ctxMarketTrends = document.getElementById('marketTrendsGraph').getContext('2d');
const marketTrendsGraph = new Chart(ctxMarketTrends, {
    type: 'line',
    data: marketTrendsData,
    options: {
        responsive: true
    }
});
