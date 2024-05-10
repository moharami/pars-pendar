<?php

namespace App\Jobs;

use App\Mail\ArticleCreatedEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailToAdminJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $admin_mail = config('admin.email');
//        Mail::to($admin_mail)->send(new ArticleCreatedEmail([
//            'name' => 'Admin',
//        ]));
        Log::info('SMS Email To Admin');
    }
}
