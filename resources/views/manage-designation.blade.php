@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <!-- <h1>Designation Details</h1> -->


    <div class="row">
    <div class="col-12">
    <a href="{{ route('addDesignation') }}" class="btn btn-primary mb-3 float-left" data-toggle="tooltip" data-placement="left" title="Add New Designation"><i class="fa-solid fa-plus"></i></a>

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
    <!-- Designation Details Table -->
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
                <th scope="col" class="text-center">Record ID</th>
                <th scope="col">Designation Name</th>
                <th scope="col" class="text-center actions-column">Actions</th>
            </tr>
        </thead>
        <tbody id="designationTableBody">
            @foreach($designations as $designation)
            <tr data-status="{{ is_null($designation->deleted_at) ? 'active' : 'inactive' }}">
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ $designation->id }}</td>
                <td>{{ $designation->designation_name ? $designation->designation_name : 'N/A' }}</td>

                <td class="text-center">



                   
                    <a href="{{ route('editDesignation', $designation->id) }}" class="btn btn-primary btn-edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                   

                    <label class="switch btn-toggle">
                        <input type="checkbox" {{ is_null($designation->deleted_at) ? 'checked' : '' }} data-id="{{ $designation->id }}">
                        <span class="slider round"></span>
                    </label>
                </td>


            </tr>
            @endforeach
        </tbody>
    </table>


    </div>
    </div>

    <!-- Edit Designation Modal -->
    <div class="modal fade" id="editDesignationModal" tabindex="-1" role="dialog" aria-labelledby="editDesignationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="updateDesignationForm">
                    <input type="hidden" id="editDesignationId" name="id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDesignationModalLabel">Edit Designation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_designation_name">Designation Name</label>
                            <input type="text" class="form-control" id="edit_designation_name" name="designation_name" required>
                        </div>
                        <div class="form-group">
                            <label for="captcha">Enter Captcha</label>
                            <div style="position: relative;">
                                <img id="designationCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                                <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha('designationCaptchaImage')"></i>
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

        $(document).on('change', '.switch input', function() {
            var $checkbox = $(this);
            var designationId = $checkbox.data('id');
            var isActive = $checkbox.is(':checked');
            var originalState = isActive;

            $checkbox.prop('disabled', true);


            Swal.fire({
                title: 'Are you sure?',
                text: `You want to ${isActive ? 'activate' : 'deactivate'} this Designation?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.isConfirmed) {

                    var base_url = "{{ url('/') }}";

                    $.ajax({
                        url: base_url+'/designation/' + designationId + '/update-status',
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

    });
</script>
@endpush