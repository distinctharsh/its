@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <select id="yearSelect" class="form-control mb-3" style="width: 80px;"> 
            @foreach($years as $year)
                <option value="{{ $year->year }}" {{ $year->year == $currentYear ? 'selected' : '' }}>
                    {{ $year->year }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="row">
        <div style="max-width: 800px; margin: 0 auto;">
            <canvas id="pieChart" width="500" height="300"></canvas>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('assets/vendor/buttons/chart.js') }}"></script>

<script>
    $(document).ready(function() {
        let chartInstance; // Variable to store the current chart instance
        const pieData = @json($pieChartData); // Pass pie chart data to JavaScript
        const selectedYear = $('#yearSelect').val();

        function updatePieChart(year) {
            // Filter pie chart data for selected year
            const filteredData = pieData.filter(data => data.year == year);
            
            // If data is empty for the selected year, return
            if (filteredData.length === 0) {
                return;
            }

            // Extract schedule data for the pie chart
            const schedule1 = filteredData[0].schedule_1;
            const schedule2 = filteredData[0].schedule_2;
            const schedule3 = filteredData[0].schedule_3;
            const schedule4 = filteredData[0].schedule_4;

            // Destroy the previous chart if it exists
            if (chartInstance) {
                chartInstance.destroy();
            }

            // Create the new pie chart
            const ctx = document.getElementById('pieChart').getContext('2d');
            chartInstance = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Schedule 1', 'Schedule 2', 'Schedule 3', 'OCPF'],
                    datasets: [{
                        data: [schedule1, schedule2, schedule3, schedule4],
                        backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)'],
                        borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { labels: { font: { size: 10 } } }
                    }
                }
            });
        }

        // Initial chart for selected year
        updatePieChart(selectedYear);

        // Update chart on year change
        $('#yearSelect').on('change', function() {
            const selectedYear = $(this).val();
            updatePieChart(selectedYear);
        });
    });
</script>
@endpush
