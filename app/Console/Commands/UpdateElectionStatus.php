<?php

namespace App\Console\Commands;

use App\Models\Election;
use Illuminate\Console\Command;

class UpdateElectionStatus extends Command
{
    protected $signature = 'elections:update-status';
    protected $description = 'Update election active status based on start and end dates';

    public function handle()
    {
        Election::chunk(100, function ($elections) {
            foreach ($elections as $election) {
                $now = now();
                $shouldBeActive = $now->between($election->start_date, $election->end_date);
                
                if ($election->is_active !== $shouldBeActive) {
                    $election->is_active = $shouldBeActive;
                    $election->save();
                }
            }
        });

        $this->info('Election statuses updated successfully');
    }
}