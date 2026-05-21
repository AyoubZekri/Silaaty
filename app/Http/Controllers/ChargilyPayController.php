<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChargilyPayController extends Controller
{
    public function createPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|integer|in:0,1,2',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
            ]);
        }
        $user = auth()->user();
        $amounts = [
            0 => 1500,
            1 => 5000,
            2 => 25000,
        ];
        $payment = \App\Models\ChargilyPayment::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'currency' => 'dzd',
            'type' => $request->type,
            'amount' => $amounts[$request->type],
        ]);
        $checkout = $this->chargilyPayInstance()
            ->checkouts()
            ->create([
                'metadata' => [
                    'payment_id' => $payment->id,
                ],

                'locale' => 'ar',

                'amount' => $payment->amount,

                'currency' => $payment->currency,

                'description' => "Payment #{$payment->id}",

                // deep link optional
                'success_url' => 'myapp://payment-success',

                'failure_url' => 'myapp://payment-failed',

                'webhook_endpoint' => route('chargily.webhook'),
            ]);

        return response()->json([
            'status' => true,

            'payment_id' => $payment->id,

            'checkout_url' => $checkout->getUrl(),
        ]);
    }

    public function paymentStatus($id)
    {
        try {

            $payment = \App\Models\ChargilyPayment::findOrFail($id);

            return response()->json([
                'status' => 1,
                'data' => $payment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ]);
        }

    }

    public function paymentInvoice()
    {
        try {

            $payment = \App\Models\ChargilyPayment::where('user_id', auth()->id())
                ->latest()
                ->firstOrFail();
            $user = \App\Models\User::where('id', auth()->id())->first();

            return response()->json([
                'status' => 1,
                'data' => $payment,
                'user' => $user,
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ]);

        }
    }

    public function webhook()
    {
        $webhook = $this->chargilyPayInstance()
            ->webhook()
            ->get();

        if (
            ! $webhook
        ) {
            return response()->json([
                'status' => 0,
            ], 403);
        }

        $checkout = $webhook->getData();

        if (
            ! $checkout instanceof \Chargily\ChargilyPay\Elements\CheckoutElement
        ) {
            return response()->json([
                'status' => 0,
            ], 403);
        }

        $metadata = $checkout->getMetadata();

        $payment = \App\Models\ChargilyPayment::find(
            $metadata['payment_id']
        );

        if (! $payment) {
            return response()->json([
                'status' => 0,
            ], 404);
        }

        switch ($checkout->getStatus()) {

            case 'paid':

                $payment->status = 'paid';

                // confirm order here

                break;

            case 'failed':

            case 'canceled':

                $payment->status = 'failed';

                break;
        }

        $payment->save();

        return response()->json([
            'status' => true,
        ]);
    }

    protected function chargilyPayInstance()
    {
        return new \Chargily\ChargilyPay\ChargilyPay(
            new \Chargily\ChargilyPay\Auth\Credentials([
                'mode' => 'test',
                'public' => env('CHARGILY_PUBLIC_KEY'),
                'secret' => env('CHARGILY_SECRET_KEY'),
            ])
        );
    }
}
