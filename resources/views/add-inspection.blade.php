@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <h3 class="mb-4">Add New Inspection</h3>
    <div class="text-left mb-3">
        <button type="button" class="btn back-btn" onclick="window.location='{{ route('manageInspection') }}'">Back</button>
    </div>
    <form id="addInspectionForm" enctype="multipart/form-data">

        <div class="card card-outline-secondary inspection-form">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group inspection-form-group">
                        <label for="inspector_id">Select Inspector</label>
                        <select class="form-control" id="inspector_id" name="inspector_id" required>
                            <option value="">Select Inspector</option>
                            @foreach($inspectors as $inspector)
                            <option value="{{ $inspector->id }}">{{ $inspector->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 form-group inspection-form-group">
                        <label for="category_id">Inspection Category</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($inspection_categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group inspection-form-group">
                        <label for="date_of_joining">Joining Date</label>
                        <input type="date" class="form-control" id="date_of_joining" name="date_of_joining" required>
                    </div>

                    <div class="col-md-6 form-group inspection-form-group">
                        <label for="status">Status</label>
                        <select name="status_id" class="form-control" required>
                            <option value="">Select Status</option>
                            @foreach($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group inspection-form-group">
                        <label for="remarks">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                    </div>

                    <div class="col-md-6 form-group inspection-form-group">
                        <label for="captcha">Enter Captcha</label>
                        <div style="position: relative;">
                            <img id="addInspectionCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                            <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha('addInspectionCaptchaImage')"></i>
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

        $('#addInspectionForm').submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('createInspection') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        FancyAlerts.show({
                            msg: response.msg || 'Inspection added successfully!',
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
