@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4 d-flex flex-column">
    <div class="row">
        <select id="yearSelect" class="form-control mb-3" style="width: 150px;"> 
            @foreach($years as $year)
                <option value="{{ $year->year }}" {{ $year->year == $currentYear ? 'selected' : '' }}>
                    {{ $year->year }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Dropdown to select year -->
    <div class="row">
        <!-- Canvas for Bar Chart with smaller size -->
        <div class="col-12" style="max-width: 66%; margin: 0 auto;">
            <canvas id="yearwiseChart" width="500" height="300"></canvas>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('assets/vendor/buttons/chart.js') }}"></script>

<script>
    $(document).ready(function() {
        let chartInstance; // Variable to store the current chart instance

        const selectedYear = $('#yearSelect').val();

        // Initial data for the current year
        const yearData = @json($visits); // Pass visits data to JavaScript

        // Function to update the chart based on selected year
        function updateChart(year) {
            const filteredData = yearData.filter(data => data.year == year);
            
            // Prepare the data for the chart
            const labels = filteredData.map(data => data.state_name); // Use state name
            const schedule1 = filteredData.map(data => data.schedule_1);
            const schedule2 = filteredData.map(data => data.schedule_2);
            const schedule3 = filteredData.map(data => data.schedule_3);
            const schedule4 = filteredData.map(data => data.schedule_4);

            // Destroy the previous chart if it exists
            if (chartInstance) {
                chartInstance.destroy();
            }

            // Create the new chart
            const ctx = document.getElementById('yearwiseChart').getContext('2d');
            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Schedule 1',
                            data: schedule1,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Schedule 2',
                            data: schedule2,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Schedule 3',
                            data: schedule3,
                            backgroundColor: 'rgba(255, 206, 86, 0.2)',
                            borderColor: 'rgba(255, 206, 86, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'OCPF',
                            data: schedule4,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });
        }

        // Update chart initially with the current year data
        updateChart(selectedYear);

        // Update chart on year change
        $('#yearSelect').on('change', function() {
            const selectedYear = $(this).val();
            updateChart(selectedYear);
        });
    });
</script>
@endpush

