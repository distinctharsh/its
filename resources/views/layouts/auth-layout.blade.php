<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inspector Tracking Software</title>
  <!-- <link href='https://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'> -->


  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->

  <!-- Bootstrap CSS -->
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> -->
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
  <!-- Bootstrap Icons -->
  <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet"> -->



  <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/bootstrap-icons.min.css') }}">
  <link href="{{ asset('assets/vendor/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">



  <link rel="stylesheet" href="css/style.css">
</head>

<body class="d-flex justify-content-center align-items-center" style="background: url({{ asset('images/login-background.jpg') }}) no-repeat center center fixed; background-size: cover; height: 100vh;">
  @yield('content')


  <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('theme/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/sweetalert2/sweetalert2.min.js') }}"></script>

  <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
  <script src="{{ asset('assets/vendor/popper/popper.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/bootstrap/bootstrap.min.js') }}"></script>
 

  <script>
    function refreshCaptcha() {
      var captchaImage = document.getElementById('captchaImage');
      captchaImage.src = "{{ route('captcha') }}?" + new Date().getTime();
    }




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
  </script>
</body>

</html>