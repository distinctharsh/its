@extends('layouts.layout')

@section('content')

<div class="container-fluid mt-4 table-responsive">
    <!-- <h1>Inspector Data Management</h1> -->


    <div class="row mb-2">
    <div class="col-12">
        @if(!auth()->user()->hasRole('Viewer'))
        <a href="{{ route('addOtherStaff') }}" class="btn btn-primary  float-left" data-toggle="tooltip" data-placement="left" title="Add"><i class="fa-solid fa-plus"></i></a>
        @endif
        





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

             @if(strtolower(auth()->user()->role->name) === 'admin' || strtolower(auth()->user()->role->name) === 'user')
                <div class="form-check mr-3">
                    <input class="form-check-input" type="radio" name="statusFilter" id="statusDraft" value="draft">
                    <label class="form-check-label text-dark" for="statusDraft">Draft</label>
                </div>

                    <div class="form-check mr-3">
                    <input class="form-check-input" type="radio" name="statusFilter" id="statusReverted" value="reverted">
                    <label class="form-check-label text-dark" for="statusReverted">Reverted</label>
                </div>
            @endif

           
        </div>


        </div>
    
    </div>


    <table class="table table-bordered table-striped" id="myTable">
   
  
        <select id="pageLengthSelect" class="form-control mb-3"  >
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="all">All</option>
        </select>

        <thead class="thead-dark">
            <tr>
                @if($otherStaffs->isEmpty())
                    <th scope="col" class="text-center">
                        Records
                    </th>
                @else
                    <th scope="col" class="text-center">Sl. No.</th>
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
                    <th scope="col" class="text-center">Scope Of Access</th>
                    <th scope="col" class="text-center">Security Status</th>
                    <th scope="col" class="text-center">OPCW Communication Date</th>
                    <th scope="col" class="text-center">Deletion Date</th>
                    <th scope="col" class="text-center">Remarks</th>
                    <th scope="col" class="text-center">Actions</th>
                @endif
            </tr>
        </thead>
        <tbody id="inspectorTableBody">

            @if($otherStaffs->isEmpty())
            <tr>
                <td colspan="6" class="text-center">No Other Staff available.</td>
            </tr>
            @else

            @foreach($otherStaffs as $staff)
            <!-- <tr data-status="{{ is_null($staff->deleted_at) ? 'active' : 'inactive' }}" > -->
            <tr data-status="{{ $staff->is_reverted ? 'reverted' : ($staff->is_draft ? 'draft' : (is_null($staff->deleted_at) ? 'active' : 'inactive')) }}">
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $staff->name ? $staff->name : 'N/A' }} <br></td>
                <td class="text-center">{{ $staff->gender->gender_name ? $staff->gender->gender_name : 'N/A' }} </td>
                <td class="text-center">
                    {{ $staff->dob ? \Carbon\Carbon::parse($staff->dob)->format('d-m-Y') : 'N/A' }}
                </td>
                <td class="text-center">{{ $staff->place_of_birth ? $staff->place_of_birth : 'N/A' }} </td>
                
                <td class="text-center">
                    {{ $staff->nationality && $staff->nationality->country_name ? $staff->nationality->country_name : '' }}
                </td>
                <td class="text-center remarks-text">{{ $staff->unlp_number ? $staff->unlp_number : '' }}</td>
                <td class="text-center remarks-text">{{ $staff->passport_number ? $staff->passport_number : '' }}</td>
                <td class="text-center"> {{ $staff->designation? $staff->designation->designation_name : 'N/A' }}</td>
                <td class="text-center"> {{ $staff->rank ? $staff->rank->rank_name : 'N/A' }}  </td>
                
                
                <td class="text-center remarks-text">
                    {{ str_replace('Ã¢â‚¬â€œ', '–', $staff->qualifications) }}
                </td>
                <td class="text-center remarks-text">
                    {{ str_replace('Ã¢â‚¬â€œ', '–', $staff->professional_experience) }}
                </td>
                
                <td class="text-center"> {{ $staff->scope_of_access ? $staff->scope_of_access : 'N/A' }}  </td>
                <td class="text-center"> {{ $staff->status ? $staff->status->status_name : 'N/A' }}  </td>
                
                <td class="text-center">
                    {{ $staff->opcw_communication_date ? \Carbon\Carbon::parse($staff->opcw_communication_date)->format('d-m-Y') : 'N/A' }}
                </td>
                <td class="text-center">
                    {{ $staff->deletion_date ? \Carbon\Carbon::parse($staff->deletion_date)->format('d-m-Y') : 'N/A' }}
                </td>

                <td class=" text-center remarks-text">{{ $staff->remarks ? $staff->remarks : 'N/A' }}</td>
           
    
                <!-- <td class="text-center">

                    @php
                        $isLocked = isset($otherStaffLock) &&
                                    $otherStaffLock->locked &&
                                    $staff->created_at >= $otherStaffLock->from &&
                                    $staff->created_at <= $otherStaffLock->to;
                    @endphp

              

                    @if(!auth()->user()->hasRole('Viewer'))

                    @if(auth()->user()->hasRole('Admin'))
                        @if($isLocked)
                            <span class="btn btn-primary disabled btn-edit" title="Locked during this period">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </span>
                        @else
                            <a href="{{ route('editOtherStaff', $staff->id) }}" class="btn btn-primary btn-edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        @endif
                    @else
                        @if($staff->deleted_at != null)
                            <span class="btn btn-primary disabled btn-edit" title="Cannot edit deleted staff">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </span>
                        @elseif($isLocked)
                            <span class="btn btn-primary disabled btn-edit" title="Locked during this period">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </span>
                        @else
                            <a class="btn btn-primary btn-edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        @endif
                    @endif

                    @if(auth()->user()->hasRole('Admin'))
                    <label class="switch btn-toggle">
                        <input type="checkbox" {{ is_null($staff->deleted_at) ? 'checked' : '' }} data-id="{{ $staff->id }}">
                        <span class="slider round"></span>
                    </label>
                    @else
                    <label class="switch btn-toggle">
                        <input type="checkbox" {{ is_null($staff->deleted_at) ? 'checked' : '' }} data-id="{{ $staff->id }}" @if($staff->deleted_at != null) disabled @endif>
                        <span class="slider round"></span>
                    </label>
                    @endif
                    @endif
                </td> -->



                <td class="text-center">
    @php
        $isLocked = isset($otherStaffLock) &&
                    $otherStaffLock->locked &&
                    $staff->created_at >= $otherStaffLock->from &&
                    $staff->created_at <= $otherStaffLock->to;
    @endphp

    {{-- View Button (visible to all roles) --}}
    <a href="{{ route('otherstaff.show', $staff->id) }}" class="btn btn-eye">
        <i class="fa-solid fa-eye"></i>
    </a>

    @if(!auth()->user()->hasRole('Viewer'))
        {{-- Edit Button Logic --}}
        @if(auth()->user()->hasRole('Admin'))
            {{-- Admin can always edit unless locked --}}
            @if($isLocked)
                <span class="btn btn-primary disabled btn-edit" title="Locked during this period">
                    <i class="fa-solid fa-pen-to-square"></i>
                </span>
            @else
                <a href="{{ route('editOtherStaff', $staff->id) }}" class="btn btn-primary btn-edit">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
            @endif
        @else
            {{-- Regular Users (not Admin or Viewer) --}}
            @if($staff->deleted_at != null || $isLocked)
                <span class="btn btn-primary disabled btn-edit" 
                      title="{{ $staff->deleted_at != null ? 'Cannot edit deleted staff' : 'Locked during this period' }}">
                    <i class="fa-solid fa-pen-to-square"></i>
                </span>
            @else
                <a href="{{ route('editOtherStaff', $staff->id) }}" class="btn btn-primary btn-edit">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
            @endif
        @endif

        {{-- Toggle Switch --}}
        @if(auth()->user()->hasRole('Admin'))
            {{-- Admin can always toggle --}}
            <label class="switch btn-toggle">
                <input type="checkbox" {{ is_null($staff->deleted_at) ? 'checked' : '' }} data-id="{{ $staff->id }}">
                <span class="slider round"></span>
            </label>
        @else
            {{-- Regular Users can only toggle active records --}}
            <label class="switch btn-toggle">
                <input type="checkbox" {{ is_null($staff->deleted_at) ? 'checked' : '' }} 
                       data-id="{{ $staff->id }}" 
                       @if($staff->deleted_at != null) disabled @endif>
                <span class="slider round"></span>
            </label>
        @endif

        {{-- Approve/Revert Buttons (Admin only) --}}
        @if(auth()->user()->hasRole('Admin'))
            @if($staff->is_draft)
                <form action="{{ route('otherStaff.approve', $staff->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success btn-approve" title="Approve this staff">
                        <i class="fa-solid fa-check"></i> Approve
                    </button>
                </form>
            @endif

            @if(!$staff->is_draft && !$staff->is_reverted && is_null($staff->deleted_at))
                <form action="{{ route('otherStaff.revert', $staff->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-warning btn-revert" title="Mark as reverted for review">
                        <i class="fa-solid fa-rotate-left"></i> Revert
                    </button>
                </form>
            @endif
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
                text: `You want to ${isActive ? 'activate' : 'deactivate'} this OPCW Other Staff?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var base_url = "{{ url('/') }}";
                    $.ajax({
                        url: base_url+'/other-staff/' + inspectorId + '/update-status',
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