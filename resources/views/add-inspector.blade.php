@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <h3 class="mb-4">Add New Inspector</h3>
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
                        <label for="nationality">Country</label>
                        <select class="form-control" id="nationality" name="nationality" required>
                            <option value="">Select Country</option>
                            @foreach($nationalities as $nationality)
                            @if($nationality->is_active)
                            <option value="{{ $nationality->id }}">{{ $nationality->country_name }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="place_of_birth">Place of Birth</label>
                        <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" placeholder="Enter Place of Birth" required>
                    </div>
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="passport_number">Passport Number</label>
                        <input type="text" class="form-control" id="passport_number" name="passport_number" placeholder="Enter Passport Number (e.g., A12345678)" required>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="unlp_number">UNLP Number</label>
                        <input type="text" class="form-control" id="unlp_number" name="unlp_number" placeholder="Enter UNLP Number">
                    </div>
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="rank">Designation</label>
                        <select class="form-control" id="rank" name="rank">
                            <option value="">Select Designation</option>
                            @foreach($ranks as $rank)
                            <option value="{{ $rank->id }}">{{ $rank->rank_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="clearance_certificate">Clearance Certificate <span class="text-danger upload-font" >*PDF only (Max. Size 5MB)</span></label>
                        <input type="file" class="form-control-file" id="clearance_certificate" name="clearance_certificate" accept="application/pdf">
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
                <!-- <h4 class="mb-4">Inspection Details</h4> -->

                <div class="row">
                    <div class="col-md-12 form-group inspector-form-group border rounded p-3 bg-light mt-3">
                    <h5 class="text-left" style="position: absolute; top: -15px; left: 0; background: white; padding: 0 10px;">Routine Inspection Profile</h5> 
                        <div id="routineCategories" class="d-flex flex-row justify-content-between mt-3">
                            @foreach($inspection_categories as $category)
                            @if($category->is_challenge == 0)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="routine_inspection_category" id="routineCategory{{ $category->id }}" value="{{ $category->id }}">
                                <label class="form-check-label" for="routineCategory{{ $category->id }}">{{ $category->category_name }}</label>
                            </div>
                            @endif
                            @endforeach
                        </div>

                        <!-- <div id="routineCategoryTypes" class="d-flex flex-column mt-3">
                            <label>Inspection Category Types</label>
                            <p>Please Select category to show types</p>
                            <br>
                            
                        </div> -->

                        <div class="row mt-3">
                            <div class="col-6">
                                <label for="routine_joining_date">Joining Date</label>
                                <input type="date" class="form-control" id="routine_joining_date" name="routine_joining_date">
                            </div>
                            <div class="col-6">
                                <label for="routine_status">Status</label>
                                <select name="routine_status_id" class="form-control">
                                    <option value="">Select Status</option>
                                    @foreach($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                    </div>

                    <div class="col-md-12 form-group inspector-form-group border rounded p-3 bg-light mt-3">
                  
                    <h5 class="text-left" style="position: absolute; top: -15px; left: 0; background: white; padding: 0 10px;">Challenge Inspection Profile</h5>
                        <div id="challengeCategories" class="d-flex flex-row justify-content-between mt-3">
                            @foreach($inspection_categories as $category)
                            @if($category->is_challenge == 1)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="challenge_inspection_category" id="challengeCategory{{ $category->id }}" value="{{ $category->id }}">
                                <label class="form-check-label" for="challengeCategory{{ $category->id }}">{{ $category->category_name }}</label>
                            </div>
                            @endif
                            @endforeach
                        </div>

                        <!-- <div id="challengeCategoryTypes" class="d-flex flex-column mt-3">
                            <label>Inspection Category Types</label>
                          
                            <p>Please Select category to show types</p>
                            <br>
                        </div> -->

                        <div class="row mt-3">
                            <div class="col-6">
                                <label for="challenge_joining_date">Joining Date</label>
                                <input type="date" class="form-control" id="challenge_joining_date" name="challenge_joining_date">
                            </div>
                            <div class="col-6">
                                <label for="challenge_status">Status</label>
                                <select name="challenge_status_id" class="form-control">
                                    <option value="">Select Status</option>
                                    @foreach($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>



                    </div>
                </div>

                <!-- <div class="col-md-12 form-group inspection-form-group"> -->
                <div class="row mb-3 justify-content-between">

                    <div class="col-md-8 form-group inspector-form-group">
                        <label for="remarks">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="5"></textarea>
                    </div>

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="captcha">Enter Captcha</label>
                        <div style="position: relative;">
                            <img id="addInspectorCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                            <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha('addInspectorCaptchaImage')"></i>

                        </div>
                        <input type="text" name="captcha" class="form-control mt-3" minlength="6" maxlength="6" placeholder="Enter Captcha" required>
                    </div>
                </div>

                <!-- </div> -->
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

        // function updateCategoryTypes(isChallenge) {
        //     var categoryId = isChallenge ? $('input[name="challenge_inspection_category"]:checked').val() :
        //         $('input[name="routine_inspection_category"]:checked').val();
        //     var typeContainer = isChallenge ? $('#challengeCategoryTypes') : $('#routineCategoryTypes');
        //     typeContainer.empty(); // Clear previous types

        //     // Ensure label is always shown
        //     var typeLabel = isChallenge ? $('#challengeCategoryTypes label') : $('#routineCategoryTypes label');
        //     typeLabel.show(); // Ensure the label is visible

        //     if (categoryId) {
        //         var selectedCategory = @json($inspection_categories);
        //         selectedCategory.forEach(function(category) {
        //             if (category.id == categoryId) {
        //                 category.types.forEach(function(type) {
        //                     typeContainer.append(`
        //             <div class="form-check">
        //                 <input class="form-check-input" type="radio" name="${isChallenge ? 'challenge_category_type_id' : 'routine_category_type_id'}" id="${isChallenge ? 'challengeType' : 'routineType'}${type.id}" value="${type.id}">
        //                 <label class="form-check-label" for="${isChallenge ? 'challengeType' : 'routineType'}${type.id}">${type.type_name}</label>
        //             </div>
        //         `);
        //                 });
        //             }
        //         });
        //     }
        // }




        // $('input[name="routine_inspection_category"]').change(function() {
        //     updateCategoryTypes(false);
        // });

        // $('input[name="challenge_inspection_category"]').change(function() {
        //     updateCategoryTypes(true);
        // });



        function validateForm() {
            var isValid = true;
            // Check if at least one category is filled
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
                // Joining Date Validation
                var routineJoiningDate = $('#routine_joining_date').val();
                if (!routineJoiningDate) {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Joining Date for Routine is required.',
                        type: 'error'
                    });
                }

                // Status Validation
                var routineStatus = $('select[name="routine_status_id"]').val();
                if (!routineStatus) {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Status for Routine is required.',
                        type: 'error'
                    });
                }

            }

            // Date of Birth Validation
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



            // Joining Dates and Status Validation
            if (routineFilled) {
                var routineJoiningDate = $('#routine_joining_date').val();
                var routineStatus = $('select[name="routine_status_id"]').val();
                if (!routineJoiningDate) {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Joining Date for Routine is required.',
                        type: 'error'
                    });
                }
                if (!routineStatus) {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Status for Routine is required.',
                        type: 'error'
                    });
                }
            }

            if (challengeFilled) {
                var challengeJoiningDate = $('#challenge_joining_date').val();
                var challengeStatus = $('select[name="challenge_status_id"]').val();
                if (!challengeJoiningDate) {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Joining Date for Challenge is required.',
                        type: 'error'
                    });
                }
                if (!challengeStatus) {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Status for Challenge is required.',
                        type: 'error'
                    });
                }
            }


            // Clearance Certificate Validation
            var clearanceCertificate = $('#clearance_certificate')[0].files[0];
            if (clearanceCertificate) {
                // Check file type
                var fileType = clearanceCertificate.type;
                // Check file size (5MB = 5 * 1024 * 1024 bytes)
                var fileSize = clearanceCertificate.size;

                if (fileType !== 'application/pdf') {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Clearance Certificate must be a PDF file.',
                        type: 'error'
                    });
                } else if (fileSize > 5 * 1024 * 1024) {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Clearance Certificate must be less than 5MB.',
                        type: 'error'
                    });
                }
            }

            return isValid;
        }

        $('#addInspectorForm').submit(function(e) {
            e.preventDefault();

            if (validateForm()) {
                var formData = new FormData(this);

                function appendCategoryData(formData, isChallenge) {
                    var category = isChallenge ?
                        $('input[name="challenge_inspection_category"]:checked').val() :
                        $('input[name="routine_inspection_category"]:checked').val();
                    // var type = isChallenge ?
                    //     $('input[name="challenge_category_type_id"]:checked').val() :
                    //     $('input[name="routine_category_type_id"]:checked').val();
                    var joiningDate = isChallenge ? $('#challenge_joining_date').val() : $('#routine_joining_date').val();
                    var status = isChallenge ? $('select[name="challenge_status_id"]').val() : $('select[name="routine_status_id"]').val();

                    if (category) formData.append(isChallenge ? 'challenge_category_id' : 'routine_category_id', category);
                    // if (type) formData.append(isChallenge ? 'challenge_category_type_id' : 'routine_category_type_id', type);
                    if (joiningDate) formData.append(isChallenge ? 'challenge_date_of_joining' : 'date_of_joining', joiningDate);
                    if (status) formData.append(isChallenge ? 'challenge_status_id' : 'routine_status_id', status);
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
        });
    });
</script>
@endpush