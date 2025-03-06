@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <!-- <h3 class="mb-4">Add New Visit</h3> -->
    <div class="text-left mb-3">
        <button type="button" class="btn back-btn" onclick="window.location='{{ route('manageVisit') }}'">Back</button>
    </div>
    <form id="addVisitForm" enctype="multipart/form-data">
        <div class="card card-outline-secondary inspector-form">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="inspection_type_selection">Inspection Type</label>
                        <select class="form-control" id="inspection_type_selection" name="inspection_type_selection" required>
                            <option value="">Select Inspection Type</option>
                            @foreach ($inspection_properties as $properties )
                            <option value="{{$properties->id}}">{{ $properties->name }}</option>
                            @endforeach
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
                        <label for="escort_officers">Escort Officers (Inspection Site)</label>
                        <select class="form-control" id="escort_officers" name="escort_officers[]" multiple required>
                            @foreach($escort_officers as $officer)
                            <option value="{{ $officer->id }}">{{ $officer->officer_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                </div>

                <div class="row">
                   

                <div class="col-md-4 form-group inspector-form-group">
                        <label for="escort_officers_poe">Escort Officers (POE)</label>
                        <select class="form-control" id="escort_officers_poe" name="escort_officers_poe[]" multiple required>
                            @foreach($escort_officers as $officer)
                            <option value="{{ $officer->id }}">{{ $officer->officer_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-group inspector-form-group" id="point_of_entry_container">
                        <!-- Site code fields will be appended here -->
                        <label for="point_of_entry">Point of Entry</label>
                        <select class="custom-select" name="point_of_entry[]" multiple required id="point_of_entry">
                            <option value="">Select Point of Entry</option>
                            @foreach($point_address as $place)
                            <option value="{{ $place->id }}">{{ $place->point_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-group inspector-form-group" id="point_of_exit_container">
                    <label for="point_of_exit">Point of Exit</label>
                        <!-- Site of Inspection fields will be appended here -->
                        <select class="custom-select" name="point_of_exit[]" multiple required id="point_of_exit">
                            <option value="">Select Point of Exit</option>
                            @foreach($point_address as $place)
                            <option value="{{ $place->id }}">{{ $place->point_name }}</option>
                            @endforeach
                        </select>
                    </div>

                  
                </div>

                <div class="row">
                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="arrival_datetime">Date Time of Arrival</label>
                        <input type="datetime-local" class="form-control" id="arrival_datetime" name="arrival_datetime" required>
                    </div>

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="departure_datetime">Date Time of Departure</label>
                        <input type="datetime-local" class="form-control" id="departure_datetime" name="departure_datetime" required>
                    </div>

                    <div class="col-md-2 form-group inspector-form-group">
                        <label for="category_id">Visit Category</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($visit_categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-2 form-group inspector-form-group mt-4">
                        <div class="form-check">
                            <input type="checkbox" id="is_closed" name="is_closed" value="1">
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
                            <option value="{{ $id }}">{{ $fax_number }}</option>
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
                        <textarea class="form-control" id="acentric_report" name="acentric_report" rows="3" ></textarea>
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
                            <img id="addVisitCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image mb-2">
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

function resetModal() {
            $('#addFaxForm')[0].reset();
            $('#fax_document_container').show();  
            $('#fax_document').attr('required', true);  
            $('#fax_document_link').hide(); 
        }

        
    $('#detailsModal').on('hidden.bs.modal', function() {
        resetModal();
    });
    
    $(document).ready(function() {
        

        $('#opcw_document_id').select2({
            placeholder: "Select Document Number", 
            allowClear: true 
        });
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var inspectionCategories = @json($inspection_categories);
        var inspectionTypes = @json($inspection_types);
        var inspectionPhases = @json($inspection_phases);
        var inspectionCategoriesType = @json($inspection_categories_type);
        var selectedInspectionCategoryId = $('#inspection_category_id').val();

        const arrivalInput = document.getElementById('arrival_datetime');
        const departureInput = document.getElementById('departure_datetime');

        arrivalInput.addEventListener('input', function() {
            const arrivalValue = arrivalInput.value;
            departureInput.min = arrivalValue;
            if (departureInput.value && departureInput.value < arrivalValue) {
                departureInput.value = arrivalValue;
            }
        });

        $('#inspectionFields').on('change', 'select[name="site_code_id[]"]', function() {
            var selectedOption = $(this).find('option:selected');
            var siteAddress = selectedOption.data('address');
            var siteName = selectedOption.data('name');
            var stateId = selectedOption.data('state-id'); 
            var inputIndex = $(this).index('select[name="site_code_id[]"]');  
            $('#site_of_inspection_container input[name="site_of_inspection[]"]').eq(inputIndex).val(siteName + ', ' + siteAddress);
            $('#state_container select[name="state_id[]"]').eq(inputIndex).val(stateId);
        });

        $('#inspection_type_selection').change(function() {
            var selectedValue = $(this).val();
            $('#category_type_id').empty().append('<option value="">Select Sub Category Type</option>');
            $('#inspectionFields').addClass('d-none'); 

            if (selectedValue == '1' || selectedValue == '2') {
                $('#categoryTypeContainer').removeAttr('disabled');

                if (selectedValue === '2') {
                    $('#category_type_id').val('sequential'); 
                } else {
                    $('#category_type_id').prop('disabled', false);
                }

                let uniqueTypes = {};

                inspectionCategories.forEach(function(category) {
                    var isChallenge = category.is_challenge === 1;
                    if ((selectedValue === '1' && !isChallenge) ||
                        (selectedValue === '2' && isChallenge)) {
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
            } else {
                $('#categoryTypeContainer').attr('disabled', true);
            }
        });

    const inspectionFields = $('#inspectionFields');
    const categoryOfInspectionSelect = $('#inspection_category_id');
    const siteCodeTemplate = `
        <div class="col-md-3 form-group inspector-form-group" id="site_code_container">
            <label for="site_code_id">Plant Site</label>
            <select class="form-control" name="site_code_id[]" required>
                <option value="">Plant Site</option>
                @foreach($site_codes as $site_code)
                <option value="{{ $site_code->id }}" data-name="{{ $site_code->site_name }}" data-address="{{ $site_code->site_address }}" data-state-id="{{ $site_code->state_id }}">
                    {{ $site_code->site_code }}
                </option>
                @endforeach
            </select>
        </div>
    `;
    const siteOfInspectionTemplate = `
        <div class="col-md-3 form-group inspector-form-group" id="site_of_inspection_container">
            <label for="site_of_inspection">Site Name and Address</label>
            <input type="text" class="form-control" name="site_of_inspection[]" placeholder="Site Name and Address" required>
        </div>
    `;
    const stateTemplate = `
        <div class="col-md-3 form-group inspector-form-group" id="state_container">
            <label for="state_id">State</label>
            <select class="form-control" name="state_id[]" required>
                <option value="">Select State</option>
                @foreach($states as $state)
                <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                @endforeach
            </select>
        </div>
    `;
    const inspectionCategoryTemplate = (filter = null) => `
        <div class="col-md-3 form-group inspector-form-group" id="inspection_category_container">
            <label for="inspection_category_id">Inspection Category</label>
            <select class="form-control" name="inspection_category_id[]" required>
                <option value="">Select Inspection Category</option>
                @foreach($all_inspection_category as $inspection_category)
                    ${
                    filter && !filter.includes({{ $inspection_category->id }}) 
                    ? '' 
                    : `<option value="{{ $inspection_category->id }}" data-name="{{ $inspection_category->type_name }}">
                            {{ $inspection_category->type_name }}
                        </option>`
                    }
                @endforeach
            </select>
        </div>
    `;

    const inspectionPhaseTemplate = `
        <div class="col-md-3 mb-4 form-group inspector-form-group" id="inspection_phase_container">
            <label for="inspection_phase_id">Inspection Phase</label>
            <select class="form-control" name="inspection_phase_id[]" required>
                <option value="">Select Inspection Phase</option>
                @foreach($inspection_phases as $inspection_phase)
                <option value="{{ $inspection_phase->id }}" data-name="{{ $inspection_phase->phase_type_name }}">
                    {{ $inspection_phase->phase_type_name }}
                </option>
                @endforeach
            </select>
        </div>
    `;

    const adjustSpace = `
        <div class="col-md-3 mb-4 form-group inspector-form-group" >
           
        </div>
    `;

    const inspectionPhaseOptionTemplate = `
        <div class="col-md-3 mb-4 form-group inspector-form-group" id="phase_option_container" style="display: none;">
            <label for="phase_option_id">Phase Option</label>
            <select class="form-control" name="phase_option_id[]" required>
                <option value="" disabled selected>Select Inspection Phase Option</option>
                @foreach($phase_options as $phase_option)
                    <option value="{{ $phase_option->id }}" data-name="{{ addslashes($phase_option->option_name) }}">
                        {{ htmlspecialchars($phase_option->option_name) }}
                    </option>
                @endforeach
            </select>
        </div>
    `;

    const preliminaryReportTemplate = `
        <div class="col-md-3 mb-4 form-group inspector-form-group" id="preliminary_report_container">
            <label for="preliminary_report">Preliminary Report</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="preliminary_report" name="preliminary_report[]" accept="application/pdf" required 
                    onchange="this.nextElementSibling.textContent = this.files.length > 0 ? this.files[0].name : 'Choose Preliminary Report';">
                <label class="custom-file-label" for="preliminary_report">Choose Preliminary Report</label>
            </div>
        </div>
    `;
    const finalInspectionReportTemplate = `
        <div class="col-md-3 mb-4 form-group inspector-form-group" id="final_inspection_report_container">
            <label for="final_inspection_report">Final Inspection Report</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="final_inspection_report" name="final_inspection_report[]" accept="application/pdf" required 
                    onchange="this.nextElementSibling.textContent = this.files.length > 0 ? this.files[0].name : 'Choose Final Inspection Report';">
                <label class="custom-file-label" for="final_inspection_report">Choose Final Inspection Report</label>
            </div>
        </div>
    `;
    const inspectionIssue = `
        <div class="col-md-3 mb-4 form-group inspector-form-group" id="inspection_issue_container">
            <label for="inspection_issue_id">Issues</label>
            <select name="inspection_issue_id[]" class="form-control">
                <option value="">Select Issues</option>
                @foreach($inspection_issues as $inspection_issue)
                <option value="{{ $inspection_issue->id }}">{{ $inspection_issue->name }}</option>
                @endforeach
            </select>
        </div>
    `;
    const issueDocument = `
        <div class="col-md-4 form-group inspector-form-group" id="issue_document_container" >
            <label for="issue_document">Upload Issue Document<span class="text-danger upload-font">*PDF only (Max. Size 10MB)</span></label>
            
            <input type="file" class="form-control-file" name="issue_document[]" accept="application/pdf">
        </div>
    `;

    function resetFields() {
        inspectionFields.empty().removeClass('d-none');
        $('#site_code_container, #site_of_inspection_container, #state_container, #inspection_category_container, #inspection_phase_container, #phase_option_container, #preliminary_report_container, #final_inspection_report_container').empty();
    }

    function appendCommonFields() {
        inspectionFields.append(
            inspectionPhaseTemplate +
            siteCodeTemplate +
            siteOfInspectionTemplate +
            stateTemplate +
            preliminaryReportTemplate +
            finalInspectionReportTemplate +
            inspectionIssue +
            issueDocument
        );
    }

    $('#category_type_id').change(function () {
        const selectedTypeId = $(this).val();
        resetFields();

        if (selectedTypeId == 2) {
            inspectionFields.append(inspectionCategoryTemplate());
            appendCommonFields();
            
        } else if (selectedTypeId == 1) {
            const iterationCount = inspectionCategoriesType.find(category => category.id === 1)?.iteration || 2;
            for (let i = 0; i < iterationCount; i++) {
                inspectionFields.append(inspectionCategoryTemplate([3, 4]));
                appendCommonFields();
                inspectionFields.append(adjustSpace);
                inspectionFields.append(adjustSpace);
            }
        }

        // Update inspection categories dynamically
        categoryOfInspectionSelect.empty().append('<option value="">Select Inspection Category</option>');
        const filteredCategories = inspectionTypes.filter(type => selectedTypeId == 2 || (selectedTypeId == 1 && [3, 4].includes(type.id)));
        filteredCategories.forEach(type => {
            categoryOfInspectionSelect.append('<option value="' + type.id + '">' + type.type_name + '</option>');
        });
    });

    $(document).on('change', 'select[name="inspection_category_id[]"], select[name="inspection_phase_id[]"]', function () {
        const inspectionCategoryId = $('select[name="inspection_category_id[]"]').val();
        const inspectionPhaseId = $('select[name="inspection_phase_id[]"]').val();


        if (inspectionCategoryId == 2 && inspectionPhaseId == 2) {
            inspectionFields.append(inspectionPhaseOptionTemplate); // Append the phase option template
        } else {
            $('#phase_option_container').remove(); // Remove it if conditions are not met
        }

        $('#phase_option_container').toggle(inspectionCategoryId == 2 && inspectionPhaseId == 2);
    });

        var inspectionTypeSelected = false;
        var phaseSelected = false;

        // Handle Inspection Type Change
        $('#inspection_category_id').change(function() {
            var selectedInspectionCategoryId = $(this).val();
            handleInspectionTypeChange(selectedInspectionCategoryId);
        });

        $('#inspection_phase').change(function() {
            var selectedInspectionCategoryId = $('#inspection_category_id').val();
        });

        $('#opcw_document_id').change(function() {
            var selectedDocumentNumberId = $(this).val(); 
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


            } else {
                documentContainer.hide();
                fileLinkContainer.hide(); 
            }
        });

        function handleInspectionTypeChange(inspectionTypeId) {
            var phaseSelect = $('#inspection_phase');
            phaseSelect.empty().append('<option value="">Select Phase</option>');
            $('#inspection_phase_container').show();
        }

        // updatePhaseOptions(selectedInspectionCategoryId);

        function validateForm() {
            var isValid = true;
            return isValid;
        }

        $('#addVisitForm').submit(function(e) {
            e.preventDefault();

            if (validateForm()) {
                var formData = new FormData(this);
                if ($('#inspection_type_selection').val() === 'challenge') {
                    formData.append('category_type_id', '1');
                }

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

                            console.log(response);

                            faxDocumentContainer.show();  
                            $('#detailsModal').modal('hide');
                            var faxId = response.fax_id; // The fax_id from the response
                            var faxNumber = response.fax_number; // The fax_number from the response
                            var faxDocument = response.fax_document; // The fax_document path
                            var existsInDropdown = $('#opcw_document_id option[value="' + faxId + '"]').length > 0;
                    
                            if (!existsInDropdown) {
                                // Add the fax number to the dropdown with fax_id as the value
                                $('#opcw_document_id').append(new Option(faxNumber, faxId));
                            }
                            
                            // Set the selected option to the fax number
                            $('#opcw_document_id').val(faxId);
                            
                            fileLinkContainer.show();  

                            var faxDocumentUrl = '{{ asset("storage/app/") }}' + '/' + response.fax_document;
    
                           
                            fileLinkContainer.html('<a href="' + faxDocumentUrl + '" target="_blank">View Document</a>');

                            $('#fax_document').prop('disabled', true);


                            setTimeout(function() {
                                $('#detailsModal').modal('hide');
                            }, 1500); 
                         
                        } else {
                            FancyAlerts.show({
                                msg: 'Error: ' + response.msg,
                                type: 'error'
                            });
                        }

                        refreshCaptcha('addVisitCaptchaImage');
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