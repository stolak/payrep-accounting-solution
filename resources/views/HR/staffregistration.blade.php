<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Registration
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
                            <li class="breadcrumb-item active">Staff Registration</li>
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
                            <h4 class="card-title">Staff Registration</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-9">
                                    </div>
                                    <div class="col-md-3">
                                        <div style="width: 150px;height:150px;" class="float-right">
                                            <img src="{{ '/img/profile_img.png' }}" alt="profile image" id="output_image"
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
                                                    <input type="text" class="form-control" value=""
                                                        name="staffno">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>First Name</label>
                                                    <input type="text" class="form-control" value=""
                                                        name="fname">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Middle Name</label>
                                                    <input type="text" class="form-control" value=""
                                                        name="mname">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Last Name</label>
                                                    <input type="text" class="form-control" value=""
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
                                                    <input type="text" class="form-control" value=""
                                                        name="phoneno">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input type="text" class="form-control" value=""
                                                        name="email">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Address</label>
                                                    <input type="text" class="form-control" value=""
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
                                                    <label>Department</label>
                                                    <select class="form-control" name="department" id="department">
                                                        <option value="">-select-</option>
                                                        @foreach ($Department as $list)
                                                            <option value="{{ $list->id }}">{{ $list->department }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select class="form-control" name="grade" id="grade" onchange="updateSalaryRange()">
                                                        <option value="">-select-</option>
                                                        @foreach ($Grade as $list)
                                                            <option value="{{ $list->id }}" 
                                                                data-lower-salary="{{ $list->lower_salary ?? 0 }}"
                                                                data-upper-salary="{{ $list->upper_salary ?? 0 }}">
                                                                {{ $list->grade }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Offer Amount</label>
                                                    <input type="number" step="0.01" class="form-control" 
                                                        value="{{ old('offer_amount') }}" 
                                                        name="offer_amount" 
                                                        id="offer_amount"
                                                        min="0"
                                                        onchange="validateOfferAmount()">
                                                    <small class="text-muted" id="salary_range_hint" style="display: none;"></small>
                                                    <small class="text-danger" id="offer_amount_error" style="display: none;"></small>
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
                                                    <label>Bank</label>
                                                    <select class="select2 form-control" name="bank">
                                                        <option value="">-select-</option>
                                                        @foreach ($BankList as $list)
                                                            <option value="{{ $list->bankID }}"> {{ $list->bankCode }} -
                                                                {{ $list->bank }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Account Number</label>
                                                    <input type="text" class="form-control" value=""
                                                        name="accountno">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Bank Account Name</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ old('bank_account_name') }}"
                                                        name="bank_account_name">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Pension Number</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ old('pension_number') }}" name="pension_number">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>PAYEE Number</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ old('payee_number') }}" name="payee_number">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>NHF Number</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ old('nhf_number') }}" name="nhf_number">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <form method="post" id="noform" name="noform">
        {{ csrf_field() }}
        <input type="hidden" class="form-control" id="noid" name="id" value="">

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
        function editfunc(id, cat) {
            document.getElementById('id').value = id;
            document.getElementById('category').value = cat;


            $("#edit_details").modal('show')
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
                hintElement.textContent = 'Salary range: ' + formatCurrency(lowerSalary) + ' - ' + formatCurrency(upperSalary);
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
                    errorElement.textContent = 'Offer amount must be between ' + formatCurrency(lowerSalary) + ' and ' + formatCurrency(upperSalary);
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

        // Validate on form submit
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.querySelector('form[method="post"]');
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
