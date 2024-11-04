@extends('layouts.layout')

@section('content')
<div class="container my-5">
    <div class="row" >
        <!-- Inspector Card -->
     
        <div class="col-md-4 mb-4">
            <div class="card border-primary">
                <h5 class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="bi bi-person-check display-5 mr-2"></i>
                    Inspector
                </h5>
                <div class="card-body text-center bg-light" style="background-color: #f5fffa;">
                    <p class="card-text">Total Records : {{ $inspectorCount }}</p>
                    <a href="{{ route('manageInspector') }}" class="btn btn-outline-primary">View Details</a>
                </div>
            </div>
        </div>
  

        <!-- Inspection Card -->
        <!-- <div class="col-md-4 mb-4">
            <div class="card border-success">
                <h5 class="card-header bg-success text-white d-flex align-items-center">
                    <i class="bi bi-clipboard-check display-5 mr-2"></i>
                    Inspection
                </h5>
                <div class="card-body text-center bg-light" style="background-color: #f5fffa;">
                    <p class="card-text">Total Records : {{ $inspectionCount }}</p>
                    <a href="{{ route('manageInspection') }}" class="btn btn-outline-success">View Details</a>
                </div>
            </div>
        </div> -->

        <!-- Visit Card -->
        <div class="col-md-4 mb-4">
            <div class="card border-warning">
                <h5 class="card-header bg-warning text-dark d-flex align-items-center">
                    <i class="bi bi-calendar-event display-5 mr-2"></i>
                    Visit
                </h5>
                <div class="card-body text-center bg-light" style="background-color: #f5fffa;">
                    <p class="card-text">Total Records : {{ $visitCount }}</p>
                    <a href="{{ route('manageVisit') }}" class="btn btn-outline-warning">View Details</a>
                </div>
            </div>
        </div>

        <!-- OPCW Card -->
        <div class="col-md-4 mb-4">
            <div class="card border-danger">
                <h5 class="card-header bg-danger text-white d-flex align-items-center">
                    <i class="bi bi-flag display-5 mr-2"></i>
                    OPCW
                </h5>
                <div class="card-body text-center bg-light" style="background-color: #f5fffa;">
                    <p class="card-text">Total Records : {{ $opcwFaxeCount }}</p>
                    <a href="{{ route('manageOpcw') }}" class="btn btn-outline-danger">View Details</a>
                </div>
            </div>
        </div>

        <!-- Reports Card -->
        <!-- <div class="col-md-4 mb-4">
            <div class="card border-info">
                <h5 class="card-header bg-info text-white d-flex align-items-center">
                    <i class="bi bi-file-earmark-text display-5 mr-2"></i>
                    Reports
                </h5>
                <div class="card-body text-center bg-light" style="background-color: #f5fffa;">
                    <p class="card-text">Total Records : {{ $reportCount }}</p>
                    <a href="{{ route('manageReport') }}" class="btn btn-outline-info">View Details</a>
                </div>
            </div>
        </div> -->
    </div>
</div>
@endsection
