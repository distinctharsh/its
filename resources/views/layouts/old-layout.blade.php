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


</head>

<body>
  <div class="container-fluid p-0 m-0">
    <!-- Navbar -->
    @include('layouts.header')
    @include('layouts.nav')

    <div class="row ml-0 mr-0">
      @if(!auth()->user()->hasRole('User'))
      <nav id="sidebar" class="col-md-4 col-lg-3 d-md-block sidebar" style="flex:1;">
        <div class="sidebar-sticky">
          <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="{{ route('manageNationality') }}"><i class="fa-solid fa-globe"></i> Country Master</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('manageRank') }}"><i class="fa-solid fa-ranking-star"></i> Designation Master</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('manageStatus') }}"><i class="fa-solid fa-bars-progress"></i> Status Master</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('manageInspectionCategory') }}"><i class="fa-solid fa-binoculars"></i> Inspection Category Master</a></li>
          </ul>
        </div>
      </nav>
      @endif

      <main role="main" class="col-md-{{ auth()->user()->hasRole('User') ? '12' : '8 col-lg-9' }}">
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
  <script src="{{ mix('js/app.js') }}"></script>

  <script>
    $(document).ready(function() {
      // Get CSRF token from meta tag
      var csrfToken = $('meta[name="csrf-token"]').attr('content');

      const tableId = 'myTable';
      const exportColumnsAttr = $(`#${tableId}`).data('export-columns');
      const columnsToExport = exportColumnsAttr ? exportColumnsAttr.split(',').map(Number) : [];

      let table = $(`#${tableId}`).DataTable({
    dom: 'Bfrtip',
    pageLength: 10,
    buttons: [
      {
        extend: 'copyHtml5',
        text: '<i class="fa-solid fa-copy"></i>',
        titleAttr: 'Copy to clipboard',
        exportOptions: {
          columns: columnsToExport,
          modifier: {
            page: 'all' // Export all rows, including filtered data
          },
          format: {
            body: function (data, row, column) {
              // Use custom serial number or column data as required
              return column === 0 ? row + 1 : data;
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
            body: function (data, row, column) {
              return column === 0 ? row + 1 : data;
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
          columns: columnsToExport, // Include all specified columns
          modifier: {
            page: 'all'
          },
          format: {
            body: function (data, row, column) {
              return column === 0 ? row + 1 : data;
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
            body: function (data, row, column) {
              return column === 0 ? row + 1 : data;
            }
          }
        }
      }
    ],
  });




      // Custom search function to filter active/inactive status before pagination
      $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        const selectedStatus = $('input[name="statusFilter"]:checked').val();
        const rowStatus = $(table.row(dataIndex).node()).data('status');

        // Show row if status matches selected filter or if "all" is selected
        if (selectedStatus === 'all' || rowStatus === selectedStatus) {
          return true;
        }
        return false;
      });

      // Serial number update function with continuous numbering
      function updateSerialNumbers() {
        // Current page info to calculate starting index for the page
        const pageInfo = table.page.info();
        const start = pageInfo.start; // Starting index for the current page

        $('#myTable tbody tr:visible').each(function(index) {
          $(this).find('td:first').text(start + index + 1);
        });
      }

      // Filter when status radio button changes
      $('input[name="statusFilter"]').on('change', function() {
        table.draw(); // Redraws the table with the applied filter
      });

      // Page length change handler
      $('#pageLengthSelect').on('change', function() {
        const newLength = $(this).val() === "all" ? -1 : parseInt($(this).val());
        table.page.len(newLength).draw(false); // Apply new page length and keep the current page
      });

      // Update serial numbers on each draw
      table.on('draw', function() {
        updateSerialNumbers();
      });

      // Initial filter application
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
            console.log(xhr.responseText);
          }
        })
      });
      $('#sidebar-toggle-btn').click(function() {
        $('#sidebar').toggleClass('collapsed');
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
  </script>

  @stack('script')

</body>

</html>