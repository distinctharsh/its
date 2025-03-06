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
        <div class="panel-group back-cyan2" id="accordion" role="tablist" aria-multiselectable="true">
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

                                @if(request('filter_type') != '2')
                                    <div class="col-md-4">
                                        <!-- Escort Officer Filter -->
                                        <div class="mb-3">
                                            <label for="escortOfficerPoE">Escort Officers (PoE)</label><br>
                                            
                                            <select name="escortOfficerPoE[]" id="escort_officers_poe" class="form-control" multiple>
                                                {!! $escortOfficersPoEDropdown !!}
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <!-- Escort Officer Filter -->
                                        <div class="mb-3">
                                            <label for="escortOfficer">Escort Officers (Inspection Site)</label><br>
                                            
                                            <select name="escortOfficer[]" id="escortOfficer" class="form-control" multiple>
                                                {!! $escortOfficersDropdown !!}
                                            </select>
                                        </div>
                                    </div>

                                    @endif


                                    <div class="col-md-4">
                                        <!-- Rank Filter -->
                                        <div class="mb-3">
                                            <label for="rank">Rank</label><br>
                                            <select name="rank[]" id="rank" class="form-control" multiple>
                                                <option value="">Select Rank</option>
                                                {!! $rankDropdown !!}
                                            </select>
                                        </div>

                                     
                                    </div>
                                    @if(request('filter_type') != '2') 
                                </div>
                                <!-- Second Row -->
                                <div class="row mr-3">
                                    @endif

                                    <div class="col-md-4">
                                        <!-- Nationality Filter -->
                                        <div class="mb-3">
                                            <label for="country">Nationality</label>
                                            <select name="country[]" id="country" class="form-control" multiple>
                                                <option value="">Select Nationality</option>
                                                {!! $nationalityDropdown !!}
                                            </select>
                                        </div>
                                    </div>

                                    @if(request('filter_type') != '2') 

                                    <div class="col-md-4">
                                        <!-- State Filter -->
                                        <div class="mb-3">
                                            <label for="state">State</label>
                                            <select name="state[]" id="state" class="form-control" multiple>
                                                <option value="">Select State</option>
                                                {!! $stateDropdown !!}
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <!-- Site Code Filter -->
                                        <div class="mb-3">
                                            <label for="siteCode">Plant Site</label><br>
                                            <select name="siteCode[]" id="siteCode" class="form-control" multiple>
                                                <option value="">Select Plant Site</option>
                                                {!! $siteCodeDropdown !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if(request('filter_type') != '2') 
                                <div class="row mr-3">

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="inspectionTypeSelection">Inspection Type</label>
                                            <select name="inspectionTypeSelection[]" id="inspectionTypeSelection" class="form-control" multiple>
                                                <option value="">Select Inspection Type</option>
                                                {!! $inspectionTypeDropdown !!}
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <!-- Inspection Sub Category Type Filter -->
                                        <div class="mb-3">
                                            <label for="inspectionCategoryType">Sub Category Type</label><br>
                                            <select name="inspectionCategoryType[]" id="inspectionCategoryType" class="form-control" multiple>
                                                <option value="">Select Sub Category Type</option>
                                                {!! $subCategoryTypeDropdown !!}
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <!-- Type of Inspection Filter -->
                                        <div class="mb-3">
                                            <label for="typeOfInspection">Inspection Category</label>
                                            <select name="typeOfInspection[]" id="typeOfInspection" class="form-control" multiple>
                                                <option value="">Select Inspection Category</option>
                                                {!! $inspectionCategoryDropdown !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if(request('filter_type') != '2') 
                                <div class="row mr-3">
                                    <div class="col-md-4">
                                        <!-- Status Filter -->
                                        <div class="mb-3">
                                            <label for="status">Status</label>
                                            <select name="status[]" id="status" class="form-control" multiple>
                                                <option value="">Select status</option>
                                                {!! $statusDropdown !!}
                                            </select>
                                        </div>
                                    </div>
                                   
                                    <div class="col-md-4">
                                        <!-- Date of Arrival Filter -->
                                        <div class="mb-3">
                                            <label for="dateOfArrival">Date of Arrival (From - To)</label>
                                            {{-- <input type="date" name="dateOfArrival" id="dateOfArrival" class="form-control" value="{{ old('dateOfArrival', $dateOfArrival ? \Carbon\Carbon::parse($dateOfArrival)->format('Y-m-d') : '') }}"> --}}
                                            <div class="d-flex flex-column flex-md-row">
                                        
                                                <input type="date" name="dateOfArrivalFrom" id="dateOfArrivalFrom" class="form-control " value="{{ old('dateOfArrivalFrom', $dateOfArrivalFrom ? \Carbon\Carbon::parse($dateOfArrivalFrom)->format('Y-m-d') : '') }}" placeholder="From">
                                                
                                                <!-- To Date Input -->
                                                <input type="date" name="dateOfArrivalTo" id="dateOfArrivalTo" class="form-control" value="{{ old('dateOfArrivalTo', $dateOfArrivalTo ? \Carbon\Carbon::parse($dateOfArrivalTo)->format('Y-m-d') : '') }}" placeholder="To">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 ">
                                        <!-- Date of Departure Filter -->
                                        <div class="mb-3">
                                            <label for="dateOfDeparture">Date of Departure</label>
                                            
                                            {{-- <input type="date" name="dateOfDeparture" id="dateOfDeparture" class="form-control" value="{{ old('dateOfDeparture', $dateOfDeparture ? \Carbon\Carbon::parse($dateOfDeparture)->format('Y-m-d') : '') }}"> --}}

                                            <div class="d-flex flex-column flex-md-row">
                                                <input type="date" name="dateOfDepartureFrom" id="dateOfDepartureFrom" class="form-control " value="{{ old('dateOfDepartureFrom', $dateOfDepartureFrom ? \Carbon\Carbon::parse($dateOfDepartureFrom)->format('Y-m-d') : '') }}" placeholder="From">
                                                <input type="date" name="dateOfDepartureTo" id="dateOfDepartureTo" class="form-control" value="{{ old('dateOfDepartureTo', $dateOfDepartureTo ? \Carbon\Carbon::parse($dateOfDepartureTo)->format('Y-m-d') : '') }}" placeholder="To">
                                            </div>
                                        
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Fourth Row -->
                                @if(request('filter_type') != '2') 
                                <div class="row mr-3">
                                    @endif
                                @if(request('filter_type') != '2') 
                                    <div class="col-md-4">
                                        <!-- Visit Category Filter -->
                                        <div class="mb-3">
                                            <label for="visitCategory">Visit Category Type</label><br>
                                            <select name="visitCategory[]" id="visitCategory" class="form-control" multiple>
                                                <option value="">Select Visit Category</option>
                                                {!! $visitCategoryDropdown !!}
                                            </select>
                                        </div>
                                    </div>
                                    @endif



                                    
                                    <div class="col-md-4">
                                        <!-- Designation Filter -->
                                        <div class="mb-3">
                                                <label for="designation">Designation</label><br>
                                                <select name="designation[]" id="designation" class="form-control" multiple>
                                                    <option value="">Select Designation</option>
                                                    {!! $designationDropdown !!}
                                                </select>
                                            </div>
                                    </div>
                                    @if(request('filter_type') != '2') 
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="issue">Inspection Issues</label>
                                            <select name="issue[]" id="issue" class="form-control" multiple>
                                                <option value="">Select Issue</option>
                                                {!! $issueDropdown !!}
                                            </select>
                                        </div>
                                    </div>
                                    @endif


                                    @if(request('filter_type') == '2') 
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="dateOfArrival">OPCW Communication Date (From - To)</label>
                                                <div class="d-flex flex-column flex-md-row">
                                                    <input type="date" name="opcwCommunicationFrom" id="opcwCommunicationFrom" class="form-control " value="{{ old('opcwCommunicationFrom', $opcwCommunicationFrom ? \Carbon\Carbon::parse($opcwCommunicationFrom)->format('Y-m-d') : '') }}" placeholder="From">
                                                    <input type="date" name="opcwCommunicationTo" id="opcwCommunicationTo" class="form-control" value="{{ old('opcwCommunicationTo', $opcwCommunicationTo ? \Carbon\Carbon::parse($opcwCommunicationTo)->format('Y-m-d') : '') }}" placeholder="To">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="dateOfArrival">Date of deletion (From - To)</label>
                                                <div class="d-flex flex-column flex-md-row">
                                                    <input type="date" name="opcwDeletionFrom" id="opcwDeletionFrom" class="form-control " value="{{ old('opcwDeletionFrom', $opcwDeletionFrom ? \Carbon\Carbon::parse($opcwDeletionFrom)->format('Y-m-d') : '') }}" placeholder="From">
                                                    <input type="date" name="opcwDeletionTo" id="opcwDeletionTo" class="form-control" value="{{ old('opcwDeletionTo', $opcwDeletionTo ? \Carbon\Carbon::parse($opcwDeletionTo)->format('Y-m-d') : '') }}" placeholder="To">
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="filter_type">Filter Type</label><br>
                                            <select name="filter_type" id="filter_type" class="form-control">
                                                <option value="">Select Filter Type</option>
                                              
                                                <option value="0" {{  $filter_type == 0 ? 'selected' : '' }} >Inspector</option>
                                                <option value="1" {{  $filter_type == 1 ? 'selected' : '' }} >Inspection</option>
                                                <option value="2" {{  $filter_type == 2 ? 'selected' : '' }} >OPCW Other Staff</option>
                                                
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
  
    <table class="display table table-bordered table-striped myDataTable" data-exports-column="{{ request('filter_type') == '0' ? '0,1,2,3,4,5, 6' : (request('filter_type') == '2' ? '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14, 15, 16' : '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16') }}">
        <select id="reportPageLengthSelect" class="form-control "  >
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="all">All</option>
        </select>
      
        @if(request()->headers->get('referer'))
            
            <div class="dt-buttons flex-wrap" style="width: 100px;">
                <a href="{{ url()->previous() }}" title="Back">
                    <span>
                        <i class="fa fa-arrow-circle-left" style="font-size:24px"></i>
                    </span>
                </a> 
            </div> 
        @endif


@php
    $filter_type = request('filter_type', '1');  // Default to '1' if not provided
@endphp

        <thead >
            @if(request('filter_type') == '0')
                <tr class="back-cyan">
                    <th scope="col" class="text-center">Sl. No.</th>
                    <th scope="col" class="text-center">Inspector Name</th>
                    <th scope="col" class="text-center">No. of Times Inspector visit India</th>               
                    <th scope="col" class="text-center">Gender</th>
                    <th scope="col" class="text-center">Date of Birth</th>
                    <th scope="col" class="text-center">Rank</th>
                    <th scope="col" class="text-center">Professional Experience</th>
                    <th scope="col" class="text-center">Details</th>
                
                </tr>

            @elseif(request('filter_type') == '2')
                <tr class="back-cyan">
                    <th scope="col" class="text-center">Sl. No.</th>
                    <th scope="col">Name</th>
                    <th scope="col">Gender</th>
                    <th scope="col">Date Of Birth</th>
                    <th scope="col">Place Of Birth</th>
                    <th scope="col" class="text-center">Nationality</th>
                    <th scope="col" class="text-center">UNLP No.</th>
                    <th scope="col" class="text-center">Passport No.</th>
                    <th scope="col" class="text-center">Designation</th>
                    <th scope="col" class="text-center">Rank</th>
                    <th scope="col" class="text-center">Qualification</th>
                    <th scope="col" class="text-center">Professional Experience</th>
                    <th scope="col" class="text-center">Scope Of Access</th>
                    <th scope="col" class="text-center">Security Status</th>
                    <th scope="col" class="text-center">OPCW Communication Date</th>
                    <th scope="col" class="text-center">Deletion Date</th>
                    <th scope="col" class="text-center">Remarks</th>
                </tr>
            @else
                <tr class="back-cyan">
                    @if($results->isEmpty())
                        <th scope="col">Record</th>
                    @else
                        <th scope="col" class="text-center">Sl. No.</th>
                        <th scope="col" class="text-center">Document Number</th>
                        <th scope="col" class="text-center">Receipt Date</th>
                        <th scope="col" class="text-center">Inspection Type</th>
                        <th scope="col" class="text-center">Inspection Category</th>
                        <th scope="col" class="text-center">Inspection Phase</th>
                        <th scope="col" class="text-center">Visit Category</th>
                        <th scope="col" class="text-center">Inspection Sub Category </th>
                        <th scope="col" class="text-center">Name and Address of Site of Inspection & State</th>
                        <th scope="col" class="text-center">Team Lead Name</th>
                        <th scope="col" class="text-center">List of Inspectors</th>
                        <th scope="col" class="text-center">Point of Entry</th>
                        <th scope="col" class="text-center">Arrival Date & Time</th>
                        <th scope="col" class="text-center">Escort Officers (PoE)</th>
                        <th scope="col" class="text-center">Point of Exit</th>
                        <th scope="col" class="text-center">Departure Date & Time</th>
                        <th scope="col" class="text-center">Escort Officers (Inspection Site)</th>
                        <th scope="col" class="text-center">Inspection Issues</th>
                        <th scope="col" class="text-center">Action Taken Report</th>
                        <th scope="col" class="text-center">Remarks</th>
                    @endif
                </tr>       
            @endif

        </thead>
        @if(request('filter_type') == '0')
            @php
                $inspectorsData = collect($inspectorsData); 
            @endphp
        @elseif(request('filter_type') == '2')
            @php
                $otherStaffData = collect($otherStaffData); 
            @endphp

          
        @else
            @php
                $results = collect($results);
            @endphp
        @endif

   
        <tbody id="inspectorListTableBody">
            @if(request('filter_type') == '0')
                @if(empty($inspectorsData))
                    <tr>
                        <td colspan="7" class="text-center">No inspectors available.</td>
                    </tr>
                @else
                    @foreach($inspectorsData as $index => $inspector)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $inspector['name'] ? $inspector['name'] : '' }}</td>
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
            @elseif(request('filter_type') == '2')
                @foreach($otherStaffData as $index => $staff)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $staff['name'] ? $staff['name'] : '' }}</td>
                        <td>{{ $staff['gender'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($staff['dob'])->format('d-m-Y') }}</td>
                        <td>{{ $staff['place_of_birth'] }}</td>
                        <td>{{ $staff['nationality']['country_name'] }}</td>
                        <td>{{ $staff['unlp_number'] }}</td>
                        <td>{{ $staff['passport_number'] }}</td>
                        <td>{{ $staff['designation'] }}</td>
                        <td>{{ $staff['rank'] }}</td>
                        <td>{{ $staff['qualifications'] }}</td>
                        <td>{{ $staff['professional_experience'] }}</td>
                        <td>{{ $staff['scope_of_access'] }}</td>
                        <td>{{ $staff['security_status'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($staff['opcw_communication_date'])->format('d-m-Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($staff['deletion_date'])->format('d-m-Y') }}</td>
                        <td>{{ $staff['remarks'] }}</td>
                    </tr>
                @endforeach
            @else
                @if($results->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center">No Record Found</td>
                    </tr>
                @else
                    @php
                        $sno = 1;        
                    @endphp

                    @foreach($results as $row)
                    <tr data-status="{{ is_null($row->v_deleted_at) ? 'active' : 'inactive' }}" data-join-date="{{ $row->arrival_datetime ? date('Y-m-d') : '' }}">

                        <td class="text-center">
                            {{ $sno }}
                        </td>

                        <td class="text-center">
                            {{ $row->fax_number ?? '' }}
                        </td>

                        <td class="text-center">
                            {{ $row->receipt_date ? \Carbon\Carbon::parse($row->receipt_date)->format('d M Y') : '' }}
                        </td>

                        
                        <td class="text-center">
                            {{ $row->ip_name ?? '' }}
                        </td>

                        <td class="text-center">
                            {{ $row->it_name ?? '' }}
                        </td>

                        <td class="text-center">
                            {{ $row->inspection_phase_name ?? '' }} 
                        </td>

                        <td class="text-center">
                            {{ $row->vc_name ?? '' }}
                        </td>

                        <td class="text-center">
                            {{ $row->ict_name ?? '' }}
                        </td>

                        <td class="text-center">
                            {{ $row->sc_name ?? '' }}
                        </td>

                        <td class="text-center">
                            {{ $row->tl_name ?? '' }}
                        </td>
                        <td class="text-center">
                            {{ mapValues($row->list_of_inspectors, $inspectors, 'Unknown Inspector') }}
                        </td>
                        <td class="text-center">
                            {{ mapValues($row->point_of_entry, $entry_exit_points, 'Unknown State') }}
                        </td>
                        <td class="text-center">
                            {{ $row->arrival_datetime ? formatDate($row->arrival_datetime) : '' }}
                        </td>

                        <td class="text-center">
                            {{ mapValues($row->escort_officers_poe, $escort_officers, 'Unknown Officer') }}
                        </td>
                        <td class="text-center">
                            {{ mapValues($row->point_of_exit, $entry_exit_points, 'Unknown State') }}
                        </td>
                        <td class="text-center">
                            {{ $row->departure_datetime ? formatDate($row->departure_datetime) : '' }}
                        </td>

                        <td class="text-center">
                            {{ mapValues($row->list_of_escort_officers, $escort_officers, 'Unknown Officer') }}
                        </td>
                        <td class="text-center">
                            {{ $row->inspection_issue_name ?? '' }}
                        </td>
                        <td class="text-center">
                            {{ $row->acentric_report ?? '' }}
                        </td>

                    




                        <!-- <td class="text-center">
                            {{ $row->clearance_certificate ?? '' }}
                        </td> -->


                        <!-- <td class="text-center">
                            {{ $row->list_of_inspectors ?? '' }}
                        </td> -->



                        



                        <td class="text-center">
                            {{ $row->remarks ?? '' }}
                        </td>
                    </tr>
                        @php
                            $sno++;                
                        @endphp
                    @endforeach
                @endif
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
                        <p><strong>Nationality:</strong> <span id="modalNationality"></span></p>
                        <p><strong>Designation:</strong> <span id="modalDesignation"></span></p>
                        <p><strong>Rank:</strong> <span id="modalRank"></span></p>
                        <p><strong>IB Status:</strong> <span id="modalIbStatus"></span></p>
                        <p><strong>RAW Status:</strong> <span id="modalRAWStatus"></span></p>
                        <p><strong>MEA Status:</strong> <span id="modalMEAStatus"></span></p>

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
                        <h5 class="card-title">Inspection Profile</h5>
                    </div>
                    <div class="card-body">
                        <div id="inspectionDetails"></div>
                    </div>
                </div>

                <!-- Visit Details Card -->
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title">Inspection Details</h5>
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
                        columns: columnsToExports,
                        format: {
                            body: function(data, row, column) {
                                return processDataForExport(data, row, column);
                            }
                        }
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa-solid fa-file-excel"></i>',
                    titleAttr: 'Export to Excel',
                    exportOptions: {
                        columns: columnsToExports,
                        format: {
                            body: function(data, row, column) {
                                return processDataForExport(data, row, column);
                            }
                        }
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa-solid fa-file-pdf"></i>',
                    titleAttr: 'Export to PDF',
                    pageSize: 'A0',
                    exportOptions: {
                        columns: columnsToExports,
                        format: {
                            body: function(data, row, column) {
                                return processDataForExport(data, row, column);
                            }
                        }
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
                        columns: columnsToExports,
                        format: {
                            body: function(data, row, column) {
                                return processDataForExport(data, row, column);
                            }
                        }
                    },
                    
                },{
                    extend: 'colvis',
                text: '<i class="fa-solid fa-eye"></i>',
                titleAttr: 'Show/Hide Columns'
                }
            ],
            responsive: false,
            autoWidth: true,
        });

        function processDataForExport(data, row, column) {
            if (column === 0) {
                return row + 1; 
            }

            let tempDiv = document.createElement('div');
            tempDiv.innerHTML = data;
            let cleanData = tempDiv.textContent || tempDiv.innerText || '';
            cleanData = cleanData.replace(/<a[^>]*>(.*?)<\/a>/g, '$1');  
            cleanData = cleanData.replace(/<i[^>]*>(.*?)<\/i>/g, '$1'); 
            return cleanData.replace(/\s+/g, ' ').trim();
        }

        reportTable.on('column-visibility.dt', function(e, settings, column, state) {
            const visibleColumns = reportTable.columns(':visible').indexes();
            columnsToExports.length = 0;
            visibleColumns.each(function(index) {
                columnsToExports.push(index); 
            });
        });

        $('#reportPageLengthSelect').on('change', function() {
            const newLength = $(this).val() === "all" ? -1 : parseInt($(this).val());
            reportTable.page.len(newLength).draw(false);
        });
        reportTable.draw(); 
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

    @if(request('filter_type') == '0')

        // Pass the escortOfficers data to JavaScript
        const escortOfficersData = @json($escortOfficers);
        const entryExitPointsData = @json($entryExitPoints);
        const opcwDocumentData = @json($opcwDocuments);
        const allInspectors = @json($allInspectors);
        const inspectionCategories = @json($inspectionCategories);
        const allVisitCategory = @json($allVisitCategory);
        const typesOfInspection = @json($typesOfInspection);

        const siteCodes = @json($siteCodes); // Pass site codes array
        const allStates = @json($allStates);
        const allStatus = @json($allStatus);

        function showDetails(inspector) {
            // Personal info setup
            $('#modalName').text(inspector.name || 'N/A');
            $('#modalGender').text(inspector.gender || 'N/A');
            $('#modalRank').text(inspector.rank || 'N/A');
            $('#modalProfessionalExperience').text(inspector.professional_experience || 'N/A');
            $('#modalDOB').text(inspector.dob);
            $('#modalNationality').text(inspector.nationality.country_name || 'N/A');
            $('#modalDesignation').text(inspector.designation || 'N/A');
            $('#modalPassportNumber').text(inspector.passport_number || 'N/A');
            $('#modalUNLPNumber').text(inspector.unlp_number || 'N/A');
            $('#modalQualifications').text(inspector.qualifications || 'N/A');
            $('#modalRemarks').text(inspector.remarks || 'N/A');
            $('#modalIbStatus').text(inspector.ibStatus || 'N/A');
            $('#modalRAWStatus').text(inspector.rawStatus || 'N/A');
            $('#modalMEAStatus').text(inspector.meaStatus || 'N/A');

            // Populate inspection details
            let inspectionHtml = '';
            if (inspector.inspections && inspector.inspections.length > 0) {
				let inspectionNumber = 1; // Start the numbering from 1
                inspector.inspections.forEach(inspection => {

                    const category = inspectionCategories.find(c => c.id == inspection.category_id)?.category_name || 'N/A';
                   

				
                    inspectionHtml += `
                        <div class="inspection">
                            <div class="inspection-number"><strong>Inspection #:</strong> ${inspectionNumber}</div><br>
                            <div class="category"><strong>Category:</strong> ${category}</div><br>
                            <div class="date-of-joining"><strong>Communication Date:</strong> ${formatDate(inspection.date_of_joining) || 'N/A'}</div><br>
                            <div class="date-of-joining"><strong>Deletion Date:</strong> ${formatDate(inspection.deletion_date) || 'N/A'}</div><br>
                           
                            <div class="status"><strong>Remarks:</strong> ${inspection.remarks}</div>
                        </div><hr>`;
                    inspectionNumber++; // Increment the inspection number
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


                    let escortOfficersPoe = 'N/A'; // Default value
                    if (visit.escort_officers_poe) {
                        try {
                            const escortOfficersArray = JSON.parse(visit.escort_officers_poe);
                            const uniqueEscortOfficers = [...new Set(escortOfficersArray)];
                            escortOfficersPoe = uniqueEscortOfficers.map(id => escortOfficersData[id] || 'Unknown Officer').join(', ');
                        } catch (e) {
                            console.error('Error parsing escort officers:', e);
                        }
                    }

                    let pointOfEntry = 'N/A'; // Default value
                    if (visit.point_of_entry) {
                        try {
                            const pointOfEntryArray = JSON.parse(visit.point_of_entry);
                            const uniquePointOfEntry = [...new Set(pointOfEntryArray)];
                            pointOfEntry = uniquePointOfEntry.map(id => entryExitPointsData[id] || 'Unknown entry').join(', ');
                        } catch (e) {
                            console.error('Error parsing point of entry:', e);
                        }
                    }

                    let pointOfExit = 'N/A'; // Default value
                    if (visit.point_of_exit) {
                        try {
                            // If the value is a number, treat it as a single ID
                            if (typeof visit.point_of_exit === 'number') {
                                pointOfExit = entryExitPointsData[visit.point_of_exit] || 'Unknown exit';
                            } else {
                                // If it's a string, try parsing it as a JSON array
                                const pointOfExitValue = Array.isArray(visit.point_of_exit) 
                                    ? visit.point_of_exit 
                                    : (typeof visit.point_of_exit === 'string' ? JSON.parse(visit.point_of_exit) : [visit.point_of_exit]);

                                // Ensure the value is an array
                                if (Array.isArray(pointOfExitValue)) {
                                    const uniquePointOfExit = [...new Set(pointOfExitValue)];
                                    pointOfExit = uniquePointOfExit.map(id => entryExitPointsData[id] || 'Unknown exit').join(', ');
                                } else {
                                    pointOfExit = 'N/A';
                                }
                            }
                        } catch (e) {
                            console.error('Error parsing point of exit:', e);
                        }
                    }



                    let documentNumber = 'N/A'; // Default value
                    if (visit.opcw_document_id) {
                        try {
                            // If the value is a number, treat it as a single ID
                            if (typeof visit.opcw_document_id === 'number') {
                                documentNumber = opcwDocumentData[visit.opcw_document_id] || 'Unknown document';
                            } else {
                                // If it's a string, try parsing it as a JSON array
                                const documentArray = Array.isArray(visit.opcw_document_id) 
                                    ? visit.opcw_document_id 
                                    : (typeof visit.opcw_document_id === 'string' ? JSON.parse(visit.opcw_document_id) : [visit.opcw_document_id]);

                                // Ensure the value is an array
                                if (Array.isArray(documentArray)) {
                                    const uniqueDocument = [...new Set(documentArray)];
                                    documentNumber = uniqueDocument.map(id => opcwDocumentData[id] || 'Unknown document').join(', ');
                                } else {
                                    documentNumber = 'N/A';
                                }
                            }
                        } catch (e) {
                            console.error('Error parsing document number:', e);
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
                    <div class="escort-officers"><strong>Escort Officers (Inspection Site):</strong> ${escortOfficers}</div><br>
                    <div class="escort-officers"><strong>Escort Officers (POE):</strong> ${escortOfficersPoe}</div><br>
                    <div class="inspectors"><strong>List of Inspectors:</strong> ${inspectors}</div><br>
                    <div class="inspection-category">
                    <strong>Inspection Type:</strong>  
                    ${visit.inspection_type_selection 
                        ? visit.inspection_type_selection === 'routine' 
                        ? 'Article VI Inspection' 
                        : capitalizeFirstLetter(visit.inspection_type_selection) 
                        : 'N/A'}
                    </div><br>
                    <div class="visit-category"><strong>Visit Category:</strong> ${visitCategory}</div><br>
                    <div class="inspection-type"><strong>Inspection Category:</strong> ${inspectionType}</div><br>
                    <div class="arrival"><strong>Arrival:</strong> ${formatDate(visit.arrival_datetime) || 'N/A'}</div><br>
                    <div class="departure"><strong>Departure:</strong> ${formatDate(visit.departure_datetime) || 'N/A'}</div><br>
                    <div class="point-of-entry"><strong>Point of Entry:</strong> ${(pointOfEntry) || 'N/A'}</div><br>
                    <div class="point-of-exit"><strong>Point of Exit:</strong> ${(pointOfExit) || 'N/A'}</div><br>
                    <div class="document-number"><strong>Document Number:</strong> ${(documentNumber) || 'N/A'}</div>
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

    @endif


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
        allData.push(['IB Status:', $('#modalIbStatus').text()]);
        allData.push(['RAW Status:', $('#modalRAWStatus').text()]);
        allData.push(['MEA Status:', $('#modalMEAStatus').text()]);
        allData.push(['']); // Empty row to separate sections

        // Inspection Details Section
        allData.push(['Inspection Details']); // Heading
        allData.push(['Category', 'Date of Joining']); // Column headers

        // Get the inspection data from the modal (if any)
        $('#inspectionDetails .inspection').each(function() {
            var category = $(this).find('.category').text().replace('Category:', '').trim() || 'N/A';
        
            var dateOfJoining = $(this).find('.date-of-joining').text().replace('Date of Joining:', '').trim() || 'N/A';
            allData.push([category, dateOfJoining]);
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
        var remarks = $('#modalIbStatus').text();
        var remarks = $('#modalRAWStatus').text();
        var remarks = $('#modalMEAStatus').text();

        // Get inspection details dynamically
        var inspectionDetails = [];
        $('#inspectionDetails .inspection').each(function() {
            var category = $(this).find('.category').text().replace('Category:', '').trim() || 'N/A';
           
            var dateOfJoining = $(this).find('.date-of-joining').text().replace('Date of Joining:', '').trim() || 'N/A';
            inspectionDetails.push([category,  dateOfJoining]);
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
                            ['Category',  'Date of Joining'],
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
