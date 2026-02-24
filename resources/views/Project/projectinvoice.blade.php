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
                                                <label>Invoice Number</label>
                                                <?php if ($InvoiceNumber == '') {
                                                    $InvoiceNumber = old('InvoiceNumber');
                                                } ?>
                                                <input type="text" class="form-control" value=""
                                                    name="InvoiceNumber" id="InvoiceNumber" readonly
                                                    style="background-color: #f0f0f0;"
                                                    placeholder="Auto-generated after you save">
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
                                        <div class="col-md-12">
                                            <label>Invoice Items <span class="text-danger">*</span></label>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 48%;">Description</th>
                                                            <th style="width: 14%;">Qty</th>
                                                            <th style="width: 16%;">Price</th>
                                                            <th style="width: 16%;">Subtotal</th>
                                                            <th style="width: 6%;">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="add-items-body">
                                                        @php
                                                            $oldDescriptions = old('item_description', ['']);
                                                            $oldQty = old('item_quantity', ['']);
                                                            $oldPrice = old('item_price', ['']);
                                                        @endphp
                                                        @foreach ($oldDescriptions as $idx => $oldDescription)
                                                            <tr class="item-row">
                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control item-description"
                                                                        name="item_description[]"
                                                                        value="{{ $oldDescription }}" required>
                                                                </td>
                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control item-qty add-item-input"
                                                                        name="item_quantity[]"
                                                                        value="{{ $oldQty[$idx] ?? '' }}" required>
                                                                </td>
                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control item-price add-item-input"
                                                                        name="item_price[]"
                                                                        value="{{ $oldPrice[$idx] ?? '' }}" required>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control item-subtotal"
                                                                        value="0.00" step="0.01" readonly
                                                                        style="background-color: #f0f0f0;">
                                                                </td>
                                                                <td class="text-center">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-danger remove-item-btn">
                                                                        <i class="fe fe-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-secondary"
                                                onclick="addItemRow('add')">
                                                <i class="fe fe-plus"></i> Add Item
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Amount (Auto) <span class="text-danger">*</span>
                                                    <span class="ml-2">
                                                        <input type="checkbox" name="vatInclude" id="vatInclude"
                                                            value="1" onchange="calculateExpectedAmount()"
                                                            {{ old('vatInclude', true) ? 'checked' : '' }}>
                                                        VAT Inclusive
                                                    </span>
                                                </label>
                                                <input type="text" class="form-control" value="0.00" name="amount"
                                                    id="amount" step="0.01" readonly
                                                    style="background-color: #f0f0f0;">
                                                <small class="text-muted">Sum of item subtotals.</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">

                                            <div class="form-group">
                                                <label>VAT</label>
                                                <div class="d-flex align-items-center">
                                                    <select name="vatType" id="vatType" class="form-control"
                                                        style="width: auto; margin-right: 10px;"
                                                        onchange="calculateExpectedAmount()">
                                                        <?php
                                                        if ($vat == '') {
                                                            $vat = old('vat');
                                                        }
                                                        $selectedVat = $vat == '10' ? '10' : '7.5';
                                                        ?>
                                                        <option value="7.5"
                                                            {{ $selectedVat == '7.5' ? 'selected' : '' }}>7.5%</option>
                                                        <option value="10"
                                                            {{ $selectedVat == '10' ? 'selected' : '' }}>10%</option>
                                                    </select>
                                                    <input type="hidden" name="vat" id="vat_percentage"
                                                        value="{{ $vat }}">
                                                    <input type="text" class="form-control" value="0.00"
                                                        id="vat_amount" step="0.01" readonly
                                                        style="flex: 1; background-color: #f0f0f0;">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>WHT</label>
                                                <div class="d-flex align-items-center">
                                                    <select name="whtType" id="whtType" class="form-control"
                                                        style="width: auto; margin-right: 10px;"
                                                        onchange="calculateExpectedAmount()">
                                                        <?php
                                                        if ($wht == '') {
                                                            $wht = old('wht');
                                                        }
                                                        $selectedWht = $wht == '10' ? '10' : '7.5';
                                                        ?>
                                                        <option value="7.5"
                                                            {{ $selectedWht == '7.5' ? 'selected' : '' }}>7.5%</option>
                                                        <option value="10"
                                                            {{ $selectedWht == '10' ? 'selected' : '' }}>10%</option>
                                                    </select>
                                                    <input type="hidden" name="wht" id="wht_percentage"
                                                        value="{{ $wht }}">
                                                    <input type="text" class="form-control" value="0.00"
                                                        id="wht_amount" step="0.01" min="0" readonly
                                                        style="flex: 1; background-color: #f0f0f0;">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Receivable Amount</label>
                                                <input type="text" class="form-control" value="{{ $expectedAmount }}"
                                                    name="expectedAmount" id="expectedAmount" step="0.01" readonly
                                                    style="background-color: #f0f0f0;">
                                                <small class="text-muted">Calculated based on VAT Include option</small>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
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
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Notes</label>
                                                <textarea class="form-control" name="notes" id="notes" rows="3" placeholder="Additional notes...">{{ old('notes') }}</textarea>
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
                                                <th rowspan="1">Items</th>
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
                                                        <td>
                                                            <strong>{{ $invoice->itemCount ?? 0 }} item(s)</strong>
                                                            @if (!empty($invoice->items) && count($invoice->items) > 0)
                                                                <ul class="mb-0 mt-1 pl-3">
                                                                    @foreach ($invoice->items as $item)
                                                                        <li>
                                                                            {{ $item->description }}
                                                                            ({{ number_format($item->quantity, 2, '.', ',') }}
                                                                            x
                                                                            {{ number_format($item->price, 2, '.', ',') }})
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
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
                                                            <a class="btn btn-sm bg-primary-light"
                                                                href="{{ url('/project-invoice-view') }}?invoiceId={{ $invoice->id }}"
                                                                target="_blank" rel="noopener noreferrer"
                                                                title="View Invoice">
                                                                <i class="fa fa-file"></i>
                                                            </a>
                                                            @if ($invoice->status != 'Approved')
                                                                <a class="btn btn-sm bg-success-light"
                                                                    href="javascript: editfunc('{{ $invoice->id }}')"
                                                                    title="Edit">
                                                                    <i class="fe fe-pencil"></i>
                                                                </a>
                                                                <a class="btn btn-sm bg-info-light"
                                                                    href="javascript: approvefunc('{{ $invoice->id }}')"
                                                                    title="Approve">
                                                                    <i class="fe fe-check"></i>
                                                                </a>
                                                                <a class="btn btn-sm bg-danger-light"
                                                                    href="javascript: deletefunc('{{ $invoice->id }}')"
                                                                    title="Delete">
                                                                    <i class="fe fe-trash"></i>
                                                                </a>
                                                            @else
                                                                <span class="text-muted">Locked</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="12" class="text-center">No invoices added for this
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
                                        <label>Invoice Number</label>
                                        <input type="text" id="edit_InvoiceNumber" name="InvoiceNumber"
                                            class="form-control" readonly style="background-color: #f0f0f0;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Amount (Auto) <span class="text-danger">*</span></label>
                                        <input type="text" id="edit_amount" name="amount" class="form-control"
                                            step="0.01" required readonly style="background-color: #f0f0f0;"
                                            oninput="calculateEditExpectedAmount()">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Invoice Items <span class="text-danger">*</span></label>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th style="width: 48%;">Description</th>
                                                    <th style="width: 14%;">Qty</th>
                                                    <th style="width: 16%;">Price</th>
                                                    <th style="width: 16%;">Subtotal</th>
                                                    <th style="width: 6%;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="edit-items-body"></tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="addItemRow('edit')">
                                        <i class="fe fe-plus"></i> Add Item
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="vatInclude" id="edit_vatInclude" value="1"
                                                onchange="calculateEditExpectedAmount()">
                                            VAT Include
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>VAT (%)</label>
                                        <input type="number" id="edit_vat" name="vat" class="form-control"
                                            step="0.01" min="0" max="100"
                                            oninput="calculateEditExpectedAmount()">
                                    </div>
                                    <div class="form-group">
                                        <label>WHT (%)</label>
                                        <input type="number" id="edit_wht" name="wht" class="form-control"
                                            step="0.01" min="0" max="100"
                                            oninput="calculateEditExpectedAmount()">
                                    </div>
                                    <div class="form-group">
                                        <label>Payable</label>
                                        <input type="text" id="edit_expectedAmount" name="expectedAmount"
                                            class="form-control" step="0.01" readonly
                                            style="background-color: #f0f0f0;">
                                        <small class="text-muted">Calculated based on VAT Include option</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Due Date <span class="text-danger">*</span></label>
                                        <input type="date" id="edit_dueDate" name="dueDate" class="form-control"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label>Notes</label>
                                        <textarea class="form-control" name="notes" id="edit_notes" rows="3" placeholder="Additional notes..."></textarea>
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

        <!-- Approve Modal -->
        <div class="modal fade" id="approve_modal" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form method="post" id="approveForm">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="form-content p-2">
                                <h4 class="modal-title">Approve Invoice</h4>
                                <p class="mb-4">Are you sure you want to approve this invoice? Once approved, it cannot
                                    be
                                    edited or deleted.</p>
                                <button type="submit" class="btn btn-primary" name="approve">Approve</button>
                                <input type="hidden" id="approveid" name="approveid">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Approve Modal -->

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
        @php
            $invoiceEditData = ($invoices ?? collect())
                ->mapWithKeys(function ($invoice) {
                    return [
                        $invoice->id => [
                            'id' => $invoice->id,
                            'invoiceNumber' => $invoice->InvoiceNumber,
                            'vat' => (float) ($invoice->vat ?? 0),
                            'wht' => (float) ($invoice->wht ?? 0),
                            'isVatInclusive' => (int) ($invoice->isVatInclusive ?? 0),
                            'dueDate' => $invoice->dueDate ? date('Y-m-d', strtotime($invoice->dueDate)) : null,
                            'status' => $invoice->status ?? 'Pending',
                            'items' => collect($invoice->items ?? collect())
                                ->map(function ($item) {
                                    return [
                                        'description' => $item->description,
                                        'quantity' => (float) $item->quantity,
                                        'price' => (float) $item->price,
                                    ];
                                })
                                ->values()
                                ->toArray(),
                        ],
                    ];
                })
                ->toArray();
        @endphp
        const invoiceEditData = @json($invoiceEditData);

        function selectProject() {
            var projectId = document.getElementById('projectId').value;
            if (projectId) {
                document.getElementById('projectSelectForm').submit();
            }
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text ?? '').replace(/[&<>"']/g, (m) => map[m]);
        }

        // Numeric formatting (ported from vendorproject.blade.php)
        function splitNumericParts(rawValue) {
            let cleaned = String(rawValue ?? '').replace(/,/g, '').replace(/[^\d.]/g, '');
            const firstDotIndex = cleaned.indexOf('.');
            const hasDot = firstDotIndex !== -1;
            const hasTrailingDot = hasDot && cleaned.endsWith('.');

            if (hasDot) {
                cleaned = cleaned.slice(0, firstDotIndex + 1) + cleaned.slice(firstDotIndex + 1).replace(/\./g, '');
            }

            const parts = cleaned.split('.');
            const integerPart = parts[0] || '';
            const decimalPart = parts.length > 1 ? parts[1].slice(0, 2) : '';

            return {
                integerPart,
                decimalPart,
                hasDot,
                hasTrailingDot,
            };
        }

        function normalizeNumericInput(rawValue) {
            const parts = splitNumericParts(rawValue);
            const intPart = (parts.integerPart || '0').replace(/^0+(?=\d)/, '') || '0';
            if (parts.decimalPart !== '') {
                return `${intPart}.${parts.decimalPart}`;
            }
            return intPart;
        }

        function formatNumberForDisplay(rawValue) {
            const parts = splitNumericParts(rawValue);
            if (!parts.integerPart && !parts.hasDot) return '';

            const intPart = (parts.integerPart || '0').replace(/^0+(?=\d)/, '');
            const withCommas = (intPart || '0').replace(/\B(?=(\d{3})+(?!\d))/g, ',');

            if (parts.hasDot) {
                return parts.decimalPart !== '' ? `${withCommas}.${parts.decimalPart}` : `${withCommas}.`;
            }
            return withCommas;
        }

        function formatCurrency(amount) {
            const numeric = Number(amount) || 0;
            return numeric.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        }

        function parseFormattedNumber(rawValue) {
            const normalized = normalizeNumericInput(rawValue);
            if (normalized === '') return 0;
            const parsed = parseFloat(normalized);
            return Number.isFinite(parsed) ? parsed : 0;
        }

        function applyNumberFormatting(input) {
            if (!input) return;
            const formatted = formatNumberForDisplay(input.value);
            input.value = formatted;
        }

        function itemRowTemplate(prefix, data = {}) {
            const desc = escapeHtml(data.description || '');
            const qty = data.quantity !== undefined && data.quantity !== null ? formatNumberForDisplay(data.quantity) : '';
            const price = data.price !== undefined && data.price !== null ? formatNumberForDisplay(data.price) : '';
            return `
                <tr class="item-row">
                    <td><input type="text" class="form-control item-description" name="item_description[]" value="${desc}" required></td>
                    <td><input type="text" class="form-control item-qty ${prefix}-item-input" name="item_quantity[]" value="${qty}" required></td>
                    <td><input type="text" class="form-control item-price ${prefix}-item-input" name="item_price[]" value="${price}" required></td>
                    <td><input type="text" class="form-control item-subtotal" value="0.00" readonly step="0.01" style="background-color:#f0f0f0;"></td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-item-btn"><i class="fe fe-trash"></i></button></td>
                </tr>
            `;
        }

        function addItemRow(prefix, data = {}) {
            const body = document.getElementById(prefix + '-items-body');
            if (!body) return;
            body.insertAdjacentHTML('beforeend', itemRowTemplate(prefix, data));
            recalculateInvoiceTotals(prefix);
        }

        function recalculateInvoiceTotals(prefix) {
            let total = 0;
            document.querySelectorAll('#' + prefix + '-items-body tr').forEach((row) => {
                const qty = parseFormattedNumber(row.querySelector('.item-qty')?.value);
                const price = parseFormattedNumber(row.querySelector('.item-price')?.value);
                const lineSubtotal = qty * price;
                const subtotalInput = row.querySelector('.item-subtotal');
                if (subtotalInput) subtotalInput.value = formatCurrency(lineSubtotal);
                total += lineSubtotal;
            });

            if (prefix === 'add') {
                const amountEl = document.getElementById('amount');
                if (amountEl) amountEl.value = formatCurrency(total);
                calculateExpectedAmount();
            } else if (prefix === 'edit') {
                const amountEl = document.getElementById('edit_amount');
                if (amountEl) amountEl.value = formatCurrency(total);
                calculateEditExpectedAmount();
            }
        }

        function calculateExpectedAmount() {
            var amount = parseFormattedNumber(document.getElementById('amount').value) || 0;
            var vatType = parseFloat(document.getElementById('vatType').value) || 0;
            var whtType = parseFloat(document.getElementById('whtType').value) || 0;
            var vatInclude = document.getElementById('vatInclude').checked;

            // Store percentages in hidden fields for form submission
            document.getElementById('vat_percentage').value = vatType;
            document.getElementById('wht_percentage').value = whtType;

            var vat = vatType;
            var wht = whtType;
            var vatAmount, whtAmount, expectedAmount, baseAmount;

            if (vatInclude) {
                // VAT is already included in the amount
                // Extract base amount: amount = base + (base * vat/100)
                // base = amount / (1 + vat/100)
                baseAmount = amount / (1 + (vat / 100));
                vatAmount = amount - baseAmount;
                whtAmount = (baseAmount * wht) / 100;
                expectedAmount = amount - whtAmount - vatAmount;
            } else {
                // VAT is added to the amount
                baseAmount = amount;
                vatAmount = (amount * vat) / 100;
                whtAmount = (amount * wht) / 100;
                expectedAmount = amount - vatAmount - whtAmount;
            }

            // Display calculated amounts in VAT and WHT fields
            document.getElementById('vat_amount').value = formatCurrency(vatAmount);
            document.getElementById('wht_amount').value = formatCurrency(whtAmount);
            document.getElementById('expectedAmount').value = formatCurrency(expectedAmount);
        }

        function calculateEditExpectedAmount() {
            var amount = parseFormattedNumber(document.getElementById('edit_amount').value) || 0;
            var vat = parseFloat(document.getElementById('edit_vat').value) || 0;
            var wht = parseFloat(document.getElementById('edit_wht').value) || 0;
            var vatInclude = document.getElementById('edit_vatInclude').checked;

            var vatAmount, whtAmount, expectedAmount;

            if (vatInclude) {
                // VAT is already included in the amount
                // Extract base amount: amount = base + (base * vat/100)
                // base = amount / (1 + vat/100)
                var baseAmount = amount / (1 + (vat / 100));
                vatAmount = amount - baseAmount;
                whtAmount = (baseAmount * wht) / 100;
                expectedAmount = amount - whtAmount - vatAmount;
            } else {
                // VAT is added to the amount
                vatAmount = (amount * vat) / 100;
                whtAmount = (amount * wht) / 100;
                expectedAmount = amount - vatAmount - whtAmount;
            }

            document.getElementById('edit_expectedAmount').value = formatCurrency(expectedAmount);
        }

        function editfunc(id) {
            const row = invoiceEditData[id];
            if (!row) return;

            document.getElementById('edit_id').value = row.id;
            document.getElementById('edit_InvoiceNumber').value = row.invoiceNumber || '';
            document.getElementById('edit_vat').value = row.vat ?? 0;
            document.getElementById('edit_wht').value = row.wht ?? 0;
            document.getElementById('edit_vatInclude').checked = (row.isVatInclusive ?? 0) == 1;
            document.getElementById('edit_dueDate').value = row.dueDate || '';
            document.getElementById('edit_status').value = row.status || 'Pending';

            const editBody = document.getElementById('edit-items-body');
            editBody.innerHTML = '';
            (row.items || []).forEach((item) => addItemRow('edit', item));
            if ((row.items || []).length === 0) {
                addItemRow('edit');
            }
            recalculateInvoiceTotals('edit');

            $("#edit_modal").modal('show')
        }

        function deletefunc(id) {
            document.getElementById('deleteid').value = id;
            $("#delete_modal").modal('show')
        }

        function approvefunc(id) {
            document.getElementById('approveid').value = id;
            $("#approve_modal").modal('show')
        }

        // Initialize calculation on page load if form is filled
        document.addEventListener('DOMContentLoaded', function() {
            if (document.querySelectorAll('#add-items-body tr').length === 0) {
                addItemRow('add');
            }
            document.querySelectorAll(
                'input[name="item_quantity[]"], input[name="item_price[]"], #amount, #vat_amount, #wht_amount, #expectedAmount'
            ).forEach((input) => applyNumberFormatting(input));
            recalculateInvoiceTotals('add');
        });

        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('add-item-input')) {
                applyNumberFormatting(e.target);
                recalculateInvoiceTotals('add');
            }
            if (e.target.classList.contains('edit-item-input')) {
                applyNumberFormatting(e.target);
                recalculateInvoiceTotals('edit');
            }
        });

        document.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.remove-item-btn');
            if (!removeBtn) return;
            const row = removeBtn.closest('tr');
            const parentId = row?.parentElement?.id || '';
            row?.remove();

            if (parentId === 'add-items-body') {
                if (document.querySelectorAll('#add-items-body tr').length === 0) {
                    addItemRow('add');
                }
                recalculateInvoiceTotals('add');
            }
            if (parentId === 'edit-items-body') {
                if (document.querySelectorAll('#edit-items-body tr').length === 0) {
                    addItemRow('edit');
                }
                recalculateInvoiceTotals('edit');
            }
        });

        function stripCommasBeforeSubmit(formId) {
            const form = document.getElementById(formId);
            if (!form) return;
            form.addEventListener('submit', function() {
                form.querySelectorAll(
                        'input[name="item_quantity[]"], input[name="item_price[]"], input[name="amount"]')
                    .forEach((input) => {
                        input.value = normalizeNumericInput(input.value);
                    });
            });
        }

        stripCommasBeforeSubmit('addInvoiceForm');
        stripCommasBeforeSubmit('editForm');
    </script>
@endsection
<!-- /Page Wrapper -->
