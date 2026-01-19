<?php

use App\Models\Invoice;
use App\Models\Payment;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$invoice = Invoice::latest()->first();

if (!$invoice) {
    echo json_encode(['error' => 'No invoices found']);
    exit;
}

$payment = $invoice->payments->first();

$data = [
    'invoice_number' => $invoice->invoice_number,
    'invoice_status' => $invoice->status,
    'payment' => $payment ? [
        'id' => $payment->payment_id,
        'status' => $payment->status,
        'method' => $payment->payment_method,
        'notes' => $payment->notes
    ] : null
];

echo json_encode($data, JSON_PRETTY_PRINT);
