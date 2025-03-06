@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <!-- <h3 class="mb-4">Edit Opcw Fax</h3> -->
    <div class="text-left mb-3">
        <button type="button" class="btn back-btn" onclick="window.location='{{ route('manageOpcw') }}'">Back</button>
    </div>
    <form id="updateFaxForm" action="{{ route('updateOpcw', $fax->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="editFaxId" name="fax_id" value="{{ $fax->id }}">

        <div class="card card-outline-secondary inspection-form">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group inspection-form-group">
                        <label for="edit_fax_date">Communication Date</label>
                        <input type="date" class="form-control" id="edit_fax_date" name="fax_date" value="{{ $fax->fax_date->format('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-6 form-group inspection-form-group">
                        <label for="edit_fax_number">Document Number</label>
                        <input type="text" class="form-control" id="edit_fax_number" name="fax_number" value="{{ $fax->fax_number }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group inspection-form-group">
                        <label for="edit_reference_number">Reference Number</label>
                        <input type="text" class="form-control" id="edit_reference_number" name="reference_number" value="{{ $fax->reference_number }}" required>
                    </div>

                    <div class="col-md-6 form-group inspector-form-group">
                        <label for="fax_document">Fax Document <span class="text-danger upload-font">*PDF only (Max. Size 5MB)</span></label>
                        @if($fax->fax_document)
                        <span id="current-file" class="d-block">Current Report: <a href="{{ asset('storage/app/' . $fax->fax_document) }}" target="_blank">View Document</a></span>
                        @else
                        <span id="current-file" class="d-block">No Report Available</span>
                        @endif
                        <input type="file" class="form-control-file" id="fax_document" name="fax_document" accept="application/pdf">
                        <span id="selected-file" class="d-block mt-2"></span>
                    </div>


                </div>


                <div class="row">
                    <div class="col-md-8 form-group inspection-form-group">
                        <label for="edit_remarks">Remarks</label>
                        <textarea class="form-control" id="edit_remarks" name="remarks" rows="3">{{ $fax->remarks }}</textarea>
                    </div>
                    <div class="col-md-4 form-group inspection-form-group">
                        <label for="captcha">Enter Captcha</label>
                        <div style="position: relative;">
                            <img id="editOpcwCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                            <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha('editOpcwCaptchaImage')"></i>
                        </div>
                        <input type="text" name="captcha" class="form-control" minlength="6" maxlength="6" required>
                    </div>
                </div>

                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary">Update</button>
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

    @media (max-width: 576px) {
        .inspection-form-group {
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

        var maxFileSize = 5 * 1024 * 1024; 

        // Store the original values to compare with the updated ones
        var originalData = {
            fax_date: $('#edit_fax_date').val(),
            fax_number: $('#edit_fax_number').val(),
            reference_number: $('#edit_reference_number').val(),
            remarks: $('#edit_remarks').val(),
            fax_document: $('#fax_document')[0].files.length ? $('#fax_document')[0].files[0].name : null
        };

        $('#updateFaxForm').submit(function(e) {
            e.preventDefault();

            var formData = new FormData();

            // Check if anything has changed before appending it to formData
            var isChanged = false;

            if ($('#edit_fax_date').val() !== originalData.fax_date) {
                formData.append('fax_date', $('#edit_fax_date').val());
                isChanged = true;
            }
            if ($('#edit_fax_number').val() !== originalData.fax_number) {
                formData.append('fax_number', $('#edit_fax_number').val());
                isChanged = true;
            }
            if ($('#edit_reference_number').val() !== originalData.reference_number) {
                formData.append('reference_number', $('#edit_reference_number').val());
                isChanged = true;
            }
            if ($('#edit_remarks').val() !== originalData.remarks) {
                formData.append('remarks', $('#edit_remarks').val());
                isChanged = true;
            }

            // Check if a new document has been uploaded (if the file input is not empty)
            if ($('#fax_document')[0].files.length > 0) {
                formData.append('fax_document', $('#fax_document')[0].files[0]);
                isChanged = true;
            }

            // Captcha (this will be sent always, as it is a required field)
            formData.append('captcha', $('input[name="captcha"]').val());

            // Only submit if there are changes
            if (isChanged) {
                var faxId = $('#editFaxId').val();

                $.ajax({
                    url: "{{ route('updateOpcw', '') }}/" + faxId,
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
                                msg: response.msg || 'Opcw updated successfully!',
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
            } else {
                // If no changes, show a message (optional)
                FancyAlerts.show({
                    msg: 'No changes detected to update.',
                    type: 'warning'
                });
            }
        });
    });
</script>
@endpush