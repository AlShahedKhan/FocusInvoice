<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class PayPalController extends Controller
{
    public function createOrder(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $order = $provider->createOrder([
            "intent" => "CAPTURE",
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
            // Check if the user is authenticated
            if (!Auth::check()) {
                return response()->json(['error' => 'User is not authenticated.'], 401);
            }

            // Save payment details to the database
            $payment = new Payment();
            $payment->user_id = Auth::id();
            $payment->order_id = $request->orderID;
            $payment->status = $response['status'];
            $payment->amount = $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
            $payment->currency = $response['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'];
            $payment->save();

            return response()->json(['success' => 'Transaction completed and saved to database.']);
        }

        return response()->json(['error' => 'Transaction failed.'], 500);
    }


    // public function captureOrder(Request $request)
    // {
    //     $provider = new PayPalClient;
    //     $provider->setApiCredentials(config('paypal'));
    //     $provider->getAccessToken();

    //     $response = $provider->capturePaymentOrder($request->orderID);

    //     if (isset($response['status']) && $response['status'] === 'COMPLETED') {
    //         // Save payment details to the database
    //         $payment = new Payment();
    //         $payment->user_id = Auth::id(); // Assuming the user is authenticated
    //         $payment->order_id = $request->orderID;
    //         $payment->status = $response['status'];
    //         $payment->amount = $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
    //         $payment->currency = $response['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'];
    //         $payment->save();

    //         return response()->json(['success' => 'Transaction completed and saved to database.']);
    //     }

    //     return response()->json(['error' => 'Transaction failed.'], 500);
    // }

    public function cancelOrder()
    {
        return response()->json(['message' => 'Payment was cancelled.']);
    }
}
