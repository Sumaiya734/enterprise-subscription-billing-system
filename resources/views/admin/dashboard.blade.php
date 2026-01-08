@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')

<style>
/* ------------------------------ */
/*  PREMIUM ADVANCED THEME STYLES */
/* ------------------------------ */

/* Color Palette (Soft, Eye-Soothing) */
:root {
    --soft-blue: #6EA8FE;
    --soft-purple: #A78BFA;
    --soft-cyan: #67E8F9;
    --soft-lavender: #C4B5FD;
    --soft-green: #86EFAC;
    --soft-orange: #FDBA74;
    --soft-pink: #F9A8D4;
    --soft-red: #FCA5A5;
    --dark-text: #1f2937;
}

/* Smooth fade animation */
.fade-in {
    animation: fadeIn 0.9s ease forwards;
    opacity: 0;
}

@keyframes fadeIn {
    to { opacity: 1; }
}

/* Soft slide animation */
.slide-up {
    animation: slideUp 0.8s ease forwards;
    opacity: 0;
    transform: translateY(20px);
}

@keyframes slideUp {
    to { opacity: 1; transform: translateY(0); }
}

/* Animated gradient cards */
.advanced-card {
    border-radius: 18px;
    padding: 28px;
    color: #fff;
    border: none;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    transition: 0.35s ease;
}

.advanced-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 14px 40px rgba(0,0,0,0.12);
}

.gradient-1 { background: linear-gradient(135deg, var(--soft-blue), var(--soft-purple)); }
.gradient-2 { background: linear-gradient(135deg, var(--soft-green), var(--soft-cyan)); }
.gradient-3 { background: linear-gradient(135deg, var(--soft-orange), #F59E0B); }
.gradient-4 { background: linear-gradient(135deg, var(--soft-pink), var(--soft-lavender)); }
.gradient-5 { background: linear-gradient(135deg, var(--soft-red), #EF4444); }
.gradient-6 { background: linear-gradient(135deg, #8B5CF6, #EC4899); }

.icon-bg {
    position: absolute;
    right: -15px;
    top: -15px;
    font-size: 90px;
    opacity: 0.18;
}

/* Clean white cards */
.glass-card {
    border-radius: 16px;
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.35);
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    transition: 0.3s ease;
}

.glass-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,0.08);
}

/* Quick Action Buttons */
.quick-btn {
    border-radius: 14px !important;
    padding: 18px !important;
    font-weight: 600;
    transition: 0.25s ease;
    background: rgba(255,255,255,0.85);
    border: 2px solid #e5e7eb;
}

.quick-btn:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.10);
    border-color: transparent;
}

.quick-btn i {
    font-size: 32px;
    margin-right: 14px;
}

/* Recent Tickets Table */
.ticket-table th {
    font-weight: 600;
    color: #4b5563;
}

.ticket-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}

.priority-high {
    background-color: #FEF3C7;
    color: #92400E;
}

.priority-urgent {
    background-color: #FEE2E2;
    color: #B91C1C;
}

.status-open {
    background-color: #FEF3C7;
    color: #92400E;
}

.status-in-progress {
    background-color: #DBEAFE;
    color: #1E40AF;
}

.status-resolved {
    background-color: #D1FAE5;
    color: #065F46;
}
</style>

<script>
function submitReply() {
    // Disable button to prevent multiple submissions
    const submitBtn = document.querySelector('#replyMessageModal .btn-primary');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sending...';
    
    const formData = {
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        message_id: document.getElementById('messageId').value,
        reply: document.getElementById('replyContent').value,
        status: document.getElementById('messageStatus').value
    };
    
    // Create form data object for the POST request
    const postData = new FormData();
    postData.append('_token', formData._token);
    postData.append('status', formData.status);
    postData.append('admin_reply', formData.reply);
    
    // Use fetch with proper error handling and headers to ensure it's treated as AJAX
    fetch(`/admin/customer-messages/${formData.message_id}/reply`, {
        method: 'POST',
        body: postData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        // Check response status and content type
        if (!response.ok) {
            // If response is not OK, check what kind of error it is
            console.log('Response status:', response.status);
            console.log('Response URL:', response.url);
            
            // Try to get the response text to see what error page was returned
            return response.text().then(text => {
                console.log('Error response:', text);
                throw new Error(`Server returned status ${response.status}: ${text.substring(0, 200)}...`);
            });
        }
        
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // If not JSON, get the text to see what was returned
            return response.text().then(text => {
                console.log('Non-JSON response:', text);
                throw new Error('Server returned non-JSON response: ' + text.substring(0, 200));
            });
        }
    })
    .then(data => {
        if(data.success) {
            alert('Reply sent successfully!');
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('replyMessageModal'));
            modal.hide();
            // Reset form
            document.getElementById('replyMessageForm').reset();
            // Refresh the page or update the UI
            location.reload();
        } else {
            alert('Error sending reply: ' + (data.message || 'Unknown error'));
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Send Reply';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending reply: ' + error.message);
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Send Reply';
    });
}

function openReplyModal(messageId, subject, customer, content) {
    document.getElementById('messageId').value = messageId;
    document.getElementById('messageSubject').value = subject;
    document.getElementById('messageCustomer').value = customer;
    document.getElementById('messageContent').value = content;
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('replyMessageModal'));
    modal.show();
}

// Add event listeners for reply buttons
document.addEventListener('DOMContentLoaded', function() {
    const replyButtons = document.querySelectorAll('.reply-btn');
    replyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const messageId = this.getAttribute('data-message-id');
            const subject = this.getAttribute('data-subject');
            const customer = this.getAttribute('data-customer');
            const content = this.getAttribute('data-content');
            
            openReplyModal(messageId, subject, customer, content);
        });
    });
});
</script>


<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h2 class="fw-bold text-dark">
        <i class="fas fa-tachometer-alt text-primary me-2"></i> Dashboard Overview
    </h2>

    <div>
        <button class="btn btn-light border me-2 shadow-sm">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
        <button class="btn btn-primary shadow-sm">
            <i class="fas fa-download"></i> Export
        </button>
    </div>
</div>


<!-- GRADIENT STAT CARDS -->
<div class="row g-4 mb-4">

    <div class="col-xl-3 col-md-6 slide-up" style="animation-delay: .1s">
        <div class="advanced-card gradient-1">
            <div class="icon-bg"><i class="fas fa-users"></i></div>
            <h6>Total Customers</h6>
            <h2>{{ $totalCustomers ?? 0 }}</h2>
            <small>Active subscribers</small>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 slide-up" style="animation-delay: .2s">
        <div class="advanced-card gradient-2">
            <div class="icon-bg"><i class="fas fa-money-bill-wave"></i></div>
            <h6>Monthly Revenue</h6>
            <h2>৳{{ number_format($monthlyRevenue ?? 0, 2) }}</h2>
            <small>Current month</small>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 slide-up" style="animation-delay: .3s">
        <div class="advanced-card gradient-3">
            <div class="icon-bg"><i class="fas fa-clock"></i></div>
            <h6>Pending Bills</h6>
            <h2>{{ $pendingBills ?? 0 }}</h2>
            <small>Awaiting payment</small>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 slide-up" style="animation-delay: .4s">
        <div class="advanced-card gradient-4">
            <div class="icon-bg"><i class="fas fa-cube"></i></div>
            <h6>Active Products</h6>
            <h2>{{ $activeproducts ?? 0 }}</h2>
            <small>Total products</small>
        </div>
    </div>

</div>

<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6 slide-up" style="animation-delay: .5s">
        <div class="advanced-card gradient-5">
            <div class="icon-bg"><i class="fas fa-envelope"></i></div>
            <h6>Unread Messages</h6>
            <h2>{{ $unreadMessages ?? 0 }}</h2>
            <small>New inquiries</small>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 slide-up" style="animation-delay: .6s">
        <div class="advanced-card gradient-6">
            <div class="icon-bg"><i class="fas fa-comments"></i></div>
            <h6>Total Messages</h6>
            <h2>{{ $totalMessages ?? 0 }}</h2>
            <small>All inquiries</small>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 slide-up" style="animation-delay: .7s">
        <div class="advanced-card gradient-6">
            <div class="icon-bg"><i class="fas fa-bell"></i></div>
            <h6>Unread Notifications</h6>
            <h2>{{ $unreadNotifications ?? 0 }}</h2>
            <small>System alerts</small>
        </div>
    </div>
</div>


<!-- CLEAN WHITE STATS -->
<div class="row g-4 mb-4">

    <div class="col-lg-4 col-md-6 slide-up" style="animation-delay: .7s">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted">Overdue Bills</h6>
                    <h3 class="text-danger fw-bold">{{ $overdueBills ?? 0 }}</h3>
                </div>
                <i class="fas fa-exclamation-triangle text-danger fa-2x"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 slide-up" style="animation-delay: .8s">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted">Paid Invoices</h6>
                    <h3 class="text-success fw-bold">{{ $paidInvoices ?? 0 }}</h3>
                </div>
                <i class="fas fa-check-circle text-success fa-2x"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 slide-up" style="animation-delay: .9s">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted">New Customers</h6>
                    <h3 class="text-primary fw-bold">{{ $newCustomers ?? 0 }}</h3>
                </div>
                <i class="fas fa-user-plus text-primary fa-2x"></i>
            </div>
        </div>
    </div>

</div>

<!-- RECENT CUSTOMER MESSAGES -->
<div class="row g-4 mb-4">
    <div class="col-12 slide-up" style="animation-delay: 1s">
        <div class="glass-card">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-envelope text-primary me-2"></i>Recent Customer Messages
                    </h5>
                    <a href="{{ route('admin.customer-messages.index') }}" class="btn btn-sm btn-outline-primary">
                        View All Messages
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if(isset($recentMessages) && $recentMessages->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover ticket-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Message ID</th>
                                <th>Customer</th>
                                <th>Subject</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentMessages as $message)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.customer-messages.show', $message->id) }}" class="text-decoration-none">
                                        {{ $message->message_id }}
                                    </a>
                                </td>
                                <td>
                                    @if($message->customer)
                                        {{ $message->customer->name }}
                                    @else
                                        {{ $message->name }}
                                    @endif
                                </td>
                                <td>{{ Str::limit($message->subject, 30) }}</td>
                                <td>
                                    <span class="ticket-badge">
                                        {{ ucfirst($message->category) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="ticket-badge status-{{ $message->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $message->status)) }}
                                    </span>
                                </td>
                                <td>{{ $message->created_at->format('M d, Y') }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary reply-btn"
                                        data-message-id="{{ $message->id }}"
                                        data-subject="{{ $message->subject }}"
                                        data-customer="{{ $message->customer ? $message->customer->name : $message->name }}"
                                        data-content="{{ $message->message }}">
                                        <i class="fas fa-reply"></i> Reply
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-envelope-open-text text-muted fa-2x mb-3"></i>
                    <p class="text-muted mb-0">No customer messages yet</p>
                    <small class="text-muted">Messages from customers will appear here</small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Reply Message Modal -->
<div class="modal fade" id="replyMessageModal" tabindex="-1" aria-labelledby="replyMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="replyMessageModalLabel">Reply to Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="replyMessageForm">
                    @csrf
                    <input type="hidden" id="messageId" name="message_id">
                    <div class="mb-3">
                        <label for="messageSubject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="messageSubject" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="messageCustomer" class="form-label">Customer</label>
                        <input type="text" class="form-control" id="messageCustomer" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="messageContent" class="form-label">Original Message</label>
                        <textarea class="form-control" id="messageContent" rows="3" readonly></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="replyContent" class="form-label">Your Reply</label>
                        <textarea class="form-control" id="replyContent" name="reply" rows="4" placeholder="Type your response here..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="messageStatus" class="form-label">Status</label>
                        <select class="form-select" id="messageStatus" name="status">
                            <option value="open">Open</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitReply()">Send Reply</button>
            </div>
        </div>
    </div>
</div>

<!-- QUICK ACTIONS -->
<div class="glass-card mt-4 slide-up" style="animation-delay: 1.1s">
    <div class="card-header bg-white border-0">
        <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-bolt text-warning me-2"></i> Quick Actions</h5>
    </div>

    <div class="card-body">
        <div class="row g-3">

            <div class="col-lg-3 col-md-6">
                <a href="{{ route('admin.customers.create') }}" class="quick-btn w-100 d-flex align-items-center">
                    <i class="fas fa-user-plus text-primary"></i>
                    <div>
                        Add Customer<br><small class="text-muted">Register new customer</small>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6">
                <a href="{{ route('admin.billing.billing-invoices') }}" class="quick-btn w-100 d-flex align-items-center">
                    <i class="fas fa-file-invoice-dollar text-success"></i>
                    <div>
                        Generate Bills<br><small class="text-muted">Create invoices</small>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6">
                <a href="{{ route('admin.customer-messages.index') }}" class="quick-btn w-100 d-flex align-items-center">
                    <i class="fas fa-envelope text-info"></i>
                    <div>
                        Customer Messages<br><small class="text-muted">Manage inquiries</small>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6">
                <a href="{{ route('admin.notifications.create') }}" class="quick-btn w-100 d-flex align-items-center">
                    <i class="fas fa-bell text-warning"></i>
                    <div>
                        Send Alerts<br><small class="text-muted">Payment reminders</small>
                    </div>
                </a>
            </div>

        </div>
    </div>
</div>

@endsection