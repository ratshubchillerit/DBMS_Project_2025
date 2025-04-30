// Data for Production Tracking Graph
const productionData = {
    labels: ['January', 'February', 'March', 'April', 'May', 'June'],
    datasets: [{
        label: 'Milk Production (liters)',
        data: [1000, 1200, 1100, 1400, 1300, 1500],
        backgroundColor: 'rgba(153, 102, 255, 0.2)',
        borderColor: 'rgba(153, 102, 255, 1)',
        borderWidth: 1
    }]
};

// Data for Market Prices Graph
const marketPricesData = {
    labels: ['Beef', 'Pork', 'Chicken', 'Milk'],
    datasets: [{
        label: 'Market Price (USD)',
        data: [5, 4, 3, 2],
        backgroundColor: 'rgba(255, 159, 64, 0.2)',
        borderColor: 'rgba(255, 159, 64, 1)',
        borderWidth: 1
    }]
};

// Create production graph
const ctxProduction = document.getElementById('productionGraph').getContext('2d');
const productionGraph = new Chart(ctxProduction, {
    type: 'line',
    data: productionData,
    options: {
        responsive: true
    }
});

// Create market prices graph
const ctxMarketPrices = document.getElementById('marketPricesGraph').getContext('2d');
const marketPricesGraph = new Chart(ctxMarketPrices, {
    type: 'bar',
    data: marketPricesData,
    options: {
        responsive: true
    }
});
