@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <h1 class="mb-4">Visit Details</h1>

    <div class="text-left mb-3">
        <button type="button" class="btn back-btn" onclick="window.location='{{ route('manageVisit') }}'">Back</button>
    </div>

    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ $visit->teamLead ? $visit->teamLead->name : 'Details' }}</h5>
            @if(!auth()->user()->hasRole('Viewer'))
            @if(auth()->user()->hasRole('Admin'))
            <a href="{{ route('editVisit', $visit->id) }}" class="btn btn-primary btn-edit">
                <i class="fa-solid fa-pen-to-square"></i> Edit
            </a>
            @else
            @if($visit->deleted_at != null)
            <span class="btn btn-primary disabled" title="Cannot edit deleted visit">
                <i class="fa-solid fa-pen-to-square"></i> Edit
            </span>
            @else
            <a href="{{ route('editVisit', $visit->id) }}" class="btn btn-primary btn-edit">
                <i class="fa-solid fa-pen-to-square"></i> Edit
            </a>
            @endif
            @endif
            @endif
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped">
                <tbody>
                    @if($visit->teamLead)
                    <tr>
                        <th>Team Lead</th>
                        <td>{{ $visit->teamLead->name }}</td>
                    </tr>
                    @endif

                    @if($escortOfficers)
                    <tr>
                        <th>Escort Officers</th>
                        <td>{{ implode(', ', $escortOfficers) }}</td>
                    </tr>
                    @endif

                    @if($inspectors)
                    <tr>
                        <th>List of Inspectors</th>
                        <td>{{ implode(', ', $inspectors) }}</td>
                    </tr>
                    @endif

                    @if($visit->inspection_type_selection)
                    <tr>
                        <th>Inspection Category</th>
                        <td>{{ ucfirst($visit->inspection_type_selection) }}</td>
                    </tr>
                    @endif

                    @if($visit->inspectionCategoryType)
                    <tr>
                        <th>Inspection Sub Category Type</th>
                        <td>{{ $visit->inspectionCategoryType->type_name }}</td>
                    </tr>
                    @endif

                    @if($visit->siteMappings->isNotEmpty())
                    <tr>
                        <th>Site Code</th>
                        <td>
                            @php
                            $siteCodesList = $visit->siteMappings->map(function ($siteMapping) use ($siteCodes) {
                            return $siteCodes->firstWhere('id', $siteMapping->site_code_id)->site_code ?? 'N/A';
                            })->filter(); // Filter out any N/A values
                            @endphp

                            {{ $siteCodesList->implode(', ') }}
                        </td>
                    </tr>

                    <tr>
                        <th>Name and Address of Site of Inspection</th>
                        <td>
                            @foreach($visit->siteMappings as $siteMapping)
                            <p>{{ $siteMapping->site_of_inspection ?: 'N/A' }}<br>

                            </p>
                            @endforeach
                        </td>
                    </tr>

                    <tr>
                        <th>State</th>
                        <td>
                            @php
                            $stateNames = $visit->siteMappings->map(function ($siteMapping) use ($states) {
                            return $states->firstWhere('id', $siteMapping->state_id)->state_name ?? 'N/A';
                            })->filter(); // Filter out any N/A values
                            @endphp

                            {{ $stateNames->implode(', ') }}
                        </td>
                    </tr>
                    @endif

                    @if($visit->category)
                    <tr>
                        <th>Visit Category</th>
                        <td>{{ $visit->category->category_name }}</td>
                    </tr>
                    @endif

                    @if($visit->inspectionType)
                    <tr>
                        <th>Inspection Type</th>
                        <td>{{ $visit->inspectionType->type_name }}</td>
                    </tr>
                    @endif

                    @if($visit->arrival_datetime)
                    <tr>
                        <th>Arrival Date & Time</th>
                        <td>{{ $visit->arrival_datetime->format('d M Y H:i') }}</td>
                    </tr>
                    @endif

                    @if($visit->departure_datetime)
                    <tr>
                        <th>Departure Date & Time</th>
                        <td>{{ $visit->departure_datetime->format('d M Y H:i') }}</td>
                    </tr>
                    @endif

                    <tr>
                        <th>Upload Document</th>
                        <td>
                            @if($visit->clearance_certificate)
                            <a href="{{ asset('storage/app/' . $visit->clearance_certificate) }}" target="_blank">View Document</a>
                            @else
                            <span>No Uploaded Document Available</span>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>Visit Report</th>
                        <td>
                            @if($visit->visit_report)
                            <a href="{{ asset('storage/app/' . $visit->visit_report) }}" target="_blank">View Report</a>
                            @else
                            <span>No Report Available</span>
                            @endif
                        </td>
                    </tr>

                    @if($visit->remarks)
                    <tr>
                        <th>Remarks</th>
                        <td>{{ $visit->remarks }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection