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
                    <button class="btn btn-info btn-sm" onclick='showDetails(this, "{{ $inspector['passport_number'] }}", "{{ $inspector['unlp_number'] }}", "{{ $inspector['qualifications'] }}", "{{ $inspector['remarks'] }}", @json($inspector['inspections'] ?? []), @json($inspector['visits'] ?? []))'>
                        View Details
                    </button>
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>

</div>
@endsection

@push('script')
<script>
    function showDetails(button, passportNumber, unlpNumber, qualifications, remarks, inspections, visits) {
        const row = $(button).closest('tr');
        const detailsRows = row.nextAll('.details-row'); // Get all existing details rows

        // If details rows exist, remove them
        if (detailsRows.length > 0) {
            detailsRows.remove();
            $(button).text('View Details');
            return; 
        }

        let detailsHtml = `
        <tr class="details-row">
            <td colspan="7">
                <div class="card card-body">
                    <h5>Additional Details</h5>
                    <table class="table table-bordered mt-2">
                        <thead>
                            <tr>
                                <th scope="col">Passport Number</th>
                                <th scope="col">UNLP Number</th>
                                <th scope="col">Qualifications</th>
                                <th scope="col">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>${passportNumber}</td>
                                <td>${unlpNumber}</td>
                                <td>${qualifications}</td>
                                <td>${remarks}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>`;

        // Add inspection details if available
        detailsHtml += `
        <tr class="details-row">
            <td colspan="7">
                <div class="card card-body">
                    <h5>Inspection Details</h5>
                    <table class="table table-bordered mt-2">
                        <thead>
                            <tr>
                             
                                <th scope="col">Category</th>
                                <th scope="col">Date of Joining</th>
                                <th scope="col">Status</th>
                                <th scope="col">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>`;

        if (inspections && inspections.length > 0) {
            inspections.forEach(inspection => {
                detailsHtml += `
                <tr>
                 
                    <td>${inspection.category.category_name || 'N/A'}</td>
                    <td>${inspection.date_of_joining || 'N/A'}</td>
                    <td>${inspection.status?.status_name || 'N/A'}</td>
                    <td>${inspection.remarks || 'N/A'}</td>
                </tr>`;
            });
        } else {
            detailsHtml += `
            <tr>
                <td colspan="5" class="text-center">No inspection data available</td>
            </tr>`;
        }

        detailsHtml += `</tbody></table></div></td></tr>`;

        // Add visit details if available
        detailsHtml += `
        <tr class="details-row">
            <td colspan="7">
                <div class="card card-body">
                    <h5>Visit Details</h5>
                    <table class="table table-bordered mt-2">
                        <thead>
                            <tr>
                               
                                <th scope="col">Purpose of Visit</th>
                                <th scope="col">Point of Entry</th>
                                <th scope="col">Arrival Datetime</th>
                                <th scope="col">Departure Datetime</th>
                                <th scope="col">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>`;

        if (visits && visits.length > 0) {
            visits.forEach(visit => {
                detailsHtml += `
                <tr>
                 
                    <td>${visit.purpose_of_visit || 'N/A'}</td>
                    <td>${visit.point_of_entry || 'N/A'}</td>
                    <td>${visit.arrival_datetime || 'N/A'}</td>
                    <td>${visit.departure_datetime || 'N/A'}</td>
                    <td>${visit.remarks || 'N/A'}</td>
                </tr>`;
            });
        } else {
            detailsHtml += `
            <tr>
                <td colspan="6" class="text-center">No visit data available</td>
            </tr>`;
        }

        detailsHtml += `</tbody></table></div></td></tr>`;

        // Insert the details rows after the clicked row
        row.after(detailsHtml);
        $(button).text('Hide Details'); // Change button text
    }
</script>
@endpush
