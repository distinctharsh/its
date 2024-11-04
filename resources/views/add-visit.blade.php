@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <h3 class="mb-4">Add New Visit</h3>
    <div class="text-left mb-3">
        <button type="button" class="btn back-btn" onclick="window.location='{{ route('manageVisit') }}'">Back</button>
    </div>
    <form id="addVisitForm" enctype="multipart/form-data">
        <div class="card card-outline-secondary inspector-form">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="escort_officers">Escort Officers</label>
                        <select class="form-control" id="escort_officers" name="escort_officers[]" multiple required>
                            @foreach($escort_officers as $officer)
                            <option value="{{ $officer->id }}">{{ $officer->officer_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="type_of_inspection">Type of Inspection</label>
                        <select class="form-control" id="type_of_inspection" name="inspection_type" required>
                            <option value="">Select Inspection Type</option>
                            @foreach($inspection_types as $inspection_type)
                            <option value="{{ $inspection_type->id }}">{{ $inspection_type->type_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-group visit-form-group">
                        <label for="category_id">Visit Category</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($visit_categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="team_lead">Team Lead</label>

                        <select class="form-control" id="team_lead" name="team_lead" required>
                            <option value="">Select Team Lead</option>
                            @foreach($inspectors as $inspector)
                            <option value="{{ $inspector->id }}"
                                {{ $inspector->deleted_at == null ? '' : 'disabled' }}
                                style="{{ $inspector->deleted_at == null ? '' : 'color: #ccc;' }}">
                                {{ $inspector->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="list_of_inspectors">List of Inspectors</label>

                        <select class="form-control" id="list_of_inspectors" name="list_of_inspectors[]" multiple required>
                            @foreach($inspectors as $inspector)
                            <option value="{{ $inspector->id }}">{{ $inspector->name }}</option>
                            @endforeach
                        </select>

                    </div>




                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="arrival_datetime">Date Time of Arrival</label>
                        <input type="datetime-local" class="form-control" id="arrival_datetime" name="arrival_datetime" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="departure_datetime">Date Time of Departure</label>
                        <input type="datetime-local" class="form-control" id="departure_datetime" name="departure_datetime" required>
                    </div>


                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="inspection_type_selection">Inspection Category</label>
                        <select class="form-control" id="inspection_type_selection" name="inspection_type_selection" required>
                            <option value="">Select Category</option>
                            <option value="routine">Routine</option>
                            <option value="challenge">Challenge</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4" id="categoryTypeContainer" disabled>
                        <label for="category_type_id">Sub Category Type</label>
                        <select class="form-control" id="category_type_id" name="category_type_id">
                            <option value="">Select Sub Category Type</option>
                            <!-- Options will be populated here -->
                        </select>
                    </div>


                </div>

                <div class="row d-none" id="inspectionFields">
                    <div class="col-md-4 form-group inspector-form-group" id="site_code_container">
                        
                        <!-- Site code fields will be appended here -->
                    </div>

                    <div class="col-md-4 form-group inspector-form-group" id="site_of_inspection_container">
                        <!-- Site of Inspection fields will be appended here -->
                    </div>

                    <div class="col-md-4 form-group inspector-form-group" id="state_container">
                        <!-- State fields will be appended here -->
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="visit_report">Upload Visit Report <span class="text-danger upload-font" >*PDF only (Max. Size 10MB)</span></label>
                        <input type="file" class="form-control-file" id="visit_report" name="visit_report" accept="application/pdf">
                    </div>

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="clearance_certificate">Upload Document <span class="text-danger upload-font" >*PDF only (Max. Size 50MB)</span></label>
                        <input type="file" class="form-control-file" id="clearance_certificate" name="clearance_certificate" accept="application/pdf">
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-md-8 form-group inspector-form-group">
                        <label for="remarks">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                    </div>
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="captcha">Enter Captcha</label>
                        <div style="position: relative;">
                            <img id="addVisitCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                            <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha('addVisitCaptchaImage')"></i>

                        </div>
                        <input type="text" name="captcha" class="form-control" minlength="6" maxlength="6" required>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-danger">Reset</button>
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
        var inspectionCategories = @json($inspection_categories);

        $('#site_code_container').on('change', 'select[name="site_code_id[]"]', function() {
            var selectedOption = $(this).find('option:selected');
            var siteAddress = selectedOption.data('address');
            var siteName = selectedOption.data('name');
            var stateId = selectedOption.data('state-id'); // Get the state ID

            var inputIndex = $(this).index(); // Get the index of the current site code dropdown

            // Update the corresponding site of inspection input
            $('#site_of_inspection_container input[name="site_of_inspection[]"]').eq(inputIndex).val(siteName + ', ' + siteAddress);

            // Set the corresponding state dropdown
            $('#state_container select[name="state_id[]"]').eq(inputIndex).val(stateId); // Set the state dropdown based on the selected site code
        });



        $('#inspection_type_selection').change(function() {
            var selectedValue = $(this).val();
            $('#category_type_id').empty().append('<option value="">Select Sub Category Type</option>');
            $('#inspectionFields').addClass('d-none'); // Hide fields initially

            if (selectedValue === 'routine' || selectedValue === 'challenge') {
                $('#categoryTypeContainer').removeAttr('disabled');

                if (selectedValue === 'challenge') {
                    // Automatically select 'Single Inspection' and disable the dropdown
                    $('#category_type_id').append('<option value="single_inspection" selected>Single Inspection</option>');
                    $('#category_type_id').prop('disabled', true);
                    
                    // Set the hidden input for payload
                    $('#category_type_id').val('single_inspection'); // Set the value in the form data

                    // Show fields related to Single Inspection
                    showInspectionFields('Single Inspection');
                } else {
                    $('#category_type_id').prop('disabled', false);
                }
                // Use an object to track unique types
                let uniqueTypes = {};

                inspectionCategories.forEach(function(category) {
                    var isChallenge = category.is_challenge === 1;

                    // Check if the category matches the selected sub-category
                    if ((selectedValue === 'routine' && !isChallenge) ||
                        (selectedValue === 'challenge' && isChallenge)) {
                        category.types.forEach(function(type) {
                            if (!uniqueTypes[type.id]) {
                                uniqueTypes[type.id] = type.type_name;
                            }
                        });
                    }
                });

                // Append unique types to the dropdown
                for (let id in uniqueTypes) {
                    $('#category_type_id').append('<option value="' + id + '">' + uniqueTypes[id] + '</option>');
                }
            } else {
                $('#categoryTypeContainer').attr('disabled', true);
            }
        });


        function showInspectionFields(selectedTypeName) {
        $('#inspectionFields').removeClass('d-none');
        $('#site_code_container').empty();
        $('#site_of_inspection_container').empty();
        $('#state_container').empty();

        if (selectedTypeName === 'Single Inspection') {
            // Show single set of fields
            $('#site_code_container').append(`
                <select class="form-control" name="site_code_id[]" required>
                    <option value="">Select Site Code</option>
                    @foreach($site_codes as $site_code)
                    <option value="{{ $site_code->id }}" data-name="{{ $site_code->site_name }}" data-address="{{ $site_code->site_address }}" data-state-id="{{ $site_code->state_id }}">{{ $site_code->site_code }}</option>
                    @endforeach
                </select>
            `);
            $('#site_of_inspection_container').append(`
                <input type="text" class="form-control" name="site_of_inspection[]" placeholder="Site Name and Address" required>
            `);
            $('#state_container').append(`
                <select class="form-control" name="state_id[]" required>
                    <option value="">Select State</option>
                    @foreach($states as $state)
                    <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                    @endforeach
                </select>
            `);
            $('#site_of_inspection, #state').parent().show(); // Show required fields
        }
        // Additional handling for other types can go here...
    }

        $('#category_type_id').change(function() {
            var selectedTypeId = $(this).val();
            var selectedTypeName = $(this).find('option:selected').text();

            $('#inspectionFields').removeClass('d-none');
            $('#site_code_container').empty();
            $('#site_of_inspection_container').empty();
            $('#state_container').empty();

            if (selectedTypeName === 'Single Inspection') {
                // Show single set of fields
                $('#site_code_container').append(`
            <select class="form-control" name="site_code_id[]" required>
                <option value="">Select Site Code</option>
                @foreach($site_codes as $site_code)
                <option value="{{ $site_code->id }}" data-name="{{ $site_code->site_name }}" data-address="{{ $site_code->site_address }}"  data-state-id="{{ $site_code->state_id }}">{{ $site_code->site_code }}</option>
                @endforeach
            </select>
        `);
                $('#site_of_inspection_container').append(`
            <input type="text" class="form-control" name="site_of_inspection[]" placeholder="Site Name and Address" required>
        `);
                $('#state_container').append(`
            <select class="form-control" name="state_id[]" required>
                <option value="">Select State</option>
                @foreach($states as $state)
                <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                @endforeach
            </select>
        `);
                $('#site_of_inspection, #state').parent().show(); // Show required fields

            } else if (selectedTypeName === 'Sequential Inspection') {
                // Find the iteration count
                let iterationCount = 0;

                inspectionCategories.forEach(function(category) {
                    var isChallenge = category.is_challenge === 1;

                    if (!isChallenge) { // Only consider routine categories
                        category.types.forEach(function(type) {
                            if (type.type_name === 'Sequential Inspection') {
                                iterationCount = type.iteration; // Get the iteration count
                            }
                        });
                    }
                });

                // Create and append site code fields based on iteration count
                for (let i = 0; i < iterationCount; i++) {
                    $('#site_code_container').append(`
                <select class="form-control" name="site_code_id[]" required>
                    <option value="">Select Site Code</option>
                    @foreach($site_codes as $site_code)
                    <option value="{{ $site_code->id }}" data-name="{{ $site_code->site_name }}" data-address="{{ $site_code->site_address }}"  data-state-id="{{ $site_code->state_id }}">{{ $site_code->site_code }}</option>
                    @endforeach
                </select>
            `);

                    $('#site_of_inspection_container').append(`
                <input type="text" class="form-control" name="site_of_inspection[]" placeholder="Site Name and Address" required>
            `);

                    $('#state_container').append(`
                <select class="form-control" name="state_id[]" required>
                    <option value="">Select State</option>
                    @foreach($states as $state)
                    <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                    @endforeach
                </select>
            `);
                }

                $('#site_of_inspection, #state').parent().show(); // Show required fields
            }
        });




        function validateForm() {
            var isValid = true;
            // Clearance Certificate Validation
            var clearanceCertificate = $('#clearance_certificate')[0].files[0];
            if (clearanceCertificate) {

                var fileType = clearanceCertificate.type;

                var fileSize = clearanceCertificate.size;

                if (fileType !== 'application/pdf') {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Clearance Certificate must be a PDF file.',
                        type: 'error'
                    });
                } else if (fileSize > 50 * 1024 * 1024) {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Clearance Certificate must be less than 50MB.',
                        type: 'error'
                    });
                }
            }

            // Visit Report
            var visitReport = $('#visit_report')[0].files[0];
            if (visitReport) {

                var fileType = visitReport.type;

                var fileSize = visitReport.size;

                if (fileType !== 'application/pdf') {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Visit Report must be a PDF file.',
                        type: 'error'
                    });
                } else if (fileSize > 5 * 1024 * 1024) {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'Visit Report must be less than 5MB.',
                        type: 'error'
                    });
                }
            }

            return isValid;
        }

        $('#addVisitForm').submit(function(e) {
            e.preventDefault();

            if (validateForm()) {
                var formData = new FormData(this);


                if ($('#inspection_type_selection').val() === 'challenge') {
        formData.append('category_type_id', '1'); // Assuming '1' is the ID for 'Single Inspection'
    }

                $.ajax({
                    url: "{{ route('createVisit') }}",
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
                                msg: response.msg || 'Visit added successfully!',
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