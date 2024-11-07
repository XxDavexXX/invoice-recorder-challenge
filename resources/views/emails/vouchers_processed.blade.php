<!DOCTYPE html>
<html>
<head>
    <title>Resultados de la Carga de Comprobantes</title>
</head>
<body>
    <h1>Estimado {{ $user->name }},</h1>
    <p>Aquí tienes el resumen de la carga de tus comprobantes:</p>
    
    <h2>Comprobantes Registrados Correctamente</h2>
    @if(count($successfulVouchers) > 0)
        <ul>
            @foreach($successfulVouchers as $voucher)
                <li>{{ $voucher->issuer_name }} - Serie: {{ $voucher->serie }} - Número: {{ $voucher->numero }}</li>
            @endforeach
        </ul>
    @else
        <p>No hubo comprobantes registrados exitosamente.</p>
    @endif

    <h2>Comprobantes con Errores</h2>
    @if(count($failedVouchers) > 0)
        <ul>
            @foreach($failedVouchers as $failed)
                <li>Razón: {{ $failed['reason'] }}</li>
            @endforeach
        </ul>
    @else
        <p>No hubo errores en la carga de comprobantes.</p>
    @endif

    <p>Gracias por usar nuestro servicio.</p>
</body>
</html>
