@extends('layouts.layout')

@section('content')

@php
$id = 1;
@endphp
<div class="container my-5">
    <div class="row pt-4" >
       <!-- Visit Card -->
       <div class="col-md-3 mb-4 d-flex justify-content-center">
            <div class="card " style="height: 280px; width: 250px; border-radius: 20px;">
                <h5 class="card-header  text-white d-flex align-items-center justify-content-center" style="background: #dab262;  border-radius: 20px 20px 0px 0px;">
                    <!-- <i class="bi bi-calendar-event display-5 mr-2"></i> -->
                    <i class="fa-solid fa-calendar-days mr-2"></i>
                    Inspection
                </h5>
                <div class="card-body text-center " style="background-color: #f7f4ed; border-radius: 0px 0px 20px 20px;">
                    <p class="card-text mt-3"><span style="color: #5d4a2f;">Total <br>Records</span>  <br> <br> <strong> {{ $visitCount }} </strong></p>
                    <a href="{{ route('manageVisit') }}" class="btn btn-outline-warning" >View Details</a>
                </div>
            </div>
        </div>

        <!-- Inspector Card -->     
        <div class="col-md-3 mb-4 d-flex justify-content-center">
            <div class="card " style="height: 280px; width: 250px; border-radius: 20px;">
                <h5 class="card-header  text-white d-flex align-items-center justify-content-center" style="background: #de6fa4;  border-radius: 20px 20px 0px 0px;">
                    <!-- <i class="bi bi-person-check display-5 mr-2"></i> -->
                    <i class="fa-solid fa-user-check mr-2"></i>
                    Inspector
                </h5>
                <div class="card-body text-center " style="background-color: #e1e5f3; border-radius: 0px 0px 20px 20px;">
                    <p class="card-text mt-3"> <span style="color: #242c52;">Total <br>Records</span>  <br><br> <strong> {{ $inspectorCount }} </strong></p>
                    <a href="{{ route('manageInspector') }}" class="btn btn-outline-primary dashboard-view-detail-btn" >View Details</a>
                </div>
            </div>
        </div>
  
        <!-- OPCW Card -->
        <div class="col-md-3 mb-4 d-flex justify-content-center">
            <div class="card " style="height: 280px; width: 250px; border-radius: 20px;">
                <h5 class="card-header text-white d-flex align-items-center justify-content-center" style="background: #d97d58;  border-radius: 20px 20px 0px 0px;">
                    <!-- <i class="bi bi-flag display-5 mr-2"></i> -->
                    <i class="fa-solid fa-flag mr-2"></i>
                    OPCW Notification
                </h5>
                <div class="card-body text-center " style="background-color: #f7efee; border-radius: 0px 0px 20px 20px;">
                    <p class="card-text mt-3"><span style="color: #4e2a22;">Total <br>Records</span>  <br> <br> <strong> {{ $opcwFaxeCount }} </strong></p>
                    <a href="{{ route('manageOpcw') }}" class="btn btn-outline-danger" >View Details</a>
                </div>
            </div>
        </div>

        <!-- Reports Card -->
        <div class="col-md-3 mb-4 d-flex justify-content-center">
            <div class="card" style="height: 280px; width: 250px; border-radius: 20px;">
                <h5 class="card-header text-white d-flex align-items-center justify-content-center" 
                    style="background: #2C786C;  border-radius: 20px 20px 0 0;">
                    <i class="fa-solid fa-flag mr-2"></i>
                    Sequential <br/> Inspection
                </h5>
                <div class="card-body text-center" style="background-color: #f7efee; border-radius: 0 0 20px 20px;">
                    <p class="card-text mt-3">
                        <span style="color: #4e2a22;">Total <br>Records</span>  <br><br>
                        <strong>{{ $SequentialInspection }}</strong>
                    </p>
                    <a href="{{ route('listInspectorsdash.get', ['id' => $id]) }}" class="btn btn-outline-success">
                        View Details
                    </a>
                </div>
            </div>
        </div>

    </div>

     <!-- Select Year Section -->
     <hr class="my-5">
     <section class="mt-5">

<!-- Pie Chart Section -->
<div class="row mt-5">
<div class="col-md-4 mx-auto mb-4">
        <div class="card" style="border-radius: 20px;">
            <h5 class="card-header text-white d-flex align-items-center justify-content-center" style="background: #712fd3; border-radius: 20px 20px 0 0;">
                <i class="fa-solid fa-chart-pie mr-2"></i> Total Inspections
            </h5>

         
            <div class="card-body d-flex flex-column">
                <div class="row justify-content-between">
                    <select id="pieYearSelect" class="form-control mb-3" style="width: 150px; margin-bottom: 20px;">
                        @foreach($years as $year)
                            <option value="{{ $year->year }}" {{ $year->year == $currentYear ? 'selected' : '' }}>{{ $year->year }}</option>
                        @endforeach
                    </select>
    
                    <div>
                        <a href="javascript:void(0);" onclick="printChart('pieChart')" style="font-size: 20px; margin-top: 15px;">
                            <i class="fa-solid fa-print"></i> 
                        </a>
                    </div>
                </div>


                <canvas id="pieChart" width="500" height="200"></canvas>
               
            </div>
        </div>
    </div>

    <div class="col-md-4 mx-auto mb-4">
        <div class="card" style="border-radius: 20px;">
            <h5 class="card-header text-white text-center" style="background: #2C786C; border-radius: 20px 20px 0 0;">
                <i class="fa-solid fa-chart-pie mr-2"></i> Sequential Inspections
            </h5>
            <div class="card-body d-flex flex-column ">
                <div class="row justify-content-between">
                    <select class="form-control mb-3" style="width: 150px; margin-bottom: 20px; visibility: hidden;" >
                        @foreach($years as $year)
                            <option value="{{ $year->year }}" {{ $year->year == $currentYear ? 'selected' : '' }}>{{ $year->year }}</option>
                        @endforeach
                    </select>

                    <a href="javascript:void(0);" onclick="printChart('sequentialPieChart')" style="font-size: 20px; margin-top: 15px;">
                        <i class="fa-solid fa-print"></i> 
                    </a>

                    </div>
                <canvas id="sequentialPieChart" width="400" height="200"></canvas>
             
            </div>
        </div>
    </div>


    <div class="col-md-4 mx-auto mb-4">
        <div class="card" style="border-radius: 20px;">
            <h5 class="card-header text-white text-center" style="background: #FF5733; border-radius: 20px 20px 0 0;">
                <i class="fa-solid fa-chart-pie mr-2"></i> Non-Sequential Inspections
            </h5>
            <div class="card-body d-flex flex-column ">
                <div class="row justify-content-between">
                    <select class="form-control mb-3" style="width: 150px; margin-bottom: 20px; visibility: hidden;" >
                        <option value="" ></option>
                    </select>

                    <a href="javascript:void(0);" onclick="printChart('nonSequentialPieChart')" style="font-size: 20px; margin-top: 15px;">
        <i class="fa-solid fa-print"></i> 
    </a>
                </div>

                <canvas id="nonSequentialPieChart" width="400" height="200"></canvas>
             
            </div>
        </div>
    </div>
</div>

<!-- Yearwise Chart Section -->
<div class="row">
    <div class="col-md-8 mx-auto mb-4">
        <div class="card" style="border-radius: 20px; ">
            <h5 class="card-header text-white text-center" style="background: #fb5a22; border-radius: 20px 20px 0 0;">
                State Wise Inspections
            </h5>
            <div class="card-body d-flex flex-column ">
                <div class="row justify-content-between">
                    <select id="yearSelect" class="form-control mb-3" style="width: 150px; margin-bottom: 20px;">
                        @foreach($years as $year)
                            <option value="{{ $year->year }}" {{ $year->year == $currentYear ? 'selected' : '' }}>{{ $year->year }}</option>
                        @endforeach
                    </select>

                    <a href="javascript:void(0);" onclick="printChart('yearwiseChart')" style="font-size: 20px; margin-top: 15px;">
                        <i class="fa-solid fa-print"></i> 
                    </a>
                </div>
                <canvas id="yearwiseChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>


    <div class="col-md-8 mx-auto mb-4">
        <div class="card" style="border-radius: 20px; ">
            <h5 class="card-header text-white text-center" style="background: #fb5a22; border-radius: 20px 20px 0 0;">
                Issues Bar Graph Year Wise Inspections
            </h5>
            <div class="card-body d-flex flex-column ">
                <div class="row justify-content-between">
                    <select id="yearIssueSelect" class="form-control mb-3" style="width: 150px; margin-bottom: 20px;">
                        @foreach($years as $year)
                            <option value="{{ $year->year }}" {{ $year->year == $currentYear ? 'selected' : '' }}>{{ $year->year }}</option>
                        @endforeach
                    </select>

                    <a href="javascript:void(0);" onclick="printChart('yearwiseIssueChart')" style="font-size: 20px; margin-top: 15px;">
                        <i class="fa-solid fa-print"></i> 
                    </a>
                </div>
                <canvas id="yearwiseIssueChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>



<div class="card draggable" style="color: black;">
    <div class="card-body">
        <div class="row">
            <div class="col-md-10 mt-0">
                <h5 class="card-title">Activity Log</h5>
            </div>

            <div class="col-auto">
                <div class="stat text-primary">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>
        </div>

        @if($loginAndActivity['lastLogin'] && $loginAndActivity['lastLogin']->created_at)
            <span class="mt-1 mb-3" style="font-size: 12px;">
                Last Log In &nbsp;
                <span class="text-muted">
                    {{ \Carbon\Carbon::parse($loginAndActivity['lastLogin']->created_at)->format('d M Y h:i A') }}
                </span>
            </span> </br>
        @else
            <span class="mt-1 mb-3" style="font-size: 12px;">
                Last Log In &nbsp;
                <span class="text-muted">N/A</span>
            </span> </br>
        @endif

        @if($loginAndActivity['lastActivity'] && $loginAndActivity['lastActivity']->created_at)
            <span class="mt-1 mb-3" style="font-size: 12px;">
                Last Update &nbsp;
                <span class="text-muted">
                    {{ \Carbon\Carbon::parse($loginAndActivity['lastActivity']->created_at)->format('d M Y h:i A') }}
                </span>
            </span>
        @else
            <span class="mt-1 mb-3" style="font-size: 12px;">
                Last Update &nbsp;
                <span class="text-muted">N/A</span>
            </span>
        @endif

    </div>
</div>




</section>


</div>
@endsection

@push('script')
<script src="{{ asset('assets/vendor/buttons/chart.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables/chartjs-plugin-datalabels.js') }}?v={{ filemtime(public_path('assets/vendor/datatables/chartjs-plugin-datalabels.js')) }}"></script>



<script>
    Chart.register(ChartDataLabels);
    $(document).ready(function () {
        let chartInstance;

        const yearData = @json($visits);
        const updateChart = (year) => {
            const filteredData = yearData.filter(data => data.year == year);
            const labels = filteredData.map(data => data.state_name);
            const schedule1 = filteredData.map(data => data.schedule_1);
            const schedule2 = filteredData.map(data => data.schedule_2);
            const schedule3 = filteredData.map(data => data.schedule_3);
            const schedule4 = filteredData.map(data => data.schedule_4);

            // Filter out zero values for visualization
            const validSchedule1 = schedule1.map(value => value === 0 ? null : value);
            const validSchedule2 = schedule2.map(value => value === 0 ? null : value);
            const validSchedule3 = schedule3.map(value => value === 0 ? null : value);
            const validSchedule4 = schedule4.map(value => value === 0 ? null : value);
            
            if (chartInstance) chartInstance.destroy();
            
            const ctx = document.getElementById('yearwiseChart').getContext('2d');
            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { 
                            label: 'Schedule 1', 
                            data: validSchedule1, 
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            barThickness: 30,
                        },
                        { 
                            label: 'Schedule 2', 
                            data: validSchedule2, 
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            barThickness: 30,
                        },
                        { 
                            label: 'Schedule 3', 
                            data: validSchedule3, 
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            barThickness: 30,
                        },
                        { 
                            label: 'OCPF', 
                            data: validSchedule4, 
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            barThickness: 30,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true },
                        x: {
                            ticks: {
                                maxRotation: 0, // Ensure state names are always readable
                                autoSkip: false, // Don't skip labels
                                padding: 30, // Add padding to prevent overlap with the x-axis
                            },
                        },
                    },
                    plugins: {
                        datalabels: {
                            display: function (context) {
                                // Only show data labels if value is not 0
                                return context.dataset.data[context.dataIndex] !== null;
                            },
                            color: '#000',
                            font: {
                                weight: 'bold', 
                                size: 12,
                            },
                            formatter: (value) => value === null ? '' : value, // Ensure zero values are removed
                            anchor: 'end',
                            align: 'bottom',
                            offset: 5,
                        },
                    },
                    layout: {
                        padding: {
                            top: 10,
                            bottom: 40, // Increase bottom padding for state name visibility
                        }
                    },
                    minBarLength: 2,   // Ensure bars are visible even for small values
                },
                plugins: [ChartDataLabels], // Register the plugin
            });
        };

        updateChart($('#yearSelect').val());
        $('#yearSelect').change(function () {
            updateChart($(this).val());
        });
    });

</script>



<!-- Issue Bar Graph -->
<script>
    Chart.register(ChartDataLabels);
    $(document).ready(function () {
        let chartInstance;

        // Assuming the backend has passed the issue counts as 'issueCounts'
        const issueData = @json($issueCounts); // This will pass the data from the backend to the JS
        
        const updateChart = (year) => {
            // Filter the data by the selected year
            const filteredData = issueData.filter(data => data.year == year);
            
            // Prepare the data for the chart
            const issues = ['GFI', 'RFA', 'Both', 'None', 'Others'];
            const data = issues.map(issue => {
                const issueData = filteredData.find(item => item.issue === issue);
                return issueData ? issueData.total : 0; // If no data, set to 0
            });

            const labels = issues; // Issue names

            if (chartInstance) chartInstance.destroy();

            const ctx = document.getElementById('yearwiseIssueChart').getContext('2d');
            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Issues',
                            data: data,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        },
                    ],
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true },
                        x: {
                            ticks: {
                                maxRotation: 0, // Ensure issue names are always readable
                                autoSkip: false, // Don't skip labels
                            },
                        },
                    },
                    plugins: {
                        datalabels: {
                            display: function (context) {
                                return context.dataset.data[context.dataIndex] !== 0;
                            },
                            color: '#000',
                            font: {
                                weight: 'bold',
                                size: 12,
                            },
                            anchor: 'end',
                            align: 'bottom',
                            offset: 5,
                        },
                    },
                    layout: {
                        padding: {
                            top: 10,
                            bottom: 40,
                        },
                    },
                    minBarLength: 2,
                },
                plugins: [ChartDataLabels], // Register the plugin
            });
        };

        updateChart($('#yearIssueSelect').val()); // Initially load the chart with the selected year

        $('#yearIssueSelect').change(function () {
            updateChart($(this).val());
        });
    });
</script>




<script>
    var sequentialCtx = document.getElementById('sequentialPieChart').getContext('2d');
    var sequentialPieData = {
        labels: @json($sequentialInspectionData->pluck('year')), 
        datasets: [{
            data: @json($sequentialInspectionData->pluck('total')), 
            backgroundColor: ['#FF5733', '#33FF57', '#3357FF', '#F1C40F', '#9B59B6'], 
        }]
    };

    new Chart(sequentialCtx, {
        type: 'pie',
        data: sequentialPieData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: {
                        font: {
                            size: 10
                        }
                    }
                },
                datalabels: {
                    display: true,  // Make sure the labels are displayed
                    formatter: (value) => value,  // Show the value
                    color: '#000', // Text color for the labels
                    font: { weight: 'bold', size: 14 }, // Label font styling
                    anchor: 'center',  // Position label at the center of each segment
                    align: 'center',  // Align the label centrally in the segment
                    offset: 5, // Offset the label a bit if needed
                }
            }
        },
        plugins: [ChartDataLabels]  // Register the plugin
    });




    var nonSequentialCtx = document.getElementById('nonSequentialPieChart').getContext('2d');
    var nonSequentialPieData = {
        labels: @json($nonSequentialInspectionData->pluck('year')),
        datasets: [{
            data: @json($nonSequentialInspectionData->pluck('total')),
            backgroundColor: ['#3498DB', '#E74C3C', '#2ECC71', '#9B59B6', '#F39C12'],
        }]
    };

    new Chart(nonSequentialCtx, {
        type: 'pie',
        data: nonSequentialPieData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: { font: { size: 10 } }
                },
                datalabels: {
                    display: true,
                    formatter: (value) => value,
                    color: '#000',
                    font: { weight: 'bold', size: 14 },
                    anchor: 'center',
                    align: 'center',
                    offset: 5,
                }
            }
        },
        plugins: [ChartDataLabels]
    });
</script>

<script>
    $(document).ready(function() {
        let chartInstance; 
        const pieData = @json($pieChartData); 
        const selectedYear = $('#yearSelect').val();

        function updatePieChart(year) {
    const filteredData = pieData.filter(data => data.year == year);
    if (filteredData.length === 0) {
        return;
    }

    // Keep original data for total display
    const originalSchedule1 = filteredData[0].schedule_1;
    const originalSchedule2 = filteredData[0].schedule_2;
    const originalSchedule3 = filteredData[0].schedule_3;
    const originalSchedule4 = filteredData[0].schedule_4;

    // Replace 0 values with a small value (e.g., 0.1) for chart rendering
    const schedule1 = originalSchedule1 || 0.1;
    const schedule2 = originalSchedule2 || 0.1;
    const schedule3 = originalSchedule3 || 0.1;
    const schedule4 = originalSchedule4 || 0.1;

    if (chartInstance) {
        chartInstance.destroy();
    }

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
                legend: { 
                    labels: { 
                        font: { size: 10 }
                    }
                },
                datalabels: {
                    display: true,  // Make sure the labels are displayed
                    formatter: (value) => value,  // Show the value
                    color: '#000', // Text color for the labels
                    font: { weight: 'bold', size: 14 }, // Label font styling
                    anchor: 'center',  // Position label at the center of each segment
                    align: 'center',  // Align the label centrally in the segment
                    offset: 5, // Offset the label a bit if needed
                }
            }
        },
        plugins: [ChartDataLabels]  // Register the plugin
    });

    // Now, when you display the total for each section (outside the chart)
    // Display the original zero value for total records
    const updateTotal = (id, value) => {
        const totalElement = document.getElementById(id);
        if (totalElement) {
            totalElement.textContent = value || 0;
        }
    };

    updateTotal('schedule1Total', originalSchedule1);
    updateTotal('schedule2Total', originalSchedule2);
    updateTotal('schedule3Total', originalSchedule3);
    updateTotal('schedule4Total', originalSchedule4);
}


        updatePieChart(selectedYear);
        $('#pieYearSelect').on('change', function() {
            const selectedYear = $(this).val();
            updatePieChart(selectedYear);
        });
    });
</script>


<script>
// Reusable print function
function printChart(chartId) {
    var chartElement = document.getElementById(chartId);

    if (!chartElement) {
        alert("Data not available.");
        return;
    }

    var chartImage = chartElement.toDataURL("image/png");

    var printWindow = window.open('', '', 'height=600,width=800');

    if (!printWindow) {
        alert("Unable to open print dialog. Please check your browser settings.");
        return;
    }

    printWindow.document.write('<html><head><title>Data Print</title>');
    printWindow.document.write('<style>body { font-family: Arial, sans-serif; margin: 20px; }</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h3>Data Print</h3>');
    printWindow.document.write('<img src="' + chartImage + '" style="max-width: 100%;">');
    printWindow.document.write('</body></html>');
    printWindow.document.close();

    printWindow.onload = function() {
        printWindow.print();
        printWindow.onafterprint = function () {
            printWindow.close();
        };
    };

    return false;
}
</script>

@endpush