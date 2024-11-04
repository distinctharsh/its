@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <h1>Country Wise Report</h1>

    @php
    // Calculate the number of categories
    $categoryCount = count($categories);
    // Generate the dynamic export columns
    $exportColumns = implode(',', range(0, $categoryCount + 1)); // +1 for the country column
    @endphp

    <!-- Country Wise Report Inspectors Table -->
    <table class="table table-bordered table-striped" id="myTable" data-export-columns="{{ $exportColumns }}">
        <select id="pageLengthSelect" class="form-control mb-3 float-right" style="width: 80px;">
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="all">All</option>
        </select>
        <thead class="thead-dark">
            <tr>
                <th scope="col">S.No.</th>
                <th scope="col">Country</th>
                @foreach($categories as $category)
                <th scope="col">{{ $category->category_name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>

            @foreach($finalData as $index => $inspector)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <a href="{{ route('inspections.byCountry', ['country' => $inspector['country']]) }}">
                        {{ $inspector['country'] }}
                    </a>
                </td>
                @php
                $totalInspectors = 0;
                @endphp
                @foreach($categories as $category)
                <td>{{ $inspector[$category->category_name] ?? 0 }}</td>
                @php
                $totalInspectors += $inspector[$category->category_name] ?? 0;
                @endphp
                @endforeach

            </tr>
            @endforeach
        </tbody>
    </table>





</div>
@endsection
