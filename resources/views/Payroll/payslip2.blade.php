<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    payslip
@endsection
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">payslip</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">payslip</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            <!-- include notoifcation -->
            @include('_partialView.nofication')
            <!-- /include notoifcation -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">payslip</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" name="mainform" id="mainform">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Year</label>
                                            <select class="form-control" name="year" onchange="Reload()">
                                                <option value="">--Select--</option>
                                                <?php $curyr = date('Y'); ?>
                                                @for ($i = 2017; $i <= $curyr + 1; $i++)
                                                    <option value="{{ $i }}"
                                                        {{ old('year') == $i || $year == $i ? 'selected' : '' }}>
                                                        {{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Month</label>
                                            <select class="form-control" name="month" onchange="Reload()">
                                                <option value="">--Select--</option>
                                                @foreach ($Months as $list)
                                                    <option value="{{ $list->id }}"
                                                        {{ old('month') == $list->id || $month == $list->id ? 'selected' : '' }}>
                                                        {{ $list->month }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Staff</label>
                                            <select class="form-control" name="staffid" onchange="Reload()">
                                                <option value="">--Select--</option>
                                                @foreach ($Staffs as $list)
                                                    <option value="{{ $list->id }}"
                                                        {{ old('staffid') == $list->id || $staffid == $list->id ? 'selected' : '' }}>
                                                        {{ $list->first_name }} {{ $list->middle_name }}
                                                        {{ $list->last_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><br></label>
                                            <br>
                                            <button class="btn btn-primary" type="submit" name="view">View</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="payslip-row">
                <div class="col-md-12">

                    <!-- Payroll Report -->
                    <div class="card card-table" id="payslip-card">
                        <div class="card-header">
                            <h4 class="card-title">Payslip</h4>
                            @if ($Payroll)
                                <button type="button" class="btn btn-primary btn-sm float-right" onclick="printPayslip()">
                                    <i class="fe fe-printer"></i> Print
                                </button>
                            @endif
                        </div>
                        <div class="card-body">
                            @if ($Payroll)
                                @php
                                    $monthName = '';
                                    foreach ($Months as $m) {
                                        if ($m->id == $Payroll->month) {
                                            $monthName = $m->month;
                                            break;
                                        }
                                    }
                                    $totalEarnings = 0;
                                    $totalDeductions = 0;
                                    // Employer contributions are mapped from tblpayroll_payment using variable_contribution_setup_map.tb_code
                                    $employerContributions = $employerContributions ?? [];
                                    $totalEmployerContributions = $totalEmployerContributions ?? 0;

                                    // Filter earnings with non-zero values
                                    $filteredEarnings = [];
                                    foreach ($EarningVariable as $earning) {
                                        $para = $earning->ref_code;
                                        $amount = $Payroll->$para ?? 0;
                                        if ($amount != 0) {
                                            $filteredEarnings[] = (object) [
                                                'variable' => $earning->variable,
                                                'ref_code' => $para,
                                                'amount' => $amount,
                                            ];
                                            $totalEarnings += $amount;
                                        }
                                    }
                                    foreach ($NonTaxableEarning as $earning) {
                                        $para = $earning->ref_code;
                                        $amount = $Payroll->$para ?? 0;
                                        if ($amount != 0) {
                                            $filteredEarnings[] = (object) [
                                                'variable' => $earning->variable,
                                                'ref_code' => $para,
                                                'amount' => $amount,
                                            ];
                                            $totalEarnings += $amount;
                                        }
                                    }

                                    // Filter deductions with non-zero values
                                    $filteredDeductions = [];
                                    foreach ($DeductionVariable as $deduction) {
                                        $para = $deduction->ref_code;
                                        $amount = abs($Payroll->$para ?? 0);
                                        if ($amount != 0) {
                                            $filteredDeductions[] = (object) [
                                                'variable' => $deduction->variable,
                                                'ref_code' => $para,
                                                'amount' => $amount,
                                            ];
                                            $totalDeductions += $amount;
                                        }
                                    }

                                @endphp
                                <style>
                                    .payslip-container {
                                        max-width: 980px;
                                        margin: 0 auto;
                                        background: white;
                                        padding: 0;
                                        font-family: Arial, Helvetica, sans-serif;
                                        color: #1d1d1d;
                                    }

                                    .payslip-header-img img,
                                    .payslip-footer-img img {
                                        width: 100%;
                                        height: auto;
                                        display: block;
                                        -webkit-print-color-adjust: exact;
                                        print-color-adjust: exact;
                                    }

                                    .payslip-body {
                                        padding: 22px 34px 18px;
                                    }

                                    .payslip-title {
                                        text-align: center;
                                        font-size: 30px;
                                        font-weight: 800;
                                        color: #0a6ea1;
                                        margin: 10px 0 18px;
                                        letter-spacing: 1px;
                                    }

                                    .meta-grid {
                                        display: grid;
                                        grid-template-columns: 1fr 1fr;
                                        column-gap: 40px;
                                        row-gap: 10px;
                                        margin-bottom: 22px;
                                    }

                                    .meta-col .meta-row {
                                        display: grid;
                                        grid-template-columns: 170px 14px 1fr;
                                        align-items: baseline;
                                        font-size: 17px;
                                        line-height: 1.35;
                                    }

                                    .meta-label {
                                        font-weight: 700;
                                        color: #111;
                                    }

                                    .meta-sep {
                                        text-align: center;
                                        font-weight: 700;
                                    }

                                    .meta-value {
                                        font-weight: 500;
                                        color: #222;
                                    }

                                    .two-col {
                                        display: grid;
                                        grid-template-columns: 1fr 1fr;
                                        gap: 28px;
                                        margin-top: 12px;
                                        margin-bottom: 22px;
                                    }

                                    .section-box {
                                        background: #f1f3f4;
                                        padding: 18px 18px 14px;
                                        min-height: 280px;
                                    }

                                    .section-title {
                                        font-size: 26px;
                                        font-weight: 800;
                                        margin: 0 0 12px;
                                        color: #111;
                                    }

                                    .line-item {
                                        display: grid;
                                        grid-template-columns: 1fr 14px 160px;
                                        gap: 10px;
                                        padding: 4px 0;
                                        font-size: 18px;
                                    }

                                    .line-item .amt {
                                        text-align: right;
                                        font-weight: 700;
                                    }

                                    .line-item.empty {
                                        color: #777;
                                        font-style: italic;
                                    }

                                    .total-row {
                                        margin-top: 10px;
                                        padding-top: 10px;
                                        border-top: 3px solid #cfe0b4;
                                        display: grid;
                                        grid-template-columns: 1fr 14px 160px;
                                        gap: 10px;
                                        align-items: baseline;
                                        font-size: 22px;
                                        font-weight: 800;
                                    }

                                    .employer-wrap {
                                        background: #f6f6f1;
                                        padding: 18px 18px 14px;
                                        margin-top: 10px;
                                    }

                                    .employer-title {
                                        font-size: 22px;
                                        font-weight: 800;
                                        margin: 0 0 10px;
                                    }

                                    .employer-divider {
                                        border-top: 3px solid #0a6ea1;
                                        margin: 12px 0 10px;
                                    }

                                    .netpay-row {
                                        margin-top: 18px;
                                        background: #cfe5f0;
                                        padding: 18px 18px;
                                        display: grid;
                                        grid-template-columns: 1fr 240px;
                                        align-items: center;
                                        column-gap: 10px;
                                    }

                                    .netpay-label {
                                        font-size: 26px;
                                        font-weight: 900;
                                        letter-spacing: 1px;
                                    }

                                    .netpay-amount {
                                        font-size: 28px;
                                        font-weight: 900;
                                        text-align: right;
                                    }

                                    .payslip-note {
                                        padding: 26px 18px 18px;
                                        text-align: center;
                                        font-size: 15px;
                                        color: #333;
                                    }

                                    @media print {
                                        .payslip-container {
                                            max-width: 100% !important;
                                            margin: 0 !important;
                                        }

                                        .payslip-body {
                                            padding-left: 28px !important;
                                            padding-right: 28px !important;
                                        }
                                    }
                                </style>
                                <div id="payslip-content" class="payslip-container">
                                    <div class="payslip-header-img">
                                        <img src="{{ asset('img/payslip_header.png') }}" alt="Payslip Header">
                                    </div>

                                    <div class="payslip-body">


                                        <div class="meta-grid">
                                            <div class="meta-col">
                                                <div class="meta-row">
                                                    <div class="meta-label">Employee Name</div>
                                                    <div class="meta-sep">:</div>
                                                    <div class="meta-value">{{ $Payroll->fullname ?? 'N/A' }}</div>
                                                </div>
                                                <div class="meta-row">
                                                    <div class="meta-label">Designation</div>
                                                    <div class="meta-sep">:</div>
                                                    <div class="meta-value">
                                                        {{ $Payroll->designation ?? ($Payroll->grades ?? 'N/A') }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="meta-col">
                                                <div class="meta-row">
                                                    <div class="meta-label">Employee ID</div>
                                                    <div class="meta-sep">:</div>
                                                    <div class="meta-value">{{ $Payroll->staff_no ?? 'N/A' }}</div>
                                                </div>
                                                <div class="meta-row">
                                                    <div class="meta-label">Pay Period</div>
                                                    <div class="meta-sep">:</div>
                                                    <div class="meta-value">{{ $monthName }} {{ $Payroll->year ?? '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="two-col">
                                            <div class="section-box">
                                                <div class="section-title">Earnings</div>
                                                @if (count($filteredEarnings) > 0)
                                                    @foreach ($filteredEarnings as $earning)
                                                        <div class="line-item">
                                                            <div>{{ $earning->variable }}</div>
                                                            <div class="meta-sep">:</div>
                                                            <div class="amt">&#8358;
                                                                {{ number_format($earning->amount, 2, '.', ',') }}</div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="line-item empty">
                                                        <div>No earnings</div>
                                                        <div class="meta-sep">:</div>
                                                        <div class="amt">-</div>
                                                    </div>
                                                @endif
                                                <div class="total-row">
                                                    <div>Total Earnings</div>
                                                    <div class="meta-sep"></div>
                                                    <div class="amt">&#8358;
                                                        {{ number_format($totalEarnings, 2, '.', ',') }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="section-box">
                                                <div class="section-title">Deductions</div>
                                                @if (count($filteredDeductions) > 0)
                                                    @foreach ($filteredDeductions as $deduction)
                                                        <div class="line-item">
                                                            <div>{{ $deduction->variable }}</div>
                                                            <div class="meta-sep">:</div>
                                                            <div class="amt">&#8358;
                                                                {{ number_format($deduction->amount, 2, '.', ',') }}</div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="line-item empty">
                                                        <div>No deductions</div>
                                                        <div class="meta-sep">:</div>
                                                        <div class="amt">-</div>
                                                    </div>
                                                @endif
                                                <div class="total-row">
                                                    <div>Total Deductions</div>
                                                    <div class="meta-sep"></div>
                                                    <div class="amt">&#8358;
                                                        {{ number_format($totalDeductions, 2, '.', ',') }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="employer-wrap">
                                            <div class="employer-title">Employer Contributions (INFORMATIONAL - Not
                                                deducted from Employee)</div>
                                            @if (count($employerContributions) > 0)
                                                @foreach ($employerContributions as $contribution)
                                                    <div class="line-item">
                                                        <div>{{ $contribution->variable }}</div>
                                                        <div class="meta-sep">:</div>
                                                        <div class="amt">&#8358;
                                                            {{ number_format($contribution->amount, 2, '.', ',') }}</div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="line-item empty">
                                                    <div>No contributions</div>
                                                    <div class="meta-sep">:</div>
                                                    <div class="amt">-</div>
                                                </div>
                                            @endif

                                            <div class="employer-divider"></div>
                                            <div class="total-row" style="border-top:none; padding-top:0;">
                                                <div>Total Employer Contributions (INFO ONLY)</div>
                                                <div class="meta-sep"></div>
                                                <div class="amt">&#8358;
                                                    {{ number_format($totalEmployerContributions, 2, '.', ',') }}</div>
                                            </div>
                                        </div>

                                        <div class="netpay-row">
                                            <div class="netpay-label">NET PAY</div>
                                            <div class="netpay-amount">&#8358;
                                                {{ number_format($totalEarnings - $totalDeductions, 2, '.', ',') }}</div>
                                        </div>


                                    </div>

                                    <div class="payslip-footer-img">
                                        <img src="{{ asset('img/payslip_footer.png') }}" alt="Payslip Footer">
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <p>Please select Year, Month, and Staff to view payslip.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- /Payroll Report -->

                </div>
            </div>
        </div>

    </div>
@endsection
@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
    <style>
        label {
            color: black;
            text-shadow: 1px 1px 2px #fff;
        }

        /* Print Styles */
        @media print {
            @page {
                margin: 1cm;
                size: A4;
            }

            /* Hide everything by default */
            body * {
                visibility: hidden;
            }

            /* Show only the payslip section */
            #payslip-row,
            #payslip-row *,
            #payslip-card,
            #payslip-card *,
            #payslip-content,
            #payslip-content * {
                visibility: visible !important;
            }

            /* Position payslip at top */
            #payslip-row {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }

            /* Hide unwanted elements */
            .sidebar,
            .header,
            .page-header,
            .breadcrumb,
            .card-header,
            .btn,
            form[name="mainform"],
            .row:first-child {
                display: none !important;
                visibility: hidden !important;
            }

            /* Remove card styling */
            #payslip-card,
            .card-body {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                background: white !important;
            }

            /* Clean up wrapper elements */
            .page-wrapper,
            .content,
            .container-fluid {
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Ensure payslip container prints correctly */
            .payslip-container {
                margin: 0 !important;
                padding: 20px !important;
                max-width: 100% !important;
            }

            /* Prevent page breaks inside sections */
            .payslip-section {
                page-break-inside: avoid;
            }

            .payslip-net-pay {
                page-break-inside: avoid;
            }
        }
    </style>
@endsection
@section('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script>
        function Reload() {
            document.forms["mainform"].submit();
        }

        function printPayslip() {
            window.print();
        }
    </script>
@endsection
<!-- /Page Wrapper -->
