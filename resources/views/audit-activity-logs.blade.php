@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <h1>Audit Activity Logs</h1>

    @if($activityLogs->isEmpty())
        <p>No audit trails found.</p>
    @else
    @endif
    <table class="table table-bordered table-striped" id="myTable" data-export-columns="0,1">
        <div class="mb-3 d-flex " style="float: right;">
            
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
                <th scope="col" class="text-center">S.No.</th>
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
                    <td>{{ $audit->created_at }}</td>
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
    document.addEventListener('DOMContentLoaded', function () {
        const changesModal = $('#changesModal');
        const changesContent = $('#changesContent');

        $('.view-changes').on('click', function () {

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
</script>
@endpush