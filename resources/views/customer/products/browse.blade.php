@extends('layouts.customer')

@section('title', 'Browse Subscriptions - Nanosoft')

@section('content')
    <div class="browse-subscriptions-page">

        <!-- Elegant Header -->
        <div class="card gradient-card welcome-banner mb-4 border-0">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center">
                            <div class="avatar-icon me-3">
                                <i class="fas fa-gem fa-3x text-white opacity-90"></i>
                            </div>
                            <div>
                                <h1 class="h3 mb-1 text-white fw-semibold">
                                    <i class="fas fa-gem me-2"></i>Subscription Plans
                                </h1>
                                <p class="mb-0 text-white opacity-90">
                                    Choose the perfect plan for your needs. No commitment required.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('customer.products.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> My Subscriptions
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid px-4">
            <!-- Plans Comparison -->
            <div class="plans-comparison mb-4">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="comparison-card bg-white rounded-3 shadow-sm p-3">
                            <div class="row g-2 text-center">
                                <div class="col-md-4">
                                    <i class="fas fa-sync-alt text-primary small mb-1 d-block"></i>
                                    <span class="text-muted small">Flexible</span>
                                </div>
                                <div class="col-md-4 border-start border-end">
                                    <i class="fas fa-shield-alt text-success small mb-1 d-block"></i>
                                    <span class="text-muted small">Secure</span>
                                </div>
                                <div class="col-md-4">
                                    <i class="fas fa-headset text-info small mb-1 d-block"></i>
                                    <span class="text-muted small">24/7 Support</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscription Plans Grid - Compact -->
            <div class="row g-3 justify-content-center">
                @forelse($products as $product)
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                        <div class="subscription-card {{ in_array($product->p_id, $customerProductIds) ? 'subscribed' : '' }}">
                            <div class="card border-0 h-100 shadow-sm hover-lift-sm">
                                <!-- Plan Badge -->
                                @if($product->product_type === 'special')
                                <div class="premium-badge">
                                    <i class="fas fa-crown me-1 small"></i> Premium
                                </div>
                                @endif
                                
                                <!-- Plan Header -->
                                <div class="card-header bg-white text-center py-3 px-3 border-bottom">
                                    <div class="plan-icon-wrapper mb-2">
                                        <div class="plan-icon-circle-sm {{ $product->product_type === 'special' ? 'premium-gradient' : 'primary-gradient' }}">
                                            <i class="fas fa-{{ $product->product_type === 'regular' ? 'layer-group' : 'stars' }}"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-semibold mb-1 text-dark">{{ $product->name }}</h6>
                                    <span class="badge bg-soft-{{ $product->product_type === 'special' ? 'warning text-warning' : 'primary text-primary' }} px-2 py-1 small">
                                        {{ ucfirst($product->product_type) }}
                                    </span>
                                </div>

                                <!-- Pricing -->
                                <div class="card-body px-3 py-3">
                                    <div class="pricing-section mb-3 text-center">
                                        <div class="price-display-sm mb-2">
                                            <span class="currency">৳</span>
                                            <span class="amount">{{ number_format($product->monthly_price, 0) }}</span>
                                            <span class="period">/month</span>
                                        </div>
                                        <p class="text-muted small mb-3">{{ Str::limit($product->description, 80) }}</p>
                                    </div>

                                    <!-- Features - Compact -->
                                    <div class="features-list-compact mb-3">
                                        <div class="feature-item-compact d-flex align-items-center mb-2">
                                            <i class="fas fa-check-circle text-success me-2 small"></i>
                                            <span class="small">Full Access</span>
                                        </div>
                                        <div class="feature-item-compact d-flex align-items-center mb-2">
                                            <i class="fas fa-headset text-info me-2 small"></i>
                                            <span class="small">24/7 Support</span>
                                        </div>
                                        @if($product->product_type === 'special')
                                            <div class="feature-item-compact d-flex align-items-center mb-2">
                                                <i class="fas fa-bolt text-warning me-2 small"></i>
                                                <span class="small">Premium Features</span>
                                            </div>
                                            <div class="feature-item-compact d-flex align-items-center">
                                                <i class="fas fa-shield-check text-success me-2 small"></i>
                                                <span class="small">Enhanced Security</span>
                                            </div>
                                        @else
                                            <div class="feature-item-compact d-flex align-items-center">
                                                <i class="fas fa-sync-alt text-primary me-2 small"></i>
                                                <span class="small">Flexible Billing</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Action Button -->
                                    <div class="action-section pt-2">
                                        @if(in_array($product->p_id, $customerProductIds))
                                            <div class="current-plan-indicator-sm text-center p-2 rounded-2 bg-soft-success">
                                                <i class="fas fa-check-circle text-success me-1 small"></i>
                                                <span class="small fw-medium">Active</span>
                                            </div>
                                        @else
                                            <a href="{{ route('customer.products.purchase', $product->p_id) }}" 
                                               class="btn btn-gradient-primary btn-sm w-100 subscribe-btn py-2">
                                                <i class="fas fa-play-circle me-1"></i> Subscribe
                                            </a>
                                            <div class="text-center mt-2">
                                                <small class="text-muted-soft">
                                                    <i class="fas fa-clock small me-1"></i> Cancel anytime
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-4 my-4">
                            <div class="empty-state">
                                <div class="empty-state-icon mb-3">
                                    <i class="fas fa-box-open fa-3x text-muted-soft"></i>
                                </div>
                                <h5 class="text-muted mb-2">No Plans Available</h5>
                                <p class="text-muted-soft small mb-3">New plans coming soon.</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- FAQ Section -->
            @if($products->count() > 0)
                <div class="faq-section mt-4 pt-4">
                    <div class="row">
                        <div class="col-lg-8 mx-auto">
                            <h6 class="fw-semibold text-center mb-3">Frequently Asked Questions</h6>
                            
                            <div class="accordion accordion-flush" id="faqAccordion">
                                <div class="accordion-item border-0 mb-2">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed rounded-2 bg-soft-light p-3 small" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                            <i class="fas fa-question-circle text-primary me-2 small"></i>
                                            Can I cancel anytime?
                                        </button>
                                    </h2>
                                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body ps-4 py-3 small">
                                            <i class="fas fa-check-circle text-success me-2 small"></i>
                                            Yes! Cancel anytime, service continues until billing period ends.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="accordion-item border-0 mb-2">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed rounded-2 bg-soft-light p-3 small" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                            <i class="fas fa-credit-card text-primary me-2 small"></i>
                                            Payment methods?
                                        </button>
                                    </h2>
                                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body ps-4 py-3 small">
                                            <div class="d-flex flex-wrap gap-1">
                                                <span class="badge bg-soft-primary small">Cards</span>
                                                <span class="badge bg-soft-success small">Bank Transfer</span>
                                                <span class="badge bg-soft-info small">Mobile Banking</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="accordion-item border-0 mb-2">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed rounded-2 bg-soft-light p-3 small" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                            <i class="fas fa-exchange-alt text-primary me-2 small"></i>
                                            Change plans later?
                                        </button>
                                    </h2>
                                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body ps-4 py-3 small">
                                            <i class="fas fa-arrow-up text-success me-2 small"></i>
                                            Upgrade anytime. Downgrades apply next cycle.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .browse-subscriptions-page {
            background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
            min-height: 100vh;
            padding-bottom: 3rem;
            animation: fadeIn 0.6s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        

        .gradient-card.welcome-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            margin-bottom: 2rem;
        }
        
        .gradient-card.welcome-banner:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.3;
        }
        
        .subscription-card {
            transition: all 0.2s ease;
        }
        
        .subscription-card:hover:not(.subscribed) {
            transform: translateY(-4px);
        }
        
        .hover-lift-sm {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
        }
        
        .hover-lift-sm:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.08) !important;
        }
        
        .premium-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: linear-gradient(45deg, #FFD700, #FFA500);
            color: #856404;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            z-index: 1;
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.2);
        }
        
        .plan-icon-wrapper {
            display: flex;
            justify-content: center;
        }
        
        .plan-icon-circle-sm {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }
        
        .primary-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .premium-gradient {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        }
        
        .price-display-sm {
            margin: 0.5rem 0;
        }
        
        .price-display-sm .currency {
            font-size: 1rem;
            vertical-align: top;
            color: #6c757d;
            font-weight: 500;
        }
        
        .price-display-sm .amount {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2d3748;
            line-height: 1;
            margin: 0 2px;
        }
        
        .price-display-sm .period {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        .features-list-compact {
            background: #f8fafc;
            border-radius: 8px;
            padding: 0.75rem;
        }
        
        .feature-item-compact {
            font-size: 0.85rem;
        }
        
        .current-plan-indicator-sm {
            border: 1px solid rgba(40, 167, 69, 0.2);
            font-size: 0.85rem;
        }
        
        .text-muted-soft {
            color: #94a3b8 !important;
        }
        
        .bg-soft-light {
            background-color: #f8fafc !important;
        }
        
        .avatar-icon {
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .bg-soft-primary {
            background-color: rgba(102, 126, 234, 0.1) !important;
        }
        
        .bg-soft-success {
            background-color: rgba(40, 167, 69, 0.1) !important;
        }
        
        .bg-soft-warning {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }
        
        .bg-soft-info {
            background-color: rgba(23, 162, 184, 0.1) !important;
        }
        
        .comparison-card {
            border: 1px solid rgba(0,0,0,0.03);
        }
        
        .btn-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        
        .btn-gradient-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .btn-outline-light {
            border-color: rgba(255,255,255,0.3);
            color: white;
        }
        
        .btn-outline-light:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-color: rgba(255,255,255,0.4);
        }
        
        .faq-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
            border: 1px solid rgba(0,0,0,0.03);
        }
        
        .accordion-button {
            font-size: 0.875rem;
            padding: 0.75rem 1rem;
        }
        
        .accordion-button:not(.collapsed) {
            background-color: rgba(102, 126, 234, 0.05) !important;
            color: #667eea !important;
            border-color: rgba(102, 126, 234, 0.1) !important;
        }
        
        .accordion-body {
            font-size: 0.85rem;
        }
        
        .subscription-card.subscribed {
            position: relative;
            opacity: 0.9;
        }
        
        .subscription-card.subscribed:after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.6);
            border-radius: inherit;
            pointer-events: none;
        }
        
        /* Card size constraints */
        .card {
            border-radius: 12px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        }
        
        .card-header {
            padding: 1rem 0.75rem;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        /* Grid spacing for small cards */
        .row.g-3 > [class*='col-'] {
            padding: 0.375rem;
        }
        
        .empty-state-icon {
            opacity: 0.4;
        }
        
        @media (max-width: 768px) {
            .hero-header {
                padding: 1.5rem 0;
            }
            
            .row.g-3 > [class*='col-'] {
                padding: 0.25rem;
            }
            
            .faq-section {
                padding: 1rem;
            }
            
            .col-xl-3 {
                max-width: 50%;
            }
            
            .plan-icon-circle-sm {
                width: 45px;
                height: 45px;
                font-size: 1rem;
            }
            
            .price-display-sm .amount {
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .col-xl-3,
            .col-lg-4,
            .col-md-6,
            .col-sm-6 {
                max-width: 100%;
            }
            
            .row.g-3 > [class*='col-'] {
                padding: 0.5rem;
            }
        }
    </style>
@endsection