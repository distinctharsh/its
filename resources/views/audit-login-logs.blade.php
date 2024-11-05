@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <h1>Audit Login Logs</h1>

    @if($auditTrails->isEmpty())
        <p>No audit trails found.</p>
    @else
    @endif
    <table class="table table-bordered table-striped" id="myTable" data-export-columns="0,1">
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
                <th scope="col">id</th>
                <th scope="col">username</th>
                <th scope="col">status</th>
                <th scope="col">ip_addr</th>
                <th scope="col">action_details</th>
                <th scope="col">created_at</th>
            </tr>
        </thead>
        <tbody>
            @foreach($auditTrails as $index => $audit)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $audit->id }}</td>
                    <td>{{ $audit->username }}</td>
                    <td>{{ $audit->status }}</td>
                    <td>{{ $audit->ip_addr }}</td>
                    <td>{{ $audit->action_details }}</td>
                    <td>{{ $audit->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


</div>
@endsection
