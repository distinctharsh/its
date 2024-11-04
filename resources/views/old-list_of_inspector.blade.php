@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4 table-responsive">
    <h1>List of Inspectors</h1>
    <table class="table table-bordered table-striped" id="myTable" data-export-columns="0,1,2,3,4">
        <div class="mb-3 d-flex" style="float: right;">
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
                <th scope="col">S.No.</th>
                <th scope="col">Name</th>
                <th scope="col">Gender</th>
                <th scope="col">Date of Birth</th>
                <th scope="col">Nationality</th>
                <th scope="col">Passport Number</th>
                <th scope="col">UNLP Number</th>
                <th scope="col">Rank</th>
                <th scope="col">Qualifications</th>
                <th scope="col">Professional Experience</th>
                <th scope="col">Clearance Certificate</th>
                <th scope="col">Remarks</th>
                <th scope="col">Active</th>
                <th scope="col">Visits Count</th>
                <th scope="col">Details</th>
            </tr>
        </thead>
        <tbody id="inspectorListTableBody">
            @if($inspectorsData->isEmpty())
            <tr>
                <td colspan="15" class="text-center">No inspectors available.</td>
            </tr>
            @else
            @foreach($inspectorsData as $index => $inspector)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $inspector['name'] }}</td>
                <td>{{ $inspector['gender'] }}</td>
                <td>{{ $inspector['dob'] }}</td>
                <td>{{ $inspector['nationality'] }}</td>
                <td>{{ $inspector['passport_number'] }}</td>
                <td>{{ $inspector['unlp_number'] }}</td>
                <td>{{ $inspector['rank'] }}</td>
                <td>{{ $inspector['qualifications'] }}</td>
                <td>{{ $inspector['professional_experience'] }}</td>
                <td>{{ $inspector['clearance_certificate'] }}</td>
                <td>{{ $inspector['remarks'] }}</td>
                <td>{{ $inspector['is_active'] ? 'Yes' : 'No' }}</td>
                <td>{{ $inspector['visits_count'] }}</td>
                <td>
                    <button class="btn btn-info btn-sm" data-toggle="collapse" data-target="#details-{{ $index }}" aria-expanded="false" aria-controls="details-{{ $index }}">
                        View Details
                    </button>
                </td>
            </tr>
            <tr id="details-{{ $index }}" class="collapse">
                <td colspan="15">
                    <div class="card card-body">
                        <!-- Inspections Details -->
                        @if($inspector['inspections']->isNotEmpty())
                        <h5>Inspections</h5>
                        <table class="table table-bordered mt-2">
                            <thead>
                                <tr>
                                    <th>Inspection ID</th>
                                    <th>Category</th>
                                    <th>Date of Joining</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inspector['inspections'] as $inspection)
                                <tr>
                                    <td>{{ $inspection->id }}</td>
                                    <td>{{ $inspection->category->category_name ?? 'N/A' }}</td>
                                    <td>{{ $inspection->date_of_joining }}</td>
                                    <td>{{ $inspection->status->status_name ?? 'N/A' }}</td>
                                    <td>{{ $inspection->remarks }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif

                        <!-- Visits Details -->
                        @if($inspector['visits']->isNotEmpty())
                        <h5>Visits</h5>
                        <table class="table table-bordered mt-2">
                            <thead>
                                <tr>
                                    <th>Visit ID</th>
                                    <th>Purpose of Visit</th>
                              
                                    <th>Point of Entry</th>
                                    <th>Arrival Datetime</th>
                                    <th>Departure Datetime</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inspector['visits'] as $visit)
                                <tr>
                                    <td>{{ $visit->id }}</td>
                                    <td>{{ $visit->purpose_of_visit }}</td>
                                
                                    <td>{{ $visit->point_of_entry }}</td>
                                    <td>{{ $visit->arrival_datetime }}</td>
                                    <td>{{ $visit->departure_datetime }}</td>
                                    <td>{{ $visit->remarks }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection
