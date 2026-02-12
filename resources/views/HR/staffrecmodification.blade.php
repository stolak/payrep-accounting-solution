<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Modification
@endsection
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Setup</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Staff Record Modification</li>
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
                            <h4 class="card-title">Staff Record Modification</h4>
                            <div class="text-right">
                                <button class="btn btn-primary" type="button" onclick="Addnew()">Add New</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data" name="mainform" id="mainform">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-9">
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <select class="select2   form-control" id="staffid" name="staffid"
                                                onchange="Reload()">
                                                <option value="">--Select--</option>
                                                @foreach ($Staffs as $list)
                                                    <option value="{{ $list->id }}"
                                                        {{ old('staffid') == $list->id || $staffid == $list->id ? 'selected' : '' }}>
                                                        {{ $list->first_name }} {{ $list->middle_name }}
                                                        {{ $list->last_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div style="width: 150px;height:150px;" class="float-right">
                                            <img src="{{ $StaffProfile->img != '' ? '/img/passport/' . $StaffProfile->img : '/img/profile_img.png' }}"
                                                alt="profile image" id="output_image"
                                                style="max-width: 100%;max-height: 100%;">
                                        </div>
                                        <input type="file" class="hidden-print" name="passport" accept="image/*"
                                            onchange="preview_image(event)">
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Personal details</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Staff No</label>
                                                    @php
                                                        if ($staffno == '') {
                                                            $staffno = old('staffno');
                                                        }
                                                    @endphp
                                                    @php
                                                        if ($staffno == '') {
                                                            $staffno = $StaffProfile->staff_no;
                                                        }
                                                    @endphp
                                                    <input type="text" class="form-control" name="staffno"
                                                        value="{{ $staffno }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>First Name</label>
                                                    @php
                                                        if ($fname == '') {
                                                            $fname = old('fname');
                                                        }
                                                    @endphp
                                                    @php
                                                        if ($fname == '') {
                                                            $fname = $StaffProfile->first_name;
                                                        }
                                                    @endphp
                                                    <input type="text" class="form-control" value="{{ $fname }}"
                                                        name="fname">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Middle Name</label>
                                                    @php
                                                        if ($mname == '') {
                                                            $mname = old('mname');
                                                        }
                                                    @endphp
                                                    @php
                                                        if ($mname == '') {
                                                            $mname = $StaffProfile->middle_name;
                                                        }
                                                    @endphp
                                                    <input type="text" class="form-control" value="{{ $mname }}"
                                                        name="mname">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Last Name</label>
                                                    @php
                                                        if ($lname == '') {
                                                            $lname = old('lname');
                                                        }
                                                    @endphp
                                                    @php
                                                        if ($lname == '') {
                                                            $lname = $StaffProfile->last_name;
                                                        }
                                                    @endphp
                                                    <input type="text" class="form-control" value="{{ $lname }}"
                                                        name="lname">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Contact details</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Phone no</label>
                                                    @php
                                                        if ($phoneno == '') {
                                                            $phoneno = old('phoneno');
                                                        }
                                                    @endphp
                                                    @php
                                                        if ($phoneno == '') {
                                                            $phoneno = $StaffProfile->phone_no;
                                                        }
                                                    @endphp
                                                    <input type="text" class="form-control" value="{{ $phoneno }}"
                                                        name="phoneno">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    @php
                                                        if ($email == '') {
                                                            $email = old('email');
                                                        }
                                                    @endphp
                                                    @php
                                                        if ($email == '') {
                                                            $email = $StaffProfile->email;
                                                        }
                                                    @endphp
                                                    <input type="text" class="form-control" value="{{ $email }}"
                                                        name="email">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Address</label>
                                                    @php
                                                        if ($address == '') {
                                                            $address = old('address');
                                                        }
                                                    @endphp
                                                    @php
                                                        if ($address == '') {
                                                            $address = $StaffProfile->address;
                                                        }
                                                    @endphp
                                                    <input type="text" class="form-control" value="{{ $address }}"
                                                        name="address">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Employment details</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    @php
                                                        if ($department == '') {
                                                            $department = old('department');
                                                        }
                                                    @endphp
                                                    @php
                                                        if ($department == '') {
                                                            $department = $StaffProfile->department;
                                                        }
                                                    @endphp
                                                    <label>Department</label>
                                                    <select class="form-control" name="department" id="department">
                                                        <option value="">-select-</option>
                                                        @foreach ($Department as $list)
                                                            <option value="{{ $list->id }}"
                                                                {{ $department == $list->id ? 'selected' : '' }}>
                                                                {{ $list->department }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    @php
                                                        if ($grade == '') {
                                                            $grade = old('grade');
                                                        }
                                                    @endphp
                                                    @php
                                                        if ($grade == '') {
                                                            $grade = $StaffProfile->grade;
                                                        }
                                                    @endphp
                                                    <select class="form-control" name="grade" id="grade"
                                                        onchange="updateSalaryRange()">
                                                        @foreach ($Grade as $list)
                                                            <option value="{{ $list->id }}"
                                                                data-lower-salary="{{ $list->lower_salary ?? 0 }}"
                                                                data-upper-salary="{{ $list->upper_salary ?? 0 }}"
                                                                {{ $grade == $list->id ? 'selected' : '' }}>
                                                                {{ $list->grade }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Offer Amount</label>
                                                    @php
                                                        if ($offer_amount == '') {
                                                            $offer_amount = old('offer_amount');
                                                        }
                                                    @endphp
                                                    @php
                                                        if ($offer_amount == '') {
                                                            $offer_amount = $StaffProfile->offer_amount ?? 0;
                                                        }
                                                    @endphp
                                                    <input type="number" step="0.01" class="form-control"
                                                        value="{{ $offer_amount }}" name="offer_amount"
                                                        id="offer_amount" min="0"
                                                        onchange="validateOfferAmount()">
                                                    <small class="text-muted" id="salary_range_hint"
                                                        style="display: none;"></small>
                                                    <small class="text-danger" id="offer_amount_error"
                                                        style="display: none;"></small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    @php
                                                        if ($status == '') {
                                                            $status = old('status');
                                                        }
                                                    @endphp
                                                    @php
                                                        if ($status == '') {
                                                            $status = $StaffProfile->status ?? 'Active';
                                                        }
                                                    @endphp
                                                    <select class="form-control" name="status" id="status">
                                                        <option value="Active"
                                                            {{ $status == 'Active' ? 'selected' : '' }}>Active</option>
                                                        <option value="Resigned"
                                                            {{ $status == 'Resigned' ? 'selected' : '' }}>Resigned</option>
                                                        <option value="Terminated"
                                                            {{ $status == 'Terminated' ? 'selected' : '' }}>Terminated
                                                        </option>
                                                        <option value="Suspended"
                                                            {{ $status == 'Suspended' ? 'selected' : '' }}>Suspended
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Account details</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    @php
                                                        if ($bank == '') {
                                                            $bank = old('bank');
                                                        }
                                                    @endphp
                                                    @php
                                                        if ($bank == '') {
                                                            $bank = $StaffProfile->bankid;
                                                        }
                                                    @endphp
                                                    <label>Bank</label>
                                                    <select class="form-control select2" name="bank" id="bank">
                                                        <option value="">-select-</option>
                                                        @foreach ($BankList as $list)
                                                            <option value="{{ $list->bankID }}"
                                                                {{ $bank == $list->bankID ? 'selected' : '' }}>
                                                                {{ $list->bankCode }} - {{ $list->bank }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Account Number</label>
                                                    @php
                                                        if ($accountno == '') {
                                                            $accountno = old('accountno');
                                                        }
                                                    @endphp
                                                    @php
                                                        if ($accountno == '') {
                                                            $accountno = $StaffProfile->account_no;
                                                        }
                                                    @endphp
                                                    <input type="text" class="form-control"
                                                        value="{{ $accountno }}" name="accountno">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Bank Account Name</label>
                                                    @php
                                                        if ($bank_account_name == '') {
                                                            $bank_account_name = old('bank_account_name');
                                                        }
                                                    @endphp
                                                    @php
                                                        if ($bank_account_name == '') {
                                                            $bank_account_name = $StaffProfile->bank_account_name;
                                                        }
                                                    @endphp
                                                    <input type="text" class="form-control"
                                                        value="{{ $bank_account_name }}" name="bank_account_name">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Pension Number</label>
                                                    @php
                                                        if ($pension_number == '') {
                                                            $pension_number = old('pension_number');
                                                        }
                                                    @endphp
                                                    @php
                                                        if ($pension_number == '') {
                                                            $pension_number = $StaffProfile->pension_number;
                                                        }
                                                    @endphp
                                                    <input type="text" class="form-control"
                                                        value="{{ $pension_number }}" name="pension_number">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>PAYEE Number</label>
                                                    @php
                                                        if ($payee_number == '') {
                                                            $payee_number = old('payee_number');
                                                        }
                                                    @endphp
                                                    @php
                                                        if ($payee_number == '') {
                                                            $payee_number = $StaffProfile->payee_number;
                                                        }
                                                    @endphp
                                                    <input type="text" class="form-control"
                                                        value="{{ $payee_number }}" name="payee_number">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>NHF Number</label>
                                                    @php
                                                        if ($nhf_number == '') {
                                                            $nhf_number = old('nhf_number');
                                                        }
                                                    @endphp
                                                    @php
                                                        if ($nhf_number == '') {
                                                            $nhf_number = $StaffProfile->nhf_number;
                                                        }
                                                    @endphp
                                                    <input type="text" class="form-control"
                                                        value="{{ $nhf_number }}" name="nhf_number">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary" name="update">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <form method="post" id="newform" name="newform" action="/staff-registration">
        {{ csrf_field() }}

    </form>
@endsection
@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">

    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <style>
        label {
            color: black text-shadow: 1px 1px 2px #fff;
        }
    </style>
@endsection
@section('scripts')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script>
        function Addnew() {

            document.forms["newform"].submit();
        }

        $('.select_picker').selectpicker({
            style: 'btn-default',
            size: 4
        });

        function SelectInventory(id) {
            document.getElementById('noid').value = id;
            document.forms["noform"].submit();
        }

        function Reload() {
            document.forms["mainform"].submit();
            return;
        }

        function preview_image(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('output_image');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        function updateSalaryRange() {
            var gradeSelect = document.getElementById('grade');
            var selectedOption = gradeSelect.options[gradeSelect.selectedIndex];
            var lowerSalary = parseFloat(selectedOption.getAttribute('data-lower-salary')) || 0;
            var upperSalary = parseFloat(selectedOption.getAttribute('data-upper-salary')) || 0;
            var hintElement = document.getElementById('salary_range_hint');
            var errorElement = document.getElementById('offer_amount_error');

            if (gradeSelect.value && upperSalary > 0) {
                hintElement.textContent = 'Salary range: ' + formatCurrency(lowerSalary) + ' - ' + formatCurrency(
                    upperSalary);
                hintElement.style.display = 'block';
                errorElement.style.display = 'none';
            } else {
                hintElement.style.display = 'none';
            }

            // Validate current offer amount if it exists
            validateOfferAmount();
        }

        function validateOfferAmount() {
            var gradeSelect = document.getElementById('grade');
            var offerAmountInput = document.getElementById('offer_amount');
            var errorElement = document.getElementById('offer_amount_error');
            var hintElement = document.getElementById('salary_range_hint');

            if (!gradeSelect.value) {
                errorElement.style.display = 'none';
                return;
            }

            var selectedOption = gradeSelect.options[gradeSelect.selectedIndex];
            var lowerSalary = parseFloat(selectedOption.getAttribute('data-lower-salary')) || 0;
            var upperSalary = parseFloat(selectedOption.getAttribute('data-upper-salary')) || 0;
            var offerAmount = parseFloat(offerAmountInput.value) || 0;

            if (offerAmount > 0 && upperSalary > 0) {
                if (offerAmount < lowerSalary || offerAmount > upperSalary) {
                    errorElement.textContent = 'Offer amount must be between ' + formatCurrency(lowerSalary) + ' and ' +
                        formatCurrency(upperSalary);
                    errorElement.style.display = 'block';
                    offerAmountInput.setCustomValidity('Offer amount must be within the grade salary range');
                } else {
                    errorElement.style.display = 'none';
                    offerAmountInput.setCustomValidity('');
                }
            } else {
                errorElement.style.display = 'none';
                offerAmountInput.setCustomValidity('');
            }
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount);
        }

        // Initialize salary range on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateSalaryRange();

            // Validate on form submit
            var form = document.getElementById('mainform');
            if (form) {
                form.addEventListener('submit', function(e) {
                    validateOfferAmount();
                    var errorElement = document.getElementById('offer_amount_error');
                    if (errorElement.style.display === 'block') {
                        e.preventDefault();
                        return false;
                    }
                });
            }
        });
    </script>
@endsection
<!-- /Page Wrapper -->
