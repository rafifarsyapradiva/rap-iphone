<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Faktur Resmi #{{ $invoice['order']->order_number }} - {{ $invoice['store']['name'] }}</title>
    @include('pdf.partials.invoice-styles')
</head>
<body>
    @include('pdf.partials.invoice-body', ['invoice' => $invoice])
</body>
</html>
