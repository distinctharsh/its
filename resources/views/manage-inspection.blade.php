@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <h1>Inspection Details Management</h1>

    <!-- Button to trigger modal -->
    <!-- <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addInspectionModal"  >
        Add New Record
    </button> -->
    @if(!auth()->user()->hasRole('Viewer'))
    <a href="{{ route('addInspection') }}" class="btn btn-primary mb-3 float-left" data-toggle="tooltip" data-placement="left" title="Add New Inspection"><i class="fa-solid fa-plus"></i></a>
    @endif
    <table class="table table-bordered table-striped" id="myTable" data-export-columns="0, 1, 2, 3, 4">
        <div class="mb-3 d-flex " style="float: right;">
            <div class="form-check mr-3">
                <input class="form-check-input" type="radio" name="statusFilter" id="statusAll" value="all">
                <label class="form-check-label" for="statusAll">All</label>
            </div>
            <div class="form-check mr-3">
                <input class="form-check-input" type="radio" name="statusFilter" id="statusActive" value="active" checked>
                <label class="form-check-label" for="statusActive">Active</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="statusFilter" id="statusInactive" value="inactive">
                <label class="form-check-label" for="statusInactive">Inactive</label>
            </div>

            <select id="pageLengthSelect" class="form-control ml-3"  >
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="all">All</option>
            </select>
        </div>
        <div class="row justify-content-center">
            <div class="mb-3">
                <label for="startDate" class="mr-2">From:</label>
                <input type="text" id="startDate" class="form-control datepicker" placeholder="YYYY-MM-DD" style="display:inline-block; width:auto;">
                <label for="endDate" class="ml-3 mr-2">To:</label>
                <input type="text" id="endDate" class="form-control datepicker" placeholder="YYYY-MM-DD" style="display:inline-block; width:auto;">
                <button id="filterDate" class="btn btn-secondary ml-3">Search</button>
            </div>
        </div>

        <thead class="thead-dark">
            <tr>
                <th scope="col" class="text-center">Sl. No.</th>
                <th scope="col">Inspector Name</th>
                <th scope="col">Category</th>
                <th scope="col" class="text-center">Status</th>
                <th scope="col" class="text-center">Remarks</th>
                <th scope="col" class="text-center">Created At</th>
            
                <th scope="col" class="text-center actions-column">Actions</th>
         

            </tr>
        </thead>
        <tbody id="inspectionTableBody">
            @if($inspections->isEmpty())
            <tr>
                <td colspan="6" class="text-center">No inspection available.</td>
            </tr>
            @else

            @foreach($inspections as $inspection)
            <tr data-status="{{ is_null($inspection->deleted_at) ? 'active' : 'inactive' }}">
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $inspection->inspector ? $inspection->inspector->name : 'N/A' }}</td>

                <td>{{ $inspection->category ? $inspection->category->category_name : 'N/A' }}</td>
                <td class="text-center">{{ $inspection->status ? $inspection->status->status_name : 'N/A' }}</td>
                <td class="text-center remarks-text">{{ $inspection->remarks ? $inspection->remarks : 'N/A' }}</td>
                <td class="text-center">{{ $inspection->created_at ? $inspection->created_at->format('d-m-Y') : 'N/A' }}</td>

                <td class="text-center">
                    
                    <a href="{{ route('inspection.show', $inspection->id) }}" class="btn btn-eye">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    @if(!auth()->user()->hasRole('Viewer'))

                    @if(auth()->user()->hasRole('Admin'))
                    <a href="{{ route('editInspection', $inspection->id) }}" class="btn btn-primary btn-edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    @else
                    @if($inspection->deleted_at != null)
                    <span class="btn btn-primary disabled btn-edit" title="Cannot edit deleted inspection">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </span>
                    @else
                    <a href="{{ route('editInspection', $inspection->id) }}" class="btn btn-primary btn-edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    @endif
                    @endif

                    @if(auth()->user()->hasRole('Admin'))
                    <label class="switch btn-toggle">
                        <input type="checkbox" {{ is_null($inspection->deleted_at) ? 'checked' : '' }} data-id="{{ $inspection->id }}">
                        <span class="slider round"></span>
                    </label>
                    @else

                    <label class="switch btn-toggle">
                        <input type="checkbox" {{ is_null($inspection->deleted_at) ? 'checked' : '' }} data-id="{{ $inspection->id }}" @if($inspection->deleted_at != null) disabled @endif>
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

        $(document).on('change', '.switch input', function() {
            var $checkbox = $(this);
            var inspectionId = $checkbox.data('id');
            var isActive = $checkbox.is(':checked');

            // Store the original state
            var originalState = isActive;

            $checkbox.prop('disabled', true);



            Swal.fire({
                title: 'Are you sure?',
                text: `You want to ${isActive ? 'activate' : 'deactivate'} this inspection?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.isConfirmed) {

                    var base_url = "{{ url('/') }}";
                    $.ajax({
                        url: base_url+'/inspection/' + inspectionId + '/update-status',
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
                    $checkbox.prop('checked', !isActive); // Reset the checkbox if action is canceled
                }

                $checkbox.prop('disabled', false);
            });
        });


        $(document).on('click', '.deleteInspectionBtn', function() {
            var inspectionId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: `You want to delete this inspection ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('deleteInspection') }}",
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        data: {
                            inspection_id: inspectionId
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', response.msg || 'Inspection deleted successfully!', 'success');
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