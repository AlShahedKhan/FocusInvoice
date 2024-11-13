<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Http\Controllers\Controller;

class PayPalController extends Controller
{
    public function createOrder(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $order = $provider->createOrder([
            "intent" => "CAPTURE",
            // "application_context" => [
            //     "return_url" => route('paypal.capture-order'),  // Named route for capturing
            //     "cancel_url" => route('paypal.cancel-order'),   // Named route for cancellation
            // ],
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $request->amount
                    ]
                ]
            ]
        ]);

        if (isset($order['id'])) {
            return response()->json($order);
        }

        return response()->json(['error' => 'Unable to create PayPal order.'], 500);
    }


    public function captureOrder(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->capturePaymentOrder($request->orderID);

        if (isset($response['status']) && $response['status'] === 'COMPLETED') {
            // Handle successful payment here (e.g., update database, send confirmation email)
            return response()->json(['success' => 'Transaction completed.']);
        }

        return response()->json(['error' => 'Transaction failed.'], 500);
    }

    public function cancelOrder()
    {
        // Handle payment cancellation
        return response()->json(['message' => 'Payment was cancelled.']);
    }
}
