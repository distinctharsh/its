@extends('layouts.layout')

@section('content')

<div class="container-fluid mt-4 table-responsive">
    <!-- <h1>General Query Report</h1> -->
     
    @if($monthName)
    <h1>{{ $monthName }} - {{ $year }}</h1>
    @else
    <h1>{{ $year }}</h1>
@endif
 
    <div class=" center-block expand-box text-white mb-3">
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingOne">
                    <h5 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne" class="btn-filter">
                            <strong>Filter</strong>
                        </a>
                    </h5>
                </div>

                <div id="collapseOne" class="panel-collapse collapse in text-dark" role="tabpanel" aria-labelledby="headingOne">
                    <div class="panel-body">

                        <div class="row ml-3 mb-3 pt-3 justify-content-between">
                            <form method="POST" action="{{ route('listInspectors.post') }}" class="w-100">
                                @csrf

                                <!-- First Row -->
                                <div class="row mr-3">
                                    <div class="col-md-4">
                                        <!-- Escort Officer Filter -->
                                        <div class="mb-3">
                                            <label for="escortOfficer">Escort Officers</label><br>
                                            <select name="escortOfficer[]" id="escortOfficer" class="form-control" multiple>
                                                @foreach ($escortOfficers as $id => $name)
                                                    <option value="{{ $id }}"
                                                            @if(in_array($id, old('escortOfficer', $escortOfficerIds ?? []))) selected @endif>
                                                        {{ $name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <!-- Rank Filter -->
                                        <div class="mb-3">
                                            <label for="rank">Rank</label><br>
                                            <select name="rank[]" id="rank" class="form-control" multiple>
                                                <option value="">Select Rank</option>
                                                @foreach ($allRank as $rank)
                                                <option value="{{ $rank->id }}"
                                                    @if(in_array($rank->id, old('rank', (array)$rankId))) selected @endif>
                                                    {{ $rank->rank_name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>

                                     
                                    </div>

                                    <div class="col-md-4">
                                        <!-- Status Filter -->
                                        <div class="mb-3">
                                            <label for="status">Status</label>
                                            <select name="status[]" id="status" class="form-control" multiple>
                                                <option value="">Select status</option>
                                                @foreach ($allStatus as $status)
                                                <option value="{{ $status->id }}"
                                                    @if(in_array($status->id, old('status', (array)$statusId))) selected @endif>
                                                    {{ $status->status_name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                </div>

                                <!-- Second Row -->
                                <div class="row mr-3">

                                    <div class="col-md-4">
                                        <!-- Nationality Filter -->
                                        <div class="mb-3">
                                            <label for="country">Nationality</label>
                                            <select name="country[]" id="country" class="form-control" multiple>
                                                <option value="">Select Nationality</option>
                                                @foreach ($allCountry as $country)
                                                <option value="{{ $country->id }}"
                                                    @if(in_array($country->id, old('country', (array)$countryId))) selected @endif>
                                                    {{ $country->country_name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <!-- State Filter -->
                                        <div class="mb-3">
                                            <label for="state">State</label>
                                            <select name="state[]" id="state" class="form-control" multiple>
                                                <option value="">Select State</option>
                                                @foreach ($allStates as $state)
                                                <option value="{{ $state->id }}"
                                                    @if(in_array($state->id, old('state', (array)$stateId))) selected @endif>
                                                    {{ $state->state_name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <!-- Site Code Filter -->
                                        <div class="mb-3">
                                            <label for="siteCode">Plant Site</label><br>
                                            <select name="siteCode[]" id="siteCode" class="form-control" multiple>
                                                <option value="">Select Plant Site</option>
                                                @foreach ($siteCodes as $siteCode)
                                                    <option value="{{ $siteCode->id }}"
                                                            @if(in_array($siteCode->id, old('siteCode', (array)$siteCodeId))) selected @endif>
                                                        {{ $siteCode->site_code }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                  
                                </div>


                                <div class="row mr-3">
                                    <div class="col-md-4">
                                        <!-- Inspection Type Selection Filter -->
                                        <div class="mb-3">
                                            <label for="inspectionTypeSelection">Inspection Category</label>
                                            <select name="inspectionTypeSelection[]" id="inspectionTypeSelection" class="form-control" multiple>
                                                <option value="">Select Inspection Category</option>
                                                <option value="routine" {{ in_array('routine', old('inspectionTypeSelection', (array)$inspectionTypeSelection)) ? 'selected' : '' }}>Routine</option>
                                                <option value="challenge" {{ in_array('challenge', old('inspectionTypeSelection', (array)$inspectionTypeSelection)) ? 'selected' : '' }}>Challenge</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <!-- Inspection Sub Category Type Filter -->
                                        <div class="mb-3">
                                            <label for="inspectionCategoryType">Sub Category Type</label><br>
                                            <select name="inspectionCategoryType[]" id="inspectionCategoryType" class="form-control" multiple>
                                                <option value="">Select Sub Category Type</option>
                                                @foreach ($allInspectionCategoryType as $inspectionCategoryType)
                                                <option value="{{ $inspectionCategoryType->id }}"
                                                    @if(in_array($inspectionCategoryType->id, old('inspectionCategoryType', (array)$inspectionCategoryTypeId))) selected @endif>
                                                    {{ $inspectionCategoryType->type_name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <!-- Type of Inspection Filter -->
                                        <div class="mb-3">
                                            <label for="typeOfInspection">Type of Inspection</label>
                                            <select name="typeOfInspection[]" id="typeOfInspection" class="form-control" multiple>
                                                <option value="">Select Type of Inspection</option>
                                                @foreach ($typesOfInspection as $type)
                                                <option value="{{ $type->id }}"
                                                    @if(in_array($type->id, old('typeOfInspection', (array)$typeOfInspection))) selected @endif>
                                                    {{ $type->type_name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mr-3">
                                   

                                    <div class="col-md-4 d-flex flex-column flex-md-row">
                                        <!-- Date of Joining Filter -->
                                        <div class="mb-3">
                                            <label for="dateOfJoiningFrom">Date of Joining (From - To)</label>
                                            <div class="d-flex flex-column flex-md-row">
                                                <!-- From Date Input -->
                                                <input type="date" name="dateOfJoiningFrom" id="dateOfJoiningFrom" class="form-control " value="{{ old('dateOfJoiningFrom', $dateOfJoiningFrom ? \Carbon\Carbon::parse($dateOfJoiningFrom)->format('Y-m-d') : '') }}" placeholder="From">
                                                
                                                <!-- To Date Input -->
                                                <input type="date" name="dateOfJoiningTo" id="dateOfJoiningTo" class="form-control" value="{{ old('dateOfJoiningTo', $dateOfJoiningTo ? \Carbon\Carbon::parse($dateOfJoiningTo)->format('Y-m-d') : '') }}" placeholder="To">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <!-- Date of Arrival Filter -->
                                        <div class="mb-3">
                                            <label for="dateOfArrival">Date of Arrival</label>
                                            <input type="date" name="dateOfArrival" id="dateOfArrival" class="form-control" value="{{ old('dateOfArrival', $dateOfArrival ? \Carbon\Carbon::parse($dateOfArrival)->format('Y-m-d') : '') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-4 ">
                                        <!-- Date of Departure Filter -->
                                        <div class="mb-3">
                                            <label for="dateOfDeparture">Date of Departure</label>
                                            <input type="date" name="dateOfDeparture" id="dateOfDeparture" class="form-control" value="{{ old('dateOfDeparture', $dateOfDeparture ? \Carbon\Carbon::parse($dateOfDeparture)->format('Y-m-d') : '') }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Fourth Row -->
                                <div class="row mr-3">

                                    <div class="col-md-4">
                                        <!-- Visit Category Filter -->
                                        <div class="mb-3">
                                            <label for="visitCategory">Visit Category Type</label><br>
                                            <select name="visitCategory[]" id="visitCategory" class="form-control" multiple>
                                                <option value="">Select Visit Category</option>
                                                @foreach ($allVisitCategory as $visitCategory)
                                                <option value="{{ $visitCategory->id }}"
                                                    @if(in_array($visitCategory->id, old('visitCategory', (array)$visitCategoryId))) selected @endif>
                                                    {{ $visitCategory->category_name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <!-- Designation Filter -->
                                        <div class="mb-3">
                                                <label for="designation">Designation</label><br>
                                                <select name="designation[]" id="designation" class="form-control" multiple>
                                                    <option value="">Select Designation</option>
                                                    @foreach ($allDesignation as $designation)
                                                    <option value="{{ $designation->id }}"
                                                        @if(in_array($designation->id, old('designation', (array)$designationId))) selected @endif>
                                                        {{ $designation->designation_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                    </div>


                                



                                  
                                </div>



                                <!-- Submit Button Row -->
                                <div class="row">
                                    <div class="col-12 text-center mt-3 mb-3">
                                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <table class="display table table-bordered table-striped myDataTable" data-exports-column="0,1,2,3,4,5">
       
            <select id="reportPageLengthSelect" class="form-control "  >
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="all">All</option>
            </select>
      

        <thead class="thead-dark">
            <tr>
                <th scope="col">S.No.</th>
                <th scope="col">Inspector Name</th>
                <th scope="col">No. of Times Inspector visit India</th>               
                <th scope="col">Gender</th>
                <th scope="col">Date of Birth</th>
                <th scope="col">Rank</th>
                <th scope="col">Professional Experience</th>
                <th scope="col">Details</th>
            </tr>
        </thead>

        @php
    $inspectorsData = collect($inspectorsData); // Ensure it's a collection
@endphp
        <tbody id="inspectorListTableBody">
        @if(empty($inspectorsData))
            <tr>
                <td colspan="7" class="text-center">No inspectors available.</td>
            </tr>
            @else
            @foreach($inspectorsData as $index => $inspector)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $inspector['name'] }}</td>
                <td>{{ $inspector['count_cti'] }}</td>
                <td>{{ $inspector['gender'] }}</td>
                <td>{{ \Carbon\Carbon::parse($inspector['dob'])->format('d-m-Y') }}</td>
                <td>{{ $inspector['rank'] }}</td>
                <td>{{ $inspector['professional_experience'] }}</td>
                <td>
                    <button class="btn btn-info btn-sm" onclick='showDetails(@json($inspector))'>
                        View Details
                    </button>
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</div>

<!-- Modal for displaying inspector details -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Inspector Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <!-- Personal Information Card -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title"> Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Inspector Name:</strong> <span id="modalName"></span></p>
                        <p><strong>Gender:</strong> <span id="modalGender"></span></p>
                        <p><strong>Date of Birth:</strong> <span id="modalDOB"></span></p>
                        <p><strong>Rank:</strong> <span id="modalRank"></span></p>

                        <p><strong>Passport Number:</strong> <span id="modalPassportNumber"></span></p>
                        <p><strong>UNLP Number:</strong> <span id="modalUNLPNumber"></span></p>
                        <p><strong>Professional Experience:</strong> <span id="modalProfessionalExperience"></span></p>
                        <p><strong>Qualifications:</strong> <span id="modalQualifications"></span></p>
                        <p><strong>Remarks:</strong> <span id="modalRemarks"></span></p>
                    </div>
                </div>

                <!-- Inspection Details Card -->
                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title">Inspection Details</h5>
                    </div>
                    <div class="card-body">
                        <div id="inspectionDetails"></div>
                    </div>
                </div>

                <!-- Visit Details Card -->
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title">Visit Details</h5>
                    </div>
                    <div class="card-body">
                        <div id="visitDetails"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="copyToClipboard()" data-toggle="tooltip" data-placement="top" title="Copy to Clipboard">
                    <i class="fa-solid fa-copy"></i>
                </button>
                <button type="button" class="btn btn-success" onclick="downloadExcel()" data-toggle="tooltip" data-placement="top" title="Download as Excel">
                    <i class="fa-solid fa-file-excel"></i>
                </button>
                <button type="button" class="btn btn-danger" onclick="downloadPDF()" data-toggle="tooltip" data-placement="top" title="Download as PDF">
                    <i class="fa-solid fa-file-pdf"></i>
                </button>
                <button type="button" class="btn btn-info" onclick="printModal()" data-toggle="tooltip" data-placement="top" title="Print">
                    <i class="fa-solid fa-print"></i>
                </button>

                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<!-- Include html2canvas (latest version) -->
<!-- <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script> -->

<!-- Include jsPDF (Latest Version) -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script> -->

<!-- Include XLSX (For Excel Export) -->
<!-- <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script> -->

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script> -->

<!-- Include pdfMake -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.69/pdfmake.min.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.69/vfs_fonts.js"></script> -->

<script>
    $(document).ready(function() {
  
    const exportColumnsAttrs = $('.myDataTable').data('exports-column');
      const columnsToExports = exportColumnsAttrs ? exportColumnsAttrs.split(',').map(Number) : [];
    

    var reportTable = $('.myDataTable').DataTable({
        dom: 'Bfrtip',
        pageLength: 10,
        language: {
            emptyTable: "No Record Found",
            zeroRecords: "No matching records found.",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
        },
        buttons: [
            {
                extend: 'copyHtml5',
                text: '<i class="fa-solid fa-copy"></i>',
                titleAttr: 'Copy to clipboard',
                exportOptions: {
                    columns: columnsToExports
                }
            },
            {
                extend: 'excelHtml5',
                text: '<i class="fa-solid fa-file-excel"></i>',
                titleAttr: 'Export to Excel',
                exportOptions: {
                    columns: columnsToExports
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa-solid fa-file-pdf"></i>',
                titleAttr: 'Export to PDF',
                exportOptions: {
                    columns: columnsToExports
                },
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                text: '<i class="fa-solid fa-print"></i>',
                titleAttr: 'Print the table',
                exportOptions: {
                    columns: columnsToExports
                },
                
            }
        ],
        responsive: true,
        autoWidth: true,
    });

    $('#reportPageLengthSelect').on('change', function() {
        const newLength = $(this).val() === "all" ? -1 : parseInt($(this).val());
        reportTable.page.len(newLength).draw(false);
      });

    // Test the DataTable with no filters applied (for debugging purposes)
    // This should show all records without any filtering
    reportTable.draw();  // Force the table to redraw and show all data
});

</script>



<script>
    // Function to format date to dd-mm-yy format
    function formatDate(dateString) {
        const date = new Date(dateString);
        if (isNaN(date)) return 'N/A'; // If the date is invalid, return N/A
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
        const year = date.getFullYear(); // Get last two digits of the year
        return `${day}-${month}-${year}`;
    }

    function getTeamLeadName(teamLeadId) {
        // Check if the team lead ID is already set to avoid multiple calls
        if (!teamLeadId) return 'N/A'; // If no team lead ID is provided, return 'N/A'

        // Find the team lead from the inspectors data array
        const teamLead = allInspectors.find(inspector => inspector.id === teamLeadId);

        // If a team lead is found, return their name, otherwise return 'N/A'
        return teamLead ? teamLead.name : 'N/A';
    }

    function capitalizeFirstLetter(string) {
        return string.replace(/\b\w/g, function(char) {
            return char.toUpperCase();
        });
    }


    // Pass the escortOfficers data to JavaScript
    const escortOfficersData = @json($escortOfficers);
    const allInspectors = @json($allInspectors);
    const inspectionCategories = @json($inspectionCategories);
    const allVisitCategory = @json($allVisitCategory);
    const typesOfInspection = @json($typesOfInspection);

    const siteCodes = @json($siteCodes); // Pass site codes array
    const allStates = @json($allStates);
    const allStatus = @json($allStatus);

    function showDetails(inspector) {
        console.log(inspector);

        // Personal info setup
        $('#modalName').text(inspector.name || 'N/A');
        $('#modalGender').text(inspector.gender || 'N/A');
        $('#modalRank').text(inspector.rank || 'N/A');
        $('#modalProfessionalExperience').text(inspector.professional_experience || 'N/A');
        $('#modalDOB').text(inspector.dob);
        $('#modalPassportNumber').text(inspector.passport_number || 'N/A');
        $('#modalUNLPNumber').text(inspector.unlp_number || 'N/A');
        $('#modalQualifications').text(inspector.qualifications || 'N/A');
        $('#modalRemarks').text(inspector.remarks || 'N/A');

        // Populate inspection details
        let inspectionHtml = '';
        if (inspector.inspections && inspector.inspections.length > 0) {
            inspector.inspections.forEach(inspection => {

                const category = inspectionCategories.find(c => c.id == inspection.category_id)?.category_name || 'N/A';
                const status = allStatus.find(s => s.id == inspection.status_id)?.status_name || 'N/A';


                inspectionHtml += `
            <div class="inspection">
                 <div class="category"><strong>Category:</strong> ${category}</div><br>
                <div class="date-of-joining"><strong>Date of Joining:</strong> ${formatDate(inspection.date_of_joining) || 'N/A'}</div><br>
                 <div class="status"><strong>Status:</strong> ${status}</div>
            </div><hr>`;
            });
        } else {
            inspectionHtml = '<p>No inspection data available.</p>';
        }
        $('#inspectionDetails').html(inspectionHtml);

        // Populate visit details
        let visitHtml = '';
        if (inspector.visits && inspector.visits.length > 0) {
            inspector.visits.forEach(visit => {
                // Handle escort officers data
                let escortOfficers = 'N/A'; // Default value
                if (visit.list_of_escort_officers) {
                    try {
                        const escortOfficersArray = JSON.parse(visit.list_of_escort_officers);
                        const uniqueEscortOfficers = [...new Set(escortOfficersArray)];
                        escortOfficers = uniqueEscortOfficers.map(id => escortOfficersData[id] || 'Unknown Officer').join(', ');
                    } catch (e) {
                        console.error('Error parsing escort officers:', e);
                    }
                }

                // Handle team lead
                const teamLeadName = visit.team_lead_id ? getTeamLeadName(visit.team_lead_id) : 'N/A';

                const visitCategory = allVisitCategory.find(c => c.id == visit.category_id)?.category_name || 'N/A';
                const inspectionType = typesOfInspection.find(c => c.id == visit.type_of_inspection_id)?.type_name || 'N/A';


                // Handle list of inspectors
                let inspectors = 'N/A';
                if (visit.list_of_inspectors) {
                    try {
                        const inspectorsArray = JSON.parse(visit.list_of_inspectors);
                        inspectors = inspectorsArray.map(id => {
                            const foundInspector = allInspectors.find(ins => ins.id == id);
                            return foundInspector ? foundInspector.name : 'Unknown Inspector';
                        }).join(', ');
                    } catch (e) {
                        console.error('Error parsing list_of_inspectors:', e);
                    }
                }

                // Handle site mappings (map IDs to names)
                let siteMappingsHtml = '';
                if (visit.site_mappings && visit.site_mappings.length > 0) {
                    visit.site_mappings.forEach(mapping => {
                        // Find site code and state name based on IDs
                        const siteCode = siteCodes.find(sc => sc.id == mapping.site_code_id)?.site_code || 'N/A';
                        const stateName = allStates.find(state => state.id == mapping.state_id)?.state_name || 'N/A';

                        siteMappingsHtml += `
                    <div class="site-mapping">
                        <br><div class="site-code"><strong>Plant Site:</strong> ${siteCode}</div><br>
                        <div class="site-of-inspection"><strong>Site of Inspection:</strong> ${mapping.site_of_inspection || 'N/A'}</div><br>
                        <div class="state-name"><strong>State:</strong> ${stateName}</div>
                    </div><hr>`;
                    });
                } else {
                    siteMappingsHtml = '<p>No site mappings available.</p>';
                }

                // Assemble visit HTML
                visitHtml += `
            <div class="visit">
                <div class="team-lead"><strong>Team Lead:</strong> ${teamLeadName}</div><br>
                <div class="escort-officers"><strong>Escort Officers:</strong> ${escortOfficers}</div><br>
                <div class="inspectors"><strong>List of Inspectors:</strong> ${inspectors}</div><br>
                <div class="inspection-category"><strong>Inspection Category:</strong>  ${visit.inspection_type_selection ? capitalizeFirstLetter(visit.inspection_type_selection) : 'N/A'}</div><br>
                <div class="visit-category"><strong>Visit Category:</strong> ${visitCategory}</div><br>
                <div class="inspection-type"><strong>Inspection Type:</strong> ${inspectionType}</div><br>
                <div class="arrival"><strong>Arrival:</strong> ${formatDate(visit.arrival_datetime) || 'N/A'}</div><br>
                <div class="departure"><strong>Departure:</strong> ${formatDate(visit.departure_datetime) || 'N/A'}</div>
                <div class="site-mappings">${siteMappingsHtml}</div>
                <div class="remarks"><strong>Remarks:</strong> ${visit.remarks || 'N/A'}</div>
            </div><hr>`;
            });
        } else {
            visitHtml = '<p>No visit data available.</p>';
        }
        $('#visitDetails').html(visitHtml);

        // Show the modal
        $('#detailsModal').modal('show');
    }




    // Function to print the modal content
    function printModal() {
        // Extract the content of the modal body
        var printContents = document.querySelector('#detailsModal .modal-body').innerHTML;

        // Open a new window and write the content into the new window
        var newWindow = window.open('', '', 'height=400,width=800');
        newWindow.document.write('<html><head><title>Inspector Details</title>');
        newWindow.document.write('<style>body { font-family: Arial, sans-serif; }</style></head><body>');
        newWindow.document.write(printContents); // Write modal body content
        newWindow.document.write('</body></html>');

        // Close the document, so the content can be printed
        newWindow.document.close();

        // Trigger the print dialog
        newWindow.print();
    }


    // Function to copy modal content to clipboard
    function copyToClipboard() {
        var modalContent = document.querySelector('#detailsModal .modal-body');

        // Create a range object and select the content
        var range = document.createRange();
        range.selectNodeContents(modalContent);

        // Remove any existing selection on the page
        window.getSelection().removeAllRanges();

        // Add the new selection to the window's selection
        window.getSelection().addRange(range);

        // Try to execute the copy command
        try {
            document.execCommand('copy');
            alert("Content copied to clipboard!");
        } catch (err) {
            alert("Failed to copy content. Please try again.");
        }

        // Clear the selection after copying
        window.getSelection().removeAllRanges();
    }

    function downloadExcel() {
        var wb = XLSX.utils.book_new(); // Create a new workbook

        // Create a single array that will hold all rows of data for the Excel sheet
        var allData = [];

        // Personal Information Section
        allData.push(['Information']); // Heading
        allData.push(['Inspector Name:', $('#modalName').text()]);
        allData.push(['Gender:', $('#modalGender').text()]);
        allData.push(['Date of Birth:', $('#modalDOB').text()]); // Fixed ID to modalDOB
        allData.push(['Rank:', $('#modalRank').text()]);
        allData.push(['Passport Number:', $('#modalPassportNumber').text()]);
        allData.push(['UNLP Number:', $('#modalUNLPNumber').text()]);
        allData.push(['Professional Experience:', $('#modalProfessionalExperience').text()]);
        allData.push(['Qualifications:', $('#modalQualifications').text()]);
        allData.push(['Remarks:', $('#modalRemarks').text()]);
        allData.push(['']); // Empty row to separate sections

        // Inspection Details Section
        allData.push(['Inspection Details']); // Heading
        allData.push(['Category', 'Status', 'Date of Joining']); // Column headers

        // Get the inspection data from the modal (if any)
        $('#inspectionDetails .inspection').each(function() {
            var category = $(this).find('.category').text().replace('Category:', '').trim() || 'N/A';
            var status = $(this).find('.status').text().replace('Status:', '').trim() || 'N/A';
            var dateOfJoining = $(this).find('.date-of-joining').text().replace('Date of Joining:', '').trim() || 'N/A';
            allData.push([category, status, dateOfJoining]);
        });
        allData.push(['']); // Empty row to separate sections

        // Visit Details Section
        allData.push(['Visit Details']); // Heading
        allData.push(['Team Lead', 'Escort Officers', 'List of Inspectors', 'Inspection Type', 'Visit Category', 'Arrival', 'Departure', 'Remarks']); // Column headers

        // Get the visit data from the modal (if any)
        $('#visitDetails .visit').each(function() {
            var teamLead = $(this).find('.team-lead').text().replace('Team Lead:', '').trim() || 'N/A';
            var escortOfficers = $(this).find('.escort-officers').text().replace('Escort Officers:', '').trim() || 'N/A';
            var inspectors = $(this).find('.inspectors').text().replace('List of Inspectors:', '').trim() || 'N/A';
            var inspectionType = $(this).find('.inspection-category').text().replace('Inspection Type:', '').trim() || 'N/A';
            var visitCategory = $(this).find('.visit-category').text().replace('Visit Category:', '').trim() || 'N/A';
            var arrival = $(this).find('.arrival').text().replace('Arrival:', '').trim() || 'N/A';
            var departure = $(this).find('.departure').text().replace('Departure:', '').trim() || 'N/A';
            var remarks = $(this).find('.remarks').text().replace('Remarks:', '').trim() || 'N/A';
            allData.push([teamLead, escortOfficers, inspectors, inspectionType, visitCategory, arrival, departure, remarks]);
        });

        // Create a single sheet with all the data
        var sheet = XLSX.utils.aoa_to_sheet(allData);

        // Add the sheet to the workbook
        XLSX.utils.book_append_sheet(wb, sheet, 'Inspector Report');

        // Write the workbook to Excel
        XLSX.writeFile(wb, 'Inspector_Report.xlsx');
    }





    function downloadPDF() {
        // Gather dynamic content from the modal
        var name = $('#modalName').text();
        var gender = $('#modalGender').text();
        var dob = $('#modalDOB').text();
        var rank = $('#modalRank').text();
        var passportNumber = $('#modalPassportNumber').text();
        var unlpNumber = $('#modalUNLPNumber').text();
        var experience = $('#modalProfessionalExperience').text();
        var qualifications = $('#modalQualifications').text();
        var remarks = $('#modalRemarks').text();

        // Get inspection details dynamically
        var inspectionDetails = [];
        $('#inspectionDetails .inspection').each(function() {
            var category = $(this).find('.category').text().replace('Category:', '').trim() || 'N/A';
            var status = $(this).find('.status').text().replace('Status:', '').trim() || 'N/A';
            var dateOfJoining = $(this).find('.date-of-joining').text().replace('Date of Joining:', '').trim() || 'N/A';
            inspectionDetails.push([category, status, dateOfJoining]);
        });

        // Get visit details dynamically
        var visitDetails = [];
        $('#visitDetails .visit').each(function() {
            var teamLead = $(this).find('.team-lead').text().replace('Team Lead:', '').trim() || 'N/A';
            var escortOfficers = $(this).find('.escort-officers').text().replace('Escort Officers:', '').trim() || 'N/A';

            // Clean up Inspectors field (remove 'List of' or any unwanted text)
            var inspectors = $(this).find('.inspectors').text().replace('Inspectors:', '').replace('List of', '').trim() || 'N/A';

            // Clean up Inspection Type field (remove 'Inspection Type:' label and extra text)
            var inspectionType = $(this).find('.inspection-category').text().replace('Inspection Type:', '').replace('Inspection Category:', '').trim() || 'N/A';

            // Clean up Visit Category (remove the label "Visit Category:" and extra spaces)
            var visitCategory = $(this).find('.visit-category').text().replace('Visit Category:', '').trim() || 'N/A';

            var arrival = $(this).find('.arrival').text().replace('Arrival:', '').trim() || 'N/A';
            var departure = $(this).find('.departure').text().replace('Departure:', '').trim() || 'N/A';
            var visitRemarks = $(this).find('.remarks').text().replace('Remarks:', '').trim() || 'N/A';

            // Handle Site Mappings (multiple sites per visit)
            let siteMappingsData = []; // Array to hold data for the table rows
            if ($(this).find('.site-mapping').length > 0) {
                $(this).find('.site-mapping').each(function() {
                    var siteCode = $(this).find('.site-code').text().replace('Site Code:', '').trim() || 'N/A';
                    var siteOfInspection = $(this).find('.site-of-inspection').text().replace('Site of Inspection:', '').trim() || 'N/A';
                    var stateName = $(this).find('.state-name').text().replace('State:', '').trim() || 'N/A';
                    siteMappingsData.push([siteCode, siteOfInspection, stateName]);
                });
            } else {
                siteMappingsData.push(['N/A', 'N/A', 'N/A']);
            }

            // Add visit details tables
            visitDetails.push({
                table: {
                    body: [
                        ['Team Lead', 'Escort Officers', 'Inspectors', 'Inspection Type'], // Headers
                        [teamLead, escortOfficers, inspectors, inspectionType] // Data (only the value of inspection type)
                    ]
                },
                style: 'table',
                layout: 'lightHorizontalLines',
            }, {
                table: {
                    body: [
                        ['Visit Category', 'Arrival', 'Departure', 'Remarks'], // Headers
                        [visitCategory, arrival, departure, visitRemarks] // Data
                    ]
                },
                style: 'table',
                layout: 'lightHorizontalLines',
            }, {
                table: {
                    body: [
                        ['Plant Site', 'Site of Inspection', 'State'], // Headers for Site Mappings
                        ...siteMappingsData // Add rows for each site mapping
                    ]
                },
                style: 'table',
                layout: 'lightHorizontalLines',
            });
        });

        // PDF structure
        const docDefinition = {
            content: [{
                    text: 'Inspector Details',
                    style: 'header'
                },
                {
                    text: 'Information',
                    style: 'subheader'
                },
                {
                    table: {
                        body: [
                            ['Name', name],
                            ['Gender', gender],
                            ['Date of Birth', dob],
                            ['Rank', rank],
                            ['Passport Number', passportNumber],
                            ['UNLP Number', unlpNumber],
                            ['Professional Experience', experience],
                            ['Qualifications', qualifications],
                            ['Remarks', remarks]
                        ]
                    },
                    style: 'table'
                },
                {
                    text: 'Inspection Details',
                    style: 'subheader'
                },
                {
                    table: {
                        body: [
                            ['Category', 'Status', 'Date of Joining'],
                            ...inspectionDetails
                        ]
                    },
                    style: 'table'
                },
                {
                    text: 'Visit Details',
                    style: 'subheader'
                },
                ...visitDetails // Add the visit details tables
            ],
            styles: {
                header: {
                    fontSize: 18,
                    bold: true
                },
                subheader: {
                    fontSize: 14,
                    bold: true,
                    margin: [0, 10, 0, 10]
                },
                table: {
                    margin: [0, 5, 0, 15]
                }
            },
            defaultStyle: {
                columnWidth: 'auto' // Ensures the content fits within table cells
            },
            pageSize: 'A4',
            pageMargins: [20, 60, 20, 40]
        };

        // Create and download PDF
        pdfMake.createPdf(docDefinition).download('Inspector_Report.pdf');
    }
</script>

@endpush