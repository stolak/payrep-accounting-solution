<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoiceNo }}</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f3f3f3;
            color: #1d1d1d;
        }

        .document-wrap {
            width: 1020px;
            margin: 20px auto 40px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .page {
            padding: 0;
        }

        .top-blue-strip {
            height: 26px;
            background: #0a6ea1;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 18px 30px 12px;
            border-bottom: 6px solid #73b401;
        }

        .header-left {
            width: 55%;
        }

        .logo {
            width: 340px;
            max-width: 100%;
            margin-bottom: 10px;
        }

        .company-meta {
            color: #0a6ea1;
            font-size: 16px;
            line-height: 1.35;
            letter-spacing: 0.4px;
        }

        .header-right {
            width: 40%;
            text-align: right;
        }

        .header-right .title {
            font-size: 46px;
            letter-spacing: 7px;
            font-weight: 700;
            margin-top: 12px;
        }

        .content {
            padding: 22px 36px 30px;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            gap: 30px;
            margin-bottom: 18px;
        }

        .meta-left {
            width: 56%;
        }

        .meta-right {
            width: 40%;
        }

        .meta-left .invoice-no {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 2px;
            margin: 0 0 12px 0;
        }

        .kv {
            width: 100%;
            border-collapse: collapse;
        }

        .kv td {
            padding: 6px 0;
            font-size: 16px;
            letter-spacing: 1px;
        }

        .kv td.key {
            width: 210px;
            color: #2a2a2a;
            font-weight: 700;
        }

        .kv td.sep {
            width: 18px;
            text-align: center;
        }

        .billto-title {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 4px;
            margin: 0 0 10px;
        }

        .billto-box {
            font-size: 16px;
            line-height: 1.45;
            color: #2a2a2a;
        }

        .billto-box strong {
            font-size: 18px;
            letter-spacing: 1px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        .items-table th,
        .items-table td {
            padding: 12px 12px;
            font-size: 16px;
        }

        .items-table thead th {
            background: #73b401;
            color: #fff;
            letter-spacing: 2px;
            text-align: left;
        }

        .items-table thead th:nth-child(1) {
            width: 70px;
            text-align: center;
        }

        .items-table thead th:nth-child(3),
        .items-table thead th:nth-child(4),
        .items-table thead th:nth-child(5) {
            text-align: right;
        }

        .items-table tbody tr:nth-child(odd) td {
            background: #f5f7f8;
        }

        .items-table tbody td:nth-child(1) {
            text-align: center;
        }

        .items-table tbody td:nth-child(3),
        .items-table tbody td:nth-child(4),
        .items-table tbody td:nth-child(5) {
            text-align: right;
        }

        .bottom-row {
            display: flex;
            justify-content: space-between;
            gap: 28px;
            margin-top: 24px;
        }

        .payment-box,
        .summary-box {
            background: #eef2f4;
            padding: 18px 18px;
        }

        .payment-box {
            width: 54%;
        }

        .summary-box {
            width: 40%;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 3px;
            margin: 0 0 10px;
        }

        .payment-kv td {
            padding: 4px 0;
            font-size: 15px;
        }

        .summary-kv {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-kv td {
            padding: 7px 0;
            font-size: 16px;
            letter-spacing: 1px;
        }

        .summary-kv td:last-child {
            text-align: right;
            font-weight: 700;
        }

        .total-due {
            margin-top: 12px;
            background: #73b401;
            color: #fff;
            padding: 12px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .terms {
            margin-top: 18px;
        }

        .terms p {
            margin: 0;
            color: #3a3a3a;
            font-size: 14px;
            line-height: 1.35;
        }

        .footer {
            text-align: center;
            padding: 26px 36px 34px;
        }

        .footer .thanks {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 4px;
            margin-bottom: 8px;
        }

        .footer .contact {
            font-size: 13px;
            color: #444;
            margin-bottom: 14px;
        }

        .footer-bar {
            background: #0a6ea1;
            color: #fff;
            padding: 14px 18px;
            border-radius: 28px;
            display: inline-block;
            font-size: 14px;
            letter-spacing: 0.8px;
        }

        @media print {
            body {
                background: #fff;
            }

            .document-wrap {
                width: 100%;
                margin: 0;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="document-wrap">
        <section class="page">
            <div class="top-blue-strip"></div>
            <div class="header">
                <div class="header-left">
                    <img src="{{ $company['logo'] }}" alt="Company Logo" class="logo">
                    <div class="company-meta">
                        {{ $company['address'] }}<br>
                        {{ $company['phone'] }} | {{ $company['email'] }}<br>
                        {{ $company['website'] }}
                    </div>
                </div>
                <div class="header-right">
                    <div class="title">INVOICE</div>
                </div>
            </div>

            <div class="content">
                <div class="meta-row">
                    <div class="meta-left">
                        <div class="invoice-no">INVOICE # &nbsp; {{ $invoiceNo }}</div>
                        <table class="kv">
                            <tr>
                                <td class="key">INVOICE DATE</td>
                                <td class="sep">:</td>
                                <td>{{ $invoiceDate }}</td>
                            </tr>
                            <tr>
                                <td class="key">DUE DATE</td>
                                <td class="sep">:</td>
                                <td>{{ $dueDate ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="key">PURCHASE ORDER #</td>
                                <td class="sep">:</td>
                                <td>{{ $purchaseOrderNo }}</td>
                            </tr>
                            <tr>
                                <td class="key">PARTNER CODE</td>
                                <td class="sep">:</td>
                                <td>{{ $partnerCode }}</td>
                            </tr>
                            <tr>
                                <td class="key">TAX ID</td>
                                <td class="sep">:</td>
                                <td>{{ $taxId }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="meta-right">
                        <div class="billto-title">BILL TO</div>
                        <div class="billto-box">
                            <strong>{{ $billTo['name'] }}</strong><br>
                            {{ $billTo['address'] }}<br>
                            @if (!empty($billTo['email']))
                                {{ $billTo['email'] }}<br>
                            @endif
                            @if (!empty($billTo['phone']))
                                {{ $billTo['phone'] }}
                            @endif
                        </div>
                    </div>
                </div>

                <table class="items-table">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>DESCRIPTION</th>
                            <th>PRICE</th>
                            <th>QTY</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currency = 'NGN';
                            $lineItems = $items ?? collect();
                        @endphp
                        @foreach ($lineItems as $idx => $item)
                            @php
                                $qty = (float) ($item->quantity ?? 0);
                                $price = (float) ($item->price ?? 0);
                                $total = (float) ($item->subtotal ?? $qty * $price);
                            @endphp
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>{{ $item->description }}</td>
                                <td>{{ number_format($price, 2, '.', ',') }}</td>
                                <td>{{ number_format($qty, 2, '.', ',') }}</td>
                                <td>{{ number_format($total, 2, '.', ',') }}</td>
                            </tr>
                        @endforeach
                        @for ($i = $lineItems->count(); $i < 5; $i++)
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>

                <div class="bottom-row">
                    <div class="payment-box">
                        <div class="section-title">PAYMENT METHOD</div>
                        <table class="payment-kv">
                            <tr>
                                <td style="width:160px;">Bank</td>
                                <td style="width:16px;">:</td>
                                <td>{{ $payment['bank'] }}</td>
                            </tr>
                            <tr>
                                <td>Account Name</td>
                                <td>:</td>
                                <td>{{ $payment['accountName'] }}</td>
                            </tr>
                            <tr>
                                <td>Account Number</td>
                                <td>:</td>
                                <td>{{ $payment['accountNumber'] }}</td>
                            </tr>
                            <tr>
                                <td>Sort Code</td>
                                <td>:</td>
                                <td>{{ $payment['sortCode'] }}</td>
                            </tr>
                        </table>

                        <div class="terms">
                            <div class="section-title" style="margin-top:16px;">TERM AND CONDITIONS</div>
                            <p>Please make the payment by the due date to the indicated account details on this invoice.
                            </p>
                        </div>
                    </div>

                    <div class="summary-box">
                        <table class="summary-kv">
                            <tr>
                                <td>SUB-TOTAL</td>
                                <td>{{ $currency }} {{ number_format($subTotal, 2, '.', ',') }}</td>
                            </tr>
                            <tr>
                                <td>VAT ({{ number_format($vatPercent, 1) }}%)</td>
                                <td>{{ $currency }} {{ number_format($vatAmount, 2, '.', ',') }}</td>
                            </tr>
                            <tr>
                                <td>WHT ({{ number_format($whtPercent, 1) }}%)</td>
                                <td>{{ $currency }} {{ number_format($whtAmount, 2, '.', ',') }}</td>
                            </tr>
                        </table>
                        <div class="total-due">
                            <div>Total Due</div>
                            <div>{{ $currency }} {{ number_format($totalDue, 2, '.', ',') }}</div>
                        </div>
                    </div>
                </div>


            </div>

            <div class="footer">
                <div class="thanks">THANK YOU FOR YOUR BUSINESS</div>
                <div class="contact">
                    Please contact {{ $company['email'] }} for any concerns regarding this invoice.
                </div>
                <div class="footer-bar">
                    {{ $company['address'] }} | {{ $company['email'] }} | {{ $company['phone'] }} |
                    {{ $company['website'] }}
                </div>
            </div>
        </section>
    </div>
</body>

</html>
