@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <!-- <h3 class="mb-4">Edit Nationality</h3> -->
    <div class="text-left mb-3">
        <button type="button" class="btn back-btn" onclick="window.location='{{ route('manageNationality') }}'">Back</button>
    </div>
    <form id="updateNationalityForm" action="{{ route('updateNationality', $nationality->id) }}" method="POST">
        @csrf
        <input type="hidden" id="editNationalityId" name="nationality_id" value="{{ $nationality->id }}">

        <div class="card card-outline-secondary nationality-form">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 form-group nationality-form-group">
                        <label for="edit_country_name">Nationality Name</label>
                        <input type="text" class="form-control" id="edit_country_name" name="country_name" value="{{ $nationality->country_name }}" required>
                    </div>
                </div>
                <div class="row">
                <div class="col-md-12 d-flex align-items-center">
                            <img id="editNationalityCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image mr-2">
                            <i class="fa-solid fa-arrows-rotate" style="cursor: pointer;" onclick="refreshCaptcha('editNationalityCaptchaImage')"></i>
                        </div>
                        <div class="col-md-4" style="float: right;">
                            <label for="captcha">Enter Captcha</label>
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

    .nationality-form {
        border: none;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .nationality-form-group .form-control {
        border-radius: 0.25rem;
    }
</style>
@endpush

@push('script')
<script>
    $(document).ready(function() {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $('#updateNationalityForm').submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var nationalityId = $('#editNationalityId').val();

            $.ajax({
                url: "{{ route('updateNationality', '') }}/" + nationalityId,
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
                            msg: response.msg || 'Nationality updated successfully!',
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
