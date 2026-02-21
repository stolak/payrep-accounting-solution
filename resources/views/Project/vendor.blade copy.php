<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Vendor Setup
@endsection
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Setup</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Vendor Setup</li>
                        </ul>
                    </div>
                </div>
            </div>

            @include('_partialView.nofication')

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Create Vendor</h4>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Vendor Name <span class="text-danger">*</span></label>
                                            <?php if ($name == '') {
                                                $name = old('name');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $name }}" required
                                                name="name">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Vendor ID</label>
                                            <?php if ($vendorId == '') {
                                                $vendorId = old('vendorId');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $vendorId }}"
                                                name="vendorId">
                                            <small class="form-text text-muted">Optional. Auto-generated when left
                                                blank.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Trade Name</label>
                                            <?php if ($tradeName == '') {
                                                $tradeName = old('trade_name');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $tradeName }}"
                                                name="trade_name">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Vendor Type</label>
                                            <?php if ($vendorType == '') {
                                                $vendorType = old('vendor_type');
                                            } ?>
                                            <select class="select2 form-control" name="vendor_type">
                                                <option value="">--Select Type--</option>
                                                @foreach ($vendorTypes as $type)
                                                    <option value="{{ $type->id }}"
                                                        {{ $vendorType == $type->id ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Vendor Category</label>
                                            <?php if ($vendorCategory == '') {
                                                $vendorCategory = old('vendor_category');
                                            } ?>
                                            <select class="select2 form-control" name="vendor_category">
                                                <option value="">--Select Category--</option>
                                                @foreach ($vendorCategories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ $vendorCategory == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Tax Number</label>
                                            <?php if ($taxNumber == '') {
                                                $taxNumber = old('tax_number');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $taxNumber }}"
                                                name="tax_number">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <?php if ($email == '') {
                                                $email = old('email');
                                            } ?>
                                            <input type="email" class="form-control" value="{{ $email }}"
                                                name="email">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Contact Phone Number</label>
                                            <?php if ($contactPhoneNumber == '') {
                                                $contactPhoneNumber = old('contact_phone_number');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $contactPhoneNumber }}"
                                                name="contact_phone_number">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Contact Person</label>
                                            <?php if ($contactPerson == '') {
                                                $contactPerson = old('contact_person');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $contactPerson }}"
                                                name="contact_person">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Account</label>
                                            <?php if ($accountId == '') {
                                                $accountId = old('accountId');
                                            } ?>
                                            <select class="select2 form-control" name="accountId">
                                                <option value="">--Select Account--</option>
                                                @foreach ($accountLookUp as $account)
                                                    <option value="{{ $account->id }}"
                                                        {{ $accountId == $account->id ? 'selected' : '' }}>
                                                        {{ $account->accountdescription }} ({{ $account->accountno }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Bank</label>
                                            <?php if ($bankid == '') {
                                                $bankid = old('bankid');
                                            } ?>
                                            <select class="select2 form-control" name="bankid">
                                                <option value="">--Select Bank--</option>
                                                @foreach ($banks as $bank)
                                                    <option value="{{ $bank->bankID }}"
                                                        {{ $bankid == $bank->bankID ? 'selected' : '' }}>
                                                        {{ $bank->bank }} ({{ $bank->bankCode }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Bank Account Name</label>
                                            <?php if ($bankAccountName == '') {
                                                $bankAccountName = old('bank_account_name');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $bankAccountName }}"
                                                name="bank_account_name">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Bank Account Number</label>
                                            <?php if ($bankAccountNumber == '') {
                                                $bankAccountNumber = old('bank_account_number');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $bankAccountNumber }}"
                                                name="bank_account_number">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Currency</label>
                                            <?php if ($currency == '') {
                                                $currency = old('currency');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $currency }}"
                                                name="currency">
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <label>Address</label>
                                            <?php if ($address == '') {
                                                $address = old('address');
                                            } ?>
                                            <textarea class="form-control" rows="2" name="address">{{ $address }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Scope of Work</label>
                                            <?php if ($description == '') {
                                                $description = old('description');
                                            } ?>
                                            <textarea class="form-control" rows="3" name="description">{{ $description }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary" name="addnew">Create</button>
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
                            <h4 class="card-title">Vendors</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Vendor Name</th>
                                            <th>Vendor ID</th>
                                            <th>Type</th>
                                            <th>Category</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Contact Person</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1; @endphp
                                        @foreach ($vendors as $vendor)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $vendor->name }}</td>
                                                <td>{{ $vendor->vendorId }}</td>
                                                <td>{{ $vendor->vendorTypeName ?? 'N/A' }}</td>
                                                <td>{{ $vendor->vendorCategoryName ?? 'N/A' }}</td>
                                                <td>{{ $vendor->email ?? 'N/A' }}</td>
                                                <td>{{ $vendor->contact_phone_number ?? 'N/A' }}</td>
                                                <td>{{ $vendor->contact_person ?? 'N/A' }}</td>
                                                <td>
                                                    @if ($vendor->status == 'Active')
                                                        <span class="badge bg-success">Active</span>
                                                    @elseif ($vendor->status == 'On Hold')
                                                        <span class="badge bg-warning">On Hold</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a class="btn btn-sm bg-success-light"
                                                        href="javascript: editfunc(
                                                            '{{ $vendor->id }}',
                                                            '{{ addslashes($vendor->name) }}',
                                                            '{{ addslashes($vendor->vendorId) }}',
                                                            '{{ addslashes($vendor->trade_name ?? '') }}',
                                                            '{{ $vendor->vendor_type ?? '' }}',
                                                            '{{ addslashes($vendor->tax_number ?? '') }}',
                                                            '{{ $vendor->vendor_category ?? '' }}',
                                                            '{{ addslashes($vendor->address ?? '') }}',
                                                            '{{ addslashes($vendor->email ?? '') }}',
                                                            '{{ addslashes($vendor->contact_phone_number ?? '') }}',
                                                            '{{ addslashes($vendor->contact_person ?? '') }}',
                                                            '{{ $vendor->bankid ?? '' }}',
                                                            '{{ addslashes($vendor->bank_account_name ?? '') }}',
                                                            '{{ addslashes($vendor->bank_account_number ?? '') }}',
                                                            '{{ addslashes($vendor->currency ?? '') }}',
                                                            '{{ addslashes($vendor->description ?? '') }}',
                                                            '{{ $vendor->accountId ?? '' }}',
                                                            '{{ $vendor->status ?? 'Active' }}'
                                                        )">
                                                        <i class="fe fe-pencil"></i>
                                                    </a>
                                                    <a class="btn btn-sm bg-danger-light"
                                                        href="javascript: deletefunc('{{ $vendor->id }}')">
                                                        <i class="fe fe-trash"></i>
                                                    </a>
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
        </div>

        <div class="modal fade" id="edit_details" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Vendor</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Vendor Name <span class="text-danger">*</span></label>
                                        <input type="text" id="name" name="name" class="form-control"
                                            autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Vendor ID</label>
                                        <input type="text" id="vendorId" name="vendorId" class="form-control"
                                            autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Trade Name</label>
                                        <input type="text" id="trade_name" name="trade_name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tax Number</label>
                                        <input type="text" id="tax_number" name="tax_number" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Vendor Type</label>
                                        <select class="select2 form-control" id="vendor_type" name="vendor_type">
                                            <option value="">--Select Type--</option>
                                            @foreach ($vendorTypes as $type)
                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Vendor Category</label>
                                        <select class="select2 form-control" id="vendor_category" name="vendor_category">
                                            <option value="">--Select Category--</option>
                                            @foreach ($vendorCategories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="Active">Active</option>
                                            <option value="On Hold">On Hold</option>
                                            <option value="Inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" id="email" name="email" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Contact Phone Number</label>
                                        <input type="text" id="contact_phone_number" name="contact_phone_number"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Contact Person</label>
                                        <input type="text" id="contact_person" name="contact_person"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Account</label>
                                        <select class="select2 form-control" id="accountId" name="accountId">
                                            <option value="">--Select Account--</option>
                                            @foreach ($accountLookUp as $account)
                                                <option value="{{ $account->id }}">
                                                    {{ $account->accountdescription }} ({{ $account->accountno }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Bank</label>
                                        <select class="select2 form-control" id="bankid" name="bankid">
                                            <option value="">--Select Bank--</option>
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->bankID }}">
                                                    {{ $bank->bank }} ({{ $bank->bankCode }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Bank Account Name</label>
                                        <input type="text" id="bank_account_name" name="bank_account_name"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Bank Account Number</label>
                                        <input type="text" id="bank_account_number" name="bank_account_number"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Currency</label>
                                        <input type="text" id="currency" name="currency" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <textarea class="form-control" rows="2" id="address" name="address"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Scope of Work</label>
                                <textarea class="form-control" rows="3" id="description" name="description"></textarea>
                            </div>

                            <input type="hidden" id="id" name="id">
                            <div class="form-content p-2">
                                <button type="submit" class="btn btn-primary" name="update">Save Changes</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="delete_modal" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form method="post">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="form-content p-2">
                                <h4 class="modal-title">Delete</h4>
                                <p class="mb-4">Are you sure want to delete this vendor?</p>
                                <button type="submit" class="btn btn-primary" name="del">Continue </button>
                                <input type="hidden" id="deleteid" name="deleteid">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
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
        function editfunc(id, name, vendorId, tradeName, vendorType, taxNumber, vendorCategory, address, email,
            contactPhoneNumber, contactPerson, bankid, bankAccountName, bankAccountNumber, currency, description, accountId,
            status) {
            document.getElementById('id').value = id;
            document.getElementById('name').value = name || '';
            document.getElementById('vendorId').value = vendorId || '';
            document.getElementById('trade_name').value = tradeName || '';
            document.getElementById('tax_number').value = taxNumber || '';
            document.getElementById('address').value = address || '';
            document.getElementById('email').value = email || '';
            document.getElementById('contact_phone_number').value = contactPhoneNumber || '';
            document.getElementById('contact_person').value = contactPerson || '';
            document.getElementById('bank_account_name').value = bankAccountName || '';
            document.getElementById('bank_account_number').value = bankAccountNumber || '';
            document.getElementById('currency').value = currency || '';
            document.getElementById('description').value = description || '';
            document.getElementById('status').value = status || 'Active';

            $('#vendor_type').val(vendorType || '').trigger('change');
            $('#vendor_category').val(vendorCategory || '').trigger('change');
            $('#bankid').val(bankid || '').trigger('change');
            $('#accountId').val(accountId || '').trigger('change');

            $("#edit_details").modal('show');
        }

        function deletefunc(id) {
            document.getElementById('deleteid').value = id;
            $("#delete_modal").modal('show');
        }
    </script>
@endsection
<!-- /Page Wrapper -->
