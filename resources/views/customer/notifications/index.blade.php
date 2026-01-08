@extends('layouts.customer')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="page-title">
                    <h3><i class="fas fa-bell me-2"></i>Notifications</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Your Notifications</h5>
                    <div>
                        <a href="{{ route('customer.notifications.mark-all-read') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-check-double me-1"></i> Mark All Read
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notifications as $notification)
                                <tr class="{{ $notification->is_read ? '' : 'table-primary' }}">
                                    <td>{{ Str::limit($notification->title, 40) }}</td>
                                    <td>{{ Str::limit($notification->message, 60) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $notification->is_read ? 'success' : 'warning' }}">
                                            {{ $notification->is_read ? 'Read' : 'Unread' }}
                                        </span>
                                    </td>
                                    <td>{{ $notification->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('customer.notifications.mark-read', $notification->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-check me-1"></i> Mark Read
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $notifications->links() }}
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No notifications yet</h5>
                        <p class="text-muted">You don't have any notifications at the moment.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection