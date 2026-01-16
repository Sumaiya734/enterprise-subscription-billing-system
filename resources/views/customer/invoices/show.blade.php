@extends('layouts.customer')

@section('title', 'Invoice #' . $invoice->invoice_number)

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="fas fa-file-invoice me-2 text-primary"></i>Invoice #{{ $invoice->invoice_number }}
                    </h1>
                    <p class="text-muted mb-0">Issued on {{ $invoice->issue_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <a href="{{ route('customer.invoices.download', $invoice->invoice_id) }}" class="btn btn-primary">
                        <i class="fas fa-download me-1"></i> Download PDF
                    </a>
                    <a href="{{ route('customer.invoices.index') }}" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Invoice Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-5">
                        <!-- Header -->
                        <div class="d-flex justify-content-between mb-5">
                            <div>
                                <h3 class="fw-bold text-primary">NanoSoft</h3>
                                <p class="text-muted">billing@nanosoft.com.bd</p>
                            </div>
                            <div class="text-end">
                                <h5 class="mb-1">Invoice #{{ $invoice->invoice_number }}</h5>
                                <p class="mb-0">Date: {{ $invoice->issue_date->format('M d, Y') }}</p>
                                <div class="mt-2">
                                    <span class="badge bg-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'pending' ? 'warning' : 'danger') }} fs-6">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Bill To -->
                        <div class="row mb-5">
                            <div class="col-sm-6">
                                <h6 class="text-muted text-uppercase fw-bold mb-3">Bill To</h6>
                                <h5 class="fw-bold">{{ $invoice->customerProduct->customer->user->name ?? $invoice->customerProduct->customer->name ?? 'Customer' }}</h5>
                                <p class="mb-0">{{ $invoice->customerProduct->customer->phone ?? '' }}</p>
                                <p class="mb-0">{{ $invoice->customerProduct->customer->address ?? '' }}</p>
                            </div>
                        </div>

                        <!-- Items -->
                        <div class="table-responsive mb-5">
                            <table class="table">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0">Item Description</th>
                                        <th class="border-0 text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $invoice->customerProduct->product->name }}</div>
                                            @if($invoice->customerProduct->billing_cycle_months)
                                                <small class="text-muted">({{ $invoice->customerProduct->billing_cycle_months }} Month Subscription)</small>
                                            @endif
                                        </td>
                                        <td class="text-end">৳{{ number_format($invoice->subtotal, 2) }}</td>
                                    </tr>
                                    @if($invoice->previous_due > 0)
                                    <tr>
                                        <td>Previous Due</td>
                                        <td class="text-end">৳{{ number_format($invoice->previous_due, 2) }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-end fw-bold border-top">Total</td>
                                        <td class="text-end fw-bold border-top">৳{{ number_format($invoice->total_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end text-success">Paid</td>
                                        <td class="text-end text-success">৳{{ number_format($invoice->received_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fw-bold fs-5">Due Amount</td>
                                        <td class="text-end fw-bold fs-5 text-primary">৳{{ number_format($invoice->total_amount - $invoice->received_amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Footer -->
                        <div class="text-center text-muted border-top pt-4">
                            <p class="mb-0">Thank you for your business!</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Payment History -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0"><i class="fas fa-history me-2 text-info"></i>Payment History</h6>
                    </div>
                    <div class="card-body">
                        @if($invoice->payments && $invoice->payments->count() > 0)
                            <div class="timeline">
                                @foreach($invoice->payments as $payment)
                                    <div class="timeline-item pb-3 border-start ps-3 ms-2 position-relative">
                                        <div class="position-absolute start-0 top-0 translate-middle rounded-circle bg-success p-1 border border-white"></div>
                                        <p class="mb-1 fw-bold">Payment Received</p>
                                        <small class="text-muted d-block mb-1">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</small>
                                        <span class="text-success fw-bold">৳{{ number_format($payment->amount, 2) }}</span>
                                        <span class="badge bg-light text-dark border ms-2">{{ $payment->payment_method }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center py-3 mb-0">No payments recorded yet.</p>
                        @endif
                    </div>
                </div>
                
                @if($invoice->status != 'paid')
                <div class="d-grid">
                    <a href="{{ route('customer.payments.create', ['invoice_id' => $invoice->invoice_id]) }}" class="btn btn-success btn-lg">
                        <i class="fas fa-credit-card me-2"></i> Pay Now
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
