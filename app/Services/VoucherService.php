<?php

namespace App\Services;

use App\Events\Vouchers\VouchersCreated;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherLine;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use SimpleXMLElement;
use App\Jobs\ProcessVouchersJob;

class VoucherService
{

    public function storeVouchersInBackground(array $xmlContents, User $user)
    {
        ProcessVouchersJob::dispatch($xmlContents, $user);
    }

    public function getVouchers(int $page, int $paginate): LengthAwarePaginator
    {
        return Voucher::with(['lines', 'user'])->paginate(perPage: $paginate, page: $page);
    }

    /**
     * @param string[] $xmlContents
     * @param User $user
     * @return Voucher[]
     */
    public function storeVouchersFromXmlContents(array $xmlContents, User $user): array
    {
        $vouchers = [];
        foreach ($xmlContents as $xmlContent) {
            $vouchers[] = $this->storeVoucherFromXmlContent($xmlContent, $user);
        }

        VouchersCreated::dispatch($vouchers, $user);

        return $vouchers;
    }

    public function storeVoucherFromXmlContent(string $xmlContent, User $user): Voucher
    {
        try {
            $xml = new SimpleXMLElement($xmlContent);
            
            // Emisor
            $issuerName = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyName/cbc:Name')[0];
            $issuerDocumentType = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
            $issuerDocumentNumber = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];
    
            // Receptor
            $receiverName = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName')[0];
            $receiverDocumentType = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
            $receiverDocumentNumber = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];
    
            // Totales y datos adicionales
            $totalAmount = (string) $xml->xpath('//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount')[0];
            $id = (string) $xml->xpath('//cbc:ID')[0];
            if (strpos($id, '-') !== false) {
                [$serie, $numero] = explode('-', $id, 2);
            } else {
                $serie = ''; 
                $numero = $id;
            }
            $tipo = (string) $xml->xpath('//cbc:InvoiceTypeCode')[0] ?? null;
            $moneda = (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0];
    
            // Crear y guardar el voucher
            $voucher = new Voucher([
                'issuer_name' => $issuerName,
                'issuer_document_type' => $issuerDocumentType,
                'issuer_document_number' => $issuerDocumentNumber,
                'receiver_name' => $receiverName,
                'receiver_document_type' => $receiverDocumentType,
                'receiver_document_number' => $receiverDocumentNumber,
                'total_amount' => $totalAmount,
                'xml_content' => $xmlContent,
                'user_id' => $user->id,
                'serie' => $serie,
                'numero' => $numero,
                'tipo' => $tipo,
                'moneda' => $moneda,
            ]);
            $voucher->save();
    
            // Guardar las líneas de comprobante
            foreach ($xml->xpath('//cac:InvoiceLine') as $invoiceLine) {
                $name = (string) $invoiceLine->xpath('cac:Item/cbc:Description')[0];
                $quantity = (float) $invoiceLine->xpath('cbc:InvoicedQuantity')[0];
                $unitPrice = (float) $invoiceLine->xpath('cac:Price/cbc:PriceAmount')[0];
    
                $voucherLine = new VoucherLine([
                    'name' => $name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'voucher_id' => $voucher->id,
                ]);
                $voucherLine->save();
            }
    
            return $voucher;
    
        } catch (\Exception $e) {
            \Log::error("Error processing XML content: " . $e->getMessage());
            throw new \Exception("Error processing XML content.");
        }
    }


    public function updateExistingVouchersFields()
    {
        // Solo seleccionamos los registros que no tienen la información completa
        $vouchers = Voucher::whereNull('serie')->get();

        foreach ($vouchers as $voucher) {
            try {
                $xml = new SimpleXMLElement($voucher->xml_content);

                // Validamos y extraemos la serie y el número
                $idNode = $xml->xpath('//cbc:ID')[0] ?? null;
                if ($idNode) {
                    $id = (string) $idNode;
                    if (strpos($id, '-') !== false) {
                        [$serie, $numero] = explode('-', $id, 2);
                    } else {
                        $serie = '';
                        $numero = $id;
                    }
                } else {
                    \Log::warning("No se encontró el nodo cbc:ID en el XML para el voucher ID: {$voucher->id}");
                    continue; // O manejar el caso de otra manera
                }

                // Extraer y validar el tipo de documento y la moneda
                $tipoNode = $xml->xpath('//cbc:InvoiceTypeCode')[0] ?? null;
                $monedaNode = $xml->xpath('//cbc:DocumentCurrencyCode')[0] ?? null;

                $tipo = $tipoNode ? (string) $tipoNode : null;
                $moneda = $monedaNode ? (string) $monedaNode : null;

                // Actualizamos solo si todos los campos requeridos están presentes
                $voucher->update([
                    'serie' => $serie,
                    'numero' => $numero,
                    'tipo' => $tipo,
                    'moneda' => $moneda,
                ]);

            } catch (\Exception $e) {
                \Log::error("Error al procesar el XML del voucher ID: {$voucher->id}. Error: " . $e->getMessage());
                continue;
            }
        }
    }


}
