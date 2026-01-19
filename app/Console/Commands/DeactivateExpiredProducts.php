<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomerProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DeactivateExpiredProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:deactivate-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically deactivate products 7 days after their due date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for products that need to be deactivated (7 days after due date)...');

        // Get all active customer products
        $customerProducts = CustomerProduct::where('status', 'active')
            ->where('is_active', 1)
            ->get();

        $deactivatedCount = 0;

        foreach ($customerProducts as $customerProduct) {
            // Use the new checkAndDeactivateIfExpired method
            if ($customerProduct->checkAndDeactivateIfExpired()) {
                $this->info("Deactivated product: {$customerProduct->product->name} for customer {$customerProduct->customer->name} (expired on {$customerProduct->expire_date})");
                $deactivatedCount++;
            }
        }

        $this->info("Deactivation complete! {$deactivatedCount} products were deactivated.");
        
        return 0;
    }
}