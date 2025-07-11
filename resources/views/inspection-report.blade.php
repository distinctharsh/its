@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <h5><b>2. Update on Inspection</b></h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped myDataTable">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Type of Inspection</th>
                    <th>Type of Facilities</th>
                    <th>Duration (Days)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Ongoing Section Heading -->
                <tr style="background:#343a40;color:#fff;font-weight:bold;"><td colspan="4">Ongoing</td></tr>
                @php $ongoingFirst = true; @endphp
                @foreach($ongoingBreakdown as $ongoing)
                    @if($ongoing['count'] > 0)
                        @foreach($ongoing['facilityData'] as $i => $facility)
                            <tr>
                                @if($ongoingFirst && $i == 0)
                                    <td rowspan="{{ $ongoing['count'] }}">Ongoing</td>
                                @elseif($i == 0)
                                    <td rowspan="{{ $ongoing['count'] }}"></td>
                                @endif
                                @if($i == 0)
                                    <td rowspan="{{ $ongoing['count'] }}"><b>{{ $ongoing['name'] }}: {{ $ongoing['count'] }}</b></td>
                                @endif
                                <td>
                                    @foreach($inspectionTypes as $type)
                                        {{ $type->type_name }}: <b>{{ $facility['facilities'][$type->type_name] }}</b><br>
                                    @endforeach
                                </td>
                                <td><b>{{ $facility['duration'] }}</b></td>
                            </tr>
                        @endforeach
                        @php $ongoingFirst = false; @endphp
                    @endif
                @endforeach
                <!-- Next Inspection Section Heading -->
                <tr style="background:#343a40;color:#fff;font-weight:bold;"><td colspan="4">Next Inspection</td></tr>
                @php $nextFirst = true; @endphp
                @foreach($nextBreakdown as $next)
                    @if($next['count'] > 0)
                        @foreach($next['facilityData'] as $i => $facility)
                            <tr>
                                @if($nextFirst && $i == 0)
                                    <td rowspan="{{ $next['count'] }}">Next Inspection</td>
                                @elseif($i == 0)
                                    <td rowspan="{{ $next['count'] }}"></td>
                                @endif
                                @if($i == 0)
                                    <td rowspan="{{ $next['count'] }}"><b>{{ $next['name'] }}: {{ $next['count'] }}</b></td>
                                @endif
                                <td>
                                    @foreach($inspectionTypes as $type)
                                        {{ $type->type_name }}: <b>{{ $facility['facilities'][$type->type_name] }}</b><br>
                                    @endforeach
                                </td>
                                <td><b>{{ $facility['duration'] }}</b></td>
                            </tr>
                        @endforeach
                        @php $nextFirst = false; @endphp
                    @endif
                @endforeach
                <!-- Cumulative -->
                <tr style="background:#343a40;color:#fff;font-weight:bold;"><td colspan="4">Cumulative during the calendar year up to date with details</td></tr>
                <tr>
                    @foreach($inspectionTypes as $type)
                        <td>{{ $type->type_name }}<br><b>{{ $cumulativeTotals[$type->type_name] ?? 0 }}</b></td>
                    @endforeach
                    @if(count($inspectionTypes) < 4)
                        @for($i = count($inspectionTypes); $i < 4; $i++)
                            <td></td>
                        @endfor
                    @endif
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection 