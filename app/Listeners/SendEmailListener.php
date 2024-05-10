<?php

namespace App\Listeners;

use App\Events\ArticleSaved;
use App\Jobs\SendEmailToAdminJob;
use App\Jobs\SendSMSToAdminJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmailListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ArticleSaved $event): void
    {
        SendEmailToAdminJob::dispatch()->onQueue('default');
    }
}
