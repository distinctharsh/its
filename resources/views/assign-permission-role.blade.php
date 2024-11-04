@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <h1>Manage Roles</h1>

    <!-- Button trigger modal -->
     <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#assignPermissionRoleModal">
        Assign Permission to Role
    </button>


    <table class="table">
        <thead>
            <tr>
                <th scope="col">S.No.</th>
                <th scope="col">Permission</th>
                <th scope="col">Roles</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $x =0;
            @endphp
          @foreach($permissionsWithRoles as $permission)
          <tr>
            <td>{{ ++$x }}</td>
            <td>{{ $permission->name }}</td>
            <td>
                @foreach ($permission->roles as $role)
                    {{ $role->name }}
                @endforeach
            </td>
          </tr>
          @endforeach
        </tbody>
    </table>



    <!-- Create Role Modal -->
    <div class="modal fade" id="assignPermissionRoleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="assignPermissionRoleForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Assign Permission to Role</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Role</label>
                            <select name="role_id" class="form-control" required>
                                <option>Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                                </select>
                        </div>

                        <div class="form-group">
                            <label for="">Permission</label>
                            <select name="permission_id" class="form-control" required>
                                <option>Select Permission</option>
                                @foreach($permissions as $permission)
                                    <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                @endforeach
                                </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary assignPermissionRoleBtn">Assign</button>
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

        $('#assignPermissionRoleForm').submit(function(e) {
            e.preventDefault();

            $('.assignPermissionRoleBtn').prop('disabled', true);

            var formData = $(this).serialize();

            $.ajax({
                url: "{{ route('createPermissionRole') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('.assignPermissionRoleBtn').prop('disabled', false);
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