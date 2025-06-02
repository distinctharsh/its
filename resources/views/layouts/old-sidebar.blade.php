<div class="card draggable" style="color: black;">
    <div class="card-body">
        <div class="row">
            <div class="col-md-10 mt-0">
                <h5 class="card-title">Activity Log</h5>
            </div>

            <div class="col-auto">
                <div class="stat text-primary">
                <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>
        </div>
        <span class="mt-1 mb-3" style="font-size: 12px;">Last Log In &nbsp; <span class="text-muted">{{ \Carbon\Carbon::parse($loginAndActivity['lastLogin']->created_at)->format('d M Y h:i A') }}</span> </span> </br>
        <span class="mt-1 mb-3" style="font-size: 12px;">Last Update <span class="text-muted">{{ \Carbon\Carbon::parse($loginAndActivity['lastActivity']->created_at)->format('d M Y h:i A') }}</span> </span>
    </div>
</div>