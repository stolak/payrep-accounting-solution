<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Cash Flow Report
@endsection
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Reports</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Cash Flow Report</li>
                        </ul>
                    </div>
                </div>
            </div>

            @include('_partialView.nofication')

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Cash Flow Filter</h4>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Cash Ledger Type</label>
                                            <select class="form-control" name="subheadid">
                                                <option value="">--All--</option>
                                                @foreach ($cashSubheads as $subhead)
                                                    <option value="{{ $subhead->id }}"
                                                        {{ old('subheadid', $subheadid) == $subhead->id ? 'selected' : '' }}>
                                                        {{ $subhead->subhead }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Cash Ledger Account</label>
                                            <select class="form-control" name="accountid">
                                                <option value="">--All--</option>
                                                @foreach ($cashAccounts as $account)
                                                    <option value="{{ $account->id }}"
                                                        {{ old('accountid', $accountid) == $account->id ? 'selected' : '' }}>
                                                        {{ $account->accountdescription }} ({{ $account->accountno }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>From</label>
                                            <input type="date" name="fromdate" value="{{ $fromdate }}"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>To</label>
                                            <input type="date" name="todate" value="{{ $todate }}"
                                                class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button class="btn btn-primary" type="submit" name="post">Refresh</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="mb-1">Opening Cash Balance</h6>
                            <h4 class="mb-0">{{ number_format($openingBalance, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="mb-1">Cash Inflow</h6>
                            <h4 class="mb-0 text-success">{{ number_format($periodInflow, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="mb-1">Cash Outflow</h6>
                            <h4 class="mb-0 text-danger">{{ number_format($periodOutflow, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="mb-1">Closing Cash Balance</h6>
                            <h4 class="mb-0">{{ number_format($closingBalance, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-table">
                        <div class="card-header">
                            <h4 class="card-title">Cash Flow by Ledger</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Ledger</th>
                                            <th>Inflow</th>
                                            <th>Outflow</th>
                                            <th>Net Flow</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($cashFlowByLedger as $index => $row)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $row->accountdescription ?? 'N/A' }} ({{ $row->accountno ?? '' }})
                                                </td>
                                                <td>{{ number_format($row->inflow, 2) }}</td>
                                                <td>{{ number_format($row->outflow, 2) }}</td>
                                                <td>{{ number_format($row->netflow, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No cash ledger movement found for
                                                    selected period.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-table">
                        <div class="card-header">
                            <h4 class="card-title">Cash Transaction Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="cashflow-details" class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>Manual Ref</th>
                                            <th>Ledger Type</th>
                                            <th>Ledger</th>
                                            <th>Remarks</th>
                                            <th>Debit (Inflow)</th>
                                            <th>Credit (Outflow)</th>
                                            <th>Posted By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($cashTransactions as $index => $trx)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $trx->transdate }}</td>
                                                <td>{{ $trx->ref }}</td>
                                                <td>{{ $trx->manual_ref }}</td>
                                                <td>{{ $trx->subhead }}</td>
                                                <td>{{ $trx->accountdescription ?? 'N/A' }} ({{ $trx->accountno ?? '' }})
                                                </td>
                                                <td>{{ $trx->remarks }}</td>
                                                <td>{{ number_format($trx->debit, 2) }}</td>
                                                <td>{{ number_format($trx->credit, 2) }}</td>
                                                <td>{{ $trx->postedBy ?? '' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">No cash transactions found for
                                                    selected period.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
            color: black;
            text-shadow: 1px 1px 2px #fff;
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#cashflow-details').DataTable({
                pageLength: 25,
                order: [
                    [1, 'desc']
                ]
            });
        });
    </script>
@endsection
<!-- /Page Wrapper -->
