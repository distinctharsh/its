@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <h3 class="mb-4">Add New OPCW Fax</h3>
    <div class="text-left mb-3">
        <button type="button" class="btn back-btn" onclick="window.location='{{ route('manageOpcw') }}'">Back</button>
    </div>
    <form id="addFaxForm" enctype="multipart/form-data">

        <div class="card card-outline-secondary inspection-form">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group inspection-form-group">
                        <label for="fax_date">Fax Date</label>
                        <input type="date" class="form-control" id="fax_date" name="fax_date" required>
                    </div>

                    <div class="col-md-6 form-group inspection-form-group">
                        <label for="fax_number">Fax Number</label>
                        <input type="text" class="form-control" id="fax_number" name="fax_number" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group inspection-form-group">
                        <label for="reference_number">Reference Number</label>
                        <input type="text" class="form-control" id="reference_number" name="reference_number" required>
                    </div>

                    <div class="col-md-4 form-group inspector-form-group">
                        <label for="fax_document">Upload Document <span class="text-danger upload-font" >*PDF only (Max. Size 5MB)</span></label>
                        <input type="file" class="form-control-file" id="fax_document" name="fax_document" accept="application/pdf">
                    </div>


                </div>

                <div class="row justify-content-between">
                    <div class="col-md-6 form-group inspection-form-group">
                        <label for="remarks">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                    </div>

                    <div class="col-md-4 form-group inspection-form-group">
                        <label for="captcha">Enter Captcha</label>
                        <div style="position: relative;">
                            <img id="addOpcwCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                            <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha('addOpcwCaptchaImage')"></i>
                        </div>
                        <input type="text" name="captcha" class="form-control" minlength="6" maxlength="6" required>
                    </div>

                </div>

                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary">Add</button>
                    <button type="reset" class="btn btn-danger">Reset</button>
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

    .inspection-form {
        border: none;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .inspection-form-group .form-control,
    .inspection-form-group .form-control-file {
        border-radius: 0.25rem;
    }
</style>
@endpush

@push('script')
<script>
    $(document).ready(function() {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        function formatDateTime(datetime) {
            let date = new Date(datetime);
            let year = date.getFullYear();
            let month = ('0' + (date.getMonth() + 1)).slice(-2);
            let day = ('0' + date.getDate()).slice(-2);
            return `${year}-${month}-${day}`;
        }

        function validateForm() {
            var isValid = true;
            // fax_document Validation
            var faxDocument = $('#fax_document')[0].files[0];
            if (faxDocument) {

                var fileType = faxDocument.type;

                var fileSize = faxDocument.size;

                if (fileType !== 'application/pdf') {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'OPCW Fax Document must be a PDF file.',
                        type: 'error'
                    });
                } else if (fileSize > 50 * 1024 * 1024) {
                    isValid = false;
                    FancyAlerts.show({
                        msg: 'OPCW Fax Document must be less than 50MB.',
                        type: 'error'
                    });
                }
            }
            return isValid;

        }

        $('#addFaxForm').submit(function(e) {
            e.preventDefault();

            if (validateForm()) {
                var formData = new FormData(this);
                $.ajax({
                    url: "{{ route('createOpcw') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            FancyAlerts.show({
                                msg: response.msg || 'Opcw added successfully!',
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

            }
        });
    });
</script>
@endpush