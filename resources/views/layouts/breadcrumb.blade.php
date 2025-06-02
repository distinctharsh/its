<div class="row mb-2">
  <div class="col-sm-6">
    @if (!empty($breadcrumbPath) && is_array($breadcrumbPath) && end($breadcrumbPath))
      <h1 class="m-0">
          {{ end($breadcrumbPath)['title'] }}
      </h1>
    @else
        <h1 class="m-0">No Title</h1>
    @endif
  </div>
  <div class="col-sm-6">
    <ol class="breadcrumb float-sm-right">
      @unless(request()->routeIs('dashboard'))
        <li class="breadcrumb-item">
          <a href="{{ route('dashboard') }}">Dashboard</a>
        </li>
      @endunless

      @foreach ($breadcrumbPath as $index => $breadcrumb)
        @php
          $isLast = $index === count($breadcrumbPath) - 1;
          $routeExists = Route::has($breadcrumb['route_name']);
          $activeClass = $isLast ? 'active' : '';
        @endphp

        <li class="breadcrumb-item {{ $activeClass }}">
          @if ($routeExists && !$isLast)
            <a href="{{ route($breadcrumb['route_name']) }}">
              {{ $breadcrumb['label'] }}
            </a>
          @else
            {{ $breadcrumb['label'] }}
          @endif
        </li>
      @endforeach
    </ol>
  </div>
</div>
