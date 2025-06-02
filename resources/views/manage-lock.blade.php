@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">

    @if(session('success'))
    <div class="alert alert-success" id="successAlert">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger" id="errorAlert">
        {{ session('error') }}
    </div>
    @endif


    <div class="row mb-4">
        <div class="col-12">
            <form method="POST" action="{{ route('createLock') }}">
                @csrf
                <div class="form-row align-items-center">

                    <div class="col-auto">
                        <label for="pageSelect">Select Page</label>
                        <select name="page" id="pageSelect" class="form-control" required>
                            <option value="">-- Select Page --</option>
                            <option value="inspector">Inspector</option>
                            <option value="inspection">Inspection</option>
                            <option value="opcw">OPCW</option>
                            <option value="other_staff">Other Staff</option>
                        </select>
                    </div>

                    <div class="col-auto">
                        <label for="fromDatetime">From</label>
                        <input type="datetime-local" id="fromDatetime" name="from" class="form-control" required>
                    </div>

                    <div class="col-auto">
                        <label for="toDatetime">To</label>
                        <input type="datetime-local" id="toDatetime" name="to" class="form-control" required>
                    </div>

                    <div class="col-auto d-flex align-items-center" style="padding-top: 32px;">
                        <div class="form-check">
                            <input type="checkbox" name="locked" id="lockedCheckbox" class="form-check-input" checked>
                            <label for="lockedCheckbox" class="form-check-label">Locked</label>
                        </div>
                    </div>

                    <div class="col-auto d-flex align-items-center" style="padding-top: 32px;">
                        <button type="submit" class="btn btn-primary">Lock</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if($locks->isEmpty())
            <p>No locks found.</p>
            @else
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Page</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Locked</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($locks as $lock)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $lock->page)) }}</td>
                        <td>{{ \Carbon\Carbon::parse($lock->from)->format('d-m-Y h:i A') }}</td>
                        <td>{{ \Carbon\Carbon::parse($lock->to)->format('d-m-Y h:i A') }}</td>
                        <td>{{ $lock->locked ? 'Yes' : 'No' }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning editBtn"
                                data-id="{{ $lock->id }}"
                                data-page="{{ $lock->page }}"
                                data-from="{{ $lock->from }}"
                                data-to="{{ $lock->to }}"
                                data-locked="{{ $lock->locked }}">
                                Edit
                            </button>

                            <button class="btn btn-sm btn-danger deleteBtn"
                                data-id="{{ $lock->id }}">
                                Delete
                            </button>
                        </td>



                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>




    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editId">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Lock</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="mb-3">
                            <label>Page</label>
                            <select id="editPage" class="form-control" disabled>
                                <option value="inspector">Inspector</option>
                                <option value="inspection">Inspection</option>
                                <option value="opcw">OPCW</option>
                                <option value="other_staff">Other Staff</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>From</label>
                            <input type="datetime-local" id="editFrom" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>To</label>
                            <input type="datetime-local" id="editTo" class="form-control" required>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="editLocked">
                            <label class="form-check-label" for="editLocked">
                                Locked
                            </label>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>













</div>
@endsection



@push('script')
<script>
    $(document).ready(function() {
        setTimeout(() => {
            $('#successAlert').fadeOut('slow');
            $('#errorAlert').fadeOut('slow');
        }, 5000);
    });
</script>



<script>
    $(document).ready(function() {

        // Auto-hide alert
        setTimeout(() => {
            $('#successAlert').fadeOut('slow');
            $('#errorAlert').fadeOut('slow');
        }, 5000);

        // Edit button click
        $('.editBtn').on('click', function() {
            $('#editId').val($(this).data('id'));
            $('#editPage').val($(this).data('page'));
            $('#editFrom').val(new Date($(this).data('from')).toISOString().slice(0, 16));
            $('#editTo').val(new Date($(this).data('to')).toISOString().slice(0, 16));
            $('#editLocked').prop('checked', $(this).data('locked'));

            $('#editModal').modal('show');
        });

        // AJAX Update
        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            let id = $('#editId').val();

            $.ajax({
                url: `/locks/${id}`, // route banani padegi
                method: 'POST',
                data: {
                    _method: 'PUT',
                    _token: '{{ csrf_token() }}',
                    from: $('#editFrom').val(),
                    to: $('#editTo').val(),
                    locked: $('#editLocked').is(':checked') ? 1 : 0
                },
                success: function(response) {
                    location.reload(); // for now, reload table. Later: update row directly
                },
                error: function(xhr) {
                    alert("Update failed!");
                }
            });
        });
    });
</script>




<script>
    $(document).ready(function() {
        const csrfToken = '{{ csrf_token() }}';

        $(document).on('click', '.deleteBtn', function() {
            const lockId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this Lock?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/locks/${lockId}`,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        data: {
                            _method: 'DELETE'
                        },
                        success: function(res) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: res.message || 'Lock deleted successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });

                            // Remove the deleted row from table without reload
                            $(`.deleteBtn[data-id="${lockId}"]`).closest('tr').remove();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong while deleting.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });
    });
</script>


@endpush