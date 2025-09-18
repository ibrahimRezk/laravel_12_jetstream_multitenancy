<?php

namespace App\Services;

use App\Models\SubscriptionPlan;
use App\Models\TenantSubscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PayPalService
{
    private $accessToken;
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.paypal.mode') === 'live' 
            ? 'https://api.paypal.com'
            : 'https://api.sandbox.paypal.com';
            
        $this->getAccessToken();
    }

    private function getAccessToken()
    {
        $response = Http::withBasicAuth(
            config('services.paypal.client_id'),
            config('services.paypal.client_secret')
        )->asForm()->post($this->baseUrl . '/v1/oauth2/token', [
            'grant_type' => 'client_credentials'
        ]);

        if ($response->successful()) {
            $this->accessToken = $response->json('access_token');
        } else {
            throw new \Exception('Failed to get PayPal access token: ' . $response->body());
        }
    }

    private function makeRequest(string $method, string $endpoint, array $data = [])
    {
        $response = Http::withToken($this->accessToken)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'PayPal-Request-Id' => uniqid(),
            ]);

        $response = match(strtoupper($method)) {
            'GET' => $response->get($this->baseUrl . $endpoint),
            'POST' => $response->post($this->baseUrl . $endpoint, $data),
            'PUT' => $response->put($this->baseUrl . $endpoint, $data),
            'DELETE' => $response->delete($this->baseUrl . $endpoint),
            'PATCH' => $response->patch($this->baseUrl . $endpoint, $data),
        };

        if (!$response->successful()) {
            Log::error('PayPal API Error', [
                'method' => $method,
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'response' => $response->json()
            ]);
            throw new \Exception('PayPal API Error: ' . $response->body());
        }

        return $response->json();
    }

    public function createProduct(array $productData)
    {
        $data = [
            'name' => $productData['name'],
            'description' => $productData['description'],
            'type' => 'SERVICE',
            'category' => 'SOFTWARE',
            // 'home_url' => config('app.url') // tobe unchecked
        ];

        if (isset($productData['image_url'])) {
            $data['image_url'] = $productData['image_url'];
        }

        try {
            $result = $this->makeRequest('POST', '/v1/catalogs/products', $data);
            return (object) $result;
        } catch (\Exception $e) {
            Log::error('PayPal Product Creation Failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function createPlan(string $productId, array $planData): object
    {
        $data = [
            'product_id' => $productId,
            'name' => $planData['name'],
            'description' => $planData['description'],
            'billing_cycles' => [
                [
                    'frequency' => [
                        'interval_unit' => $planData['interval'],
                        'interval_count' => 1
                    ],
                    'tenure_type' => 'REGULAR',
                    'sequence' => 1,
                    'total_cycles' => 0, // Infinite
                    'pricing_scheme' => [
                        'fixed_price' => [
                            'value' => (string) $planData['price'],
                            'currency_code' => $planData['currency_code']
                        ]
                    ]
                ]
            ],
            'payment_preferences' => [
                'auto_bill_outstanding' => true,
                'setup_fee' => [
                    'value' => '0',
                    'currency_code' => $planData['currency_code']
                ],
                'setup_fee_failure_action' => 'CONTINUE',
                'payment_failure_threshold' => 3
            ],
            'taxes' => [
                'percentage' => '0',
                'inclusive' => false
            ]
        ];

        try {
            $result = $this->makeRequest('POST', '/v1/billing/plans', $data);
            return (object) $result;
        } catch (\Exception $e) {
            Log::error('PayPal Plan Creation Failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function createSubscription(string $planId)
    {
        $data = [ 
            'plan_id' => $planId,
            'start_time' => now()->addMinute()->toISOString(),
            'subscriber' => [
                'name' => [
                    'given_name' => auth()->user()->name ?? 'Customer',
                    'surname' => auth()->user()->name ?? 'User'
                ],
                'email_address' => auth()->user()->email
            ],
            'application_context' => [
                'brand_name' => config('app.name'),
                'locale' => 'en-US',
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'SUBSCRIBE_NOW',
                'payment_method' => [
                    'payer_selected' => 'PAYPAL',
                    'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED'
                ],
                'return_url' => route('paypal.success'),
                'cancel_url' => route('paypal.cancel')
            ]
        ];

        try {
            $result = $this->makeRequest('POST', '/v1/billing/subscriptions', $data);
            // dd($result);
            return (object) $result;
        } catch (\Exception $e) {
            Log::error('PayPal Subscription Creation Failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function getSubscription(string $subscriptionId)
    {
        try {
            $result = $this->makeRequest('GET', "/v1/billing/subscriptions/{$subscriptionId}");
            return (object) $result;
        } catch (\Exception $e) {
            Log::error('PayPal Get Subscription Failed', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function cancelSubscription(string $subscriptionId, string $reason = 'User requested cancellation')
    {
        $data = [
            'reason' => $reason
        ];

        try {
            $this->makeRequest('POST', "/v1/billing/subscriptions/{$subscriptionId}/cancel", $data);
            return true;
        } catch (\Exception $e) {
            Log::error('PayPal Cancel Subscription Failed', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function suspendSubscription(string $subscriptionId, string $reason = 'Suspended by merchant')
    {
        $data = [
            'reason' => $reason
        ];

        try {
            $this->makeRequest('POST', "/v1/billing/subscriptions/{$subscriptionId}/suspend", $data);
            return true;
        } catch (\Exception $e) {
            Log::error('PayPal Suspend Subscription Failed', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function activateSubscription(string $subscriptionId, string $reason = 'Reactivating the subscription')
    {
        $data = [
            'reason' => $reason
        ];

        try {
            $this->makeRequest('POST', "/v1/billing/subscriptions/{$subscriptionId}/activate", $data);
            return true;
        } catch (\Exception $e) {
            Log::error('PayPal Activate Subscription Failed', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function verifyWebhookSignature(array $headers, string $payload, string $webhookId): bool
    {
        $data = [
            'transmission_id' => $headers['PAYPAL-TRANSMISSION-ID'] ?? '',
            'cert_id' => $headers['PAYPAL-CERT-ID'] ?? '',
            'auth_algo' => $headers['PAYPAL-AUTH-ALGO'] ?? '',
            'transmission_time' => $headers['PAYPAL-TRANSMISSION-TIME'] ?? '',
            'webhook_id' => $webhookId,
            'webhook_event' => json_decode($payload, true)
        ];

        try {
            $result = $this->makeRequest('POST', '/v1/notifications/verify-webhook-signature', $data);
            return ($result['verification_status'] ?? '') === 'SUCCESS';
        } catch (\Exception $e) {
            Log::error('PayPal Webhook Verification Failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function listPlans(int $pageSize = 20, int $page = 1)
    {
        try {
            $result = $this->makeRequest('GET', "/v1/billing/plans?page_size={$pageSize}&page={$page}");
            return $result;
        } catch (\Exception $e) {
            Log::error('PayPal List Plans Failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }



    public function migratePlanSubscriptions(string $oldPlanId, string $newPlanId, array $subscriptionIds = [])
    {
        $results = [
            'successful' => [],
            'failed' => []
        ];

        // If no specific subscription IDs provided, get all active subscriptions for the old plan
        if (empty($subscriptionIds)) {
            $subscriptions = TenantSubscription::where('paypal_subscription_id', '!=', null)
                ->where('status', 'active')
                ->whereHas('subscriptionPlan', function($query) use ($oldPlanId) {
                    $query->where('paypal_plan_id', $oldPlanId);
                })
                ->get();
            
            $subscriptionIds = $subscriptions->pluck('paypal_subscription_id')->toArray();
        }

        foreach ($subscriptionIds as $subscriptionId) {
            try {
                // Cancel old subscription
                $this->cancelSubscription($subscriptionId, 'Plan migration');
                
                // Note: You would need to create a new subscription with the new plan
                // This typically requires user interaction for payment authorization
                $results['successful'][] = $subscriptionId;
                
            } catch (\Exception $e) {
                Log::error('Failed to migrate subscription', [
                    'subscription_id' => $subscriptionId,
                    'old_plan_id' => $oldPlanId,
                    'new_plan_id' => $newPlanId,
                    'error' => $e->getMessage()
                ]);
                
                $results['failed'][] = [
                    'subscription_id' => $subscriptionId,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }



    public function updateProduct(string $productId, array $updates)
    {
        $patchOperations = [];

        // Update product name
        if (isset($updates['name'])) {
            $patchOperations[] = [
                'op' => 'replace',
                'path' => '/name',
                'value' => $updates['name']
            ];
        }

        // Update product description
        if (isset($updates['description'])) {
            $patchOperations[] = [
                'op' => 'replace',
                'path' => '/description',
                'value' => $updates['description']
            ];
        }

        // Update product category
        if (isset($updates['category'])) {
            $patchOperations[] = [
                'op' => 'replace',
                'path' => '/category',
                'value' => strtoupper($updates['category'])
            ];
        }

        // Update product image URL
        if (isset($updates['image_url'])) {
            $patchOperations[] = [
                'op' => 'replace',
                'path' => '/image_url',
                'value' => $updates['image_url']
            ];
        }

        // Update product home URL
        if (isset($updates['home_url'])) {
            $patchOperations[] = [
                'op' => 'replace',
                'path' => '/home_url',
                'value' => $updates['home_url']
            ];
        }

        if (empty($patchOperations)) {
            throw new \InvalidArgumentException('No valid update parameters provided');
        }

        try {
            $this->makeRequest('PATCH', "/v1/catalogs/products/{$productId}", $patchOperations);
            
            Log::info('PayPal Product Updated Successfully', [
                'product_id' => $productId,
                'updates' => $updates
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('PayPal Update Product Failed', [
                'product_id' => $productId,
                'updates' => $updates,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function deactivatePlan(string $planId)
    {
        $data = [
            [
                'op' => 'replace',
                'path' => '/status',
                'value' => 'INACTIVE'
            ]
        ];

        try {
            $this->makeRequest('PATCH', "/v1/billing/plans/{$planId}", $data);
            
            Log::info('PayPal Plan Deactivated', ['plan_id' => $planId]);
            return true;
        } catch (\Exception $e) {
            Log::error('PayPal Deactivate Plan Failed', [
                'plan_id' => $planId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function activatePlan(string $planId)
    {
        $data = [
            [
                'op' => 'replace',
                'path' => '/status',
                'value' => 'ACTIVE'
            ]
        ];

        try {
            $this->makeRequest('PATCH', "/v1/billing/plans/{$planId}", $data);
            
            Log::info('PayPal Plan Activated', ['plan_id' => $planId]);
            return true;
        } catch (\Exception $e) {
            Log::error('PayPal Activate Plan Failed', [
                'plan_id' => $planId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }




    public function createNewPlanWithUpdatedPricing(string $productId, array $planData, string $oldPlanId = null)
    {
        // Create a new plan with updated pricing/billing cycle
        $newPlan = $this->createPlan($productId, $planData);
        
        if ($oldPlanId) {
            // Deactivate the old plan
            try {
                $this->deactivatePlan($oldPlanId);
                Log::info('Old plan deactivated after creating new one', [
                    'old_plan_id' => $oldPlanId,
                    'new_plan_id' => $newPlan->id
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to deactivate old plan', [
                    'old_plan_id' => $oldPlanId,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $newPlan;
    }

    // public function migratePlanSubscriptions(string $oldPlanId, string $newPlanId, array $subscriptionIds = [])
    // {
    //     $results = [
    //         'successful' => [],
    //         'failed' => []
    //     ];

    //     // If no specific subscription IDs provided, get all active subscriptions for the old plan
    //     if (empty($subscriptionIds)) {
    //         $subscriptions = TenantSubscription::where('paypal_subscription_id', '!=', null)
    //             ->where('status', 'active')
    //             ->whereHas('subscriptionPlan', function($query) use ($oldPlanId) {
    //                 $query->where('paypal_plan_id', $oldPlanId);
    //             })
    //             ->get();
            
    //         $subscriptionIds = $subscriptions->pluck('paypal_subscription_id')->toArray();
    //     }

    //     foreach ($subscriptionIds as $subscriptionId) {
    //         try {
    //             // Cancel old subscription
    //             $this->cancelSubscription($subscriptionId, 'Plan migration');
                
    //             // Note: You would need to create a new subscription with the new plan
    //             // This typically requires user interaction for payment authorization
    //             $results['successful'][] = $subscriptionId;
                
    //         } catch (\Exception $e) {
    //             Log::error('Failed to migrate subscription', [
    //                 'subscription_id' => $subscriptionId,
    //                 'old_plan_id' => $oldPlanId,
    //                 'new_plan_id' => $newPlanId,
    //                 'error' => $e->getMessage()
    //             ]);
                
    //             $results['failed'][] = [
    //                 'subscription_id' => $subscriptionId,
    //                 'error' => $e->getMessage()
    //             ];
    //         }
    //     }

    //     return $results;
    // }

    public function updatePlanPricing(string $planId, array $pricingData)
    {
        // PayPal requires activating pricing changes with a separate API call
        $data = [
            'pricing_schemes' => [
                [
                    'billing_cycle_sequence' => 1,
                    'pricing_scheme' => [
                        'fixed_price' => [
                            'value' => (string) $pricingData['price'],
                            'currency_code' => $pricingData['currency_code'] ?? 'USD'
                        ]
                    ]
                ]
            ]
        ];

        try {
            // Use the correct endpoint for updating plan pricing
            $this->makeRequest('POST', "/v1/billing/plans/{$planId}/update-pricing-schemes", $data);
            
            Log::info('PayPal Plan Pricing Updated Successfully', [
                'plan_id' => $planId,
                'new_price' => $pricingData['price'],
                'currency' => $pricingData['currency_code'] ?? 'USD'
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('PayPal Update Plan Pricing Failed', [
                'plan_id' => $planId,
                'pricing_data' => $pricingData,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function updatePlan(string $planId, array $updates)
    {
        $patchOperations = [];

        // Update plan name
        if (isset($updates['name'])) {
            $patchOperations[] = [
                'op' => 'replace',
                'path' => '/name',
                'value' => $updates['name']
            ];
        }

        // Update plan description
        if (isset($updates['description'])) {
            $patchOperations[] = [
                'op' => 'replace',
                'path' => '/description',
                'value' => $updates['description']
            ];
        }

        // Handle pricing separately using the dedicated pricing endpoint
        $pricingUpdateNeeded = isset($updates['price']) || isset($updates['currency_code']);
        
        // Update other allowed fields
        if (isset($updates['auto_bill_outstanding'])) {
            $patchOperations[] = [
                'op' => 'replace',
                'path' => '/payment_preferences/auto_bill_outstanding',
                'value' => (bool) $updates['auto_bill_outstanding']
            ];
        }

        if (isset($updates['payment_failure_threshold'])) {
            $patchOperations[] = [
                'op' => 'replace',
                'path' => '/payment_preferences/payment_failure_threshold',
                'value' => (int) $updates['payment_failure_threshold']
            ];
        }

        if (isset($updates['setup_fee_failure_action'])) {
            $patchOperations[] = [
                'op' => 'replace',
                'path' => '/payment_preferences/setup_fee_failure_action',
                'value' => strtoupper($updates['setup_fee_failure_action']) // CONTINUE or CANCEL
            ];
        }

        try {
            // Update basic plan details if any
            if (!empty($patchOperations)) {
                $this->makeRequest('PATCH', "/v1/billing/plans/{$planId}", $patchOperations);
                Log::info('PayPal Plan Details Updated', ['plan_id' => $planId]);
            }

            // Update pricing if needed
            if ($pricingUpdateNeeded) {
                $this->updatePlanPricing($planId, [
                    'price' => $updates['price'] ?? null,
                    'currency_code' => $updates['currency_code'] ?? 'USD'
                ]);
            }

            Log::info('PayPal Plan Updated Successfully', [
                'plan_id' => $planId,
                'updates' => $updates
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('PayPal Update Plan Failed', [
                'plan_id' => $planId,
                'updates' => $updates,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }



}