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

            <div class="row">
                <div class="col-md-12">

                    <!-- Payroll Report -->
                    <div class="card card-table">
                        <div class="card-header">
                            <h4 class="card-title">Payslip</h4>
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
                                    .salary-slip {
                                        margin: 15px;
                                    }

                                    .salary-slip .empDetail {
                                        width: 100%;
                                        text-align: left;
                                        border: 2px solid black;
                                        border-collapse: collapse;
                                        table-layout: fixed;
                                    }

                                    .salary-slip .myBackground {
                                        padding-top: 10px;
                                        text-align: left;
                                        border: 1px solid black;
                                        height: 40px;
                                    }

                                    .salary-slip .myAlign {
                                        text-align: center;
                                        border-right: 1px solid black;
                                    }

                                    .salary-slip .myTotalBackground {
                                        padding-top: 10px;
                                        text-align: left;
                                        background-color: #EBF1DE;
                                        border-spacing: 0px;
                                    }

                                    .salary-slip .table-border-right {
                                        border-right: 1px solid;
                                    }

                                    .salary-slip .companyName {
                                        text-align: right;
                                        font-size: 25px;
                                        font-weight: bold;
                                    }

                                    .salary-slip th,
                                    .salary-slip td {
                                        padding-left: 6px;
                                        border: 1px solid black;
                                    }
                                </style>
                                <div class="salary-slip">
                                    <table class="empDetail">
                                        <tr height="100px">
                                            <td colspan='4'>
                                                <img height="90px" src='{{ asset('assets/img/logo.jpeg') }}' />
                                            </td>
                                            <td colspan='4' class="companyName">
                                                {{ env('Coy_Name', 'ACCOUNTING SOLUTIONS') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Name</th>
                                            <td>{{ $Payroll->fullname ?? '' }}</td>
                                            <td></td>
                                            <th>Employee Number</th>
                                            <td>{{ $Payroll->staff_no ?? '' }}</td>
                                            <td></td>
                                            <th>Position</th>
                                            <td>{{ $Payroll->grades ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <td></td>
                                            <td></td>
                                            <th></th>
                                            <td></td>
                                            <td></td>
                                            <th>Period</th>
                                            <td>{{ $monthName }}, {{ $Payroll->year ?? '' }}</td>
                                        </tr>
                                        <tr class="myBackground">
                                            <th colspan="2">Earnings</th>
                                            <th>Particular</th>
                                            <th class="table-border-right">Amount (Naira)</th>
                                            <th colspan="2">Deductions</th>
                                            <th>Particular</th>
                                            <th>Amount (Naira)</th>
                                        </tr>
                                        @php
                                            $maxRows = max(count($filteredEarnings), count($filteredDeductions));
                                        @endphp
                                        @for ($i = 0; $i < $maxRows; $i++)
                                            <tr>
                                                @if ($i < count($filteredEarnings))
                                                    @php
                                                        $earning = $filteredEarnings[$i];
                                                    @endphp
                                                    <th colspan="2">{{ $earning->variable }}</th>
                                                    <td></td>
                                                    <td class="myAlign">{{ number_format($earning->amount, 2, '.', ',') }}
                                                    </td>
                                                @else
                                                    <th colspan="2"></th>
                                                    <td></td>
                                                    <td class="myAlign"></td>
                                                @endif
                                                @if ($i < count($filteredDeductions))
                                                    @php
                                                        $deduction = $filteredDeductions[$i];
                                                    @endphp
                                                    <th colspan="2">{{ $deduction->variable }}</th>
                                                    <td></td>
                                                    <td class="myAlign">
                                                        {{ number_format($deduction->amount, 2, '.', ',') }}</td>
                                                @else
                                                    <th colspan="2"></th>
                                                    <td></td>
                                                    <td class="myAlign"></td>
                                                @endif
                                            </tr>
                                        @endfor
                                        <tr class="myBackground">
                                            <th colspan="3">Total Payments</th>
                                            <td class="myAlign">{{ number_format($totalEarnings, 2, '.', ',') }}</td>
                                            <th colspan="3">Total Deductions</th>
                                            <td class="myAlign">{{ number_format($totalDeductions, 2, '.', ',') }}</td>
                                        </tr>
                                        <tr height="40px">
                                            <th colspan="2"></th>
                                            <th></th>
                                            <td class="table-border-right"></td>
                                            <th colspan="2" class="table-border-bottom">Net Salary</th>
                                            <td></td>
                                            <td>{{ number_format($totalEarnings - $totalDeductions, 2, '.', ',') }}</td>
                                        </tr>
                                    </table>
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
    </script>
@endsection
<!-- /Page Wrapper -->
