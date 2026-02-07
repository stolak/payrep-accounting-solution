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
                                    $totalEmployerContributions = 0;

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

                                    // Calculate Employer Contributions
                                    $employerContributions = [];
                                    $pensionableAmount = 0;
                                    
                                    // Get pensionable earnings (usually basic salary and statutory earnings)
                                    foreach ($filteredEarnings as $earning) {
                                        // Check if this earning is pensionable (you may need to adjust this logic)
                                        $pensionableAmount += $earning->amount;
                                    }
                                    
                                    // Employer Pension (10% of pensionable amount)
                                    $employeePension = 0;
                                    foreach ($filteredDeductions as $deduction) {
                                        if (stripos($deduction->variable, 'pension') !== false || stripos($deduction->variable, 'Pension') !== false) {
                                            $employeePension = $deduction->amount;
                                            break;
                                        }
                                    }
                                    // If employee pension is 8%, employer is 10% (ratio 8:10)
                                    $employerPension = $employeePension > 0 ? ($employeePension * 10 / 8) : ($pensionableAmount * 0.10);
                                    if ($employerPension > 0) {
                                        $employerContributions[] = (object) [
                                            'variable' => 'Employer Pension (10%)',
                                            'amount' => $employerPension,
                                        ];
                                        $totalEmployerContributions += $employerPension;
                                    }

                                    // Employer NHIS (typically 10% of basic salary or fixed amount)
                                    $employeeNHIS = 0;
                                    foreach ($filteredDeductions as $deduction) {
                                        if (stripos($deduction->variable, 'NHIS') !== false || stripos($deduction->variable, 'nhis') !== false) {
                                            $employeeNHIS = $deduction->amount;
                                            break;
                                        }
                                    }
                                    // Employer NHIS is typically 10% of basic or employee contribution * 10/5
                                    $employerNHIS = $employeeNHIS > 0 ? ($employeeNHIS * 10 / 5) : ($pensionableAmount * 0.10);
                                    if ($employerNHIS > 0) {
                                        $employerContributions[] = (object) [
                                            'variable' => 'Employer NHIS',
                                            'amount' => $employerNHIS,
                                        ];
                                        $totalEmployerContributions += $employerNHIS;
                                    }

                                    // NSITF (typically 1% of basic salary)
                                    $nsitf = $pensionableAmount * 0.01;
                                    if ($nsitf > 0) {
                                        $employerContributions[] = (object) [
                                            'variable' => 'NSITF',
                                            'amount' => $nsitf,
                                        ];
                                        $totalEmployerContributions += $nsitf;
                                    }

                                    // ITF (Training Fund - typically 1% of basic salary)
                                    $itf = $pensionableAmount * 0.01;
                                    if ($itf > 0) {
                                        $employerContributions[] = (object) [
                                            'variable' => 'ITF (Training Fund)',
                                            'amount' => $itf,
                                        ];
                                        $totalEmployerContributions += $itf;
                                    }
                                @endphp
                                <style>
                                    .payslip-container {
                                        max-width: 900px;
                                        margin: 0 auto;
                                        background: white;
                                        padding: 30px;
                                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                                    }

                                    .payslip-header {
                                        display: flex;
                                        justify-content: space-between;
                                        align-items: center;
                                        padding: 20px 0;
                                        border-bottom: 3px solid #2c5f2d;
                                        margin-bottom: 30px;
                                    }

                                    .payslip-logo {
                                        height: 60px;
                                    }

                                    .payslip-company-info {
                                        text-align: right;
                                    }

                                    .payslip-company-name {
                                        font-size: 24px;
                                        font-weight: bold;
                                        color: #2c5f2d;
                                        margin: 0;
                                    }

                                    .payslip-tagline {
                                        font-size: 12px;
                                        color: #666;
                                        margin-top: 5px;
                                    }

                                    .payslip-title {
                                        text-align: center;
                                        font-size: 28px;
                                        font-weight: bold;
                                        color: #2c5f2d;
                                        margin: 20px 0;
                                    }

                                    .payslip-employee-info {
                                        display: grid;
                                        grid-template-columns: repeat(2, 1fr);
                                        gap: 15px;
                                        margin-bottom: 30px;
                                        padding: 15px;
                                        background: #f8f9fa;
                                        border-radius: 5px;
                                    }

                                    .payslip-info-item {
                                        display: flex;
                                        flex-direction: column;
                                    }

                                    .payslip-info-label {
                                        font-size: 11px;
                                        color: #666;
                                        text-transform: uppercase;
                                        margin-bottom: 5px;
                                    }

                                    .payslip-info-value {
                                        font-size: 14px;
                                        font-weight: 600;
                                        color: #333;
                                    }

                                    .payslip-sections {
                                        display: grid;
                                        grid-template-columns: repeat(2, 1fr);
                                        gap: 20px;
                                        margin-bottom: 30px;
                                    }

                                    .payslip-employer-section {
                                        margin-top: 20px;
                                        margin-bottom: 30px;
                                    }

                                    .payslip-employer-section .payslip-section {
                                        width: 100%;
                                    }

                                    .payslip-section {
                                        border: 1px solid #ddd;
                                        border-radius: 5px;
                                        overflow: hidden;
                                    }

                                    .payslip-section-header {
                                        background: #2c5f2d;
                                        color: white;
                                        padding: 12px;
                                        font-weight: bold;
                                        text-align: center;
                                        font-size: 14px;
                                    }

                                    .payslip-section-body {
                                        padding: 0;
                                    }

                                    .payslip-item {
                                        display: flex;
                                        justify-content: space-between;
                                        padding: 10px 12px;
                                        border-bottom: 1px solid #eee;
                                    }

                                    .payslip-item:last-child {
                                        border-bottom: none;
                                    }

                                    .payslip-item-label {
                                        font-size: 12px;
                                        color: #333;
                                        flex: 1;
                                    }

                                    .payslip-item-amount {
                                        font-size: 12px;
                                        font-weight: 600;
                                        color: #333;
                                        text-align: right;
                                        min-width: 120px;
                                    }

                                    .payslip-total {
                                        background: #e8f5e9;
                                        font-weight: bold;
                                        padding: 12px;
                                        display: flex;
                                        justify-content: space-between;
                                    }

                                    .payslip-net-pay {
                                        background: #2c5f2d;
                                        color: white;
                                        padding: 20px;
                                        text-align: center;
                                        border-radius: 5px;
                                        margin: 30px 0;
                                    }

                                    .payslip-net-pay-label {
                                        font-size: 14px;
                                        margin-bottom: 10px;
                                        text-transform: uppercase;
                                    }

                                    .payslip-net-pay-amount {
                                        font-size: 32px;
                                        font-weight: bold;
                                    }

                                    .payslip-footer {
                                        margin-top: 30px;
                                        padding: 15px;
                                        background: #f8f9fa;
                                        border-radius: 5px;
                                        font-size: 11px;
                                        color: #666;
                                        text-align: center;
                                        border-top: 2px solid #2c5f2d;
                                    }

                                    .payslip-empty {
                                        color: #999;
                                        font-style: italic;
                                    }

                                    @media print {
                                        .payslip-container {
                                            padding: 20px;
                                        }
                                    }
                                </style>
                                <div id="payslip-content" class="payslip-container">
                                    <div class="payslip-header">
                                        <div>
                                            <img src="{{ asset('assets/img/logo.jpeg') }}" alt="Logo" class="payslip-logo" />
                                        </div>
                                        <div class="payslip-company-info">
                                            <h2 class="payslip-company-name">{{ env('Coy_Name', 'McEmtol CONSULTING') }}</h2>
                                            <p class="payslip-tagline">PROFESSIONALISM | SERVICE | RESULTS</p>
                                        </div>
                                    </div>

                                    <h1 class="payslip-title">EMPLOYEE PAYSLIP</h1>

                                    <div class="payslip-employee-info">
                                        <div class="payslip-info-item">
                                            <span class="payslip-info-label">Employee Name</span>
                                            <span class="payslip-info-value">{{ $Payroll->fullname ?? 'N/A' }}</span>
                                        </div>
                                        <div class="payslip-info-item">
                                            <span class="payslip-info-label">Designation</span>
                                            <span class="payslip-info-value">{{ $Payroll->designation ?? $Payroll->grades ?? 'N/A' }}</span>
                                        </div>
                                        <div class="payslip-info-item">
                                            <span class="payslip-info-label">Employee ID</span>
                                            <span class="payslip-info-value">{{ $Payroll->staff_no ?? 'N/A' }}</span>
                                        </div>
                                        <div class="payslip-info-item">
                                            <span class="payslip-info-label">Pay Period</span>
                                            <span class="payslip-info-value">{{ $monthName }}, {{ $Payroll->year ?? '' }}</span>
                                        </div>
                                    </div>

                                    <div class="payslip-sections">
                                        <!-- Earnings Section -->
                                        <div class="payslip-section">
                                            <div class="payslip-section-header">Earnings</div>
                                            <div class="payslip-section-body">
                                                @if (count($filteredEarnings) > 0)
                                                    @foreach ($filteredEarnings as $earning)
                                                        <div class="payslip-item">
                                                            <span class="payslip-item-label">{{ $earning->variable }}</span>
                                                            <span class="payslip-item-amount">₦ {{ number_format($earning->amount, 2, '.', ',') }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="payslip-item">
                                                        <span class="payslip-item-label payslip-empty">No earnings</span>
                                                    </div>
                                                @endif
                                                <div class="payslip-total">
                                                    <span>Total Earnings</span>
                                                    <span>₦ {{ number_format($totalEarnings, 2, '.', ',') }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Deductions Section -->
                                        <div class="payslip-section">
                                            <div class="payslip-section-header">Deductions</div>
                                            <div class="payslip-section-body">
                                                @if (count($filteredDeductions) > 0)
                                                    @foreach ($filteredDeductions as $deduction)
                                                        <div class="payslip-item">
                                                            <span class="payslip-item-label">{{ $deduction->variable }}</span>
                                                            <span class="payslip-item-amount">₦ {{ number_format($deduction->amount, 2, '.', ',') }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="payslip-item">
                                                        <span class="payslip-item-label payslip-empty">No deductions</span>
                                                    </div>
                                                @endif
                                                <div class="payslip-total">
                                                    <span>Total Deductions</span>
                                                    <span>₦ {{ number_format($totalDeductions, 2, '.', ',') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Employer Contributions Section -->
                                    <div class="payslip-employer-section">
                                        <div class="payslip-section">
                                            <div class="payslip-section-header">Employer Contributions (INFORMATIONAL - Not deducted from Employee)</div>
                                            <div class="payslip-section-body">
                                                @if (count($employerContributions) > 0)
                                                    @foreach ($employerContributions as $contribution)
                                                        <div class="payslip-item">
                                                            <span class="payslip-item-label">{{ $contribution->variable }}</span>
                                                            <span class="payslip-item-amount">₦ {{ number_format($contribution->amount, 2, '.', ',') }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="payslip-item">
                                                        <span class="payslip-item-label payslip-empty">No contributions</span>
                                                    </div>
                                                @endif
                                                <div class="payslip-total">
                                                    <span>Total Employer Contributions (INFO ONLY)</span>
                                                    <span>₦ {{ number_format($totalEmployerContributions, 2, '.', ',') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="payslip-net-pay">
                                        <div class="payslip-net-pay-label">NET PAY</div>
                                        <div class="payslip-net-pay-amount">₦ {{ number_format($totalEarnings - $totalDeductions, 2, '.', ',') }}</div>
                                    </div>

                                    <div class="payslip-footer">
                                        <p><strong>Employer contributions shown above are for information only and do not reduce the employee's net pay. This payslip is computer-generated.</strong></p>
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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
    <style>
        label {
            color: black text-shadow: 1px 1px 2px #fff;
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
