@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <!-- <h1>Site Code Details</h1> -->
    <div class="row">
    <div class="col-12">
    <a href="{{ route('addSiteCode') }}" class="btn btn-primary mb-3 float-left" data-toggle="tooltip" data-placement="left" title="Add New Site Code"><i class="fa-solid fa-plus"></i></a>

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


    <table class="table table-bordered table-striped" id="myTable" data-export-columns="0,1,2 ,3 ,4">
    <select id="pageLengthSelect" class="form-control "  >
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="all">All</option>
            </select>
       
        <thead class="thead-dark">
            <tr>
                <th scope="col" class="text-center">Sl. No.</th>
                <th scope="col">Code</th>
                <th scope="col">Name</th>
                <th scope="col">Address</th>
                <th scope="col">State</th>
                <th scope="col" class="text-center actions-column">Actions</th>

            </tr>
        </thead>
        <tbody>
            @foreach($siteCodes as $site)
            <tr data-status="{{ is_null($site->deleted_at) ? 'active' : 'inactive' }}">
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $site->site_code ? $site->site_code : '' }}</td>
                <td>{{ $site->site_name ? $site->site_name : '' }}</td>
                <td>{{ $site->site_address ? $site->site_address : '' }}</td>
                <td>{{ $site->state ? $site->state->state_name : '' }}</td>

                <td class="text-center">
                <input type="hidden" class="status-value" value="0">




                   
                    <a href="{{ route('editSiteCode', $site->id) }}" class="btn btn-primary btn-edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                   

                    <label class="switch btn-toggle">
                        <input type="checkbox" {{ is_null($site->deleted_at) ? 'checked' : '' }} data-id="{{ $site->id }}">
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

        var csrfToken = $('meta[name="csrf-token"]').attr('content');


        $(document).on('change', '.switch input', function() {
            var $checkbox = $(this);
            var sitecodeId = $checkbox.data('id');
            var isActive = $checkbox.is(':checked');

            var originalState = isActive;

            $checkbox.prop('disabled', true);

            Swal.fire({
                title: 'Are you sure?',
                text: `You want to ${isActive ? 'activate' : 'deactivate'} this Plant Site?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.isConfirmed) {

                    var base_url = "{{ url('/') }}";

                    $.ajax({
                        url: base_url+'/site-code/' + sitecodeId + '/update-status',
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
                                    msg: response.message || 'Plant Site updated successfully!',
                                    type: 'success'
                                });
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                $checkbox.prop('checked', !originalState);
                                FancyAlerts.show({
                                    msg: response.message || 'Error updating Plant Site',
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
            var site_id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: `You want to delete this Site Code ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('deleteSiteCode') }}",
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        data: {
                            sitecode_id: site_id
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', response.msg || 'Plant Site deleted successfully!', 'success');
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