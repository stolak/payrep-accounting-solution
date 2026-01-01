<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Payroll Summary
@endsection
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Report</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Payroll Report</li>
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
                            <h4 class="card-title">Payroll Report</h4>
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
                            <h4 class="card-title">Payroll Report</h4>
                            @if (isset($Payroll) && count($Payroll) > 0)
                                <button type="button" class="btn btn-success btn-sm float-right" onclick="exportToExcel()">
                                    <i class="fe fe-download"></i> Export to Excel
                                </button>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0" id="exportTable">
                                    <thead>
                                        <tr>
                                            <th rowspan="1">S/N</th>
                                            <th rowspan="1">Staff No</th>
                                            <th rowspan="1">Names</th>
                                            <th rowspan="1">Grade</th>
                                            @foreach ($EarningVariable as $list)
                                                <th rowspan="1">{{ $list->variable }}</th>
                                            @endforeach
                                            <th rowspan="1">Gross Pay</th>
                                            @foreach ($NonTaxableEarning as $list)
                                                <th rowspan="1">{{ $list->variable }}</th>
                                            @endforeach
                                            <th rowspan="1">Total Earning</th>
                                            @foreach ($DeductionVariable as $list)
                                                <th rowspan="1">{{ $list->variable }}</th>
                                            @endforeach
                                            <th rowspan="1">Gross Deduction</th>
                                            <th rowspan="1">Net pay</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                            $grossearn = 0;
                                            $totalearn = 0;
                                            $grossdeduction = 0;
                                            $net = 0;
                                        @endphp

                                        @foreach ($Payroll as $list2)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $list2->staff_no }}</td>
                                                <td>{{ $list2->fullname }}</td>
                                                <td>{{ $list2->grades }}</td>
                                                @php $subgross=0; @endphp
                                                @php $otherearn =0; @endphp
                                                @php $subnet =0; @endphp
                                                @foreach ($EarningVariable as $list)
                                                    @php $para=$list->ref_code; @endphp
                                                    @php $subgross +=$list2->$para; @endphp
                                                    <td>{{ number_format($list2->$para, 2, '.', ',') }}</td>
                                                @endforeach
                                                <td>{{ number_format($subgross, 2, '.', ',') }}</td>
                                                @foreach ($NonTaxableEarning as $list)
                                                    @php $para=$list->ref_code; @endphp
                                                    @php $otherearn +=$list2->$para; @endphp
                                                    <td>{{ number_format($list2->$para, 2, '.', ',') }}</td>
                                                @endforeach
                                                <td>{{ number_format($otherearn + $subgross, 2, '.', ',') }}</td>
                                                @php $subdeduction=0; @endphp
                                                @foreach ($DeductionVariable as $list)
                                                    @php $para=$list->ref_code; @endphp
                                                    @php $subdeduction +=$list2->$para; @endphp
                                                    <td>
                                                        @if ($list2->$para < 0)
                                                            ({{ number_format(abs($list2->$para), 2, '.', ',') }})
                                                        @else
                                                            {{ number_format(abs($list2->$para), 2, '.', ',') }}
                                                        @endif
                                                    </td>
                                                @endforeach
                                                <td>
                                                    @if ($subdeduction < 0)
                                                        ({{ number_format(abs($subdeduction), 2, '.', ',') }})
                                                    @else
                                                        {{ number_format(abs($subdeduction), 2, '.', ',') }}
                                                    @endif
                                                </td>
                                                @php $subnet +=$subdeduction+$subgross+$otherearn; @endphp
                                                <td>{{ number_format($subnet, 2, '.', ',') }}</td>
                                            </tr>
                                            @php
                                                $grossearn += $subgross;
                                                $totalearn += $subgross + $otherearn;
                                                $grossdeduction += $subdeduction;
                                                $net += $subnet;
                                            @endphp
                                        @endforeach
                                        <tr>
                                            <td colspan=1>Total</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            @foreach ($EarningVariable as $list)
                                                @php $para=$list->ref_code; @endphp
                                                <td>
                                                    @if ($MonthlyActiveVariable->$para < 0)
                                                        ({{ number_format(abs($MonthlyActiveVariable->$para), 2, '.', ',') }})
                                                    @else
                                                        {{ number_format(abs($MonthlyActiveVariable->$para), 2, '.', ',') }}
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td>{{ number_format($grossearn, 2, '.', ',') }}</td>
                                            @foreach ($NonTaxableEarning as $list)
                                                @php $para=$list->ref_code; @endphp
                                                <td>
                                                    @if ($MonthlyActiveVariable->$para < 0)
                                                        ({{ number_format(abs($MonthlyActiveVariable->$para), 2, '.', ',') }})
                                                    @else
                                                        {{ number_format(abs($MonthlyActiveVariable->$para), 2, '.', ',') }}
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td>{{ number_format($totalearn, 2, '.', ',') }}</td>
                                            @foreach ($DeductionVariable as $list)
                                                @php $para=$list->ref_code; @endphp
                                                <td>
                                                    @if ($MonthlyActiveVariable->$para < 0)
                                                        ({{ number_format(abs($MonthlyActiveVariable->$para), 2, '.', ',') }})
                                                    @else
                                                        {{ number_format(abs($MonthlyActiveVariable->$para), 2, '.', ',') }}
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td>
                                                @if ($grossdeduction < 0)
                                                    ({{ number_format(abs($grossdeduction), 2, '.', ',') }})
                                                @else
                                                    {{ number_format(abs($grossdeduction), 2, '.', ',') }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($net < 0)
                                                    ({{ number_format(abs($net), 2, '.', ',') }})
                                                @else
                                                    {{ number_format(abs($net), 2, '.', ',') }}
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        function Reload() {
            document.forms["mainform"].submit();
        }

        function exportToExcel() {
            // Get table data
            var table = document.getElementById('exportTable');
            var data = [];

            // Get headers
            var headers = [];
            var headerRow = table.querySelectorAll('thead tr th');
            headerRow.forEach(function(th) {
                headers.push(th.textContent.trim());
            });
            data.push(headers);

            // Get body rows (excluding total row)
            var rows = table.querySelectorAll('tbody tr');
            rows.forEach(function(row, index) {
                // Skip the total row
                if (row.querySelector('td[colspan]')) {
                    return;
                }
                var rowData = [];
                var cells = row.querySelectorAll('td');
                cells.forEach(function(cell) {
                    // Remove formatting from numbers
                    var cellText = cell.textContent.trim();
                    // Remove commas and parentheses for numbers
                    var numValue = cellText.replace(/,/g, '').replace(/[()]/g, '');
                    if (!isNaN(numValue) && numValue !== '') {
                        rowData.push(parseFloat(numValue));
                    } else {
                        rowData.push(cellText);
                    }
                });
                data.push(rowData);
            });

            // Add total row
            var totalRow = table.querySelector('tbody tr:last-child');
            if (totalRow && totalRow.querySelector('td[colspan]')) {
                var totalData = [];
                var totalCells = totalRow.querySelectorAll('td');
                totalCells.forEach(function(cell) {
                    var cellText = cell.textContent.trim();
                    var numValue = cellText.replace(/,/g, '').replace(/[()]/g, '');
                    if (!isNaN(numValue) && numValue !== '') {
                        totalData.push(parseFloat(numValue));
                    } else {
                        totalData.push(cellText);
                    }
                });
                data.push(totalData);
            }

            // Create workbook
            var wb = XLSX.utils.book_new();
            var ws = XLSX.utils.aoa_to_sheet(data);

            // Set column widths (dynamic based on number of columns)
            var colCount = headers.length;
            var colWidths = [];
            for (var i = 0; i < colCount; i++) {
                if (i === 0) {
                    colWidths.push({
                        wch: 8
                    }); // S/N
                } else if (i === 1) {
                    colWidths.push({
                        wch: 12
                    }); // Staff No
                } else if (i === 2) {
                    colWidths.push({
                        wch: 30
                    }); // Names
                } else if (i === 3) {
                    colWidths.push({
                        wch: 15
                    }); // Grade
                } else {
                    colWidths.push({
                        wch: 15
                    }); // Other columns (amounts)
                }
            }
            ws['!cols'] = colWidths;

            // Add worksheet to workbook
            XLSX.utils.book_append_sheet(wb, ws, "Payroll Report");

            // Generate filename
            var filename = "Payroll_Report_" + new Date().getTime() + ".xlsx";

            // Write file
            XLSX.writeFile(wb, filename);
        }
    </script>
@endsection
<!-- /Page Wrapper -->
