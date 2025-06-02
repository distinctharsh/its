@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <!-- <h3 class="mb-4">Edit State</h3> -->
    <div class="text-left mb-3">
        <button type="button" class="btn back-btn" onclick="window.location='{{ route('manageState') }}'">Back</button>
    </div>
    <form id="updateStateForm" action="{{ route('updateState', $state->id) }}" method="POST">
        @csrf
        <input type="hidden" id="editStateId" name="state_id" value="{{ $state->id }}">

        <div class="card card-outline-secondary state-form">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 form-group state-form-group">
                        <label for="edit_state_name">State Name</label>
                        <input type="text" class="form-control" id="edit_state_name" name="state_name" value="{{ $state->state_name }}" required>
                    </div>
                </div>
                <div class="row">
                <!-- <div class="col-md-12 d-flex align-items-center">
                            <img id="editStateCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image mr-2">
                            <i class="fa-solid fa-arrows-rotate" style="cursor: pointer;" onclick="refreshCaptcha('editStateCaptchaImage')"></i>
                        </div>
                        <div class="col-md-4" style="float: right;">
                            <label for="captcha">Enter Captcha</label>
                            <input type="text" name="captcha" class="form-control" minlength="6" maxlength="6" required>
                        </div>
                </div> -->

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

    .state-form {
        border: none;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .state-form-group .form-control {
        border-radius: 0.25rem;
    }
</style>
@endpush

@push('script')
<script>
    $(document).ready(function() {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $('#updateStateForm').submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var stateId = $('#editStateId').val();

            $.ajax({
                url: "{{ route('updateState', '') }}/" + stateId,
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
                            msg: response.msg || 'State updated successfully!',
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
