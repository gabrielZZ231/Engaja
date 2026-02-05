import ApexCharts from 'apexcharts';

let responsesChart = null;

export function initResponsesChart(data = { labels: [], series: [] }) {
    const el = document.querySelector('#responsesChart');

    if (!el) return;

    if (responsesChart) {
        responsesChart.destroy();
    }

    responsesChart = new ApexCharts(el, {
        chart: {
            type: 'line',
            toolbar: { show: false }
        },
        colors: ['#421944'], // Engaja purple
        series: [
            {
                name: 'Respostas',
                data: data.series
            }
        ],
        xaxis: {
            categories: data.labels
        }
    });

    responsesChart.render();
}
