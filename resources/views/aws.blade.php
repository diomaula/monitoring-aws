<!DOCTYPE html>
<html>
<head>
    <title>Monitoring AWS BMKG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .status-merah { background: red; color: white; padding: 5px 10px; border-radius: 5px; }
        .status-hijau { background: green; color: white; padding: 5px 10px; border-radius: 5px; }
    </style>
</head>
<body class="p-4">
    <div class="container">
        <h1 class="mb-4">Monitoring AWS BMKG</h1>

        <table class="table table-bordered" id="aws-table">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Waktu</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $station)
                    <tr>
                        <td>{{ $station['idaws'] }}</td>
                        <td>{{ $station['waktu'] }}</td>
                        <td>
                            <span class="{{ $station['status'] == 'MERAH' ? 'status-merah' : 'status-hijau' }}">
                                {{ $station['status'] }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h3 class="mt-5">Grafik Curah Hujan</h3>
        <canvas id="rainChart" height="100"></canvas>
    </div>

    <script>
        async function fetchData() {
            const response = await fetch('/aws/api');
            return await response.json();
        }

        function updateTable(data) {
            const tbody = document.querySelector("#aws-table tbody");
            tbody.innerHTML = "";
            data.forEach(station => {
                const row = `
                    <tr>
                        <td>${station.idaws}</td>
                        <td>${station.waktu}</td>
                        <td><span class="${station.status === 'MERAH' ? 'status-merah' : 'status-hijau'}">${station.status}</span></td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }

        let rainChart;
        function updateChart(data) {
            const labels = data.map(s => s.idaws);
            const values = data.map(s => parseFloat(s.rain ?? 0));

            if (rainChart) {
                rainChart.data.labels = labels;
                rainChart.data.datasets[0].data = values;
                rainChart.update();
            } else {
                const ctx = document.getElementById('rainChart').getContext('2d');
                rainChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Curah Hujan (mm)',
                            data: values,
                            backgroundColor: 'blue'
                        }]
                    },
                    options: { responsive: true, scales: { y: { beginAtZero: true } } }
                });
            }
        }

        async function refreshData() {
            const data = await fetchData();
            updateTable(data);
            updateChart(data);
        }

        refreshData();
        setInterval(refreshData, 60000); // refresh tiap 1 menit
    </script>
</body>
</html>
