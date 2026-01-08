<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentGatewayService
{
    /**
     * Process payment through various gateways
     */
    public function processPayment($paymentData)
    {
        $method = $paymentData['payment_method'];
        
        switch ($method) {
            case 'bkash':
                return $this->processBkashPayment($paymentData);
            case 'nagad':
                return $this->processNagadPayment($paymentData);
            case 'rocket':
                return $this->processRocketPayment($paymentData);
            case 'card':
                return $this->processCardPayment($paymentData);
            case 'bank_transfer':
                return $this->processBankTransfer($paymentData);
            case 'cash':
                return $this->processCashPayment($paymentData);
            default:
                throw new Exception('Unsupported payment method');
        }
    }

    /**
     * Process bKash payment
     */
    private function processBkashPayment($paymentData)
    {
        try {
            // bKash API integration
            $bkashConfig = config('payment.bkash');
            
            // Step 1: Get bKash token
            $tokenResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'username' => $bkashConfig['username'],
                'password' => $bkashConfig['password']
            ])->post($bkashConfig['base_url'] . '/tokenized/checkout/token/grant', [
                'app_key' => $bkashConfig['app_key'],
                'app_secret' => $bkashConfig['app_secret']
            ]);

            if (!$tokenResponse->successful()) {
                throw new Exception('Failed to get bKash token');
            }

            $token = $tokenResponse->json()['id_token'];

            // Step 2: Create payment
            $paymentResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'authorization' => $token,
                'x-app-key' => $bkashConfig['app_key']
            ])->post($bkashConfig['base_url'] . '/tokenized/checkout/create', [
                'mode' => '0011',
                'payerReference' => $paymentData['customer_phone'],
                'callbackURL' => route('customer.payments.bkash.callback'),
                'amount' => $paymentData['amount'],
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => $paymentData['invoice_number']
            ]);

            if (!$paymentResponse->successful()) {
                throw new Exception('Failed to create bKash payment');
            }

            $paymentInfo = $paymentResponse->json();

            return [
                'success' => true,
                'payment_id' => $paymentInfo['paymentID'],
                'bkash_url' => $paymentInfo['bkashURL'],
                'redirect_url' => $paymentInfo['bkashURL'],
                'gateway' => 'bkash'
            ];

        } catch (Exception $e) {
            Log::error('bKash payment failed', [
                'error' => $e->getMessage(),
                'payment_data' => $paymentData
            ]);

            return [
                'success' => false,
                'error' => 'bKash payment failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process Nagad payment
     */
    private function processNagadPayment($paymentData)
    {
        try {
            $nagadConfig = config('payment.nagad');
            
            // Nagad API integration
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-KM-Api-Version' => 'v-0.2.0',
                'X-KM-IP-V4' => request()->ip(),
                'X-KM-Client-Type' => 'PC_WEB'
            ])->post($nagadConfig['base_url'] . '/remote-payment-gateway-1.0/api/dfs/check-out/initialize/' . $nagadConfig['merchant_id'] . '/' . $paymentData['invoice_number'], [
                'merchantId' => $nagadConfig['merchant_id'],
                'orderId' => $paymentData['invoice_number'],
                'amount' => $paymentData['amount'],
                'currencyCode' => '050'
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                return [
                    'success' => true,
                    'payment_url' => $responseData['callBackUrl'],
                    'redirect_url' => $responseData['callBackUrl'],
                    'gateway' => 'nagad'
                ];
            }

            throw new Exception('Nagad API error');

        } catch (Exception $e) {
            Log::error('Nagad payment failed', [
                'error' => $e->getMessage(),
                'payment_data' => $paymentData
            ]);

            return [
                'success' => false,
                'error' => 'Nagad payment failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process Rocket payment
     */
    private function processRocketPayment($paymentData)
    {
        // Rocket payment integration
        return [
            'success' => true,
            'message' => 'Please send money to Rocket: 01XXXXXXXXX',
            'instructions' => [
                'Dial *322#',
                'Select Send Money',
                'Enter Merchant Number: 01XXXXXXXXX',
                'Enter Amount: ' . $paymentData['amount'],
                'Enter Reference: ' . $paymentData['invoice_number'],
                'Confirm payment'
            ],
            'gateway' => 'rocket'
        ];
    }

    /**
     * Process card payment (Stripe/SSLCommerz)
     */
    private function processCardPayment($paymentData)
    {
        try {
            // SSLCommerz integration for Bangladesh
            $sslConfig = config('payment.sslcommerz');
            
            $postData = [
                'store_id' => $sslConfig['store_id'],
                'store_passwd' => $sslConfig['store_password'],
                'total_amount' => $paymentData['amount'],
                'currency' => 'BDT',
                'tran_id' => $paymentData['invoice_number'],
                'success_url' => route('customer.payments.ssl.success'),
                'fail_url' => route('customer.payments.ssl.fail'),
                'cancel_url' => route('customer.payments.ssl.cancel'),
                'cus_name' => $paymentData['customer_name'],
                'cus_email' => $paymentData['customer_email'],
                'cus_phone' => $paymentData['customer_phone'],
                'cus_add1' => $paymentData['customer_address'],
                'cus_city' => 'Dhaka',
                'cus_country' => 'Bangladesh',
                'product_name' => $paymentData['product_name'],
                'product_category' => 'Subscription',
                'product_profile' => 'general'
            ];

            $response = Http::asForm()->post($sslConfig['base_url'] . '/gwprocess/v4/api.php', $postData);

            if ($response->successful()) {
                $responseData = $response->json();
                
                if ($responseData['status'] === 'SUCCESS') {
                    return [
                        'success' => true,
                        'redirect_url' => $responseData['GatewayPageURL'],
                        'gateway' => 'sslcommerz'
                    ];
                }
            }

            throw new Exception('SSLCommerz API error');

        } catch (Exception $e) {
            Log::error('Card payment failed', [
                'error' => $e->getMessage(),
                'payment_data' => $paymentData
            ]);

            return [
                'success' => false,
                'error' => 'Card payment failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process bank transfer
     */
    private function processBankTransfer($paymentData)
    {
        return [
            'success' => true,
            'message' => 'Bank transfer instructions sent',
            'instructions' => [
                'Bank: Dutch Bangla Bank Limited',
                'Account Name: Your Company Name',
                'Account Number: 1234567890',
                'Routing Number: 090270001',
                'Reference: ' . $paymentData['invoice_number'],
                'Amount: ৳' . number_format($paymentData['amount'], 2)
            ],
            'gateway' => 'bank_transfer'
        ];
    }

    /**
     * Process cash payment
     */
    private function processCashPayment($paymentData)
    {
        return [
            'success' => true,
            'message' => 'Cash payment recorded',
            'status' => 'pending_verification',
            'gateway' => 'cash'
        ];
    }

    /**
     * Verify payment status
     */
    public function verifyPayment($paymentId, $gateway)
    {
        switch ($gateway) {
            case 'bkash':
                return $this->verifyBkashPayment($paymentId);
            case 'nagad':
                return $this->verifyNagadPayment($paymentId);
            case 'sslcommerz':
                return $this->verifySSLCommerzPayment($paymentId);
            default:
                return ['success' => false, 'message' => 'Gateway not supported'];
        }
    }

    /**
     * Verify bKash payment
     */
    private function verifyBkashPayment($paymentId)
    {
        try {
            $bkashConfig = config('payment.bkash');
            
            // Get token first
            $tokenResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'username' => $bkashConfig['username'],
                'password' => $bkashConfig['password']
            ])->post($bkashConfig['base_url'] . '/tokenized/checkout/token/grant', [
                'app_key' => $bkashConfig['app_key'],
                'app_secret' => $bkashConfig['app_secret']
            ]);

            $token = $tokenResponse->json()['id_token'];

            // Query payment
            $queryResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'authorization' => $token,
                'x-app-key' => $bkashConfig['app_key']
            ])->post($bkashConfig['base_url'] . '/tokenized/checkout/payment/status', [
                'paymentID' => $paymentId
            ]);

            if ($queryResponse->successful()) {
                $result = $queryResponse->json();
                
                return [
                    'success' => true,
                    'status' => $result['transactionStatus'],
                    'transaction_id' => $result['trxID'] ?? null,
                    'amount' => $result['amount'] ?? null
                ];
            }

            return ['success' => false, 'message' => 'Payment verification failed'];

        } catch (Exception $e) {
            Log::error('bKash verification failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Verification error'];
        }
    }
}