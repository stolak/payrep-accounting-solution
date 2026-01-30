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
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Select Period</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" name="mainform" id="mainform">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Year <span class="text-danger">*</span></label>
                                            <select class="form-control" name="year" id="year" onchange="Reload()"
                                                required>
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
                                            <label>Month <span class="text-danger">*</span></label>
                                            <select class="form-control" name="month" id="month" onchange="Reload()"
                                                required>
                                                <option value="">--Select--</option>
                                                @foreach ($Months as $list)
                                                    <option value="{{ $list->id }}"
                                                        {{ old('month') == $list->id || $month == $list->id ? 'selected' : '' }}>
                                                        {{ $list->month }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><br></label>
                                            <br>
                                            <button class="btn btn-primary" type="submit" name="view">View
                                                Records</button>
                                            @if (isset($LockStatus) && $LockStatus == 'locked')
                                                <button class="btn btn-warning" type="submit" name="unlock"
                                                    onclick="return confirm('Are you sure you want to unlock payroll for this period?');">
                                                    <i class="fe fe-unlock"></i> Unlock Payroll
                                                </button>
                                            @elseif(isset($LockStatus) && ($LockStatus == 'unlocked' || $LockStatus == 'partial'))
                                                <button class="btn btn-danger" type="submit" name="lock"
                                                    onclick="return confirm('Are you sure you want to lock payroll for this period? This action cannot be undone easily.');">
                                                    <i class="fe fe-lock"></i> Lock Payroll
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if (isset($PayrollRecords) && count($PayrollRecords) > 0)
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-table">
                            <div class="card-header">
                                <h4 class="card-title">Payroll Records - {{ $year }} -
                                    @foreach ($Months as $m)
                                        @if ($m->id == $month)
                                            {{ $m->month }}
                                        @endif
                                    @endforeach
                                </h4>
                                @if (isset($LockStatus))
                                    <span
                                        class="badge badge-{{ $LockStatus == 'locked' ? 'danger' : ($LockStatus == 'partial' ? 'warning' : 'success') }} float-right">
                                        Status: {{ ucfirst($LockStatus) }}
                                    </span>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-center mb-0">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Staff No</th>
                                                <th>Full Name</th>
                                                <th>Grade</th>
                                                <th>Bank ID</th>
                                                <th>Account No</th>
                                                <th>Lock Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($PayrollRecords as $index => $record)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $record->staff_no }}</td>
                                                    <td>{{ $record->fullname }}</td>
                                                    <td>{{ $record->grade }}</td>
                                                    <td>{{ $record->bankid }}</td>
                                                    <td>{{ $record->account_no }}</td>
                                                    <td>
                                                        @if ($record->isLocked == 1)
                                                            <span class="badge badge-danger">Locked</span>
                                                        @else
                                                            <span class="badge badge-success">Unlocked</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(isset($year) && $year != '' && isset($month) && $month != '')
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>No records found</strong> for the selected period.
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function Reload() {
            document.getElementById('mainform').submit();
        }
    </script>
@endsection
