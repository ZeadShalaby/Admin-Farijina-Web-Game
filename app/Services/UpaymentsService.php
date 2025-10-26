<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class UpaymentsService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        // You can set these values in your config/services.php or .env file.
        $this->apiKey  = config('services.upayments.api_key', 'jtest123');
        $this->baseUrl = config('services.upayments.base_url', 'https://sandboxapi.upayments.com');
    }

    /**
     * Create a test payment using UPayments API.
     *
     * @param array $paymentData
     * @return array
     *
     * @throws \Exception
     */
    public function createPayment(array $paymentData)
    {
        $endpoint = $this->baseUrl . '/api/v1/charge';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ])->post($endpoint, $paymentData);

        if ($response->successful()) {
            return $response->json();
        }

        // Optionally log the error or handle it as needed
        throw new \Exception('Error creating payment: ' . $response->body());
    }
    /**
     * Get the payment status using the track_id.
     *
     * @param string $trackId
     * @return array
     *
     * @throws \Exception
     */
    public function getPaymentStatus(string $trackId)
    {
        $endpoint = $this->baseUrl . '/api/v1/get-payment-status/' . $trackId;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept'        => 'application/json',
        ])->get($endpoint);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Error fetching payment status: ' . $response->body());
    }
}
