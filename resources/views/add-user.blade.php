@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <div class="text-left mb-3">
        <button type="button" class="btn btn-secondary" onclick="window.location='{{ route('manageUser') }}'">
            <i class="fa fa-arrow-left"></i> Back
        </button>
    </div>

    <form id="addUserForm">
        <div class="card user-form shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="user_name">User Name</label>
                        <input type="text" class="form-control" id="user_name" name="user_name" required>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="user_email">User Email</label>
                        <input type="email" class="form-control" id="user_email" name="user_email" required>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="user_role_id">User Role</label>
                        <select class="form-control" id="user_role_id" name="user_role_id" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                <div class="col-md-4 form-group">
                    <label for="designation_id">Designation</label>
                    <select class="form-control" id="designation_id" name="designation_id" required>
                        <option value="">Select Designation</option>
                        @foreach($designations as $designation)
                        <option value="{{ $designation->id }}">{{ $designation->designation_name }}</option>
                        @endforeach
                    </select>
                </div>

              
                </div>

                <!-- <div class="row mt-3">
                    <div class="col-md-4 form-group">
                        <label for="captcha">Enter Captcha</label><br>
                        <div class="d-flex align-items-center">
                            <img id="addUserCaptchaImage" src="{{ route('captcha') }}" alt="Captcha" class="captcha-image mr-2">
                            <i class="fa fa-sync-alt refresh-captcha" onclick="refreshCaptcha()" style="cursor: pointer;"></i>
                        </div>
                        <input type="text" name="captcha" class="form-control mt-2" minlength="6" maxlength="6" required>
                    </div>
                </div> -->
            </div>

            <div class="card-footer text-center">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-user-plus"></i> Add User
                </button>
                <button type="reset" class="btn btn-danger">
                    <i class="fa fa-undo"></i> Reset
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

    .refresh-captcha {
        font-size: 1.2rem;
        color: #007bff;
    }

    .refresh-captcha:hover {
        color: #0056b3;
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

        $('#addUserForm').submit(function (e) {
            e.preventDefault();
            
            var submitButton = $(this).find('button[type="submit"]');
            submitButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Adding...');

            var formData = new FormData(this);

            $.ajax({
                url: "{{ route('createUser') }}",
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
                            msg: response.msg || 'User added successfully!',
                            type: 'success'
                        });

                        setTimeout(function () {
                            location.reload();
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

                    submitButton.prop('disabled', false).html('<i class="fa fa-user-plus"></i> Add User');
                }
            });
        });
    });

    function refreshCaptcha() {
        $('#addUserCaptchaImage').attr('src', "{{ route('captcha') }}?" + Math.random());
    }
</script>
@endpush
