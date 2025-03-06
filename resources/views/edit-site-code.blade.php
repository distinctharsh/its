@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <!-- <h3 class="mb-4">Edit Site Code</h3> -->
    <div class="text-left mb-3">
        <button type="button" class="btn back-btn" onclick="window.location='{{ route('manageSiteCode') }}'">Back</button>
    </div>
    <form id="updateSiteCodeForm" action="{{ route('updateSiteCode', $siteCode->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="editSiteCodeId" name="site_code_id" value="{{ $siteCode->id }}">

        <div class="card card-outline-secondary status-form">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group status-form-group">
                        <label for="edit_site_code">Code</label>
                        <input type="text" class="form-control" id="edit_site_code" name="site_code" value="{{ $siteCode->site_code }}" placeholder="Enter Plant Site" required>
                    </div>

                    <div class="col-md-6 form-group status-form-group">
                        <label for="edit_site_name">Name</label>
                        <input type="text" class="form-control" id="edit_site_name" name="site_name" value="{{ $siteCode->site_name }}" placeholder="Enter Site Name" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group status-form-group">
                        <label for="edit_site_address">Address</label>
                        <textarea class="form-control" id="edit_site_address" name="site_address" rows="3" required>{{ $siteCode->site_address }}</textarea>
                    </div>
                    <div class="col-md-6 form-group status-form-group">
                        <label for="site_state_id">State</label>
                        <select class="form-control" name="state_id" id="site_state_id" required>
                            <option value="">Select State</option>
                            @foreach($states as $state)
                            @if($state->id == $siteCode->state_id)
                            <option value="{{ $state->id }}" selected>{{ $state->state_name }}</option>
                            @else
                            <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>


                </div>

                <div class="row">
                    <div class="col-md-6 form-group status-form-group">
                        <label for="captcha">Enter Captcha</label>
                        <div style="position: relative;">
                            <img id="editSiteCodeCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                            <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha('editSiteCodeCaptchaImage')"></i>
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

    .status-form {
        border: none;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .status-form-group .form-control {
        border-radius: 0.25rem;
    }

    @media (max-width: 576px) {
        .status-form-group {
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

        $('#updateSiteCodeForm').submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var siteCodeId = $('#editSiteCodeId').val();

            $.ajax({
                url: "{{ route('updateSiteCode', '') }}/" + siteCodeId,
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
                            msg: response.msg || 'Plant Site updated successfully!',
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