@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <!-- <h5><b>2. Update on Inspection</b></h5> -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped myDataTable">
            <thead>
                <tr>
                    <th style="width: 20%;">Status</th>
                    <th style="width: 25%;">Type of Inspection</th>
                    <th style="width: 35%;">Type of facilities</th>
                    <th style="width: 20%;">Duration</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Ongoing</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Next Inspection</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Cumulative during the calendar year up to date with details</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection 