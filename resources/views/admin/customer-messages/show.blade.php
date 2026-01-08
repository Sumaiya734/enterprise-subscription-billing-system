@extends('layouts.admin')

@section('title', 'View Message - ' . $message->subject)

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Customer Message Details</h4>
                    <div class="card-tools">
                        <a href="{{ route('admin.customer-messages.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Messages
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Message Header -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Message ID:</strong></td>
                                    <td>{{ $message->message_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Customer Name:</strong></td>
                                    <td>{{ $message->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $message->email }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Department:</strong></td>
                                    <td>{{ ucfirst($message->department) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Category:</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($message->category) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Priority:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $message->priority == 'high' || $message->priority == 'urgent' ? 'danger' : 'secondary' }}">
                                            {{ ucfirst($message->priority) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Subject -->
                    <div class="mb-4">
                        <h5><strong>Subject:</strong> {{ $message->subject }}</h5>
                    </div>
                    
                    <!-- Original Message -->
                    <div class="mb-4">
                        <h6>Original Message:</h6>
                        <div class="border p-3 bg-light rounded">
                            <p class="mb-0">{{ $message->message }}</p>
                        </div>
                    </div>
                    
                    <!-- Reply Section -->
                    <div class="mt-5">
                        <h5>Reply to Message</h5>
                        
                        <form id="replyForm" method="POST" action="{{ route('admin.customer-messages.reply', $message->id) }}">
                            @csrf
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="open" {{ $message->status == 'open' ? 'selected' : '' }}>Open</option>
                                        <option value="in_progress" {{ $message->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="resolved" {{ $message->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                        <option value="closed" {{ $message->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="admin_reply" class="form-label">Your Reply</label>
                                <textarea name="admin_reply" id="admin_reply" class="form-control" rows="5" required 
                                    placeholder="Type your response to the customer here...">{{ old('admin_reply') }}</textarea>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary" id="replyBtn">
                                    <i class="fas fa-paper-plane me-1"></i> Send Reply
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Admin Reply (if exists) -->
                    @if($message->admin_reply)
                    <div class="mt-5">
                        <h6>Previous Admin Reply:</h6>
                        <div class="border p-3 bg-success text-white rounded">
                            <p class="mb-0">{{ $message->admin_reply }}</p>
                            @if($message->replied_at)
                            <small>Replied on: {{ $message->replied_at->format('M d, Y H:i') }}</small>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const replyForm = document.getElementById('replyForm');
    const replyBtn = document.getElementById('replyBtn');
    
    replyForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent normal form submission
        
        // Show loading state
        replyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sending...';
        replyBtn.disabled = true;
        
        // Prepare form data
        const formData = new FormData(replyForm);
        
        // Send AJAX request
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Show success message
                alert(data.message);
                
                // Optionally reset the form
                document.getElementById('admin_reply').value = '';
                
                // Reload the page to show the updated reply
                location.reload();
            } else {
                // Show error message
                alert('Error: ' + (data.message || 'Unknown error occurred'));
                
                // Re-enable button
                replyBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Send Reply';
                replyBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while sending the reply. Please try again.');
            
            // Re-enable button
            replyBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Send Reply';
            replyBtn.disabled = false;
        });
    });
});
</script>

@endsection