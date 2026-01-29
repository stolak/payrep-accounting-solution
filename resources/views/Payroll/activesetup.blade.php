<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Salary Computation
@endsection
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Payroll</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Active Period</li>
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
                            <h4 class="card-title">Active Period Setup</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" name="mainform" id="mainform">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Current Active year</label>
                                            <input type="text" class="form-control" value="{{ $cyear }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Current Active Month</label>
                                            <input type="text" class="form-control" value="{{ $cmonth }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Mandate Message</label>
                                            <input type="text" class="form-control"
                                                value="{{ $active_period->mandateMessage }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>New Active year</label>
                                            <select class="form-control" name="year">
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
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>New Active Month</label>
                                            <select class="form-control" name="month">
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
                                            <label>
                                                Mandate Message
                                                <a href="javascript:void(0)" onclick="insertTemplateMessage()"
                                                    class="ml-2" title="Insert template message"
                                                    style="color: #007bff; text-decoration: none; cursor: pointer;">
                                                    <i class="fas fa-copy"></i>
                                                </a>
                                            </label>
                                            <input type="text" class="form-control" name="mandateMessage"
                                                id="mandateMessage" value='{{ $mandateMessage }}'>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label><br></label>
                                            <br>
                                            <button class="btn btn-primary" type="submit" name="update">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
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

        function insertTemplateMessage() {
            // Get selected year and month
            var yearSelect = document.querySelector('select[name="year"]');
            var monthSelect = document.querySelector('select[name="month"]');
            var mandateMessageInput = document.getElementById('mandateMessage');

            var year = yearSelect ? yearSelect.value : '';
            var monthId = monthSelect ? monthSelect.value : '';

            // Get month name from selected option
            var monthName = '';
            if (monthSelect && monthId) {
                var selectedOption = monthSelect.options[monthSelect.selectedIndex];
                monthName = selectedOption ? selectedOption.text : '';
            }

            // Build template message
            var templateMessage = 'Salary for ' + (year || '[Year]') + ' ' + (monthName || '[month]');

            // Insert into input field
            if (mandateMessageInput) {
                mandateMessageInput.value = templateMessage;
                mandateMessageInput.focus();
            }
        }
    </script>
@endsection
<!-- /Page Wrapper -->
