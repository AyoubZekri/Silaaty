<?php

namespace App\Console\Commands;

use App\Function\Notification;
use App\Models\User;
use Illuminate\Console\Command;

class NotifyTrialEndingSoon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:notify-trial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::whereNotNull('date_experiment')
            ->whereDate('date_experiment', '=', now()->addDay()->toDateString())
            ->get();

        foreach ($users as $user) {
            $notification = new Notification();
            $notification->sendNotification($user->fcm_token, 'تنبيه', 'الفترة التجريبية تنتهي غدا', $user->id, ['pagename' => 'notifications']);

        }

        $this->info('تم إرسال إشعارات للمستخدمين الذين تنتهي فترتهم التجريبية غدًا.');
    }
}
