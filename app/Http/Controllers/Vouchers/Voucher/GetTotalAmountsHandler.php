<?php

namespace App\Http\Controllers\Vouchers\Voucher;

use App\Models\Voucher;
use Illuminate\Http\JsonResponse;

class GetTotalAmountsHandler
{
    public function __invoke(): JsonResponse
    {
        try {
            // Consulta los montos totales agrupados por moneda
            $totals = Voucher::selectRaw('moneda, SUM(total_amount) as total')
                ->groupBy('moneda')
                ->pluck('total', 'moneda');

            $response = [
                'soles' => $totals['PEN'] ?? 0,
                'dolares' => $totals['USD'] ?? 0,
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            // Manejo de errores en caso de que ocurra alguna excepción
            return response()->json([
                'message' => 'Hubo un problema al obtener los montos totales.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
