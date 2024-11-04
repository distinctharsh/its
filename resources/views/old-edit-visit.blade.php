@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <h3 class="mb-4">Edit Visit</h3>
    <div class="text-left mb-3">
        <button type="button" class="btn back-btn" onclick="window.location='{{ route('manageVisit') }}'">Back</button>
    </div>
    <form id="updateVisitForm" action="{{ route('updateVisit', $visit->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="editVisitId" name="id" value="{{ $visit->id }}">

        <div class="card card-outline-secondary inspector-form">
            <div class="card-body">
                <div class="row">
                <div class="col-md-4 form-group inspector-form-group">
                    <label for="edit_list_of_escort_officers">List of Escort Officers</label>
                    @php
                        $listOfEscortOfficers = is_string($visit->list_of_escort_officers) ? json_decode($visit->list_of_escort_officers, true) : $visit->list_of_escort_officers;
                    @endphp

                    <select class="form-control" id="edit_list_of_escort_officers" name="escort_officers[]" multiple required>
                        <option disabled>Select Officer</option>
                        @foreach($escort_officers as $escort_officer)
                            <option value="{{ $escort_officer->id }}" {{ in_array($escort_officer->id, $listOfEscortOfficers) ? 'selected' : '' }}>
                                {{ $escort_officer->officer_name }}
                            </option>
                        @endforeach
                    </select>

                    @if ($errors->has('escort_officers'))
                        <span class="text-danger">{{ $errors->first('escort_officers') }}</span>
                    @endif
                </div>

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="edit_category">Type of Inspection </label>
                        <select class="form-control" id="edit_type_of_inspection" name="inspection_type" required>
                            @foreach($inspection_types as $inspection_type)
                            <option value="{{ $inspection_type->id }}" {{ $visit->inspection_type_id == $inspection_type->id ? 'selected' : '' }}>{{ $inspection_type->type_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="edit_category">Visit Category</label>
                        <select class="form-control" id="edit_category" name="category_id" required>
                            @foreach($visit_categories as $category)
                            @if($category->id == $visit->category_id)
                            <option value="{{ $category->id }}" selected>
                                {{ $category->category_name }}
                            </option>
                            @else
                            <option value="{{ $category->id }}">
                                {{ $category->category_name }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="edit_team_lead">Team Lead</label>
                        <select class="form-control" id="edit_team_lead" name="team_lead" required>
                            <option value="">Select Team Lead</option>
                            @foreach($inspectors as $inspector)
                           
                            <option value="{{ $inspector->id }}" {{ $visit->team_lead_id == $inspector->id ? 'selected' : '' }}>
                                {{ $inspector->name }}
                            </option>
                           
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="edit_list_of_inspectors">List of Inspectors</label>
                        @php
                        $listOfInspectors = is_string($visit->list_of_inspectors) ? json_decode($visit->list_of_inspectors, true) : $visit->list_of_inspectors;
                        @endphp

                        <select class="form-control" id="edit_list_of_inspectors" name="list_of_inspectors[]" multiple required>
                            @foreach($inspectors as $inspector)
                            @if ($inspector->is_active || in_array($inspector->id, $listOfInspectors))
                            <option value="{{ $inspector->id }}" {{ in_array($inspector->id, $listOfInspectors) ? 'selected' : '' }}>
                                {{ $inspector->name }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="edit_arrival_datetime">Date Time of Arrival</label>
                        <input type="datetime-local" class="form-control" id="edit_arrival_datetime" name="arrival_datetime" value="{{ $visit->arrival_datetime }}" required>
                    </div>
                </div>

                <div class="row">


                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="edit_departure_datetime">Date Time of Departure</label>
                        <input type="datetime-local" class="form-control" id="edit_departure_datetime" name="departure_datetime" value="{{ $visit->departure_datetime }}" required>
                    </div>


                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="inspection_type_selection">Select Inspection Category</label>
                        <select class="form-control" id="inspection_type_selection" name="inspection_type_selection" required>
                            <option value="">Select Category</option>
                            <option value="routine" {{ $visit->inspection_type_selection == 'routine' ? 'selected' : '' }}>Routine</option>
                            <option value="challenge" {{ $visit->inspection_type_selection == 'challenge' ? 'selected' : '' }}>Challenge</option>
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


                <div class="row ">

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="visit_report">Visit Report <span class="text-danger">*Only PDF format allowed (Max. Size 5MB)</span></label>
                        @if($visit->visit_report)
                        <span id="current-file" class="d-block">Current Report: <a href="{{ asset('storage/app/' . $visit->visit_report) }}" target="_blank">View Report</a></span>
                        @else
                        <span id="current-file" class="d-block">No Report Available</span>
                        @endif
                        <input type="file" class="form-control-file" id="visit_report" name="visit_report" accept="application/pdf">
                        <span id="selected-file" class="d-block mt-2"></span>
                    </div>

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="clearance_certificate">Upload Document <span class="text-danger">(Max. Size 50mb)</span></label>
                        @if($visit->clearance_certificate)
                        <span id="current-file" class="d-block">Current Document: <a href="{{ asset('storage/app/' . $visit->clearance_certificate) }}" target="_blank">View Document</a></span>
                        @else
                        <span id="current-file" class="d-block">No Document Available</span>
                        @endif
                        <input type="file" class="form-control-file" id="clearance_certificate" name="clearance_certificate">
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-md-8 form-group inspector-form-group">
                        <label for="edit_remarks">Remarks</label>
                        <textarea class="form-control" id="edit_remarks" name="remarks" rows="3">{{ $visit->remarks }}</textarea>
                    </div>

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="captcha">Enter Captcha</label>
                        <div style="position: relative;">
                            <img id="editVisitCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                            <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha('editVisitCaptchaImage')"></i>

                        </div>
                        <input type="text" name="captcha" class="form-control" minlength="6" maxlength="6" placeholder="Enter Captcha" required>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-primary">Update Visit</button>
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

    var currentCategoryTypeId = {{$visit->inspection_category_type_id}};
    var currentInspectionType = '{{ $visit->inspection_type_selection }}'; // Get the current inspection type

    var visitSiteMapping = @json($visit_site_mapping);

    // Run this function on page load to initialize the form
    handleInspectionTypeChange();

    function handleInspectionTypeChange() {
        var selectedValue = $('#inspection_type_selection').val();
        $('#category_type_id').empty().append('<option value="">Select Sub Category Type</option>');
        $('#inspectionFields').addClass('d-none'); // Hide fields initially

        if (selectedValue === 'routine' || selectedValue === 'challenge') {
            $('#categoryTypeContainer').removeAttr('disabled');

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

            // Select the current category type if it exists in the dropdown
            if (currentCategoryTypeId) {
                $('#category_type_id').val(currentCategoryTypeId);
                handleSiteCodeChange(); // Call to populate site codes
            }

            // Show inspection fields if a valid type is selected
            $('#inspectionFields').removeClass('d-none');

        } else {
            $('#categoryTypeContainer').attr('disabled', true);
        }
    }

    function handleSiteCodeChange() {
        var selectedTypeName = $('#category_type_id option:selected').text(); // Get the selected category type name
        $('#site_code_container').empty();
        $('#site_of_inspection_container').empty();
        $('#state_container').empty();

        if (selectedTypeName === 'Single Inspection') {
        $('#site_code_container').append(`
            <select class="form-control" name="site_code_id[]" required>
                <option value="">Select Site Code</option>
                @foreach($site_codes as $site_code)
                <option value="{{ $site_code->id }}"
                    @if(isset($visit_site_mapping[0]) && $visit_site_mapping[0]->site_code_id == $site_code->id) selected @endif>
                    {{ $site_code->site_code }}
                </option>
                @endforeach
            </select>
        `);
        $('#site_of_inspection_container').append(`
            <input type="text" class="form-control" name="site_of_inspection[]" placeholder="Site Name and Address" required
                value="@if(isset($visit_site_mapping[0])){{ $visit_site_mapping[0]->site_of_inspection }}@endif">
        `);
        $('#state_container').append(`
            <select class="form-control" name="state_id[]" required>
                <option value="">Select State</option>
                @foreach($states as $state)
                <option value="{{ $state->id }}"
                    @if(isset($visit_site_mapping[0]) && $visit_site_mapping[0]->state_id == $state->id) selected @endif>
                    {{ $state->state_name }}
                </option>
                @endforeach
            </select>
        `);
        } else if (selectedTypeName === 'Sequential Inspection') {
            let iterationCount = 0;

            // Calculate the iteration count for Sequential Inspection
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
                        <option value="{{ $site_code->id }}">
                            {{ $site_code->site_code }}
                        </option>
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

            // Populate fields based on visit_site_mapping data
            visitSiteMapping.forEach(function(mapping, index) {
                if (index < iterationCount) {
                    $('select[name="site_code_id[]"]').eq(index).val(mapping.site_code_id); // Set site code
                    $('input[name="site_of_inspection[]"]').eq(index).val(mapping.site_of_inspection); // Set site name
                    $('select[name="state_id[]"]').eq(index).val(mapping.state_id); // Set state
                }
            });
        }
    }



    // Event listeners
    $('#inspection_type_selection').change(function() {
        handleInspectionTypeChange();
    });

    $('#category_type_id').change(function() {
        handleSiteCodeChange();
    });

    handleSiteCodeChange();

    function validateForm() {
        var isValid = true;

        // Visit Report Validation
        var visitReport = $('#visit_report').val();
        if (visitReport) {
            var fileExtension = visitReport.split('.').pop().toLowerCase();
            if (fileExtension !== 'pdf') {
                isValid = false;
                FancyAlerts.show({
                    msg: 'Visit Report must be a PDF file.',
                    type: 'error'
                });
            }
        }
        return isValid;
    }

    $('#updateVisitForm').submit(function(e) {
        e.preventDefault();
        if (validateForm()) {
            var formData = new FormData(this);

            var visitId = $('#editVisitId').val();
            $.ajax({
                url: "{{ url('update-visit') }}/" + visitId,
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
                            msg: response.msg || 'Visit updated successfully!',
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