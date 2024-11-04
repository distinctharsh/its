@extends('layouts.layout')

@section('content')
<div class="container my-5">
    <div class="row pt-4" style="background : #f5f1f2;">
        <!-- Inspector Card -->
     
        <div class="col-md-4 mb-4 d-flex justify-content-center">
            <div class="card " style="height: 280px; width: 250px; border-radius: 20px;">
                <h5 class="card-header  text-white d-flex align-items-center justify-content-center" style="background: #de6fa4;  border-radius: 20px 20px 0px 0px;">
                    <i class="bi bi-person-check display-5 mr-2"></i>
                    Inspector
                </h5>
                <div class="card-body text-center " style="background-color: #e1e5f3; border-radius: 0px 0px 20px 20px;">
                    <p class="card-text mt-3"> <span style="color: #242c52;">Total <br>Records</span>  <br><br> <strong> {{ $inspectorCount }} </strong></p>
                    <a href="{{ route('manageInspector') }}" class="btn btn-outline-primary" style="border-radius: 10px; background: #fff; border-color: #a4b4ca;">View Details</a>
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
        <div class="col-md-4 mb-4 d-flex justify-content-center">
            <div class="card " style="height: 280px; width: 250px; border-radius: 20px;">
                <h5 class="card-header  text-white d-flex align-items-center justify-content-center" style="background: #dab262;  border-radius: 20px 20px 0px 0px;">
                    <i class="bi bi-calendar-event display-5 mr-2"></i>
                    Visit
                </h5>
                <div class="card-body text-center " style="background-color: #f7f4ed; border-radius: 0px 0px 20px 20px;">
                    <p class="card-text mt-3"><span style="color: #5d4a2f;">Total <br>Records</span>  <br> <br> <strong> {{ $visitCount }} </strong></p>
                    <a href="{{ route('manageVisit') }}" class="btn btn-outline-warning" style="border-radius: 10px; background: #fff; border-color: #a4b4ca;">View Details</a>
                </div>
            </div>
        </div>

        <!-- OPCW Card -->
        <div class="col-md-4 mb-4 d-flex justify-content-center">
            <div class="card " style="height: 280px; width: 250px; border-radius: 20px;">
                <h5 class="card-header text-white d-flex align-items-center justify-content-center" style="background: #d97d58;  border-radius: 20px 20px 0px 0px;">
                    <i class="bi bi-flag display-5 mr-2"></i>
                    OPCW
                </h5>
                <div class="card-body text-center " style="background-color: #f7efee; border-radius: 0px 0px 20px 20px;">
                    <p class="card-text mt-3"><span style="color: #4e2a22;">Total <br>Records</span>  <br> <br> <strong> {{ $opcwFaxeCount }} </strong></p>
                    <a href="{{ route('manageOpcw') }}" class="btn btn-outline-danger" style="border-radius: 10px; background: #fff; border-color: #a4b4ca;">View Details</a>
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
