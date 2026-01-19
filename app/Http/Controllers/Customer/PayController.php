<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayController extends Controller
{
    /**
     * Display a listing of the customer's payments.
     */
    public function index(Request $request)
    {
        // Get authenticated customer
        $customer = Customer::where('user_id', Auth::id())->firstOrFail();
        
        // Get payments query
        $query = Payment::where('c_id', $customer->c_id)
            ->with(['invoice.customerProduct.product'])
            ->orderBy('payment_date', 'desc');
        
        // Apply filters if provided
        if ($request->has('method') && $request->method !== 'all') {
            $query->where('payment_method', $request->method);
        }
        
        if ($request->has('month') && $request->month !== 'all') {
            $query->whereMonth('payment_date', $request->month);
        }
        
        if ($request->has('year') && $request->year !== 'all') {
            $query->whereYear('payment_date', $request->year);
        }
        
        // Get paginated results
        $payments = $query->paginate(15);
        
        // Calculate totals
        $totalPayments = $payments->total();
        $totalAmount = $payments->sum('amount');
        
        // Get payment methods for stats
        $methodCounts = Payment::where('c_id', $customer->c_id)
            ->select('payment_method', DB::raw('count(*) as count'))
            ->groupBy('payment_method')
            ->pluck('count', 'payment_method')
            ->toArray();
        
        // Get months and years for filters
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        
        $years = Payment::where('c_id', $customer->c_id)
            ->select(DB::raw('YEAR(payment_date) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        
        // Get recent payments (last 6 months)
        $recentPayments = Payment::where('c_id', $customer->c_id)
            ->where('payment_date', '>=', now()->subMonths(6))
            ->orderBy('payment_date', 'desc')
            ->limit(6)
            ->get();
        
        return view('customer.payments.index', compact(
            'customer',
            'payments',
            'totalPayments',
            'totalAmount',
            'methodCounts',
            'months',
            'years',
            'recentPayments'
        ));
    }

    /**
     * Display the specified payment.
     */
    public function show($id)
    {
        $customer = Customer::where('user_id', Auth::id())->firstOrFail();
        
        $payment = Payment::where('payment_id', $id)
            ->where('c_id', $customer->c_id)
            ->with(['invoice.customerProduct.product', 'invoice.customerProduct.customer'])
            ->firstOrFail();
        
        return view('customer.payments.show', compact('customer', 'payment'));
    }

    /**
     * Show the form for making a new payment.
     */
    public function create($invoiceId = null)
    {
        $customer = Customer::where('user_id', Auth::id())->firstOrFail();
        
        $invoice = null;
        if ($invoiceId) {
            $invoice = \App\Models\Invoice::where('invoice_id', $invoiceId)
                ->whereHas('customerProduct', function($query) use ($customer) {
                    $query->where('c_id', $customer->c_id);
                })
                ->first();
        }
        
        // Get unpaid invoices for the dropdown
        $unpaidInvoices = \App\Models\Invoice::whereHas('customerProduct', function($query) use ($customer) {
                $query->where('c_id', $customer->c_id);
            })
            ->whereIn('status', ['unpaid', 'partial'])
            ->with(['customerProduct.product'])
            ->orderBy('issue_date', 'desc')
            ->get();
        
        return view('customer.payments.create', compact('customer', 'invoice', 'unpaidInvoices'));
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request)
    {
        $customer = Customer::where('user_id', Auth::id())->firstOrFail();
        
        $request->validate([
            'invoice_id' => 'required|exists:invoices,invoice_id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:bkash,nagad,rocket,card,bank,cash',
            'transaction_id' => 'nullable|string|max:100',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);
        
        // Verify invoice belongs to customer
        $invoice = \App\Models\Invoice::where('invoice_id', $request->invoice_id)
            ->whereHas('customerProduct', function($query) use ($customer) {
                $query->where('c_id', $customer->c_id);
            })
            ->firstOrFail();
        
        // Check if payment amount is valid
        $remaining = $invoice->total_amount - $invoice->received_amount;
        if ($request->amount > $remaining) {
            return back()->withErrors(['amount' => 'Payment amount cannot exceed the due amount (৳' . number_format($remaining, 2) . ')']);
        }
        
        // Determine status
        $status = $request->payment_method === 'cash' ? 'completed' : 'pending';

        // Create payment
        $payment = Payment::create([
            'c_id' => $customer->c_id,
            'invoice_id' => $request->invoice_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'payment_date' => $request->payment_date,
            'notes' => $request->notes,
            'status' => $status,
        ]);
        
        // Update invoice status ONLY if payment is completed (e.g. Cash)
        if ($status === 'completed') {
            $invoice->received_amount += $request->amount;
            
            if ($invoice->received_amount >= $invoice->total_amount) {
                $invoice->status = 'paid';
            } elseif ($invoice->received_amount > 0) {
                $invoice->status = 'partial';
            }
            
            $invoice->save();
        }
        
        return redirect()->route('customer.payments.index')
            ->with('success', 'Payment of ৳' . number_format($request->amount, 2) . ' has been recorded successfully!');
    }

    /**
     * Show payment instructions for manual payment methods
     */
    public function instructions($id)
    {
        $customer = Customer::where('user_id', Auth::id())->firstOrFail();
        
        $payment = Payment::where('payment_id', $id)
            ->where('c_id', $customer->c_id)
            ->with(['invoice.customerProduct.product'])
            ->firstOrFail();
        
        $paymentResult = session('payment_result');
        
        return view('customer.payments.instructions', compact('customer', 'payment', 'paymentResult'));
    }

    /**
     * Handle bKash payment callback
     */
    public function bkashCallback(Request $request)
    {
        $paymentID = $request->get('paymentID');
        $status = $request->get('status');
        
        if ($status === 'success' && $paymentID) {
            // Execute payment with bKash
            $paymentGateway = new \App\Services\PaymentGatewayService();
            $execution = $paymentGateway->executeBkashPayment($paymentID);
            
            if ($execution['success']) {
                // Update payment and invoice status
                $success = $this->updatePaymentStatus($paymentID, 'completed', $execution['transaction_id']);
                
                if ($success) {
                    return redirect()->route('customer.products.browse')
                        ->with('success', '🎉 Payment successful! Your subscription is now active.');
                }
            } else {
                 return redirect()->route('customer.products.browse')
                    ->with('error', 'Payment execution failed: ' . ($execution['error'] ?? 'Unknown error'));
            }
        }
        
        return redirect()->route('customer.products.browse')
            ->with('error', 'Payment failed or was cancelled. Please try again.');
    }

    /**
     * Handle SSLCommerz success callback
     */
    public function sslSuccess(Request $request)
    {
        $tranId = $request->get('tran_id');
        $status = $request->get('status');
        
        if ($status === 'VALID') {
            $this->updatePaymentStatus($tranId, 'completed', $request->get('bank_tran_id'));
            
            return redirect()->route('customer.products.index')
                ->with('success', '🎉 Payment successful! Your subscription is now active.');
        }
        
        return redirect()->route('customer.products.browse')
            ->with('error', 'Payment verification failed. Please contact support.');
    }

    /**
     * Handle SSLCommerz failure callback
     */
    public function sslFail(Request $request)
    {
        return redirect()->route('customer.products.browse')
            ->with('error', 'Payment failed. Please try again or use a different payment method.');
    }

    /**
     * Handle SSLCommerz cancel callback
     */
    public function sslCancel(Request $request)
    {
        return redirect()->route('customer.products.browse')
            ->with('info', 'Payment was cancelled. You can try again anytime.');
    }

    /**
     * Update payment status and activate subscription
     */
    private function updatePaymentStatus($paymentReference, $status, $transactionId = null)
    {
        // Find payment by reference (could be paymentID or invoice number)
        $payment = Payment::where('notes', 'like', '%' . $paymentReference . '%')
            ->orWhereHas('invoice', function($query) use ($paymentReference) {
                $query->where('invoice_number', $paymentReference);
            })
            ->first();
        
        if ($payment) {
            // Update payment status
            $payment->update([
                'status' => $status,
                'notes' => $payment->notes . ' | Transaction ID: ' . $transactionId
            ]);
            
            // Update invoice
            $invoice = $payment->invoice;
            $invoice->update([
                'received_amount' => $payment->amount,
                'next_due' => 0,
                'status' => 'paid'
            ]);
            
            // Activate subscription
            $customerProduct = $invoice->customerProduct;
            $customerProduct->update([
                'status' => 'active',
                'is_active' => true
            ]);
            
            return true;
        }
        
        return false;
    }
}