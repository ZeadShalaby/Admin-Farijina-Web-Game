<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Services\UpaymentsService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $upaymentsService;

    public function __construct(UpaymentsService $upaymentsService)
    {
        $this->upaymentsService = $upaymentsService;
    }

    /**
     * Initiate the payment.
     *
     * Optionally, store a local transaction record in a pending state.
     */
    public function createPayment(Request $request)
    {
        $user = $request->user; // Assume this returns the authenticated user
        // create validation rules based on your requirements
        $rules = [
            'amount' => 'required|numeric',
            'num_of_games_he_pay' => 'required|numeric|min:1',
        ];

        // validate the request data against the rules
        $request->validate($rules);


        // Build the payload based on UPayments API requirements.
        $paymentData = [
            'order' => [
                'id'          => uniqid(),
                'reference'   => $request->input('num_of_games_he_pay'),
                'description' => 'Credit',
                'currency'    => 'KWD',
                'amount'      => $request->input('amount'),
            ],
            'language' => 'en',
            'paymentGateway' => [
                'src' => 'knet',
            ],
            'reference' => [
                'id' => $request->input('num_of_games_he_pay'),
            ],
            'customer' => [
                'uniqueId' => (string)$user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'mobile'   => $user->phone,
            ],
            'returnUrl'       => url('/payments/return'),
            'cancelUrl'       => url('/payments/cancel'),
            'notificationUrl' => url('/payments/notify'),
        ];

        try {
            // Call the UPayments API to initiate the payment.
            $result = $this->upaymentsService->createPayment($paymentData);




            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle the return URL callback after payment.
     *
     * This method retrieves the detailed payment status using the track_id from the callback,
     * creates a transaction record, and completes the process.
     */
    public function returnUrl(Request $request)
    {
        // Retrieve the track_id from the query parameters.
        $track_id = $request->query('track_id');

        if (!$track_id) {
            return response()->json(['error' => 'Missing track_id in callback'], 400);
        }

        try {
            // Fetch detailed payment status from UPayments using track_id.
            $paymentStatus = $this->upaymentsService->getPaymentStatus($track_id);

            // Assume the detailed transaction data is located in:
            // $paymentStatus['data']['transaction']
            $transactionData = $paymentStatus['data']['transaction'] ?? null;
            if ($transactionData) {
                $user = User::where('id', $transactionData['customer_unique_id'])->first();
                $user->num_of_games += (int) $transactionData['reference'];
                $user->save();
                // Create a new transaction record based on the payment details.
                $transaction = Transaction::create([
                    'payment_id'         => $transactionData['payment_id'] ?? null,
                    'result'             => $transactionData['result'] ?? null,
                    'order_id'           => $transactionData['order_id'] ?? null,
                    'requested_order_id' => $transactionData['merchant_requested_order_id'] ?? null,
                    'refund_order_id'    => $transactionData['refund_order_id'] ?? null,
                    'payment_type'       => $transactionData['payment_type'] ?? null,
                    'invoice_id'         => $transactionData['invoice_id'] ?? null,
                    'transaction_date'   => $transactionData['transaction_date'] ?? null,
                    'receipt_id'         => $transactionData['receipt_id'] ?? null,
                    // Optionally, if you have more details:
                    'track_id'           => $transactionData['track_id'] ?? null,
                    'ref'                => $transactionData['reference'] ?? null,
                    // Set the user_id from the authenticated user (if applicable)
                    'user_id'            =>  $user->id ?? null,
                    // Amount can be taken from total_price or another field.
                    'amount'             => $transactionData['total_price'] ?? 0.00,
                    // Determine status based on result/status values.
                    'status'             => (
                        isset($transactionData['result']) &&
                        ($transactionData['result'] === 'CAPTURED' || ($transactionData['status'] ?? '') === 'done')
                    ) ? 'success' : 'failed',
                    // For demonstration, we assume one game per payment.
                    'num_of_games_he_pay' => $transactionData['reference'],
                ]);

                return redirect()->away('https://www.freejnakw.com?success=true&transaction_id=' . $transaction->id);
            } else {
                return redirect()->away('https://www.freejnakw.com?success=false&error=Transaction data not found');
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




    /**
     * Handle the cancel URL callback when a payment is canceled.
     */
    public function cancelUrl(Request $request)
    {
        // Process the cancellation callback.
        // Example: update the order status, log cancellation details, etc.
        return response()->json([
            'message' => 'Payment was canceled.',
            'data'    => $request->all(),
        ]);
    }

    /**
     * (Optional) Handle the notification URL callback.
     */
    public function notifyUrl(Request $request)
    {
        // Process webhook notifications from UPayments if needed.
        return response()->json([
            'message' => 'Notification received.',
            'data'    => $request->all(),
        ]);
    }
}
