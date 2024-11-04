@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4 table-responsive">
    <h1>List of Inspector</h1>

   
    <!-- Inspector List Table -->
    <table class="table table-bordered table-striped" id="myTable" data-export-columns="0,1,2,3,4">
        <div class="mb-3 d-flex " style="float: right;">
            <select id="pageLengthSelect" class="form-control ml-3" style="width: 80px;">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="all">All</option>
            </select>
        </div>

        <thead class="thead-dark">
            <tr>
                <th scope="col" class="text-center">S.No.</th>
                <th scope="col">Name</th>
                <th scope="col" class="text-center">Designation</th>
                <th scope="col" class="text-center">Passport Number</th>
                <th scope="col" class="text-center">Remarks</th>
                <th scope="col" class="text-center">Created At</th>
                <th scope="col" class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody id="inspectorTableBody">

            @if($inspectorsData->isEmpty())
            <tr>
                <td colspan="6" class="text-center">No inspectors available.</td>
            </tr>
            @else


            @foreach($inspectorsData as $inspector)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                
                
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>


</div>
@endsection
