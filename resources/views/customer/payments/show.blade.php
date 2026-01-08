@extends('layouts.customer')

@section('title', 'Payment Details - Nanosoft')

@section('content')
    <div class="payment-details-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Page Header -->
                    <div class="page-header mb-4">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-2">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('customer.payments.index') }}" class="text-decoration-none">
                                        <i class="fas fa-credit-card me-1"></i>Payments
                                    </a>
                                </li>
                                <li class="breadcrumb-item active">Payment Details</li>
                            </ol>
                        </nav>
                        <h1 class="h2 mb-2">
                            <i class="fas fa-receipt me-2 text-primary"></i>Payment Details
                        </h1>
                        <p class="text-muted mb-0">View detailed information about your payment.</p>
                    </div>

                    <!-- Payment Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-success text-white py-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-check-circle me-2"></i>Payment Successful
                                </h5>
                                <span class="badge bg-light text-success px-3 py-2">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="payment-info-item mb-4">
                                        <label class="text-muted small fw-bold">Payment ID</label>
                                        <h6 class="fw-bold text-primary">#{{ $payment->payment_id }}</h6>
                                    </div>
                                    
                                    <div class="payment-info-item mb-4">
                                        <label class="text-muted small fw-bold">Amount Paid</label>
                                        <h4 class="fw-bold text-success mb-0">
                                            ৳{{ number_format($payment->amount, 2) }}
                                        </h4>
                                    </div>

                                    <div class="payment-info-item mb-4">
                                        <label class="text-muted small fw-bold">Payment Method</label>
                                        <p class="mb-0 fw-bold">{{ $payment->payment_method_text }}</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="payment-info-item mb-4">
                                        <label class="text-muted small fw-bold">Payment Date</label>
                                        <p class="mb-0 fw-bold">{{ $payment->formatted_payment_date }}</p>
                                        <small class="text-muted">{{ $payment->payment_date->diffForHumans() }}</small>
                                    </div>

                                    <div class="payment-info-item mb-4">
                                        <label class="text-muted small fw-bold">Transaction ID</label>
                                        <p class="mb-0 fw-bold">
                                            {{ $payment->transaction_id ?? 'N/A' }}
                                        </p>
                                    </div>

                                    <div class="payment-info-item mb-4">
                                        <label class="text-muted small fw-bold">Status</label>
                                        <div>
                                            @if($payment->status === 'completed')
                                                <span class="badge bg-success rounded-pill px-3 py-2">
                                                    <i class="fas fa-check-circle me-1"></i> Completed
                                                </span>
                                            @elseif($payment->status === 'pending')
                                                <span class="badge bg-warning rounded-pill px-3 py-2">
                                                    <i class="fas fa-clock me-1"></i> Pending
                                                </span>
                                            @else
                                                <span class="badge bg-danger rounded-pill px-3 py-2">
                                                    <i class="fas fa-times-circle me-1"></i> {{ ucfirst($payment->status) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($payment->notes)
                                <div class="payment-notes mt-4 p-3 bg-light rounded">
                                    <label class="text-muted small fw-bold">Notes</label>
                                    <p class="mb-0">{{ $payment->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Invoice Information -->
                    @if($payment->invoice)
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-invoice me-2 text-secondary"></i>Related Invoice
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="invoice-info-item mb-3">
                                            <label class="text-muted small fw-bold">Invoice Number</label>
                                            <p class="mb-0 fw-bold text-primary">{{ $payment->invoice->invoice_number }}</p>
                                        </div>
                                        
                                        <div class="invoice-info-item mb-3">
                                            <label class="text-muted small fw-bold">Product/Service</label>
                                            <p class="mb-0 fw-bold">
                                                {{ $payment->invoice->customerProduct->product->name ?? 'Product' }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="invoice-info-item mb-3">
                                            <label class="text-muted small fw-bold">Invoice Total</label>
                                            <p class="mb-0 fw-bold">৳{{ number_format($payment->invoice->total_amount, 2) }}</p>
                                        </div>
                                        
                                        <div class="invoice-info-item mb-3">
                                            <label class="text-muted small fw-bold">Invoice Status</label>
                                            <span class="badge bg-{{ $payment->invoice->status === 'paid' ? 'success' : ($payment->invoice->status === 'partial' ? 'warning' : 'danger') }} rounded-pill">
                                                {{ ucfirst($payment->invoice->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <a href="{{ route('customer.invoices.show', $payment->invoice->invoice_id) }}" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> View Full Invoice
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="text-center">
                        <a href="{{ route('customer.payments.index') }}" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-arrow-left me-2"></i> Back to Payments
                        </a>
                        <a href="{{ route('customer.payments.download', $payment->payment_id) }}" 
                           class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-download me-2"></i> Download Receipt
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .payment-details-page {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .payment-info-item,
        .invoice-info-item {
            padding: 1rem;
            background: #f8fafc;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        .payment-notes {
            border-left: 4px solid #28a745;
        }

        @media (max-width: 768px) {
            .payment-details-page {
                padding: 1rem 0;
            }
        }
    </style>
@endsection