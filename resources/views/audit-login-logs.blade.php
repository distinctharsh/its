@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">

    @if($auditTrails->isEmpty())
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
                            <form method="POST" action="{{ route('loginLogs.post') }}"
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

                                    <!-- Status Filter on the Right -->
                                    <div class="col-md-5 d-flex">
                                        <p class="text-dark mt-2"><strong>Status: </strong></p>
                                        <div class="d-flex justify-content-start align-items-center ml-3">
                                            <!-- Status Filter Radio Buttons with more space -->
                                            <div class="mr-4">
                                                <input type="radio" id="status_all" name="status" value="all" {{ request('status') == 'all' ? 'checked' : '' }}>
                                                <label for="status_all" class="ml-2">All</label>
                                            </div>
                                            <div class="mr-4">
                                                <input type="radio" id="status_success" name="status" value="success" {{ request('status') == 'success' ? 'checked' : '' }}>
                                                <label for="status_success" class="ml-2">Success</label>
                                            </div>
                                            <div>
                                                <input type="radio" id="status_failed" name="status" value="failed" {{ request('status') == 'failed' ? 'checked' : '' }}>
                                                <label for="status_failed" class="ml-2">Failed</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-5 d-flex">
                                        <p class="text-dark mt-2 mr-3"><strong>Action Detail : </strong></p>
                                        <div class="d-flex">
                                            <select name="action_detail" id="action_detail" class="form-control" style="width: auto;">
                                                <option value="all" {{ request('action_detail') == 'all' ? 'selected' : '' }}>All</option>
                                                <option value="Login successful" {{ request('action_detail') == 'Login successful' ? 'selected' : '' }}>Login successful</option>
                                                <option value="Email & Password is incorrect" {{ request('action_detail') == 'Email & Password is incorrect' ? 'selected' : '' }}>Email & Password is incorrect</option>
                                                <option value="User logged out successfully" {{ request('action_detail') == 'User logged out successfully' ? 'selected' : '' }}>User logged out successfully</option>
                                                <option value="Captcha validation failed" {{ request('action_detail') == 'Captcha validation failed' ? 'selected' : '' }}>Captcha validation failed</option>
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


     <!-- Table to Display Audit Logs -->
     <div class="table-container">
        <!-- Position the length dropdown to the left of DataTable buttons -->
    
            <select id="loginPageLengthSelect" class="form-control "  >
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="all">All</option>
            </select>
       
        <!-- DataTable itself -->
        <table class="table table-bordered table-striped myDataTable">
            <thead class="thead-dark">
                <tr>
                    <th scope="col" class="text-center">Sl. No.</th>
                    <th scope="col">id</th>
                    <th scope="col">username</th>
                    <th scope="col">status</th>
                    <th scope="col">ip_addr</th>
                    <th scope="col">action_details</th>
                    <th scope="col">created_at</th>
                </tr>
            </thead>
            <tbody>
                @foreach($auditTrails as $index => $audit)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $audit->id }}</td>
                    <td>{{ $audit->username }}</td>
                    <td>{{ $audit->status }}</td>
                    <td>{{ $audit->ip_addr }}</td>
                    <td>{{ $audit->action_details }}</td>
                    <td>{{ \Carbon\Carbon::parse($audit->created_at)->format('d-m-Y h:i:s A') }}</td>



                </tr>
                @endforeach
            </tbody>
        </table>
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

        $('#loginPageLengthSelect').on('change', function() {
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
