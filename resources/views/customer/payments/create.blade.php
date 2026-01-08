@extends('layouts.customer')

@section('title', 'Make Payment - Nanosoft')

@section('content')
    <div class="make-payment-page">
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
                                <li class="breadcrumb-item active">Make Payment</li>
                            </ol>
                        </nav>
                        <h1 class="h2 mb-2">
                            <i class="fas fa-plus-circle me-2 text-primary"></i>Make a Payment
                        </h1>
                        <p class="text-muted mb-0">Pay for your outstanding invoices quickly and securely.</p>
                    </div>

                    <!-- Payment Form -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-4">
                            <h5 class="mb-0">
                                <i class="fas fa-money-bill-wave me-2 text-success"></i>Payment Details
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('customer.payments.store') }}">
                                @csrf

                                <!-- Invoice Selection -->
                                <div class="mb-4">
                                    <label for="invoice_id" class="form-label">Select Invoice *</label>
                                    @if($invoice)
                                        <!-- Pre-selected invoice -->
                                        <div class="selected-invoice p-3 bg-light rounded">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">{{ $invoice->invoice_number }}</h6>
                                                    <p class="text-muted mb-0">
                                                        {{ $invoice->customerProduct->product->name ?? 'Product' }}
                                                    </p>
                                                </div>
                                                <div class="text-end">
                                                    <div class="h5 text-primary mb-0">৳{{ number_format($invoice->next_due, 2) }}</div>
                                                    <small class="text-muted">Due Amount</small>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="invoice_id" value="{{ $invoice->invoice_id }}">
                                    @else
                                        <!-- Invoice selection dropdown -->
                                        <select class="form-select @error('invoice_id') is-invalid @enderror" 
                                                id="invoice_id" name="invoice_id" required>
                                            <option value="">Choose an unpaid invoice</option>
                                            @forelse($unpaidInvoices as $unpaidInvoice)
                                                <option value="{{ $unpaidInvoice->invoice_id }}" 
                                                        data-amount="{{ $unpaidInvoice->next_due }}">
                                                    {{ $unpaidInvoice->invoice_number }} - 
                                                    {{ $unpaidInvoice->customerProduct->product->name ?? 'Product' }} - 
                                                    ৳{{ number_format($unpaidInvoice->next_due, 2) }} due
                                                </option>
                                            @empty
                                                <option value="" disabled>No unpaid invoices found</option>
                                            @endforelse
                                        </select>
                                        @error('invoice_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </div>

                                <!-- Payment Amount -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="amount" class="form-label">Payment Amount *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">৳</span>
                                            <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                                   id="amount" name="amount" value="{{ old('amount') }}" 
                                                   min="0.01" step="0.01" required>
                                        </div>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="payment_date" class="form-label">Payment Date *</label>
                                        <input type="date" class="form-control @error('payment_date') is-invalid @enderror"
                                               id="payment_date" name="payment_date" 
                                               value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                        @error('payment_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Payment Method -->
                                <div class="mb-4">
                                    <label class="form-label">Payment Method *</label>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="payment-method-card">
                                                <input type="radio" class="btn-check" name="payment_method" id="bkash_pay" value="bkash" {{ old('payment_method') == 'bkash' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-primary w-100 p-3" for="bkash_pay">
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-3">📱</span>
                                                        <div>
                                                            <strong>bKash</strong>
                                                            <div class="small text-muted">Mobile Banking</div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="payment-method-card">
                                                <input type="radio" class="btn-check" name="payment_method" id="nagad_pay" value="nagad" {{ old('payment_method') == 'nagad' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-success w-100 p-3" for="nagad_pay">
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-3">💳</span>
                                                        <div>
                                                            <strong>Nagad</strong>
                                                            <div class="small text-muted">Mobile Banking</div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="payment-method-card">
                                                <input type="radio" class="btn-check" name="payment_method" id="rocket_pay" value="rocket" {{ old('payment_method') == 'rocket' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-warning w-100 p-3" for="rocket_pay">
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-3">🚀</span>
                                                        <div>
                                                            <strong>Rocket</strong>
                                                            <div class="small text-muted">DBBL Mobile Banking</div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="payment-method-card">
                                                <input type="radio" class="btn-check" name="payment_method" id="card_pay" value="card" {{ old('payment_method') == 'card' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-info w-100 p-3" for="card_pay">
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-3">💳</span>
                                                        <div>
                                                            <strong>Card Payment</strong>
                                                            <div class="small text-muted">Visa, MasterCard</div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="payment-method-card">
                                                <input type="radio" class="btn-check" name="payment_method" id="bank_pay" value="bank" {{ old('payment_method') == 'bank' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-secondary w-100 p-3" for="bank_pay">
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-3">🏦</span>
                                                        <div>
                                                            <strong>Bank Transfer</strong>
                                                            <div class="small text-muted">Direct Transfer</div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="payment-method-card">
                                                <input type="radio" class="btn-check" name="payment_method" id="cash_pay" value="cash" {{ old('payment_method') == 'cash' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-dark w-100 p-3" for="cash_pay">
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-3">💵</span>
                                                        <div>
                                                            <strong>Cash Payment</strong>
                                                            <div class="small text-muted">Pay at Office</div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('payment_method')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Transaction ID -->
                                <div class="mb-4">
                                    <label for="transaction_id" class="form-label">Transaction ID (Optional)</label>
                                    <input type="text" class="form-control @error('transaction_id') is-invalid @enderror"
                                           id="transaction_id" name="transaction_id" value="{{ old('transaction_id') }}"
                                           placeholder="Enter transaction ID if available">
                                    <small class="text-muted">Provide transaction ID for mobile banking or online payments</small>
                                    @error('transaction_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Notes -->
                                <div class="mb-4">
                                    <label for="notes" class="form-label">Notes (Optional)</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" 
                                              placeholder="Any additional notes about this payment...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('customer.payments.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Back to Payments
                                    </a>
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-credit-card me-2"></i> Submit Payment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <div class="payment-info mt-4">
                        <div class="card border-0 bg-light">
                            <div class="card-body p-4">
                                <h6 class="mb-3">
                                    <i class="fas fa-info-circle me-2 text-info"></i>Payment Information
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success me-2"></i>
                                                Payments are processed securely
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success me-2"></i>
                                                Instant confirmation for digital payments
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success me-2"></i>
                                                24/7 payment support available
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success me-2"></i>
                                                Multiple payment methods accepted
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .make-payment-page {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .payment-method-card .btn-check:checked + .btn {
            background: linear-gradient(45deg, var(--bs-primary), var(--bs-success));
            border-color: var(--bs-primary);
            color: white;
            transform: scale(1.02);
        }
        
        .payment-method-card .btn {
            transition: all 0.3s ease;
            height: 100%;
            border: 2px solid #e9ecef;
        }
        
        .payment-method-card .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .selected-invoice {
            border: 2px solid #28a745;
        }

        @media (max-width: 768px) {
            .make-payment-page {
                padding: 1rem 0;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const invoiceSelect = document.getElementById('invoice_id');
            const amountInput = document.getElementById('amount');

            if (invoiceSelect) {
                invoiceSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption.dataset.amount) {
                        amountInput.value = parseFloat(selectedOption.dataset.amount).toFixed(2);
                    }
                });
            }
        });
    </script>
@endsection