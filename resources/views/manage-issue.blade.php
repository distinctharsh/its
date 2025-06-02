@extends('layouts.layout')

@section('content')
    <div class="container-fluid mt-4">
        <!-- <h1>Issue Details</h1> -->
        <div class="row">
            <div class="col-12">
                <a href="{{ route('addIssue') }}" class="btn btn-primary mb-3 float-left" data-toggle="tooltip" data-placement="left" title="Add New Issue"><i class="fa-solid fa-plus"></i></a>
            
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
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <!-- Issue Details Table -->
                <table class="table table-bordered table-striped" id="myTable" data-export-columns="0, 1, 2">
                    <select id="pageLengthSelect" class="form-control " >
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="all">All</option>
                    </select>
                
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col" class="text-center">Sl. No.</th>
                            <th scope="col" >Issue Name</th>
                            <th scope="col" >Issue Created At</th>
                            <th scope="col" class="text-center actions-column">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="issueTableBody">
                        @if($issues->isEmpty())
                            <tr>
                                <td colspan="6" class="text-center">No issues available.</td>
                            </tr>
                        @else
                            @foreach($issues as $issue)
                                <tr data-status="{{ is_null($issue->deleted_at) ? 'active' : 'inactive' }}">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td >{{ $issue->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($issue->created_at)->format('d-m-Y h:i A') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('editIssue', $issue->id) }}" class="btn btn-primary btn-edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <label class="switch btn-toggle">
                                            <input type="checkbox" {{ is_null($issue->deleted_at) ? 'checked' : '' }} data-id="{{ $issue->id }}">
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $(document).on('change', '.switch input', function() {
            var $checkbox = $(this);
            var issueId = $checkbox.data('id');
            var isActive = $checkbox.is(':checked');
            var originalIssue = isActive;
            Swal.fire({
                title: 'Are you sure?',
                text: `You want to ${isActive ? 'activate' : 'deactivate'} this Issue ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var base_url = "{{ url('/') }}";
                    $.ajax({
                        url: base_url+'/issue/' + issueId + '/update-status',
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
                                    msg: response.message || 'Status updated successfully!',
                                    type: 'success'
                                });
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                $checkbox.prop('checked', !originalIssue);
                                FancyAlerts.show({
                                    msg: response.message || 'Error updating status',
                                    type: 'error'
                                });
                            }
                        },
                        error: function(xhr) {
                            $checkbox.prop('checked', !originalIssue);
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
    });
</script>
@endpush