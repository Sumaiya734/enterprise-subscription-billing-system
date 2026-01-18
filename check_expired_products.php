<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CustomerProduct;

echo "Checking active products and their expiration status:\n\n";

$activeProducts = CustomerProduct::where('status', 'active')
    ->where('is_active', 1)
    ->with('product')
    ->get();

foreach ($activeProducts as $cp) {
    $productName = $cp->product ? $cp->product->name : 'Unknown';
    $dueDate = $cp->due_date ? $cp->due_date : 'N/A';
    $customDueDate = $cp->custom_due_date ? $cp->custom_due_date : 'N/A';
    $isExpired = $cp->is_expired ? 'YES' : 'NO';
    
    echo "CP ID: {$cp->cp_id}\n";
    echo "Product: {$productName}\n";
    echo "Due Date: {$dueDate}\n";
    echo "Custom Due Date: {$customDueDate}\n";
    echo "Is Expired: {$isExpired}\n";
    echo "---\n";
}

echo "\nRunning expiration check command...\n";
exec('php artisan products:deactivate-expired', $output);
foreach ($output as $line) {
    echo $line . "\n";
}