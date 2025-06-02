@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <div class="text-left mb-3">
        <button type="button" class="btn btn-secondary" onclick="window.location='{{ route('manageIssue') }}'">
            <i class="fa fa-arrow-left"></i> Back
        </button>
    </div>

    <form id="editIssueForm">
        <div class="card issue-form shadow-sm">
            <div class="card-body">
                <input type="hidden" id="issue_id" value="{{ $issue->id }}">

                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="issue_name">Issue Name</label>
                        <input type="text" class="form-control" id="issue_name" name="issue_name" value="{{ $issue->name }}" required>
                    </div>
                </div>

                <!-- <div class="row">
                    <div class="col-md-6 form-group status-form-group">
                        <label for="captcha">Enter Captcha</label>
                        <div style="position: relative;">
                            <img id="editIssueCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                            <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha('editIssueCaptchaImage')"></i>
                        </div>
                        <input type="text" name="captcha" class="form-control" minlength="6" maxlength="6" required>
                    </div>
                </div> -->
            </div>

            <div class="card-footer text-center">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Update Issue
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

    .issue-form {
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

        $('#editIssueForm').submit(function (e) {
            e.preventDefault();

            var submitButton = $(this).find('button[type="submit"]');
            submitButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');

            var formData = new FormData(this);
            var issueId = $('#issue_id').val();

            var base_url = "{{ url('/') }}";

            $.ajax({
                url: base_url+"/update-issue/" + issueId,
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
                            msg: response.msg || 'Issue updated successfully!',
                            type: 'success'
                        });

                        setTimeout(function () {
                            window.location.href = "{{ route('manageIssue') }}";
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

                    submitButton.prop('disabled', false).html('<i class="fa fa-save"></i> Update Issue');
                }
            });
        });
    });
</script>
@endpush
