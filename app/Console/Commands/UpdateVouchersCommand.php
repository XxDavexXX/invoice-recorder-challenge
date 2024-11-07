<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\VoucherService;


class UpdateVouchersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vouchers:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los campos serie, numero, tipo y moneda en vouchers existentes';

    /**
     * El servicio de vouchers
     *
     * @var VoucherService
     */
    private VoucherService $voucherService;

    /**
     * Crea una nueva instancia del comando.
     *
     * @param VoucherService $voucherService
     */
    public function __construct(VoucherService $voucherService)
    {
        parent::__construct();
        $this->voucherService = $voucherService;
    }


    /**
     * Ejecuta el comando de consola.
     *
     * @return int
     */
    public function handle()
    {
        $this->voucherService->updateExistingVouchersFields();
        $this->info('Vouchers actualizados exitosamente.');
        return 0;
    }
}
