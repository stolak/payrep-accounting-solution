<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Payroll Mandate
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
                            <li class="breadcrumb-item active">Earning/Deduction Report</li>
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
                            <h4 class="card-title">Earning/Deduction Report</h4>
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
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Particular</label>
                                            <select class="form-control" name="variable">
                                                <option value="">--Select--</option>
                                                @foreach ($PayrollVariable as $list)
                                                    <option value="{{ $list->ref_code }}"
                                                        {{ old('variable') == $list->ref_code || $variable == $list->ref_code ? 'selected' : '' }}>
                                                        {{ $list->variable }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
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

                    <!-- Salary Mandate -->
                    <div class="card card-table">
                        <div class="card-header">
                            <h4 class="card-title">{{ $variableName }}</h4>
                            @if (isset($NetpaySummary) && count($NetpaySummary) > 0)
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
                                            <th rowspan="1">Beneficiary</th>
                                            <th rowspan="1">Amount</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                            $net = 0;
                                        @endphp
                                        @foreach ($NetpaySummary as $list2)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $list2->fullname }}</td>
                                                <td>{{ number_format(abs($list2->Net), 2, '.', ',') }}</td>

                                            </tr>
                                            @php $net+=$list2->Net; @endphp
                                        @endforeach
                                        <tr>
                                            <td colspan=2>Total</td>
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
                    <!-- /Salary Mandate -->

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

            // Set column widths
            var colWidths = [{
                    wch: 10
                }, // S/N
                {
                    wch: 30
                }, // Beneficiary
                {
                    wch: 15
                } // Amount
            ];
            ws['!cols'] = colWidths;

            // Add worksheet to workbook
            XLSX.utils.book_append_sheet(wb, ws, "Report");

            // Generate filename
            var filename = "Variable_Report_" + new Date().getTime() + ".xlsx";

            // Write file
            XLSX.writeFile(wb, filename);
        }
    </script>
@endsection
<!-- /Page Wrapper -->
