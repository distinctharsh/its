@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <h1>Inspection Details</h1>

    <div class="text-left mb-3">
        <button type="button" class="btn back-btn" onclick="window.location='{{ route('manageInspection') }}'">Back to Inspection List</button>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>Inspection Information</h5>

            @if(auth()->user()->hasRole('Admin'))
                        <a href="{{ route('editInspection', $inspection->id) }}" class="btn btn-primary btn-edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        @else
                        @if($inspection->deleted_at != null)
                        <span class="btn btn-primary disabled btn-edit" title="Cannot edit deleted inspection">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </span>
                        @else
                        <a href="{{ route('editInspection', $inspection->id) }}" class="btn btn-primary btn-edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        @endif
                    @endif
        </div>
        <div class="card-body">
            <p><strong>Category:</strong> {{ $inspection->category->category_name ? $inspection->category->category_name  : 'N/A' }}</p>
            <p><strong>Date of Joining:</strong> {{ $inspection->date_of_joining ? $inspection->date_of_joining->format('d-m-Y')  : 'N/A' }}</p>
            <p><strong>Status:</strong> {{ $inspection->status->status_name ? $inspection->status->status_name : 'N/A' }}</p>
            <p><strong>Inspector:</strong> {{ $inspection->inspector ? $inspection->inspector->name : 'N/A' }}</p>
            <p><strong>Remarks:</strong> {{ $inspection->remarks ? $inspection->remarks : 'N/A'}}</p>
        </div>
    </div>
</div>
@endsection
