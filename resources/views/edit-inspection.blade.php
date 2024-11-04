@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <h3 class="mb-4">Edit Inspection</h3>
    <div class="text-left mb-3">
        <button type="button" class="btn back-btn" onclick="window.location='{{ route('manageInspection') }}'">Back</button>
    </div>
    <form id="updateInspectionForm" action="{{ route('updateInspection', $inspection->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="editInspectionId" name="inspection_id" value="{{ $inspection->id }}">

        <div class="card card-outline-secondary inspection-form">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group inspection-form-group">
                        <label for="edit_inspector_id">Select Inspector</label>
                        <!-- <select class="form-control" id="edit_inspector_id" name="inspector_id" required>
                            @foreach($inspectors as $inspector)
                            <option value="{{ $inspector->id }}" {{ $inspector->id == $inspection->inspector_id ? 'selected' : '' }}>
                                {{ $inspector->name }}
                            </option>
                            @endforeach
                        </select> -->

                        <!-- <select class="form-control" id="edit_inspector_id" name="inspector_id" required>
                            @foreach($inspectors as $inspector)
                            <option value="{{ $inspector->id }}" 
                                {{ (old('inspector_id') ?? $inspection->inspector_id) == $inspector->id ? 'selected' : '' }}
                                {{ !$inspector->is_active ? 'disabled' : '' }}
                                style="{{ $inspector->is_active ? '' : 'color: #ccc;' }}">
                                {{ $inspector->name }}
                            </option>
                            @endforeach
                        </select> -->


                        <select class="form-control" id="edit_inspector_id" name="inspector_id" required>
                            @foreach($inspectors as $inspector)
                            @if($inspector->id == $inspection->inspector_id)
                            <option value="{{ $inspector->id }}" selected>
                                {{ $inspector->name }}
                            </option>
                            @else
                            <option value="{{ $inspector->id }}">
                                {{ $inspector->name }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 form-group inspection-form-group">
                        <label for="edit_category">Inspection Category</label>
                        <!-- <select class="form-control" id="edit_category" name="category_id" required>
                            @foreach($inspection_categories as $category)
                            <option value="{{ $category->id }}" {{ $category->id == $inspection->category_id ? 'selected' : '' }}
                                {{ !$category->is_active ? 'disabled' : '' }}
                                style="{{ $category->is_active ? '' : 'color: #ccc;' }}">
                                {{ $category->category_name }}
                            </option>
                            @endforeach
                        </select> -->


                        <select class="form-control" id="edit_category" name="category_id" required>
                            @foreach($inspection_categories as $category)
                            @if($category->id == $inspection->category_id)
                            <option value="{{ $category->id }}" selected>
                                {{ $category->category_name }}
                            </option>
                            @else
                            <option value="{{ $category->id }}">
                                {{ $category->category_name }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group inspection-form-group">
                        <label for="edit_date_of_joining">Joining Date</label>
                        <input type="date" class="form-control" id="edit_date_of_joining" name="date_of_joining" value="{{ $inspection->date_of_joining ? \Carbon\Carbon::parse($inspection->date_of_joining)->format('Y-m-d') : '' }}" required>

                    </div>

                    <div class="col-md-6 form-group inspection-form-group">
                        <label for="edit_status">Status</label>
                        <!-- <select class="form-control" id="edit_status" name="status_id" required>
                            @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ $status->id == $inspection->status_id ? 'selected' : '' }}
                                {{ !$status->is_active ? 'disabled' : '' }}
                                style="{{ $status->is_active ? '' : 'color: #ccc;' }}">
                                {{ $status->status_name }}
                            </option>
                            @endforeach
                        </select> -->

                        <select class="form-control" id="edit_status" name="status_id" required>
                            @foreach($statuses as $status)
                            @if($status->id == $inspection->status_id)
                            <option value="{{ $status->id }}" selected>
                                {{ $status->status_name }}
                            </option>
                            @elseif($status->is_active)
                            <option value="{{ $status->id }}">
                                {{ $status->status_name }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group inspection-form-group">
                        <label for="edit_remarks">Remarks</label>
                        <textarea class="form-control" id="edit_remarks" name="remarks" rows="3">{{ $inspection->remarks }}</textarea>
                    </div>

                    <div class="col-md-6 form-group inspection-form-group">
                        <label for="captcha">Enter Captcha</label>
                        <div style="position: relative;">
                            <img id="editInspectionCaptchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                            <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha('editInspectionCaptchaImage')"></i>
                        </div>
                        <input type="text" name="captcha" class="form-control" minlength="6" maxlength="6"  required>
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

        $('#updateInspectionForm').submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);


            var inspectionId = $('#editInspectionId').val();

            $.ajax({
                url: "{{ url('update-inspection') }}/" + inspectionId,
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
                            msg: response.msg || 'Inspection updated successfully!',
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