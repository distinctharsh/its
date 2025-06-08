@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <!-- <h1>Nationality Details</h1> -->

    <div class="row">
<div class="col-12">


        <a href="{{ route('addNationality') }}" class="btn btn-primary mb-3 float-left" data-toggle="tooltip" data-placement="left" title="Add New Nationality"><i class="fa-solid fa-plus"></i></a>
    
    
    
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


        



    <!-- Nationality Details Table -->
    <table class="table table-bordered table-striped" id="myTable">


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
                <th scope="col" class="text-center">Record ID</th>
                <th scope="col" >Nationality Name</th>
                <th scope="col" class="text-center actions-column">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nationalities as $nationality)
            <tr data-status="{{ is_null($nationality->deleted_at) ? 'active' : 'inactive' }}">
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ $nationality->id }}</td>
                <td >{{ $nationality->country_name }}</td>
                <td class="text-center">
                    <a href="{{ route('editNationality', $nationality->id) }}" class="btn btn-primary btn-edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                   


                    <label class="switch btn-toggle">
                        <input type="checkbox" {{ is_null($nationality->deleted_at) ? 'checked' : '' }} data-id="{{ $nationality->id }}">
                        <span class="slider round"></span>
                    </label>
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>


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
            var nationalityId = $checkbox.data('id');
            var isActive = $checkbox.is(':checked');

            var originalState = isActive;


            Swal.fire({
                title: 'Are you sure?',
                text: `You want to ${isActive ? 'activate' : 'deactivate'} this Nationality ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.isConfirmed) {

                    var base_url = "{{ url('/') }}";

                    $.ajax({
                        url: base_url+'/nationality/' + nationalityId + '/update-status',
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

        $(document).on('click', '.deleteNationalityBtn', function() {
            var nationalityId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: `You want to delete this nationality?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('deleteNationality') }}",
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        data: {
                            nationality_id: nationalityId
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', response.msg || 'Nationality deleted successfully!', 'success');
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