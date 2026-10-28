{{-- Stylesheet nota PDF (DomPDF-safe: tabel, tanpa flex/grid, font Helvetica bawaan) --}}
<style>
    @page {
        margin: 26px 32px 30px 32px;
    }
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    body {
        font-family: Helvetica, Arial, sans-serif;
        font-size: 10.5px;
        line-height: 1.45;
        color: #1D1D1F;
        background-color: #FFFFFF;
    }
    table {
        border-collapse: collapse;
    }
    .invoice {
        position: relative;
    }
    .page-break {
        page-break-after: always;
    }

    /* Watermark status non-lunas */
    .watermark {
        position: absolute;
        top: 330px;
        left: 40px;
        width: 640px;
        text-align: center;
        font-size: 92px;
        font-weight: 700;
        letter-spacing: 6px;
        color: #FF3B30;
        opacity: 0.09;
        transform: rotate(-28deg);
    }

    /* Header */
    .header {
        width: 100%;
        border-bottom: 2px solid #1D1D1F;
    }
    .header td {
        vertical-align: top;
        padding-bottom: 14px;
    }
    .brand-mark {
        display: inline-block;
        width: 34px;
        height: 34px;
        line-height: 34px;
        border-radius: 9px;
        background-color: #0B0C10;
        color: #FFFFFF;
        text-align: center;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -0.5px;
    }
    .brand-title {
        font-size: 21px;
        font-weight: 700;
        letter-spacing: -0.4px;
        color: #0B0C10;
    }
    .brand-tagline {
        font-size: 8.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.4px;
        color: #0071E3;
    }
    .store-info {
        font-size: 8.8px;
        color: #515154;
        margin-top: 7px;
        line-height: 1.5;
    }
    .doc-label {
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #86868B;
    }
    .doc-title {
        font-size: 19px;
        font-weight: 700;
        letter-spacing: -0.3px;
        color: #1D1D1F;
        margin-top: 2px;
    }
    .doc-number {
        font-family: 'Courier', monospace;
        font-size: 12px;
        font-weight: 700;
        color: #0071E3;
        margin-top: 3px;
    }
    .status-pill {
        display: inline-block;
        margin-top: 7px;
        padding: 4px 10px;
        border-radius: 10px;
        font-size: 8.5px;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
    }
    .status-paid {
        background-color: #E8F8EE;
        color: #167940;
        border: 1px solid #34C759;
    }
    .status-pending {
        background-color: #FFF6E5;
        color: #B26A00;
        border: 1px solid #FF9F0A;
    }
    .status-cancelled {
        background-color: #FDECEC;
        color: #C5221F;
        border: 1px solid #FF3B30;
    }

    /* Metadata */
    .meta {
        width: 100%;
        margin-top: 16px;
        background-color: #F5F5F7;
    }
    .meta td {
        vertical-align: top;
        padding: 12px 14px;
    }
    .meta td + td {
        border-left: 1px solid #E5E5EA;
    }
    .caption {
        font-size: 7.8px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #86868B;
        margin-bottom: 4px;
    }
    .meta-value {
        font-size: 11px;
        font-weight: 700;
        color: #1D1D1F;
    }
    .meta-sub {
        font-size: 9.2px;
        color: #515154;
        margin-top: 2px;
        line-height: 1.5;
    }
    .kv {
        width: 100%;
    }
    .kv td {
        padding: 0 0 2px 0;
        font-size: 9.2px;
        border: none !important;
    }
    .kv .k {
        color: #86868B;
        width: 44%;
    }
    .kv .v {
        color: #1D1D1F;
        font-weight: 700;
    }

    /* Items */
    .items {
        width: 100%;
        margin-top: 18px;
    }
    .items th {
        background-color: #0B0C10;
        color: #FFFFFF;
        font-size: 8px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 8px 9px;
        text-align: left;
    }
    .items td {
        padding: 10px 9px;
        border-bottom: 1px solid #E5E5EA;
        vertical-align: top;
    }
    .product-name {
        font-size: 11.5px;
        font-weight: 700;
        color: #1D1D1F;
    }
    .variant {
        font-size: 9.5px;
        color: #1D1D1F;
        margin-top: 2px;
    }
    .condition {
        font-size: 8.8px;
        color: #515154;
        margin-top: 1px;
    }
    .imei-chip {
        display: inline-block;
        margin-top: 5px;
        padding: 2px 7px;
        background-color: #EEF6FF;
        border: 1px solid #B6D8FF;
        border-radius: 4px;
        color: #0051A8;
        font-family: 'Courier', monospace;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.4px;
    }
    .imei-pending {
        background-color: #F5F5F7;
        border-color: #D2D2D7;
        color: #86868B;
    }
    .warranty-name {
        font-size: 9.3px;
        font-weight: 700;
        color: #1D1D1F;
    }
    .warranty-period {
        display: inline-block;
        margin-top: 3px;
        padding: 1px 6px;
        border-radius: 4px;
        background-color: #E8F8EE;
        color: #167940;
        font-size: 8.5px;
        font-weight: 700;
    }
    .warranty-until {
        font-size: 8.5px;
        color: #515154;
        margin-top: 3px;
    }
    .num {
        text-align: right;
        white-space: nowrap;
    }

    /* Summary */
    .summary-wrap {
        width: 100%;
        margin-top: 14px;
    }
    .summary-wrap > tbody > tr > td {
        vertical-align: top;
    }
    .words-box {
        border-left: 3px solid #0071E3;
        padding: 6px 10px;
        background-color: #FBFBFD;
    }
    .words {
        font-size: 9.5px;
        font-style: italic;
        color: #1D1D1F;
        text-transform: capitalize;
        margin-top: 2px;
    }
    .summary {
        width: 100%;
    }
    .summary td {
        padding: 4px 0;
        font-size: 10px;
    }
    .summary .label {
        color: #515154;
    }
    .summary .value {
        text-align: right;
        font-weight: 700;
    }
    .summary .discount td {
        color: #167940;
    }
    .summary .total td {
        border-top: 2px solid #1D1D1F;
        padding-top: 8px;
        font-size: 13px;
        font-weight: 700;
        color: #0071E3;
    }
    .summary .total .label {
        color: #1D1D1F;
        font-size: 10px;
        letter-spacing: 0.8px;
    }

    /* Footer: terms + verification */
    .footer {
        width: 100%;
        margin-top: 22px;
        border-top: 1px solid #D2D2D7;
    }
    .footer > tbody > tr > td {
        vertical-align: top;
        padding-top: 14px;
    }
    .terms-title {
        font-size: 8.8px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #1D1D1F;
        margin-bottom: 5px;
    }
    .terms {
        width: 100%;
    }
    .terms td {
        font-size: 8.3px;
        color: #515154;
        line-height: 1.5;
        padding-bottom: 3px;
        vertical-align: top;
    }
    .terms .n {
        width: 12px;
        font-weight: 700;
        color: #1D1D1F;
    }
    .verify-box {
        border: 1px solid #D2D2D7;
        border-radius: 8px;
        padding: 10px;
        text-align: center;
        background-color: #FBFBFD;
    }
    .verify-box img {
        width: 104px;
        height: 104px;
    }
    .verify-id {
        font-family: 'Courier', monospace;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.6px;
        color: #1D1D1F;
        margin-top: 4px;
    }
    .verify-hint {
        font-size: 7.5px;
        color: #86868B;
        margin-top: 2px;
        line-height: 1.4;
    }
    .stamp {
        display: inline-block;
        margin-top: 8px;
        padding: 5px 10px;
        border: 2px solid #0071E3;
        border-radius: 6px;
        color: #0071E3;
        text-align: center;
    }
    .stamp-title {
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 1.2px;
        text-transform: uppercase;
    }
    .stamp-sub {
        font-size: 7.3px;
        letter-spacing: 0.5px;
        margin-top: 1px;
    }
    .stamp-void {
        border-color: #C5221F;
        color: #C5221F;
    }
    .doc-footer {
        margin-top: 16px;
        padding-top: 8px;
        border-top: 1px solid #E5E5EA;
        font-size: 7.8px;
        color: #86868B;
        text-align: center;
    }
</style>

