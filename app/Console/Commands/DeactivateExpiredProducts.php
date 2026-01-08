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
    protected $description = 'Automatically deactivate products when their billing cycle ends';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for products that need to be deactivated...');

        // Find all active products that have reached the end of their billing cycle
        $today = Carbon::today();
        
        // Get all active customer products
        $customerProducts = CustomerProduct::where('status', 'active')
            ->where('is_active', 1)
            ->get();

        $deactivatedCount = 0;

        foreach ($customerProducts as $customerProduct) {
            // Calculate when the next billing cycle would start
            $assignDate = Carbon::parse($customerProduct->assign_date);
            $billingCycleMonths = $customerProduct->billing_cycle_months ?? 1;
            
            // Calculate the number of months that have passed since assignment
            $monthsSinceAssignment = $assignDate->diffInMonths($today);
            
            // Calculate the current billing cycle number (0-indexed)
            $currentCycleNumber = floor($monthsSinceAssignment / $billingCycleMonths);
            
            // Calculate when the current billing cycle ends
            $currentCycleEndDate = $assignDate->copy()->addMonths(($currentCycleNumber + 1) * $billingCycleMonths);
            
            // If today is past the end of the current billing cycle, the product should be deactivated
            if ($today->greaterThanOrEqualTo($currentCycleEndDate)) {
                $customerProduct->deactivate();
                
                $this->info("Deactivated product: {$customerProduct->product->name} for customer {$customerProduct->customer->name}");
                Log::info("Product deactivated: Product ID {$customerProduct->cp_id}, Customer ID {$customerProduct->c_id}, Billing cycle ended on {$currentCycleEndDate->format('Y-m-d')}");
                
                $deactivatedCount++;
            }
        }

        $this->info("Deactivation complete! {$deactivatedCount} products were deactivated.");
        
        return 0;
    }
}