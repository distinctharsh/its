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
                        <label for="nationality">Nationality</label>
                        <select class="form-control" id="nationality" name="nationality" required>
                            <option value="">Select Nationality</option>
                            @foreach($nationalities as $nationality)
                            <option value="{{ $nationality->id }}">{{ $nationality->country_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="place_of_birth">Place of Birth</label>
                        <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" placeholder="Enter Place of Birth" required>
                    </div>
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="passport_number">Passport Number</label>
                        <input type="text" class="form-control" id="passport_number" name="passport_number" placeholder="Enter Passport Number (e.g., A12345678)" required oninput="this.value = this.value.toUpperCase()">
                    </div>


                </div>

                <div class="row">
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="unlp_number">UNLP Number</label>
                        <input type="text" class="form-control" id="unlp_number" name="unlp_number" placeholder="Enter UNLP Number" oninput="this.value = this.value.toUpperCase()">
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





                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="ib_clearance">IB Clearance <span class="text-danger upload-font">*PDF only (Max. Size 5MB)</span></label>
                        <input type="file" class="form-control-file" id="ib_clearance" name="ib_clearance" accept="application/pdf">
                    </div>

                    <div class="col-4" id="ib_status_col">
                        <label for="ib_status">IB Status</label>
                        <select name="ib_status_id" class="form-control">
                            <option value="">Select IB Status</option>
                            @foreach($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="raw_clearance">RAW Clearance <span class="text-danger upload-font">*PDF only (Max. Size 5MB)</span></label>
                        <input type="file" class="form-control-file" id="raw_clearance" name="raw_clearance" accept="application/pdf">
                    </div>

                    <div class="col-4" id="raw_status_col">
                        <label for="raw_status">RAW Status</label>
                        <select name="raw_status_id" class="form-control">
                            <option value="">Select Status</option>
                            @foreach($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="mea_clearance">MEA Clearance <span class="text-danger upload-font">*PDF only (Max. Size 5MB)</span></label>
                        <input type="file" class="form-control-file" id="mea_clearance" name="mea_clearance" accept="application/pdf">
                    </div>

                    <div class="col-4" id="mea_status_col">
                        <label for="mea_status">MEA Status</label>
                        <select name="mea_status_id" class="form-control">
                            <option value="">Select Status</option>
                            @foreach($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->status_name }}</option>
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


                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12 form-group inspector-form-group border rounded p-3 bg-light mt-3">
                        <h5 class="text-left" style="position: absolute; top: -15px; left: 0; background: white; padding: 0 10px;">Routine Inspection Profile</h5>
                        <span type="button" class="  mb-3 position-absolute" style="top: 4px; right: 4px;" \
                            onclick="
                         // Reset radio buttons
                                document.querySelectorAll('input[name=routine_inspection_category]').forEach(radio => radio.checked = false); 
                                
                                // Reset select elements
                              
                                
                                var purposeSelect = document.querySelector('select[name=routine_purpose_of_deletion]');
                                if (purposeSelect) purposeSelect.value = ''; 
                                
                                var departmentSelect = document.querySelector('select[name=routine_objection_department]');
                                if (departmentSelect) departmentSelect.value = ''; 
                                
                                // Reset input fields
                                var joiningDateInput = document.querySelector('input[name=routine_joining_date]');
                                if (joiningDateInput) joiningDateInput.value = ''; 
                                
                                var deletionDateInput = document.querySelector('input[name=routine_deletion_date]');
                                if (deletionDateInput) deletionDateInput.value = ''; 
                                
                                var otherReasonInput = document.querySelector('input[name=other_reason]');
                                if (otherReasonInput) otherReasonInput.value = ''; 
                                
                                // Reset the textarea
                                var remarksTextarea = document.querySelector('textarea[name=routine_remarks]');
                                if (remarksTextarea) remarksTextarea.value = ''; 

                                // Hide specific fields if necessary
                                document.getElementById('routine_purpose_of_deletion_wrapper').style.display = 'none';
                                document.getElementById('routine_objection_department').style.display = 'none';
                                document.getElementById('routine_objection_file_section').style.display = 'none';
                        ">
                            <i class="fa-regular fa-trash-can text-danger"></i>
                        </span>
                        <div id="routineCategories" class="d-flex flex-row justify-content-between mt-3">
                            @foreach($inspection_categories as $category)
                            @if($category->is_challenge == 0)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="routine_inspection_category" id="routineCategory{{ $category->id }}" value="{{ $category->id }}">
                                <label class="form-check-label" for="routineCategory{{ $category->id }}">
                                    {{ str_replace('Ã¢â‚¬â€œ', '–', $category->category_name) }}
                                </label>
                            </div>
                            @endif
                            @endforeach
                        </div>

                        <div class="row mt-3">
                            <div class="col-4">
                                <label for="routine_joining_date">Communication Date</label>
                                <input type="text" class="form-control datepicker" id="routine_joining_date" name="routine_joining_date" placeholder="dd/ mm/ yyyy">
                            </div>
                            <div class="col-4">
                                <label for="routine_deletion_date">Deletion Date</label>
                                <input type="text" class="form-control datepicker" id="routine_deletion_date" name="routine_deletion_date" placeholder="dd/mm/yyyy">
                            </div>

                            <div class="col-4" id="routine_purpose_of_deletion_wrapper" style="display:none;">
                                <label for="routine_purpose_of_deletion">Purpose of Deletion</label>
                                <select name="routine_purpose_of_deletion" class="form-control" id="routine_purpose_of_deletion">
                                    <option value="">Select Purpose</option>
                                    @foreach($purposeOfDeletions as $purpose)
                                    <option value="{{ $purpose->purpose_name }}">{{ $purpose->purpose_name }}</option>
                                    @endforeach
                                    <option value="Other Reason">Other Reason</option>
                                </select>

                                <input type="text" id="other_reason" class="form-control" style="display: none;" placeholder="Please specify the purpose" />
                            </div>

                            

                            <div class="col-4 mt-4" id="routine_objection_department" style="display: none;">
                                <label for="routine_objection_department">Departments</label>
                                <select name="routine_objection_department" class="form-control">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mt-4 form-group inspector-form-group" id="routine_objection_file_section" style="display:none;">
                                <label for="routine_objection_document">Objection Document <span class="text-danger upload-font">*PDF only (Max. Size 5MB)</span></label>
                                <input type="file" class="form-control-file" id="routine_objection_document" name="routine_objection_document" accept="application/pdf">
                            </div>

                            <div class="col-8 mt-4 form-group inspector-form-group">
                                <label for="routine_remarks">Remarks</label>
                                <textarea class="form-control" id="routine_remarks" name="routine_remarks" rows="5"></textarea>
                            </div>
                        </div>


                    </div>

                    <div class="col-md-12 form-group inspector-form-group border rounded p-3 bg-light mt-3">

                        <h5 class="text-left" style="position: absolute; top: -15px; left: 0; background: white; padding: 0 10px;">Challenge Inspection Profile</h5>
                        <span type="button" class=" mb-3 position-absolute" style="top: 4px; right: 4px;" onclick="
                        // Reset radio buttons
                            document.querySelectorAll('input[name=challenge_inspection_category]').forEach(radio => radio.checked = false); 
                            
                            // Reset select elements
                          
                            
                            var challengePurposeSelect = document.querySelector('select[name=challenge_purpose_of_deletion]');
                            if (challengePurposeSelect) challengePurposeSelect.value = ''; 
                            
                            var challengeDepartmentSelect = document.querySelector('select[name=challenge_objection_department]');
                            if (challengeDepartmentSelect) challengeDepartmentSelect.value = ''; 
                            
                            // Reset input fields
                            var challengeJoiningDateInput = document.querySelector('input[name=challenge_joining_date]');
                            if (challengeJoiningDateInput) challengeJoiningDateInput.value = ''; 
                            
                            var challengeDeletionDateInput = document.querySelector('input[name=challenge_deletion_date]');
                            if (challengeDeletionDateInput) challengeDeletionDateInput.value = ''; 
                            
                            var challengeOtherReasonInput = document.querySelector('input[name=challenge_other_reason]');
                            if (challengeOtherReasonInput) challengeOtherReasonInput.value = ''; 
                            
                            // Reset the textarea
                            var challengeRemarksTextarea = document.querySelector('textarea[name=challenge_remarks]');
                            if (challengeRemarksTextarea) challengeRemarksTextarea.value = ''; 

                            // Hide specific fields if necessary
                            document.getElementById('challenge_purpose_of_deletion_wrapper').style.display = 'none';
                            document.getElementById('challenge_objection_department').style.display = 'none';
                            document.getElementById('challenge_objection_file_section').style.display = 'none';
                        
                        
                        
                        
                        ">
                            <i class="fa-regular fa-trash-can text-danger"></i>
                        </span>
                        <div id="challengeCategories" class="d-flex flex-row justify-content-between mt-3">
                            @foreach($inspection_categories as $category)
                            @if($category->is_challenge == 1)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="challenge_inspection_category" id="challengeCategory{{ $category->id }}" value="{{ $category->id }}">
                                <label class="form-check-label" for="challengeCategory{{ $category->id }}">
                                    {{ str_replace('Ã¢â‚¬â€œ', '–', $category->category_name) }}
                                </label>
                            </div>
                            @endif
                            @endforeach
                        </div>

                        <div class="row mt-3">
                            <div class="col-4">
                                <label for="challenge_joining_date">Communication Date</label>
                                <input type="text" class="form-control datepicker" id="challenge_joining_date" name="challenge_joining_date" placeholder="dd/ mm/ yyyy">
                            </div>
                            <div class="col-4">
                                <label for="challenge_deletion_date">Deletion Date</label>
                                <input type="text" class="form-control datepicker" id="challenge_deletion_date" name="challenge_deletion_date" placeholder="dd/mm/yyyy">
                            </div>

                            <div class="col-4" id="challenge_purpose_of_deletion_wrapper" style="display: none;">
                                <label for="challenge_purpose_of_deletion">Purpose of Deletion</label>
                                <select name="challenge_purpose_of_deletion" class="form-control" id="challenge_purpose_of_deletion">
                                    <option value="">Select Purpose</option>
                                    @foreach($purposeOfDeletions as $purpose)
                                    <option value="{{ $purpose->purpose_name }}">{{ $purpose->purpose_name }}</option>
                                    @endforeach
                                    <option value="Other Reason">Other Reason</option>
                                </select>

                                <input type="text" id="challenge_other_reason" class="form-control" style="display: none;" placeholder="Please specify the purpose" />
                            </div>

                            

                            <div class="col-4 mt-4" id="challenge_objection_department" style="display: none;">
                                <label for="challenge_objection_department">Departments </label>
                                <select name="challenge_objection_department" class="form-control">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-4 mt-4 form-group inspector-form-group" id="challenge_objection_file_section" style="display:none;">
                                <label for="challenge_objection_document">Objection Document <span class="text-danger upload-font">*PDF only (Max. Size 5MB)</span></label>
                                <input type="file" class="form-control-file" id="challenge_objection_document" name="challenge_objection_document" accept="application/pdf">
                            </div>


                            <div class="col-8 mt-4 form-group inspector-form-group">
                                <label for="challenge_remarks">Remarks</label>
                                <textarea class="form-control" id="challenge_remarks" name="challenge_remarks" rows="5"></textarea>
                            </div>
                        </div>



                    </div>
                </div>

                <!-- <div class="row mb-3 justify-content-between">

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="captcha">Enter Captcha</label>
                        <div style="position: relative;">
                            <img id="addInspectorCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                            <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha('addInspectorCaptchaImage')"></i>

                        </div>
                        <input type="text" name="captcha" class="form-control mt-3" minlength="6" maxlength="6" placeholder="Enter Captcha" required>
                    </div>
                </div> -->


            </div>
            <div class="card-footer text-center">
    <button type="submit" class="btn btn-primary" id="submitBtn">
        <span id="submitText">Submit</span>
        <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
    </button>
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

        var deletionDate = $('#routine_deletion_date').val();
        if (deletionDate) {
            $('#routine_status_col').addClass('mt-4'); // Add mt-4 if routine_deletion_date has a value
        }
        var challengeDeletionDate = $('#challenge_deletion_date').val();
       

        // Also add class when the user enters a value in the deletion date field
        $('#routine_deletion_date').on('change', function() {
            var deletionDate = $(this).val();
            if (deletionDate) {
                $('#routine_status_col').addClass('mt-4');
            } else {
                $('#routine_status_col').removeClass('mt-4');
            }
        });
       

        $('#routine_deletion_date').on('change', function() {
            var routineDeletionDate = $(this).val().trim();
            if (routineDeletionDate) {
                $('#routine_purpose_of_deletion_wrapper').show();
            } else {
               
                $('#routine_purpose_of_deletion_wrapper').hide();
            }
        });

        $('#challenge_deletion_date').on('change', function() {
            var challengeDeletionDate = $(this).val().trim();
            if (challengeDeletionDate) {
                $('#challenge_purpose_of_deletion_wrapper').show();
            } else {
             
                $('#challenge_purpose_of_deletion_wrapper').hide();
            }
        });


       
        $('select[name="routine_objection_department"]').on('change', function() {
            var selectedStatusId = $(this).val();
           
            if (selectedStatusId) {
                $('#routine_objection_file_section').show();
            } else {
                $('#routine_objection_file_section').hide();
            }
        });
       
        $('select[name="challenge_objection_department"]').on('change', function() {
            var selectedStatusId = $(this).val();
            if (selectedStatusId) {
                $('#challenge_objection_file_section').show();
            } else {
                $('#challenge_objection_file_section').hide();
            }
        });


        $('#routine_purpose_of_deletion').on('change', function() {
            if ($(this).val() === "Other Reason") {
                $('#other_reason').show();
            } else {
                $('#other_reason').hide();
                $('#other_reason').val('');
            }
        });

        $('#other_reason').on('input', function() {
            $('#routine_purpose_of_deletion').val("Other Reason");
        });



        $('#challenge_purpose_of_deletion').on('change', function() {
          
            if ($(this).val() === "Other Reason") {
                $('#challenge_other_reason').show();
            } else {
                $('#challenge_other_reason').hide();
                $('#challenge_other_reason').val('');
            }
        });

        $('#challenge_other_reason').on('input', function() {
            $('#challenge_purpose_of_deletion').val("Other Reason");
        });




        function validateForm() {
            var isValid = true;
            var routineFilled = !!$('input[name="routine_inspection_category"]:checked').val();
            var challengeFilled = !!$('input[name="challenge_inspection_category"]:checked').val();

            if (!routineFilled && !challengeFilled) {
                isValid = false;
                FancyAlerts.show({
                    msg: 'Please fill at least one inspection category (Routine or Challenge).',
                    type: 'error'
                });
            }

            if (routineFilled) {
                var routineJoiningDate = $('#routine_joining_date').val();
                if (!routineJoiningDate) {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Communication Date for Routine is required.',
                        type: 'error'
                    });
                }

            }

            var dob = $('#dob').val();
            var dobDate = new Date(dob);
            var today = new Date();
            var age = today.getFullYear() - dobDate.getFullYear();
            var monthDiff = today.getMonth() - dobDate.getMonth();

            if (dob === '' || age < 18 ||
                (age === 18 && monthDiff < 0) ||
                (age === 18 && monthDiff === 0 && today.getDate() < dobDate.getDate())) {
                isValid = false;
                FancyAlerts.show({
                    msg: 'Date of Birth must be at least 18 years old.',
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

            if (routineFilled) {
                var routineJoiningDate = $('#routine_joining_date').val();
               
                if (!routineJoiningDate) {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Communication Date for Routine is required.',
                        type: 'error'
                    });
                }
              
            }

            if (challengeFilled) {
                var challengeJoiningDate = $('#challenge_joining_date').val();
               
                if (!challengeJoiningDate) {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Communication Date for Challenge is required.',
                        type: 'error'
                    });
                }
              
            }

            var ibClearance = $('#ib_clearance')[0].files[0];
            if (ibClearance) {
                var fileType = ibClearance.type;
                var fileSize = ibClearance.size;

                if (fileType !== 'application/pdf') {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Intelligence Bureau Clearance must be a PDF file.',
                        type: 'error'
                    });
                } else if (fileSize > 5 * 1024 * 1024) {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Intelligence Bureau Clearance must be less than 5MB.',
                        type: 'error'
                    });
                }
            }

            var rawClearance = $('#raw_clearance')[0].files[0];
            if (rawClearance) {
                // Check file type
                var fileType = rawClearance.type;
                // Check file size (5MB = 5 * 1024 * 1024 bytes)
                var fileSize = rawClearance.size;

                if (fileType !== 'application/pdf') {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Research and Analysis Wing Clearance must be a PDF file.',
                        type: 'error'
                    });
                } else if (fileSize > 5 * 1024 * 1024) {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Research and Analysis Wing Clearance must be less than 5MB.',
                        type: 'error'
                    });
                }
            }
            // Clearance Certificate Validation
            var meaClearance = $('#mea_clearance')[0].files[0];
            if (meaClearance) {
                // Check file type
                var fileType = meaClearance.type;
                // Check file size (5MB = 5 * 1024 * 1024 bytes)
                var fileSize = meaClearance.size;

                if (fileType !== 'application/pdf') {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Ministry of External Affairs Clearance must be a PDF file.',
                        type: 'error'
                    });
                } else if (fileSize > 5 * 1024 * 1024) {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Ministry of External Affairs Clearance must be less than 5MB.',
                        type: 'error'
                    });
                }
            }


            return isValid;
        }

        $('#addInspectorForm').submit(function(e) {
            e.preventDefault();

            // Get the submit button and disable it
            var submitBtn = $('#submitBtn');
            var submitText = $('#submitText');
            var submitSpinner = $('#submitSpinner');

            // Disable button and show spinner
            submitBtn.prop('disabled', true);
            submitText.text('Processing...');
            submitSpinner.removeClass('d-none');

            if (validateForm()) {
                var formData = new FormData(this);

                // Check if 'Other Reason' is selected and append it to the FormData
                var routinePurpose = $('#routine_purpose_of_deletion').val();
                if (routinePurpose === "Other Reason" && $('#other_reason').val() !== "") {
                    formData.append('routine_purpose_of_deletion', $('#other_reason').val());
                } else if (routinePurpose !== "Other Reason") {
                    formData.append('routine_purpose_of_deletion', routinePurpose);
                }


                // Check if 'Other Reason' is selected for challenge purpose and append it to the FormData
                var challengePurpose = $('#challenge_purpose_of_deletion').val();
                if (challengePurpose === "Other Reason" && $('#challenge_other_reason').val() !== "") {
               
                    formData.append('challenge_purpose_of_deletion', $('#challenge_other_reason').val());
                } else if (challengePurpose !== "Other Reason") {
           
                    formData.append('challenge_purpose_of_deletion', challengePurpose);
                }

                function appendCategoryData(formData, isChallenge) {
                    var category = isChallenge ?
                        $('input[name="challenge_inspection_category"]:checked').val() :
                        $('input[name="routine_inspection_category"]:checked').val();

                    var joiningDate = isChallenge ? $('#challenge_joining_date').val() : $('#routine_joining_date').val();
                    

                    if (category) formData.append(isChallenge ? 'challenge_category_id' : 'routine_category_id', category);
                    if (joiningDate) formData.append(isChallenge ? 'challenge_date_of_joining' : 'date_of_joining', joiningDate);
                   
                }

                appendCategoryData(formData, false); // Routine
                appendCategoryData(formData, true); // Challenge


                $.ajax({
                    url: "{{ route('createInspector') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {

                        // Re-enable button and hide spinner
                        submitBtn.prop('disabled', false);
                        submitText.text('Submit');
                        submitSpinner.addClass('d-none');


                        if (response.success) {
                            FancyAlerts.show({
                                msg: response.msg || 'Inspector added successfully!',
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

                        // Re-enable button and hide spinner on error
                        submitBtn.prop('disabled', false);
                        submitText.text('Submit');
                        submitSpinner.addClass('d-none');
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
            }

            else {
                // If validation fails, re-enable the button
                submitBtn.prop('disabled', false);
                submitText.text('Submit');
                submitSpinner.addClass('d-none');
            }
        });
    });
</script>
@endpush