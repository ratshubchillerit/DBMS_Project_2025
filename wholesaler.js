// Sales Performance Data
const salesData = {
    labels: ['January', 'February', 'March', 'April', 'May', 'June'],
    datasets: [{
        label: 'Sales Volume (kg)',
        data: [1000, 1200, 1500, 1300, 1600, 1800],
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1
    }]
};

// Pricing Trends Data
const pricingData = {
    labels: ['Beef', 'Pork', 'Chicken', 'Lamb'],
    datasets: [{
        label: 'Market Price (USD)',
        data: [5, 4, 3, 7],
        backgroundColor: 'rgba(153, 102, 255, 0.2)',
        borderColor: 'rgba(153, 102, 255, 1)',
        borderWidth: 1
    }]
};

// Create Sales Performance Graph
const ctxSales = document.getElementById('salesGraph').getContext('2d');
const salesGraph = new Chart(ctxSales, {
    type: 'line',
    data: salesData,
    options: {
        responsive: true
    }
});

// Create Pricing Trends Graph
const ctxPricing = document.getElementById('pricingGraph').getContext('2d');
const pricingGraph = new Chart(ctxPricing, {
    type: 'bar',
    data: pricingData,
    options: {
        responsive: true
    }
});

// Create Overview Graph (example pie chart for sales and inventory)
const overviewData = {
    labels: ['Sales', 'Inventory'],
    datasets: [{
        label: 'Overview',
        data: [60, 40],
        backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(255, 159, 64, 0.2)'],
        borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 159, 64, 1)'],
        borderWidth: 1
    }]
};

const ctxOverview = document.getElementById('overviewGraph').getContext('2d');
const overviewGraph = new Chart(ctxOverview, {
    type: 'pie',
    data: overviewData,
    options: {
        responsive: true
    }
});

// INVENTORY CHART

const ctxInventory = document.getElementById('inventoryChart').getContext('2d');

const productNames = inventoryData.map(item => item.ProductType);
const stockLevels = inventoryData.map(item => item.AvailableQuantity);

new Chart(ctxInventory, {
    type: 'bar',
    data: {
        labels: productNames,
        datasets: [{
            label: 'Available Stock (kg)',
            data: stockLevels,
            backgroundColor: '#4caf50',
            borderColor: '#388e3c',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Quantity (kg)'
                }
            }
        },
        plugins: {
            title: {
                display: true,
                text: 'Inventory Stock Levels by Product'
            },
            legend: {
                display: false
            }
        }
    }
});

