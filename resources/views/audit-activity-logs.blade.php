@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <!-- <h1>Audit Activity Logs</h1> -->

    @if($activityLogs->isEmpty())
    <p>No audit trails found.</p>
    @else
    @endif






    <div class="center-block expand-box text-white mb-3">
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
                            <form method="POST" action="{{ route('activityLogs.post') }}"
                                class="w-100">

                                @csrf

                                <div class="row ml-3 mb-3 pt-3 justify-content-between">

                                    <!-- Date Filter on the Left -->
                                    <div class="col-md-5 d-flex">
                                        <p class="text-dark mt-2 mr-3"><strong>Date : </strong></p>
                                        <div class="d-flex">
                                            <input type="text" name="startDate" id="startDate" class="form-control datepicker" placeholder="From" style="width: auto; margin-right: 10px;">
                                            <input type="text" name="endDate" id="endDate" class="form-control datepicker" placeholder="To" style="width: auto;">
                                        </div>
                                    </div>

                                    <div class="col-md-5 d-flex">
                                        <p class="text-dark mt-2 mr-3"><strong>Action Type : </strong></p>
                                        <div class="d-flex">

                                            <select name="action_type" id="action_type" class="form-control" style="width: auto;">
                                                <option value="all" {{ request('action_type') == 'all' ? 'selected' : '' }}>All</option>
                                                <option value="insert" {{ request('action_type') == 'insert' ? 'selected' : '' }}>Insert</option>
                                                <option value="update" {{ request('action_type') == 'update' ? 'selected' : '' }}>Update</option>
                                                <option value="delete" {{ request('action_type') == 'delete' ? 'selected' : '' }}>Delete</option>
                                                <option value="restore" {{ request('action_type') == 'restore' ? 'selected' : '' }}>Restore</option>
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


    <table class="table table-bordered table-striped myDataTable" data-exports-column="0,1,2,3,4,5,6">
  

            <select id="activityPageLengthSelect" class="form-control "  >
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="all">All</option>
            </select>
  
        <thead class="thead-dark">
            <tr>
                <th scope="col" class="text-center">Sl. No.</th>
                <th scope="col">user_id</th>
                <th scope="col">action_type</th>
                <th scope="col">ip_addr</th>
                <th scope="col">affected_table</th>
                <th scope="col">record_id</th>
                <th scope="col">created_at</th>
                <th scope="col">changes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activityLogs as $index => $audit)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $audit->user_id }}</td>
                <td>{{ $audit->action_type }}</td>
                <td>{{ $audit->ip_addr }}</td>
                <td>{{ $audit->affected_table }}</td>
                <td>{{ $audit->record_id }}</td>
                <td>{{ \Carbon\Carbon::parse($audit->created_at)->format('d-m-Y h:i:s A') }}</td>
                <td>
                    <div class="log-changes">
                        <button type="button" class="btn btn-primary btn-sm view-changes" data-id="{{ $audit->id }}" data-changes="{{ $audit->changes }}" data-toggle="modal" data-target="#changesModal">
                            <i class="fas fa-eye"></i>&nbsp;View Changes
                        </button>
                    </div>
                </td>
                @endforeach
        </tbody>
    </table>

    <!-- Modal -->
    <div class="modal fade" id="changesModal" tabindex="-1" role="dialog" aria-labelledby="changesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changesModalLabel">Audit Log Changes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <pre id="changesContent" style="white-space: pre-wrap;"></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection
@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const changesModal = $('#changesModal');
        const changesContent = $('#changesContent');

        $('.view-changes').on('click', function() {

            const changesData = $(this).data('changes');
            const readableChangesData = JSON.stringify(changesData, null, 2);


            try {
                // Parse and format JSON
                const formattedJson = JSON.stringify(JSON.parse(readableChangesData), null, 4);
                changesContent.text(formattedJson);
            } catch (error) {
                // If parsing fails, display as plain text
                changesContent.text(readableChangesData);
            }
        });
    });







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
            buttons: [{
                    extend: 'copyHtml5',
                    text: '<i class="fa-solid fa-copy"></i>',
                    titleAttr: 'Copy to clipboard',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa-solid fa-file-excel"></i>',
                    titleAttr: 'Export to Excel',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa-solid fa-file-pdf"></i>',
                    titleAttr: 'Export to PDF',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                }
            ],
            responsive: true,
            autoWidth: true,
        });

        $('#activityPageLengthSelect').on('change', function() {
            const newLength = $(this).val() === "all" ? -1 : parseInt($(this).val());
            reportTable.page.len(newLength).draw(false);
        });

       
        $('form').on('submit', function(e) {
            e.preventDefault(); 
            var formData = $(this).serialize(); 
            window.location.search = formData;
        });
    });
</script>
@endpush