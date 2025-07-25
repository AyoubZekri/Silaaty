<?php

namespace App\Console\Commands;

use App\Function\Notification;
use App\Models\invoies;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyUserOnPaymentDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:payment-day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification to users if today is their invoice payment day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();

        $invoices = invoies::whereDate('invoies_payment_date', $today)
            ->with(['products', 'transaction.user'])
            ->get();

        foreach ($invoices as $invoice) {
            $transaction = $invoice->transaction;
            $user = $transaction?->user;

            if ($transaction && $user) {
                $isSupplier = $transaction->transactions == 1;

                // حساب مجموع السعر حسب نوع المعاملة
                $total = $invoice->products->sum(function ($product) use ($isSupplier) {
                    $price = $isSupplier ? $product->product_price_purchase : $product->product_price;
                    return $price * $product->product_quantity;
                });

                $paid = $invoice->Payment_price ?? 0;
                $remaining = $total - $paid;

                // إذا مافي حتى دين باقي، ما تبعثش الإشعار
                if ($remaining <= 0) {
                    continue;
                }

                $notification = new Notification();

                $name = $transaction->name ?? 'الطرف';
                $family = $transaction->family_name ?? '';

                $message = $isSupplier
                    ? "حان وقت تسليم ديونك إلى {$name} {$family} - الفاتورة رقم {$invoice->invoies_numper} - المتبقي: " . number_format($remaining, 2) . " دج"
                    : "حان وقت إستلام ديونك من {$name} {$family} - الفاتورة رقم {$invoice->invoies_numper} - المتبقي: " . number_format($remaining, 2) . " دج";

                $notification->sendNotification(
                    $user->fcm_token,
                    'تنبيه',
                    $message,
                    $user->id,
                    ['pagename' => $isSupplier ? 'Dealer' : 'Clients']
                );
            }
        }
        return Command::SUCCESS;
    }
}
