@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <div class="text-left mb-3">
        <button type="button" class="btn back-btn" onclick="window.location='{{ route('manageInspector') }}'">Back</button>
    </div>
    <form id="updateInspectorForm" action="{{ route('updateInspector', $inspector->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="editInspectorId" value="{{ $inspector->id }}">
        <div class="card card-outline-secondary inspector-form">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $inspector->name }}" placeholder="Enter Name" required>
                    </div>
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="gender">Gender</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            @foreach($genders as $gender)
                            <option value="{{ $gender->id }}" {{ $inspector->gender_id == $gender->id ? 'selected' : '' }}>{{ $gender->gender_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" class="form-control" id="dob" name="dob" value="{{ $inspector->dob }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="nationality">Nationality</label>
                        <select class="form-control" id="nationality" name="nationality" required>
                            <option value="">Select Nationality</option>
                            @foreach($nationalities as $nationality)
                            @if($nationality->id == $inspector->nationality_id)
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
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="place_of_birth">Place of Birth</label>
                        <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" value="{{ $inspector->place_of_birth }}" placeholder="Enter Place of Birth" required>
                    </div>
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="passport_number">Passport Number</label>
                        <input type="text" class="form-control" id="passport_number" name="passport_number" value="{{ $inspector->passport_number }}" placeholder="Enter Passport Number" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="unlp_number">UNLP Number</label>
                        <input type="text" class="form-control" id="unlp_number" name="unlp_number" value="{{ $inspector->unlp_number }}" placeholder="Enter UNLP Number">
                    </div>

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="designation_id">Designation</label>
                        <select class="form-control" id="designation_id" name="designation_id" required>
                            <option value="">Select Designation</option>
                            @foreach($designations->sortBy('designation_name') as $designation)
                            <option value="{{ $designation->id }}" {{ $inspector->designation_id == $designation->id ? 'selected' : '' }}>
                                {{ $designation->designation_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="rank">Rank</label>
                        <select class="form-control" id="rank" name="rank" required>
                            <option value="">Select Rank</option>
                            @foreach($ranks as $rank)
                            <option value="{{ $rank->id }}" {{ $inspector->rank_id == $rank->id ? 'selected' : '' }}>{{ $rank->rank_name }}</option>
                            @endforeach
                        </select>
                    </div>




                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="ib_clearance">IB Clearance <span class="text-danger upload-font">*PDF only (Max. Size 5MB)</span></label>
                        @if($inspector->ib_clearance)
                        <span id="current-file" class="d-block">Current Document: <a href="{{ asset('storage/app/' . $inspector->ib_clearance) }}" target="_blank">View Document</a></span>
                        @else
                        <span id="current-file" class="d-block">No Document Available</span>
                        @endif
                        <input type="file" class="form-control-file" id="ib_clearance" name="ib_clearance" accept="application/pdf">
                    </div>

                   



                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="raw_clearance">RAW Clearance <span class="text-danger upload-font">*PDF only (Max. Size 5MB)</span></label>
                        @if($inspector->raw_clearance)
                        <span id="current-file" class="d-block">Current Document: <a href="{{ asset('storage/app/' . $inspector->raw_clearance) }}" target="_blank">View Document</a></span>
                        @else
                        <span id="current-file" class="d-block">No Document Available</span>
                        @endif
                        <input type="file" class="form-control-file" id="raw_clearance" name="raw_clearance" accept="application/pdf">
                    </div>

                   


                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="mea_clearance">MEA Clearance <span class="text-danger upload-font">*PDF only (Max. Size 5MB)</span></label>
                        @if($inspector->mea_clearance)
                        <span id="current-file" class="d-block">Current Document: <a href="{{ asset('storage/app/' . $inspector->mea_clearance) }}" target="_blank">View Document</a></span>
                        @else
                        <span id="current-file" class="d-block">No Document Available</span>
                        @endif
                        <input type="file" class="form-control-file" id="mea_clearance" name="mea_clearance" accept="application/pdf">
                    </div>


                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="ib_status" id="ib_status_col">IB Status</label>
                        <select name="ib_status_id" class="form-control">
                            <option value="">Select Status</option>
                            @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ $inspector->ib_status_id == $status->id ? 'selected' : '' }}>{{ $status->status_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="raw_status" id="raw_status_col">RAW Status</label>
                        <select name="raw_status_id" class="form-control">
                            <option value="">Select Status</option>
                            @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ $inspector->raw_status_id == $status->id ? 'selected' : '' }}>{{ $status->status_name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="mea_status" id="mea_status_col">MEA Status</label>
                        <select name="mea_status_id" class="form-control">
                            <option value="">Select Status</option>
                            @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ $inspector->mea_status_id == $status->id ? 'selected' : '' }}>{{ $status->status_name }}</option>
                            @endforeach
                        </select>
                    </div>



                </div>

                <div class="row">
                    <div class="col-md-6 form-group inspector-form-group">
                        <label for="qualifications">Qualifications</label>
                        <textarea class="form-control" id="qualifications" name="qualifications" rows="6" placeholder="Enter Qualifications">{{ $inspector->qualifications }}</textarea>
                    </div>

                    <div class="col-md-6 form-group inspector-form-group">
                        <label for="professional_experience">Professional Experience</label>
                        <textarea class="form-control" id="professional_experience" name="professional_experience" rows="6" placeholder="Enter Professional Experience">{{ $inspector->professional_experience }}</textarea>
                    </div>
                </div>

                <hr>
                <h4 class="mb-4">Inspection Details</h4>
                <div class="row">
                    <div class="col-md-12 form-group inspector-form-group border rounded p-3 bg-light">
                        <!-- Routine Inspection Category -->
                        <h5 class="text-left" style="position: absolute; top: -15px; left: 0; background: white; padding: 0 10px;">Routine Inspection Profile</h5>
                        <span type="button" class="  mb-3 position-absolute" style="top: 4px; right: 4px;" \
                            onclick="
                         // Reset radio buttons
                                document.querySelectorAll('input[name=routine_inspection_category]').forEach(radio => radio.checked = false); 
                                
                               
                                
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

                        <div id="routineCategories" class="d-flex mt-3 flex-row justify-content-between">
                            @foreach($inspection_categories as $category)
                            @if($category->is_challenge == 0)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="routine_inspection_category" id="routineCategory{{ $category->id }}" value="{{ $category->id }}"
                                    {{ isset($routineInspection) && $category->id == $routineInspection->category_id ? 'checked' : '' }}>
                                <label class="form-check-label" for="routineCategory{{ $category->id }}">{{ $category->category_name }}</label>
                            </div>
                            @endif
                            @endforeach
                        </div>

                        <div class="row mt-3">
                            <div class="col-4">
                                <label for="routine_joining_date">Communication Date</label>
                                <input type="text" class="form-control datepicker" id="routine_joining_date" name="routine_joining_date" placeholder="dd/ mm/ yyyy"
                                    value="{{ isset($routineInspection) ? \Carbon\Carbon::parse($routineInspection->date_of_joining)->format('Y-m-d') : '' }}">
                            </div>


                            <div class="col-4">
                                <label for="routine_deletion_date">Deletion Date</label>
                                <input type="text" class="form-control datepicker" id="routine_deletion_date" name="routine_deletion_date" placeholder="dd/mm/yyyy"
                                    value="{{ isset($routineInspection) && $routineInspection->deletion_date ? \Carbon\Carbon::parse($routineInspection->deletion_date)->format('Y-m-d') : '' }}">
                            </div>


                            <div class="col-4" id="routine_purpose_of_deletion_wrapper" style="display:none;">
                                <label for="routine_purpose_of_deletion">Purpose of Deletion</label>
                                @if(isset($routineInspection) && $routineInspection->purpose_of_deletion)
                                <select name="routine_purpose_of_deletion" class="form-control" id="routine_purpose_of_deletion">
                                    <option value="">Select Purpose</option>
                                    @foreach($purposeOfDeletions as $purpose)
                                    <option value="{{ $purpose->purpose_name }}"
                                        {{ isset($routineInspection) && $routineInspection->purpose_of_deletion == $purpose->purpose_name ? 'selected' : '' }}>
                                        {{ $purpose->purpose_name }}
                                    </option>
                                    @endforeach
                                    <option value="Other Reason"
                                        {{ isset($routineInspection) && !in_array($routineInspection->purpose_of_deletion, $purposeOfDeletions->pluck('purpose_name')->toArray()) ? 'selected' : '' }}>
                                        Other Reason
                                    </option>
                                </select>

                                <input type="text" id="other_reason" class="form-control"
                                    style="{{ isset($routineInspection) && !in_array($routineInspection->purpose_of_deletion, $purposeOfDeletions->pluck('purpose_name')->toArray()) ? 'display:block;' : 'display:none;' }}"
                                    placeholder="Please specify the purpose"
                                    value="{{ isset($routineInspection) && !in_array($routineInspection->purpose_of_deletion, $purposeOfDeletions->pluck('purpose_name')->toArray()) ? $routineInspection->purpose_of_deletion : '' }}" />
                                @else
                                <select name="routine_purpose_of_deletion" class="form-control" id="routine_purpose_of_deletion">
                                    <option value="">Select Purpose</option>
                                    @foreach($purposeOfDeletions as $purpose)
                                    <option value="{{ $purpose->purpose_name }}">{{ $purpose->purpose_name }}</option>
                                    @endforeach
                                    <option value="Other Reason">Other Reason</option>
                                </select>

                                <input type="text" id="other_reason" class="form-control" style="display: none;" placeholder="Please specify the purpose" />
                                @endif
                            </div>

                          

                            <div class="col-4 mt-4" id="routine_objection_department" style="display: none;">
                                <label for="routine_objection_department">Departments </label>
                                <select name="routine_objection_department" class="form-control">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ (isset($routineInspection) && $department->id == $routineInspection->objection_department_id) ? 'selected' : '' }}>{{ $department->department_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mt-4 form-group inspector-form-group" id="routine_objection_file_section" style="display:none;">
                                <label for="routine_objection_document">Objection Document <span class="text-danger upload-font">*PDF only (Max. Size 5MB)</span></label>
                                @if(isset($routineInspection) && $routineInspection->routine_objection_document)
                                <span id="current-file" class="d-block">Current Objection Document: <a href="{{ asset('storage/app/' . $routineInspection->routine_objection_document) }}" target="_blank">View Document</a></span>
                                @else
                                <span id="current-file" class="d-block">No Objection Document Available</span>
                                @endif
                                <input type="file" class="form-control-file" id="routine_objection_document" name="routine_objection_document" accept="application/pdf">
                            </div>


                            <div class="col-8 mt-4 form-group inspector-form-group text-left">
                                <label for="routine_remarks">Remarks</label>
                                <textarea class="form-control" id="routine_remarks" name="routine_remarks" rows="5">{{ isset($routineInspection) && $routineInspection->remarks ? $routineInspection->remarks : '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 form-group inspector-form-group border rounded p-3 bg-light mt-5">
                        <h5 class="text-left" style="position: absolute; top: -15px; left: 0; background: white; padding: 0 10px;">Challenge Inspection Profile</h5>
                        <span type="button" class=" mb-3 position-absolute" style="top: 4px; right: 4px;" onclick="
                        // Reset radio buttons
                            document.querySelectorAll('input[name=challenge_inspection_category]').forEach(radio => radio.checked = false); 
                            
                            
                            
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
                        <div id="challengeCategories" class="d-flex mt-3 flex-row justify-content-between">
                            @foreach($inspection_categories as $category)
                            @if($category->is_challenge == 1)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="challenge_inspection_category" id="challengeCategory{{ $category->id }}" value="{{ $category->id }}"
                                    {{ isset($challengeInspection) && $category->id == $challengeInspection->category_id ? 'checked' : '' }}>
                                <label class="form-check-label" for="challengeCategory{{ $category->id }}">{{ $category->category_name }}</label>
                            </div>
                            @endif
                            @endforeach
                        </div>

                        <div class="row mt-3">
                            <div class="col-4">
                                <label for="challenge_joining_date">Communication Date</label>
                                <input type="text" class="form-control datepicker" id="challenge_joining_date" name="challenge_joining_date" placeholder="dd/ mm/ yyyy"
                                    value="{{ isset($challengeInspection) ? \Carbon\Carbon::parse($challengeInspection->date_of_joining)->format('Y-m-d') : '' }}">
                            </div>

                            <div class="col-4">
                                <label for="challenge_deletion_date">Deletion Date</label>
                                <input type="text" class="form-control datepicker" id="challenge_deletion_date" name="challenge_deletion_date" placeholder="dd/mm/yyyy"
                                    value="{{ isset($challengeInspection) && $challengeInspection->deletion_date ? \Carbon\Carbon::parse($challengeInspection->deletion_date)->format('Y-m-d') : '' }}">
                            </div>

                            <div class="col-4" id="challenge_purpose_of_deletion_wrapper" style="display:none;">
                                <label for="challenge_purpose_of_deletion">Purpose of Deletion</label>
                                @if(isset($challengeInspection) && $challengeInspection->purpose_of_deletion)
                                <select name="challenge_purpose_of_deletion" class="form-control" id="challenge_purpose_of_deletion">
                                    <option value="">Select Purpose</option>
                                    @foreach($purposeOfDeletions as $purpose)
                                    <option value="{{ $purpose->purpose_name }}"
                                        {{ isset($challengeInspection) && $challengeInspection->purpose_of_deletion == $purpose->purpose_name ? 'selected' : '' }}>
                                        {{ $purpose->purpose_name }}
                                    </option>
                                    @endforeach
                                    <option value="Other Reason"
                                        {{ isset($challengeInspection) && !in_array($challengeInspection->purpose_of_deletion, $purposeOfDeletions->pluck('purpose_name')->toArray()) ? 'selected' : '' }}>
                                        Other Reason
                                    </option>
                                </select>

                                <input type="text" id="challenge_other_reason" class="form-control"
                                    style="{{ isset($challengeInspection) && !in_array($challengeInspection->purpose_of_deletion, $purposeOfDeletions->pluck('purpose_name')->toArray()) ? 'display:block;' : 'display:none;' }}"
                                    placeholder="Please specify the purpose"
                                    value="{{ isset($challengeInspection) && !in_array($challengeInspection->purpose_of_deletion, $purposeOfDeletions->pluck('purpose_name')->toArray()) ? $challengeInspection->purpose_of_deletion : '' }}" />
                                @else
                                <select name="challenge_purpose_of_deletion" class="form-control" id="challenge_purpose_of_deletion">
                                    <option value="">Select Purpose</option>
                                    @foreach($purposeOfDeletions as $purpose)
                                    <option value="{{ $purpose->purpose_name }}">{{ $purpose->purpose_name }}</option>
                                    @endforeach
                                    <option value="Other Reason">Other Reason</option>
                                </select>

                                <input type="text" id="challenge_other_reason" class="form-control" style="display: none;" placeholder="Please specify the purpose" />
                                @endif
                            </div>

                           
                            <div class="col-4 mt-4" id="challenge_objection_department" style="display: none;">
                                <label for="challenge_objection_department">Departments </label>
                                <select name="challenge_objection_department" class="form-control">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ (isset($challengeInspection) && $department->id == $challengeInspection->objection_department_id) ? 'selected' : '' }}>{{ $department->department_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mt-4 form-group inspector-form-group" id="challenge_objection_file_section" style="display:none;">
                                <label for="challenge_objection_document">Objection Document <span class="text-danger upload-font">*PDF only (Max. Size 5MB)</span></label>
                                @if(isset($challengeInspection) && $challengeInspection->challenge_objection_document)
                                <span id="current-file" class="d-block">Current Objection Document: <a href="{{ asset('storage/app/' . $challengeInspection->challenge_objection_document) }}" target="_blank">View Document</a></span>
                                @else
                                <span id="current-file" class="d-block">No Objection Document Available</span>
                                @endif
                                <input type="file" class="form-control-file" id="challenge_objection_document" name="challenge_objection_document" accept="application/pdf">
                            </div>


                            <div class="col-8 mt-4 form-group inspector-form-group">
                                <label for="challenge_remarks">Remarks</label>
                                <textarea class="form-control" id="challenge_remarks" name="challenge_remarks" rows="5">{{ isset($challengeInspection) && $challengeInspection->remarks ? trim($challengeInspection->remarks) : '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3 justify-content-between">

                    <div class="col-md-4 form-group inspector-form-group ">
                        <label for="captcha">Enter Captcha</label>
                        <div style="position: relative;">
                            <img id="editInspectorCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                            <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha('editInspectorCaptchaImage')"></i>

                        </div>
                        <input type="text" name="captcha" class="form-control mt-3" minlength="6" maxlength="6" placeholder="Enter Captcha" required>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-primary">Update Inspector</button>
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

    .border {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }


    .inspector-form-group .form-control,
    .inspector-form-group .form-control-file {
        border-radius: 0.25rem;
    }

    @media (max-width: 576px) {
        .inspector-form-group {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>
@endpush

@push('script')
<script>
    

    $('select[name="routine_objection_department"]').on('change', function() {
        var objectionValue = $(this).val();
        if (objectionValue) {
            $('#routine_objection_file_section').show();
        } else {
            $('#routine_objection_file_section').hide();
            $('#routine_objection_document').val(''); 
        }
    });

    $('#routine_deletion_date').on('change', function() {
        var deletionDate = $(this).val();
   
        if (deletionDate) {
            $('#routine_purpose_of_deletion_wrapper').show();
           
        } else {
            $('#routine_purpose_of_deletion_wrapper').hide();
            $('#routine_purpose_of_deletion').val('');
           
        }
    });





   

    $('select[name="challenge_objection_department"]').on('change', function() {
        var objectionValue = $(this).val();
        if (objectionValue) {
            $('#challenge_objection_file_section').show();
        } else {
            $('#challenge_objection_file_section').hide();
            $('#challenge_objection_document').val(''); 
        }
    });

    $('#challenge_deletion_date').on('change', function() {
        var deletionDate = $(this).val();
     
        if (deletionDate) {
            $('#challenge_purpose_of_deletion_wrapper').show();
           
        } else {
            $('#challenge_purpose_of_deletion_wrapper').hide();
            $('#challenge_purpose_of_deletion').val('');
            
        }
    });



    




    $(document).ready(function() {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        var routineInspection = @json($routineInspection ?? null);
        var challengeInspection = @json($challengeInspection ?? null);

        var deletionDate = $('#routine_deletion_date').val();
        if (deletionDate) {
            $('#routine_purpose_of_deletion_wrapper').show();
            
        }



        if ($('#routine_purpose_of_deletion').val() == 'Other Reason') {
            $('#other_reason').show();
        }
        $('#routine_purpose_of_deletion').on('change', function() {
            if ($(this).val() == 'Other Reason') {
                $('#other_reason').show();
            } else {
                $('#other_reason').hide();
            }
        });

       

       

        var selectedRoutineDepartment = $('select[name="routine_objection_department"]').val();
        if (selectedRoutineDepartment) {
            $('#routine_objection_file_section').show();
        } else {
            $('#routine_objection_file_section').hide();
            $('#routine_objection_file_section').val('');
        }


    


        var challengedeletionDate = $('#challenge_deletion_date').val();
        if (challengedeletionDate) {
            $('#challenge_purpose_of_deletion_wrapper').show();
           
        }



        if ($('#challenge_purpose_of_deletion').val() == 'Other Reason') {
            $('#challenge_other_reason').show();
        }
        $('#challenge_purpose_of_deletion').on('change', function() {
            if ($(this).val() == 'Other Reason') {
                $('#challenge_other_reason').show();
            } else {
                $('#challenge_other_reason').hide();
            }
        });

        

        var selectedChallengeDepartment = $('select[name="challenge_objection_department"]').val();
        if (selectedChallengeDepartment) {
            $('#challenge_objection_file_section').show();
        } else {
            $('#challenge_objection_file_section').hide();
            $('#challenge_objection_file_section').val('');
        }





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

            // Date of Birth Validation
            var dob = $('#dob').val();
            var dobDate = new Date(dob);
            var today = new Date();
            var age = today.getFullYear() - dobDate.getFullYear();
            var monthDiff = today.getMonth() - dobDate.getMonth();

            if (dob === '' || age < 18 || (age === 18 && monthDiff < 0) || (age === 18 && monthDiff === 0 && today.getDate() < dobDate.getDate())) {
                isValid = false;
                FancyAlerts.show({
                    msg: 'Date of Birth must be at least 18 years old.',
                    type: 'error'
                });
            }

            // Passport Number Validation
            var passportNumber = $('#passport_number').val();
            var passportRegex = /^[A-Z0-9]+$/;
            if (!passportRegex.test(passportNumber)) {
                isValid = false;
                FancyAlerts.show({
                    msg: 'Passport Number must contain only uppercase letters and digits.',
                    type: 'error'
                });
            }

            // UNLP Number Validation
            var unlpNumber = $('#unlp_number').val();
            if (unlpNumber && !passportRegex.test(unlpNumber)) {
                isValid = false;
                FancyAlerts.show({
                    msg: 'UNLP Number must contain only uppercase letters and digits.',
                    type: 'error'
                });
            }

            // CAPTCHA Validation
            var captchaInput = $('input[name="captcha"]').val();
            if (captchaInput.length < 6) {
                isValid = false;
                FancyAlerts.show({
                    msg: 'CAPTCHA must be at least 6 characters long.',
                    type: 'error'
                });
            }

            // Clearance Certificate Validation
            var clearanceCertificate = $('#clearance_certificate').val();
            if (clearanceCertificate) {
                var fileExtension = clearanceCertificate.split('.').pop().toLowerCase();
                if (fileExtension !== 'pdf') {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Clearance Certificate must be a PDF file.',
                        type: 'error'
                    });
                }
            }


            // Joining Dates and Status Validation
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


            return isValid;
        }

        $('#updateInspectorForm').submit(function(e) {
            e.preventDefault(); // Prevent default form submission

            if (validateForm()) {
                var formData = new FormData(this);

                var routinePurpose = $('#routine_purpose_of_deletion').val().trim().replace(/[\r\n]+/g, ' ');
                var challengePurpose = $('#challenge_purpose_of_deletion').val().trim().replace(/[\r\n]+/g, ' ');
                var otherReasonValue = $('#other_reason').val().trim().replace(/[\r\n]+/g, ' ');
                var otherChallengeReasonValue = $('#challenge_other_reason').val().trim().replace(/[\r\n]+/g, ' ');

                // Remove any previously appended field to ensure it's updated
                formData.delete('routine_purpose_of_deletion');

                if (routinePurpose === "Other Reason") {
                    if (otherReasonValue) {
                        formData.append('routine_purpose_of_deletion', otherReasonValue);
                    } else {
                        formData.append('routine_purpose_of_deletion', '');
                    }
                } else if (routinePurpose) {
                    formData.append('routine_purpose_of_deletion', routinePurpose);
                }


                formData.delete('challenge_purpose_of_deletion');

                if (challengePurpose === "Other Reason") {
                    if (otherChallengeReasonValue) {
                        formData.append('challenge_purpose_of_deletion', otherChallengeReasonValue);
                    } else {
                        formData.append('challenge_purpose_of_deletion', '');
                    }
                } else if (challengePurpose) {
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


                var inspectorId = $('#editInspectorId').val();

                $.ajax({
                    url: "{{ url('update-inspector') }}/" + inspectorId,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            FancyAlerts.show({
                                msg: response.msg || 'Inspector updated successfully!',
                                type: 'success',
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else if (response.msg) {
                            // Handle non-validation error messages
                            FancyAlerts.show({
                                msg: 'Error: ' + (typeof response.msg === 'string' ? response.msg : JSON.stringify(response.msg)),
                                type: 'error',
                            });
                        } else {
                            FancyAlerts.show({
                                msg: 'Error: An unknown error occurred.',
                                type: 'error',
                            });
                        }
                    },
                }).fail(function(xhr) {
                    try {
                        var response = JSON.parse(xhr.responseText);

                        // Check if 'msg' contains validation errors
                        if (response.msg && typeof response.msg === 'object') {
                            // Extract and format all validation error messages
                            let errorMessages = [];
                            for (let field in response.msg) {
                                if (Array.isArray(response.msg[field])) {
                                    errorMessages.push(...response.msg[field]); // Add all error messages for the field
                                }
                            }

                            FancyAlerts.show({
                                msg: 'Validation Errors:' + errorMessages.join('<br>'),
                                type: 'error',
                            });
                        } 
                        // Handle other types of errors
                        else if (response.msg && typeof response.msg === 'string') {
                            FancyAlerts.show({
                                msg: 'Error: ' + response.msg,
                                type: 'error',
                            });
                        } else {
                            FancyAlerts.show({
                                msg: 'Error: An unknown error occurred.',
                                type: 'error',
                            });
                        }
                    } catch (e) {
                        FancyAlerts.show({
                            msg: 'Error: Unable to process the error response.',
                            type: 'error',
                        });
                    }
                });
            }
        });
    });
</script>
@endpush