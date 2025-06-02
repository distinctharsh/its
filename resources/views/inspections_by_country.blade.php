@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <!-- <h1>Inspections for {{ $country }}</h1> -->

    <div class="text-left mb-3">
        <button type="button" class="btn back-btn" onclick="window.location='{{ route('manageReport') }}'">Back</button>
    </div>


    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Inspector</th>
                <th scope="col">Category</th>
                <th scope="col">Date</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            @if($inspections->isEmpty())
                <tr>
                    <td colspan="4" class="text-center">No inspections found for this country.</td>
                </tr>
            @else
                @foreach($inspections as $inspection)
                <tr>
                    <td>{{ $inspection->inspector->name ?? 'N/A' }}</td>
                    <td>{{ str_replace('Ã¢â‚¬â€œ', '–', $inspection->category->category_name ?? 'N/A') }}</td> <!-- Correct access to category_name -->

                    <td>{{ \Carbon\Carbon::parse($inspection->date_of_joining)->format('d-m-Y') }}</td>

                    <td>{{ $inspection->status->status_name ?? 'N/A' }}</td> <!-- Accessing status name -->
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection
