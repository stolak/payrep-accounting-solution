<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Project Invoice Management
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
                            <li class="breadcrumb-item active">Project Invoice Management</li>
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
                                            <select class="select2 form-control" name="projectId" id="projectId" required
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
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Add Invoice</h4>
                            </div>
                            <div class="card-body">
                                <form method="post" id="addInvoiceForm">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="projectId" value="{{ $projectId }}">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Invoice Number <span class="text-danger">*</span></label>
                                                <?php if ($InvoiceNumber == '') {
                                                    $InvoiceNumber = old('InvoiceNumber');
                                                } ?>
                                                <input type="text" class="form-control" value="{{ $InvoiceNumber }}"
                                                    name="InvoiceNumber" id="InvoiceNumber" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Project Name <span class="text-danger">*</span></label>

                                                <input type="text" class="form-control" value="{{ $projectName ?? '' }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Client Name <span class="text-danger">*</span></label>

                                                <input type="text" class="form-control" value="{{ $clientName ?? '' }}"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Amount <span class="text-danger">*</span></label>
                                                <?php if ($amount == '') {
                                                    $amount = old('amount');
                                                } ?>
                                                <input type="number" class="form-control" value="{{ $amount }}"
                                                    name="amount" id="amount" step="0.01" min="0" required
                                                    oninput="calculateExpectedAmount()">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Due Date <span class="text-danger">*</span></label>
                                                <?php if ($dueDate == '') {
                                                    $dueDate = old('dueDate');
                                                } ?>
                                                <input type="date" class="form-control" value="{{ $dueDate }}"
                                                    name="dueDate" id="dueDate" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>VAT (%)</label>
                                                <?php if ($vat == '') {
                                                    $vat = old('vat');
                                                } ?>
                                                <input type="number" class="form-control" value="{{ $vat }}"
                                                    name="vat" id="vat" step="0.01" min="0"
                                                    max="100" oninput="calculateExpectedAmount()">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>WHT (%)</label>
                                                <?php if ($wht == '') {
                                                    $wht = old('wht');
                                                } ?>
                                                <input type="number" class="form-control" value="{{ $wht }}"
                                                    name="wht" id="wht" step="0.01" min="0"
                                                    max="100" oninput="calculateExpectedAmount()">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Expected Amount</label>
                                                <input type="number" class="form-control" value="{{ $expectedAmount }}"
                                                    name="expectedAmount" id="expectedAmount" step="0.01" readonly
                                                    style="background-color: #f0f0f0;">
                                                <small class="text-muted">Calculated: Amount + VAT - WHT</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <?php if ($status == '') {
                                                    $status = old('status', 'Pending');
                                                } ?>
                                                <select class="form-control" name="status" id="status">
                                                    <option value="Pending" {{ $status == 'Pending' ? 'selected' : '' }}>
                                                        Pending</option>
                                                    <option value="Validated"
                                                        {{ $status == 'Validated' ? 'selected' : '' }}>Validated</option>
                                                    <option value="Approved"
                                                        {{ $status == 'Approved' ? 'selected' : '' }}>Approved</option>
                                                    <option value="Rejected"
                                                        {{ $status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary" name="addnew">Add
                                            Invoice</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <!-- List of invoices -->
                        <div class="card card-table">
                            <div class="card-header">
                                <h4 class="card-title">Invoices</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-center mb-0">
                                        <thead>
                                            <tr>
                                                <th rowspan="1">S/N</th>
                                                <th rowspan="1">Invoice Number</th>
                                                <th rowspan="1">Amount</th>
                                                <th rowspan="1">VAT (%)</th>
                                                <th rowspan="1">WHT (%)</th>
                                                <th rowspan="1">Expected Amount</th>
                                                <th rowspan="1">Due Date</th>
                                                <th rowspan="1">Status</th>
                                                <th rowspan="1">Created By</th>
                                                <th rowspan="1">Validated By</th>
                                                <th rowspan="1">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $i = 1;
                                            @endphp

                                            @if ($invoices->count() > 0)
                                                @foreach ($invoices as $invoice)
                                                    <tr>
                                                        <td>
                                                            {{ $i++ }}
                                                        </td>
                                                        <td>
                                                            <strong>{{ $invoice->InvoiceNumber }}</strong>
                                                        </td>
                                                        <td style="text-align: right;">
                                                            {{ number_format($invoice->amount, 2, '.', ',') }}
                                                        </td>
                                                        <td style="text-align: right;">
                                                            {{ number_format($invoice->vat, 2, '.', ',') }}%
                                                        </td>
                                                        <td style="text-align: right;">
                                                            {{ number_format($invoice->wht, 2, '.', ',') }}%
                                                        </td>
                                                        <td style="text-align: right;">
                                                            <strong>{{ number_format($invoice->expectedAmount, 2, '.', ',') }}</strong>
                                                        </td>
                                                        <td>
                                                            {{ date('Y-m-d', strtotime($invoice->dueDate)) }}
                                                        </td>
                                                        <td>
                                                            @if ($invoice->status == 'Approved')
                                                                <span class="badge bg-success">Approved</span>
                                                            @elseif ($invoice->status == 'Validated')
                                                                <span class="badge bg-info">Validated</span>
                                                            @elseif ($invoice->status == 'Rejected')
                                                                <span class="badge bg-danger">Rejected</span>
                                                            @else
                                                                <span class="badge bg-warning">Pending</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ $invoice->createdByName ?? 'N/A' }}
                                                        </td>
                                                        <td>
                                                            {{ $invoice->validatedByName ?? '-' }}
                                                            @if ($invoice->validatedAt)
                                                                <br><small
                                                                    class="text-muted">{{ date('Y-m-d', strtotime($invoice->validatedAt)) }}</small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a class="btn btn-sm bg-success-light"
                                                                href="javascript: editfunc('{{ $invoice->id }}','{{ $invoice->InvoiceNumber }}','{{ $invoice->amount }}','{{ $invoice->vat }}','{{ $invoice->wht }}','{{ $invoice->dueDate }}','{{ $invoice->status }}')">
                                                                <i class="fe fe-pencil"></i>
                                                            </a>
                                                            <a class="btn btn-sm bg-danger-light"
                                                                href="javascript: deletefunc('{{ $invoice->id }}')">
                                                                <i class="fe fe-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="11" class="text-center">No invoices added for this
                                                        project yet.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /List of invoices -->
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <p class="text-center text-muted">Please select a project to view and manage its invoices.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="edit_modal" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form method="post" id="editForm">
                        {{ csrf_field() }}
                        <input type="hidden" name="projectId" value="{{ $projectId }}">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Invoice</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Invoice Number <span class="text-danger">*</span></label>
                                        <input type="text" id="edit_InvoiceNumber" name="InvoiceNumber"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Amount <span class="text-danger">*</span></label>
                                        <input type="number" id="edit_amount" name="amount" class="form-control"
                                            step="0.01" min="0" required
                                            oninput="calculateEditExpectedAmount()">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>VAT (%)</label>
                                        <input type="number" id="edit_vat" name="vat" class="form-control"
                                            step="0.01" min="0" max="100"
                                            oninput="calculateEditExpectedAmount()">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>WHT (%)</label>
                                        <input type="number" id="edit_wht" name="wht" class="form-control"
                                            step="0.01" min="0" max="100"
                                            oninput="calculateEditExpectedAmount()">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Expected Amount</label>
                                        <input type="number" id="edit_expectedAmount" name="expectedAmount"
                                            class="form-control" step="0.01" readonly
                                            style="background-color: #f0f0f0;">
                                        <small class="text-muted">Calculated: Amount + VAT - WHT</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Due Date <span class="text-danger">*</span></label>
                                        <input type="date" id="edit_dueDate" name="dueDate" class="form-control"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" id="edit_status" name="status">
                                            <option value="Pending">Pending</option>
                                            <option value="Validated">Validated</option>
                                            <option value="Approved">Approved</option>
                                            <option value="Rejected">Rejected</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="edit_id" name="id">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" name="update">Save Changes</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Edit Modal -->

        <!-- Delete Modal -->
        <div class="modal fade" id="delete_modal" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form method="post" id="deleteForm">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="form-content p-2">
                                <h4 class="modal-title">Delete</h4>
                                <p class="mb-4">Are you sure want to delete this invoice?</p>
                                <button type="submit" class="btn btn-primary" name="del">Continue </button>
                                <input type="hidden" id="deleteid" name="deleteid">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Delete Modal -->

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
        function selectProject() {
            var projectId = document.getElementById('projectId').value;
            if (projectId) {
                document.getElementById('projectSelectForm').submit();
            }
        }

        function calculateExpectedAmount() {
            var amount = parseFloat(document.getElementById('amount').value) || 0;
            var vat = parseFloat(document.getElementById('vat').value) || 0;
            var wht = parseFloat(document.getElementById('wht').value) || 0;

            var vatAmount = (amount * vat) / 100;
            var whtAmount = (amount * wht) / 100;
            var expectedAmount = amount + vatAmount - whtAmount;

            document.getElementById('expectedAmount').value = expectedAmount.toFixed(2);
        }

        function calculateEditExpectedAmount() {
            var amount = parseFloat(document.getElementById('edit_amount').value) || 0;
            var vat = parseFloat(document.getElementById('edit_vat').value) || 0;
            var wht = parseFloat(document.getElementById('edit_wht').value) || 0;

            var vatAmount = (amount * vat) / 100;
            var whtAmount = (amount * wht) / 100;
            var expectedAmount = amount + vatAmount - whtAmount;

            document.getElementById('edit_expectedAmount').value = expectedAmount.toFixed(2);
        }

        function editfunc(id, invoiceNumber, amount, vat, wht, dueDate, status) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_InvoiceNumber').value = invoiceNumber;
            document.getElementById('edit_amount').value = amount;
            document.getElementById('edit_vat').value = vat || '';
            document.getElementById('edit_wht').value = wht || '';
            document.getElementById('edit_dueDate').value = dueDate;
            document.getElementById('edit_status').value = status || 'Pending';

            // Calculate expected amount
            calculateEditExpectedAmount();

            $("#edit_modal").modal('show')
        }

        function deletefunc(id) {
            document.getElementById('deleteid').value = id;
            $("#delete_modal").modal('show')
        }

        // Initialize calculation on page load if form is filled
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('amount').value) {
                calculateExpectedAmount();
            }
        });
    </script>
@endsection
<!-- /Page Wrapper -->
