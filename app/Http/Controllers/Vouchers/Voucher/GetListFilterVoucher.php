<?php

namespace App\Http\Controllers\Vouchers\Voucher;

use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GetListFilterVoucher
{
    public function __invoke(Request $request): JsonResponse
    {
        $userId = auth()->id();
        
        // Validar entrada para mayor tolerancia a fallos
        $validator = Validator::make($request->all(), [
            'serie' => 'string|nullable',
            'numero' => 'string|nullable',
            'fecha_inicio' => 'date|nullable',
            'fecha_fin' => 'date|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Construir la consulta base para los comprobantes del usuario autenticado
        $query = Voucher::where('user_id', $userId);

        // Aplicar filtros condicionalmente
        if ($request->filled('serie')) {
            $query->where('serie', $request->input('serie'));
        }

        if ($request->filled('numero')) {
            $query->where('numero', $request->input('numero'));
        }

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('created_at', [
                $request->input('fecha_inicio'),
                $request->input('fecha_fin')
            ]);
        }

        // Ejecutar la consulta y obtener los resultados paginados
        $vouchers = $query->paginate(10);

        return response()->json($vouchers);
    }
}
