<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Petty Cash Report
@endsection
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Reports</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Petty Cash Report</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <!-- include notification -->
            @include('_partialView.nofication')
            <!-- /include notification -->

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Operational Expense Report</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" name="mainform" id="mainform">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Administrative Expense</label>
                                            <select class="form-control" name="particular">
                                                <option value="">--All--</option>
                                                @foreach ($ProjectAccount as $list)
                                                    <option value="{{ $list->id }}"
                                                        {{ old('particular', $particular) == $list->id ? 'selected' : '' }}>
                                                        {{ $list->particular }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>From</label>
                                            <input type="date" name="fromdate" value="{{ $fromdate }}"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
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
                <div class="col-md-12">
                    <div class="card card-table">
                        <div class="card-header">
                            <h4 class="card-title">Operational Expense Report</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="mytable" class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Transaction Date</th>
                                            <th>Administrative Expense</th>
                                            <th>Description</th>
                                            <th>Ledger Account</th>
                                            <th>Amount</th>
                                            <th>Reference Number</th>
                                            <th>Posted By</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                        @endphp
                                        @foreach ($PettyTransaction as $list)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $list->transdate }}</td>
                                                <td>{{ $list->Particular ?? 'N/A' }}</td>
                                                <td>{{ $list->remark ?? '' }}</td>
                                                <td>{{ $list->AccountName ?? 'N/A' }}</td>
                                                <td>{{ $list->amount }}</td>
                                                <td>{{ $list->manual_ref ?? '' }}</td>
                                                <td>{{ $list->Postedby ?? 'N/A' }}</td>

                                            </tr>
                                        @endforeach
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
@endsection
<!-- /Page Wrapper -->
