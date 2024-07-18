<?php

namespace App\Jobs;

use App\Models\CreditClass;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class UpdateClassCode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $class;
    /**
     * Create a new job instance.
     */
    public function __construct(CreditClass $class)
    {
        $this->class = $class;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->class->update(['class_code' => Str::random(6)]);
    }
}
