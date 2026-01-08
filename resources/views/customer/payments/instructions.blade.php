@extends('layouts.customer')

@section('title', 'Payment Instructions - Nanosoft')

@section('content')
    <div class="payment-instructions-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Success Header -->
                    <div class="text-center mb-5">
                        <div class="success-icon mb-3">
                            <i class="fas fa-check-circle text-success fa-4x"></i>
                        </div>
                        <h2 class="text-success mb-2">Subscription Created Successfully!</h2>
                        <p class="text-muted">Please complete your payment to activate your subscription.</p>
                    </div>

                    <!-- Payment Instructions Card -->
                    <div class="card border-0 shadow-lg mb-4">
                        <div class="card-header bg-primary text-white py-4">
                            <h4 class="mb-0 text-center">
                                <i class="fas fa-credit-card me-2"></i>Payment Instructions
                            </h4>
                        </div>
                        <div class="card-body p-4">
                            @if($paymentResult && isset($paymentResult['instructions']))
                                <div class="payment-method-info mb-4">
                                    <h5 class="text-primary mb-3">
                                        {{ $paymentResult['gateway'] === 'rocket' ? '🚀 Rocket Payment' : '🏦 Bank Transfer' }}
                                    </h5>
                                    
                                    <div class="instructions-list">
                                        @foreach($paymentResult['instructions'] as $instruction)
                                            <div class="instruction-item d-flex align-items-start mb-3">
                                                <div class="step-number me-3">
                                                    <span class="badge bg-primary rounded-circle">{{ $loop->iteration }}</span>
                                                </div>
                                                <div class="instruction-text">
                                                    <p class="mb-0">{{ $instruction }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Important:</strong> {{ $paymentResult['message'] }}
                                </div>
                            @endif

                            <!-- Payment Details -->
                            <div class="payment-details bg-light p-4 rounded mb-4">
                                <h6 class="mb-3">Payment Details:</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item mb-2">
                                            <strong>Invoice Number:</strong>
                                            <span class="text-primary">{{ $payment->invoice->invoice_number }}</span>
                                        </div>
                                        <div class="detail-item mb-2">
                                            <strong>Product:</strong>
                                            {{ $payment->invoice->customerProduct->product->name }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item mb-2">
                                            <strong>Amount:</strong>
                                            <span class="text-success h5">৳{{ number_format($payment->amount, 0) }}</span>
                                        </div>
                                        <div class="detail-item mb-2">
                                            <strong>Payment Method:</strong>
                                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Next Steps -->
                            <div class="next-steps">
                                <h6 class="mb-3">What happens next?</h6>
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-success">
                                            <i class="fas fa-check text-white"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6>Subscription Created</h6>
                                            <p class="text-muted mb-0">Your subscription has been created and is waiting for payment.</p>
                                        </div>
                                    </div>
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-warning">
                                            <i class="fas fa-clock text-white"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6>Complete Payment</h6>
                                            <p class="text-muted mb-0">Follow the instructions above to complete your payment.</p>
                                        </div>
                                    </div>
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-primary">
                                            <i class="fas fa-rocket text-white"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6>Subscription Activated</h6>
                                            <p class="text-muted mb-0">Once payment is verified, your subscription will be activated automatically.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="text-center">
                        <a href="{{ route('customer.products.index') }}" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-box me-2"></i> View My Subscriptions
                        </a>
                        <a href="{{ route('customer.payments.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-history me-2"></i> Payment History
                        </a>
                    </div>

                    <!-- Support Section -->
                    <div class="support-section mt-5 text-center">
                        <div class="card border-0 bg-light">
                            <div class="card-body p-4">
                                <h6 class="mb-3">Need Help?</h6>
                                <p class="text-muted mb-3">
                                    If you have any questions about your payment or subscription, our support team is here to help.
                                </p>
                                <a href="{{ route('customer.support.create') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-headset me-2"></i> Contact Support
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .payment-instructions-page {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .success-icon {
            animation: bounceIn 0.8s ease-out;
        }

        .instruction-item {
            padding: 1rem;
            background: white;
            border-radius: 8px;
            border-left: 4px solid #007bff;
            margin-bottom: 1rem;
        }

        .step-number .badge {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }

        .timeline-marker {
            position: absolute;
            left: -2rem;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .timeline-content {
            padding-left: 1rem;
        }

        .detail-item {
            padding: 0.5rem 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                opacity: 1;
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @media (max-width: 768px) {
            .payment-instructions-page {
                padding: 1rem 0;
            }
            
            .timeline {
                padding-left: 1.5rem;
            }
            
            .timeline-marker {
                left: -1.5rem;
            }
        }
    </style>
@endsection