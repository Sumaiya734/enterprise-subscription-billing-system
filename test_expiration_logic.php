<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CustomerProduct;
use Carbon\Carbon;

echo "Testing Customer Product Expiration Logic\n";
echo "=========================================\n\n";

// Test a specific customer product that we know should be expired
$testCp = CustomerProduct::find(77);
if ($testCp) {
    echo "Testing CP ID: {$testCp->cp_id}\n";
    echo "Product Name: " . ($testCp->product ? $testCp->product->name : 'N/A') . "\n";
    echo "Status: {$testCp->status}\n";
    echo "Is Active: " . ($testCp->is_active ? 'Yes' : 'No') . "\n";
    echo "Due Date: " . ($testCp->due_date ? $testCp->due_date : 'N/A') . "\n";
    echo "Custom Due Date: " . ($testCp->custom_due_date ? $testCp->custom_due_date : 'N/A') . "\n";
    echo "Is Expired (Accessor): " . ($testCp->is_expired ? 'YES' : 'NO') . "\n";
    echo "Is Expired (Manual Check): " . (Carbon::parse($testCp->due_date)->isPast() ? 'YES' : 'NO') . "\n";
    
    // Test the checkAndDeactivateIfExpired method
    echo "\nTesting checkAndDeactivateIfExpired method:\n";
    $result = $testCp->checkAndDeactivateIfExpired();
    echo "Method returned: " . ($result ? 'TRUE (deactivated)' : 'FALSE (not deactivated)') . "\n";
    
    // Refresh the model to see current state
    $testCp->refresh();
    echo "After method call - Status: {$testCp->status}\n";
    echo "After method call - Is Active: " . ($testCp->is_active ? 'Yes' : 'No') . "\n";
} else {
    echo "Customer Product with ID 77 not found\n";
}

echo "\n" . str_repeat("-", 50) . "\n";
echo "Checking all active products:\n\n";

$activeProducts = CustomerProduct::where('status', 'active')
    ->where('is_active', 1)
    ->with('product')
    ->get();

if ($activeProducts->isEmpty()) {
    echo "No active products found!\n";
} else {
    foreach ($activeProducts as $cp) {
        $isExpired = $cp->is_expired;
        $productName = $cp->product ? $cp->product->name : 'Unknown';
        echo "CP ID: {$cp->cp_id} | Product: {$productName} | Status: {$cp->status} | Expired: " . ($isExpired ? 'YES' : 'NO') . "\n";
        if ($isExpired) {
            echo "  ^^^ This should be deactivated!\n";
        }
    }
}