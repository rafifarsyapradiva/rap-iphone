<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Notifikasi Resmi Rap-iPhone' }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #0B0C10;
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #E5E5EA;
            -webkit-font-smoothing: antialiased;
        }
        .email-wrapper {
            width: 100%;
            background-color: #0B0C10;
            padding: 36px 16px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #161B22;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }
        .email-header {
            background: linear-gradient(135deg, #0D1117 0%, #161B22 100%);
            padding: 28px 32px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            text-align: center;
        }
        .brand-logo {
            font-size: 22px;
            font-weight: 700;
            color: #FFFFFF;
            letter-spacing: -0.02em;
            text-decoration: none;
        }
        .brand-tagline {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #0071E3;
            margin-top: 4px;
        }
        .email-body {
            padding: 32px;
        }
        .badge-pill {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 14px;
        }
        .badge-blue {
            background-color: rgba(0, 113, 227, 0.18);
            color: #4DA3FF;
            border: 1px solid rgba(0, 113, 227, 0.4);
        }
        .badge-green {
            background-color: rgba(52, 199, 89, 0.18);
            color: #34C759;
            border: 1px solid rgba(52, 199, 89, 0.4);
        }
        .badge-amber {
            background-color: rgba(255, 159, 10, 0.18);
            color: #FF9F0A;
            border: 1px solid rgba(255, 159, 10, 0.4);
        }
        .hero-title {
            font-size: 24px;
            font-weight: 700;
            color: #FFFFFF;
            margin: 0 0 10px 0;
            letter-spacing: -0.02em;
        }
        .lead-text {
            font-size: 15px;
            line-height: 1.6;
            color: #A1A1A6;
            margin: 0 0 24px 0;
        }
        .glass-panel {
            background-color: #1F2631;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 22px;
        }
        .panel-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #8E8E93;
            margin: 0 0 12px 0;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table td {
            padding: 8px 0;
            font-size: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            vertical-align: top;
        }
        .data-table tr:last-child td {
            border-bottom: none;
        }
        .td-label {
            color: #8E8E93;
            width: 42%;
        }
        .td-value {
            color: #E5E5EA;
            font-weight: 600;
            text-align: right;
        }
        .total-highlight {
            font-size: 18px;
            font-weight: 700;
            color: #4DA3FF;
        }
        .cta-wrapper {
            text-align: center;
            margin: 28px 0 12px 0;
        }
        .cta-button {
            display: inline-block;
            background-color: #0071E3;
            color: #FFFFFF !important;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            padding: 13px 28px;
            border-radius: 999px;
            box-shadow: 0 6px 20px rgba(0, 113, 227, 0.35);
        }
        .email-footer {
            background-color: #0D1117;
            padding: 24px 32px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            text-align: center;
            font-size: 12px;
            color: #8E8E93;
            line-height: 1.6;
        }
        @media only screen and (max-width: 620px) {
            .email-body {
                padding: 22px 18px;
            }
            .hero-title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="email-header">
                <div class="brand-logo"> Rap-iPhone</div>
                <div class="brand-tagline">Premium Apple Store &amp; Trade-In Indonesia</div>
            </div>

            <div class="email-body">
                @yield('content')
            </div>

            <div class="email-footer">
                <strong>Rap-iPhone Official Store Indonesia</strong><br>
                Jl. Jenderal Sudirman Kav. 52-53, SCBD, Jakarta Selatan<br>
                100% Produk Original Garansi Resmi • Gratis Pengiriman &amp; Asuransi Penuh<br>
                &copy; {{ date('Y') }} Rap-iPhone. Seluruh hak cipta dilindungi undang-undang.
            </div>
        </div>
    </div>
</body>
</html>
