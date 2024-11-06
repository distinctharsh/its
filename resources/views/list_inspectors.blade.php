@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4 table-responsive">
    <h1>General Query Report</h1>
    <table class="display table table-bordered table-striped" id="myTable" data-export-columns="0,1,2,3,4,5">
        <div class="mb-3 d-flex" style="float: right;">
            <select id="pageLengthSelect" class="form-control ml-3" style="width: 80px;">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="all">All</option>
            </select>
        </div>

        <thead class="thead-dark">
            <tr>
                <th scope="col">S.No.</th>
                <th scope="col">Name</th>
                <th scope="col">Gender</th>
                <th scope="col">Date of Birth</th>
                <th scope="col">Rank</th>
                <th scope="col">Professional Experience</th>
                <th scope="col">Details</th>
            </tr>
        </thead>
        <tbody id="inspectorListTableBody">
            @if($inspectorsData->isEmpty())
            <tr>
                <td colspan="7" class="text-center">No inspectors available.</td>
            </tr>
            @else
            @foreach($inspectorsData as $index => $inspector)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $inspector['name'] }}</td>
                <td>{{ $inspector['gender'] }}</td>
                <td>{{ $inspector['dob'] }}</td>
                <td>{{ $inspector['rank'] }}</td>
                <td>{{ $inspector['professional_experience'] }}</td>
                <td>
                    <!-- Safely pass JSON-encoded data -->
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
                <h5 class="modal-title" id="detailsModalLabel">Inspector Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6>Personal Information</h6>
                <p><strong>Name:</strong> <span id="modalName"></span></p>
                <p><strong>Passport Number:</strong> <span id="modalPassportNumber"></span></p>
                <p><strong>UNLP Number:</strong> <span id="modalUNLPNumber"></span></p>
                <p><strong>Qualifications:</strong> <span id="modalQualifications"></span></p>
                <p><strong>Remarks:</strong> <span id="modalRemarks"></span></p>

                <hr>
                
                <h6>Inspection Details</h6>
                <div id="inspectionDetails"></div>
                
                <hr>

                <h6>Visit Details</h6>
                <div id="visitDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    function showDetails(inspector) {
        // Set modal title and basic info
        $('#modalName').text(inspector.name || 'N/A');
        $('#modalPassportNumber').text(inspector.passport_number || 'N/A');
        $('#modalUNLPNumber').text(inspector.unlp_number || 'N/A');
        $('#modalQualifications').text(inspector.qualifications || 'N/A');
        $('#modalRemarks').text(inspector.remarks || 'N/A');

        // Generate inspection details
        let inspectionHtml = '';
        if (inspector.inspections && inspector.inspections.length > 0) {
            inspector.inspections.forEach(inspection => {
                inspectionHtml += `
                    <p><strong>Category:</strong> ${inspection.category?.category_name || 'N/A'}</p>
                    <p><strong>Date of Joining:</strong> ${inspection.date_of_joining || 'N/A'}</p>
                    <p><strong>Status:</strong> ${inspection.status?.status_name || 'N/A'}</p>
                    <p><strong>Remarks:</strong> ${inspection.remarks || 'N/A'}</p>
                    <hr>`;
            });
        } else {
            inspectionHtml = '<p>No inspection data available.</p>';
        }
        $('#inspectionDetails').html(inspectionHtml);

        // Generate visit details
        let visitHtml = '';
        if (inspector.visits && inspector.visits.length > 0) {
            inspector.visits.forEach(visit => {
                visitHtml += `
                    <p><strong>Purpose of Visit:</strong> ${visit.purpose_of_visit || 'N/A'}</p>
                    <p><strong>Point of Entry:</strong> ${visit.point_of_entry || 'N/A'}</p>
                    <p><strong>Arrival Datetime:</strong> ${visit.arrival_datetime || 'N/A'}</p>
                    <p><strong>Departure Datetime:</strong> ${visit.departure_datetime || 'N/A'}</p>
                    <p><strong>Remarks:</strong> ${visit.remarks || 'N/A'}</p>
                    <hr>`;
            });
        } else {
            visitHtml = '<p>No visit data available.</p>';
        }
        $('#visitDetails').html(visitHtml);

        // Show the modal
        $('#detailsModal').modal('show');
    }
</script>
@endpush
