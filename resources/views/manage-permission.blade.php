@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <h1>Manage Permissions</h1>

    <!-- Button trigger modal -->
     <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#createPermissionModal">
        Create Permission
    </button>


    <table class="table">
        <thead>
            <tr>
                <th scope="col">Sl. No.</th>
                <th scope="col">Permission</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
            $x = 0;
            @endphp

            @foreach($permissions as $permission)
            <tr>
                <th scope="row">{{ ++$x }}</th>
                <td>{{ $permission->name }}</td>
                <td>
                   
                    <button class="btn btn-primary">Edit</button>
                    <button data-id="{{ $permission->id }}" data-name="{{ $permission->name }}" class="btn btn-danger" >Delete</button>
                   
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>



    <!-- Create Permission Modal -->
    <div class="modal fade" id="createPermissionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="createPermissionForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Create Permission</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Permission</label>
                            <input type="text" class="form-control" name="permission" placeholder="Enter Permission" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary createPermissionBtn">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $('#createPermissionForm').submit(function(e) {
            e.preventDefault();

            $('.createPermissionBtn').prop('disabled', true);

            var formData = $(this).serialize();

            $.ajax({
                url: "{{ route('createPermission') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('.createPermissionBtn').prop('disabled', false);
                        alert(response.msg);
                        location.reload();
                    } else {
                        alert("Error: " + response.msg);
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText); 
                    alert("An error occurred. Please check the console for details.");
                }
            });
        });
    });
</script>
@endpush