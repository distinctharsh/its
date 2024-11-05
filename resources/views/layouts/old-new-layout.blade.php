<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="captcha-url" content="{{ route('captcha') }}">

  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notification.css') }}">

  <title>ITS</title>

  <style>
    .select2-container--default .select2-results__option {
      display: flex;
      align-items: center;
      padding-left: 20px;
    }

    .select2-container--default .select2-results__option .select2-checkbox {
      margin-right: 10px;
    }



    /* Filter */


    .panel-heading {
      padding: 0;
      border: 0;
    }

    .panel-title>a,
    .panel-title>a:active {
      display: block;
      padding: 15px;
      color: #333333;

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
      background-color: #fff;
      box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;
    }

    .btn-filter {
      color: #333333;
    }

    .panel-body {
      background-color: #fbfbfb;
    }
  </style>
</head>

<body>
  <div class="container-fluid p-0 m-0">
    <!-- Navbar -->
    @include('layouts.header')
    @include('layouts.nav')

    <div class="row ml-0 mr-0">

      @if(auth()->user()->hasRole('Admin'))


      <nav id="sidebar" class=" sidebar">

        <div class="sidebar-sticky">
          <ul class="nav flex-column">
            <li class="nav-item mb-5">
              <a id="sidebarToggle" class="btn">
                <i class="fa-solid fa-bars"></i>
              </a>
            </li>
            <li class="nav-item"><a class="nav-link" href="{{ route('manageNationality') }}" data-toggle="tooltip" data-placement="top" title="Country Master"><i class="fa-solid fa-globe"></i> Country Master</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('manageState') }}" data-toggle="tooltip" data-placement="top" title="State Master"><i class="fa-solid fa-globe"></i> State Master</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('manageRank') }}" data-toggle="tooltip" data-placement="top" title="Designation Master"><i class="fa-solid fa-ranking-star"></i> Designation Master</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('manageStatus') }}" data-toggle="tooltip" data-placement="top" title="Status Master"><i class="fa-solid fa-bars-progress"></i> Status Master</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('manageInspectionCategory') }}" data-toggle="tooltip" data-placement="top" title="Inspection Category Master"><i class="fa-solid fa-binoculars"></i> Inspection Category Master</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('manageInspectionCategoryType') }}" data-toggle="tooltip" data-placement="top" title="Sub Inspection Category Type Master"><i class="fa-solid fa-binoculars"></i> Sub Category Type Master</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('manageVisitCategory') }}" data-toggle="tooltip" data-placement="top" title="Visit Category Master"><i class="fa-solid fa-binoculars"></i> Visit Category Master</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('manageEscortOfficer') }}" data-toggle="tooltip" data-placement="top" title="Escort Officer Master"><i class="fa-solid fa-binoculars"></i> Escort Officer Master</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('manageSiteCode') }}" data-toggle="tooltip" data-placement="top" title="Site Code Master"><i class="fa-solid fa-binoculars"></i> Site Code Master</a></li>
          </ul>
        </div>
      </nav>

      @endif

      <main role="main" style=" overflow-y: auto; overflow-x: auto;">
        @yield('content')
      </main>
    </div>
  </div>

  <!-- Optional JavaScript -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Add these lines in the <head> section -->
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>



  <script src="{{ mix('js/app.js') }}"></script>

  <script>
    $(document).ready(function() {

      $('#sidebarToggle').click(function() {
        $('#sidebar').toggleClass('collapsed');
      });


      $(".datepicker").datepicker({
        dateFormat: "yy-mm-dd",
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
          emptyTable: "No data available",
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
                  let tempDiv = document.createElement('div');
                  tempDiv.innerHTML = data;
                  let cleanData = tempDiv.textContent || tempDiv.innerText || '';
                  cleanData = cleanData.replace(/<a[^>]*>(.*?)<\/a>/g, '$1'); // Remove <a> tags
                  cleanData = cleanData.replace(/<i[^>]*>(.*?)<\/i>/g, '$1'); // Remove <i> tags
                  return column === 0 ? row + 1 : cleanData.replace(/\s+/g, ' ').trim();
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
                  let tempDiv = document.createElement('div');
                  tempDiv.innerHTML = data; // Set the HTML
                  let cleanData = tempDiv.textContent || tempDiv.innerText || ''; // Get plain text

                  // Replace unwanted HTML tags (e.g., <a>, <i>)
                  cleanData = cleanData.replace(/<a[^>]*>(.*?)<\/a>/g, '$1'); // Remove <a> tags
                  cleanData = cleanData.replace(/<i[^>]*>(.*?)<\/i>/g, '$1'); // Remove <i> tags

                  return column === 0 ? row + 1 : cleanData.replace(/\s+/g, ' ').trim();
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
                  let tempDiv = document.createElement('div');
                  tempDiv.innerHTML = data;
                  let cleanData = tempDiv.textContent || tempDiv.innerText || '';
                  cleanData = cleanData.replace(/<a[^>]*>(.*?)<\/a>/g, '$1'); // Remove <a> tags
                  cleanData = cleanData.replace(/<i[^>]*>(.*?)<\/i>/g, '$1'); // Remove <i> tags
                  return column === 0 ? row + 1 : cleanData.replace(/\s+/g, ' ').trim();
                }
              }
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
                  let tempDiv = document.createElement('div');
                  tempDiv.innerHTML = data;
                  let cleanData = tempDiv.textContent || tempDiv.innerText || '';
                  cleanData = cleanData.replace(/<a[^>]*>(.*?)<\/a>/g, '$1'); // Remove <a> tags
                  cleanData = cleanData.replace(/<i[^>]*>(.*?)<\/i>/g, '$1'); // Remove <i> tags
                  return column === 0 ? row + 1 : cleanData.replace(/\s+/g, ' ').trim();
                }
              }
            }
          }
        ],
      });

      $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        let selectedStatus = $('input[name="statusFilter"]:checked').val();
        let rowStatus = $(table.row(dataIndex).node()).data('status');
        let rowDate = new Date($(table.row(dataIndex).node()).data('join-date'));

        // Convert input dates to Date objects
        let startDate = $('#startDate').val() ? new Date($('#startDate').val()) : null;
        let endDate = $('#endDate').val() ? new Date($('#endDate').val()) : null;

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