@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <div class="text-left mb-3">
        <button type="button" class="btn back-btn" onclick="window.location='{{ route('manageOtherStaff') }}'">Back</button>
    </div>
    <form id="updateOtherStaffForm" action="{{ route('updateOtherStaff', $staff->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="editOtherStaffId" value="{{ $staff->id }}">
        <div class="card card-outline-secondary staff-form">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group staff-form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $staff->name }}" placeholder="Enter Name" required>
                    </div>
                    <div class="col-md-4 form-group staff-form-group">
                        <label for="gender">Gender</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            @foreach($genders as $gender)
                            <option value="{{ $gender->id }}" {{ $staff->gender_id == $gender->id ? 'selected' : '' }}>{{ $gender->gender_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group staff-form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" class="form-control" id="dob" name="dob" value="{{ $staff->dob }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group staff-form-group">
                        <label for="nationality">Nationality</label>
                        <select class="form-control" id="nationality" name="nationality" required>
                            <option value="">Select Nationality</option>
                            @foreach($nationalities as $nationality)
                            @if($nationality->id == $staff->nationality_id)
                            <option value="{{ $nationality->id }}" selected>
                                {{ $nationality->country_name }}
                            </option>
                            @else
                            <option value="{{ $nationality->id }}">
                                {{ $nationality->country_name }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group staff-form-group">
                        <label for="place_of_birth">Place of Birth</label>
                        <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" value="{{ $staff->place_of_birth }}" placeholder="Enter Place of Birth" required>
                    </div>
                    <div class="col-md-4 form-group staff-form-group">
                        <label for="passport_number">Passport Number</label>
                        <input type="text" class="form-control" id="passport_number" name="passport_number" value="{{ $staff->passport_number }}" placeholder="Enter Passport Number" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group staff-form-group">
                        <label for="unlp_number">UNLP Number</label>
                        <input type="text" class="form-control" id="unlp_number" name="unlp_number" value="{{ $staff->unlp_number }}" placeholder="Enter UNLP Number">
                    </div>

                    <div class="col-md-4 form-group staff-form-group">
                        <label for="designationId">Designation</label>
                        <select class="form-control" id="designationId" name="designationId" required>
                            <option value="">Select Designation</option>
                            @foreach($designations->sortBy('designation_name') as $designation)
                            <option value="{{ $designation->id }}" {{ $staff->designation_id == $designation->id ? 'selected' : '' }}>
                                {{ $designation->designation_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    
                    <div class="col-md-4 form-group staff-form-group">
                        <label for="rank">Rank</label>
                        <select class="form-control" id="rank" name="rank" required>
                            <option value="">Select Rank</option>
                            @foreach($ranks as $rank)
                            <option value="{{ $rank->id }}" {{ $staff->rank_id == $rank->id ? 'selected' : '' }}>{{ $rank->rank_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group staff-form-group">
                        <label for="qualifications">Qualifications</label>
                        <textarea class="form-control" id="qualifications" name="qualifications" rows="6" placeholder="Enter Qualifications">{{ $staff->qualifications }}</textarea>
                    </div>

                    <div class="col-md-6 form-group staff-form-group">
                        <label for="professional_experience">Professional Experience</label>
                        <textarea class="form-control" id="professional_experience" name="professional_experience" rows="6" placeholder="Enter Professional Experience">{{ $staff->professional_experience }}</textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group staff-form-group">
                        <label for="scope_of_access">Scope of Access</label>
                        <textarea class="form-control" id="scope_of_access" name="scope_of_access" rows="2" placeholder="Enter Professional Experience">{{ $staff->scope_of_access }}</textarea>
                    </div>

                    <div class="col-4">
                        <label for="opcw_communication_date">OPCW Communication Date</label>
                       <input type="date" class="form-control" id="opcw_communication_date" name="opcw_communication_date"
    value="{{ isset($staff) ? \Carbon\Carbon::parse($staff->opcw_communication_date)->format('Y-m-d') : '' }}">

                    </div>
        
                    <div class="col-4">
                        <label for="deletion_date">Deletion Date</label>
                        <input type="date" class="form-control" id="deletion_date" name="deletion_date"
    value="{{ isset($staff) && $staff->deletion_date ? \Carbon\Carbon::parse($staff->deletion_date)->format('Y-m-d') : '' }}">

                    </div>

                </div>
                


                <div class="row">
            
                    <div class="col-4">
                        <label for="routine_status" id="routine_status_col">Security Status</label>
                        <select name="routine_status_id" class="form-control">
                            <option value="">Select Security Status</option>
                            @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ (isset($staff) && $status->id == $staff->security_status) ? 'selected' : '' }}>
                                {{ $status->status_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>


                    
                <div class="col-8 form-group inspector-form-group text-left">
                    <label for="remarks">Remarks</label>
                    <textarea class="form-control" id="remarks" name="remarks" rows="5">{{ isset($staff) && $staff->remarks ? $staff->remarks : '' }}</textarea>
                </div>

    

                </div>


           

                <!-- <div class="row mb-3 justify-content-between">

                    <div class="col-md-4 form-group staff-form-group ">
                        <label for="captcha">Enter Captcha</label>
                        <div style="position: relative;">
                            <img id="editInspectorCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                            <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha('editInspectorCaptchaImage')"></i>

                        </div>
                        <input type="text" name="captcha" class="form-control mt-3" minlength="6" maxlength="6" placeholder="Enter Captcha" required>
                    </div>
                </div> -->
            </div>
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="reset" class="btn btn-danger">Reset</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('style')
<style>
    body {
        background-color: #f8f9fa;
    }

    .staff-form {
        border: none;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .border {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }


    .staff-form-group .form-control,
    .staff-form-group .form-control-file {
        border-radius: 0.25rem;
    }

    @media (max-width: 576px) {
        .staff-form-group {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>
@endpush

@push('script')
<script>
    $(document).ready(function() {
        // Store initial values
        var initialData = {
            name: $('#name').val(),
            gender: $('#gender').val(),
            dob: $('#dob').val(),
            nationality: $('#nationality').val(),
            place_of_birth: $('#place_of_birth').val(),
            passport_number: $('#passport_number').val(),
            unlp_number: $('#unlp_number').val(),
            designationId: $('#designationId').val(),
            rank: $('#rank').val(),
            qualifications: $('#qualifications').val(),
            professional_experience: $('#professional_experience').val(),
            scope_of_access: $('#scope_of_access').val(),
            opcw_communication_date: $('#opcw_communication_date').val(),
            deletion_date: $('#deletion_date').val(),
            routine_status_id: $('#routine_status').val(),
            remarks: $('#remarks').val(),
        
        };

        var successMessage = "{{ session('success') }}";
        if (successMessage) {
            FancyAlerts.show({
                msg: successMessage,
                type: 'success'
            });
        }

        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $('#updateOtherStaffForm').submit(function(e) {
            e.preventDefault(); 

            var hasChanges = false;
            var formData = new FormData(this);

            $.each(initialData, function(key, value) {
                if ($('#' + key).val() != value) {
                    hasChanges = true;
                    return false; 
                }
            });

            if (!hasChanges) {
                FancyAlerts.show({
                    msg: 'No changes were made.',
                    type: 'info'
                });
                return; 
            }

        
            var staffId = $('#editOtherStaffId').val(); 

            $.ajax({
                url: "{{ route('updateOtherStaff', '') }}/" + staffId,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        FancyAlerts.show({
                            msg: response.msg || 'OPCW Staff updated successfully!',
                            type: 'success'
                        });
                        
                        setTimeout(function() {
                            window.location.href = "{{ route('editOtherStaff', '') }}/" + staffId;
                        }, 2000);
                    } else {
                        FancyAlerts.show({
                            msg: 'Error: ' + response.msg,
                            type: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    var response = JSON.parse(xhr.responseText);
                    var message = response.msg ? response.msg : 'An unknown error occurred';
                    FancyAlerts.show({
                        msg: 'Error: ' + message,
                        type: 'error'
                    });
                }
            });
        });

    });


</script>
@endpush