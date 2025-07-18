<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class CheckExperimentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check-experiments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate expired experiment users';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $expiredUsers = User::whereNotNull('date_experiment')
            ->where('date_experiment', '<', $now)
            ->get();

        foreach ($expiredUsers as $user) {
            $user->status = 1;
            $user->experiment_ends_at = null;
            $user->save();

            $this->info("User #{$user->id} has been deactivated.");
        }

        return 0;
    }
}
