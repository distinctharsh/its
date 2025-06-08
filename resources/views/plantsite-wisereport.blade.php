@extends('layouts.layout')

@section('content')

<div class=" center-block expand-box text-white mb-3">
        <div class="panel-group back-cyan2" id="accordion" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingOne">
                    <h5 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne" class="btn-filter">
                            <strong>Filter</strong>
                        </a>
                    </h5>
                </div>

                <div id="collapseOne" class="panel-collapse collapse in text-dark" role="tabpanel" aria-labelledby="headingOne">
                    <div class="panel-body">

                        <div class="row ml-3 mb-3 pt-3 justify-content-between">
                            <form method="POST" action="{{ route('plantsitewiseReport.post') }}" class="w-100">
                                @csrf
                                <!-- Second Row -->
                                <div class="row mr-3">
                                    <div class="col-md-4">
                                        <!-- State Filter -->
                                        <div class="mb-3">
                                            <label for="state">State</label>
                                            <select name="state[]" id="state" class="form-control" multiple>
                                                <option value="">Select State</option>
                                                @foreach ($allStates as $state)
                                                <option value="{{ $state->id }}"
                                                    @if(in_array($state->id, old('state', (array)$stateId))) selected @endif>
                                                    {{ $state->state_name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <!-- Date of Arrival Filter -->
                                        <div class="mb-3">
                                            <label for="dateOfArrival">Date of Arrival (From - To)</label>
                                            {{-- <input type="date" name="dateOfArrival" id="dateOfArrival" class="form-control" value="{{ old('dateOfArrival', $dateOfArrival ? \Carbon\Carbon::parse($dateOfArrival)->format('Y-m-d') : '') }}"> --}}
                                            <div class="d-flex flex-column flex-md-row">
                                        
                                                <input type="date" name="dateOfArrivalFrom" id="dateOfArrivalFrom" class="form-control " value="{{ old('dateOfArrivalFrom', $dateOfArrivalFrom ? \Carbon\Carbon::parse($dateOfArrivalFrom)->format('Y-m-d') : '') }}" placeholder="From">
                                                
                                                <!-- To Date Input -->
                                                <input type="date" name="dateOfArrivalTo" id="dateOfArrivalTo" class="form-control" value="{{ old('dateOfArrivalTo', $dateOfArrivalTo ? \Carbon\Carbon::parse($dateOfArrivalTo)->format('Y-m-d') : '') }}" placeholder="To">
                                            </div>
                                        </div>
                                    </div>
                                  
                                </div>
                                <!-- Submit Button Row -->
                                <div class="row">
                                    <div class="col-12 text-center mt-3 mb-3">
                                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

<div class="container-fluid mt-4">
    @php
    $typeCount = count($inspectionTypes);
    $exportColumns = implode(',', range(0, $typeCount + 1)); // +1 for state column
    @endphp

    <!-- State-Wise Report Table -->
    <table class="table table-bordered table-striped " id="myTable"  data-export-columns="0, 1, 2, 3, 4, 5, 6">
        <select id="pageLengthSelect" class="form-control mb-3 float-right">
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="all" >All</option>
        </select>
        <thead >
            <tr class="back-cyan">
                <th scope="col" class="text-center">Sl. No.</th>
                <th scope="col" class="text-center">Plant Site</th>
                @foreach($inspectionTypes as $type)
                    <th scope="col" class="text-center">{{ $type->type_name }}</th>
                @endforeach
                <th scope="col" class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($finalData as $index => $data)
            <tr >
                <td class="text-center">{{ $index + 1 }}</td>
                <td><a href="javascript:void(0)" class="GeneralQueryReport" data-filter="{'siteCode':'{{ $data['id'] }}'}">{{ $data['site_code'] }}</a></td>
                @php
                $totalVisits = 0;
                @endphp
                @foreach($inspectionTypes as $type)
                    <td class="text-center">
                        @php
                            $visitCount = $data[$type->type_name] ?? 0;
                            $visitCountTypeId = $data[$type->type_name.'_id'] ?? 0;
                        @endphp
                        @if($visitCount > 0)
                            <a href="javascript:void(0)" class="GeneralQueryReport" data-filter="{'siteCode':'{{ $data['id'] }}','typeOfInspection':'{{ $visitCountTypeId }}'}">
                                {{ $visitCount }}
                            </a>
                            &nbsp;
                            <a href="javascript:void(0)" class="float-right GeneralQueryReport" data-filter="{'siteCode':'{{ $data['id'] }}','typeOfInspection':'{{ $visitCountTypeId }}'}">
                                 
                            </a>
                        @else
                            0
                        @endif
                    </td>
                    @php
                    $totalVisits += $visitCount;
                    @endphp
                @endforeach
                <td class="text-center">{{ $totalVisits }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection


@push('script')

<script>
$(document).ready(function () {
    $(".GeneralQueryReport").on("click", function () {
        let data = $(this).data("filter");
        if (!data) {
            console.error("No data-filter attribute found!");
            return;
        }
        if (typeof data === "string") {
            data = JSON.parse(data.replace(/'/g, '"'));
        }
        let $form = $("<form>", {
            action: "{{ url('list-inspectors') }}", 
            method: "POST"
        });
        $form.append(
            $("<input>", {
                type: "hidden",
                name: "_token",
                value: "{{ csrf_token() }}" 
            })
        );
        $.each(data, function (key, value) {
            $form.append(
                $("<input>", {
                    type: "hidden",
                    name: key + "[0]", 
                    value: value
                })
            );
        });
        $("body").append($form);
        $form.submit();
    });
});
</script>

<script>


</script>
@endpush