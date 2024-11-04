@extends('layouts.layout')

@section('content')

<div class="container-fluid mt-4 table-responsive">
    <h1>Inspector Data Management</h1>

    @if(!auth()->user()->hasRole('Viewer'))
    <a href="{{ route('addInspector') }}" class="btn btn-primary mb-3 ml-auto float-right" data-toggle="tooltip" data-placement="left" title="Add New Inspector"><i class="fa-solid fa-plus"></i></a>
    @endif
    <br>
    <br>

    <table class="table table-bordered table-striped" id="myTable" data-export-columns="0,1,2,3,4, 5, 6">
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
                                <p class="text-dark mr-3 mt-2"><strong>Date of Joining between : </strong></p>
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
                <th scope="col">Name & Designation</th>
                <th scope="col" class="text-center">Country</th>
                <th scope="col" class="text-center">Professional Experience</th>
                <th scope="col" class="text-center">Passport & UNLP Number</th>
                <th scope="col" class="text-center">Inspections</th>
                <th scope="col" class="text-center">Remarks</th>
                <th scope="col" class="text-center">Actions</th>

            </tr>
        </thead>
        <tbody id="inspectorTableBody">

            @if($inspectors->isEmpty())
            <tr>
                <td colspan="6" class="text-center">No inspectors available.</td>
            </tr>
            @else

            @foreach($inspectors as $inspector)
            <tr data-status="{{ is_null($inspector->deleted_at) ? 'active' : 'inactive' }}" data-join-date="{{ $inspector->inspections->isNotEmpty() ? $inspector->inspections->first()->date_of_joining : '' }}">
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $inspector->name ? $inspector->name : 'N/A' }} {{','}} <br>
                    {{ $inspector->rank ? $inspector->rank->rank_name : 'N/A' }} <br>

                    @if($inspector->clearance_certificate)
                    <a href="{{ asset('storage/app/' . $inspector->clearance_certificate) }}" target="_blank" class="pdf-icon" data-toggle="tooltip" title="Clearance Certificate">
                        <i class="fa-solid fa-file-pdf"></i>
                    </a>
                    @endif
                </td>

                <td class="text-center">{{ $inspector->nationality->country_name ? $inspector->nationality->country_name : '' }}</td>

                <td class="text-center remarks-text">{{ $inspector->professional_experience ? $inspector->professional_experience : '' }}</td>
                <td class="text-center remarks-text">{{ $inspector->passport_number ? $inspector->passport_number : '' }} <br> {{ $inspector->unlp_number ? $inspector->unlp_number : '' }}</td>

                <td class="text-center remarks-text">
                    @if($inspector->inspections->isNotEmpty())
                    @foreach($inspector->inspections as $inspection)
                    <div>
                        {{ $inspection->category->category_name ?? 'N/A' }} <br>
                        {{ $inspection->status->status_name ?? 'N/A' }} <br>
                        {{ $inspection->date_of_joining ? $inspection->date_of_joining->format('d-m-Y') : 'N/A' }}

                    </div>
                    @endforeach
                    @else
                    <p>No inspections available.</p>
                    @endif
                </td>
                <td class="text-center remarks-text">{{ $inspector->remarks ? $inspector->remarks : 'N/A' }}</td>
                <td class="text-center">
                    <a href="{{ route('inspector.show', $inspector->id) }}" class="btn btn-eye">
                        <i class="fa-solid fa-eye"></i>
                    </a>

                    @if(!auth()->user()->hasRole('Viewer'))

                    @if(auth()->user()->hasRole('Admin'))
                    <a href="{{ route('editInspector', $inspector->id) }}" class="btn btn-primary btn-edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    @else
                    @if($inspector->deleted_at != null)
                    <span class="btn btn-primary disabled btn-edit" title="Cannot edit deleted inspector">
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
                    $.ajax({
                        url: '/inspector/' + inspectorId + '/update-status',
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
    });
</script>
@endpush