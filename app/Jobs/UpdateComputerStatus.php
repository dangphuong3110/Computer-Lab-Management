<?php

namespace App\Jobs;

use App\Models\Computer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateComputerStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $computer;

    /**
     * Create a new job instance.
     */
    public function __construct(Computer $computer)
    {
        $this->computer = $computer;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->computer->update(['is_active' => false]);
    }
}
