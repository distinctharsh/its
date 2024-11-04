@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <h3 class="mb-4">Edit Inspector</h3>
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
                        <label for="nationality">Country</label>

                        <select class="form-control" id="nationality" name="nationality" required>
                            <option value="">Select Country</option>
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
                        <label for="rank">Designation</label>
                        <select class="form-control" id="rank" name="rank" required>
                            <option value="">Select Designation</option>
                            @foreach($ranks as $rank)
                            <option value="{{ $rank->id }}" {{ $inspector->rank_id == $rank->id ? 'selected' : '' }}>{{ $rank->rank_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="clearance_certificate">Clearance Certificate <span class="text-danger upload-font" >*PDF only (Max. Size 5MB)</span></label>
                        <input type="file" class="form-control-file" id="clearance_certificate" name="clearance_certificate">
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
                        <div id="routineCategories" class="d-flex flex-row justify-content-between">
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


                        <!-- Routine Category Types -->
                        <!-- <div id="routineCategoryTypes" class="d-flex flex-column mt-3">
                            <label>Inspection Category Types</label>
                            <p>Please Select category to show types</p>
                            <br>
                            @if(isset($routineInspection))
                            @foreach($inspection_categories as $category)
                            @if($category->id == $routineInspection->category_id)
                            @foreach($category->types as $type)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="routine_category_type_id" id="routineType{{ $type->id }}" value="{{ $type->id }}"
                                    {{ $type->id == $routineInspection->category_type_id ? 'checked' : '' }}>
                                <label class="form-check-label" for="routineType{{ $type->id }}">{{ $type->type_name }}</label>
                            </div>
                            @endforeach
                            @endif
                            @endforeach
                            @endif
                        </div> -->

                        <div class="row mt-3">
                            <div class="col-6">
                                <label for="routine_joining_date">Joining Date</label>
                                <input type="date" class="form-control" id="routine_joining_date" name="routine_joining_date"
                                    value="{{ isset($routineInspection) ? \Carbon\Carbon::parse($routineInspection->date_of_joining)->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-6">
                                <label for="routine_status">Status</label>
                                <select name="routine_status_id" class="form-control">
                                    <option value="">Select Status</option>
                                    @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ (isset($routineInspection) && $status->id == $routineInspection->status_id) ? 'selected' : '' }}>
                                        {{ $status->status_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                    </div>


                    <div class="col-md-12 form-group inspector-form-group border rounded p-3 bg-light mt-5">
                        <!-- Challenge Inspection Category -->
                        <h5 class="text-left" style="position: absolute; top: -15px; left: 0; background: white; padding: 0 10px;">Challenge Inspection Profile</h5>
                        <div id="challengeCategories" class="d-flex flex-row justify-content-between">
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

                        <!-- <div id="challengeCategoryTypes" class="d-flex flex-column mt-3">
                            <label>Inspection Category Types</label>
                            <p>Please Select category to show types</p>
                            <br>
                            @if(isset($challengeInspection))
                            @foreach($inspection_categories as $category)
                            @if($category->id == $challengeInspection->category_id)
                            @foreach($category->types as $type)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="challenge_category_type_id" id="challengeType{{ $type->id }}" value="{{ $type->id }}"
                                    {{ $type->id == $challengeInspection->category_type_id ? 'checked' : '' }}>
                                <label class="form-check-label" for="challengeType{{ $type->id }}">{{ $type->type_name }}</label>
                            </div>
                            @endforeach
                            @endif
                            @endforeach
                            @endif
                        </div> -->

                        <div class="row mt-3">
                            <div class="col-6">
                                <label for="challenge_joining_date">Joining Date</label>
                                <input type="date" class="form-control" id="challenge_joining_date" name="challenge_joining_date"
                                    value="{{ isset($challengeInspection) ? \Carbon\Carbon::parse($challengeInspection->date_of_joining)->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-6">
                                <label for="challenge_status">Status</label>
                                <select name="challenge_status_id" class="form-control">
                                    <option value="">Select Status</option>
                                    @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ (isset($challengeInspection) && $status->id == $challengeInspection->status_id) ? 'selected' : '' }}>
                                        {{ $status->status_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="row mb-3 justify-content-between">
                    <div class="col-md-8 form-group inspector-form-group">
                        <label for="remarks">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="5">{{ $inspector->remarks }}</textarea>
                    </div>
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
    $(document).ready(function() {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        var routineInspection = @json($routineInspection ?? null);
        var challengeInspection = @json($challengeInspection ?? null);

        //         function updateCategoryTypes(isChallenge) {
        //     var categoryId = isChallenge 
        //         ? $('input[name="challenge_inspection_category"]:checked').val() 
        //         : $('input[name="routine_inspection_category"]:checked').val();

        //     var typeContainer = isChallenge ? $('#challengeCategoryTypes') : $('#routineCategoryTypes');
        //     typeContainer.empty(); // Clear previous types

        //     if (categoryId) {
        //         var selectedCategory = @json($inspection_categories);
        //         selectedCategory.forEach(function(category) {
        //             if (category.id == categoryId) {
        //                 category.types.forEach(function(type) {
        //                     typeContainer.append(
        //                         `<div class="form-check">
        //                             <input class="form-check-input" type="radio" name="${isChallenge ? 'challenge_category_type_id' : 'routine_category_type_id'}" id="${isChallenge ? 'challengeType' : 'routineType'}${type.id}" value="${type.id}" ${isChallenge && challengeInspection && type.id == challengeInspection.category_type_id ? 'checked' : (type.id == routineInspection.category_type_id ? 'checked' : '')}>
        //                             <label class="form-check-label" for="${isChallenge ? 'challengeType' : 'routineType'}${type.id}">${type.type_name}</label>
        //                         </div>`
        //                     );
        //                 });
        //             }
        //         });
        //     }
        // }



        // function updateCategoryTypes(isChallenge) {
        //     var categoryId = isChallenge ?
        //         $('input[name="challenge_inspection_category"]:checked').val() :
        //         $('input[name="routine_inspection_category"]:checked').val();

        //     var typeContainer = isChallenge ? $('#challengeCategoryTypes') : $('#routineCategoryTypes');
        //     typeContainer.empty(); // Clear previous types

        //     if (categoryId) {
        //         var selectedCategory = @json($inspection_categories);
        //         selectedCategory.forEach(function(category) {
        //             if (category.id == categoryId) {
        //                 category.types.forEach(function(type) {
        //                     var isChecked = isChallenge ?
        //                         challengeInspection && type.id == challengeInspection.category_type_id :
        //                         routineInspection && type.id == routineInspection.category_type_id;

        //                     // Append the radio button for types
        //                     typeContainer.append(
        //                         `<div class="form-check">
        //                     <input class="form-check-input" type="radio" name="${isChallenge ? 'challenge_category_type_id' : 'routine_category_type_id'}" id="${isChallenge ? 'challengeType' : 'routineType'}${type.id}" value="${type.id}" ${isChecked ? 'checked' : ''}>
        //                     <label class="form-check-label" for="${isChallenge ? 'challengeType' : 'routineType'}${type.id}">${type.type_name}</label>
        //                 </div>`
        //                     );
        //                 });
        //             }
        //         });
        //     } else {
        //         // If no category selected, show only the types once
        //         var allCategories = @json($inspection_categories);
        //         allCategories.forEach(function(category) {
        //             category.types.forEach(function(type) {
        //                 if (!typeContainer.find(`#${isChallenge ? 'challengeType' : 'routineType'}${type.id}`).length) {
        //                     typeContainer.append(
        //                         `<div class="form-check">
        //                     <input class="form-check-input" type="radio" name="${isChallenge ? 'challenge_category_type_id' : 'routine_category_type_id'}" id="${isChallenge ? 'challengeType' : 'routineType'}${type.id}" value="${type.id}">
        //                     <label class="form-check-label" for="${isChallenge ? 'challengeType' : 'routineType'}${type.id}">${type.type_name}</label>
        //                 </div>`
        //                     );
        //                 }
        //             });
        //         });
        //     }
        // }




        // Event listeners for category changes
        // $('input[name="routine_inspection_category"]').on('change', function() {
        //     updateCategoryTypes(false);
        //     console.log("Routine category changed");
        // });

        // $('input[name="challenge_inspection_category"]').on('change', function() {
        //     updateCategoryTypes(true);
        //     console.log("Challenge category changed");
        // });

        // function initializeForm() {
        //     updateCategoryTypes(false);
        //     updateCategoryTypes(true);
        // }

        // initializeForm(); 

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


            return isValid;
        }

        $('#updateInspectorForm').submit(function(e) {
            e.preventDefault(); // Prevent default form submission

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

                var inspectorId = $('#editInspectorId').val();

                $.ajax({
                    url: "{{ url('update-inspector') }}/" + inspectorId,
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
                                msg: response.msg || 'Inspector updated successfully!',
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
                        var message = response.msg ? response.msg : 'An unknown error occurred';
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