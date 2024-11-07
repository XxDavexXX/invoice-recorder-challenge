<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\VoucherService;
use App\Models\User;


class ProcessVouchersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $xmlContents;
    protected $user;

    public function __construct(array $xmlContents, User $user)
    {
        $this->xmlContents = $xmlContents;
        $this->user = $user;
    }

    public function handle(VoucherService $voucherService)
    {
        $successfulVouchers = [];
        $failedVouchers = [];

        foreach ($this->xmlContents as $xmlContent) {
            try {
                $voucher = $voucherService->storeVoucherFromXmlContent($xmlContent, $this->user);
                $successfulVouchers[] = $voucher;
            } catch (\Exception $e) {
                $failedVouchers[] = [
                    'xmlContent' => $xmlContent,
                    'reason' => $e->getMessage(),
                ];
            }
        }

        // Enviar el correo con los comprobantes exitosos y fallidos
        $this->sendNotification($successfulVouchers, $failedVouchers);

        Log::info("Job ProcessVouchersJob completado para el usuario {$this->user->email}");
    }

    protected function sendNotification(array $successfulVouchers, array $failedVouchers)
    {
        \Mail::to($this->user->email)->send(new \App\Mail\VouchersProcessed($this->user, $successfulVouchers, $failedVouchers));
    }
}
