// Data for Overview Graph
const overviewData = {
    labels: ['Available', 'Occupied'],
    datasets: [{
        label: 'Storage Capacity',
        data: [80, 20],
        backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(255, 99, 132, 0.2)'],
        borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
        borderWidth: 1
    }]
};

// Data for Temperature and Humidity Graph
const temperatureHumidityData = {
    labels: ['Morning', 'Afternoon', 'Evening'],
    datasets: [{
        label: 'Temperature (°C)',
        data: [5, 7, 4],
        backgroundColor: 'rgba(153, 102, 255, 0.2)',
        borderColor: 'rgba(153, 102, 255, 1)',
        borderWidth: 1
    }, {
        label: 'Humidity (%)',
        data: [75, 80, 78],
        backgroundColor: 'rgba(255, 159, 64, 0.2)',
        borderColor: 'rgba(255, 159, 64, 1)',
        borderWidth: 1
    }]
};

// Data for Storage Capacity Graph
const capacityData = {
    labels: ['Storage 1', 'Storage 2', 'Storage 3'],
    datasets: [{
        label: 'Storage Usage (kg)',
        data: [800, 1000, 500],
        backgroundColor: 'rgba(255, 159, 64, 0.2)',
        borderColor: 'rgba(255, 159, 64, 1)',
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

// Create Temperature & Humidity Graph
const ctxTemperatureHumidity = document.getElementById('temperatureHumidityGraph').getContext('2d');
const temperatureHumidityGraph = new Chart(ctxTemperatureHumidity, {
    type: 'line',
    data: temperatureHumidityData,
    options: {
        responsive: true
    }
});

// Create Storage Capacity Graph
const ctxCapacity = document.getElementById('capacityGraph').getContext('2d');
const capacityGraph = new Chart(ctxCapacity, {
    type: 'bar',
    data: capacityData,
    options: {
        responsive: true
    }
});
