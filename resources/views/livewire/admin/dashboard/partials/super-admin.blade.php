<div class="col-12">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="card-title">Super Admin</h5>
            <p class="card-text">Full system access. Quick links:</p>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.users') }}" class="btn btn-outline-primary">Manage Users</a>
                <a href="{{ route('admin.roles') }}" class="btn btn-outline-secondary">Roles</a>
                <a href="{{ route('admin.permissions') }}" class="btn btn-outline-info">Permissions</a>
                <a href="{{ route('admin.reports') }}" class="btn btn-outline-success">Reports</a>
            </div>
        </div>
    </div>
</div>
