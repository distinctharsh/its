@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <h3 class="mb-4">Edit Visit Category</h3>
    <div class="text-left mb-3">
        <button type="button" class="btn back-btn" onclick="window.location='{{ route('manageVisitCategory') }}'">Back</button>
    </div>
    <form id="updateVisitCategoryForm" action="{{ route('updateVisitCategory', $visit_category->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="editVisitCategoryId" name="visit_category_id" value="{{ $visit_category->id }}">

        <div class="card card-outline-secondary visit-category-form">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 form-group visit-category-form-group">
                        <label for="edit_category_name">Visit Category Name</label>
                        <input type="text" class="form-control" id="edit_category_name" name="category_name" value="{{ $visit_category->category_name }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 form-group visit-category-form-group">
                        <label for="captcha">Enter Captcha</label>
                        <div style="position: relative;">
                            <img id="editVisitCategoryCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                            <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha('editVisitCategoryCaptchaImage')"></i>
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

        $('#updateVisitCategoryForm').submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var visitCategoryId = $('#editVisitCategoryId').val();

            $.ajax({
                url: "{{ route('updateVisitCategory', '') }}/" + visitCategoryId,
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
                            msg: response.msg || 'Visit Category updated successfully!',
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