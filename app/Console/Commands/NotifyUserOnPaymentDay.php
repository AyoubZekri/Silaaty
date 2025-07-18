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

        $invoices = invoies::whereDate('invoies_payment_date', $today)->with('transaction.user')->get();

        foreach ($invoices as $invoice) {
            if ($invoice->transaction && $invoice->transaction->user) {
                $user = $invoice->transaction->user;
                $transaction = $invoice->transaction;

                $notification = new Notification();

                $otherPartyName = $transaction->name ?? 'طرف المعاملة';
                $otherPartyfamlyname = $transaction->family_name ?? '';


                if ($transaction->transactions == 1) {
                    $notification->sendNotification(
                        $user->fcm_token,
                        'تنبيه',
                        "حان وقت تسليم ديونك إلى {$otherPartyName} {$otherPartyfamlyname} - الفاتورة رقم {$invoice->invoies_numper}",
                        $user->id,
                        ['pagename' => 'Dealer']

                    );
                } else {
                    $notification->sendNotification(
                        $user->fcm_token,
                        'تنبيه',
                        "حان وقت إستلام ديونك من {$otherPartyName} {$otherPartyfamlyname} - الفاتورة رقم {$invoice->invoies_numper}",
                        $user->id,
                        ['pagename' => 'Clients']

                    );
                }
            }
        }

        return Command::SUCCESS;
    }
}
