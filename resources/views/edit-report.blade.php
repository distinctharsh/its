@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <h3 class="mb-4">Edit Report</h3>
    <div class="text-left mb-3">
        <button type="button" class="btn btn-secondary" onclick="window.location='{{ route('manageReport') }}'">Back</button>
    </div>
    <form id="updateReportForm" action="{{ route('updateReport', $report->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="editReportId" name="report_id" value="{{ $report->id }}">

        <div class="card card-outline-secondary report-form">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 form-group report-form-group">
                        <label for="edit_description">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" required>{{ $report->description }}</textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group report-form-group">
                        <label for="captcha">Enter Captcha</label>
                        <div style="position: relative;">
                            <img id="editReportCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                            <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha('editReportCaptchaImage')"></i>
                        </div>
                        <input type="text" name="captcha" class="form-control" minlength="6" maxlength="6" required>
                    </div>
                </div>

                <div class="card-footer text-center">
                    <button type="reset" class="btn btn-danger">Reset</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
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

    .report-form {
        border: none;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .report-form-group .form-control {
        border-radius: 0.25rem;
    }

    @media (max-width: 576px) {
        .report-form-group {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>
@endpush

@push('script')
<script>
    $(document).ready(function() {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $('#updateReportForm').submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var reportId = $('#editReportId').val();

            $.ajax({
                url: "{{ route('updateReport', '') }}/" + reportId,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        FancyAlerts.show({
                            msg: response.msg || 'Report updated successfully!',
                            type: 'success'
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        FancyAlerts.show({
                            msg: 'Error: ' + response.msg,
                            type: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    var response = JSON.parse(xhr.responseText);
                    var message = response.msg ? response.msg : 'An unknown error occurred';
                    FancyAlerts.show({
                        msg: 'Error: ' + message,
                        type: 'error'
                    });
                }
            });
        });
    });
</script>
@endpush
