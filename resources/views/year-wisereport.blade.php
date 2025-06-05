@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
@php
    $categoryCount = count($categories ?? []);
    $exportColumns = implode(',', range(0, $categoryCount + 1)); 
@endphp



<!-- Nationality Wise Report Inspectors Table -->
<table class="table table-bordered table-striped" id="myTable" data-export-columns="0, 1, 2, 3, 4, 5, 6">
    <select id="pageLengthSelect" class="form-control mb-3 float-right">
        <option value="5">5</option>
        <option value="10" selected>10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="all">All</option>
    </select>
    <thead >
        <tr class="back-cyan">
            <th scope="col" class="text-center">Sl. No.</th>
            <th scope="col" class="text-center">Year</th>
            <th scope="col" class="text-center">Schedule 1</th>
            <th scope="col" class="text-center">Schedule 2</th>
            <th scope="col" class="text-center">Schedule 3</th>
            <th scope="col" class="text-center">OCPF</th>
            <th scope="col" class="text-center">Total</th>
        </tr>
    </thead>
    <tbody>
    @if ($visits->isNotEmpty())
    @foreach ($visits as $index => $visit)
        <tr >
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="text-center">
                <a href="{{ route('monthly.report', ['year' => $visit->year]) }}" class="text-decoration-none">
                    {{ $visit->year ? $visit->year : '' }}
                </a>
            </td>

            @php
                $types = [
                    1 => 'schedule_1',
                    2 => 'schedule_2',
                    3 => 'schedule_3',
                    4 => 'ocpf',
                ];
                $dateRange = [
                    'dateOfArrivalFrom' => $visit->year . '-01-01',
                    'dateOfArrivalTo' => $visit->year . '-12-31',
                ];
            @endphp

            @foreach ($types as $type => $schedule)
                <td class="text-center">
                    @php
                        $dataFilter = array_merge($dateRange, ['typeOfInspection' => $type]);
                    @endphp

                    <a href="javascript:void(0)" class="GeneralQueryReport" data-filter="{{ json_encode($dataFilter) }}">
                        {{ $visit->$schedule }}
                    </a>
                    &nbsp;
                    <a href="javascript:void(0)" class="float-right GeneralQueryReport" data-filter="{{ json_encode($dataFilter) }}">
                        
                    </a>
                </td>
            @endforeach

            <td class="text-center">{{ $visit->total }}</td>
        </tr>
    @endforeach

    @else
        <tr>
            <td colspan="7">No data available</td>
        </tr>
    @endif
    </tbody>

    <!-- Add this tfoot section after your tbody -->
    <tfoot>
        <tr class="back-cyan">
            <td class="text-center" colspan="2"><strong>Total Year Inspection Records</strong></td>
            <td class="text-center">{{ $categoryTotals['schedule_1'] }}</td>
            <td class="text-center">{{ $categoryTotals['schedule_2'] }}</td>
            <td class="text-center">{{ $categoryTotals['schedule_3'] }}</td>
            <td class="text-center">{{ $categoryTotals['ocpf'] }}</td>
            <td class="text-center">{{ $categoryTotals['total'] }}</td>
        </tr>
    </tfoot>
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


