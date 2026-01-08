@extends('layouts.admin')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Notifications</h2>
                <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Send New Notification
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>User</th>
                                    <th>Read Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notifications as $notification)
                                <tr>
                                    <td>{{ $notification->id }}</td>
                                    <td>{{ Str::limit($notification->title, 30) }}</td>
                                    <td>{{ Str::limit($notification->message, 50) }}</td>
                                    <td>
                                        @if($notification->user)
                                            {{ $notification->user->name }}
                                        @else
                                            System
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $notification->is_read ? 'bg-success' : 'bg-warning' }}">
                                            {{ $notification->is_read ? 'Read' : 'Unread' }}
                                        </span>
                                    </td>
                                    <td>{{ $notification->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="#" class="btn btn-sm btn-outline-primary" 
                                               data-bs-toggle="modal" 
                                               data-bs-target="#viewNotificationModal" 
                                               data-id="{{ $notification->id }}"
                                               data-title="{{ $notification->title }}"
                                               data-message="{{ $notification->message }}"
                                               data-user="{{ $notification->user ? $notification->user->name : 'System' }}"
                                               data-date="{{ $notification->created_at->format('M d, Y H:i') }}"
                                               data-read="{{ $notification->is_read ? 'Read' : 'Unread' }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.notifications.destroy', $notification->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this notification?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <i class="fas fa-bell-slash fa-2x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">No notifications found</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Notification Modal -->
<div class="modal fade" id="viewNotificationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <p class="form-control-plaintext" id="modalTitle"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <div class="form-control-plaintext" id="modalMessage" style="white-space: pre-wrap;"></div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">User</label>
                        <p class="form-control-plaintext" id="modalUser"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date</label>
                        <p class="form-control-plaintext" id="modalDate"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <p class="form-control-plaintext" id="modalRead"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewModal = document.getElementById('viewNotificationModal');
    
    if (viewModal) {
        viewModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const title = button.getAttribute('data-title');
            const message = button.getAttribute('data-message');
            const user = button.getAttribute('data-user');
            const date = button.getAttribute('data-date');
            const read = button.getAttribute('data-read');
            
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;
            document.getElementById('modalUser').textContent = user;
            document.getElementById('modalDate').textContent = date;
            document.getElementById('modalRead').textContent = read;
        });
    }
});
</script>
@endsection