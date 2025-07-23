@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <div class="text-left mb-3">
        <button type="button" class="btn back-btn" onclick="window.location='{{ route('manageInspector') }}'">Back</button>
    </div>
    <form id="addInspectorForm" enctype="multipart/form-data">
        <div class="card card-outline-secondary inspector-form">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" required>
                    </div>
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="gender">Gender</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            @foreach($genders as $gender)
                            <option value="{{ $gender->id }}">{{ $gender->gender_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" class="form-control" id="dob" name="dob" required>
                    </div>
                </div>

                <div class="row">
                    
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="place_of_birth">Place of Birth</label>
                        <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" placeholder="Enter Place of Birth" required>
                    </div>
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="nationality">Nationality</label>
                        <select class="form-control" id="nationality" name="nationality" required>
                            <option value="">Select Nationality</option>
                            @foreach($nationalities as $nationality)
                            <option value="{{ $nationality->id }}">{{ $nationality->country_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="unlp_number">UNLP Number</label>
                        <input type="text" class="form-control" id="unlp_number" name="unlp_number" placeholder="Enter UNLP Number">
                    </div>
                   

                </div>

                <div class="row">
                 
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="passport_number">Passport Number</label>
                        <input type="text" class="form-control" id="passport_number" name="passport_number" placeholder="Enter Passport Number (e.g., A12345678)" required>
                    </div>

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="designationId">Designation</label>
                        <select class="form-control" id="designationId" name="designationId">
                            <option value="">Select Designation</option>
                            @foreach($designations->sortBy('designation_name') as $designation)
                            <option value="{{ $designation->id }}">{{ $designation->designation_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="rank">Rank</label>
                        <select class="form-control" id="rank" name="rank">
                            <option value="">Select Rank</option>
                            @foreach($ranks as $rank)
                            <option value="{{ $rank->id }}">{{ $rank->rank_name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-6 form-group inspector-form-group">
                        <label for="qualifications">Qualifications</label>
                        <textarea class="form-control" id="qualifications" name="qualifications" rows="6"></textarea>
                    </div>

                    <div class="col-md-6 form-group inspector-form-group">
                        <label for="professional_experience">Professional Experience</label>
                        <textarea class="form-control" id="professional_experience" name="professional_experience" rows="6"></textarea>
                    </div>
                    <div class="col-md-6 form-group inspector-form-group">
                        <label for="scope_of_access">Scope of Access</label>
                        <textarea class="form-control" id="scope_of_access" name="scope_of_access" rows="2"></textarea>
                    </div>


                    <div class="col-4" id="security_status_col">
                        <label for="security_status">Security Status</label>
                        <select name="security_status_id" class="form-control">
                            <option value="">Select Security Status</option>
                            @foreach($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                            @endforeach
                        </select>
                    </div>


                </div>


                <div class="row">
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="communication_date">OPCW Communication Date</label>
                        <input type="date" class="form-control" id="communication_date" name="communication_date" required>
                    </div>


                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="deletion_date">Deletion Date</label>
                        <input type="date" class="form-control" id="deletion_date" name="deletion_date">
                    </div>

                    <div class="col-8 mt-4 form-group inspector-form-group">
                        <label for="remarks">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="5"></textarea>
                    </div>


                    <!-- <div class="col-md-4 form-group inspection-form-group">
                        <label for="captcha">Enter Captcha</label>
                        <div style="position: relative;">
                            <img id="addOtherStaffImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                            <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha('addOtherStaffImage')"></i>
                        </div>
                        <input type="text" name="captcha" class="form-control" minlength="6" maxlength="6" required>
                    </div> -->
                </div>
                <hr>
            


            </div>
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
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

    .inspector-form {
        border: none;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .inspector-form-group .form-control,
    .inspector-form-group .form-control-file {
        border-radius: 0.25rem;
    }

    .border {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }

    .captcha-image {
        width: 100%;
        height: auto;
        margin-bottom: 10px;
    }
</style>
@endpush

@push('script')
<script>
    $(document).ready(function() {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $('#addInspectorForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: "{{ route('createOtherStaff') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        FancyAlerts.show({
                            msg: response.msg || 'Other Staff added successfully!',
                            type: 'success'
                        });
                        setTimeout(function() {
                            location.reload();
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
                    var message;

                    if (response.msg) {
                        message = response.msg;
                    } else if (response.errors) {

                        message = Object.values(response.errors)
                            .flat()
                            .join(', ');
                    } else {
                        message = 'An unknown error occurred';
                    }

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