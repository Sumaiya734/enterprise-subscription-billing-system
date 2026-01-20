<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>

    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #555;
            margin: 0;
            padding: 10px;
        }

        .invoice-box {
            max-width: 780px;
            margin: auto;
            padding: 20px;
            border: 1px solid #eee;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
            width: 100%;
        }
        
        .header .company-info {
            flex: 0 0 45%;
            text-align: left;
        }
        
        .header .invoice-details {
            flex: 0 0 45%;
            text-align: right;
        }

        .company-info h1 {
            margin: 0;
            font-size: 22px;
            color: #333;
        }

        .company-info p,
        .invoice-details p {
            margin: 2px 0;
            font-size: 12px;
        }

        .invoice-details h2 {
            margin: 0 0 5px 0;
            font-size: 18px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 11px;
            border-radius: 4px;
            color: #fff;
        }

        .paid { background: #28a745; }
        .partial { background: #ffc107; color: #000; }

        .bill-to {
            margin-bottom: 12px;
        }

        .bill-to h3 {
            font-size: 14px;
            margin-bottom: 5px;
            border-bottom: 1px solid #333;
        }

        .bill-to p {
            margin: 2px 0;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        th {
            background: #f5f5f5;
            font-size: 12px;
            padding: 6px;
            border-bottom: 1px solid #333;
        }

        td {
            padding: 6px;
            font-size: 12px;
            border-bottom: 1px solid #eee;
        }

        .summary {
            margin-top: 10px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            padding: 4px 0;
        }

        .summary-row.total {
            font-weight: bold;
            font-size: 13px;
        }

        .summary-row.paid {
            color: #28a745;
        }

        .summary-row.due {
            font-weight: bold;
            font-size: 14px;
            color: #007bff;
            border-top: 1px solid #333;
            margin-top: 5px;
            padding-top: 5px;
        }

        .actions {
            margin-top: 10px;
            text-align: right;
        }

        .btn {
            padding: 5px 10px;
            font-size: 11px;
            border-radius: 3px;
            border: none;
            cursor: pointer;
            margin-left: 5px;
        }

        .btn-print { background: #007bff; color: #fff; }
        .btn-pay { background: #28a745; color: #fff; }

        .footer {
            margin-top: 12px;
            padding-top: 8px;
            font-size: 11px;
            text-align: center;
            border-top: 1px solid #eee;
        }
    </style>
</head>

<body>

<div class="invoice-box">

    <!-- HEADER -->
    <div class="header">
        <div class="company-info">
            <h1>NanoSoft</h1>
            <p>Dhaka, Bangladesh</p>
            <p>Email: billing@nanosoft.com.bd</p>
            <p>Phone: 01XXXXXXXXX</p>
        </div>

        <div class="invoice-details">
            <h2>INVOICE</h2>
            <p><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
            <p><strong>Date:</strong> {{ $invoice->issue_date->format('d M Y') }}</p>
            <p>
                <span class="status-badge {{ $invoice->status == 'paid' ? 'paid' : ($invoice->received_amount > 0 ? 'partial' : 'unpaid') }}">
                    {{ $invoice->status == 'paid' ? 'PAID' : ($invoice->received_amount > 0 ? 'PARTIALLY PAID' : 'UNPAID') }}
                </span>
            </p>
        </div>
    </div>

    <!-- BILL TO -->
    <div class="bill-to">
        <h3>Bill To</h3>
        <p><strong>Name:</strong> {{ $invoice->customerProduct->customer->user->name ?? $invoice->customerProduct->customer->name ?? 'Customer' }}</p>
        <p><strong>Email:</strong> {{ $invoice->customerProduct->customer->email ?? '' }}</p>
        <p><strong>Phone:</strong> {{ $invoice->customerProduct->customer->phone ?? '' }}</p>
    </div>

    <!-- ITEMS -->
    <table>
        <thead>
            <tr>
                <th align="left">Description</th>
                <th align="center">Qty</th>
                <th align="right">Price</th>
                <th align="right">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $invoice->customerProduct->product->name }}</td>
                <td align="center">{{ $invoice->customerProduct->billing_cycle_months ?? 1 }}</td>
                <td align="right">৳{{ number_format($invoice->customerProduct->product->price ?? 0, 0) }}</td>
                <td align="right">৳{{ number_format($invoice->subtotal, 0) }}</td>
            </tr>
            @if($invoice->previous_due > 0)
            <tr>
                <td>Previous Due Amount</td>
                <td align="center">—</td>
                <td align="right">—</td>
                <td align="right">৳{{ number_format($invoice->previous_due, 0) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- SUMMARY -->
    <div class="summary">
        <div class="summary-row">
            <span>Subtotal</span>
            <span>৳{{ number_format($invoice->subtotal, 0) }}</span>
        </div>
        @if($invoice->previous_due > 0)
        <div class="summary-row">
            <span>Previous Due</span>
            <span>৳{{ number_format($invoice->previous_due, 0) }}</span>
        </div>
        @endif
        <div class="summary-row">
            <span>Tax</span>
            <span>৳0</span>
        </div>
        <div class="summary-row total">
            <span>Total</span>
            <span>৳{{ number_format($invoice->total_amount, 0) }}</span>
        </div>
        <div class="summary-row paid">
            <span>Amount Paid</span>
            <span>৳{{ number_format($invoice->received_amount, 0) }}</span>
        </div>
        <div class="summary-row due">
            <span>Amount Due</span>
            <span>৳{{ number_format($invoice->total_amount - $invoice->received_amount, 0) }}</span>
        </div>
    </div>

    <!-- PAYMENT HISTORY -->
    @if($invoice->payments && $invoice->payments->count() > 0)
    <h3 style="margin-top:12px;font-size:14px;border-bottom:1px solid #333;">
        Payment History
    </h3>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Method</th>
                <th>Transaction ID</th>
                <th align="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->payments as $payment)
            <tr>
                <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                <td>{{ $payment->payment_method }}</td>
                <td>{{ $payment->transaction_id ?? '—' }}</td>
                <td align="right">৳{{ number_format($payment->amount, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- FOOTER -->
    <div class="footer">
        Thank you for your business.
    </div>

</div>

</body>
</html>

