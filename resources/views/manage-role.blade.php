@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <h1>Manage Roles</h1>

    <!-- Button trigger modal -->
     <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#createRoleModal">
        Create Role
    </button>


    <table class="table">
        <thead>
            <tr>
                <th scope="col">S.No.</th>
                <th scope="col">Role</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
            $x = 0;
            @endphp

            @foreach($roles as $role)
            <tr>
                <th scope="row">{{ ++$x }}</th>
                <td>{{ $role->name }}</td>
                <td>
                    @if(strtolower($role->name) != 'user')
                    <button class="btn btn-primary">Edit</button>
                    <button data-id="{{ $role->id }}" data-name="{{ $role->name }}" class="btn btn-danger deleteRoleBtn" data-toggle="modal" data-target="#deleteRoleModal"><i class="fa-solid fa-xmark"></i></button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>



    <!-- Create Role Modal -->
    <div class="modal fade" id="createRoleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="createRoleForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Create Role</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Role</label>
                            <input type="text" class="form-control" name="role" placeholder="Enter Role" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary createRoleBtn">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




    <!-- Delete Role Modal -->

    <div class="modal fade" id="deleteRoleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="deleteRoleForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Delete Role</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="role_id" id="deleteRoleId">
                        <p>Are you sure, You want to delete the <span class="delete-role"></span> Role?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger delete-role-btn">Delete</button>
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

        $('#createRoleForm').submit(function(e) {
            e.preventDefault();

            $('.createRoleBtn').prop('disabled', true);

            var formData = $(this).serialize();

            $.ajax({
                url: "{{ route('createRole') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('.createRoleBtn').prop('disabled', false);
                        alert(response.msg);
                        location.reload();
                    } else {
                        alert("Error: " + response.msg);
                    }
                },
                error: function(xhr) {
                   
                    alert("An error occurred. Please check the console for details.");
                }
            });
        });

        $('.deleteRoleBtn').click(function() {
            var roleId = $(this).data('id'); 
            var roleName = $(this).data('name'); 
            $('#deleteRoleId').val(roleId); 
            $('.delete-role').text(roleName);
        });

        $('#deleteRoleForm').submit(function(e) {
            e.preventDefault();

            $('.delete-role-btn').prop('disabled', true);

            var formData = $(this).serialize();

            $.ajax({
                url: "{{ route('deleteRole') }}",
                type: "DELETE",
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('.delete-role-btn').prop('disabled', false);
                        alert(response.msg);
                        location.reload();
                    } else {
                        alert("Error: " + response.msg);
                    }
                },
                error: function(xhr) {
                    alert("An error occurred. Please check the console for details.");
                }
            });
        });

    });
</script>
@endpush