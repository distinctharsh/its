@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
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
                        <label for="inspection_type_selection">Select Inspection Type</label>
                        <select class="form-control" id="inspection_type_selection" name="inspection_type_selection" required>
                            <option value="">Select Inspection Type</option>
                            @foreach($inspection_properties as $properties)
                            <option value="{{ $properties->id }}" {{ $visit->inspection_property_id == $properties->id ? 'selected' : '' }}>{{ $properties->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4" id="categoryTypeContainer" disabled>
                        <label for="category_type_id">Sub Category Type</label>
                        <select class="form-control" id="category_type_id" name="category_type_id">
                            <option value="">Select Sub Category Type</option>
                        </select>
                    </div>
                </div>

                <div class="row d-none" id="inspectionFields">
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
                            // Ensure $listOfInspectors is always an array.
                            $listOfInspectors = is_string($visit->list_of_inspectors) ? json_decode($visit->list_of_inspectors, true) : $visit->list_of_inspectors;
                            
                            // If it's not an array, default it to an empty array.
                            if (!is_array($listOfInspectors)) {
                                $listOfInspectors = [];
                            }
                        @endphp
                        <select class="form-control" id="edit_list_of_inspectors" name="list_of_inspectors[]" multiple required>
                            @foreach($inspectors as $inspector)
                            <option value="{{ $inspector->id }}" {{ in_array($inspector->id, $listOfInspectors) ? 'selected' : '' }}>
                                {{ $inspector->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="edit_list_of_escort_officers">Escort Officers (Inspection Site)</label>
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
                </div>
        
                <div class="row">

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="escort_officers_poe">Escort Officers (POE)</label>
                        @php
                            $listOfEscortOfficers = is_string($visit->escort_officers_poe) 
                                ? json_decode($visit->escort_officers_poe, true) 
                                : ($visit->escort_officers_poe ?? []);
                        @endphp

                        <select class="form-control" id="escort_officers_poe" name="escort_officers_poe[]" multiple required>
                            <option disabled>Select Officer</option>
                            @foreach($escort_officers as $escort_officer)
                                <option value="{{ $escort_officer->id }}" 
                                        {{ in_array($escort_officer->id, $listOfEscortOfficers) ? 'selected' : '' }}>
                                    {{ $escort_officer->officer_name }}
                                </option>
                            @endforeach
                        </select>

                        @if ($errors->has('escort_officers'))
                            <span class="text-danger">{{ $errors->first('escort_officers') }}</span>
                        @endif
                    </div>

                    <div class="col-md-4 form-group inspector-form-group" id="point_of_entry_container">
                        <label for="edit_point_of_entry">Point of Entry</label>
                        <select class="custom-select" name="point_of_entry[]" multiple required id="edit_point_of_entry">
                            <option value="" disabled>Select Point of Entry</option>
                            @foreach($point_address as $point)
                                @php
                                    $pointOfEntryArray = json_decode($visit->point_of_entry, true) ?? [];
                                @endphp
                                <option value="{{ $point->id }}" 
                                    @if(in_array($point->id, $pointOfEntryArray)) selected @endif>
                                    {{ $point->point_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-4 form-group inspector-form-group" id="point_of_exit_container">
                        <label for="edit_point_of_exit">Point of Exit</label>
                        <select class="custom-select" name="point_of_exit[]" multiple required id="edit_point_of_exit">
                            <option value="" disabled>Select Point of Exit</option>
                            @foreach($point_address as $point)
                                @php
                                    $pointOfExitArray = json_decode($visit->point_of_exit, true) ?? [];
                                @endphp
                                <option value="{{ $point->id }}" 
                                    @if(in_array($point->id, $pointOfExitArray)) selected @endif>
                                    {{ $point->point_name }}
                                </option>
                            @endforeach
                        </select>

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="edit_arrival_datetime">Date Time of Arrival</label>
                        <input type="datetime-local" class="form-control" id="edit_arrival_datetime" name="arrival_datetime" value="{{ $visit->arrival_datetime }}" required>
                    </div>
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="edit_departure_datetime">Date Time of Departure</label>
                        <input type="datetime-local" class="form-control" id="edit_departure_datetime" name="departure_datetime" value="{{ $visit->departure_datetime }}" required>
                    </div>

                    <div class="col-md-2 form-group inspector-form-group">
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

                    <div class="col-md-2 form-group inspector-form-group mt-4">
                        <div class="form-check">
                            <input 
                                type="checkbox" 
                                id="is_closed" 
                                name="is_closed" 
                                value="1" 
                                class="form-check-input" 
                                {{ $visit->is_closed ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_closed">Closed</label>
                        </div>
                    </div>
                </div>

             
                <div class="row">
                    <div class="col-md-4 form-group inspector-form-group">
                        <label>Document Number</label>
                        <select class="form-control" id="opcw_document_id" name="opcw_document_id" required>
                            <option value="" >Select Document Number</option>
                            @foreach($opcw_document_numbers as $id => $fax_number)
                            <option value="{{ $id }}" value="{{ $id }}" {{ $visit->opcw_document_id == $id ? 'selected' : '' }}
                            >{{ $fax_number }}</option>
                            @endforeach
                            <option value="not_list">Not in the list</option>
                        </select>
                    </div>

                    <div class="col-md-4 form-group inspector-form-group" id="fax_document_container" style="display: none;">
                        <label for="fax_document">Upload Document<span class="text-danger upload-font">*PDF only (Max. Size 10MB)</span></label>
                        <div id="fax_document_link" style="display: none;">
                            <label>Fax Document Link:</label>
                        </div>
                        <input type="file" class="form-control-file" id="fax_document" name="fax_document" accept="application/pdf">
                    </div>

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="acentric_report">Action Taken Report</label>
                        <textarea class="form-control" id="acentric_report" name="acentric_report" rows="3" > {{ $visit->acentric_report }}</textarea>
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
        </div>
        <div class="card-footer text-center">
            <button type="submit" class="btn btn-primary">Update Inspection</button>
            <button type="reset" class="btn btn-danger">Reset</button>
        </div>
    </form>
</div>

<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true" style="top: 140px;">
    <div class="modal-dialog modal-lg" role="document">
        <form id="addFaxForm" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">OPCW Notification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title"> OPCW</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 form-group inspection-form-group">
                                    <label for="fax_date">Communication Date</label>
                                    <input type="date" class="form-control" id="fax_date" name="fax_date" required>
                                </div>

                                <div class="col-md-6 form-group inspection-form-group">
                                    <label for="fax_number">Document Number</label>
                                    <input type="text" class="form-control" id="fax_number" name="fax_number" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group inspection-form-group">
                                    <label for="reference_number">Reference Number</label>
                                    <input type="text" class="form-control" id="reference_number" name="reference_number" required>
                                </div>

                                <div class="col-md-4 form-group inspector-form-group">
                                    <label for="fax_document">Upload Document <span class="text-danger upload-font" >*PDF only (Max. Size 5MB)</span></label>
                                    <input type="file" class="form-control-file" id="fax_document" name="fax_document" accept="application/pdf">
                                </div>
                            </div>
                            

                            <div class="row justify-content-between">
                                <div class="col-md-8 form-group inspection-form-group">
                                    <label for="remarks">Remarks</label>
                                    <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                                </div>

                                <div class="col-md-4 form-group inspection-form-group">
                                    <label for="captcha">Enter Captcha</label>
                                    <div style="position: relative;">
                                        <img id="addOpcwCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                                        <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha('addOpcwCaptchaImage')"></i>
                                    </div>
                                    <input type="text" name="captcha" class="form-control" minlength="6" maxlength="6" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="closeModal">Close</button>
                </div>
            </div>
        </form>
    </div>
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

<script src="{{ asset('assets/vendor/datatables/select2.min.js') }}"></script>
<script>

    $('#detailsModal').on('hidden.bs.modal', function() {
        resetModal();
    });

    $(document).ready(function() {
        function resetModal() {
            $('#addFaxForm')[0].reset();
            $('#fax_document_container').show();  
            $('#fax_document').attr('required', true);  
            $('#fax_document_link').hide();  
        }

        $('#opcw_document_id').select2({
            placeholder: "Select Document Number", // Placeholder text
            allowClear: true // Allows clearing the selection
        });
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var inspectionCategories = @json($inspection_categories);
        var currentCategoryTypeId = {{ $visit->inspection_category_type_id }};
        var currentInspectionType = '{{ $visit->inspection_type_selection }}'; 
        var inspectionCategoriesType = @json($inspection_categories_type);
        var visitSiteMapping = @json($visit_site_mapping);
        var siteCodes = @json($site_codes);
        var states = @json($states);
        var inspectionTypes = @json($inspection_types);
        var currentInspectionTypeId = {{ $visit->type_of_inspection_id }};
        var selectedCategoryTypeId = currentCategoryTypeId;
        var allInspectionCategories = @json($all_inspection_category);  
        var inspection_issues = @json($inspection_issues);  
        
        $('#inspectionFields').on('change', 'select[name="site_code_id[]"]', function() {
            var selectedOption = $(this).find('option:selected');
            var siteAddress = selectedOption.data('address');
            var siteName = selectedOption.data('name');
            var stateId = selectedOption.data('state-id'); 
            var inputIndex = $(this).index('select[name="site_code_id[]"]');  
            var selectedValue = selectedOption[0].value;
            var selectedSiteCode = siteCodes.find(function(mapping) {
                return mapping.id == selectedValue; 
            });

            if (selectedSiteCode) {
                var siteName = selectedSiteCode.site_name; 
                var siteAddress = selectedSiteCode.site_address; 
                var stateId = selectedSiteCode.state_id; 
            }

            $('#site_of_inspection_container input[name="site_of_inspection[]"]').eq(inputIndex).val(siteName + ', ' + siteAddress);
            $('#state_container select[name="state_id[]"]').eq(inputIndex).val(stateId);
        });

        function handleInspectionTypeChange() {
            var selectedValue = $('#inspection_type_selection').val();
            $('#inspectionFields').addClass('d-none'); 
            $('#category_type_id').empty().append('<option value="">Select Sub Category Type</option>');
            $('#inspectionFields').addClass('d-none'); 

            if (selectedValue === '1' || selectedValue === '2') {
                $('#categoryTypeContainer').removeAttr('disabled');
                let uniqueTypes = {};
                inspectionCategories.forEach(function(category) {
                    var isChallenge = category.is_challenge === 1;
                    if ((selectedValue === '1' && !isChallenge) || (selectedValue === '2' && isChallenge)) {
                        category.types.forEach(function(type) {
                            if (!uniqueTypes[type.id]) {
                                uniqueTypes[type.id] = type.type_name;
                            }
                        });
                    }
                });

                for (let id in uniqueTypes) {
                    $('#category_type_id').append('<option value="' + id + '">' + uniqueTypes[id] + '</option>');
                }

                if (currentCategoryTypeId) {
                    $('#category_type_id').val(currentCategoryTypeId);
                    handleSiteCodeChange();
                }

                $('#inspectionFields').removeClass('d-none');
            } else {
                $('#categoryTypeContainer').attr('disabled', true);
            }
        }

        handleInspectionTypeChange();

        $('#inspection_type_selection').change(function() {
            handleInspectionTypeChange();
        });

        function handleSiteCodeChange() {
            var selectedCategoryTypeId = $('#category_type_id option:selected').val();
            var selectedTypeName = $('#category_type_id option:selected').text();

            $('#inspectionFields').empty().removeClass('d-none');
            $('#site_code_container, #site_of_inspection_container, #state_container, #inspection_category_container, #inspection_phase_container, #phase_option_container, #preliminary_report_container, #final_inspection_report_container, #issue_document_container',  '#inspection_issue_container').empty();

            function generateSiteCodeSelect(selectedSiteCodeId) {
                let options = '@foreach($site_codes as $site_code)' +
                                `<option value="{{ $site_code->id }}"` +
                                ` @if(isset($visit_site_mapping[0]) && $visit_site_mapping[0]->site_code_id == $site_code->id) selected @endif>` +
                                `{{ $site_code->site_code }}</option>` +
                            '@endforeach';
                return `
                    <div class="col-md-3 form-group inspector-form-group" id="site_code_container">
                        <label for="site_code_id">Plant Site</label>
                        <select class="form-control" name="site_code_id[]" required>
                            <option value="">Select Plant Site</option>
                            ${options}
                        </select>
                    </div>`;
            }

            function generateSiteOfInspectionInput(value) {
                return `
                    <div class="col-md-3 form-group inspector-form-group" id="site_of_inspection_container">
                        <label for="site_of_inspection">Site Name and Address</label>
                        <input type="text" class="form-control" name="site_of_inspection[]" placeholder="Site Name and Address" required
                            value="${value || ''}">
                    </div>`;
            }

            function generateStateSelect(selectedStateId) {
                let options = '@foreach($states as $state)' +
                                `<option value="{{ $state->id }}"` +
                                ` @if(isset($visit_site_mapping[0]) && $visit_site_mapping[0]->state_id == $state->id) selected @endif>` +
                                `{{ $state->state_name }}</option>` +
                            '@endforeach';
                return `
                    <div class="col-md-3 form-group inspector-form-group" id="state_container">
                        <label for="state_id">State</label>
                        <select class="form-control" name="state_id[]" required>
                            <option value="">Select State</option>
                            ${options}
                        </select>
                    </div>`;
            }

            function generateInspectionCategorySelect(filterIds = []) {
                let selectHTML = `
                    <div class="col-md-3 form-group inspector-form-group" id="inspection_category_container">
                        <label for="inspection_category_id">Inspection Category</label>
                        <select class="form-control" name="inspection_category_id[]" required>
                            <option value="">Select Inspection Category</option>
                `;

                allInspectionCategories.forEach(function(category) {
                    // If filterIds is provided, only add options that match the filter
                    if (filterIds.length === 0 || filterIds.includes(category.id)) {
                        selectHTML += `
                            <option value="${category.id}">${category.type_name}</option>
                        `;
                    }
                });

                // Close the select tag
                selectHTML += `
                        </select>
                    </div>
                `;
                
                return selectHTML;
            }

            function generateInspectionIssueSelect(filterIds = []) {
                let selectHTML = `
                    <div class="col-md-3 form-group inspector-form-group" id="inspection_issue_container">
                        <label for="inspection_issue_id">Inspection Issue</label>
                        <select class="form-control" name="inspection_issue_id[]" >
                            <option value="">Select Inspection Issue</option>
                `;

                inspection_issues.forEach(function(issue) {
                    // If filterIds is provided, only add options that match the filter
                    if (filterIds.length === 0 || filterIds.includes(issue.id)) {
                        selectHTML += `
                            <option value="${issue.id}">${issue.name}</option>
                        `;
                    }
                });

                // Close the select tag
                selectHTML += `
                        </select>
                    </div>
                `;
                
                return selectHTML;
            }



            function generateInspectionPhaseSelect() {
                let options = '@foreach($inspection_phases as $inspection_phase)' +
                                `<option value="{{ $inspection_phase->id }}" data-name="{{ $inspection_phase->phase_type_name }}">{{ $inspection_phase->phase_type_name }}</option>` +
                            '@endforeach';
                return `
                    <div class="col-md-3 mb-4 form-group inspector-form-group" id="inspection_phase_container">
                        <label for="inspection_phase_id">Inspection Phase</label>
                        <select class="form-control" name="inspection_phase_id[]" required>
                            <option value="">Select Inspection Phase</option>
                            ${options}
                        </select>
                    </div>`;
            }

            function generatePhaseOptionSelect() {
                let options = '@foreach($phase_options as $phase_option)' +
                                `<option value="{{ $phase_option->id }}" data-name="{{ addslashes($phase_option->option_name) }}">{{ htmlspecialchars($phase_option->option_name) }}</option>` +
                            '@endforeach';
                return `
                    <div class="col-md-3 mb-4 form-group inspector-form-group" id="phase_option_container" style="display: none;">
                        <label for="phase_option_id">Phase Option</label>
                        <select class="form-control" name="phase_option_id[]">
                            <option value="" disabled selected>Select Inspection Phase Option</option>
                            ${options}
                        </select>
                    </div>`;
            }

            function generatePreliminaryReportInput(index) {
                return `
                    <div class="col-md-3 mb-4 form-group inspector-form-group" id="preliminary_report_container_${index}">
                        <label for="preliminary_report">Preliminary Report</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="preliminary_report_${index}" name="preliminary_report[]" accept="application/pdf"
                                onchange="this.nextElementSibling.textContent = this.files.length > 0 ? this.files[0].name : 'Choose Preliminary Report';">
                            <label class="custom-file-label" for="preliminary_report_${index}">Choose Preliminary Report</label>
                        </div>
                        <div id="view_document_link_container_${index}"></div>
                    </div>
                `;
            }

            function generateFinalInspectionReportInput(index) {
                return `
                    <div class="col-md-3 mb-4 form-group inspector-form-group" id="final_inspection_report_container_${index}">
                        <label for="final_inspection_report">Final Inspection Report</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="final_inspection_report_${index}" name="final_inspection_report[]" accept="application/pdf"
                                onchange="this.nextElementSibling.textContent = this.files.length > 0 ? this.files[0].name : 'Choose Final Inspection Report';">
                            <label class="custom-file-label" for="final_inspection_report_${index}">Choose Final Inspection Report</label>
                        </div>
                        <div id="view_final_link_container_${index}"></div>
                    </div>

                    
                `;
            }

            function generateInspectionIssueDocument(index) {
                return `
                     <div class="col-md-3 mb-4 form-group inspector-form-group" id="issue_document_container_${index}">
                        <label for="issue_document">Issue Document</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="issue_document_${index}" name="issue_document[]" accept="application/pdf"
                                onchange="this.nextElementSibling.textContent = this.files.length > 0 ? this.files[0].name : 'Choose Issue Document';">
                            <label class="custom-file-label" for="issue_document_${index}">Choose Issue Document</label>
                        </div>
                        <div id="view_issue_document_container_${index}"></div>
                    </div>

                 
                `;
            }

            function blankField(index) {
                return `
                     <div class="col-md-3 mb-4 form-group inspector-form-group"></div>
                `;
            }



            if (selectedCategoryTypeId == '2') {
                $('#inspectionFields').append(`
                    ${generateInspectionCategorySelect()}
                    ${generateInspectionPhaseSelect()}
                    ${generateSiteCodeSelect()}
                    ${generateSiteOfInspectionInput('@if(isset($visit_site_mapping[0])){{ $visit_site_mapping[0]->site_of_inspection }}@endif')}
                    ${generateStateSelect('@if(isset($visit_site_mapping[0])){{ $visit_site_mapping[0]->state_id }}@endif')}
                    ${generatePhaseOptionSelect()}
                    ${generatePreliminaryReportInput(0)}
                    ${generateFinalInspectionReportInput(0)}
                    ${generateInspectionIssueSelect()}
                    ${generateInspectionIssueDocument(0)}
                `);

                $(document).on('change', 'select[name="inspection_category_id[]"], select[name="inspection_phase_id[]"]', function() {
                    var inspectionCategoryId = $('select[name="inspection_category_id[]"]').val();
                    var inspectionIssueId = $('select[name="inspection_issue_id[]"]').val();
                    var inspectionPhaseId = $('select[name="inspection_phase_id[]"]').val();

                    if (inspectionCategoryId == 2 && inspectionPhaseId == 2) {
                        $('#phase_option_container').show();
                    } else {
                        $('#phase_option_container').hide();
                    }
                });

                let iterationCount = 0;
                let foundCategory = inspectionCategoriesType.find(category => category.id === 1);
                iterationCount = foundCategory ? foundCategory.iteration : 2;

                visitSiteMapping.forEach(function(mapping, index) {
                    if (index < iterationCount) {
                        $('select[name="site_code_id[]"]').eq(index).val(mapping.site_code_id);
                        $('input[name="site_of_inspection[]"]').eq(index).val(mapping.site_of_inspection);
                        $('select[name="state_id[]"]').eq(index).val(mapping.state_id);
                        $('select[name="inspection_category_id[]"]').eq(index).val(mapping.inspection_category_id);
                        $('select[name="inspection_issue_id[]"]').eq(index).val(mapping.inspection_issue_id);
                        $('select[name="inspection_phase_id[]"]').eq(index).val(mapping.inspection_phase_id);
                        $('select[name="phase_option_id[]"]').eq(index).val(mapping.phase_option_id);

                         // For Preliminary Report
                        let preliminaryReportPath = "{{ asset('storage/app/') }}" + "/" + mapping.preliminary_report;
                        if (mapping.preliminary_report) {
                            $(`#view_document_link_container_${index}`).html(`<a href="${preliminaryReportPath}" target="_blank">View Preliminary Report</a>`);
                        }

                        // For Final Inspection Report
                        let finalInspectionReportPath = "{{ asset('storage/app/') }}" + "/" + mapping.final_inspection_report;
                        if (mapping.final_inspection_report) {
                            $(`#view_final_link_container_${index}`).html(`<a href="${finalInspectionReportPath}" target="_blank">View Final Inspection Report</a>`);
                        }

                        let issueDocumentPath = "{{ asset('storage/app/') }}" + "/" + mapping.issue_document;
                        if (mapping.issue_document) {
                            $(`#view_issue_document_container_${index}`).html(`<a href="${issueDocumentPath}" target="_blank">View Issue Document</a>`);
                        }

                        if (mapping.inspection_category_id == 2 && mapping.inspection_phase_id == 2) {
                            $('#phase_option_container').show();
                        } else {
                            $('#phase_option_container').hide();
                        }
                    }
                });

            } else if (selectedCategoryTypeId === '1') {
                $('#phase_option_container').hide();
                $('select[name="phase_option_id[]"]').prop('disabled', true);

                let iterationCount = 0;
                let foundCategory = inspectionCategoriesType.find(category => category.id === 1);
                iterationCount = foundCategory ? foundCategory.iteration : 2;

                for (let i = 0; i < iterationCount; i++) {
                    $('#inspectionFields').append(`
                        ${generateInspectionCategorySelect([3, 4])}
                        ${generateInspectionPhaseSelect()}
                        ${generateSiteCodeSelect()}
                        ${generateSiteOfInspectionInput()}
                        ${generateStateSelect()}
                        ${generatePreliminaryReportInput(i)}
                        ${generateFinalInspectionReportInput(i)}
                        ${generateInspectionIssueSelect()}
                        ${generateInspectionIssueDocument(i)}
                        ${blankField(i)}
                        ${blankField(i)}
                        ${blankField(i)}
                    `);
                }

                visitSiteMapping.forEach(function(mapping, index) {
                    if (index < iterationCount) {
                        $('select[name="site_code_id[]"]').eq(index).val(mapping.site_code_id);
                        $('input[name="site_of_inspection[]"]').eq(index).val(mapping.site_of_inspection);
                        $('select[name="state_id[]"]').eq(index).val(mapping.state_id);
                        $('select[name="inspection_category_id[]"]').eq(index).val(mapping.inspection_category_id);
                        $('select[name="inspection_issue_id[]"]').eq(index).val(mapping.inspection_issue_id);
                        $('select[name="inspection_phase_id[]"]').eq(index).val(mapping.inspection_phase_id);

                        let preliminaryReportPath = "{{ asset('storage/app/') }}" + "/" + mapping.preliminary_report;
                        if (mapping.preliminary_report) {
                            $(`#view_document_link_container_${index}`).html(`<a href="${preliminaryReportPath}" target="_blank">View Preliminary Report</a>`);
                        }

                        let finalInspectionReportPath = "{{ asset('storage/app/') }}" + "/" + mapping.final_inspection_report;
                        if (mapping.final_inspection_report) {
                            $(`#view_final_link_container_${index}`).html(`<a href="${finalInspectionReportPath}" target="_blank">View Final Inspection Report</a>`);
                        }

                        let issueDocumentPath = "{{ asset('storage/app/') }}" + "/" + mapping.issue_document;
                        if (mapping.issue_document) {
                            $(`#view_issue_document_container_${index}`).html(`<a href="${issueDocumentPath}" target="_blank">View Final Inspection Report</a>`);
                        }

                    }
                });
            }
        }

        $(document).on('change', 'select[name="inspection_category_id[]"], select[name="inspection_phase_id[]"]', function() {
            var inspectionCategoryId = $('select[name="inspection_category_id[]"]').val();
            var inspectionPhaseId = $('select[name="inspection_phase_id[]"]').val();
            if (inspectionCategoryId == 2 && inspectionPhaseId == 2) {
                $('#phase_option_container').show();
            } else {
                $('#phase_option_container').hide();
            }
        });

        var phaseSelectValue = $('#inspection_phase').val();
        function populateSiteAddressAndState(selectedSiteCodeId, index) {
            var selectedSiteCode = siteCodes.find(function(siteCode) {
                return siteCode.id == selectedSiteCodeId;
            });

            if (selectedSiteCode) {
                $('input[name="site_of_inspection[]"]').eq(index).val(selectedSiteCode.site_address);
                var stateId = selectedSiteCode.state_id;
                $('select[name="state_id[]"]').eq(index).val(stateId);
            } else {
                $('input[name="site_of_inspection[]"]').eq(index).val('');
                $('select[name="state_id[]"]').eq(index).val('');
            }
        }

        $('#category_type_id').change(function() {
            handleSiteCodeChange();
        });

        $('#site_code_container').on('change', 'select[name="site_code_id[]"]', function() {
            var selectedSiteCodeId = $(this).val();
            var index = $(this).index('select[name="site_code_id[]"]');
            populateSiteAddressAndState(selectedSiteCodeId, index);
        });

       

        function validateForm() {
            var isValid = true;
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

            var inspectionPhase = $('#inspection_phase').val();
            return isValid;
        }

        var selectedDocumentNumberId = $('#opcw_document_id').val();
        if(selectedDocumentNumberId){
            handleFaxDocumentDisplay(selectedDocumentNumberId);     
        }

        $('#opcw_document_id').change(function() {
            var selectedDocumentNumberId = $(this).val();
            handleFaxDocumentDisplay(selectedDocumentNumberId);
        });

        function handleFaxDocumentDisplay(documentNumberId) {
            var selectedDocumentNumberId = $('#opcw_document_id').val();
            var documentContainer = $('#fax_document_container');
            var fileLinkContainer = $('#fax_document_link'); 
            
            if (selectedDocumentNumberId) {
                if(selectedDocumentNumberId == 'not_list'){
                    documentContainer.hide(); 
                    fileLinkContainer.hide(); 
                    $('#detailsModal').modal('show');
                }else{
                    $.ajax({
                        url: "{{ route('getFaxDetails') }}",  
                        type: 'GET',  
                        data: {
                            document_number_id: selectedDocumentNumberId,  
                            _token: '{{ csrf_token() }}'  
                        },
                        success: function(response) {
                            var faxData = response;
                            if (faxData && faxData['fax_document']) {
                                documentContainer.show();
                                var faxDocumentUrl = '{{ asset("storage/app/") }}' + '/' + faxData['fax_document'];
                                fileLinkContainer.show();  
                                fileLinkContainer.html('<a href="' + faxDocumentUrl + '" target="_blank">View Document</a>');
                                $('#fax_document').prop('disabled', true);
                            } else {
                                documentContainer.show();
                                $('#fax_document').attr('required', true); 
                                $('#fax_document').prop('disabled', false);
                                fileLinkContainer.hide(); 
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('Error:', error);
                        }
                    });
                }
            }
        }

        $('#updateVisitForm').submit(function(e) {
            e.preventDefault();
            if (validateForm()) {
                var formData = new FormData(this);
  
                if ($('#is_closed').prop('checked')) {
                    formData.append('is_closed', '1');
                } else {
                    formData.append('is_closed', '0'); 
                }

                var fileInput = $('#fax_document')[0];  
                var faxDocumentContainer = $('#fax_document_container');  
                if (faxDocumentContainer.is(':visible') && fileInput && fileInput.files.length > 0) {
                    formData.append('fax_document', fileInput.files[0]);
                } else {
                    formData.append('fax_document', null); 
                }

                var visitId = $('#editVisitId').val();
                $.ajax({
                    url: "{{ url('update-inspection') }}/" + visitId,
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
                                msg: response.msg || 'inspection updated successfully!',
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

        $('#addFaxForm').submit(function(e) {
            e.preventDefault();
            var fileLinkContainer = $('#fax_document_link'); 
            var faxDocumentContainer = $('#fax_document_container'); 
            if (validateForm()) {
                var formData = new FormData(this);
                $.ajax({
                    url: "{{ route('createOpcw') }}",
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
                                msg: response.msg || 'Opcw added successfully!',
                                type: 'success'
                            });

                            faxDocumentContainer.show();  
                            $('#detailsModal').modal('hide');
                            var faxNumber = response.fax_number;
                            var existsInDropdown = $('#opcw_document_id option[value="' + faxNumber + '"]').length > 0;
                            
                            if (!existsInDropdown) {
                                $('#opcw_document_id').append(new Option(faxNumber, faxNumber));
                            }
        
                            $('#opcw_document_id').val(faxNumber);
                            fileLinkContainer.show();  
                            var faxDocumentUrl = '{{ asset("storage/app/") }}' + '/' + response.fax_document;
                            fileLinkContainer.html('<a href="' + faxDocumentUrl + '" target="_blank">View Document</a>');
                            $('#fax_document').prop('disabled', true);
                            setTimeout(function() {
                                $('#detailsModal').modal('hide');
                            }, 1500); 
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