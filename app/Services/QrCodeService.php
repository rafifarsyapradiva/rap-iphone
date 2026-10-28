<?php

namespace App\Services;

use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrCodeService
{
    /**
     * Render QR Code sebagai data URI SVG (data:image/svg+xml;base64,...).
     *
     * Output vektor SVG dirender oleh php-svg-lib bawaan DomPDF sehingga
     * tidak memerlukan ekstensi PHP GD.
     */
    public function svgDataUri(string $payload): string
    {
        $options = new QROptions([
            'outputType' => QROutputInterface::MARKUP_SVG,
            'outputBase64' => true,
            'eccLevel' => EccLevel::M,
            'addQuietzone' => true,
            'quietzoneSize' => 2,
            'drawLightModules' => false,
            'connectPaths' => true,
            'svgAddXmlHeader' => true,
        ]);

        return (new QRCode($options))->render($payload);
    }

    /**
     * Matriks modul QR (true = modul gelap) untuk fallback render tabel HTML.
     *
     * @return array<int, array<int, bool>>
     */
    public function matrix(string $payload): array
    {
        $options = new QROptions([
            'eccLevel' => EccLevel::M,
            'addQuietzone' => false,
        ]);

        $qr = new QRCode($options);
        $qr->addByteSegment($payload);

        return $qr->getQRMatrix()->getBooleanMatrix();
    }
}

