@extends('layouts.customer')

@section('title', 'Invoice #' . $invoice->invoice_number)

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">
                <i class="fas fa-file-invoice text-primary me-1"></i>
                Invoice #{{ $invoice->invoice_number }}
            </h5>
            <small class="text-muted">
                Issued on {{ $invoice->issue_date->format('M d, Y') }}
            </small>
        </div>
        <div>
            <a href="{{ route('customer.invoices.download', $invoice->invoice_id) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-download me-1"></i> PDF
            </a>
            <a href="{{ route('customer.invoices.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                Back
            </a>
        </div>
    </div>

    <!-- Invoice -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <!-- Company & Invoice Info -->
            <div class="row border-bottom pb-3 mb-3">
                <div class="col-md-6">
                    <h4 class="fw-bold text-primary mb-0">NanoSoft</h4>
                    <small class="text-muted d-block">billing@nanosoft.com.bd</small>
                    <small class="text-muted">Invoice Management System</small>
                </div>
                <div class="col-md-6 text-end">
                    <h6 class="fw-bold mb-2">INVOICE</h6>
                    <div><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</div>
                    <div><strong>Date:</strong> {{ $invoice->issue_date->format('M d, Y') }}</div>
                    <div>
                        <strong>Due:</strong>
                        {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}
                    </div>
                    <span class="badge bg-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'pending' ? 'warning' : 'danger') }} mt-2">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>
            </div>

            <!-- Bill To -->
            <div class="mb-3">
                <h6 class="text-uppercase text-muted fw-bold mb-1">Bill To</h6>
                <div class="fw-bold">
                    {{ $invoice->customerProduct->customer->user->name ?? $invoice->customerProduct->customer->name ?? 'Customer' }}
                </div>
                <small class="d-block">{{ $invoice->customerProduct->customer->phone ?? '' }}</small>
                <small class="d-block">{{ $invoice->customerProduct->customer->address ?? '' }}</small>
                <small>{{ $invoice->customerProduct->customer->email ?? '' }}</small>
            </div>

            <!-- Items -->
            <div class="table-responsive mb-3">
                <table class="table table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Description</th>
                            <th class="text-center">Cycle</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">
                                {{ $invoice->customerProduct->product->name }}
                                @if($invoice->customerProduct->billing_cycle_months)
                                    <div class="text-muted small">
                                        ({{ $invoice->customerProduct->billing_cycle_months }} Month Subscription)
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                {{ $invoice->customerProduct->billing_cycle_months ?? 1 }}
                                {{ $invoice->customerProduct->billing_cycle_months == 1 ? 'Month' : 'Months' }}
                            </td>
                            <td class="text-end">
                                ৳{{ number_format($invoice->customerProduct->product->price ?? 0, 2) }}
                            </td>
                            <td class="text-end">
                                ৳{{ number_format($invoice->subtotal, 2) }}
                            </td>
                        </tr>

                        @if($invoice->previous_due > 0)
                        <tr>
                            <td colspan="3">Previous Due</td>
                            <td class="text-end">
                                ৳{{ number_format($invoice->previous_due, 2) }}
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="row justify-content-end mb-3">
                <div class="col-md-5">
                    <table class="table table-sm">
                        <tr>
                            <td>New Charges</td>
                            <td class="text-end">৳{{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>

                        @if($invoice->previous_due > 0)
                        <tr>
                            <td>Previous Due</td>
                            <td class="text-end">৳{{ number_format($invoice->previous_due, 2) }}</td>
                        </tr>
                        @endif

                        <tr class="fw-bold">
                            <td>Total</td>
                            <td class="text-end">৳{{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>

                        <tr class="text-success fw-bold">
                            <td>Paid</td>
                            <td class="text-end">৳{{ number_format($invoice->received_amount, 2) }}</td>
                        </tr>

                        <tr class="fw-bold border-top">
                            <td>Amount Due</td>
                            <td class="text-end text-primary">
                                ৳{{ number_format($invoice->total_amount - $invoice->received_amount, 2) }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Payment History (INSIDE INVOICE) -->
            @if($invoice->payments && $invoice->payments->count() > 0)
            <div class="mb-3">
                <h6 class="text-uppercase text-muted fw-bold mb-2">
                    Payment History
                </h6>

                <table class="table table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Method</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->payments as $payment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $payment->payment_method }}
                                </span>
                            </td>
                            <td class="text-end text-success fw-bold">
                                ৳{{ number_format($payment->amount, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Footer -->
            <div class="text-center text-muted border-top pt-3">
                <small class="fw-bold d-block">Thank you for your business</small>
                <small>This is a system generated invoice</small>
                <small>Contact: billing@nanosoft.com.bd</small>
            </div>

        </div>
    </div>

    <!-- Pay Now -->
    @if($invoice->status != 'paid')
    <div class="d-grid mt-3">
        <a href="{{ route('customer.payments.create', ['invoice_id' => $invoice->invoice_id]) }}"
           class="btn btn-success btn-lg">
            <i class="fas fa-credit-card me-2"></i> Pay Now
        </a>
    </div>
    @endif

</div>
@endsection

