@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    @php
    $typeCount = count($inspectionTypes);
    $exportColumns = implode(',', range(0, $typeCount + 1)); // +1 for state column
    @endphp



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
                            <form method="POST" action="{{ route('stateWiseReport.post') }}" class="w-100">
                                @csrf
                                <div class="row mr-3">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="dateOfArrival">Date of Arrival (From - To)</label>
                                            <div class="d-flex flex-column flex-md-row">
                                                <input type="date" 
                                                    name="dateOfArrivalFrom" 
                                                    id="dateOfArrivalFrom" 
                                                    class="form-control" 
                                                    value="{{ old('dateOfArrivalFrom', isset($dateOfArrivalFrom) ? \Carbon\Carbon::parse($dateOfArrivalFrom)->format('Y-m-d') : '') }}" 
                                                    placeholder="From">

                                                <input type="date" 
                                                    name="dateOfArrivalTo" 
                                                    id="dateOfArrivalTo" 
                                                    class="form-control" 
                                                    value="{{ old('dateOfArrivalTo', isset($dateOfArrivalTo) ? \Carbon\Carbon::parse($dateOfArrivalTo)->format('Y-m-d') : '') }}" 
                                                    placeholder="To">
                                            </div>

                                        </div>
                                    </div>
                                </div>

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


    <!-- State-Wise Report Table -->
    <table class="table table-bordered table-striped" id="myTable"  >
        <select id="statePageLengthSelect" class="form-control mb-3 float-right">
            <option value="5">5</option>
            <option value="10" >10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="all" selected>All</option>
        </select>
        <thead >
            <tr class="back-cyan">
                <th scope="col" class="text-center">Sl. No.</th>
                <th scope="col" class="text-center">State</th>
                @foreach($inspectionTypes as $type)
                    <th scope="col" class="text-center">{{ $type->type_name }}</th>
                @endforeach
                <th scope="col" class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($finalData as $index => $data)
            <tr class="back-cyan">
                <td class="text-center">{{ $index + 1 }}</td>
                <td><a href="{{ route('plantsitewiseReport.post', ['state' => $data['state_id']]) }}">{{ $data['state'] }}</a></td>
                @php
                $totalVisits = 0;
                @endphp
                @foreach($inspectionTypes as $type)
                <td class="text-center">
                    @php
                    $visitCount = $data[$type->type_name] ?? 0;
                    @endphp
                    @if($visitCount > 0)
                        @php
                        // Check if the inspection type is a single value or an array
                        $typeOfInspection = is_array($inspectionTypes) ? json_encode(range(1, count($inspectionTypes))) : json_encode([$type->id]);
                        @endphp
                        <a href="javascript:void(0)" class="GeneralQueryReport" data-filter='{ "state": "{{ $data['state_id'] }}", "typeOfInspection": {{ $typeOfInspection }} }'>
                            {{ $visitCount }}
                            
                        </a>
                    @else
                        0
                    @endif
                </td>

                    @php
                    $totalVisits += $visitCount;
                    @endphp
                @endforeach
                @php
                    $typeIds = $inspectionTypes->pluck('id')->implode(',');
                @endphp
                <td class="text-center">
                    @if($totalVisits > 0)
                        <a href="javascript:void(0)" class="GeneralQueryReport" data-filter='{ "state": "{{ $data['state_id'] }}", "typeOfInspection": {{ json_encode(range(1, count($inspectionTypes))) }} }'>
                            {{ $totalVisits }}
                         
                        </a>
                    @else
                        0
                    @endif
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection


@push('script')

<script>
  document.addEventListener("DOMContentLoaded", function () {
      var tableHeaders = document.querySelectorAll("#myTable th");
      var columns = [];
      tableHeaders.forEach(function (header, index) {
          if (!header.querySelector('.btn') && !header.classList.contains('actions-column')) {
              columns.push(index);
          }
      });
      var table = document.getElementById("myTable");
      table.setAttribute("data-export-columns", columns.join(", "));
  });
</script>


<script>
$(document).ready(function () {
        $(".GeneralQueryReport").on("click", function () {
            let data = $(this).data("filter");
            if (!data) {
                console.error("No data-filter attribute found!");
                return;
            }

            if (typeof data === "string") {
                try {
                    data = JSON.parse(data); 
                } catch (e) {
                    console.error("Error parsing JSON:", e);
                    return;
                }
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
                if (Array.isArray(value)) {
                    value.forEach(val => {
                        $form.append(
                            $("<input>", {
                                type: "hidden",
                                name: key + "[]", 
                                value: val
                            })
                        );
                    });
                } else {
                    $form.append(
                        $("<input>", {
                            type: "hidden",
                            name: key + "[0]", 
                            value: value
                        })
                    );
                }

            });

            $("body").append($form);
            $form.submit();
        });
    });
</script>


<script>
    $(document).ready(function() {
  
    const exportColumnsAttrs = $('.stateTable').data('exports-column');
      const columnsToExports = exportColumnsAttrs ? exportColumnsAttrs.split(',').map(Number) : [];
    

    var reportTable = $('.stateTable').DataTable({
        dom: 'Bfrtip',
        pageLength: -1,
        language: {
            emptyTable: "No Record Found",
            zeroRecords: "No matching records found.",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
        },
        buttons: [
            {
                extend: 'copyHtml5',
                text: '<i class="fa-solid fa-copy"></i>',
                titleAttr: 'Copy to clipboard',
                exportOptions: {
                    columns: columnsToExports
                }
            },
            {
                extend: 'excelHtml5',
                text: '<i class="fa-solid fa-file-excel"></i>',
                titleAttr: 'Export to Excel',
                exportOptions: {
                    columns: columnsToExports
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa-solid fa-file-pdf"></i>',
                titleAttr: 'Export to PDF',
                exportOptions: {
                    columns: columnsToExports
                },
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                text: '<i class="fa-solid fa-print"></i>',
                titleAttr: 'Print the table',
                exportOptions: {
                    columns: columnsToExports
                },
                
            }
        ],
        responsive: true,
        autoWidth: true,
    });

    $('#statePageLengthSelect').on('change', function() {
            const newLength = $(this).val() === "all" ? -1 : parseInt($(this).val());
            reportTable.page.len(newLength).draw(false);
        });

    // Test the DataTable with no filters applied (for debugging purposes)
    // This should show all records without any filtering
    reportTable.draw();  // Force the table to redraw and show all data
});

</script>
@endpush