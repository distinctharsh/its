@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <h1>Visit Details Management</h1>

    <!-- Button to trigger modal -->
    <!-- <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addVisitModal"  >
        Add New Record
    </button> -->
    @if(!auth()->user()->hasRole('Viewer'))
    <a href="{{ route('addVisit') }}" class="btn btn-primary mb-3 float-right" data-toggle="tooltip" data-placement="left" title="Add New Visit"><i class="fa-solid fa-plus"></i></a>
    @endif

    <br>
    <br>
    <!-- Visit Details Table -->
    <table class="table table-bordered table-striped table-responsive" id="myTable" data-export-columns="0, 1, 2, 3, 4, 5, 6, 7">
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
                            
                            <div class="row ml-3 mb-3 pt-3 justify-content-center">
                                <p class="text-dark mr-3 mt-2"><strong>Arrival Date between : </strong></p>
                                <div class="">
                                    <label for="startDate" class="text-dark mr-2">From:</label>
                                    <input type="text" id="startDate" class="form-control datepicker" placeholder="From Date" style="display:inline-block; width:auto;">
                                    <label for="endDate" class="text-dark ml-3 mr-2">To:</label>
                                    <input type="text" id="endDate" class="form-control datepicker" placeholder="To Date" style="display:inline-block; width:auto;">
                                </div>
                            </div>

                            <div class="mb-3 mr-3 d-flex " style="float: right;">
                                <div class="form-check mr-3">
                                    <input class="form-check-input" type="radio" name="statusFilter" id="statusAll" value="all">
                                    <label class="form-check-label text-dark" for="statusAll" >All</label>
                                </div>
                                <div class="form-check mr-3">
                                    <input class="form-check-input" type="radio" name="statusFilter" id="statusActive" value="active" checked>
                                    <label class="form-check-label text-dark" for="statusActive" >Active</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="statusFilter" id="statusInactive" value="inactive">
                                    <label class="form-check-label text-dark" for="statusInactive" >Inactive</label>
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

      
        <select id="pageLengthSelect" class="form-control ml-3 mb-3" style="width: 80px;">
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="all">All</option>
        </select>
        
        <thead class="thead-dark">
            <tr>
                <th scope="col" class="text-center">S.No.</th>
                <th scope="col" class="text-center">Team Lead Name</th>
                <th scope="col" class="text-center">Inspection Category & Type</th>
                <th scope="col" class="text-center">Inspection Sub Category & Name and Address of Site of Inspection</th>
                <th scope="col" class="text-center">State</th>
                <th scope="col" class="text-center">Arrival Date & Time</th>
                <th scope="col" class="text-center">Departure Date & Time</th>
                <th scope="col" class="text-center">Remarks</th>
                <!-- <th scope="col" class="text-center">Created At</th> -->

                <th scope="col" class="text-center">Actions</th>

            </tr>
        </thead>
        <tbody id="visitTableBody">
            @if($visits->isEmpty())
            <tr>
                <td colspan="6" class="text-center">No visit available.</td>
            </tr>
            @else
            @foreach($visits as $visit)
            <tr data-status="{{ is_null($visit->deleted_at) ? 'active' : 'inactive' }}" data-join-date="{{ $visits->isNotEmpty() ? $visit->arrival_datetime : '' }}">
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">
                    {{$visit->teamLead->name ? $visit->teamLead->name : ''}}


                    <div class="pdf-icons-container mt-2">
                        @if($visit->clearance_certificate)
                        <a href="{{ asset('storage/app/' . $visit->clearance_certificate) }}" target="_blank" class="pdf-icon" data-toggle="tooltip" title="Document">
                            <i class="fa-solid fa-book"></i>
                        </a>
                        @else
                        <!-- <span class="pdf-icon disabled" data-toggle="tooltip" title="No Upload Document Available">
                            <i class="fa-solid fa-file-pdf"></i>
                        </span> -->
                        @endif

                        @if($visit->visit_report)
                        <a href="{{ asset('storage/app/' . $visit->visit_report) }}" target="_blank" class="pdf-icon" data-toggle="tooltip" title="Visit Report">
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

                    {{ $visit->inspection_type_selection ? ucfirst($visit->inspection_type_selection) : '' }} {{$visit->inspection_type_selection ? ',' : ''}}
                    <br>
                    {{ $visit->inspectionType ? $visit->inspectionType->type_name : '' }}


                </td>
                <td class="text-center">

                    @if($visit->inspectionCategoryType)
                    @if($visit->inspectionCategoryType->type_name)
                    {{ $visit->inspectionCategoryType->type_name }}
                    ,
                    @endif
                    <br>
                    @endif

                    @foreach($visit->siteMappings as $siteMapping)
                    <div>{{ $siteMapping->site_of_inspection }}</div>
                    <br>
                    @endforeach
                </td>
                <td class="text-center">
                    @php
                    $stateNames = [];
                    foreach ($visit->siteMappings as $siteMapping) {
                    $stateName = $states->firstWhere('id', $siteMapping->state_id)->state_name ?? 'N/A';
                    $stateNames[] = $stateName; // Store state names in an array
                    }
                    @endphp
                    {{ implode(', ', $stateNames) }} <!-- Join with a comma -->
                </td>

                <td class="text-center">{{ $visit->arrival_datetime ? $visit->arrival_datetime->format('d-m-Y h:i A') : '' }}</td>
                <td class="text-center">{{ $visit->departure_datetime ? $visit->departure_datetime->format('d-m-Y h:i A') : '' }}</td>




                <td class="text-center">{{ $visit->remarks ? $visit->remarks : '' }}</td>
                <!-- <td class="text-center">{{ $visit->created_at ? $visit->created_at->format('d M Y H:i') : '' }}</td> -->

                <td class="text-center">

                    <a href="{{ route('visit.show', $visit->id) }}" class="btn btn-eye">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    @if(!auth()->user()->hasRole('Viewer'))

                    @if(auth()->user()->hasRole('Admin'))
                    <a href="{{ route('editVisit', $visit->id) }}" class="btn btn-primary btn-edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    @else
                    @if($visit->deleted_at != null)
                    <span class="btn btn-primary disabled btn-edit" title="Cannot edit deleted visit">
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
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>




</div>
@endsection
@push('script')
<script>
    $(document).ready(function() {


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
                    $.ajax({
                        url: '/visit/' + visitId + '/update-status',
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


    });
</script>

@endpush