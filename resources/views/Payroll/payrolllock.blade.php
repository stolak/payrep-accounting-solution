<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Payroll Lock/Unlock
@endsection
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Payroll Lock/Unlock</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Payroll Lock/Unlock</li>
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
                    <div class="card card-table">
                        <div class="card-header">
                            <h4 class="card-title">Payroll Periods Lock Status</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Period</th>
                                            <th>Year</th>
                                            <th>Month</th>
                                            <th>Total Records</th>

                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($Periods) && count($Periods) > 0)
                                            @foreach ($Periods as $index => $period)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td><strong>{{ $period['year'] }} - {{ $period['monthName'] }}</strong>
                                                    </td>
                                                    <td>{{ $period['year'] }}</td>
                                                    <td>{{ $period['monthName'] }}</td>
                                                    <td>{{ $period['totalCount'] }}</td>

                                                    <td>
                                                        @if ($period['status'] == 'locked')
                                                            <span class="badge badge-danger">Locked</span>
                                                        @elseif($period['status'] == 'partial')
                                                            <span class="badge badge-warning">Partial</span>
                                                        @else
                                                            <span class="badge badge-success">Open</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <form method="post" style="display: inline-block;">
                                                            {{ csrf_field() }}
                                                            <input type="hidden" name="year"
                                                                value="{{ $period['year'] }}">
                                                            <input type="hidden" name="month"
                                                                value="{{ $period['month'] }}">
                                                            @if ($period['status'] == 'locked')
                                                                <button class="btn btn-sm btn-warning" type="submit"
                                                                    name="unlock"
                                                                    onclick="return confirm('Are you sure you want to unlock payroll for {{ $period['year'] }} - {{ $period['monthName'] }}?');">
                                                                    <i class="fe fe-unlock"></i> Unlock
                                                                </button>
                                                            @else
                                                                <button class="btn btn-sm btn-danger" type="submit"
                                                                    name="lock"
                                                                    onclick="return confirm('Are you sure you want to lock payroll for {{ $period['year'] }} - {{ $period['monthName'] }}? This action cannot be undone easily.');">
                                                                    <i class="fe fe-lock"></i> Lock
                                                                </button>
                                                            @endif
                                                        </form>
                                                        @if ($period['status'] != 'locked')
                                                            <form method="post"
                                                                style="display: inline-block; margin-left: 5px;">
                                                                {{ csrf_field() }}
                                                                <input type="hidden" name="year"
                                                                    value="{{ $period['year'] }}">
                                                                <input type="hidden" name="month"
                                                                    value="{{ $period['month'] }}">
                                                                <button class="btn btn-sm btn-secondary" type="submit"
                                                                    name="trash"
                                                                    onclick="return confirm('Are you sure you want to delete all records for {{ $period['year'] }} - {{ $period['monthName'] }}? This action cannot be undone!');">
                                                                    <i class="fe fe-trash-2"></i> Trash
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="9" class="text-center">
                                                    <div class="alert alert-info">
                                                        <strong>No payroll periods found.</strong>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function Reload() {
            document.getElementById('mainform').submit();
        }
    </script>
@endsection
