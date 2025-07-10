<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="UTF-8">

  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="captcha-url" content="{{ route('captcha') }}">

  <title>ITS | Inspector Tracking Software</title>

  <!-- Google Font: Source Sans Pro -->
  <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"> -->
  <!-- Font Awesome -->

  <!-- <link rel="stylesheet" href="{{ url('assets/vendor/source_code_pro/SourceCodePro-Black.ttf') }}"> -->


  <link rel="stylesheet" href="{{ url('assets/vendor/buttons/buttons.dataTables.min.css') }}">
  <link rel="stylesheet" href="{{ url('assets/vendor/fontawesome/css/all.min.css') }}">

  <!-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css"> -->


  <!-- Theme style -->
  <link rel="stylesheet" href="{{ url('theme/css/theme.css') }}">
  <link rel="stylesheet" href="{{ url('css/app.css') }}">
  <link rel="stylesheet" href="{{ url('css/notification.css') }}">

  <link rel="stylesheet" href="{{ url('assets/vendor/datatables/datatables.min.css') }}">

<link href="{{ url('assets/vendor/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

  <link href="{{ url('assets/vendor/jquery-ui/themes/base/jquery-ui.css') }}" rel="stylesheet">

  <style>
    /* Filter */




    .body{
      -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }


    .panel-heading {
      padding: 0;
      border: 0;
    }

    .panel-title>a,
    .panel-title>a:active {
      display: block;
      padding: 15px;
      color: #fff;

      text-decoration: none;
    }

    .panel-heading a:before {
      content: "\25BC";
      float: right;
      transition: all 0.5s;
    }

    .panel-heading.active a:before {
      content: "\25B2";
    }

    .expand-box {
      background-color: #6c757d;
      box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;
    }

    .btn-filter {
      color: #333333;
    }

    .panel-body {
      background-color: #f3f3f3;
    }


    .select2-selection__rendered {
      line-height: 24px !important;
    }

    .select2-selection__clear {
      margin-top: -3px;
    }

    .select2-selection__choice__display {
      color: #000 !important;
    }

    .select2-container .select2-selection--single {
      height: 33px !important;
    }

    .select2-container--default {
      width: 100% !important;
    }


    .select2-search__field:focus{
      border: none !important;
    }


    .dt-search{
      float: right;
    }


    .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
      padding-left: 8px !important;
    }

    .dt-buttons{
      margin-left: 105px !important;
    }


    #myTable, .myDataTable{
      position: relative;
    }

    #pageLengthSelect, #loginPageLengthSelect, #activityPageLengthSelect, #reportPageLengthSelect, #statePageLengthSelect{
      width: 80px;
      position: absolute;
    }


    /* .odd{
      display: none !important;
    } */

    .select2-container .select2-selection__placeholder {
        color: #495057 !important;
    }



    #sequentialPieChart {
        max-height: 500px;  /* Maximum height */
        width: 100%;        /* Adjust based on screen size */
        height: auto;       /* Auto height adjustment */
    }

    .text-red-500 {
	    color: red;
	}


  .back-cyan{
    background-color: #C4DFE6;
    color: #000;
  }
  .back-cyan2{
    background-color:#66A5AD;
    color: #000;
  }



  .card.draggable {
    position: fixed;
    left: 9px; 
    top: 85vh; 
    z-index: 999999; 
    width: auto; 
    background-color: #fff; 
    border-radius: 10px; 
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
  }

  </style>
</head>

<body class="hold-transition sidebar-mini">
  <div class="container-fluid p-0 m-0">
    <!-- Navbar -->
    @include('layouts.header')
  </div>
  <div class="wrapper ">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-dark bg-dark" style="@if(!auth()->user()->hasRole('Admin')) margin-left: 0 !important; @endif">
    @if(!auth()->user()->hasRole('Admin'))
      <a href="/dashboard" class="brand-link">
        <img src="{{ url('images/its.png') }}" alt="ITS Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">&nbsp;</span>
      </a>
      @endif
      @if(auth()->user()->hasRole('Admin'))
      <!-- Left navbar links -->
      @include('layouts.left-nav')
      @endif

      <!-- Right navbar links -->

      @include('layouts.right-nav')

    </nav>
    <!-- /.navbar -->

    @if(auth()->user()->hasRole('Admin'))
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="/dashboard" class="brand-link">
        <img src="{{ url('images/its.png') }}" alt="ITS Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">&nbsp;</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="info">
            <a href="#" class="d-block">Administrator</a>
          </div>
        </div>

        <!-- Sidebar Menu -->
        @include('layouts.sidebar')

        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

    @endif

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" style="background-color: #fff; @if(!auth()->user()->hasRole('Admin')) margin-left: 0 !important; @endif">

      <!-- Main content -->
      <div class="content-header">
        <div class="container-fluid">
          @include('layouts.breadcrumb')
          @yield('content')
        </div><!-- /.container-fluid -->
      </div>
    </div>

    <!-- Main footer -->
    <footer class="main-footer">

      <strong>Copyright &copy; {{ date('Y')}} <a href="https://nacwc.gov.in/">NACWC</a>.</strong> All rights reserved.
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->

<!-- ______________________________________________________________________________________ -->

@php
    $protection_enabled = \App\Models\Setting::where('key', 'protection_enabled')->value('value') ?? '1';
@endphp
@if($protection_enabled == '1')
<script>
    // Disable Right Click
    document.addEventListener('contextmenu', e => e.preventDefault());

    // Disable Text Selection, Dragging, and Copying
    ['selectstart', 'dragstart', 'copy'].forEach(evt => {
        document.addEventListener(evt, e => {
            e.preventDefault();
            if (evt === 'copy') alert('Copying is disabled on this page.');
        });
    });

    // Disable Keyboard Shortcuts
    document.addEventListener('keydown', function (e) {
        const key = e.key.toLowerCase();

        // Block Function Keys (F1 to F12)
        const blockedFunctionKeys = ['f1', 'f2', 'f3', 'f4', 'f5', 'f6', 'f7', 'f8', 'f9', 'f10', 'f11', 'f12'];

        // Disable F1 to F12 keys regardless of the Fn key
        if (blockedFunctionKeys.includes(key)) {
            e.preventDefault();  // Disable F1 to F12
        }

        // Block Developer Tools & View Source Shortcuts
        const blockedCombos = [
            { key: 'f12' },
            { key: 'f11' },
            { key: 'f10' },
            { key: 'f9' },
            { key: 'f8' },
            { key: 'f7' },
            { key: 'f6' },
            { key: 'f5' },
            { key: 'f4' },
            { key: 'f3' },
            { key: 'f2' },
            { key: 'f1' },
            { ctrl: true, shift: true, key: 'i' },
            { ctrl: true, shift: true, key: 'j' },
            { ctrl: true, key: 'u' },
            { ctrl: true, key: 's' }, // Save Page
            { ctrl: true, key: 'p' }, // Print
            { ctrl: true, key: 'c' }, // Copy
            { ctrl: true, key: 'a' }, // Select All
        ];

        for (const combo of blockedCombos) {
            if (
                (combo.key === key) &&
                (!!combo.ctrl === e.ctrlKey) &&
                (!!combo.shift === e.shiftKey)
            ) {
                e.preventDefault();
                if (key === 'c') alert('Copying is disabled on this page.');
                if (key === 'p') alert('Printing is disabled on this page.');
            }
        }

        // Disable PrintScreen Key (and other potential screenshot key combinations)
        if (e.key === 'PrintScreen') {
            navigator.clipboard.writeText('').catch(() => {});
            alert("Screenshots are restricted!");

            // Blur + Black Overlay to visually obscure the content
            document.body.style.filter = 'blur(8px)';
            const overlay = document.createElement('div');
            overlay.style.position = 'fixed';
            overlay.style.top = 0;
            overlay.style.left = 0;
            overlay.style.width = '100vw';
            overlay.style.height = '100vh';
            overlay.style.backgroundColor = 'black';
            overlay.style.zIndex = 9999;
            overlay.style.opacity = 1;
            document.body.appendChild(overlay);
            setTimeout(() => {
                document.body.removeChild(overlay);
                document.body.style.filter = 'none';
            }, 1500);
        }
    });

    // Prevent Printing
    window.onbeforeprint = function () {
        alert("Printing is disabled!");
        return false;
    };

    // Optional: Prevent Zoom In/Out (Ctrl + '+' or '-')
    window.addEventListener('keydown', function (e) {
        if (e.ctrlKey && (e.key === '+' || e.key === '-' || e.key === '=')) {
            e.preventDefault();
        }
    });
</script>
@endif


<script>
    document.addEventListener('keydown', function (e) {
        // Block Windows Key (Win) + Key Combinations
        if (e.key === 'Meta') { // 'Meta' is the name for the Windows key in most browsers
            // Prevent specific Windows key combinations
            if (e.ctrlKey || e.altKey || e.shiftKey) {
                e.preventDefault();
            }

            // Win + R (Run dialog)
            if (e.key === 'r') {
                e.preventDefault();
            }

            // Win + D (Show desktop)
            if (e.key === 'd') {
                e.preventDefault();
            }

            // Win + E (Open File Explorer)
            if (e.key === 'e') {
                e.preventDefault();
            }

            // Win + L (Lock the screen)
            if (e.key === 'l') {
                e.preventDefault();
            }

            // Win + Tab (Task view)
            if (e.key === 'Tab') {
                e.preventDefault();
            }

            // Win + P (Switch display modes)
            if (e.key === 'p') {
                e.preventDefault();
            }

            // Win + S (Search)
            if (e.key === 's') {
                e.preventDefault();
            }
        }
    });
</script>


<script>
    document.addEventListener('keydown', function (e) {
        // Check for the "Windows key" + "Shift" + "S" combination
        if (e.key === 'S' && e.shiftKey && e.metaKey) {
            // Prevent the default action (which is to open the Snipping Tool)
            e.preventDefault();
            alert('Screenshot tool is disabled on this page!');
        }

        // Optionally, you can add more combinations to block
        // For example, disabling Win + Shift + another key
        if (e.metaKey && e.shiftKey && e.key === 'A') {
            e.preventDefault();
            alert('Win + Shift + A is blocked!');
        }
    });
</script>

















<script src="{{ url('assets/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ url('assets/vendor/buttons/dataTables.buttons.min.js') }}"></script>
<!-- jQuery -->

<!-- DataTables core -->
<script src="{{ url('assets/vendor/datatables/dataTables.min.js') }}"></script>

<!-- Include Bootstrap 4 -->
<script src="{{ url('theme/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- Buttons Extension -->
<script src="{{ url('assets/vendor/buttons/buttons.html5.min.js') }}"></script>
<script src="{{ url('assets/vendor/buttons/buttons.print.min.js') }}"></script>

<!-- Export dependencies -->
<script src="{{ url('assets/vendor/jszip/jszip.min.js') }}"></script>
<script src="{{ url('assets/vendor/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ url('assets/vendor/pdfmake/vfs_fonts.js') }}"></script>


<!-- ______________________________________________________________________________________ -->

 <script src="{{ url('assets/vendor/sweetalert2/sweetalert2.min.js') }}"></script>
 
 <script src="{{ url('assets/vendor/jquery-ui/jquery-ui.min.js') }}"></script>

 <!-- JS ZIP -->

 <script src="{{ url('assets/vendor/html2canvas/html2canvas.min.js') }}"></script>
 <script src="{{ url('assets/vendor/xlsx/xlsx.full.min.js') }}"></script>
 <script src="{{ url('assets/vendor/html2pdf/html2pdf.bundle.js') }}"></script>
 <script src="{{ url('assets/vendor/jspdf/jspdf.umd.min.js') }}"></script>

  <!-- ITS App JS -->
  <script src="{{ url('theme/js/theme.min.js') }}"></script>
  <script src="{{ url('js/app.js') }}"></script>

  <!-- Include Chart.js -->
  <script src="{{ url('assets/vendor/buttons/chart.js') }}"></script>


  <script>
    $(document).ready(function() {
      $('.select2').on('select2:open', function () {
          $('body').css('overflow-x', 'hidden');
      });

      $('.select2').on('select2:close', function () {
          $('body').css('overflow-x', '');
      });

      document.addEventListener("DOMContentLoaded", function() {
        const emptyCells = document.querySelectorAll('td.dt-empty');

        emptyCells.forEach(cell => {
            if (cell.textContent.trim() === "1") {
                // Replace "1" with "No Record Found"
                cell.textContent = "No Record Found";
            }
        });
      });

      function replaceDashContent() {
        // Replace Ã¢â‚¬â€œ with en dash (–) for all elements that might contain text
        $('body').find('*').each(function() {
          if ($(this).children().length === 0) { // Ensure it's a leaf node (no children)
            let text = $(this).text();
            if (text.indexOf('Ã¢â‚¬â€œ') !== -1) {
              $(this).text(text.replace(/Ã¢â‚¬â€œ/g, '–')); // Replace with en dash
            }
          }
        });
      }

      // Call the function when document is ready
      replaceDashContent();

      $('#sidebarToggle').click(function() {
        $('#sidebar').toggleClass('collapsed');
      });


      $(".datepicker").datepicker({
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        changeYear: true
      });

      var csrfToken = $('meta[name="csrf-token"]').attr('content');

      const tableId = 'myTable';
      const exportColumnsAttr = $(`#${tableId}`).data('export-columns');
      const columnsToExport = exportColumnsAttr ? exportColumnsAttr.split(',').map(Number) : [];

      let table = $(`#${tableId}`).DataTable({
        dom: 'Bfrtip',
        pageLength: 10,
        language: {
          emptyTable: "No Record Found",
          zeroRecords: "No matching records found.",
          info: "Showing _START_ to _END_ of _TOTAL_ entries",
          infoFiltered: "",
        },
        buttons: [{
            extend: 'copyHtml5',
            text: '<i class="fa-solid fa-copy"></i>',
            titleAttr: 'Copy to clipboard',
            exportOptions: {
              columns: columnsToExport,
              modifier: {
                page: 'all'
              },
              format: {
                body: function(data, row, column) {
                    return processDataForExport(data, row, column);
                }
              }
            }
          },
          {
            extend: 'excelHtml5',
            text: '<i class="fa-solid fa-file-excel"></i>',
            titleAttr: 'Export to Excel',
            exportOptions: {
              columns: columnsToExport,
              modifier: {
                page: 'all'
              },
              format: {
                body: function(data, row, column) {
                    return processDataForExport(data, row, column);
                }
              }
            }
          },
          {
            extend: 'pdfHtml5',
            text: '<i class="fa-solid fa-file-pdf"></i>',
            titleAttr: 'Export to PDF',
            orientation: 'landscape',
            pageSize: 'A4',
            exportOptions: {
              columns: columnsToExport,
              modifier: {
                page: 'all'
              },
              format: {
                body: function(data, row, column) {
                    return processDataForExport(data, row, column);
                }
              }
            },
            customize: function (doc) {
              doc.pageMargins = [20, 20, 20, 20]; 

              let table = doc.content[1].table;
              let newPages = [];

              table.body.slice(1).forEach(function (row, rowIndex) {
                  let tempBody = [];

                  row.forEach((cell, index) => {
                      if (index === 0) return; 

                      let label = table.body[0][index].text || table.body[0][index]; 
                      let value = cell.text || cell;

                      tempBody.push([
                          { text: label + ':', bold: true, alignment: 'left', margin: [5, 2, 5, 2] },
                          { text: value, alignment: 'left', margin: [5, 2, 5, 2] }
                      ]);
                  });

                  let newTable = {
                      table: {
                          widths: ['30%', '70%'],
                          body: [
                              [{ text: 'Details', style: 'tableHeader', alignment: 'center', colSpan: 2 }, {}],
                              ...tempBody,
                              [{ text: '--------------------------------------------------', colSpan: 2, alignment: 'center', bold: true, margin: [0, 5, 0, 5] }]
                          ]
                      }
                  };

                  newPages.push(newTable);

                  if (rowIndex !== table.body.length - 2) {
                      newPages.push({ text: '', pageBreak: 'after' });
                  }
              });

              doc.content = [
                  {
                      text: '',
                      fontSize: 14,
                      bold: true,
                      alignment: 'center',
                      margin: [0, 0, 0, 10]
                  },
                  ...newPages
              ];

              // **Improve Readability**
              doc.styles.tableHeader = {
                  fontSize: 12,
                  bold: true,
                  fillColor: '#e0e0e0',
                  alignment: 'center',
                  margin: [2, 2, 2, 2]
              };
              doc.defaultStyle.fontSize = 9; 
            }
          },
          {
            extend: 'print',
            text: '<i class="fa-solid fa-print"></i>',
            titleAttr: 'Print the table',
            exportOptions: {
                columns: columnsToExport,
                modifier: {
                    page: 'all'
                },
                format: {
                    body: function(data, row, column) {
                        return processDataForExport(data, row, column);
                    }
                }
            },
            customize: function(doc) {
                // Ensure content fits within A0 size and landscape orientation
                doc.pageSize = 'A0'; 
                doc.pageOrientation = 'landscape'; 
                
                // Adjust page margins for better fit (prevent overflow)
                doc.margins = [40, 40, 40, 40]; // Top, Left, Bottom, Right

                // Distribute columns evenly across the page
                let table = doc.content[1].table;
                let numberOfColumns = table.body[0].length;
                table.widths = Array(numberOfColumns).fill('*'); // Evenly distribute column widths

                // Adjust the font size to fit large tables better
                doc.styles.table = {
                    fontSize: 7,  // Slightly smaller font size for better fitting on A0
                    cellPadding: 4
                };

                // Set a style for the table headers to make them more distinct
                doc.styles.tableHeader = {
                    fontSize: 10,
                    bold: true,
                    alignment: 'center',
                    fillColor: '#f2f2f2',  // Light grey background for headers
                    margin: [0, 5]
                };

                // Align all table cells to the center for consistency
                table.body.forEach(function(row) {
                    row.forEach(function(cell) {
                        cell.alignment = 'center';
                    });
                });

                // Prevent columns from overflowing by ensuring no columns are too wide
                table.body.forEach(function(row) {
                    row.forEach(function(cell) {
                        if (typeof cell === 'object') {
                            cell.alignment = 'center';
                        }
                    });
                });

                // Optionally, add a title above the table
                doc.content.unshift({
                    text: 'Table Printout',  // Add a title (optional)
                    style: 'header'
                });

                // Customize header style (optional)
                doc.styles.header = {
                    fontSize: 16,
                    bold: true,
                    alignment: 'center',
                    margin: [0, 20, 0, 20]  // Top margin for header
                };
            },  
            autoPrint: true
        },
        {
            extend: 'colvis',
            text: '<i class="fa-solid fa-eye"></i>',
            titleAttr: 'Show/Hide Columns',
             columns: ':not(.noExport)'
        }
        ],
      });

      function processDataForExport(data, row, column) {
        if (column === 0) {
          return row + 1; 
        }

        let tempDiv = document.createElement('div');
        tempDiv.innerHTML = data;
        let cleanData = tempDiv.textContent || tempDiv.innerText || '';
        cleanData = cleanData.replace(/<a[^>]*>(.*?)<\/a>/g, '$1');  
        cleanData = cleanData.replace(/<i[^>]*>(.*?)<\/i>/g, '$1'); 
        return cleanData.replace(/\s+/g, ' ').trim();
      }


      table.on('column-visibility.dt', function(e, settings, column, state) {
        const visibleColumns = table.columns(':visible').indexes();
        columnsToExport.length = 0;
        visibleColumns.each(function(index) {
          columnsToExport.push(index); 
        });
      });

      $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        let selectedStatus = $('input[name="statusFilter"]:checked').val();
        let rowStatus = $(table.row(dataIndex).node()).data('status');
        let rowDate = new Date($(table.row(dataIndex).node()).data('join-date'));

        // Convert input dates to Date objects
        let startDate = $('#startDate').val() ? new Date($('#startDate').val()) : null;
        let endDate = $('#endDate').val() ? new Date($('#endDate').val()) : null;



        if (startDate) {
          startDate.setHours(0, 0, 0, 0); // Set to midnight
        }
        if (endDate) {
          endDate.setHours(23, 59, 59, 999); // Set to end of the day
        }


        // Check if the date values are null or empty
        if (!startDate && !endDate) {
          // If both are empty, allow all rows
          return selectedStatus === 'all' || rowStatus === selectedStatus;
        }

        // Check status
        const statusCheck = (selectedStatus === 'all' || rowStatus === selectedStatus);
        // Check date range
        const startCheck = !startDate || rowDate >= startDate;
        const endCheck = !endDate || rowDate <= endDate;

        return statusCheck && startCheck && endCheck;
      });


      // Event listener for the filter button
      $('#filterDate').on('click', function() {
        table.draw(); // Redraw the table to apply the filtering
      });

      // Optional: Reset filter functionality
      $('#resetFilter').on('click', function() {
        $('#startDate').val('');
        $('#endDate').val('');
        $('input[name="statusFilter"]').prop('checked', false);
        table.draw(); // Redraw to reset filters
      });



      // Event listener for status filter changes
      $('input[name="statusFilter"]').on('change', function() {
        table.draw(); // Redraw to apply filtering
      });

      $('#pageLengthSelect').on('change', function() {
        const newLength = $(this).val() === "all" ? -1 : parseInt($(this).val());
        table.page.len(newLength).draw(false);
      });
   

      function updateSerialNumbers() {
        const pageInfo = table.page.info();
        const start = pageInfo.start;

        $('#myTable tbody tr:visible').each(function(index) {
          $(this).find('td:first').text(start + index + 1);
        });
      }

      table.on('draw', function() {
        updateSerialNumbers();
      });

      table.draw();


      $('.logout-user').click(function() {
        $.ajax({
          url: "{{ route('logout') }}",
          type: "POST",
          headers: {
            'X-CSRF-TOKEN': csrfToken
          },
          success: function(response) {
            if (response.success) {
              window.location.href = "{{ route('loadLogin') }}";
            } else {
              alert(response.msg);
            }
          },
          error: function(xhr) {
            // Handle errors here

          }
        })
      });

    });


    $(function() {
      $('.show-alert__error').click(function() {
        FancyAlerts.show({
          msg: 'Uh oh something went wrong!',
          type: 'error'
        })
      })
      $('.show-alert__success').click(function() {
        FancyAlerts.show({
          msg: 'Nailed it! This totally worked.'
        })
      })
      $('.show-alert__info').click(function() {
        FancyAlerts.show({
          msg: 'So long and thanks for all the shoes.',
          type: 'info'
        })
      });
    })


    var FancyAlerts = (function() {

      var self = this;

      self.show = function(options) {
        if ($('.fancy-alert').length > -1) {
          FancyAlerts.hide();
        }
        var defaults = {
          type: 'success',
          msg: 'Success',
          timeout: 5000,
          icon: 'fa fa-check',
          onClose: function() {}
        };

        if (options.type === 'error' && !options.icon) options.icon = 'fa fa-exclamation-triangle';
        if (options.type === 'info' && !options.icon) options.icon = 'fa fa-cog';

        var options = $.extend(defaults, options);

        var $alert = $('<div class="fancy-alert ' + options.type + ' ">' +
          '<div class="">' +
          '<i class="fancy-alert--icon ' + options.icon + '"></i>' +
          '<div class="fancy-alert--content">' +
          '<div class="fancy-alert--words">' + options.msg + '</div>' +
          '<a class="fancy-alert--close" href="#"><i class="fa fa-times"></i></a>' +
          '</div>' +
          '</div>' +
          '</div>');

        $('body').prepend($alert);
        setTimeout(function() {
          $alert.addClass('fancy-alert__active');
        }, 10);

        setTimeout(function() {
          $alert.addClass('fancy-alert__extended');
        }, 500);

        if (options.timeout) {
          self.hide(options.timeout);
        }
        $('.fancy-alert--close').on('click', function(e) {
          e.preventDefault();
          self.hide();
        });

        $alert.on('fancyAlertClosed', function() {
          options.onClose();
        });
      };


      self.hide = function(_delay) {
        var delay = _delay || 0;

        var $alert = $('.fancy-alert');
        setTimeout(function() {
          setTimeout(function() {
            $alert.removeClass("fancy-alert__extended");
          }, 10);

          setTimeout(function() {
            $alert.removeClass('fancy-alert__active');
          }, 500);
          setTimeout(function() {
            $alert.trigger('fancyAlertClosed');
            $alert.remove();
          }, 1000);
        }, delay);
      }

      return self;

    })();


    function refreshCaptcha(imageId) {
      var captchaImage = document.getElementById(imageId);
      var captchaUrl = document.querySelector('meta[name="captcha-url"]').getAttribute('content');
      captchaImage.src = captchaUrl + "?" + new Date().getTime(); // Append timestamp to bypass cache
    }



    $('.panel-collapse').on('show.bs.collapse', function() {
      $(this).closest('.panel').find('.panel-heading').addClass('active');
    });

    $('.panel-collapse').on('hide.bs.collapse', function() {
      $(this).closest('.panel').find('.panel-heading').removeClass('active');
    });
  </script>

  @stack('script')

</body>

</html>