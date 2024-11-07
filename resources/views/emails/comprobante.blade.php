<!DOCTYPE html>
<html>
<head>
    <title>Comprobantes Subidos</title>
</head>
<body>
    <h1>Estimado {{ $user->name }},</h1>
    <p>Hemos recibido tus comprobantes con los siguientes detalles:</p>
    @foreach ($comprobantes as $comprobante)
    <ul>
        <li><strong>Serie:</strong> {{ $comprobante->serie }}</li>
        <li><strong>Número:</strong> {{ $comprobante->numero }}</li>
        <li><strong>Tipo de Comprobante:</strong> {{ $comprobante->tipo }}</li>
        <li><strong>Moneda:</strong> {{ $comprobante->moneda }}</li>
        
        <li><strong>Nombre del Emisor:</strong> {{ $comprobante->issuer_name }}</li>
        <li><strong>Tipo de Documento del Emisor:</strong> {{ $comprobante->issuer_document_type }}</li>
        <li><strong>Número de Documento del Emisor:</strong> {{ $comprobante->issuer_document_number }}</li>
        
        <li><strong>Nombre del Receptor:</strong> {{ $comprobante->receiver_name }}</li>
        <li><strong>Tipo de Documento del Receptor:</strong> {{ $comprobante->receiver_document_type }}</li>
        <li><strong>Número de Documento del Receptor:</strong> {{ $comprobante->receiver_document_number }}</li>
        
        <li><strong>Monto Total:</strong> {{ $comprobante->total_amount }}</li>
    </ul>
    @endforeach
    <p>¡Gracias por usar nuestro servicio!</p>
</body>
</html>
