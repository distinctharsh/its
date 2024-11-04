<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="captcha-url" content="{{ route('captcha') }}">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- Bootstrap Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">

  <!-- Compiled CSS -->
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">


  <title>ITS</title>
</head>

<body>
  <div class="container-fluid p-0 m-0">
    <!-- Navbar -->
    @include('layouts.nav')

    <div class="row ml-0 mr-0">
      <!-- Sidebar -->
      <nav id="sidebar" class="col-md-2 d-md-block sidebar collapsed">
        <button id="sidebar-toggle-btn">
          <i class="fa-solid fa-bars"></i>
        </button>
        <div class="sidebar-sticky">
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link" href="{{ route('showInspection') }}">
                <i class="fa-solid fa-clipboard-check"></i> <span>Inspection</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('showInspection') }}">
                <i class="fa-solid fa-user-tie"></i> <span>Inspector</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('showInspection') }}">
                <i class="fa-solid fa-calendar-day"></i> <span>Visit</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('showInspection') }}">
                <i class="fa-solid fa-flag"></i> <span>Opcw Fax</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('showInspection') }}">
                <i class="fa-regular fa-flag"></i> <span>Report</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link logout-user" href="#">
                <i class="fa-solid fa-sign-out-alt"></i> <span>Logout</span>
              </a>
            </li>
          </ul>
        </div>
      </nav>


      <!-- Main content -->
      <main role="main" class="col-md-10  col-lg-10 ">
        @yield('content')
      </main>
    </div>
  </div>



  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>



  <script>
    $(document).ready(function() {
      // Get CSRF token from meta tag
      var csrfToken = $('meta[name="csrf-token"]').attr('content');

      $('.logout-user').click(function() {
        $.ajax({
          url: "{{ route('logout') }}",
          type: "POST",
          headers: {
            'X-CSRF-TOKEN': csrfToken // Add the CSRF token to the request headers
          },
          success: function(response) {
            if (response.success) {
              location.reload();
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




    function refreshCaptcha(imageId) {
      var captchaImage = document.getElementById(imageId);
      var captchaUrl = document.querySelector('meta[name="captcha-url"]').getAttribute('content');
      captchaImage.src = captchaUrl + "?" + new Date().getTime(); // Append timestamp to bypass cache
    }
  </script>

  @stack('script')

</body>

</html>