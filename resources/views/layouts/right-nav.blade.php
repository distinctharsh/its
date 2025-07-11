<ul class="navbar-nav ml-auto">
  
  <li class="nav-item">
    <a class="nav-link" href="{{ route('manageVisit') }}">
      <i class="fa-solid fa-calendar-day"></i> <span>Inspection</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{ route('manageInspector') }}">
      <i class="fa-solid fa-user-tie"></i> <span>Inspector</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{ route('manageOtherStaff') }}">
    <i class="fa-solid fa-ghost"></i> <span>OPCW Other Staff</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{ route('manageOpcw') }}">
      <i class="fa-solid fa-flag"></i> <span>OPCW Notification</span>
    </a>
  </li>

  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <i class="fa-solid fa-list"></i> Reports
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarUserDropdown" style="    min-width: 5rem;">
      <a class="dropdown-item" href="{{ route('manageReport') }}" style="color: #000;">
        Nationality Wise Report
      </a>
      <a class="dropdown-item" href="{{ route('listInspectorsgen.get') }}" style="color: #000;">
        General Query Report 
      </a>
      <a class="dropdown-item" href="{{ route('yearwiseReport') }}" style="color: #000;">
        Year Wise Inspection Report 
      </a>
      <a class="dropdown-item" href="{{ route('stateWiseReport') }}" style="color: #000;">
        State Wise Inspection Report 
      </a>
      <a class="dropdown-item" href="{{ route('plantsitewiseReport') }}" style="color: #000;">
        Plant site Wise Inspection Report 
      </a>
      <a class="dropdown-item" href="{{ route('nationalWiseInspectionReport') }}" style="color: #000;">
        Nationality Wise Inspection Report 
      </a>
      <a class="dropdown-item" href="{{ route('updateInspectionReport') }}" style="color: #000;">
        Inspection Update Report
      </a>
    </div>
   
  </li>

  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <i class="fa-solid fa-user"></i> {{ auth()->user()->name }}
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarUserDropdown" style="    min-width: 5rem;">
   
       <!-- Change Password Link -->
       <a class="dropdown-item" href="{{ route('changePassword') }}" style="color: #000;">
        <i class="fa-solid fa-key"></i> Change Password
      </a>

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