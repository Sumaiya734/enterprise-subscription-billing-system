<?php
// Customer Messages - Direct PHP with Laravel Layout Integration
require_once '../vendor/autoload.php';

$app = require_once '../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get messages from database
try {
    $messages = App\Models\CustomerMessage::latest()->paginate(15);
    $stats = [
        'total' => App\Models\CustomerMessage::count(),
        'open' => App\Models\CustomerMessage::where('status', 'open')->count(),
        'in_progress' => App\Models\CustomerMessage::where('status', 'in_progress')->count(),
        'resolved' => App\Models\CustomerMessage::where('status', 'resolved')->count(),
        'urgent' => App\Models\CustomerMessage::where('priority', 'urgent')->count(),
    ];
} catch (Exception $e) {
    $messages = collect([]);
    $stats = ['total' => 0, 'open' => 0, 'in_progress' => 0, 'resolved' => 0, 'urgent' => 0];
}

// Render the view using Laravel's view system but with direct output
$viewContent = '
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-2 col-6">
                    <div class="stat-card card">
                        <div class="card-body text-center">
                            <div class="stat-icon text-primary mb-2">
                                <i class="fas fa-envelope fa-2x"></i>
                            </div>
                            <div class="stat-title">Total</div>
                            <div class="stat-value">' . $stats['total'] . '</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="stat-card card">
                        <div class="card-body text-center">
                            <div class="stat-icon text-warning mb-2">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                            <div class="stat-title">Open</div>
                            <div class="stat-value">' . $stats['open'] . '</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="stat-card card">
                        <div class="card-body text-center">
                            <div class="stat-icon text-info mb-2">
                                <i class="fas fa-spinner fa-2x"></i>
                            </div>
                            <div class="stat-title">In Progress</div>
                            <div class="stat-value">' . $stats['in_progress'] . '</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="stat-card card">
                        <div class="card-body text-center">
                            <div class="stat-icon text-success mb-2">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                            <div class="stat-title">Resolved</div>
                            <div class="stat-value">' . $stats['resolved'] . '</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="stat-card card">
                        <div class="card-body text-center">
                            <div class="stat-icon text-danger mb-2">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                            <div class="stat-title">Urgent</div>
                            <div class="stat-value">' . $stats['urgent'] . '</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages Table -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Customer Messages</h4>
                </div>
                <div class="card-body">
                    <!-- Search and Filters -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" class="d-flex gap-2 flex-wrap">
                                <input type="text" name="search" class="form-control" placeholder="Search messages..." style="max-width: 250px;">
                                <select name="status" class="form-select" style="max-width: 150px;">
                                    <option value="">All Status</option>
                                    <option value="open">Open</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                                <select name="priority" class="form-select" style="max-width: 150px;">
                                    <option value="">All Priority</option>
                                    <option value="low">Low</option>
                                    <option value="normal">Normal</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                                <select name="category" class="form-select" style="max-width: 150px;">
                                    <option value="">All Categories</option>
                                    <option value="technical">Technical</option>
                                    <option value="billing">Billing</option>
                                    <option value="sales">Sales</option>
                                    <option value="feedback">Feedback</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="customer-messages.php" class="btn btn-secondary">Clear</a>
                            </form>
                        </div>
                    </div>

                    <!-- Messages Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Message ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>';

if ($messages->count() > 0) {
    foreach ($messages as $message) {
        $categoryClass = match($message->category ?? '') {
            'technical' => 'info',
            'billing' => 'warning',
            'sales' => 'primary',
            'feedback' => 'secondary',
            default => 'light'
        };
        
        $statusClass = match($message->status) {
            'open' => 'warning',
            'in_progress' => 'primary',
            'resolved' => 'success',
            default => 'secondary'
        };
        
        $priorityClass = match($message->priority ?? '') {
            'low' => 'secondary',
            'normal' => 'info',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'secondary'
        };
        
        $viewContent .= '
                                <tr>
                                    <td>
                                        <a href="#" class="text-decoration-none">
                                            ' . ($message->message_id ?? $message->id) . '
                                        </a>
                                    </td>
                                    <td>' . htmlspecialchars($message->name) . '</td>
                                    <td>' . htmlspecialchars($message->email) . '</td>
                                    <td>' . htmlspecialchars(substr($message->subject, 0, 40)) . '</td>
                                    <td>';
        
        if ($message->category) {
            $viewContent .= '<span class="badge bg-' . $categoryClass . '">' . ucfirst($message->category) . '</span>';
        } else {
            $viewContent .= '<span class="text-muted">-</span>';
        }
        
        $viewContent .= '</td>
                                    <td>
                                        <span class="badge bg-' . $statusClass . '">
                                            ' . ucfirst(str_replace('_', ' ', $message->status)) . '
                                        </span>
                                    </td>
                                    <td>';
        
        if ($message->priority) {
            $viewContent .= '<span class="badge bg-' . $priorityClass . '">' . ucfirst($message->priority) . '</span>';
        } else {
            $viewContent .= '<span class="text-muted">-</span>';
        }
        
        $viewContent .= '</td>
                                    <td>' . $message->created_at->format('M d, Y H:i') . '</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>';
    }
} else {
    $viewContent .= '
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        No customer messages found.
                                    </td>
                                </tr>';
}

$viewContent .= '
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';

// Try to use Laravel's view system
try {
    // Create a temporary view file
    $tempViewPath = resource_path('views/temp_customer_messages.blade.php');
    file_put_contents($tempViewPath, '@extends(\'layouts.admin\')

@section(\'title\', \'Customer Messages\')

@section(\'content\')
' . $viewContent . '
@endsection');

    // Render using Laravel
    echo view('temp_customer_messages')->render();
    
    // Clean up
    unlink($tempViewPath);
    
} catch (Exception $e) {
    // Fallback to direct HTML output
    include '../resources/views/layouts/admin.blade.php';
}
?>