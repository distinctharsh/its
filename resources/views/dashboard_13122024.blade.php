@extends('layouts.layout')

@section('content')
<div class="container my-5">
    <div class="row pt-4" >
        <!-- Inspector Card -->
     
        <div class="col-md-4 mb-4 d-flex justify-content-center">
            <div class="card " style="height: 280px; width: 250px; border-radius: 20px;">
                <h5 class="card-header  text-white d-flex align-items-center justify-content-center" style="background: #de6fa4;  border-radius: 20px 20px 0px 0px;">
                    <!-- <i class="bi bi-person-check display-5 mr-2"></i> -->
                    <i class="fa-solid fa-user-check mr-2"></i>
                    Inspector
                </h5>
                <div class="card-body text-center " style="background-color: #e1e5f3; border-radius: 0px 0px 20px 20px;">
                    <p class="card-text mt-3"> <span style="color: #242c52;">Total <br>Records</span>  <br><br> <strong> {{ $inspectorCount }} </strong></p>
                    <a href="{{ route('manageInspector') }}" class="btn btn-outline-primary dashboard-view-detail-btn" >View Details</a>
                </div>
            </div>
        </div>
  

     

        <!-- Visit Card -->
        <div class="col-md-4 mb-4 d-flex justify-content-center">
            <div class="card " style="height: 280px; width: 250px; border-radius: 20px;">
                <h5 class="card-header  text-white d-flex align-items-center justify-content-center" style="background: #dab262;  border-radius: 20px 20px 0px 0px;">
                    <!-- <i class="bi bi-calendar-event display-5 mr-2"></i> -->
                    <i class="fa-solid fa-calendar-days mr-2"></i>
                    Visit
                </h5>
                <div class="card-body text-center " style="background-color: #f7f4ed; border-radius: 0px 0px 20px 20px;">
                    <p class="card-text mt-3"><span style="color: #5d4a2f;">Total <br>Records</span>  <br> <br> <strong> {{ $visitCount }} </strong></p>
                    <a href="{{ route('manageVisit') }}" class="btn btn-outline-warning" >View Details</a>
                </div>
            </div>
        </div>

        <!-- OPCW Card -->
        <div class="col-md-4 mb-4 d-flex justify-content-center">
            <div class="card " style="height: 280px; width: 250px; border-radius: 20px;">
                <h5 class="card-header text-white d-flex align-items-center justify-content-center" style="background: #d97d58;  border-radius: 20px 20px 0px 0px;">
                    <!-- <i class="bi bi-flag display-5 mr-2"></i> -->
                    <i class="fa-solid fa-flag mr-2"></i>
                    OPCW
                </h5>
                <div class="card-body text-center " style="background-color: #f7efee; border-radius: 0px 0px 20px 20px;">
                    <p class="card-text mt-3"><span style="color: #4e2a22;">Total <br>Records</span>  <br> <br> <strong> {{ $opcwFaxeCount }} </strong></p>
                    <a href="{{ route('manageOpcw') }}" class="btn btn-outline-danger" >View Details</a>
                </div>
            </div>
        </div>

        <!-- Reports Card -->
        
        <div class="col-md-4 mb-4 d-flex justify-content-center">
            <div class="card" style="height: 280px; width: 250px; border-radius: 20px;">
                <h5 class="card-header text-white d-flex align-items-center justify-content-center" 
                    style="background: #2C786C;  border-radius: 20px 20px 0 0;">
                    <i class="fa-solid fa-flag mr-2"></i>
                    Sequential <br/> Inspection
                </h5>
                <div class="card-body text-center" style="background-color: #f7efee; border-radius: 0 0 20px 20px;">
                    <p class="card-text mt-3">
                        <span style="color: #4e2a22;">Total <br>Records</span>  <br><br>
                        <strong>{{ $SequentialInspection }}</strong>
                    </p>
                    <a href="{{ route('listInspectorsdash.get', ['id' => $id]) }}" class="btn btn-outline-success">
                        View Details
                    </a>
                </div>
            </div>
        </div>



    </div>
</div>
@endsection
