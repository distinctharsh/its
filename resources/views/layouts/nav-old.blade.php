<div style="z-index: 99; position: relative">

  <div class="container-fluid header-wrapper" style="z-index: 500">
    <div class="header-container">
      <div class="col-xs-12 col-sm-6 col-md-6 text-left">
        <div class="media">
          <div class="media-left media-middle">
            <img src="{{ asset('images/emblem.png') }}" alt="Emblem" class="media-object">

          </div>
          <div class="media-body header-emblem">
            <span class="media-heading" style="color: #000">
              <span id="Label1">Cabinet Secretariat<br><span class="subheading"><i>Government of India</i></span></span></span>
          </div>
        </div>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-6">
        <div class="media">
          <div class="media-right media-middle pull-right">

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<nav class="navbar navbar-expand-lg" style="background: linear-gradient(to top, #2f5b70, #2a62af); color: #fff;">
  <a class="navbar-brand" href="#">
    <i class="fa-solid fa-house"></i> Dashboard
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"><i class="fa-solid fa-bars"></i></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="{{ route('manageInspector') }}">
          <i class="fa-solid fa-user-tie"></i> Manage Inspector
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('manageInspection') }}">
          <i class="fa-solid fa-clipboard-check"></i> Manage Inspection
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('manageVisit') }}">
          <i class="fa-solid fa-calendar-day"></i> Manage Visit
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('manageOpcw') }}">
          <i class="fa-solid fa-flag"></i> Manage OPCW Fax
        </a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa-regular fa-flag"></i> Manage Report
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="#">
            <i class="fa-solid fa-chart-pie"></i> Report Overview
          </a>
          <a class="dropdown-item" href="#">
            <i class="fa-solid fa-file-lines"></i> Report Details
          </a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa-solid fa-user"></i> {{ auth()->user()->name }}
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarUserDropdown">
          <!-- Logout Link Triggering the POST Form -->
          <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: #000;">
            <i class="fa-solid fa-sign-out-alt"></i> Logout
          </a>
          <!-- Hidden Logout Form -->
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
        </div>
      </li>
    </ul>
  </div>
</nav>