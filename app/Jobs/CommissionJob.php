<?php

namespace App\Jobs;

use App\Services\AffiliateCommissionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CommissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     */
    public function handle(AffiliateCommissionService $service): void
    {
        try {
            Log::info("CommissionJob: Processing order #{$this->orderId}");
            $service->createForOrder($this->orderId);
        } catch (\Exception $e) {
            Log::error("CommissionJob Failed for order #{$this->orderId}: " . $e->getMessage());
            throw $e;
        }
    }
}
