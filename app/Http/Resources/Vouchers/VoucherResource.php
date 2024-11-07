<?php

namespace App\Http\Resources\Vouchers;

use App\Http\Resources\Users\UserResource;
use App\Http\Resources\VoucherLines\VoucherLineResource;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
{
    /**
     * @var Voucher
     */
    public $resource;

    public function toArray(Request $request): array
    {
        return [
            // Emisor Information
            'id' => $this->resource->id,
            'issuer_name' => $this->resource->issuer_name,
            'issuer_document_type' => $this->resource->issuer_document_type,
            'issuer_document_number' => $this->resource->issuer_document_number,
            
            // Receptor Information
            'receiver_name' => $this->resource->receiver_name,
            'receiver_document_type' => $this->resource->receiver_document_type,
            'receiver_document_number' => $this->resource->receiver_document_number,
            
            // Comprobante Details
            'total_amount' => $this->resource->total_amount,
            'serie' => $this->serie ?? 'N/A',  
            'numero' => $this->numero ?? 'N/A',
            'tipo' => $this->tipo ?? 'N/A',
            'moneda' => $this->moneda ?? 'N/A',

            // Related User (lazy loaded)
            'user' => $this->whenLoaded('user', fn () => UserResource::make($this->resource->user)),
            
            // Voucher Lines (lazy loaded)
            'lines' => $this->whenLoaded('lines', fn () => VoucherLineResource::collection($this->resource->lines)),
        ];
    }
}
