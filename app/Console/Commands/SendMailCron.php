<?php

namespace App\Console\Commands;

use App\Models\EmailTemplate;
use App\Models\Member;
use App\Utility\EmailUtility;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendMailCron extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send_mail:cron_package_expiry_warning';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        // \Log::info("Cron is working fine!");

        $members = Member::where('package_validity', '>', Carbon::now()->format('Y-m-d'))->get();

        foreach ($members as $member) {;
            $package_validity = Carbon::parse($member->package_validity);
            $currentDate = Carbon::now()->format('Y-m-d');


            $days_left = $package_validity->diffInDays($currentDate);

            if ($member->user->email != null  && env('MAIL_USERNAME') != null) {
                $package_expiring_warning_email = EmailTemplate::where('identifier', 'package_expiring_warning_email')->first();
                if ($package_expiring_warning_email->status == 1) {
                    EmailUtility::package_expiring_warning_email($member->user->id, $days_left);
                }
            }
        }
    }
}
