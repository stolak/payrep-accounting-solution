<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Budget Utilization Report
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
                            <li class="breadcrumb-item active">Budget Utilization Report</li>
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
                        <!-- Budget Utilization Report -->
                        <div class="card card-table">
                            <div class="card-header">
                                <h4 class="card-title">Budget Utilization Report</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-center mb-0">
                                        <thead>
                                            <tr>
                                                <th rowspan="1">S/N</th>
                                                <th rowspan="1">Budget Classification</th>
                                                <th rowspan="1" style="text-align: right;">Budgeted</th>
                                                <th rowspan="1" style="text-align: right;">Expense</th>
                                                <th rowspan="1" style="text-align: right;">% Utilized</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $i = 1;
                                            @endphp

                                            @if ($budgetUtilization->count() > 0)
                                                @foreach ($budgetUtilization as $item)
                                                    <tr>
                                                        <td>
                                                            {{ $i++ }}
                                                        </td>
                                                        <td>
                                                            {{ $item->classificationName ?? 'Uncategorized' }}
                                                        </td>
                                                        <td style="text-align: right;">
                                                            {{ number_format($item->budgeted, 2, '.', ',') }}
                                                        </td>
                                                        <td style="text-align: right;">
                                                            {{ number_format($item->expense, 2, '.', ',') }}
                                                        </td>
                                                        <td style="text-align: right;">
                                                            <span class="badge {{ $item->percentageUtilized > 100 ? 'bg-danger' : ($item->percentageUtilized > 80 ? 'bg-warning' : 'bg-success') }}">
                                                                {{ number_format($item->percentageUtilized, 2) }}%
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr style="background-color: #d0d0d0; font-weight: bold; font-size: 1.1em;">
                                                    <td></td>
                                                    <td class="text-right"><strong>Total:</strong></td>
                                                    <td style="text-align: right;">
                                                        <strong>{{ number_format($totalBudgeted, 2, '.', ',') }}</strong>
                                                    </td>
                                                    <td style="text-align: right;">
                                                        <strong>{{ number_format($totalExpense, 2, '.', ',') }}</strong>
                                                    </td>
                                                    <td style="text-align: right;">
                                                        <strong>
                                                            <span class="badge {{ $totalPercentageUtilized > 100 ? 'bg-danger' : ($totalPercentageUtilized > 80 ? 'bg-warning' : 'bg-success') }}">
                                                                {{ number_format($totalPercentageUtilized, 2) }}%
                                                            </span>
                                                        </strong>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center">No budget data available for this project yet.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /Budget Utilization Report -->
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <p class="text-center text-muted">Please select a project to view budget utilization report.</p>
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


