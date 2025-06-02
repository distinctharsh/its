@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <!-- <h1>Rank Details</h1> -->


    <div class="row">
    <div class="col-12">
    <a href="{{ route('addRank') }}" class="btn btn-primary mb-3 float-left" data-toggle="tooltip" data-placement="left" title="Add New Rank"><i class="fa-solid fa-plus"></i></a>

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
    <!-- Rank Details Table -->
    <table class="table table-bordered table-striped" id="myTable">

            <select id="pageLengthSelect" class="form-control"  >
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="all">All</option>
            </select>
       
        <thead class="thead-dark">
            <tr>
                <th scope="col" class="text-center">Sl. No.</th>
                <th scope="col">Rank Name</th>
                <th scope="col" class="text-center actions-column">Actions</th>
            </tr>
        </thead>
        <tbody id="rankTableBody">
            @foreach($ranks as $rank)
            <tr data-status="{{ is_null($rank->deleted_at) ? 'active' : 'inactive' }}">
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $rank->rank_name ? $rank->rank_name : 'N/A' }}</td>

                <td class="text-center">



                   
                    <a href="{{ route('editRank', $rank->id) }}" class="btn btn-primary btn-edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                   

                    <label class="switch btn-toggle">
                        <input type="checkbox" {{ is_null($rank->deleted_at) ? 'checked' : '' }} data-id="{{ $rank->id }}">
                        <span class="slider round"></span>
                    </label>
                </td>


            </tr>
            @endforeach
        </tbody>
    </table>


    </div>
    </div>

    <!-- Edit Rank Modal -->
    <div class="modal fade" id="editRankModal" tabindex="-1" role="dialog" aria-labelledby="editRankModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="updateRankForm">
                    <input type="hidden" id="editRankId" name="id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editRankModalLabel">Edit Rank</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_rank_name">Rank Name</label>
                            <input type="text" class="form-control" id="edit_rank_name" name="rank_name" required>
                        </div>
                        <div class="form-group">
                            <label for="captcha">Enter Captcha</label>
                            <div style="position: relative;">
                                <img id="rankCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                                <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha('rankCaptchaImage')"></i>
                            </div>
                            <input type="text" name="captcha" class="form-control" minlength="6" maxlength="6" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
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
            var rankId = $checkbox.data('id');
            var isActive = $checkbox.is(':checked');
            var originalState = isActive;

            $checkbox.prop('disabled', true);


            Swal.fire({
                title: 'Are you sure?',
                text: `You want to ${isActive ? 'activate' : 'deactivate'} this Rank?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.isConfirmed) {

                    var base_url = "{{ url('/') }}";

                    $.ajax({
                        url: base_url+'/rank/' + rankId + '/update-status',
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

        $(document).on('click', '.deleteRankBtn', function() {
            var rankId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: `You want to delete this rank?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('deleteRank') }}",
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        data: {
                            rank_id: rankId
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', response.msg || 'Rank deleted successfully!', 'success');
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