@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    @php
    // Calculate the number of categories
    $categoryCount = count($categories);
    // Generate the dynamic export columns
    $exportColumns = implode(',', range(0, $categoryCount + 1)); // +1 for the country column
    @endphp

    <!-- Nationality Wise Report Inspectors Table -->
    <table class="table table-bordered table-striped" id="myTable" data-export-columns="{{ $exportColumns }}">
        <select id="pageLengthSelect" class="form-control mb-3 float-right">
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="all">All</option>
        </select>
        <thead >
            <tr class="back-cyan">
                <th scope="col" class="text-center">Sl. No.</th>
                <th scope="col" class="text-center">Nationality</th>
                @foreach($categories as $category)
                    <th scope="col" class="text-center">{{ str_replace('Ã¢â‚¬â€œ', '–', $category->category_name) }}</th>
                @endforeach
                <th scope="col" class="text-center">Total</th>  <!-- Added Total column -->
            </tr>
        </thead>
        <tbody>
            @foreach($finalData as $index => $inspector)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td >
                    <a href="{{ route('inspections.byCountry', ['country' => $inspector['country']]) }}">
                        {{ $inspector['country'] }}
                    </a>
                </td>
                @php
                $totalInspectors = 0;
                @endphp
                @foreach($categories as $category)
                    <td class="text-center">{{ str_replace('Ã¢â‚¬â€œ', '–', $inspector[$category->category_name] ?? 0) }}</td>
                    @php
                    $totalInspectors += $inspector[$category->category_name] ?? 0;
                    @endphp
                @endforeach
                <td class="text-center">{{ $totalInspectors }}</td> <!-- Display the total -->
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
