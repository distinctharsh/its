<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

    <li class="nav-item">
      <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="nav-icon fa fa-tachometer-alt"></i>
        <p>
          Dashboard
        </p>
      </a>
    </li>
    @php
    $isActiveRoute = request()->routeIs('manageNationality', 'manageState', 'manageRank', 'manageDesignation', 'manageStatus', 'manageInspectionCategory', 'manageCategoryInspection', 'manageInspectionCategoryType', 'manageVisitCategory', 'manageEscortOfficer', 'manageSiteCode', 'manageUser', 'manageIssue', 'manageLock');
    @endphp
    <li class="nav-item {{ $isActiveRoute ? 'menu-open' : '' }}">
      <a href="#" class="nav-link {{ $isActiveRoute ? 'active' : '' }}">
        <i class="nav-icon fa fa-user-shield"></i>
        <p>
          Master
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="{{ route('manageNationality') }}" data-toggle="tooltip" data-placement="top" title="Nationality Master" class="nav-link {{ request()->routeIs('manageNationality') ? 'active' : '' }}">
            <i class="fa fa-globe nav-icon"></i>
            <p> Nationality</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('manageState') }}" data-toggle="tooltip" data-placement="top" title="State Master" class="nav-link {{ request()->routeIs('manageState') ? 'active' : '' }}">
            <i class="fa fa-map-marker-alt nav-icon"></i>
            <p> State</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('manageRank') }}" data-toggle="tooltip" data-placement="top" title="Rank Master" class="nav-link {{ request()->routeIs('manageRank') ? 'active' : '' }}">
            <i class="fa fa-solid fa-ranking-star nav-icon"></i>
            <p> Rank</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('manageDesignation') }}" data-toggle="tooltip" data-placement="top" title="Designation Master" class="nav-link {{ request()->routeIs('manageDesignation') ? 'active' : '' }}">
            <i class="fa fa-user-tag nav-icon"></i>
            <p> Designation</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('manageStatus') }}" data-toggle="tooltip" data-placement="top" title="Status Master" class="nav-link {{ request()->routeIs('manageStatus') ? 'active' : '' }}">
            <i class="fa fa-info-circle nav-icon"></i>
            <p> Status</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('manageInspectionCategory') }}" data-toggle="tooltip" data-placement="top" title="Inspection Profile" class="nav-link {{ request()->routeIs('manageInspectionCategory') ? 'active' : '' }}">
            <i class="fa fa-clipboard-list nav-icon"></i>
            <p> Inspection Profile</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('manageInspectionCategoryType') }}" data-toggle="tooltip" data-placement="top" title="Sub Inspection Category Type Master" class="nav-link {{ request()->routeIs('manageInspectionCategoryType') ? 'active' : '' }}">
            <i class="fa fa-tags nav-icon"></i>
            <p> Sub Category Type</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('manageVisitCategory') }}" data-toggle="tooltip" data-placement="top" title="Visit Category Master" class="nav-link {{ request()->routeIs('manageVisitCategory') ? 'active' : '' }}">
            <i class="fa fa-calendar-check nav-icon"></i>
            <p> Visit Category</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('manageCategoryInspection') }}" data-toggle="tooltip" data-placement="top" title="Escort Officer Master" class="nav-link {{ request()->routeIs('manageCategoryInspection') ? 'active' : '' }}">
            <i class="fa fa-user-tie nav-icon"></i>
            <p> Inspection Type</p>
          </a>
        </li>


        <li class="nav-item">
          <a href="{{ route('manageEscortOfficer') }}" data-toggle="tooltip" data-placement="top" title="Escort Officer Master" class="nav-link {{ request()->routeIs('manageEscortOfficer') ? 'active' : '' }}">
          <i class="fa fa-solid fa-user-nurse nav-icon"></i>
            <p> Escort Officer</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('manageSiteCode') }}" data-toggle="tooltip" data-placement="top" title="Plant Site Master" class="nav-link {{ request()->routeIs('manageSiteCode') ? 'active' : '' }}">
            <i class="fa fa-map-signs nav-icon"></i>
            <p> Plant Site</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('manageUser') }}" data-toggle="tooltip" data-placement="top" title="User" class="nav-link {{ request()->routeis('manageUser') ? 'active' : '' }}">
          <i class="fa fa-solid fa-users-gear nav-icon"></i>
            <p>User</p>
          </a>
        </li>


        <li class="nav-item">
          <a href="{{ route('manageIssue') }}" data-toggle="tooltip" data-placement="top" title="Issues" class="nav-link {{ request()->routeis('manageIssue') ? 'active' : '' }}">
            <i class="fa fa-user nav-icon"></i>
            <p>Issue</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('manageLock') }}" data-toggle="tooltip" data-placement="top" title="Issues" class="nav-link {{ request()->routeis('manageLock') ? 'active' : '' }}">
            <i class="fa fa-user nav-icon"></i>
            <p>Page Lock</p>
          </a>
        </li>
      </ul>
    </li>

    @php
    $isActiveRoute = request()->routeIs('loginLogs', 'activityLogs', 'loginLogs.get', 'loginLogs.post', 'activityLogs.get', 'activityLogs.post');
    @endphp

    <li class="nav-item {{ $isActiveRoute ? 'menu-open' : '' }}">
      <a href="#" class="nav-link {{ $isActiveRoute ? 'active' : '' }}">
        <i class="nav-icon fa fa-file-alt"></i>
        <p>
          Audit Logs
          <i class="fas fa-angle-left right"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="{{ route('loginLogs.get') }}" data-toggle="tooltip" data-placement="top" title="Audit Login Log" class="nav-link {{ request()->routeIs('loginLogs.get') ? 'active' : '' }} {{ request()->routeIs('loginLogs.post') ? 'active' : '' }}">
            <i class="fa fa-sign-in-alt nav-icon"></i>
            <p>Login Logs</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('activityLogs.get') }}" data-toggle="tooltip" data-placement="top" title="Audit Activity Log" class="nav-link {{ request()->routeIs('activityLogs.get') ? 'active' : '' }} {{ request()->routeIs('activityLogs.post') ? 'active' : '' }}">
            <i class="fa fa-list-alt nav-icon"></i>
            <p>Activity Logs</p>
          </a>
        </li>
      </ul>
    </li>
  </ul>
</nav>