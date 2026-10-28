<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Massal Nota Faktur - Rap-iPhone</title>
    @include('pdf.partials.invoice-styles')
</head>
<body>
    @foreach($invoices as $invoice)
        <div class="{{ $loop->last ? '' : 'page-break' }}">
            @include('pdf.partials.invoice-body', ['invoice' => $invoice])
        </div>
    @endforeach
</body>
</html>

