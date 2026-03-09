function initStatsChart(elementId, labels, revenue, profit, orders, margin) {
    var ctx = document.getElementById(elementId).getContext('2d');
    
    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Revenue',
                    data: revenue,
                    borderWidth: 2,
                    backgroundColor: 'rgba(54,162,235,0.1)',
                    borderColor: 'rgba(54,162,235,1)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Profit',
                    data: profit,
                    borderWidth: 2,
                    backgroundColor: 'rgba(75,192,192,0.1)',
                    borderColor: 'rgba(75,192,192,1)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Orders',
                    data: orders,
                    borderWidth: 2,
                    backgroundColor: 'rgba(255,206,86,0.1)',
                    borderColor: 'rgba(255,206,86,1)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Margin',
                    data: margin,
                    borderWidth: 2,
                    backgroundColor: 'rgba(153,102,255,0.1)',
                    borderColor: 'rgba(153,102,255,1)',
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}