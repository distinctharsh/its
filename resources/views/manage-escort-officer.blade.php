@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <h1>Escort Officer Details</h1>
    <a href="{{ route('addEscortOfficer') }}" class="btn btn-primary mb-3 float-left" data-toggle="tooltip" data-placement="left" title="Add New Escort Officer"><i class="fa-solid fa-plus"></i></a>

    <table class="table table-bordered table-striped" id="myTable" data-export-columns="0,1">
        <div class="mb-3 d-flex " style="float: right;">
            <!-- <div class="form-check mr-3">
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
            </div> -->

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
                <th scope="col">Officer Name</th>
                <th scope="col" class="text-center">Actions</th>

            </tr>
        </thead>
        <tbody>
            @foreach($officers as $officer)
            <tr >
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $officer->officer_name }}</td>
                <td class="text-center">
                    <input type="hidden" class="status-value" value="{{ $officer->is_active }}">

                    <a href="{{ route('editEscortOfficer', $officer->id) }}" class="btn btn-primary btn-edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                
                    <label class="switch btn-toggle">
                        <input type="checkbox" {{ is_null($officer->deleted_at) ? 'checked' : '' }} data-id="{{ $officer->id }}">
                        <span class="slider round"></span>
                    </label>
                </td>

            </tr>
            @endforeach
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
            var statusId = $checkbox.data('id');
            var isActive = $checkbox.is(':checked');

            var originalState = isActive;

            $checkbox.prop('disabled', true);

            Swal.fire({
                title: 'Are you sure?',
                text: `You want to ${isActive ? 'activate' : 'deactivate'} this Escort Officer?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: '/status/' + statusId + '/update-officer-status',
                        type: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        data: {
                            is_active: isActive
                        },
                        success: function(response) {
                            if (response.success) {
                                $checkbox.prop('checked', isActive);
                                FancyAlerts.show({
                                    msg: response.message || 'Escort Officer updated successfully!',
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



        $(document).on('click', '.deleteStatusBtn', function() {
            var statusId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: `You want to delete this Status ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('deleteStatus') }}",
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        data: {
                            status_id: statusId
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', response.msg || 'Status deleted successfully!', 'success');
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