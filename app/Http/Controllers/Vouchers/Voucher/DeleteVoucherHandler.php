<?php

namespace App\Http\Controllers\Vouchers\Voucher;

use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Exception;
class DeleteVoucherHandler
{
    public function __invoke($id): JsonResponse
    {
        // Buscar el comprobante por ID, incluyendo eliminados si fuera necesario
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return response()->json(['message' => 'Comprobante no encontrado.'], 404);
        }

        try {
            $voucher->delete(); // Esto aplicará eliminación lógica
            return response()->json(['message' => 'Comprobante eliminado exitosamente.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el comprobante.', 'error' => $e->getMessage()], 500);
        }
    }

}
