@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <!-- <h1 class="mb-4">Visit Details</h1> -->

    <div class="text-left mb-3">
        <button type="button" class="btn back-btn" onclick="window.location='{{ route('manageVisit') }}'">Back</button>
    </div>

    <div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ $visit->teamLead ? $visit->teamLead->name : 'Details' }}</h5>

            @php
                $isLocked = isset($visitLock) &&
                            $visitLock->locked &&
                            $visit->created_at >= $visitLock->from &&
                            $visit->created_at <= $visitLock->to;
            @endphp


            <!-- Ensure that the button is aligned right -->
            @if(!auth()->user()->hasRole('Viewer'))
                @if(auth()->user()->hasRole('Admin'))
                     @if($isLocked)
                        <span class="btn btn-primary disabled ml-auto" title="Locked during this period">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </span>
                    @else
                        <a href="{{ route('editVisit', $visit->id) }}" class="btn btn-primary btn-edit ml-auto">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </a>
                    @endif
                @else
                    @if($visit->deleted_at != null)
                        <span class="btn btn-primary disabled ml-auto" title="Cannot edit deleted visit">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </span>
                    @elseif($isLocked)
                        <span class="btn btn-primary disabled ml-auto" title="Locked during this period">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </span>
                    @else
                        <a href="{{ route('editVisit', $visit->id) }}" class="btn btn-primary btn-edit ml-auto">
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

                    @if($visit->inspectionProperties)
                    <tr>
                        <th>Inspection Type</th>
                        <td>{{ ucfirst($visit->inspectionProperties->name) }}</td>
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
                        <th>Plant Site</th>
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

                    @if($visit->point_of_entry)
                    <tr>
                        <th>Point Of Entry</th>
                        <td>
                            @php
                                $pointOfEntries = json_decode($visit->point_of_entry, true);
                            @endphp

                            @if(is_array($pointOfEntries) && count($pointOfEntries) > 0)
                                @foreach($pointOfEntries as $point)
                                    @php
                                        $entry_exit_points = $entry_exit_points->firstWhere('id', $point);
                                    @endphp
                                    {{ $entry_exit_points ? $entry_exit_points->point_name : 'Unknown State' }}@if(!$loop->last), @endif
                                @endforeach
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                    @endif

                    @if($visit->point_of_entry)
                    <tr>
                        <th>Point Of Exit</th>
                        <td>
                            @php
                                $pointOfExits = json_decode($visit->point_of_exit, true);
                            @endphp

                            @if(is_array($pointOfExits) && count($pointOfExits) > 0)
                                @foreach($pointOfExits as $point)
                                    @php
                                        $entry_exit_points = $entry_exit_points->firstWhere('id', $point);
                                    @endphp
                                    {{ $entry_exit_points ? $entry_exit_points->point_name : 'Unknown State' }}@if(!$loop->last), @endif
                                @endforeach
                            @else
                                N/A
                            @endif
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
                        <th>Inspection category</th>
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
                    
                    @if($visit->is_closed == 0 || $visit->is_closed == 1)
                    <tr>
                        <th>Closed/Open Status</th>
                        <td>{{ $visit->is_closed == 0 ? 'Open' : 'Closed' }}</td>
                    </tr>
                    @endif

                    @if($visit->documentNumber)
                    <tr>
                        <th>Document Number</th>
                        <td>{{ $visit->documentNumber->fax_number }}</td>
                    </tr>
                    @endif

                    <tr>
                        <th>Final Inspection Report</th>
                        <td>
                            @if($visit->clearance_certificate)
                            <a href="{{ url('storage/app/' . $visit->clearance_certificate) }}" target="_blank">View Document</a>
                            @else
                            <span>No Final Inspection Report Available</span>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>Preliminary Report </th>
                        <td>
                            @if($visit->visit_report)
                            <a href="{{ url('storage/app/' . $visit->visit_report) }}" target="_blank">View Report</a>
                            @else
                            <span>No Preliminary Report Available</span>
                            @endif
                        </td>
                    </tr>

                    @if($visit->acentric_report)
                    <tr>
                        <th>Action Taken Report</th>
                        <td>{{ $visit->acentric_report }}</td>
                    </tr>
                    @endif

                    @if($visit->to_the_points_comment)
                    <tr>
                        <th>To the Points Comment</th>
                        <td>{{ $visit->to_the_points_comment }}</td>
                    </tr>
                    @endif

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