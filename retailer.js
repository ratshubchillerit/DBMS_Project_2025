// Sales Performance Data
const salesData = {
    labels: ['January', 'February', 'March', 'April', 'May', 'June'],
    datasets: [{
        label: 'Sales Volume (kg)',
        data: [500, 600, 700, 800, 750, 850],
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1
    }]
};

// Customer Feedback Data
const feedbackData = {
    labels: ['Excellent', 'Good', 'Average', 'Poor'],
    datasets: [{
        label: 'Customer Ratings',
        data: [300, 200, 100, 50],
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

// Create Customer Feedback Graph
const ctxFeedback = document.getElementById('feedbackGraph').getContext('2d');
const feedbackGraph = new Chart(ctxFeedback, {
    type: 'bar',
    data: feedbackData,
    options: {
        responsive: true
    }
});

// Overview Graph (example pie chart for sales and inventory)
const overviewData = {
    labels: ['Sales', 'Inventory'],
    datasets: [{
        label: 'Overview',
        data: [65, 35],
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


