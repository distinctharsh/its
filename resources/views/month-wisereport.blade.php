@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
<h1>Year {{ $year }}</h1>


    <!-- Page Length Selector -->
    <select id="pageLengthSelect" class="form-control mb-3 float-end w-auto">
        <option value="6">6</option>
        <option value="12" selected>12</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="all">All</option>
    </select>

    <!-- Monthly Report Table -->
    <table class="table table-bordered table-striped myDataTable" id="myTable" data-export-columns="0, 1, 2, 3, 4, 5, 6">
        <thead class="table-dark">
            <tr>
                <th scope="col" class="text-center">Sl. No.</th>
                <th scope="col" class="text-center">Month</th>
                <th scope="col" class="text-center">Schedule 1</th>
                <th scope="col" class="text-center">Schedule 2</th>
                <th scope="col" class="text-center">Schedule 3</th>
                <th scope="col" class="text-center">OCPF</th>
                <th scope="col" class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($monthlyReport) && count($monthlyReport) > 0)
            @foreach($monthlyReport as $index => $report)
    <tr>
        <td class="text-center">{{ $index + 1 }}</td>

        @php
            $firstDate = $year.'-'.sprintf('%02d', $report['arrival_month']).'-01';
            $lastDate = \Carbon\Carbon::parse($firstDate)->endOfMonth()->format('Y-m-d');
        @endphp
        
        <td class="text-center">
             @if($report['total']==0)
            {{ $report['month_name'] }}
    
             @else

                <a href="javascript:void(0)" class="float-right GeneralQueryReport" data-filter="{'dateOfArrivalFrom':'{{$firstDate}}','dateOfArrivalTo':'{{$lastDate}}'}">
                    {{ $report['month_name'] }}
                </a>
    
             @endif
           
        </td>

        @php       
            $types = [
                1 => 'schedule_1',
                2 => 'schedule_2',
                3 => 'schedule_3',
                4 => 'ocpf',
            ];
            $dateRange = [
                'dateOfArrivalFrom' => $firstDate ,
                'dateOfArrivalTo' => $lastDate,
            ];
        @endphp

        @foreach ($types as $type => $schedule)
            <td class="text-center">
                @php
                    $dataFilter = array_merge($dateRange, ['typeOfInspection' => $type]);
                @endphp

                @if($report[$schedule])
                    <a href="javascript:void(0)" class="GeneralQueryReport" data-filter="{{ json_encode($dataFilter) }}">
                        {{ $report[$schedule] }}
                    </a>
                    &nbsp;
                    <a href="javascript:void(0)" class="float-right GeneralQueryReport" data-filter="{{ json_encode($dataFilter) }}">
                         
                    </a>
                @else
                    0
                @endif

            </td>
        @endforeach
        <!-- ____________________________________________________________ -->
        
        <td class="text-center">{{ $report['total'] }}</td>
    </tr>
@endforeach
  
            @else
                <tr>
                    <td colspan="7" class="text-center">No data available</td>
                </tr>
            @endif
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
                const adjustedKey = (key === 'dateOfArrivalFrom' || key === 'dateOfArrivalTo') ? key : `${key}[0]`;
                $form.append(
                    $("<input>", {
                        type: "hidden",
                        name: adjustedKey, 
                        value: value
                    })
                );
            });
            $("body").append($form);
            $form.submit();
        });
    });
</script>
@endpush

