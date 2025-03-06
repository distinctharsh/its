@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <!-- <h3 class="mb-4">Edit Designation</h3> -->
    <div class="text-left mb-3">
        <button type="button" class="btn back-btn" onclick="window.location='{{ route('manageDesignation') }}'">Back</button>
    </div>
    <form id="updateDesignationForm" action="{{ route('updateDesignation', $designation->id) }}" method="POST">
        @csrf
        <input type="hidden" id="editDesignationId" name="designation_id" value="{{ $designation->id }}">

        <div class="card card-outline-secondary inspection-form">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group inspection-form-group">
                        <label for="edit_designation_name">Designation Name</label>
                        <input type="text" class="form-control" id="edit_designation_name" name="designation_name" value="{{ $designation->designation_name }}" required>
                    </div>

                    <div class="col-md-12 form-group inspector-form-group">
                        <div class="row mb-3">
                            <div class="col-md-12 d-flex align-items-center">
                                <img id="editInspectorCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image mr-2">
                                <i class="fa-solid fa-arrows-rotate" style="cursor: pointer;" onclick="refreshCaptcha('editInspectorCaptchaImage')"></i>
                            </div>
                            <div class="col-md-4" style="float: right;">
                                <label for="captcha">Enter Captcha</label>
                                <input type="text" name="captcha" class="form-control" minlength="6" maxlength="6" required>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer text-center">
                    <button type="reset" class="btn btn-danger">Reset</button>
                    <button type="submit" class="btn btn-primary">Update</button>
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

    .inspection-form {
        border: none;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .form-control {
        border-radius: 0.25rem;
    }
</style>
@endpush

@push('script')
<script>
    $(document).ready(function() {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $('#updateDesignationForm').submit(function(e) {
            e.preventDefault();

            var formData = $(this).serialize(); // Serialize form data
            var designationId = $('#editDesignationId').val();

            $.ajax({
                url: "{{ route('updateDesignation', '') }}/" + designationId,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: formData,
                success: function(response) {
                    if (response.success) {
                        FancyAlerts.show({
                            msg: response.msg || 'Designation updated successfully!',
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