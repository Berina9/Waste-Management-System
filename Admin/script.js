document.addEventListener("DOMContentLoaded", function() {
    fetchData();
});

function fetchData() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "connection.php", true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            try {
                var data = JSON.parse(xhr.responseText);
                updateCharts(data);
            } catch (e) {
                console.error("Failed to parse JSON response:", e);
            }
        }
    };
    xhr.send();
}

function updateCharts(data) {
    var solidWasteTotal = 0;
    var organicWasteTotal = 0;
    var hazardousWasteTotal = 0;
    var numEntries = data.length;

    data.forEach(item => {
        solidWasteTotal += parseFloat(item.solid);
        organicWasteTotal += parseFloat(item.organic);
        hazardousWasteTotal += parseFloat(item.hazardous);
    });

    var solidWasteAverage = solidWasteTotal / numEntries;
    var organicWasteAverage = organicWasteTotal / numEntries;
    var hazardousWasteAverage = hazardousWasteTotal / numEntries;

    var solidCtx = document.getElementById('solidWasteChart').getContext('2d');
    var organicCtx = document.getElementById('organicWasteChart').getContext('2d');
    var hazardousCtx = document.getElementById('hazardousWasteChart').getContext('2d');

    createPieChart(solidCtx, [solidWasteAverage, 100 - solidWasteAverage], 'Solid Waste');
    createPieChart(organicCtx, [organicWasteAverage, 100 - organicWasteAverage], 'Organic Waste');
    createPieChart(hazardousCtx, [hazardousWasteAverage, 100 - hazardousWasteAverage], 'Hazardous Waste');
}

function createPieChart(ctx, data, title) {
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Collected', 'Remaining'],
            datasets: [{
                data: data,
                backgroundColor: ['#4CAF50', '#FFCDD2'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: true,
                    text: title
                }
            }
        }
    });
}
