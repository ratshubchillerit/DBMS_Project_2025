// Example data for the graphs
const overviewData = {
    labels: ['Cattle', 'Sheep', 'Pigs', 'Goats'],
    datasets: [{
        label: 'Current Livestock Population',
        data: [120, 80, 90, 50],
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1
    }]
};

const productionData = {
    labels: ['January', 'February', 'March', 'April', 'May', 'June'],
    datasets: [{
        label: 'Monthly Yield (in tons)',
        data: [12, 15, 10, 20, 18, 25],
        backgroundColor: 'rgba(153, 102, 255, 0.2)',
        borderColor: 'rgba(153, 102, 255, 1)',
        borderWidth: 1
    }]
};

const supplyDemandData = {
    labels: ['Beef', 'Pork', 'Chicken', 'Lamb'],
    datasets: [{
        label: 'Market Demand vs Supply',
        data: [200, 150, 250, 180],
        backgroundColor: 'rgba(255, 159, 64, 0.2)',
        borderColor: 'rgba(255, 159, 64, 1)',
        borderWidth: 1
    }]
};

// Create overview graph
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

// Create production graph
const ctxProduction = document.getElementById('productionGraph').getContext('2d');
const productionGraph = new Chart(ctxProduction, {
    type: 'line',
    data: productionData,
    options: {
        responsive: true
    }
});

// Create supply-demand graph
const ctxSupplyDemand = document.getElementById('supplyDemandGraph').getContext('2d');
const supplyDemandGraph = new Chart(ctxSupplyDemand, {
    type: 'pie',
    data: supplyDemandData,
    options: {
        responsive: true
    }
});
