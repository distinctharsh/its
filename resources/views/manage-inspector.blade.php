@extends('layouts.layout')

@section('content')

<div class="container-fluid mt-4 table-responsive">
    <!-- <h1>Inspector Data Management</h1> -->

    @if(!auth()->user()->hasRole('Viewer'))
    <a href="{{ route('addInspector') }}" class="btn btn-primary mb-3 ml-auto float-right" data-toggle="tooltip" data-placement="left" title="Add"><i class="fa-solid fa-plus"></i></a>
    @endif
    @if(auth()->user()->hasRole('Admin'))
        <div class="btn-group mb-3 ml-2" style="display:inline-block;">
            <select id="bulkActionSelect" class="form-control">
                <option value="">Bulk Actions</option>
                <option value="approve">Approve</option>
                <option value="reject">Reject</option>
                <option value="revert">Revert</option>
            </select>
            <button id="bulkActionApplyBtn" class="btn btn-primary ml-2">Apply</button>
        </div>
    @endif
    <br>
    <br>

    <table class="table table-bordered table-striped" id="myTable">
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
                                <div class="col-12 col-md-6 mb-3 px-3 mt-3 d-flex align-items-center">
                                    <p class="text-dark mb-0 mr-2"><strong>Communication Date :</strong></p>
                                    <input type="text" id="startDate" class="form-control datepicker" placeholder="From" style="width:auto; margin-right:10px;" >
                                    <input type="text" id="endDate" class="form-control datepicker" placeholder="To" style="width:auto;">
                                </div>

                                <!-- Status Section -->
                                <div class="col-12 col-md-6 mb-3 px-3 mt-3 d-flex align-items-center">
                                    <p class="text-dark mb-0 mr-2"><strong>Status :</strong></p>
                                    <div class="d-flex">
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
                            </div>

                            <div class="text-center mb-3">

                                <button id="filterDate" class="btn btn-primary ml-3 justify-content-center mb-3">Search</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>



        <select id="pageLengthSelect" class="form-control mb-3"  >
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="all">All</option>
        </select>

        <thead class="thead-dark">
            <tr>
                @if($inspectors->isEmpty())
                    <th scope="col" class="text-center">
                        Records
                    </th>
                @else



                    <th scope="col" class="text-center">Sl. No.</th>
                    <th scope="col" class="text-center">Record ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Gender</th>
                    <th scope="col">Date Of Birth</th>
                    <th scope="col">Place Of Birth</th>
                    <th scope="col" class="text-center">Nationality</th>
                    <th scope="col" class="text-center">UNLP No.</th>
                    <th scope="col" class="text-center">Passport No.</th>
                    <th scope="col" class="text-center">Designation</th>
                    <th scope="col" class="text-center">Rank</th>
                    <th scope="col" class="text-center">Qualification</th>
                    <th scope="col" class="text-center">Professional Experience</th>
                    <th scope="col" class="text-center">Inspection Category</th>
                    <th scope="col" class="text-center">Security Status (IB, RAW, MEA)</th>
                    <th scope="col" class="text-center">OPCW Communication Date</th>
                    <th scope="col" class="text-center">OPCW Deletion Date</th>
                    <th scope="col" class="text-center">Remarks</th>
                    <th scope="col" class="text-center">Created At</th>
                    <th scope="col" class="text-center">Actions</th>

                @endif

            </tr>
        </thead>
        <tbody id="inspectorTableBody">

            @if($inspectors->isEmpty())
            <tr>
                <td colspan="6" class="text-center">No inspectors available.</td>
            </tr>
            @else

            @foreach($inspectors as $inspector)
            <!-- <tr data-status="{{ is_null($inspector->deleted_at) ? 'active' : 'inactive' }}" data-join-date="{{ $inspector->inspections->isNotEmpty() ? $inspector->inspections->first()->date_of_joining : '' }}"> -->
            <!-- <tr data-status="{{ $inspector->is_draft ? 'draft' : (is_null($inspector->deleted_at) ? 'active' : 'inactive') }}" data-join-date="{{ $inspector->inspections->isNotEmpty() ? $inspector->inspections->first()->date_of_joining : '' }}"> -->
            <tr data-status="{{ $inspector->is_reverted ? 'reverted' : ($inspector->is_draft ? 'draft' : (is_null($inspector->deleted_at) ? 'active' : 'inactive')) }}"  data-join-date="{{ $inspector->inspections->isNotEmpty() ? $inspector->inspections->first()->date_of_joining : '' }}">

                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ $inspector->id }}</td>
                <td>

                @if(auth()->user()->hasRole('Admin'))
                <input type="checkbox" class="inspector-checkbox" value="{{ $inspector->id }}">
                @endif
                {{ $inspector->name ? $inspector->name : 'N/A' }}  <br>
           
                
                @if($inspector->ib_clearance)
                <a href="{{ url('storage/app/' . $inspector->ib_clearance) }}" target="_blank" class="pdf-icon" data-toggle="tooltip" title="IB Clearance">
                    <i class="fa-solid fa-file-pdf"></i>
                </a>
                @endif
                @if($inspector->raw_clearance)
                <a href="{{ url('storage/app/' . $inspector->raw_clearance) }}" target="_blank" class="pdf-icon" data-toggle="tooltip" title="RAW Clearance">
                    <i class="fa-solid fa-file-pdf"></i>
                </a>
                @endif
                @if($inspector->mea_clearance)
                <a href="{{ url('storage/app/' . $inspector->mea_clearance) }}" target="_blank" class="pdf-icon" data-toggle="tooltip" title="MEA Clearance">
                    <i class="fa-solid fa-file-pdf"></i>
                </a>
                @endif
            </td>
            
            <td class="text-center">{{ $inspector->gender->gender_name ? $inspector->gender->gender_name : 'N/A' }} </td>
            <td class="text-center">
                {{ $inspector->dob ? \Carbon\Carbon::parse($inspector->dob)->format('d-m-Y') : 'N/A' }}
            </td>
            <td class="text-center">{{ $inspector->place_of_birth ? $inspector->place_of_birth : 'N/A' }} </td>
            
            
            <td class="text-center">
                {{ $inspector->nationality && $inspector->nationality->country_name ? $inspector->nationality->country_name : '' }}
            </td>
            
            <td class="text-center remarks-text">{{ $inspector->unlp_number ? $inspector->unlp_number : '' }}</td>

            <td class="text-center remarks-text">{{ $inspector->passport_number ? $inspector->passport_number : '' }}</td>

            <td class="text-center"> {{ $inspector->designation? $inspector->designation->designation_name : 'N/A' }}</td>

            <td class="text-center"> {{ $inspector->rank ? $inspector->rank->rank_name : 'N/A' }}  </td>
            
            <td class="text-center remarks-text">
                {{ str_replace('Ã¢â‚¬â€œ', '–', $inspector->qualifications) }}
            </td>
            <td class="text-center remarks-text">
                {{ str_replace('Ã¢â‚¬â€œ', '–', $inspector->professional_experience) }}
            </td>
            <td class="text-center remarks-text">
                    @if($inspector->inspections->isNotEmpty())
                    @foreach($inspector->inspections as $inspection)
                    <div>
                        <!-- Replace problematic characters -->
                        {{ str_replace('Ã¢â‚¬â€œ', '–', $inspection->category->category_name ?? 'N/A') }}
                        @if(!$loop->last) 
                            , <br>
                        @endif
                    </div>
                    @endforeach
                    @else
                    <p>No inspections available.</p>
                    @endif
            </td>
            <td class="text-center">
                {{ $inspector->ibStatus ? $inspector->ibStatus->status_name : 'N/A' }}
                <br>
                {{ $inspector->ibStatus ? $inspector->rawStatus->status_name : 'N/A' }} 
                <br>
                {{ $inspector->ibStatus ? $inspector->meaStatus->status_name : 'N/A' }} 
            </td>




                <td class="text-center remarks-text">
                    @if($inspector->inspections->isNotEmpty())
                    @foreach($inspector->inspections as $inspection)
                    <div>
                        {{ $inspection->date_of_joining ? $inspection->date_of_joining->format('d-m-Y') : 'N/A' }}
                        @if(!$loop->last) 
                            , <br>
                        @endif
                    </div>
                    @endforeach
                    @else
                    <p>No inspections available.</p>
                    @endif
                </td>

                <td class="text-center remarks-text">
                    @if($inspector->inspections->isNotEmpty())
                    @foreach($inspector->inspections as $inspection)
                    <div>
                        {{ $inspection->deletion_date ? $inspection->deletion_date->format('d-m-Y') : 'N/A' }}
                        @if(!$loop->last) 
                            , <br>
                        @endif
                    </div>
                    @endforeach
                    @else
                    <p>No inspections available.</p>
                    @endif
                </td>

                <td class="text-center remarks-text">
                    @if($inspector->inspections->isNotEmpty())
                        @foreach($inspector->inspections as $inspection)
                        <div>
                            {{ $inspection->remarks && $inspection->remarks ? $inspection->remarks : '' }}
                            @if(!$loop->last) 
                                , <br>
                            @endif
                        </div>
                        @endforeach
                    @endif
                </td>
                <td class="text-center">{{ $inspector->created_at ? $inspector->created_at->format('d-m-Y H:i') : '' }}</td>
                <td class="text-center">

                    @php
                        $isLocked = isset($inspectorLock) &&
                                    $inspectorLock->locked &&
                                    $inspector->created_at >= $inspectorLock->from &&
                                    $inspector->created_at <= $inspectorLock->to;
                    @endphp



                    <a href="{{ route('inspector.show', $inspector->id) }}" class="btn btn-eye">
                        <i class="fa-solid fa-eye"></i>
                    </a>

                    @if(!auth()->user()->hasRole('Viewer'))

                    @if(auth()->user()->hasRole('Admin'))
                        @if($isLocked)
                            <span class="btn btn-primary disabled btn-edit" title="Locked during this period">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </span>
                        @else
                            <a href="{{ route('editInspector', $inspector->id) }}" class="btn btn-primary btn-edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        @endif
                    @else
                    @if($inspector->deleted_at != null)
                        <span class="btn btn-primary disabled btn-edit" title="Cannot edit deleted inspector">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </span>
                    @elseif($isLocked)
                        <span class="btn btn-primary disabled btn-edit" title="Locked during this period">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </span>
                    @else
                        <a href="{{ route('editInspector', $inspector->id) }}" class="btn btn-primary btn-edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    @endif
                    @endif

                    @if(auth()->user()->hasRole('Admin'))
                    <label class="switch btn-toggle">
                        <input type="checkbox" {{ is_null($inspector->deleted_at) ? 'checked' : '' }} data-id="{{ $inspector->id }}">
                        <span class="slider round"></span>
                    </label>
                    @else
                    <label class="switch btn-toggle">
                        <input type="checkbox" {{ is_null($inspector->deleted_at) ? 'checked' : '' }} data-id="{{ $inspector->id }}" @if($inspector->deleted_at != null) disabled @endif>
                        <span class="slider round"></span>
                    </label>
                    @endif
                    @endif





                    @if(strtolower(auth()->user()->role->name) === 'admin' && $inspector->is_draft)
                        <form action="{{ route('inspector.approve', $inspector->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-approve" title="Approve this inspector">
                                <i class="fa-solid fa-check"></i> Approve
                            </button>
                        </form>


                        <form action="{{ route('inspector.revert', $inspector->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning btn-revert" title="Mark as reverted for review">
                                <i class="fa-solid fa-rotate-left"></i> Revert
                            </button>
                        </form>
                    @endif

                    @if(auth()->user()->role && strtolower(auth()->user()->role->name) === 'admin' && !$inspector->is_draft && !$inspector->is_reverted && is_null($inspector->deleted_at))
                        <form action="{{ route('inspector.revert', $inspector->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning btn-revert" title="Mark as reverted for review">
                                <i class="fa-solid fa-rotate-left"></i> Revert
                            </button>
                        </form>
                    @endif




    <!-- @if(strtolower(auth()->user()->role->name) === 'admin' || strtolower(auth()->user()->role->name) === 'user' && $inspector->is_reverted)
        <form action="{{ route('inspector.sendToDraft', $inspector->id) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Are you sure you want to move this to draft?');">
                Send to Draft
            </button>
        </form>
    @endif -->



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
  document.addEventListener("DOMContentLoaded", function () {
      var tableHeaders = document.querySelectorAll("#myTable th");
      var columns = [];
      tableHeaders.forEach(function (header, index) {
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
        table.columns([1]).visible(false);


        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        function validateForm() {
            var isValid = true;

            var dob = $('#dob').val();
            if (dob === '' || new Date(dob) >= new Date()) {
                isValid = false;
                FancyAlerts.show({
                    msg: 'Date of Birth must be in the past.',
                    type: 'error'
                });
            }

            var passportNumber = $('#passport_number').val();
            var passportRegex = /^[A-Z0-9]+$/;
            if (!passportRegex.test(passportNumber)) {
                isValid = false;
                FancyAlerts.show({
                    msg: 'Passport Number must contain only uppercase letters and digits.',
                    type: 'error'
                });
            }

            var unlpNumber = $('#unlp_number').val();
            if (unlpNumber && !passportRegex.test(unlpNumber)) {
                isValid = false;
                FancyAlerts.show({
                    msg: 'UNLP Number must contain only uppercase letters and digits.',
                    type: 'error'
                });
            }

            return isValid;
        }

        $(document).on('change', '.switch input', function() {
            var $checkbox = $(this);
            var inspectorId = $checkbox.data('id');
            var isActive = $checkbox.is(':checked');

            var originalState = isActive;

            $checkbox.prop('disabled', true);


            Swal.fire({
                title: 'Are you sure?',
                text: `You want to ${isActive ? 'activate' : 'deactivate'} this inspector?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var base_url = "{{ url('/') }}";
                    $.ajax({
                        url: base_url+'/inspector/' + inspectorId + '/update-status',
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
                                    msg: response.message || 'Status updated successfully!',
                                    type: 'success'
                                });
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                $checkbox.prop('checked', !originalState);
                                FancyAlerts.show({
                                    msg: response.message || 'Error updating status',
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

        $(document).on('click', '.deleteInspectorBtn', function() {
            var inspectorId = $(this).data('id');
            var inspectorName = $(this).data('name');

            Swal.fire({
                title: 'Are you sure?',
                text: `You want to delete the inspector ${inspectorName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('deleteInspector') }}",
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        data: {
                            inspector_id: inspectorId
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', response.msg || 'Inspector deleted successfully!', 'success');
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
        $('#selectAllInspectors').on('change', function() {
            $('.inspector-checkbox').prop('checked', $(this).prop('checked'));
        });

        // If any checkbox is unchecked, uncheck selectAll
        $(document).on('change', '.inspector-checkbox', function() {
            if (!$(this).prop('checked')) {
                $('#selectAllInspectors').prop('checked', false);
            } else if ($('.inspector-checkbox:checked').length === $('.inspector-checkbox').length) {
                $('#selectAllInspectors').prop('checked', true);
            }
        });

        // Bulk revert button click
        $('#bulkRevertBtn').on('click', function(e) {
            e.preventDefault();
            var selectedIds = $('.inspector-checkbox:checked').map(function() {
                return $(this).val();
            }).get();
            if (selectedIds.length === 0) {
                FancyAlerts.show({
                    msg: 'Please select at least one inspector to revert.',
                    type: 'info'
                });
                return;
            }
            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to revert the selected inspectors?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, revert!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/inspector/bulk-revert') }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: { ids: selectedIds },
                        success: function(response) {
                            if (response.success) {
                                FancyAlerts.show({
                                    msg: response.msg || 'Selected inspectors reverted successfully!',
                                    type: 'success'
                                });
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                FancyAlerts.show({
                                    msg: response.msg || 'Error reverting inspectors',
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

        // Bulk action apply button click
        $('#bulkActionApplyBtn').on('click', function(e) {
            e.preventDefault();
            var selectedAction = $('#bulkActionSelect').val();
            var selectedIds = $('.inspector-checkbox:checked').map(function() {
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
                    msg: 'Please select at least one inspector.',
                    type: 'info'
                });
                return;
            }
            let url = '';
            if (selectedAction === 'approve') {
                url = "{{ url('/inspectors/bulk-approve') }}";
            } else if (selectedAction === 'reject') {
                url = "{{ url('/inspectors/bulk-reject') }}";
            } else if (selectedAction === 'revert') {
                url = "{{ url('/inspectors/bulk-revert') }}";
            }
            if (!url) return;
            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to ' + selectedAction + ' the selected inspectors?',
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
        function updateBulkActionOptions() {
            var status = $('input[name="statusFilter"]:checked').val();
            var approve = $('#bulkActionSelect option[value="approve"]');
            var reject = $('#bulkActionSelect option[value="reject"]');
            var revert = $('#bulkActionSelect option[value="revert"]');
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
        updateBulkActionOptions();
        // On filter change
        $('input[name="statusFilter"]').on('change', updateBulkActionOptions);
    });
</script>
@endpush