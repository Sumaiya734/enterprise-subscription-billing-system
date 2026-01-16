@extends('layouts.customer')

@section('title', 'Complete Subscription - Nanosoft')

@section('content')
    <div class="subscription-checkout-page">
        <!-- Progress Header -->
        <div class="checkout-header mb-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-2">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('customer.products.browse') }}" class="text-decoration-none">
                                        <i class="fas fa-th-large me-1"></i>Plans
                                    </a>
                                </li>
                                <li class="breadcrumb-item active">Checkout</li>
                            </ol>
                        </nav>
                        <h1 class="h2 mb-2">
                            <i class="fas fa-credit-card me-2 text-primary"></i>Complete Your Subscription
                        </h1>
                        <p class="text-muted mb-0">You're just one step away from accessing {{ $product->name }}!</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="security-badge">
                            <i class="fas fa-shield-alt text-success me-2"></i>
                            <span class="text-muted">Secure Checkout</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <!-- Subscription Form -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-4">
                            <h5 class="mb-0">
                                <i class="fas fa-user-circle me-2 text-secondary"></i>Subscription Details
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <!-- Selected Plan Display -->
                            <div class="selected-plan-display mb-4 p-4 bg-light rounded">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-center">
                                            <div class="plan-icon me-3">
                                                <i class="fas fa-{{ $product->product_type === 'regular' ? 'star' : 'crown' }} text-primary fa-2x"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-1">{{ $product->name }}</h5>
                                                <p class="text-muted mb-0">{{ $product->description }}</p>
                                                <span class="badge bg-primary">{{ ucfirst($product->product_type) }} Plan</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <div class="plan-price">
                                            <h4 class="text-primary mb-0">৳{{ number_format($product->monthly_price, 0) }}/mo</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Subscription Form -->
                            <form method="POST" action="{{ route('customer.products.store-purchase') }}" id="subscriptionForm">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->p_id }}">

                                <!-- Billing Cycle Selection -->
                                <div class="billing-cycle-section mb-4">
                                    <h6 class="mb-3">
                                        <i class="fas fa-calendar-alt me-2"></i>Choose Your Billing Cycle
                                    </h6>
                                    @php
                                        $discountRates = [
                                            3 => 0.95,
                                            6 => 0.90,
                                            12 => 0.85,
                                        ];
                                    @endphp
                                    <div class="row g-3">
                                        <div class="col-md-6 col-lg-3">
                                            <div class="billing-option">
                                                <input type="radio" class="btn-check" name="billing_cycle" id="cycle_1" value="1" {{ old('billing_cycle') == '1' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-primary w-100 p-3" for="cycle_1">
                                                    <div class="text-center">
                                                        <strong>Monthly</strong>
                                                        <div class="small text-muted">1 Month</div>
                                                        <div class="fw-bold text-primary">৳{{ number_format($product->monthly_price, 0) }}</div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="billing-option">
                                                <input type="radio" class="btn-check" name="billing_cycle" id="cycle_3" value="3" {{ old('billing_cycle') == '3' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-primary w-100 p-3" for="cycle_3">
                                                    <div class="text-center">
                                                        <strong>Quarterly</strong>
                                                        <div class="small text-muted">3 Months</div>
                                                        <div class="fw-bold text-primary">৳{{ number_format($product->monthly_price * 3 * $discountRates[3], 0) }}</div>
                                                        <div class="badge bg-success small">Save 5%</div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="billing-option">
                                                <input type="radio" class="btn-check" name="billing_cycle" id="cycle_6" value="6" {{ old('billing_cycle') == '6' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-primary w-100 p-3" for="cycle_6">
                                                    <div class="text-center">
                                                        <strong>Half-Yearly</strong>
                                                        <div class="small text-muted">6 Months</div>
                                                        <div class="fw-bold text-primary">৳{{ number_format($product->monthly_price * 6 * $discountRates[6], 0) }}</div>
                                                        <div class="badge bg-success small">Save 10%</div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="billing-option">
                                                <input type="radio" class="btn-check" name="billing_cycle" id="cycle_12" value="12" {{ old('billing_cycle') == '12' ? 'selected' : '' }}>
                                                <label class="btn btn-outline-primary w-100 p-3" for="cycle_12">
                                                    <div class="text-center">
                                                        <strong>Yearly</strong>
                                                        <div class="small text-muted">12 Months</div>
                                                        <div class="fw-bold text-primary">৳{{ number_format($product->monthly_price * 12 * $discountRates[12], 0) }}</div>
                                                        <div class="badge bg-success small">Save 15%</div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('billing_cycle')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Payment Method -->
                                <div class="payment-section mb-4">
                                    <h6 class="mb-3">
                                        <i class="fas fa-credit-card me-2"></i>Choose Payment Method
                                    </h6>
                                    
                                    <!-- Payment Method Cards -->
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="payment-method-card">
                                                <input type="radio" class="btn-check" name="payment_method" id="bkash" value="bkash" {{ old('payment_method') == 'bkash' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-primary w-100 p-3" for="bkash">
                                                    <div class="d-flex align-items-center">
                                                        <div class="payment-icon me-3">
                                                            <img src="https://cdn.bka.sh/images/bkash_logo.png" alt="bKash" style="height: 30px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                                            <span style="display: none;">📱</span>
                                                        </div>
                                                        <div>
                                                            <strong>bKash</strong>
                                                            <div class="small text-muted">Mobile Banking</div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="payment-method-card">
                                                <input type="radio" class="btn-check" name="payment_method" id="nagad" value="nagad" {{ old('payment_method') == 'nagad' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-success w-100 p-3" for="nagad">
                                                    <div class="d-flex align-items-center">
                                                        <div class="payment-icon me-3">
                                                            <span style="font-size: 24px;">💳</span>
                                                        </div>
                                                        <div>
                                                            <strong>Nagad</strong>
                                                            <div class="small text-muted">Mobile Banking</div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="payment-method-card">
                                                <input type="radio" class="btn-check" name="payment_method" id="rocket" value="rocket" {{ old('payment_method') == 'rocket' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-warning w-100 p-3" for="rocket">
                                                    <div class="d-flex align-items-center">
                                                        <div class="payment-icon me-3">
                                                            <span style="font-size: 24px;">🚀</span>
                                                        </div>
                                                        <div>
                                                            <strong>Rocket</strong>
                                                            <div class="small text-muted">DBBL Mobile Banking</div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="payment-method-card">
                                                <input type="radio" class="btn-check" name="payment_method" id="card" value="card" {{ old('payment_method') == 'card' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-info w-100 p-3" for="card">
                                                    <div class="d-flex align-items-center">
                                                        <div class="payment-icon me-3">
                                                            <span style="font-size: 24px;">💳</span>
                                                        </div>
                                                        <div>
                                                            <strong>Card Payment</strong>
                                                            <div class="small text-muted">Visa, MasterCard</div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="payment-method-card">
                                                <input type="radio" class="btn-check" name="payment_method" id="bank_transfer" value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-secondary w-100 p-3" for="bank_transfer">
                                                    <div class="d-flex align-items-center">
                                                        <div class="payment-icon me-3">
                                                            <span style="font-size: 24px;">🏦</span>
                                                        </div>
                                                        <div>
                                                            <strong>Bank Transfer</strong>
                                                            <div class="small text-muted">Direct Transfer</div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="payment-method-card">
                                                <input type="radio" class="btn-check" name="payment_method" id="cash" value="cash" {{ old('payment_method') == 'cash' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-dark w-100 p-3" for="cash">
                                                    <div class="d-flex align-items-center">
                                                        <div class="payment-icon me-3">
                                                            <span style="font-size: 24px;">💵</span>
                                                        </div>
                                                        <div>
                                                            <strong>Cash Payment</strong>
                                                            <div class="small text-muted">Pay at Office</div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('payment_method')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Phone Number (for mobile banking) -->
                                <div class="phone-section mb-4" id="phoneSection" style="display: none;">
                                    <label for="customer_phone" class="form-label">
                                        <i class="fas fa-phone me-2"></i>Mobile Number *
                                    </label>
                                    <input type="text" class="form-control form-control-lg @error('customer_phone') is-invalid @enderror"
                                           name="customer_phone" value="{{ old('customer_phone', $customer->phone ?? '') }}"
                                           placeholder="01XXXXXXXXX" maxlength="15">
                                    <small class="text-muted">Required for bKash, Nagad, and Rocket payments</small>
                                    @error('customer_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Additional Notes -->
                                <div class="notes-section mb-4">
                                    <label for="notes" class="form-label">
                                        <i class="fas fa-sticky-note me-2"></i>Additional Notes (Optional)
                                    </label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              name="notes" rows="3" 
                                              placeholder="Any special instructions or notes...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Terms and Conditions -->
                                <div class="terms-section mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="terms" required>
                                        <label class="form-check-label" for="terms">
                                            I agree to the <a href="#" class="text-primary">Terms of Service</a> and 
                                            <a href="#" class="text-primary">Privacy Policy</a>
                                        </label>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="action-buttons d-flex justify-content-between align-items-center">
                                    <a href="{{ route('customer.products.browse') }}" class="btn btn-outline-secondary btn-lg">
                                        <i class="fas fa-arrow-left me-2"></i> Back to Plans
                                    </a>
                                    <button type="submit" class="btn btn-success btn-lg px-5" id="subscribeBtn">
                                        <i class="fas fa-play me-2"></i> Start My Subscription
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                        <div class="card-header bg-success text-white border-0 py-4">
                            <h5 class="mb-0 text-center">
                                <i class="fas fa-receipt me-2"></i>Subscription Summary
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <!-- Plan Details -->
                            <div class="summary-item d-flex justify-content-between mb-3">
                                <span>Plan:</span>
                                <strong>{{ $product->name }}</strong>
                            </div>

                            <div class="summary-item d-flex justify-content-between mb-3">
                                <span>Monthly Rate:</span>
                                <strong>৳{{ number_format($product->monthly_price, 0) }}</strong>
                            </div>

                            <div class="summary-item d-flex justify-content-between mb-3">
                                <span>Billing Cycle:</span>
                                <strong id="cycle_display">Select cycle</strong>
                            </div>

                            <div class="summary-item d-flex justify-content-between mb-3">
                                <span>Discount:</span>
                                <strong class="text-success" id="discount_display">৳0</strong>
                            </div>

                            <hr>

                            <div class="summary-total d-flex justify-content-between mb-4">
                                <span class="h5 mb-0">Total Today:</span>
                                <strong class="h4 mb-0 text-success" id="summary_total">৳{{ number_format($product->monthly_price, 0) }}</strong>
                            </div>

                            <!-- Benefits -->
                            <div class="benefits-section">
                                <h6 class="mb-3">What's Included:</h6>
                                <div class="benefit-item mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <span>Instant activation</span>
                                </div>
                                <div class="benefit-item mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <span>Cancel anytime</span>
                                </div>
                                <div class="benefit-item mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <span>24/7 support</span>
                                </div>
                                <div class="benefit-item mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <span>No setup fees</span>
                                </div>
                            </div>

                            <!-- Next Billing Info -->
                            <div class="next-billing mt-4 p-3 bg-light rounded">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <span id="billing_note">Select a billing cycle to see next billing date.</span>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .subscription-checkout-page {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding-bottom: 3rem;
        }
        
        .checkout-header {
            background: rgba(255,255,255,0.9);
            padding: 2rem 0;
            margin: -2rem -15px 0 -15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .selected-plan-display {
            border: 2px solid #28a745;
            background: linear-gradient(45deg, #f8fff9, #e8f5e8);
        }
        
        .billing-option .btn-check:checked + .btn {
            background: linear-gradient(45deg, #28a745, #20c997);
            border-color: #28a745;
            color: white;
        }
        
        .billing-option .btn {
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .billing-option .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .security-badge {
            background: rgba(40, 167, 69, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            border: 1px solid rgba(40, 167, 69, 0.2);
        }
        
        #subscribeBtn {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            transition: all 0.3s ease;
        }
        
        #subscribeBtn:hover {
            background: linear-gradient(45deg, #218838, #1ea080);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }
        
        .benefit-item {
            padding: 0.25rem 0;
        }
        
        .summary-item {
            padding: 0.5rem 0;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .summary-item:last-child {
            border-bottom: none;
        }
        
        .payment-method-card .btn-check:checked + .btn {
            background: linear-gradient(45deg, var(--bs-primary), var(--bs-success));
            border-color: var(--bs-primary);
            color: white;
            transform: scale(1.02);
        }
        
        .payment-method-card .btn {
            transition: all 0.3s ease;
            height: 100%;
            border: 2px solid #e9ecef;
        }
        
        .payment-method-card .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .payment-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        #phoneSection {
            background: rgba(13, 110, 253, 0.05);
            border: 1px solid rgba(13, 110, 253, 0.2);
            border-radius: 8px;
            padding: 1rem;
        }
        
        @media (max-width: 768px) {
            .checkout-header {
                padding: 1rem 0;
            }
            
            .billing-option {
                margin-bottom: 1rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const billingCycleInputs = document.querySelectorAll('input[name="billing_cycle"]');
            const paymentMethodInputs = document.querySelectorAll('input[name="payment_method"]');
            const phoneSection = document.getElementById('phoneSection');
            const summaryTotal = document.getElementById('summary_total');
            const cycleDisplay = document.getElementById('cycle_display');
            const discountDisplay = document.getElementById('discount_display');
            const billingNote = document.getElementById('billing_note');
            const monthlyPrice = {{ $product->monthly_price }};

            function updatePricing() {
                const selectedCycle = document.querySelector('input[name="billing_cycle"]:checked');
                
                if (selectedCycle) {
                    const cycle = parseInt(selectedCycle.value);
                    const totalAmount = monthlyPrice * cycle;
                    const discountRates = {
                        1: 0,
                        3: 5,
                        6: 10,
                        12: 15
                    };
                    const rate = discountRates[cycle] || 0;
                    const discount = rate > 0 ? Math.round(totalAmount * rate / 100) : 0;
                    const finalAmount = totalAmount - discount;

                    summaryTotal.textContent = '৳' + finalAmount.toLocaleString('en-BD');
                    discountDisplay.textContent = discount > 0 ? '-৳' + discount.toLocaleString('en-BD') : '৳0';

                    // Update cycle display
                    const cycleTexts = {
                        1: '1 Month',
                        3: '3 Months',
                        6: '6 Months',
                        12: '12 Months'
                    };
                    cycleDisplay.textContent = cycleTexts[cycle];

                    // Update billing note
                    const nextBillingDate = new Date();
                    nextBillingDate.setMonth(nextBillingDate.getMonth() + cycle);
                    const noteTexts = {
                        1: `Next billing: ${nextBillingDate.toLocaleDateString('en-GB')}`,
                        3: `Next billing: ${nextBillingDate.toLocaleDateString('en-GB')} (Quarterly)`,
                        6: `Next billing: ${nextBillingDate.toLocaleDateString('en-GB')} (Half-yearly)`,
                        12: `Next billing: ${nextBillingDate.toLocaleDateString('en-GB')} (Yearly)`
                    };
                    billingNote.textContent = noteTexts[cycle];
                } else {
                    summaryTotal.textContent = '৳' + monthlyPrice.toLocaleString('en-BD');
                    cycleDisplay.textContent = 'Select cycle';
                    discountDisplay.textContent = '৳0';
                    billingNote.textContent = 'Select a billing cycle to see next billing date.';
                }
            }

            function updatePaymentMethod() {
                const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
                
                if (selectedMethod) {
                    const method = selectedMethod.value;
                    const mobileBankingMethods = ['bkash', 'nagad', 'rocket'];
                    
                    if (mobileBankingMethods.includes(method)) {
                        phoneSection.style.display = 'block';
                        phoneSection.querySelector('input').required = true;
                    } else {
                        phoneSection.style.display = 'none';
                        phoneSection.querySelector('input').required = false;
                    }
                }
            }

            billingCycleInputs.forEach(input => {
                input.addEventListener('change', updatePricing);
            });

            paymentMethodInputs.forEach(input => {
                input.addEventListener('change', updatePaymentMethod);
            });
            
            // Initialize on page load
            updatePricing();
            updatePaymentMethod();
        });
    </script>
@endsection
