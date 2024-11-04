@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Inspector Details</h1>

    <div class="text-left mb-3">
        <button type="button" class="btn back-btn" onclick="window.location='{{ route('manageInspector') }}'">Back</button>
    </div>


    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <h5 class="card-title">{{ $inspector->name }}</h5>

                @if(!auth()->user()->hasRole('Viewer'))
                @if(auth()->user()->hasRole('Admin'))
                <a href="{{ route('editInspector', $inspector->id) }}" class="btn btn-primary btn-edit">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
                @else
                @if($inspector->deleted_at != null)
                <span class="btn btn-primary disabled btn-edit" title="Cannot edit deleted inspector">
                    <i class="fa-solid fa-pen-to-square"></i>
                </span>
                @else
                <a href="{{ route('editInspector', $inspector->id) }}" class="btn btn-primary btn-edit">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
                @endif
                @endif
                @endif
            </div>



            <hr>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Designation:</strong> {{ $inspector->rank_name ?? 'N/A' }}</p>
                    <p><strong>Date of Birth:</strong> {{ $inspector->dob ? \Carbon\Carbon::parse($inspector->dob)->format('d F Y') : 'N/A' }}</p>

                    <p><strong>Nationality:</strong> {{ $inspector->country ?? 'N/A' }}</p>
                    <p><strong>Passport Number:</strong> {{ $inspector->passport_number }}</p>
                    <p><strong>UNLP Number:</strong> {{ $inspector->unlp_number }}</p>
                    <p><strong>Place of Birth:</strong> {{ $inspector->place_of_birth }}</p>
                    <p><strong>Clearance Certificate:</strong>
                        @if($inspector->clearance_certificate)
                        <a href="{{ asset('storage/app/' . $inspector->clearance_certificate) }}" target="_blank" class="pdf-icon" data-toggle="tooltip" title="Clearance Certificate">
                            <i class="fa-solid fa-file-pdf"></i>
                        </a>
                        @else
                        <span>No Clearance Certificate Available</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Qualifications:</strong> {{ $inspector->qualifications }}</p>
                    <p><strong>Professional Experience:</strong> {{ $inspector->professional_experience }}</p>

                    <p><strong>Remarks:</strong> {{ $inspector->remarks }}</p>
                </div>
            </div>

            <!-- Inspections Table -->               
            <h2 class="mt-4">Inspections Details</h2>

            @if($inspections->isEmpty())
                <p>No inspections available for this inspector.</p>
            @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Date of Joining</th>
                        <th>Status</th>
                        <!-- <th>Remarks</th> -->
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inspections as $inspection)
                        <tr>
                            <td>{{ $inspection->category->category_name ?? 'N/A' }}</td>
                            <td>{{ $inspection->date_of_joining ? $inspection->date_of_joining->format('d F Y') : 'N/A' }}</td>

                            <td>{{ $inspection->status->status_name ?? 'N/A' }}</td>
                            <!-- <td>{{ $inspection->remarks }}</td> -->
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

        </div>
    </div>
</div>
@endsection