<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            0 => 8500,
            1 => 12000,
            2 => 25000,
        ];
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
                "success_url" => route("payment.success"),
                "failure_url" => route("payment.failure"),

                'webhook_endpoint' => "https://silaaty.codedev.id/api/chargily/webhook",
            ]);

        return response()->json([
            'status' => true,

            'payment_id' => $payment->id,

            'checkout_url' => str_replace(
                'http://',
                'https://',
                $checkout->getUrl()
            ),
        ]);
    }

    public function paymentStatus($id)
    {
        $payment = \App\Models\ChargilyPayment::findOrFail($id);

        return response()->json([
            "status" => $payment->status,
        ]);
    }

    public function paymentInvoice()
    {
        try {

            $payment = \App\Models\ChargilyPayment::where('user_id', auth()->id())
                ->latest()
                ->first();
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

        if (!$webhook) {
            return response()->json([
                'status' => 0,
            ], 403);
        }

        $checkout = $webhook->getData();

        if (
            !$checkout instanceof \Chargily\ChargilyPay\Elements\CheckoutElement
        ) {
            return response()->json([
                'status' => 0,
            ], 403);
        }

        $metadata = $checkout->getMetadata();

        $payment = \App\Models\ChargilyPayment::find(
            $metadata['payment_id']
        );

        if (!$payment) {
            return response()->json([
                'status' => 0,
            ], 404);
        }

        switch ($checkout->getStatus()) {

            case 'paid':

                $payment->status = 'paid';

                $user = User::find($payment->user_id);

                if ($user) {

                    // إذا التاريخ القديم مازال صالح نكمل عليه
                    $baseDate = $user->date_experiment &&
                        \Carbon\Carbon::parse($user->date_experiment)->isFuture()
                        ? \Carbon\Carbon::parse($user->date_experiment)
                        : now();

                    switch ($payment->type) {

                        // شهر
                        case 0:

                            $user->status = 3;

                            $user->date_experiment = $baseDate
                                ->copy()
                                ->addMonth();

                            break;

                        // سنة
                        case 1:

                            $user->status = 3;

                            $user->date_experiment = $baseDate
                                ->copy()
                                ->addYear();

                            break;

                        // مدى الحياة مثلا
                        case 2:

                            $user->status = 4;

                            break;
                    }

                    $user->save();
                }

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
