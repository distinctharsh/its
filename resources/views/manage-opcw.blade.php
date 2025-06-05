@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <!-- <h1>OPCW Fax Details Management</h1> -->

    <!-- Button to trigger modal -->
    @if(!auth()->user()->hasRole('Viewer'))
    <a href="{{ route('addOpcw') }}" class="btn btn-primary mb-3 float-right" data-toggle="tooltip" data-placement="left" title="Add"><i class="fa-solid fa-plus"></i></a>
    @endif

    <br>
    <br>
    <!-- OPCW Fax Details Table -->
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
                                <!-- Fax Date Section -->
                                <div class="col-12 col-md-5 mb-3 px-3 mt-3">
                                    <div class="d-flex align-items-center">
                                        <p class="text-dark mb-0 mr-2"><strong>Communication Date :</strong></p>
                                        <input type="text" id="startDate" class="form-control datepicker" placeholder="From" style="width:auto; margin-right:10px;">
                                        <input type="text" id="endDate" class="form-control datepicker" placeholder="To" style="width:auto;">
                                    </div>
                                </div>

                                <!-- Status Section -->
                                <div class="col-12 col-md-5 mb-3 d-flex justify-content-start px-3 mt-3">
                                <p class="text-dark mb-0 mr-2"><strong>Status :</strong></p>
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
                <th scope="col" class="text-center">Sl. No.</th>
                <th scope="col" class="text-center">Communication Date</th>
                <th scope="col" class="text-center">Document Number</th>
                <th scope="col" class="text-center">Reference Number</th>
                <th scope="col" class="text-center">Remarks</th>
                <th scope="col" class="text-center">Created At</th>
                @if(!auth()->user()->hasRole('Viewer'))
                <th scope="col" class="text-center actions-column">Actions</th>
                @endif


            </tr>
        </thead>
        <tbody id="inspectorTableBody">
            @foreach($faxes as $fax)
           
            
            <tr data-status="{{ is_null($fax->deleted_at) ? 'active' : 'inactive' }}" data-join-date="{{ $faxes->isNotEmpty() ? $fax->fax_date->format('Y-m-d') : '' }}">
            
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ $fax->fax_date ? $fax->fax_date->format('d M Y') : 'N/A' }} <br>
                    @if($fax->fax_document)
                    <a href="{{ url('storage/app/' . $fax->fax_document) }}" target="_blank" class="pdf-icon" data-toggle="tooltip" title="Document">
                        <i class="fa-solid fa-file-pdf"></i>
                    </a>
                    @endif
                </td>
                <td class="text-center">{{ $fax->fax_number ? $fax->fax_number : 'N/A' }}</td>
                <td class="text-center">{{ $fax->reference_number ? $fax->reference_number : 'N/A' }}</td>
                <td class=" text-center remarks-text">{{ $fax->remarks ? $fax->remarks : 'N/A' }}</td>
                <td class=" text-center remarks-text">{{ $fax->created_at ? $fax->created_at : 'N/A' }}</td>

                @if(!auth()->user()->hasRole('Viewer'))

                <td class="text-center">

                @php
                        $isLocked = isset($opcwLock) &&
                                    $fax->created_at >= $opcwLock->from &&
                                    $fax->created_at <= $opcwLock->to;
                    @endphp


    


                    @if(auth()->user()->hasRole('Admin'))
                        @if($isLocked)
                            <span class="btn btn-primary disabled btn-edit" title="Locked during this period">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </span>
                        @else
                            <a href="{{ route('editOpcw', $fax->id) }}" class="btn btn-primary btn-edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        @endif
                    @else
                        @if($fax->deleted_at != null)
                            <span class="btn btn-primary disabled btn-edit" title="Cannot edit deleted inspector">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </span>
                        @elseif($isLocked)
                            <span class="btn btn-primary disabled btn-edit" title="Locked during this period">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </span>
                        @else
                            <a href="{{ route('editOpcw', $fax->id) }}" class="btn btn-primary btn-edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        @endif
                    @endif



                    @if(auth()->user()->hasRole('Admin'))
                    <label class="switch btn-toggle">
                        <input type="checkbox" {{ is_null($fax->deleted_at) ? 'checked' : '' }} data-id="{{ $fax->id }}">
                        <span class="slider round"></span>
                    </label>
                    @else
                    <label class="switch btn-toggle">
                        <input type="checkbox" {{ is_null($fax->deleted_at) ? 'checked' : '' }} data-id="{{ $fax->id }}" @if($fax->deleted_at != null) disabled @endif>
                        <span class="slider round"></span>
                    </label>
                    @endif
                </td>

                @endif



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
            var faxId = $checkbox.data('id');
            var isActive = $checkbox.is(':checked');

            var originalState = isActive;

            $checkbox.prop('disabled', true);

            Swal.fire({
                title: 'Are you sure?',
                text: `You want to ${isActive ? 'activate' : 'deactivate'} this OPCW Fax?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var base_url = "{{ url('/') }}";
                    $.ajax({
                        url: base_url+'/fax/' + faxId + '/update-status',
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



        $(document).on('click', '.deleteFaxBtn', function() {
            var faxId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: `You want to delete this OPCW Fax ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('deleteOpcw') }}",
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        data: {
                            fax_id: faxId
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', response.msg || 'Opcw fax deleted successfully!', 'success');
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