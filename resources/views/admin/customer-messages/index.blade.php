@extends('layouts.admin')

@section('title', 'Customer Messages')

@section('content')

{{-- Page-specific CSS --}}
<style>
    .stat-card {
        border-radius: 12px;
        transition: all 0.25s ease;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.1);
    }

    .stat-title {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 1.6rem;
        font-weight: 600;
    }

    .customer-messages-table th {
        white-space: nowrap;
    }

    .customer-messages-table td {
        vertical-align: middle;
    }

    .badge {
        font-size: 0.75rem;
        padding: 6px 10px;
    }

    .card-header {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .table-hover tbody tr:hover {
        background-color: #f4f6f9;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            {{-- Statistics Cards --}}
            @isset($stats)
            <div class="row mb-4">
                @php
                    $statItems = [
                        ['label' => 'Total', 'icon' => 'envelope', 'color' => 'primary', 'value' => $stats['total'] ?? 0],
                        ['label' => 'Open', 'icon' => 'clock', 'color' => 'warning', 'value' => $stats['open'] ?? 0],
                        ['label' => 'In Progress', 'icon' => 'spinner', 'color' => 'info', 'value' => $stats['in_progress'] ?? 0],
                        ['label' => 'Resolved', 'icon' => 'check-circle', 'color' => 'success', 'value' => $stats['resolved'] ?? 0],
                        ['label' => 'Urgent', 'icon' => 'exclamation-triangle', 'color' => 'danger', 'value' => $stats['urgent'] ?? 0],
                    ];
                @endphp

                @foreach($statItems as $item)
                <div class="col-md-2 col-6 mb-2">
                    <div class="card stat-card">
                        <div class="card-body text-center">
                            <div class="text-{{ $item['color'] }} mb-2">
                                <i class="fas fa-{{ $item['icon'] }} fa-2x"></i>
                            </div>
                            <div class="stat-title">{{ $item['label'] }}</div>
                            <div class="stat-value">{{ $item['value'] }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endisset

            <div class="card">
                <div class="card-header">
                    Customer Messages
                </div>

                <div class="card-body">

                    {{-- Filters --}}
                    <form method="GET" class="row g-2 mb-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control"
                                   placeholder="Search messages..."
                                   value="{{ request('search') }}">
                        </div>

                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                @foreach(['open','in_progress','resolved','closed'] as $status)
                                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_',' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select name="priority" class="form-select">
                                <option value="">All Priority</option>
                                @foreach(['low','normal','high','urgent'] as $priority)
                                    <option value="{{ $priority }}" {{ request('priority') === $priority ? 'selected' : '' }}>
                                        {{ ucfirst($priority) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach(['technical','billing','sales','feedback'] as $category)
                                    <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                        {{ ucfirst($category) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 d-flex gap-2">
                            <button class="btn btn-primary w-100">Filter</button>
                            <a href="{{ route('admin.customer-messages.index') }}" class="btn btn-secondary w-100">Clear</a>
                        </div>
                    </form>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-hover customer-messages-table">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($messages ?? [] as $message)
                                <tr>
                                    <td>{{ $message->message_id ?? $message->id }}</td>
                                    <td>{{ $message->name }}</td>
                                    <td>{{ $message->email }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($message->subject, 40) }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ ucfirst($message->category ?? '-') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ ucfirst(str_replace('_',' ', $message->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">
                                            {{ ucfirst($message->priority ?? '-') }}
                                        </span>
                                    </td>
                                    <td>{{ optional($message->created_at)->format('M d, Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.customer-messages.show', $message->id) }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        No customer messages found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if(isset($messages) && method_exists($messages, 'links'))
                        <div class="d-flex justify-content-center">
                            {{ $messages->appends(request()->query())->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
