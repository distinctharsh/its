@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <!-- <h1>Visit Details Management</h1> -->

    <!-- Button to trigger modal -->
    <!-- <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addVisitModal"  >
        Add New Record
    </button> -->
    @if(!auth()->user()->hasRole('Viewer'))
    <a href="{{ route('addVisit') }}" class="btn btn-primary mb-3 float-right" data-toggle="tooltip" data-placement="left" title="Add"><i class="fa-solid fa-plus"></i></a>
    @endif

    @if(auth()->user()->hasRole('Admin'))
    <div class="btn-group mb-3 ml-2" style="display:inline-block;">
        <select id="bulkVisitActionSelect" class="form-control">
            <option value="">Bulk Actions</option>
            <option value="approve">Approve</option>
            <option value="reject">Reject</option>
            <option value="revert">Revert</option>
        </select>
        <button id="bulkVisitActionApplyBtn" class="btn btn-primary ml-2">Apply</button>
    </div>
@endif

    <br>
    <br>
    <!-- Visit Details Table -->
    <table class="table table-bordered table-striped table-responsive" id="myTable">
        <div class=" center-block expand-box text-white mb-3">
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingOne">
                        <h5 class="panel-title">
                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne" class="btn-filter">
                                <strong>Filter</strong>
                            </a>
                        </h5>
                    </div>

                    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                        <div class="panel-body">
                            <div class="row justify-content-between align-items-center">
                                <div class="col-md-6 mb-3 px-3 mt-3">
                                    <div class="d-flex align-items-center">
                                        <p class="text-dark mb-0 mr-2"><strong>Arrival Date :</strong></p>
                                        <input type="text" id="startDate" class="form-control datepicker" placeholder="From" style="width:auto; margin-right:10px;">
                                        <input type="text" id="endDate" class="form-control datepicker" placeholder="To" style="width:auto;">
                                    </div>
                                </div>

                                <div class="col-md-5 mb-3 d-flex justify-content-start px-3 mt-3">
                                    <p class="text-dark mb-0 mr-2"><strong>Status :</strong></p>
                                    <div class="form-check mr-3">
                                        <input class="form-check-input" type="radio" name="statusFilter" id="statusAll" value="all">
                                        <label class="form-check-label text-dark" for="statusAll">All</label>
                                    </div>
                                    <div class="form-check mr-3">
                                        <input class="form-check-input" type="radio" name="statusFilter" id="statusActive" value="active" checked>
                                        <label class="form-check-label text-dark" for="statusActive">Active</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="statusFilter" id="statusInactive" value="inactive">
                                        <label class="form-check-label text-dark" for="statusInactive">Inactive</label>
                                    </div>

                                    @if(strtolower(auth()->user()->role->name) === 'admin' || strtolower(auth()->user()->role->name) === 'user')
                                            <div class="form-check mr-3">
                                                <input class="form-check-input" type="radio" name="statusFilter" id="statusDraft" value="draft">
                                                <label class="form-check-label text-dark" for="statusDraft">Pending for Approval</label>
                                            </div>

                                             <div class="form-check mr-3">
                                                <input class="form-check-input" type="radio" name="statusFilter" id="statusReverted" value="reverted">
                                                <label class="form-check-label text-dark" for="statusReverted">Reverted to the User</label>
                                            </div>
                                        @endif
                                </div>
                            </div>
                            <div class="text-center mb-3">

                                <button id="filterDate" class="btn btn-primary ml-3 justify-content-center mb-3">Search</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <select id="pageLengthSelect" class="form-control mb-3">
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="all">All</option>
        </select>

        <thead class="thead-dark">
            <tr>

                @if($visits->isEmpty())
                <th scope="col" class="text-center">
                    Records
                </th>
                @else
                <th scope="col" class="text-center">Sl. No.</th>
                <th scope="col" class="text-center">Record ID</th>
                <th scope="col" class="text-center">Document No. / Ref. No.</th>
                <th scope="col" class="text-center">Receipt Date</th>
                <th scope="col" class="text-center">Inspection Types</th>
                <th scope="col" class="text-center">Inspection Category</th>
                <th scope="col" class="text-center">Inspection Phase </th>
                <th scope="col" class="text-center">Visit Category</th>
                <th scope="col" class="text-center">Inspection Sub Category </th>
                <th scope="col" class="text-center">Plant Site Code</th>
                <th scope="col" class="text-center">Name & Address of the Facility</th>
                <th scope="col" class="text-center">State</th>
                <th scope="col" class="text-center">OPCW Team Leader</th>
                <th scope="col" class="text-center">List of OPCW Inspectors</th>
                <th scope="col" class="text-center">Point of Entry</th>
                <th scope="col" class="text-center">Arrival Date and Time</th>
                <th scope="col" class="text-center">Escort Officer at Point of Entry</th>
                <th scope="col" class="text-center">Point of Exit</th>
                <th scope="col" class="text-center">Departure Date and Time</th>
                <th scope="col" class="text-center">Escort Officer at Point of Exit</th>
                <th scope="col" class="text-center">Escort Officers (Inspection Site)</th>
                <th scope="col" class="text-center">Issues/Recommendations</th>
                <th scope="col" class="text-center">Action Taken Report</th>
                <th scope="col" class="text-center">Remarks</th>
                <th scope="col" class="text-center">Created At</th>
                <th scope="col" class="text-center actions-column">Actions</th>
                @endif

            </tr>
        </thead>
        <tbody id="visitTableBody">
            @if($visits->isEmpty())
            <tr>
                <td colspan="6" class="text-center">No visit available.</td>
            </tr>
            @else
            @foreach($visits as $visit)
            @foreach($visit->siteMappings as $siteMapping)
            <!-- <tr data-status="{{ is_null($visit->deleted_at) ? 'active' : 'inactive' }}"
                data-join-date="{{ $visit->arrival_datetime ? $visit->arrival_datetime->format('Y-m-d') : '' }}"> -->

               <tr data-status="{{ $visit->is_reverted ? 'reverted' : ($visit->is_draft ? 'draft' : (is_null($visit->deleted_at) ? 'active' : 'inactive')) }}"
    data-join-date="{{ $visit->arrival_datetime ? $visit->arrival_datetime->format('Y-m-d') : '' }}">


                <td class="text-center">
                    {{ $loop->iteration ? $loop->iteration : '' }}
                </td>

                 <td class="text-center">
                    {{ $visit->id }}
                </td>

                <td class="text-center">
                    @if(auth()->user()->hasRole('Admin'))
                        <input type="checkbox" class="visit-checkbox" value="{{ $visit->id }}">
                    @endif
                    @if($visit->documentNumber)
                    {{ $visit->documentNumber->fax_number }}
                    @else
                    N/A
                    @endif
                </td>

                <td>
                    @if($visit->documentNumber)
                    {{ $visit->documentNumber->fax_date->format('d M Y') }}
                    @else
                    N/A
                    @endif
                </td>

                <td class="text-center">
                    {{ $visit->inspectionProperties ? $visit->inspectionProperties->name : '' }}

                </td>
                <td class="text-center">
                    {{ $siteMapping->inspection_category_id ? $siteMapping->inspectionType->type_name : '' }}
                </td>
                <td class="text-center">
                    @php
                    $phase = $inspection_phases->firstWhere('id', $siteMapping->inspection_phase_id)->phase_type_name ?? 'N/A';
                    @endphp
                    <div>{{ $phase }}</div>
                    <br>
                </td>
                <td class="text-center">{{ $visit->category ? $visit->category->category_name : '' }}</td>
                <td class="text-center">

                    @if($visit->inspectionCategoryType)
                    @if($visit->inspectionCategoryType->type_name)
                    {{ $visit->inspectionCategoryType->type_name }}

                    @endif
                    @endif
                </td>

                <td class="text-center">
                    @php
                    $sitecode = $site_codes->firstWhere('id', $siteMapping->site_code_id)->site_code ?? 'N/A';
                    @endphp
                    <div>{{ $sitecode }}</div>
                </td>

                <td class="text-center">
                    @php
                    $stateName = $states->firstWhere('id', $siteMapping->state_id)->state_name ?? 'N/A';
                    @endphp

                    <div>{{ $siteMapping->site_of_inspection }}</div>

                </td>

                <td class="text-center">
                    @php
                    $stateName = $states->firstWhere('id', $siteMapping->state_id)->state_name ?? 'N/A';
                    @endphp
                    <div>{{ $stateName}}</div><br>
                </td>



                <td class="text-center">
                    {{$visit->teamLead ? $visit->teamLead->name : ''}}


                    <div class="pdf-icons-container mt-2">
                        @if($visit->clearance_certificate)
                        <a href="{{ url('storage/app/' . $visit->clearance_certificate) }}" target="_blank" class="pdf-icon" data-toggle="tooltip" title="Preliminary Report">
                            <i class="fa-solid fa-book"></i>
                        </a>
                        @else
                        <!-- <span class="pdf-icon disabled" data-toggle="tooltip" title="No Upload Document Available">
                            <i class="fa-solid fa-file-pdf"></i>
                        </span> -->
                        @endif

                        @if($visit->visit_report)
                        <a href="{{ url('storage/app/' . $visit->visit_report) }}" target="_blank" class="pdf-icon" data-toggle="tooltip" title="Final Inspection Report">
                            <i class="fa-solid fa-file-pdf"></i>
                        </a>
                        @else
                        <!-- <span class="pdf-icon disabled" data-toggle="tooltip" title="No Visit Report Available">
                            <i class="fa-solid fa-file-pdf"></i>
                        </span> -->
                        @endif
                    </div>


                </td>


                <td class="text-center">
                    @php
                    // Decode list_of_inspectors if it's still stored as a JSON string
                    $inspectorsIds = is_array($visit->list_of_inspectors) ? $visit->list_of_inspectors : json_decode($visit->list_of_inspectors, true);

                    // Check if the inspectors' IDs are valid before querying the database
                    $inspectors = collect();
                    if (is_array($inspectorsIds) && !empty($inspectorsIds)) {
                    $inspectors = App\Models\Inspector::whereIn('id', $inspectorsIds)->get();
                    }
                    @endphp

                    @if($inspectors->isNotEmpty())
                    @foreach($inspectors as $inspector)
                    {{ $inspector->name }}<br> <br> <!-- Line break after each inspector's name -->
                    @endforeach
                    @else
                    N/A
                    @endif
                </td>





                <td class="text-center">
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


                <td class="text-center">{{ $visit->arrival_datetime ? $visit->arrival_datetime->format('d-m-Y h:i A') : '' }}</td>

                <td class="text-center">
                    @php
                    $escortOfficersIds = is_array($visit->escort_officers_poe) ? $visit->escort_officers_poe : json_decode($visit->escort_officers_poe, true);
                    $escortOfficers = App\Models\EscortOfficer::whereIn('id', $escortOfficersIds)->get();
                    @endphp

                    @if($escortOfficers->isNotEmpty())
                    @foreach($escortOfficers as $escortOfficer)
                    {{ $escortOfficer->officer_name }}<br><br> <!-- Accessing the officer_name field -->
                    @endforeach
                    @else
                    N/A
                    @endif
                </td>


                <td class="text-center">
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




                <td class="text-center">{{ $visit->departure_datetime ? $visit->departure_datetime->format('d-m-Y h:i A') : '' }}</td>


                <td class="text-center">
                    @php
                    $escortOfficersIds = is_array($visit->escort_officers_poe) ? $visit->escort_officers_poe : json_decode($visit->escort_officers_poe, true);
                    $escortOfficers = App\Models\EscortOfficer::whereIn('id', $escortOfficersIds)->get();
                    @endphp

                    @if($escortOfficers->isNotEmpty())
                    @foreach($escortOfficers as $escortOfficer)
                    {{ $escortOfficer->officer_name }}<br><br> <!-- Accessing the officer_name field -->
                    @endforeach
                    @else
                    N/A
                    @endif
                </td>

                <td class="text-center">
                    @php
                    $escortOfficersIds = is_array($visit->list_of_escort_officers) ? $visit->list_of_escort_officers : json_decode($visit->list_of_escort_officers, true);
                    $escortOfficers = App\Models\EscortOfficer::whereIn('id', $escortOfficersIds)->get();
                    @endphp

                    @if($escortOfficers->isNotEmpty())
                    @foreach($escortOfficers as $escortOfficer)
                    {{ $escortOfficer->officer_name }}<br><br> <!-- Accessing the officer_name field -->
                    @endforeach
                    @else
                    N/A
                    @endif
                </td>


                <td class="text-center">{{ $visit->inspection_issue_id ? $visit->inspectionIssue->name : '' }}
                    @if($visit->inspection_issue_id)
                    <br>
                    <a href="{{ url('storage/app/' . $visit->issue_document) }}" target="_blank" class="pdf-icon" data-toggle="tooltip" title="Document"><i class="fa-solid fa-file-pdf"></i></a>
                    @endif
                </td>


                <td class="text-center">{{ $visit->acentric_report ? $visit->acentric_report : '' }}</td>

                <td class="text-center">{{ $visit->remarks ? $visit->remarks : '' }}</td>
                <!-- <td class="text-center">{{ $visit->created_at ? $visit->created_at->format('d M Y H:i') : '' }}</td> -->
                <td class="text-center">{{ $visit->created_at ? $visit->created_at->format('d-m-Y H:i') : '' }}</td>


                <td class="text-center">
    {{-- 🔒 Lock check --}}
    @php
        $isLocked = isset($visitLock) &&
                    $visitLock->locked &&
                    $visit->created_at >= $visitLock->from &&
                    $visit->created_at <= $visitLock->to;
    @endphp

    <a href="{{ route('visit.show', $visit->id) }}" class="btn btn-eye">
        <i class="fa-solid fa-eye"></i>
    </a>
    
    @if(!auth()->user()->hasRole('Viewer'))
        @if(auth()->user()->hasRole('Admin'))
            @if($isLocked)
                <span class="btn btn-primary disabled btn-edit" title="Locked during this period">
                    <i class="fa-solid fa-pen-to-square"></i>
                </span>
            @else
                <a href="{{ route('editVisit', $visit->id) }}" class="btn btn-primary btn-edit">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
            @endif
        @else
            @if($visit->deleted_at != null)
                <span class="btn btn-primary disabled btn-edit" title="Cannot edit deleted visit">
                    <i class="fa-solid fa-pen-to-square"></i>
                </span>
            @elseif($isLocked)
                <span class="btn btn-primary disabled btn-edit" title="Locked during this period">
                    <i class="fa-solid fa-pen-to-square"></i>
                </span>
            @else
                <a href="{{ route('editVisit', $visit->id) }}" class="btn btn-primary btn-edit">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
            @endif
        @endif

        @if(auth()->user()->hasRole('Admin'))
            <label class="switch btn-toggle">
                <input type="checkbox" {{ is_null($visit->deleted_at) ? 'checked' : '' }} data-id="{{ $visit->id }}">
                <span class="slider round"></span>
            </label>
        @else
            <label class="switch btn-toggle">
                <input type="checkbox" {{ is_null($visit->deleted_at) ? 'checked' : '' }} data-id="{{ $visit->id }}" @if($visit->deleted_at != null) disabled @endif>
                <span class="slider round"></span>
            </label>
        @endif

        {{-- Add Approve and Revert buttons for draft visits --}}
        @if(strtolower(auth()->user()->role->name) === 'admin' && $visit->is_draft)
            <form action="{{ route('visit.approve', $visit->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-success btn-approve" title="Approve this visit">
                    <i class="fa-solid fa-check"></i> Approve
                </button>
            </form>

            <form action="{{ route('visit.revert', $visit->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-warning btn-revert" title="Mark as reverted for review">
                    <i class="fa-solid fa-rotate-left"></i> Revert
                </button>
            </form>
        @endif

        {{-- Add Revert button for active visits --}}
        @if(auth()->user()->role && strtolower(auth()->user()->role->name) === 'admin' && !$visit->is_draft && !$visit->is_reverted && is_null($visit->deleted_at))
            <form action="{{ route('visit.revert', $visit->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-warning btn-revert" title="Mark as reverted for review">
                    <i class="fa-solid fa-rotate-left"></i> Revert
                </button>
            </form>
        @endif
    @endif
</td>



                <!-- <td class="text-center">


                    {{-- 🔒 Lock check --}}
                    @php
                        $isLocked = isset($visitLock) &&
                                    $visit->created_at >= $visitLock->from &&
                                    $visit->created_at <= $visitLock->to;
                    @endphp


                        <a href="{{ route('visit.show', $visit->id) }}" class="btn btn-eye">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        @if(!auth()->user()->hasRole('Viewer'))

                        @if(auth()->user()->hasRole('Admin'))
                        @if($isLocked)
                        <span class="btn btn-primary disabled btn-edit" title="Locked during this period">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </span>
                        @else
                        <a href="{{ route('editVisit', $visit->id) }}" class="btn btn-primary btn-edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        @endif
                        @else
                        @if($visit->deleted_at != null)
                        <span class="btn btn-primary disabled btn-edit" title="Cannot edit deleted visit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </span>
                        @elseif($isLocked)
                        <span class="btn btn-primary disabled btn-edit" title="Locked during this period">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </span>
                        @else
                        <a href="{{ route('editVisit', $visit->id) }}" class="btn btn-primary btn-edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        @endif
                        @endif


                        @if(auth()->user()->hasRole('Admin'))
                        <label class="switch btn-toggle">
                            <input type="checkbox" {{ is_null($visit->deleted_at) ? 'checked' : '' }} data-id="{{ $visit->id }}">
                            <span class="slider round"></span>
                        </label>
                        @else
                        <label class="switch btn-toggle">
                            <input type="checkbox" {{ is_null($visit->deleted_at) ? 'checked' : '' }} data-id="{{ $visit->id }}" @if($visit->deleted_at != null) disabled @endif>
                            <span class="slider round"></span>
                        </label>
                        @endif

                        @endif
                </td> -->
            </tr>
            @endforeach
            @endforeach
            @endif
        </tbody>
    </table>




</div>
@endsection
@push('script')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var tableHeaders = document.querySelectorAll("#myTable th");
        var columns = [];
        tableHeaders.forEach(function(header, index) {
            if (!header.querySelector('.btn') && !header.classList.contains('actions-column')) {
                columns.push(index);
            }
        });
        var table = document.getElementById("myTable");
        table.setAttribute("data-export-columns", columns.join(", "));
    });
</script>
<script>
    $(document).ready(function() {
        var table = $('#myTable').DataTable();

        // Modify column visibility
        table.columns([1, 7, 8]).visible(false);

        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        function formatDateTime(datetime) {
            let date = new Date(datetime);
            let year = date.getFullYear();
            let month = ('0' + (date.getMonth() + 1)).slice(-2);
            let day = ('0' + date.getDate()).slice(-2);
            let hours = ('0' + date.getHours()).slice(-2);
            let minutes = ('0' + date.getMinutes()).slice(-2);
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }



        $(document).on('change', '.switch input', function() {
            var $checkbox = $(this);
            var visitId = $checkbox.data('id');
            var isActive = $checkbox.is(':checked');

            var originalState = isActive;

            $checkbox.prop('disabled', true);

            Swal.fire({
                title: 'Are you sure?',
                text: `You want to ${isActive ? 'activate' : 'deactivate'} this visit?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var base_url = "{{ url('/') }}";
                    $.ajax({
                        url: base_url + '/inspection/' + visitId + '/update-status',
                        type: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        data: {
                            is_active: isActive
                        },
                        success: function(response) {
                            if (response.success) {
                                FancyAlerts.show({
                                    msg: response.msg || 'Status updated successfully!',
                                    type: 'success'
                                });
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                $checkbox.prop('checked', !originalState);
                                FancyAlerts.show({
                                    msg: response.msg || 'Error updating status',
                                    type: 'error'
                                });
                            }
                        },
                        error: function(xhr) {
                            $checkbox.prop('checked', !originalState);
                            var response = JSON.parse(xhr.responseText);
                            var message = response.msg ? response.msg : 'An unknown error occurred';
                            FancyAlerts.show({
                                msg: 'Error: ' + message,
                                type: 'error'
                            });
                        }
                    });
                } else {
                    $checkbox.prop('checked', !isActive);
                }

                $checkbox.prop('disabled', false);
            });
        });






        $(document).on('click', '.deleteVisitBtn', function() {
            var visitId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: `You want to delete this visit ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('deleteVisit') }}",
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        data: {
                            visit_id: visitId
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', response.msg || 'Visit deleted successfully!', 'success');
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                Swal.fire('Error!', 'Error: ' + response.msg, 'error');
                            }
                        },
                        error: function(xhr) {
                            var response = JSON.parse(xhr.responseText);
                            var message = response.msg ? response.msg : 'An unknown error occurred';
                            Swal.fire('Error!', 'Error: ' + message, 'error');
                        }
                    });
                }
            });
        });

        // Select all checkboxes
        $('#selectAllVisits').on('change', function() {
            $('.visit-checkbox').prop('checked', $(this).prop('checked'));
        });

        // If any checkbox is unchecked, uncheck selectAll
        $(document).on('change', '.visit-checkbox', function() {
            if (!$(this).prop('checked')) {
                $('#selectAllVisits').prop('checked', false);
            } else if ($('.visit-checkbox:checked').length === $('.visit-checkbox').length) {
                $('#selectAllVisits').prop('checked', true);
            }
        });

        // Bulk action apply button click
        $('#bulkVisitActionApplyBtn').on('click', function(e) {
            e.preventDefault();
            var selectedAction = $('#bulkVisitActionSelect').val();
            var selectedIds = $('.visit-checkbox:checked').map(function() {
                return $(this).val();
            }).get();
            if (!selectedAction) {
                FancyAlerts.show({
                    msg: 'Please select a bulk action.',
                    type: 'info'
                });
                return;
            }
            if (selectedIds.length === 0) {
                FancyAlerts.show({
                    msg: 'Please select at least one visit.',
                    type: 'info'
                });
                return;
            }
            let url = '';
            if (selectedAction === 'approve') {
                url = "{{ url('/visits/bulk-approve') }}";
            } else if (selectedAction === 'reject') {
                url = "{{ url('/visits/bulk-reject') }}";
            } else if (selectedAction === 'revert') {
                url = "{{ url('/visits/bulk-revert') }}";
            }
            if (!url) return;
            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to ' + selectedAction + ' the selected visits?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: { ids: selectedIds },
                        success: function(response) {
                            if (response.success) {
                                FancyAlerts.show({
                                    msg: response.msg || 'Bulk action completed!',
                                    type: 'success'
                                });
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                FancyAlerts.show({
                                    msg: response.msg || 'Error performing bulk action',
                                    type: 'error'
                                });
                            }
                        },
                        error: function(xhr) {
                            var response = JSON.parse(xhr.responseText);
                            var message = response.msg ? response.msg : 'An unknown error occurred';
                            FancyAlerts.show({
                                msg: 'Error: ' + message,
                                type: 'error'
                            });
                        }
                    });
                }
            });
        });

        // Dynamically enable/disable dropdown options based on status filter
        function updateBulkVisitActionOptions() {
            var status = $('input[name="statusFilter"]:checked').val();
            var approve = $('#bulkVisitActionSelect option[value="approve"]');
            var reject = $('#bulkVisitActionSelect option[value="reject"]');
            var revert = $('#bulkVisitActionSelect option[value="revert"]');
            // 'draft' = Pending for Approval, 'reverted' = Reverted to the User
            if (status === 'draft') {
                approve.prop('disabled', false);
                reject.prop('disabled', false);
                revert.prop('disabled', true);
            } else if (status === 'reverted') {
                approve.prop('disabled', true);
                reject.prop('disabled', true);
                revert.prop('disabled', false);
            } else {
                approve.prop('disabled', true);
                reject.prop('disabled', true);
                revert.prop('disabled', false);
            }
        }
        // Initial call
        updateBulkVisitActionOptions();
        // On filter change
        $('input[name="statusFilter"]').on('change', updateBulkVisitActionOptions);

    });

    $('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd' // Ensure it's 'YYYY-MM-DD'
    });
</script>

@endpush