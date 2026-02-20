<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Project Setup
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
                            <li class="breadcrumb-item active">Project Setup</li>
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
                            <h4 class="card-title">Create Project</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" id="createProjectForm">
                                {{ csrf_field() }}
                                @php
                                    $oldExpenseClassificationLedger = old('expenseClassificationLedger', []);
                                    if (!is_array($oldExpenseClassificationLedger)) {
                                        $oldExpenseClassificationLedger = [];
                                    }
                                @endphp
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Project Code <span class="text-danger">*</span></label>
                                            <?php if ($projectCode == '') {
                                                $projectCode = old('projectCode');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $projectCode }}" required
                                                name="projectCode">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Project Name <span class="text-danger">*</span></label>
                                            <?php if ($name == '') {
                                                $name = old('name');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $name }}" required
                                                name="name">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Client</label>
                                            <?php if ($clientId == '') {
                                                $clientId = old('clientId');
                                            } ?>
                                            <select class="select2 form-control" id="create_clientId" name="clientId"
                                                onchange="loadClientLedgers(this.value, 'create_clientAccountId')">
                                                <option value="">--Select--</option>
                                                @foreach ($clients as $client)
                                                    <option value="{{ $client->id }}"
                                                        {{ $clientId == $client->id ? 'selected' : '' }}>
                                                        {{ $client->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Description</label>
                                            <?php if ($description == '') {
                                                $description = old('description');
                                            } ?>
                                            <textarea class="form-control" rows="3" name="description">{{ $description }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Category</label>
                                            <?php if ($categoryId == '') {
                                                $categoryId = old('categoryId');
                                            } ?>
                                            <select class="select2 form-control" id="create_categoryId" name="categoryId"
                                                onchange="renderExpenseClassificationLedgers(this.value, 'create_expenseClassificationLedgerContainer', oldExpenseClassificationLedgerMap)">
                                                <option value="">--Select--</option>
                                                @foreach ($projectCategories as $cat)
                                                    <option value="{{ $cat->id }}"
                                                        {{ $categoryId == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->category }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Client Ledger</label>
                                            <?php if ($clientAccountId == '') {
                                                $clientAccountId = old('clientAccountId');
                                            } ?>
                                            <select class="select2 form-control" id="create_clientAccountId"
                                                name="clientAccountId">
                                                <option value="">--Select Client First--</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Revenue Ledger</label>
                                            <?php if ($revenue_accountId == '') {
                                                $revenue_accountId = old('revenue_accountId');
                                            } ?>
                                            <select class="select2 form-control" name="revenue_accountId">
                                                <option value="">--Select--</option>
                                                @foreach ($revenueLookUp as $account)
                                                    <option value="{{ $account->id }}"
                                                        {{ $revenue_accountId == $account->id ? 'selected' : '' }}>
                                                        {{ $account->accountdescription }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Location</label>
                                            <?php if ($location == '') {
                                                $location = old('location');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $location }}"
                                                name="location">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Project Owner</label>
                                            <?php if ($project_owner == '') {
                                                $project_owner = old('project_owner');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $project_owner }}"
                                                name="project_owner">
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Project Expense Classification Ledgers</h6>
                                            </div>
                                            <div class="card-body" id="create_expenseClassificationLedgerContainer">
                                                <p class="text-muted mb-0">Select project category to load expense
                                                    classifications.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Purchase Orders Section -->
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <h5 class="mb-3">Purchase Orders <span class="text-danger">*</span> <small
                                                class="text-muted">(At least one PO with one line item is required)</small>
                                        </h5>
                                        <div id="po-container">
                                            @php
                                                $oldPoNumbers = old('po_poNumber', []);
                                                $oldPoDescriptions = old('po_description', []);
                                                $oldPoVats = old('po_vat', []);
                                                $oldPoItemDescriptions = old('po_item_description', []);
                                                $oldPoItemUomIds = old('po_item_uomId', []);
                                                $oldPoItemQties = old('po_item_qty', []);
                                                $oldPoItemUnitCosts = old('po_item_unitCost', []);
                                                $poCount = max(1, count($oldPoNumbers));
                                            @endphp
                                            @for ($i = 0; $i < $poCount; $i++)
                                                @php
                                                    $itemCount = max(1, count($oldPoItemDescriptions[$i] ?? []));
                                                @endphp
                                                <div class="po-item card mb-3" data-po-index="{{ $i }}">
                                                    <div class="card-header bg-light">
                                                        <h6 class="mb-0">PO #{{ $i + 1 }}</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <!-- PO Header -->
                                                        <div class="row mb-3">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>PO Number <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        name="po_poNumber[]"
                                                                        value="{{ $oldPoNumbers[$i] ?? '' }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div class="form-group">
                                                                    <label>PO Description <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        name="po_description[]"
                                                                        value="{{ $oldPoDescriptions[$i] ?? '' }}"
                                                                        required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label>VAT %</label>
                                                                    <input type="text"
                                                                        class="form-control po-header-vat" name="po_vat[]"
                                                                        step="0.01" min="0"
                                                                        value="{{ $oldPoVats[$i] ?? '' }}" max="100"
                                                                        oninput="calculatePoTotals({{ $i }})">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- PO Line Items -->
                                                        <h6 class="mb-2">Line Items <span class="text-danger">*</span>
                                                        </h6>
                                                        <div class="po-items-container"
                                                            data-po-index="{{ $i }}">
                                                            @for ($j = 0; $j < $itemCount; $j++)
                                                                <div class="po-line-item card mb-2"
                                                                    data-item-index="{{ $j }}">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-md-4">
                                                                                <div class="form-group">
                                                                                    <label>Item Description <span
                                                                                            class="text-danger">*</span></label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        name="po_item_description[{{ $i }}][]"
                                                                                        value="{{ $oldPoItemDescriptions[$i][$j] ?? '' }}"
                                                                                        required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <div class="form-group">
                                                                                    <label>UOM</label>
                                                                                    <select class="select2 form-control"
                                                                                        name="po_item_uomId[{{ $i }}][]">
                                                                                        <option value="">--Select--
                                                                                        </option>
                                                                                        @foreach ($uoms as $uom)
                                                                                            <option
                                                                                                value="{{ $uom->id }}"
                                                                                                {{ ($oldPoItemUomIds[$i][$j] ?? '') == $uom->id ? 'selected' : '' }}>
                                                                                                {{ $uom->measurement }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <div class="form-group">
                                                                                    <label>Qty <span
                                                                                            class="text-danger">*</span></label>
                                                                                    <input type="text"
                                                                                        class="form-control po-item-qty"
                                                                                        name="po_item_qty[{{ $i }}][]"
                                                                                        step="0.01" min="0"
                                                                                        value="{{ $oldPoItemQties[$i][$j] ?? '' }}"
                                                                                        required
                                                                                        oninput="calculatePoItemAmount({{ $i }}, {{ $j }})">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <div class="form-group">
                                                                                    <label>Unit Cost <span
                                                                                            class="text-danger">*</span></label>
                                                                                    <input type="text"
                                                                                        class="form-control po-item-unitCost"
                                                                                        name="po_item_unitCost[{{ $i }}][]"
                                                                                        step="0.01" min="0"
                                                                                        value="{{ $oldPoItemUnitCosts[$i][$j] ?? '' }}"
                                                                                        required
                                                                                        oninput="calculatePoItemAmount({{ $i }}, {{ $j }})">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <div class="form-group">
                                                                                    <label>Subtotal</label>
                                                                                    <input type="text"
                                                                                        class="form-control po-item-subcost"
                                                                                        readonly
                                                                                        style="background-color: #f0f0f0;">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="text-right">
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-danger remove-po-line-item"
                                                                                onclick="removePoLineItem({{ $i }}, this)"
                                                                                style="display: {{ $itemCount > 1 ? 'inline-block' : 'none' }};">
                                                                                <i class="fe fe-trash"></i> Remove Item
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endfor
                                                        </div>
                                                        <div class="mb-2">
                                                            <button type="button" class="btn btn-sm btn-secondary"
                                                                onclick="addPoLineItem({{ $i }})">
                                                                <i class="fe fe-plus"></i> Add Line Item
                                                            </button>
                                                        </div>

                                                        <!-- PO Totals -->
                                                        <div class="row mt-3 pt-3 border-top">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label><strong>Total Purchase Value</strong></label>
                                                                    <input type="text"
                                                                        class="form-control po-total-subcost" readonly
                                                                        style="background-color: #e9ecef; font-weight: bold;">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label><strong>VAT Amount</strong></label>
                                                                    <input type="text"
                                                                        class="form-control po-total-vatAmount" readonly
                                                                        style="background-color: #e9ecef; font-weight: bold;">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label><strong>Total PO Value</strong></label>
                                                                    <input type="text"
                                                                        class="form-control po-total-subnet" readonly
                                                                        style="background-color: #e9ecef; font-weight: bold;">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="text-right mt-2">
                                                            <button type="button" class="btn btn-sm btn-danger remove-po"
                                                                onclick="removePoItem(this)"
                                                                style="display: {{ $poCount > 1 ? 'inline-block' : 'none' }};">
                                                                <i class="fe fe-trash"></i> Remove PO
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>

                                    </div>
                                </div>
                                <!-- /Purchase Orders Section -->

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary" name="addnew">Create Project</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">

                    <!-- List of projects -->
                    <div class="card card-table">
                        <div class="card-header">
                            <h4 class="card-title">Projects</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th rowspan="1">S/N</th>
                                            <th rowspan="1">Project Code</th>
                                            <th rowspan="1">Name</th>
                                            <th rowspan="1">Client</th>
                                            <th rowspan="1">Client Ledger</th>
                                            <th rowspan="1">Category</th>
                                            <th rowspan="1">Revenue Ledger</th>
                                            <th rowspan="1">Description</th>
                                            <th rowspan="1">Location</th>
                                            <th rowspan="1">Project Owner</th>
                                            <th rowspan="1">Status</th>
                                            <th rowspan="1">PO(s)</th>
                                            <th rowspan="1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                        @endphp

                                        @foreach ($projects as $list)
                                            <tr>
                                                <td>
                                                    {{ $i++ }}
                                                </td>
                                                <td>
                                                    {{ $list->projectCode ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    {{ $list->name }}
                                                </td>
                                                <td>
                                                    {{ $list->clientName ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    {{ $list->clientAccountName ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    {{ $list->categoryName ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    {{ $list->revenueAccountName ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    {{ Str::limit($list->description ?? 'N/A', 50) }}
                                                </td>
                                                <td>
                                                    {{ $list->location ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    {{ $list->project_owner ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    @if ($list->status == 'Active')
                                                        <span class="badge bg-success">Active</span>
                                                    @elseif ($list->status == 'Inactive')
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $projectPos = $list->projectPos ?? collect();
                                                    @endphp
                                                    @if ($projectPos->count() > 0)
                                                        @foreach ($projectPos as $po)
                                                            <div class="mb-2">
                                                                <div>
                                                                    <strong>{{ $po->poNumber ?? 'N/A' }}</strong>
                                                                </div>
                                                                <div>{{ $po->description ?? 'N/A' }}</div>
                                                                <div>Amount:
                                                                    <strong>{{ number_format($po->subnet ?? 0, 2, '.', ',') }}</strong>
                                                                </div>
                                                                <div>
                                                                    @if (($po->status ?? '') == 'Approved')
                                                                        <span class="badge bg-success">Approved</span>
                                                                    @else
                                                                        <span class="badge bg-warning">Pending</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted">No PO(s)</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a class="btn btn-sm bg-success-light"
                                                        href="javascript: editfunc('{{ $list->id }}','{{ $list->projectCode }}','{{ addslashes($list->name) }}','{{ addslashes($list->description ?? '') }}','{{ $list->categoryId }}','{{ addslashes($list->location ?? '') }}','{{ addslashes($list->project_owner ?? '') }}','{{ $list->status }}','{{ $list->clientId ?? '' }}','{{ $list->clientAccountId ?? '' }}','{{ $list->revenue_accountId ?? '' }}',{{ json_encode($list->expenseClassificationLedger ?? []) }})">
                                                        <i class="fe fe-pencil"></i>
                                                    </a>
                                                    <a class="btn btn-sm bg-info-light"
                                                        href="{{ url('/project-po?projectId=' . $list->id) }}"
                                                        title="View PO">View PO
                                                        <i class="fe fe-eye"></i>
                                                    </a>
                                                    <a class="btn btn-sm bg-danger-light"
                                                        href="javascript: deletefunc('{{ $list->id }}')">
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
                    <!-- /List of projects -->

                </div>
            </div>
        </div>

        <!-- Edit Details Modal -->
        <div class="modal fade" id="edit_details" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Project</h5>
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
                                        <label>Project Code <span class="text-danger">*</span></label>
                                        <input type="text" id="projectCode" name="projectCode" class="form-control"
                                            style="text-align: left;" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Project Name <span class="text-danger">*</span></label>
                                        <input type="text" id="name" name="name" class="form-control"
                                            style="text-align: left;" autocomplete="off" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select class="form-control" id="categoryId" name="categoryId"
                                            onchange="renderExpenseClassificationLedgers(this.value, 'edit_expenseClassificationLedgerContainer', currentEditExpenseClassificationLedgerMap)">
                                            <option value="">--Select--</option>
                                            @foreach ($projectCategories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->category }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Client</label>
                                        <select class="form-control" id="clientId" name="clientId"
                                            onchange="loadClientLedgers(this.value, 'clientAccountId')">
                                            <option value="">--Select--</option>
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Client Ledger</label>
                                        <select class="form-control" id="clientAccountId" name="clientAccountId">
                                            <option value="">--Select Client First--</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Project Expense Classification Ledgers</h6>
                                        </div>
                                        <div class="card-body" id="edit_expenseClassificationLedgerContainer">
                                            <p class="text-muted mb-0">Select project category to load expense
                                                classifications.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Revenue Ledger</label>
                                        <select class="form-control" id="revenue_accountId" name="revenue_accountId">
                                            <option value="">--Select--</option>
                                            @foreach ($revenueLookUp as $account)
                                                <option value="{{ $account->id }}">{{ $account->accountdescription }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea class="form-control" rows="3" id="description" name="description"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Location</label>
                                        <input type="text" id="location" name="location" class="form-control"
                                            style="text-align: left;" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Project Owner</label>
                                        <input type="text" id="project_owner" name="project_owner" class="form-control"
                                            style="text-align: left;" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="Active" {{ $status == 'Active' ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="Inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" id="id" name="id">
                            <div class="form-content p-2">
                                <button type="submit" class="btn btn-primary " name="update">Save Changes</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Edit Details Modal -->

        <!-- Delete Modal -->
        <div class="modal fade" id="delete_modal" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form method="post">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="form-content p-2">
                                <h4 class="modal-title">Delete</h4>
                                <p class="mb-4">Are you sure want to delete?</p>
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
        const clientLedgers = @json($clientLedgers ?? []);
        const categoryExpenseClassifications = @json($categoryExpenseClassifications ?? []);
        const expenseLedgerLookUp = @json($accountLookUp ?? []);
        const oldExpenseClassificationLedgerMap = @json($oldExpenseClassificationLedger ?? []);
        let currentEditExpenseClassificationLedgerMap = {};

        function renderExpenseClassificationLedgers(categoryId, containerId, selectedMap = {}) {
            const container = document.getElementById(containerId);
            if (!container) return;

            container.innerHTML = '';
            if (!categoryId) {
                container.innerHTML =
                    '<p class="text-muted mb-0">Select project category to load expense classifications.</p>';
                return;
            }

            const classifications = categoryExpenseClassifications.filter(item => String(item.project_categoryId) ===
                String(categoryId));
            if (classifications.length === 0) {
                container.innerHTML =
                    '<p class="text-muted mb-0">No expense classification is mapped to this project category.</p>';
                return;
            }

            const row = document.createElement('div');
            row.className = 'row';

            classifications.forEach(classification => {
                const col = document.createElement('div');
                col.className = 'col-md-6';

                const selectedValue = selectedMap && selectedMap[classification.classificationId] ?
                    selectedMap[classification.classificationId] :
                    '';

                const options = ['<option value="">--Select Expense Ledger--</option>']
                    .concat(expenseLedgerLookUp.map(ledger => {
                        const selected = String(ledger.id) === String(selectedValue) ? 'selected' : '';
                        return `<option value="${ledger.id}" ${selected}>${ledger.accountdescription}</option>`;
                    }))
                    .join('');

                col.innerHTML = `
                    <div class="form-group">
                        <label>${classification.classificationName} <span class="text-danger">*</span></label>
                        <select class="form-control" name="expenseClassificationLedger[${classification.classificationId}]">
                            ${options}
                        </select>
                    </div>
                `;
                row.appendChild(col);
            });

            container.appendChild(row);
        }

        function loadClientLedgers(clientId, targetSelectId, selectedLedgerId = '') {
            const select = document.getElementById(targetSelectId);
            if (!select) return;

            const previousValue = selectedLedgerId || '';
            select.innerHTML = '<option value="">--Select--</option>';

            if (!clientId) {
                select.innerHTML = '<option value="">--Select Client First--</option>';
                if (window.jQuery && $(select).hasClass('select2-hidden-accessible')) {
                    $(select).trigger('change.select2');
                }
                return;
            }

            const filtered = clientLedgers.filter(item => String(item.clientId) === String(clientId));
            filtered.forEach(item => {
                const option = document.createElement('option');
                option.value = item.clientAccountId;
                option.textContent = `${item.accountno}-${item.accountdescription}`;
                if (String(item.clientAccountId) === String(previousValue)) {
                    option.selected = true;
                }
                select.appendChild(option);
            });

            if (window.jQuery && $(select).hasClass('select2-hidden-accessible')) {
                $(select).trigger('change.select2');
            }
        }

        function editfunc(id, projectCode, name, description, categoryId, location, projectOwner, status, clientId, clientAccountId,
            revenueAccountId, expenseClassificationLedgerMap) {
            document.getElementById('id').value = id;
            document.getElementById('projectCode').value = projectCode || '';
            document.getElementById('name').value = name;
            document.getElementById('description').value = description || '';
            document.getElementById('categoryId').value = categoryId || '';
            document.getElementById('location').value = location || '';
            document.getElementById('project_owner').value = projectOwner || '';
            document.getElementById('status').value = status || 1;
            document.getElementById('clientId').value = clientId || '';
            loadClientLedgers(clientId || '', 'clientAccountId', clientAccountId || '');
            document.getElementById('revenue_accountId').value = revenueAccountId || '';
            currentEditExpenseClassificationLedgerMap = expenseClassificationLedgerMap || {};
            renderExpenseClassificationLedgers(categoryId || '', 'edit_expenseClassificationLedgerContainer',
                currentEditExpenseClassificationLedgerMap);

            $("#edit_details").modal('show')
        }

        function deletefunc(id) {
            document.getElementById('deleteid').value = id;

            $("#delete_modal").modal('show')
        }

        let poIndex = {{ $poCount ?? 1 }};

        function splitNumericParts(rawValue) {
            let cleaned = String(rawValue ?? '').replace(/,/g, '').replace(/[^\d.]/g, '');
            const firstDotIndex = cleaned.indexOf('.');
            const hasDot = firstDotIndex !== -1;
            if (hasDot) {
                cleaned = cleaned.slice(0, firstDotIndex + 1) + cleaned.slice(firstDotIndex + 1).replace(/\./g, '');
            }
            const parts = cleaned.split('.');
            return {
                integerPart: parts[0] || '',
                decimalPart: parts.length > 1 ? parts[1].slice(0, 2) : '',
                hasDot,
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
            const intPart = (parts.integerPart || '0').replace(/^0+(?=\d)/, '') || '0';
            const withCommas = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            if (parts.hasDot) {
                return parts.decimalPart !== '' ? `${withCommas}.${parts.decimalPart}` : `${withCommas}.`;
            }
            return withCommas;
        }

        function parseFormattedNumber(rawValue) {
            const normalized = normalizeNumericInput(rawValue);
            if (normalized === '') return 0;
            const parsed = parseFloat(normalized);
            return Number.isFinite(parsed) ? parsed : 0;
        }

        function formatCurrency(amount) {
            const numeric = Number(amount) || 0;
            return numeric.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        }

        function applyNumberFormatting(input) {
            if (!input) return;
            input.value = formatNumberForDisplay(input.value);
        }

        // Recalculate all PO amounts on page load for old input values
        document.addEventListener('DOMContentLoaded', function() {
            loadClientLedgers('{{ $clientId ?? '' }}', 'create_clientAccountId', '{{ $clientAccountId ?? '' }}');
            renderExpenseClassificationLedgers('{{ $categoryId ?? '' }}',
                'create_expenseClassificationLedgerContainer',
                oldExpenseClassificationLedgerMap);

            const poItems = document.querySelectorAll('.po-item');
            poItems.forEach((poItem, poIdx) => {
                applyNumberFormatting(poItem.querySelector('.po-header-vat'));
                const lineItems = poItem.querySelectorAll('.po-line-item');
                lineItems.forEach((lineItem, itemIdx) => {
                    applyNumberFormatting(lineItem.querySelector('.po-item-qty'));
                    applyNumberFormatting(lineItem.querySelector('.po-item-unitCost'));
                    calculatePoItemAmount(poIdx, itemIdx);
                });
                calculatePoTotals(poIdx);
            });
        });

        // Calculate individual line item amount
        function calculatePoItemAmount(poIndex, itemIndex) {
            const poItem = document.querySelector(`.po-item[data-po-index="${poIndex}"]`);
            const lineItem = poItem.querySelector(`.po-line-item[data-item-index="${itemIndex}"]`);
            const qtyInput = lineItem.querySelector('.po-item-qty');
            const unitCostInput = lineItem.querySelector('.po-item-unitCost');

            applyNumberFormatting(qtyInput);
            applyNumberFormatting(unitCostInput);

            const qty = parseFormattedNumber(qtyInput.value);
            const unitCost = parseFormattedNumber(unitCostInput.value);
            const subcost = qty * unitCost;

            lineItem.querySelector('.po-item-subcost').value = formatCurrency(subcost);

            // Recalculate PO totals
            calculatePoTotals(poIndex);
        }

        // Calculate PO totals from all line items
        function calculatePoTotals(poIndex) {
            const poItem = document.querySelector(`.po-item[data-po-index="${poIndex}"]`);
            const lineItems = poItem.querySelectorAll('.po-line-item');

            let totalSubcost = 0;
            lineItems.forEach(lineItem => {
                const subcost = parseFormattedNumber(lineItem.querySelector('.po-item-subcost').value);
                totalSubcost += subcost;
            });

            const vatInput = poItem.querySelector('.po-header-vat');
            applyNumberFormatting(vatInput);
            const vat = parseFormattedNumber(vatInput.value);
            const vatAmount = totalSubcost * (vat / 100);
            const subnet = totalSubcost + vatAmount;

            poItem.querySelector('.po-total-subcost').value = formatCurrency(totalSubcost);
            poItem.querySelector('.po-total-vatAmount').value = formatCurrency(vatAmount);
            poItem.querySelector('.po-total-subnet').value = formatCurrency(subnet);
        }

        // Add new PO
        function addPoItem() {
            const container = document.getElementById('po-container');
            const firstItem = container.querySelector('.po-item');
            const newItem = firstItem.cloneNode(true);

            // Update PO index
            const newPoIndex = poIndex;
            newItem.setAttribute('data-po-index', newPoIndex);
            newItem.querySelector('.card-header h6').textContent = `PO #${newPoIndex + 1}`;

            // Update all field names with new PO index
            newItem.querySelectorAll('input, select').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/\[(\d+)\]/, `[${newPoIndex}]`);
                }
            });

            // Update data attributes and function calls
            newItem.querySelector('.po-items-container').setAttribute('data-po-index', newPoIndex);
            newItem.querySelectorAll('.po-line-item').forEach((lineItem, idx) => {
                lineItem.setAttribute('data-item-index', idx);
                lineItem.querySelectorAll('input, select').forEach(input => {
                    if (input.name) {
                        input.name = input.name.replace(/\[(\d+)\]\[(\d+)\]/, `[${newPoIndex}][${idx}]`);
                    }
                });
                lineItem.querySelectorAll('.po-item-qty, .po-item-unitCost').forEach(input => {
                    input.setAttribute('oninput', `calculatePoItemAmount(${newPoIndex}, ${idx})`);
                });
                lineItem.querySelector('.remove-po-line-item').setAttribute('onclick',
                    `removePoLineItem(${newPoIndex}, this)`);
            });
            newItem.querySelector('.po-header-vat').setAttribute('oninput', `calculatePoTotals(${newPoIndex})`);
            newItem.querySelector('.add-po-line-item').setAttribute('onclick', `addPoLineItem(${newPoIndex})`);

            // Clear all values
            newItem.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => {
                if (!input.readOnly) {
                    input.value = '';
                } else {
                    input.value = '0.00';
                }
            });
            newItem.querySelectorAll('select').forEach(select => {
                select.value = '';
            });

            // Reset line items to one
            const lineItems = newItem.querySelectorAll('.po-line-item');
            for (let i = 1; i < lineItems.length; i++) {
                lineItems[i].remove();
            }
            newItem.querySelector('.remove-po-line-item').style.display = 'none';

            // Show remove button for all POs
            container.querySelectorAll('.remove-po').forEach(btn => {
                btn.style.display = 'inline-block';
            });

            container.appendChild(newItem);
            poIndex++;
        }

        // Remove PO
        function removePoItem(button) {
            const container = document.getElementById('po-container');
            const items = container.querySelectorAll('.po-item');

            if (items.length > 1) {
                button.closest('.po-item').remove();

                // Hide remove button if only one item remains
                if (container.querySelectorAll('.po-item').length === 1) {
                    container.querySelector('.remove-po').style.display = 'none';
                }
            }
        }

        // Add line item to a PO
        function addPoLineItem(poIndex) {
            const poItem = document.querySelector(`.po-item[data-po-index="${poIndex}"]`);
            const itemsContainer = poItem.querySelector('.po-items-container');
            const firstLineItem = itemsContainer.querySelector('.po-line-item');
            const newLineItem = firstLineItem.cloneNode(true);

            const itemIndex = itemsContainer.querySelectorAll('.po-line-item').length;
            newLineItem.setAttribute('data-item-index', itemIndex);

            // Update field names
            newLineItem.querySelectorAll('input, select').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/\[(\d+)\]\[(\d+)\]/, `[${poIndex}][${itemIndex}]`);
                }
            });

            // Update function calls
            newLineItem.querySelectorAll('.po-item-qty, .po-item-unitCost').forEach(input => {
                input.setAttribute('oninput', `calculatePoItemAmount(${poIndex}, ${itemIndex})`);
            });
            newLineItem.querySelector('.remove-po-line-item').setAttribute('onclick', `removePoLineItem(${poIndex}, this)`);

            // Clear values
            newLineItem.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => {
                if (!input.readOnly) {
                    input.value = '';
                } else {
                    input.value = '0.00';
                }
            });
            newLineItem.querySelectorAll('select').forEach(select => {
                select.value = '';
            });

            // Show remove buttons for all line items
            itemsContainer.querySelectorAll('.remove-po-line-item').forEach(btn => {
                btn.style.display = 'inline-block';
            });

            itemsContainer.appendChild(newLineItem);
        }

        // Remove line item from a PO
        function removePoLineItem(poIndex, button) {
            const poItem = document.querySelector(`.po-item[data-po-index="${poIndex}"]`);
            const itemsContainer = poItem.querySelector('.po-items-container');
            const lineItems = itemsContainer.querySelectorAll('.po-line-item');

            if (lineItems.length > 1) {
                button.closest('.po-line-item').remove();

                // Hide remove button if only one item remains
                if (itemsContainer.querySelectorAll('.po-line-item').length === 1) {
                    itemsContainer.querySelector('.remove-po-line-item').style.display = 'none';
                }

                // Recalculate totals
                calculatePoTotals(poIndex);
            }
        }

        document.addEventListener('input', function(e) {
            if (
                e.target.classList.contains('po-item-qty') ||
                e.target.classList.contains('po-item-unitCost') ||
                e.target.classList.contains('po-header-vat')
            ) {
                applyNumberFormatting(e.target);
            }
        });

        const createProjectForm = document.getElementById('createProjectForm');
        if (createProjectForm) {
            createProjectForm.addEventListener('submit', function() {
                createProjectForm.querySelectorAll(
                    'input[name^="po_item_qty"], input[name^="po_item_unitCost"], input[name="po_vat[]"]'
                ).forEach((input) => {
                    input.value = normalizeNumericInput(input.value);
                });
            });
        }
    </script>
@endsection
<!-- /Page Wrapper -->
