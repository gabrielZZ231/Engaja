import ApexCharts from 'apexcharts';

function cssVar(name) {
    return getComputedStyle(document.documentElement)
        .getPropertyValue(name)
        .trim();
}

const engajaPurple = cssVar('--engaja-purple');

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
        colors: [engajaPurple],
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

let analfabetismoChart = null;

export function initAnalfabetismoChart(data) {
    const el = document.querySelector('#analfabetismoChart');

    if (!el) return;

    if (analfabetismoChart) {
        analfabetismoChart.destroy();
    }

    analfabetismoChart = new ApexCharts(el, {
        chart: {
            type: 'bar',
            toolbar: { show: false }
        },
        colors: [engajaPurple],
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 6,
            }
        },
        series: [
            {
                name: 'Taxa (%)',
                data: data.series
            }
        ],
        xaxis: {
            categories: data.labels
        },
        dataLabels: {
            enabled: true,
            formatter: val => `${val}%`
        }
    });

    analfabetismoChart.render();
}

