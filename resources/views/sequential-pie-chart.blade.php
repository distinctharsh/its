@extends('layouts.layout')

@section('content')
<div class="container my-5">
    <h2 class="text-center">Sequential Inspections for Last 5 Years</h2>
    
    <!-- Dropdown to Select Year (Optional) -->
    <!-- <form method="GET" action="{{ route('yearSequentialPieChart') }}" class="mb-4 text-center">
        <label for="year" class="mr-2">Select Year:</label>
        <select name="year" id="year" onchange="this.form.submit()" class="form-control d-inline-block w-auto">
            @foreach ($years as $year)
                <option value="{{ $year->year }}" {{ $year->year == $selectedYear ? 'selected' : '' }}>
                    {{ $year->year }}
                </option>
            @endforeach
        </select>
    </form> -->

    <!-- Pie Chart with Smaller Size -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Specify width and height for the pie chart -->
            <canvas id="sequentialPieChart" width="300" height="300"></canvas>

        </div>
    </div>
</div>
@endsection



@push('style')
<style>
 #sequentialPieChart {
    max-width: 300px;   /* Maximum width */
    max-height: 300px;  /* Maximum height */
    width: 100%;        /* Adjust based on screen size */
    height: auto;       /* Auto height adjustment */
}

.canvas-container {
    width: 100%;   /* Ensure the parent container takes up 100% width */
    max-width: 350px;  /* Restrict the container size */
    height: 300px;  /* Set a fixed height */
    margin: 0 auto;  /* Center the container */
}

</style>
@endpush

@push('script')
<script src="{{ url('assets/vendor/buttons/chart.js') }}"></script>

<script>
    var ctx = document.getElementById('sequentialPieChart').getContext('2d');

    // Data for the pie chart
    var pieData = {
        labels: @json($sequentialInspectionData->pluck('year')), // Years (from the data)
        datasets: [{
            data: @json($sequentialInspectionData->pluck('total')), // Total inspections for each year
            backgroundColor: ['#FF5733', '#33FF57', '#3357FF', '#F1C40F', '#9B59B6'], // Colors for each segment
        }]
    };

    var pieChart = new Chart(ctx, {
        type: 'pie', // Pie chart type
        data: pieData,
    });
</script>
@endpush
