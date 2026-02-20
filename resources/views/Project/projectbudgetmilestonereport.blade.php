<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Project Budget Milestone Report
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
                            <li class="breadcrumb-item active">Project Budget Milestone Report</li>
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
                            <h4 class="card-title">Select Project</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" id="projectSelectForm">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Project <span class="text-danger">*</span></label>
                                            <?php if ($projectId == '') {
                                                $projectId = old('projectId');
                                            } ?>
                                            <select class="form-control" name="projectId" id="projectId" required
                                                onchange="selectProject()">
                                                <option value="">--Select Project--</option>
                                                @foreach ($projects as $project)
                                                    <option value="{{ $project->id }}"
                                                        {{ $projectId == $project->id ? 'selected' : '' }}>
                                                        {{ $project->projectCode }} - {{ $project->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="select_project" value="1">
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if (!empty($projectId))
                <div class="row">
                    <div class="col-md-12">
                        <!-- Project Budget Milestone Report -->
                        <div class="card card-table">
                            <div class="card-header">
                                <h4 class="card-title">Project Budget Milestone Report</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-hover table-center mb-0" style="min-width: 100%;">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" style="vertical-align: middle;">Contractor/vendor Name
                                                </th>
                                                <th rowspan="2" style="vertical-align: middle;">Amount</th>
                                                @if ($milestones->count() > 0)
                                                    <th colspan="{{ $milestones->count() }}"
                                                        style="text-align: center; border-left: 2px solid #ddd;">Milestone
                                                        Budget (Calculated)</th>
                                                    <th colspan="{{ $milestones->count() }}"
                                                        style="text-align: center; border-left: 2px solid #ddd;">Actual
                                                        Expense</th>
                                                @endif
                                                <th rowspan="2"
                                                    style="vertical-align: middle; border-left: 2px solid #ddd;">Total
                                                    Actual Payment</th>
                                            </tr>
                                            <tr>
                                                @foreach ($milestones as $milestone)
                                                    <th style="text-align: center; border-left: 1px solid #ddd;">
                                                        {{ $milestone->milestone }}<br>
                                                        <small>({{ $milestone->percentage }}%)</small>
                                                    </th>
                                                @endforeach
                                                @foreach ($milestones as $milestone)
                                                    <th style="text-align: center; border-left: 1px solid #ddd;">
                                                        Actual {{ $milestone->milestone }}
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($projectBudgets->count() > 0)
                                                @foreach ($projectBudgets as $budget)
                                                    <tr>
                                                        <td><strong>{{ $budget->budgetName }}</strong></td>
                                                        <td style="text-align: right;">
                                                            <strong>{{ number_format($budget->amount, 2, '.', ',') }}</strong>
                                                        </td>
                                                        @foreach ($milestones as $milestone)
                                                            <td style="text-align: right; border-left: 1px solid #ddd;">
                                                                {{ number_format($budget->milestoneAmounts[$milestone->id]['amount'] ?? 0, 2, '.', ',') }}
                                                            </td>
                                                        @endforeach
                                                        @foreach ($milestones as $milestone)
                                                            <td style="text-align: right; border-left: 1px solid #ddd;">
                                                                {{ number_format($budget->milestoneExpenses[$milestone->id] ?? 0, 2, '.', ',') }}
                                                            </td>
                                                        @endforeach
                                                        <td style="text-align: right; border-left: 2px solid #ddd;">
                                                            <strong>{{ number_format($budget->totalActualExpense, 2, '.', ',') }}</strong>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr style="background-color: #d0d0d0; font-weight: bold; font-size: 1.1em;">
                                                    <td class="text-right"><strong>Grand Total:</strong></td>
                                                    <td style="text-align: right;">
                                                        <strong>{{ number_format($totalBudgeted, 2, '.', ',') }}</strong>
                                                    </td>
                                                    @foreach ($milestones as $milestone)
                                                        @php
                                                            $milestoneTotal = $projectBudgets->sum(function (
                                                                $budget,
                                                            ) use ($milestone) {
                                                                return $budget->milestoneAmounts[$milestone->id][
                                                                    'amount'
                                                                ] ?? 0;
                                                            });
                                                        @endphp
                                                        <td style="text-align: right; border-left: 1px solid #ddd;">
                                                            <strong>{{ number_format($milestoneTotal, 2, '.', ',') }}</strong>
                                                        </td>
                                                    @endforeach
                                                    @foreach ($milestones as $milestone)
                                                        @php
                                                            $expenseTotal = $projectBudgets->sum(function (
                                                                $budget,
                                                            ) use ($milestone) {
                                                                return $budget->milestoneExpenses[$milestone->id] ?? 0;
                                                            });
                                                        @endphp
                                                        <td style="text-align: right; border-left: 1px solid #ddd;">
                                                            <strong>{{ number_format($expenseTotal, 2, '.', ',') }}</strong>
                                                        </td>
                                                    @endforeach
                                                    <td style="text-align: right; border-left: 2px solid #ddd;">
                                                        <strong>{{ number_format($totalActualExpense, 2, '.', ',') }}</strong>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="{{ 2 + $milestones->count() * 2 + 1 }}"
                                                        class="text-center">
                                                        No approved vendor amounts found for this project.
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /Project Budget Milestone Report -->
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <p class="text-center text-muted">Please select a project to view budget milestone report.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
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

        table {
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            white-space: nowrap;
        }

        th {
            background-color: #f2f2f2;
            position: sticky;
            top: 0;
        }

        .table-responsive {
            max-height: 600px;
            overflow-y: auto;
        }
    </style>
@endsection
@section('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script>
        function selectProject() {
            var projectId = document.getElementById('projectId').value;
            if (projectId) {
                document.getElementById('projectSelectForm').submit();
            }
        }
    </script>
@endsection
<!-- /Page Wrapper -->
