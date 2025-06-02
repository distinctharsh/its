@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <div class="text-left mb-3">
        <button type="button" class="btn btn-secondary" onclick="window.location='{{ route('manageUser') }}'">
            <i class="fa fa-arrow-left"></i> Back
        </button>
    </div>

    <form id="editUserForm">
        <div class="card user-form shadow-sm">
            <div class="card-body">
                <input type="hidden" id="user_id" value="{{ $user->id }}">

                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="user_name">User Name</label>
                        <input type="text" class="form-control" id="user_name" name="user_name" value="{{ $user->name }}" required>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="user_email">User Email</label>
                        <input type="email" class="form-control" id="user_email" name="user_email" value="{{ $user->email }}" required>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="user_role_id">User Role</label>
                        <select class="form-control" id="user_role_id" name="user_role_id" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>



                    <div class="col-md-4 form-group">
                        <label for="designation_id">Designation</label>
                        <select class="form-control" id="designation_id" name="designation_id" required>
                            <option value="">Select Designation</option>
                            @foreach($designations as $designation)
                            <option value="{{ $designation->id }}" {{ $user->designation_id == $designation->id ? 'selected' : '' }}>
                                {{ $designation->designation_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                   




                    <div class="col-md-4 form-group">
                        <label for="user_password">New Password</label>
                        <input type="password" class="form-control" id="user_password" name="user_password" placeholder="Enter new password" >
                    </div>

                </div>
                <!-- <div class="row">
                    <div class="col-md-6 form-group status-form-group">
                        <label for="captcha">Enter Captcha</label>
                        <div style="position: relative;">
                            <img id="editUserCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                            <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha('editUserCaptchaImage')"></i>
                        </div>
                        <input type="text" name="captcha" class="form-control" minlength="6" maxlength="6" required>
                    </div>
                </div> -->
            </div>

            <div class="card-footer text-center">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Update User
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('style')
<style>
    body {
        background-color: #f8f9fa;
    }

    .user-form {
        border: none;
        border-radius: 8px;
        overflow: hidden;
    }

    .form-control {
        border-radius: 6px;
    }
</style>
@endpush

@push('script')
<script>
    $(document).ready(function () {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $('#editUserForm').submit(function (e) {
            e.preventDefault();

            var submitButton = $(this).find('button[type="submit"]');
            submitButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');

            var formData = new FormData(this);
            var userId = $('#user_id').val();

            var base_url = "{{ url('/') }}";

            $.ajax({
                url: base_url +"/update-user/" + userId,
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        FancyAlerts.show({
                            msg: response.msg || 'User updated successfully!',
                            type: 'success'
                        });

                        setTimeout(function () {
                            window.location.href = "{{ route('manageUser') }}";
                        }, 2000);
                    } else {
                        FancyAlerts.show({
                            msg: response.msg || 'An error occurred!',
                            type: 'error'
                        });
                    }
                },
                error: function (xhr) {
                    var response = JSON.parse(xhr.responseText);
                    var message = response.msg || 'An unknown error occurred';

                    FancyAlerts.show({
                        msg: 'Error: ' + message,
                        type: 'error'
                    });

                    submitButton.prop('disabled', false).html('<i class="fa fa-save"></i> Update User');
                }
            });
        });
    });
</script>
@endpush
