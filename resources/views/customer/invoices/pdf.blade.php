<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
            font-size: 14px;
            line-height: 20px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
        }
        .header {
            margin-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .invoice-details {
            text-align: right;
        }
        table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }
        table td {
            padding: 5px;
            vertical-align: top;
        }
        table tr.top table td {
            padding-bottom: 20px;
        }
        table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        table tr.item td {
            border-bottom: 1px solid #eee;
        }
        table tr.item.last td {
            border-bottom: none;
        }
        table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            color: white;
            font-size: 12px;
            display: inline-block;
        }
        .bg-success { background-color: #28a745; }
        .bg-warning { background-color: #ffc107; color: #000; }
        .bg-danger { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <div class="logo">NanoSoft</div>
                            </td>
                            <td class="invoice-details">
                                <strong>Invoice #:</strong> {{ $invoice->invoice_number }}<br>
                                <strong>Date:</strong> {{ $invoice->issue_date->format('M d, Y') }}<br>
                                <strong>Status:</strong> 
                                <span class="badge bg-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr>
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <strong>Billed To:</strong><br>
                                {{ $invoice->customerProduct->customer->user->name ?? $invoice->customerProduct->customer->name ?? 'Customer' }}<br>
                                {{ $invoice->customerProduct->customer->phone ?? '' }}<br>
                                {{ $invoice->customerProduct->customer->address ?? '' }}
                            </td>
                            <td class="text-right">
                                <strong>Payable To:</strong><br>
                                NanoSoft<br>
                                billing@nanosoft.com.bd
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="heading">
                <td>Item</td>
                <td class="text-right">Price</td>
            </tr>
            
            <tr class="item">
                <td>
                    {{ $invoice->customerProduct->product->name }}
                    @if($invoice->customerProduct->billing_cycle_months)
                        <br><small>({{ $invoice->customerProduct->billing_cycle_months }} Month Subscription)</small>
                    @endif
                </td>
                <td class="text-right">
                    ৳{{ number_format($invoice->subtotal, 2) }}
                </td>
            </tr>

            @if($invoice->previous_due > 0)
            <tr class="item">
                <td>Previous Due</td>
                <td class="text-right">৳{{ number_format($invoice->previous_due, 2) }}</td>
            </tr>
            @endif
            
            <tr class="total">
                <td></td>
                <td class="text-right">
                    Total: ৳{{ number_format($invoice->total_amount, 2) }}
                </td>
            </tr>

            <tr class="total">
                <td></td>
                <td class="text-right">
                    Paid: ৳{{ number_format($invoice->received_amount, 2) }}
                </td>
            </tr>

            <tr class="total">
                <td></td>
                <td class="text-right">
                    <strong>Due: ৳{{ number_format($invoice->total_amount - $invoice->received_amount, 2) }}</strong>
                </td>
            </tr>
        </table>

        <div style="margin-top: 40px; font-size: 12px; color: #999; text-align: center;">
            <p>Thank you for your business!</p>
            <p>This is a computer-generated invoice and does not require a signature.</p>
        </div>
    </div>
</body>
</html>
