<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\FcmToken;
use Carbon\Carbon;

class CleanupInvalidFcmTokens implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
  
    public function handle()
    {
        // Remove tokens that haven't been updated in 30 days
        FcmToken::where('updated_at', '<', Carbon::now()->subDays(30))->delete();
    }
}
