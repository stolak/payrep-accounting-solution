<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Project Purchase Order</title>
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
            min-height: 1320px;
            padding: 0;
            position: relative;
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        .top-green-strip {
            height: 22px;
            background: #73b401;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 18px 30px 16px;
            border-bottom: 4px solid #1d9dd8;
        }

        .header-left {
            width: 62%;
        }

        .logo {
            width: 340px;
            max-width: 100%;
            margin-bottom: 8px;
        }

        .company-meta {
            color: #0a6ea1;
            font-size: 18px;
            line-height: 1.35;
            letter-spacing: 0.5px;
        }

        .header-right {
            width: 34%;
            text-align: right;
            color: #333;
        }

        .header-right .ref {
            color: #71b617;
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header-right .title {
            font-size: 45px;
            line-height: 0.95;
            letter-spacing: 5px;
            font-weight: 700;
        }

        .content {
            padding: 20px 36px 36px;
        }

        .address-row {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 20px;
        }

        .address-box {
            width: 50%;
        }

        .address-box h3 {
            margin: 0 0 10px;
            font-size: 20px;
            letter-spacing: 2px;
            border-bottom: 4px solid #4d4d4d;
            color: #343434;
        }

        .address-lines {
            border-left: 4px solid #4d4d4d;
            padding-left: 6px;
            min-height: 100px;
            font-size: 20px;
            line-height: 1.25;
            color: #4a4a4a;
        }

        .address-lines strong {
            color: #222;
            font-size: 20px;
            letter-spacing: 1px;
        }

        .comments-label {
            margin-top: 6px;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #101010;
        }

        .remarks-row {
            margin-top: 6px;
        }

        .line {
            height: 20px;
            border-bottom: 3px solid #6f6f6f;
            margin-bottom: 6px;
            font-size: 24px;
            color: #3a3a3a;
            display: flex;
            margin-top: 6px;
            align-items: flex-end;
            padding-bottom: 3px;
        }

        .line span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }

        .comments-line {
            width: 100%;
            font-size: 24px;
            color: #3a3a3a;
            min-height: 32px;
            line-height: 32px;
            padding-bottom: 8px;
            position: relative;
            /* Keep original full-width writing lines */
            background-image: repeating-linear-gradient(to bottom,
                    transparent 0,
                    transparent 28px,
                    #6f6f6f 28px,
                    #6f6f6f 32px);
            background-size: 100% 32px;
            background-repeat: repeat-y;
        }

        .comments-line .comments-label-inline {
            font-weight: 700;
            letter-spacing: 1px;
            color: #101010;
            background: #fff;
            background-image: none !important;
            padding-right: 8px;
            border-bottom: none !important;
            text-decoration: none !important;
            box-shadow: none !important;
            white-space: nowrap;
            display: inline-block;
            position: relative;
            z-index: 2;
        }

        /* Mask the writing-line underline under the label only */
        .comments-line .comments-label-inline::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 8px;
            /* covers the 4px writing line + a bit of anti-alias */
            background: #fff;
            z-index: -1;
        }

        .comments-line .comments-value {
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
            /* Fallback underline (works in PDF renderers that ignore background gradients) */
            /* text-decoration: underline;
            text-decoration-color: #6f6f6f;
            text-underline-offset: 6px;
            text-decoration-skip-ink: none;*/

        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table {
            margin-top: 14px;
            margin-bottom: 20px;
        }

        .meta-table th,
        .meta-table td {
            border: 2px solid #1d9dd8;
            text-align: center;
            padding: 8px 10px;
            font-size: 20px;
        }

        .meta-table th {
            background: #9bc7dd;
            color: #111;
            font-weight: 700;
        }

        .main-table th,
        .main-table td {
            border: 2px solid #272727;
            padding: 8px 12px;
            font-size: 20px;
        }

        .main-table th {
            background: #5e5e5e;
            color: #fff;
            text-align: center;
            font-weight: 700;
        }

        .main-table td {
            height: 20px;
        }

        .main-table td:nth-child(1),
        .main-table td:nth-child(3),
        .main-table td:nth-child(4) {
            text-align: right;
        }

        .total-box {
            width: 42%;
            margin-left: auto;
            background: #e6ebed;
            margin-top: 18px;
            padding: 16px 20px;
            font-size: 20px;
            line-height: 1.35;
            color: #222;
        }

        .total-box .bold {
            font-weight: 700;
            color: #111;
        }

        .terms-page {
            padding: 40px 44px 60px;
        }

        .terms-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .terms-cols {
            column-count: 2;
            column-gap: 42px;
            font-size: 16px;
            line-height: 1.35;
        }

        .terms-cols h4 {
            margin: 10px 0 4px;
            font-size: 20px;
            break-after: avoid;
        }

        .terms-cols p,
        .terms-cols ul {
            margin: 0 0 8px;
        }

        .terms-cols ul {
            padding-left: 20px;
        }

        .sign-block {
            margin-top: 18px;
            font-size: 20px;
            break-inside: avoid-column;
            page-break-inside: avoid;
        }

        @if (!empty($pdfMode) && ($pdfRenderer ?? 'dompdf') === 'dompdf')
            body {
                background: #fff;
            }

            .document-wrap {
                width: 100%;
                margin: 0;
                box-shadow: none;
            }

            .page {
                min-height: auto;
            }

            .top-green-strip {
                height: 14px;
            }

            .header {
                display: table;
                width: 100%;
                table-layout: fixed;
            }

            .header-left,
            .header-right {
                display: table-cell;
                vertical-align: top;
            }

            .header-left {
                width: 64%;
            }

            .header-right {
                width: 36%;
                text-align: right;
            }

            .logo {
                width: 260px;
            }

            .header-right .title {
                font-size: 32px;
                letter-spacing: 2px;
            }

            .header-right .ref {
                font-size: 22px;
            }

            .company-meta {
                font-size: 13px;
                line-height: 1.25;
            }

            .content {
                padding: 14px 18px 20px;
            }

            .address-row {
                display: table;
                width: 100%;
                table-layout: fixed;
            }

            .address-box {
                display: table-cell;
                width: 50%;
                vertical-align: top;
            }

            .address-box:last-child {
                padding-left: 14px;
            }

            .address-box h3,
            .address-lines,
            .address-lines strong,
            .comments-label,
            .comments-line,
            .line,
            .meta-table th,
            .meta-table td,
            .main-table th,
            .main-table td,
            .total-box,
            .terms-title,
            .terms-cols h4,
            .terms-table h4,
            .sign-block {
                font-size: 13px;
            }

            .terms-cols {
                font-size: 12px;
                line-height: 1.25;
            }

            .terms-table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 18px 0;
                font-size: 12px;
                line-height: 1.25;
            }

            .terms-table td {
                width: 50%;
                vertical-align: top;
            }

            .terms-table h4 {
                margin: 8px 0 4px;
            }

            .terms-table p,
            .terms-table ul {
                margin: 0 0 6px;
            }

            .terms-table ul {
                padding-left: 16px;
            }

            .line {
                height: 14px;
                border-bottom: 2px solid #6f6f6f;
                margin-bottom: 4px;
                margin-top: 4px;
                padding-bottom: 2px;
            }

            .comments-line {
                min-height: 20px;
                line-height: 20px;
                padding-bottom: 2px;
                background-image: repeating-linear-gradient(to bottom,
                        transparent 0,
                        transparent 17px,
                        #6f6f6f 17px,
                        #6f6f6f 20px);
                background-size: 100% 20px;
                background-repeat: repeat-y;
            }

            .remarks-row {
                width: 100%;
            }

            .comments-line .comments-value {
                text-decoration: underline;
                text-underline-offset: 3px;
                text-decoration-skip-ink: none;
            }

            .meta-table th,
            .meta-table td,
            .main-table th,
            .main-table td {
                padding: 5px 6px;
            }

            .total-box {
                padding: 10px 12px;
                margin-top: 12px;
            }
        @endif
    </style>
</head>

<body>
    <div class="document-wrap">
        <section class="page">
            <div class="top-green-strip"></div>
            <div class="header">
                <div class="header-left">
                    <img src="{{ $logoDataUri ?? (!empty($pdfMode) ? $logoPath ?? public_path('assets/img/logo.jpeg') : asset('assets/img/logo.jpeg')) }}"
                        alt="Company Logo" class="logo">
                    <div class="company-meta">
                        {{ env('Coy_Address', 'Plot 1a Remi Olowude Street, Lekki Phase 1, Lagos.') }}<br>
                        {{ env('Coy_Phone', '+234 (0) 802 222 4832') }} |
                        {{ env('Coy_Email', 'info@mcemtolconsulting.com') }}<br>
                        {{ env('Coy_Website', 'https://www.mcemtolconsulting.com') }}
                    </div>
                </div>
                <div class="header-right">
                    <br> <br>
                    <div class="title">PURCHASE<br>ORDER</div>
                </div>
            </div>

            <div class="content">
                <div class="address-row">
                    <div class="address-box">
                        <h3>ORDER TO</h3>
                        <div class="address-lines">
                            Attention:
                            {{ $vendorInfo['attention'] }}<br>
                            <strong>{{ strtoupper($vendorInfo['name']) }}</strong><br>
                            {{ $vendorInfo['address1'] }}<br>

                            {{ $vendorInfo['email'] }}<br>
                            {{ $vendorInfo['phone'] }}
                        </div>
                    </div>
                    <div class="address-box">
                        <h3>SHIP TO</h3>
                        <div class="address-lines">
                            Attention: {{ $shipTo['attention'] }}<br>
                            <strong>{{ strtoupper($shipTo['name']) }}</strong><br>
                            {{ $shipTo['address1'] }}<br>

                            {{ $shipTo['email'] }}<br>
                            {{ $shipTo['phone'] }}
                        </div>
                    </div>
                </div>

                <div class="remarks-row">
                    <div class="comments-line">
                        <span class="comments-label-inline">Comments or Special Instructions:</span>
                        <span class="comments-value">{{ $comments }}</span>
                    </div>
                </div>


                <table class="meta-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Purchase Order #</th>
                            <th>Vendor ID.</th>
                            <th>Complete By:</th>
                            <th>Terms</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $poDate }}</td>
                            <td>{{ $poNumber }}</td>
                            <td>{{ $vendorRef }}</td>
                            <td>{{ $completeBy }}</td>
                            <td>{{ $termsLabel }}</td>
                        </tr>
                    </tbody>
                </table>

                <table class="main-table">
                    <thead>
                        <tr>
                            <th style="width: 11%;">QTY</th>
                            <th style="width: 47%;">DESCRIPTION</th>
                            <th style="width: 19%;">UNIT PRICE<br>(NGN)</th>
                            <th style="width: 23%;">AMOUNT<br>(NGN)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lineItems as $item)
                            <tr>
                                <td>{{ number_format($item['qty'], 2, '.', ',') }}</td>
                                <td>{{ $item['description'] }}</td>
                                <td>{{ number_format($item['unitPrice'], 2, '.', ',') }}</td>
                                <td>{{ number_format($item['amount'], 2, '.', ',') }}</td>
                            </tr>
                        @endforeach
                        @for ($i = count($lineItems); $i < 2; $i++)
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>

                @php
                    $poCurrency = !empty($pdfMode) ? 'NGN' : '₦';
                @endphp
                <div class="total-box">
                    <div>Subtotal: &nbsp; {{ $poCurrency }} {{ number_format($subtotal, 2, '.', ',') }}</div>
                    <div>VAT ({{ number_format($vatPercent, 1) }}%): &nbsp; {{ $poCurrency }}
                        {{ number_format($vatAmount, 2, '.', ',') }}
                    </div>
                    <div class="bold">TOTAL: {{ $poCurrency }} {{ number_format($total, 2, '.', ',') }}</div>
                </div>
            </div>
        </section>

        <section class="page">
            <div class="terms-page">
                <div class="terms-title">PURCHASE ORDER TERMS &amp; CONDITIONS</div>
                @php
                    $hasDynamicTerms = !empty($poTerms) && count($poTerms) > 0;
                @endphp

                @if (!empty($pdfMode) && ($pdfRenderer ?? 'dompdf') === 'dompdf')
                    @if ($hasDynamicTerms)
                        @php
                            $termsCollection = collect($poTerms);
                            $half = (int) ceil($termsCollection->count() / 2);
                            $leftTerms = $termsCollection->slice(0, $half);
                            $rightTerms = $termsCollection->slice($half);
                        @endphp
                        <table class="terms-table">
                            <tr>
                                <td>
                                    @foreach ($leftTerms as $term)
                                        <h4>{{ $term->title }}</h4>
                                        {!! $term->body !!}
                                    @endforeach
                                    <div class="sign-block">
                                        <strong>Accepted for Seller</strong><br>
                                        Name: {{ $vendorInfo['attention'] }}<br>
                                        Date: {{ $poDate }}
                                    </div>
                                </td>
                                <td>
                                    @foreach ($rightTerms as $term)
                                        <h4>{{ $term->title }}</h4>
                                        {!! $term->body !!}
                                    @endforeach
                                </td>
                            </tr>
                        </table>
                    @else
                        <table class="terms-table">
                            <tr>
                                <td>
                                    <p><strong>{{ env('Coy_Name', 'McEmtol Consulting Company (MCC)') }}</strong></p>
                                    <p>These Purchase Order Terms and Conditions govern all purchase orders issued by
                                        Purchaser to Seller for goods and/or services described in this Order.</p>

                                    <h4>1. Acceptance &amp; Contract Formation</h4>
                                    <p>Seller accepts this Order and these Terms by signing the Order or by commencing
                                        performance. These Terms and the Order form the entire agreement.</p>

                                    <h4>2. Scope, Delivery &amp; Default</h4>
                                    <p>Time is of the essence. Seller shall deliver goods and/or perform services
                                        strictly
                                        in line with this Order schedule.</p>
                                    <ul>
                                        <li>corrective action for delay;</li>
                                        <li>delivery extension with price adjustment;</li>
                                        <li>procurement of substitutes at Seller's cost; or</li>
                                        <li>termination of part/all of the Order.</li>
                                    </ul>

                                    <h4>3. Price &amp; Taxes</h4>
                                    <p>Prices shall not exceed the Order values unless approved in writing. Applicable
                                        taxes and duties remain Seller's responsibility except where law states
                                        otherwise.
                                    </p>

                                    <h4>4. Invoicing &amp; Payment</h4>
                                    <p>Invoices must reference this Purchase Order and contain complete item
                                        descriptions,
                                        quantities, and prices. Payment term is <strong>30 days</strong> from receipt of
                                        valid invoice unless otherwise agreed.</p>

                                    <h4>5. Packaging, Shipping &amp; Risk</h4>
                                    <p>Goods must be properly packaged and shipped. Title and risk pass upon delivery
                                        and
                                        acceptance.</p>

                                    <h4>6. Inspection &amp; Acceptance</h4>
                                    <p>Goods/services are subject to inspection and acceptance. Defective or
                                        non-conforming
                                        supply may be rejected or replaced at Seller's cost.</p>

                                    <h4>7. Warranties</h4>
                                    <p>Seller warrants goods/services meet specification, are fit for purpose, and are
                                        free from defects.</p>

                                    <h4>8. Indemnity</h4>
                                    <p>Seller indemnifies Purchaser against claims, losses, damages, costs, and expenses
                                        resulting from Seller's acts, omissions, or IP infringements.</p>

                                    <h4>9. Limitation of Liability</h4>
                                    <p>Purchaser liability shall not exceed amount paid for affected goods/services and
                                        excludes indirect or punitive damages.</p>

                                    <h4>10. Purchaser Property</h4>
                                    <p>Materials, data, or equipment provided by Purchaser remain Purchaser property and
                                        must be returned on request.</p>
                                </td>
                                <td>
                                    <h4>11. Changes</h4>
                                    <p>Purchaser may modify quantity/specification/delivery in writing. Seller continues
                                        performance pending resolution.</p>

                                    <h4>12. Legal &amp; Regulatory Compliance</h4>
                                    <p>Seller complies with all applicable Nigerian laws, permits, and industry
                                        standards.
                                    </p>

                                    <h4>13. Confidentiality</h4>
                                    <p>Seller shall keep Purchaser information confidential and use it only for this
                                        Order.
                                    </p>

                                    <h4>14. Work at Purchaser or Client Sites</h4>
                                    <p>Seller complies with all site safety, security, and operational rules.</p>

                                    <h4>15. Insurance</h4>
                                    <p>Seller maintains adequate insurance, including general liability and workers
                                        compensation.</p>

                                    <h4>16. Termination</h4>
                                    <p>Purchaser may terminate for convenience or default; payment applies only to
                                        satisfactory deliveries made before termination.</p>

                                    <h4>17. Assignment</h4>
                                    <p>Seller may not assign/subcontract this Order without Purchaser's written
                                        approval.
                                    </p>

                                    <h4>18. Force Majeure</h4>
                                    <p>Neither party is liable for delays due to events beyond reasonable control with
                                        prompt notice.</p>

                                    <h4>19. Governing Law &amp; Dispute Resolution</h4>
                                    <p>This Order is governed by the laws of the Federal Republic of Nigeria. Disputes
                                        unresolved by negotiation will be referred to binding arbitration in Nigeria.
                                    </p>

                                    <h4>20. Miscellaneous</h4>
                                    <p>Severability, waiver, and entire agreement provisions apply. Notices shall be
                                        sent
                                        to the addresses in this Order.</p>

                                    <div class="sign-block">
                                        <strong>Accepted for Seller</strong><br>
                                        Name: {{ $vendorInfo['attention'] }}<br>

                                        Date: {{ $poDate }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    @endif
                @else
                    @if ($hasDynamicTerms)
                        <div class="terms-cols">
                            @foreach ($poTerms as $term)
                                <h4>{{ $term->title }}</h4>
                                {!! $term->body !!}
                            @endforeach
                            <div class="sign-block">
                                <strong>Accepted for Seller</strong><br>
                                Name: {{ $vendorInfo['attention'] }}<br>
                                Date: {{ $poDate }}
                            </div>
                        </div>
                    @else
                        <div class="terms-cols">
                            <p><strong>{{ env('Coy_Name', 'McEmtol Consulting Company (MCC)') }}</strong></p>
                            <p>These Purchase Order Terms and Conditions govern all purchase orders issued by Purchaser
                                to
                                Seller for goods and/or services described in this Order.</p>

                            <h4>1. Acceptance &amp; Contract Formation</h4>
                            <p>Seller accepts this Order and these Terms by signing the Order or by commencing
                                performance.
                                These Terms and the Order form the entire agreement.</p>

                            <h4>2. Scope, Delivery &amp; Default</h4>
                            <p>Time is of the essence. Seller shall deliver goods and/or perform services strictly in
                                line
                                with this Order schedule.</p>
                            <ul>
                                <li>corrective action for delay;</li>
                                <li>delivery extension with price adjustment;</li>
                                <li>procurement of substitutes at Seller's cost; or</li>
                                <li>termination of part/all of the Order.</li>
                            </ul>

                            <h4>3. Price &amp; Taxes</h4>
                            <p>Prices shall not exceed the Order values unless approved in writing. Applicable taxes and
                                duties remain Seller's responsibility except where law states otherwise.</p>

                            <h4>4. Invoicing &amp; Payment</h4>
                            <p>Invoices must reference this Purchase Order and contain complete item descriptions,
                                quantities, and prices. Payment term is <strong>30 days</strong> from receipt of valid
                                invoice unless otherwise agreed.</p>

                            <h4>5. Packaging, Shipping &amp; Risk</h4>
                            <p>Goods must be properly packaged and shipped. Title and risk pass upon delivery and
                                acceptance.</p>

                            <h4>6. Inspection &amp; Acceptance</h4>
                            <p>Goods/services are subject to inspection and acceptance. Defective or non-conforming
                                supply
                                may be rejected or replaced at Seller's cost.</p>

                            <h4>7. Warranties</h4>
                            <p>Seller warrants goods/services meet specification, are fit for purpose, and are free from
                                defects.</p>

                            <h4>8. Indemnity</h4>
                            <p>Seller indemnifies Purchaser against claims, losses, damages, costs, and expenses
                                resulting
                                from Seller's acts, omissions, or IP infringements.</p>

                            <h4>9. Limitation of Liability</h4>
                            <p>Purchaser liability shall not exceed amount paid for affected goods/services and excludes
                                indirect or punitive damages.</p>

                            <h4>10. Purchaser Property</h4>
                            <p>Materials, data, or equipment provided by Purchaser remain Purchaser property and must be
                                returned on request.</p>

                            <h4>11. Changes</h4>
                            <p>Purchaser may modify quantity/specification/delivery in writing. Seller continues
                                performance pending resolution.</p>

                            <h4>12. Legal &amp; Regulatory Compliance</h4>
                            <p>Seller complies with all applicable Nigerian laws, permits, and industry standards.</p>

                            <h4>13. Confidentiality</h4>
                            <p>Seller shall keep Purchaser information confidential and use it only for this Order.</p>

                            <h4>14. Work at Purchaser or Client Sites</h4>
                            <p>Seller complies with all site safety, security, and operational rules.</p>

                            <h4>15. Insurance</h4>
                            <p>Seller maintains adequate insurance, including general liability and workers
                                compensation.
                            </p>

                            <h4>16. Termination</h4>
                            <p>Purchaser may terminate for convenience or default; payment applies only to satisfactory
                                deliveries made before termination.</p>

                            <h4>17. Assignment</h4>
                            <p>Seller may not assign/subcontract this Order without Purchaser's written approval.</p>

                            <h4>18. Force Majeure</h4>
                            <p>Neither party is liable for delays due to events beyond reasonable control with prompt
                                notice.</p>

                            <h4>19. Governing Law &amp; Dispute Resolution</h4>
                            <p>This Order is governed by the laws of the Federal Republic of Nigeria. Disputes
                                unresolved
                                by negotiation will be referred to binding arbitration in Nigeria.</p>

                            <h4>20. Miscellaneous</h4>
                            <p>Severability, waiver, and entire agreement provisions apply. Notices shall be sent to the
                                addresses in this Order.</p>

                            <div class="sign-block">
                                <strong>Accepted for Seller</strong><br>
                                Name: {{ $vendorInfo['attention'] }}<br>


                                Date: {{ $poDate }}
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </section>
    </div>
</body>

</html>
