// Data for Farm Recommendations (Priority levels)
const recommendationsData = {
    labels: ['High Priority', 'Medium Priority', 'Low Priority'],
    datasets: [{
        label: 'Farm Recommendations',
        data: [10, 5, 2],
        backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(255, 159, 64, 0.2)', 'rgba(255, 99, 132, 0.2)'],
        borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 159, 64, 1)', 'rgba(255, 99, 132, 1)'],
        borderWidth: 1
    }]
};

// Data for Nutrition Value Tracking (protein content per 100g)
const nutritionData = {
    labels: ['Beef', 'Pork', 'Chicken', 'Lamb'],
    datasets: [{
        label: 'Protein Content (g per 100g)',
        data: [26, 22, 20, 28],
        backgroundColor: 'rgba(153, 102, 255, 0.2)',
        borderColor: 'rgba(153, 102, 255, 1)',
        borderWidth: 1
    }]
};

// Create Farm Recommendations Graph (Pie chart for priority levels)
const ctxRecommendations = document.getElementById('overviewGraph').getContext('2d');
const recommendationsGraph = new Chart(ctxRecommendations, {
    type: 'pie',
    data: recommendationsData,
    options: {
        responsive: true
    }
});

// Create Nutrition Value Tracking Graph (Bar chart for protein content)
const ctxNutrition = document.getElementById('nutritionGraph').getContext('2d');
const nutritionGraph = new Chart(ctxNutrition, {
    type: 'bar',
    data: nutritionData,
    options: {
        responsive: true
    }
});
