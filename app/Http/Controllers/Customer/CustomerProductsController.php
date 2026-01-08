<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerProduct;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerProductsController extends Controller
{
    public function index()
    {
        // Get authenticated customer
        $customer = Customer::where('user_id', Auth::id())->firstOrFail();
        
        // Get all customer products with product details
        $customerProducts = CustomerProduct::where('c_id', $customer->c_id)
            ->with(['product' => function($query) {
                $query->select('p_id', 'name', 'description', 'monthly_price');
            }])
            ->where('is_active', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Calculate stats
        $activeCount = CustomerProduct::where('c_id', $customer->c_id)
            ->where('is_active', 1)
            ->where('status', 'active')
            ->count();
        
        $totalMonthly = CustomerProduct::where('c_id', $customer->c_id)
            ->where('is_active', 1)
            ->with('product')
            ->get()
            ->sum(function($cp) {
                return $cp->product->monthly_price ?? 0;
            });
        
        return view('customer.products.index', compact('customer', 'customerProducts', 'activeCount', 'totalMonthly'));
    }

    public function show($id)
    {
        $customer = Customer::where('user_id', Auth::id())->firstOrFail();
        
        $customerProduct = CustomerProduct::where('cp_id', $id)
            ->where('c_id', $customer->c_id)
            ->with(['product', 'invoices' => function($query) {
                $query->orderBy('issue_date', 'desc');
            }])
            ->firstOrFail();
        
        return view('customer.products.show', compact('customer', 'customerProduct'));
    }

    /**
     * Browse available products for purchase
     */
    public function browse()
    {
        $customer = Customer::where('user_id', Auth::id())->firstOrFail();
        
        // Get all available products
        $products = Product::with('type')
            ->orderBy('name')
            ->get();
        
        // Get customer's current products to show which ones they already have
        $customerProductIds = CustomerProduct::where('c_id', $customer->c_id)
            ->where('is_active', 1)
            ->pluck('p_id')
            ->toArray();
        
        return view('customer.products.browse', compact('customer', 'products', 'customerProductIds'));
    }

    /**
     * Show purchase form for a specific product
     */
    public function purchase($productId)
    {
        $customer = Customer::where('user_id', Auth::id())->firstOrFail();
        
        $product = Product::with('type')->findOrFail($productId);
        
        // Check if customer already has this product
        $existingProduct = CustomerProduct::where('c_id', $customer->c_id)
            ->where('p_id', $productId)
            ->where('is_active', 1)
            ->first();
        
        if ($existingProduct) {
            return redirect()->route('customer.products.browse')
                ->with('error', 'You already have this product subscribed.');
        }
        
        return view('customer.products.purchase', compact('customer', 'product'));
    }

    /**
     * Process the product purchase - Netflix-like subscription system with real payments
     */
    public function storePurchase(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,p_id',
            'payment_method' => 'required|in:bkash,nagad,rocket,card,bank_transfer,cash',
            'customer_phone' => 'required_if:payment_method,bkash,nagad,rocket|string|max:15',
            'notes' => 'nullable|string|max:500',
            'billing_cycle' => 'required|in:1,3,6,12'
        ]);

        try {
            $customer = Customer::where('user_id', Auth::id())->firstOrFail();
            $product = Product::findOrFail($request->product_id);

            // Check if customer already has this product
            $existingProduct = CustomerProduct::where('c_id', $customer->c_id)
                ->where('p_id', $request->product_id)
                ->where('is_active', 1)
                ->first();

            if ($existingProduct) {
                return redirect()->route('customer.products.browse')
                    ->with('error', 'You already have an active subscription for this product.');
            }

            DB::beginTransaction();
            
            // Calculate subscription details
            $monthlyPrice = $product->monthly_price;
            $billingCycle = (int) $request->billing_cycle;
            $subscriptionAmount = $monthlyPrice * $billingCycle;
            
            // Create customer product subscription
            $customerProduct = CustomerProduct::create([
                'c_id' => $customer->c_id,
                'p_id' => $request->product_id,
                'assign_date' => Carbon::now()->toDateString(),
                'billing_cycle_months' => $billingCycle,
                'status' => 'pending', // Pending until payment confirmed
                'is_active' => false, // Will be activated after payment
                'custom_price' => $subscriptionAmount,
                'is_custom_price' => false
            ]);

            // Generate unique invoice number
            $invoiceNumber = 'SUB-' . date('Y') . '-' . str_pad(
                Invoice::whereYear('created_at', date('Y'))->count() + 1, 
                6, 
                '0', 
                STR_PAD_LEFT
            );

            // Create subscription invoice
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'cp_id' => $customerProduct->cp_id,
                'issue_date' => Carbon::now()->toDateString(),
                'previous_due' => 0,
                'subtotal' => $subscriptionAmount,
                'total_amount' => $subscriptionAmount,
                'received_amount' => 0,
                'next_due' => $subscriptionAmount,
                'status' => 'unpaid',
                'created_by' => null,
                'notes' => 'Self-service subscription purchase'
            ]);

            // Process payment through gateway
            $paymentGateway = new \App\Services\PaymentGatewayService();
            
            $paymentData = [
                'amount' => $subscriptionAmount,
                'payment_method' => $request->payment_method,
                'invoice_number' => $invoiceNumber,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $request->customer_phone ?? $customer->phone,
                'customer_address' => $customer->address,
                'product_name' => $product->name
            ];

            $paymentResult = $paymentGateway->processPayment($paymentData);

            if ($paymentResult['success']) {
                // Create payment record
                $payment = Payment::create([
                    'invoice_id' => $invoice->invoice_id,
                    'c_id' => $customer->c_id,
                    'amount' => $subscriptionAmount,
                    'payment_method' => $request->payment_method,
                    'payment_date' => Carbon::now()->toDateString(),
                    'status' => 'pending', // Will be updated after gateway confirmation
                    'notes' => $request->notes ?? 'Self-service subscription payment'
                ]);

                DB::commit();

                // Handle different payment methods
                if (isset($paymentResult['redirect_url'])) {
                    // For bKash, Nagad, SSLCommerz - redirect to gateway
                    return redirect($paymentResult['redirect_url']);
                } else {
                    // For manual methods (Rocket, Bank Transfer, Cash)
                    return redirect()->route('customer.payments.instructions', $payment->payment_id)
                        ->with('payment_result', $paymentResult);
                }
            } else {
                DB::rollback();
                return redirect()->back()
                    ->withInput()
                    ->with('error', $paymentResult['error'] ?? 'Payment processing failed');
            }

        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Subscription purchase failed', [
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Unable to process your subscription. Please try again or contact support if the issue persists.');
        }
    }
}