@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <div class="text-left mb-3">
        <button type="button" class="btn btn-secondary" onclick="window.location='{{ route('manageIssue') }}'">
            <i class="fa fa-arrow-left"></i> Back
        </button>
    </div>

    <form id="addIssueForm">
        <div class="card issue-form shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="issue_name">Issue Name</label>
                        <input type="text" class="form-control" id="issue_name" name="issue_name" required>
                    </div>

                </div>

                <!-- <div class="row mt-3">
                    <div class="col-md-4 form-group">
                        <label for="captcha">Enter Captcha</label><br>
                        <div class="d-flex align-items-center">
                            <img id="addIssueCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                            <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha('addIssueCaptchaImage')"></i>

                        </div>
                        <input type="text" name="captcha" class="form-control mt-2" minlength="6" maxlength="6" required>
                    </div>
                </div> -->
            </div>

            <div class="card-footer text-center">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-user-plus"></i> Add Issue
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

        $('#addIssueForm').submit(function (e) {
            e.preventDefault();
            
            var submitButton = $(this).find('button[type="submit"]');
            submitButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Adding...');

            var formData = new FormData(this);

            $.ajax({
                url: "{{ route('createIssue') }}",
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
                            msg: response.msg || 'Issue added successfully!',
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

                    submitButton.prop('disabled', false).html('<i class="fa fa-user-plus"></i> Add Issue');
                }
            });
        });
    });

    function refreshCaptcha() {
        $('#addUserCaptchaImage').attr('src', "{{ route('captcha') }}?" + Math.random());
    }
</script>
@endpush
