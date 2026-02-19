<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Project Purchase Order Setup
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
                            <li class="breadcrumb-item active">Purchase Order</li>
                        </ul>
                    </div>
                    <div class="col-auto">
                        <a href="{{ url('/project-setup') }}" class="btn btn-primary">
                            <i class="fe fe-arrow-left"></i> Back to Project
                        </a>
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
                        <!-- List of project POs -->
                        <div class="card card-table">
                            <div class="card-header">
                                <h4 class="card-title">Project Purchase Orders</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-center mb-0">
                                        <thead>
                                            <tr>
                                                <th rowspan="1">S/N</th>
                                                <th rowspan="1">PO Number</th>
                                                <th rowspan="1">PO Description</th>
                                                <th rowspan="1">PO Items</th>
                                                <th rowspan="1">Amount</th>
                                                <th rowspan="1">Status</th>
                                                <th rowspan="1">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $i = 1;
                                                $totalSubnet = 0;
                                            @endphp

                                            @if ($projectPos->count() > 0)
                                                @foreach ($projectPos as $list)
                                                    @php
                                                        $totalSubnet += $list->subnet;
                                                        $items = $list->items ?? collect();
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $i++ }}</td>
                                                        <td><strong>{{ $list->poNumber }}</strong></td>
                                                        <td>{{ $list->description }}</td>
                                                        <td>
                                                            <strong>{{ $items->count() }} item(s)</strong>
                                                            @if ($items->count() > 0)
                                                                <ul class="mb-0 mt-1 pl-3">
                                                                    @foreach ($items as $item)
                                                                        <li>
                                                                            {{ $item->description }}
                                                                            ({{ number_format($item->qty, 2, '.', ',') }}
                                                                            x
                                                                            {{ number_format($item->unitCost, 2, '.', ',') }})
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </td>
                                                        <td style="text-align: right;">
                                                            <strong>{{ number_format($list->subnet, 2, '.', ',') }}</strong>
                                                        </td>
                                                        <td>
                                                            @if ($list->status == 'Approved')
                                                                <span class="badge bg-success">Approved</span>
                                                            @else
                                                                <span class="badge bg-warning">Pending</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($list->status != 'Approved')
                                                                <a class="btn btn-sm bg-success-light"
                                                                    href="javascript: editfunc('{{ $list->id }}','{{ $list->poNumber }}','{{ addslashes($list->description) }}','{{ $list->vat ?? 0 }}',{{ json_encode($items->map(function ($item) {return ['id' => $item->id, 'description' => $item->description, 'uomId' => $item->uomId ?? '', 'qty' => $item->qty, 'unitCost' => $item->unitCost];})->toArray()) }})">
                                                                    <i class="fe fe-pencil"></i>
                                                                </a>
                                                                <a class="btn btn-sm bg-info-light"
                                                                    href="javascript: approvefunc('{{ $list->id }}')">
                                                                    <i class="fe fe-check"></i>
                                                                </a>
                                                            @endif
                                                            <a class="btn btn-sm bg-danger-light"
                                                                href="javascript: deletefunc('{{ $list->id }}')">
                                                                <i class="fe fe-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr style="background-color: #d0d0d0; font-weight: bold; font-size: 1.1em;">
                                                    <td colspan="4" class="text-right"><strong>Grand Total:</strong></td>
                                                    <td style="text-align: right;">
                                                        <strong>{{ number_format($totalSubnet, 2, '.', ',') }}</strong>
                                                    </td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="7" class="text-center">No purchase orders for this
                                                        project yet.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /List of project POs -->
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Add Purchase Order</h4>
                            </div>
                            <div class="card-body">
                                <form method="post" id="addPoForm">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="projectId" value="{{ $projectId }}">

                                    <!-- PO Header -->
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>PO Number <span class="text-danger">*</span></label>
                                                <?php if ($poNumber == '') {
                                                    $poNumber = old('poNumber');
                                                } ?>
                                                <input type="text" class="form-control" value="{{ $poNumber }}"
                                                    name="poNumber" id="poNumber" required>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>PO Description <span class="text-danger">*</span></label>
                                                <?php if ($description == '') {
                                                    $description = old('description');
                                                } ?>
                                                <input type="text" class="form-control" value="{{ $description }}"
                                                    name="description" id="description" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>VAT %</label>
                                                <?php if ($vat == '') {
                                                    $vat = old('vat');
                                                } ?>
                                                <input type="text" class="form-control" value="{{ $vat }}"
                                                    name="vat" id="vat" step="0.01" min="0"
                                                    max="100" oninput="calculatePoTotals()">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Line Items -->
                                    <h6 class="mb-2">Line Items <span class="text-danger">*</span> <small
                                            class="text-muted">(At least one line item is required)</small></h6>
                                    <div id="po-items-container">
                                        @php
                                            $oldItemDescriptions = old('item_description', []);
                                            $oldItemUomIds = old('item_uomId', []);
                                            $oldItemQties = old('item_qty', []);
                                            $oldItemUnitCosts = old('item_unitCost', []);
                                            $itemCount = max(1, count($oldItemDescriptions));
                                        @endphp
                                        @for ($i = 0; $i < $itemCount; $i++)
                                            <div class="po-line-item card mb-2" data-item-index="{{ $i }}">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Item Description <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    name="item_description[]"
                                                                    value="{{ $oldItemDescriptions[$i] ?? '' }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>UOM</label>
                                                                <select class="select2 form-control" name="item_uomId[]">
                                                                    <option value="">--Select--</option>
                                                                    @foreach ($uoms as $uom)
                                                                        <option value="{{ $uom->id }}"
                                                                            {{ ($oldItemUomIds[$i] ?? '') == $uom->id ? 'selected' : '' }}>
                                                                            {{ $uom->measurement }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Qty <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control po-item-qty"
                                                                    name="item_qty[]" step="0.01" min="0"
                                                                    value="{{ $oldItemQties[$i] ?? '' }}" required
                                                                    oninput="calculatePoItemAmount(this)">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Unit Cost <span class="text-danger">*</span></label>
                                                                <input type="text"
                                                                    class="form-control po-item-unitCost"
                                                                    name="item_unitCost[]" step="0.01" min="0"
                                                                    value="{{ $oldItemUnitCosts[$i] ?? '' }}" required
                                                                    oninput="calculatePoItemAmount(this)">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Subtotal</label>
                                                                <input type="text" class="form-control po-item-subcost"
                                                                    readonly style="background-color: #f0f0f0;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger remove-po-line-item"
                                                            onclick="removePoLineItem(this)"
                                                            style="display: {{ $itemCount > 1 ? 'inline-block' : 'none' }};">
                                                            <i class="fe fe-trash"></i> Remove Item
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-sm btn-secondary"
                                            onclick="addPoLineItem()">
                                            <i class="fe fe-plus"></i> Add Line Item
                                        </button>
                                    </div>

                                    <!-- PO Totals -->
                                    <div class="row mt-3 pt-3 border-top">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><strong>Total Purchase Value</strong></label>
                                                <input type="text" class="form-control" id="total-subcost" readonly
                                                    style="background-color: #e9ecef; font-weight: bold;">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><strong>VAT Amount</strong></label>
                                                <input type="text" class="form-control" id="total-vatAmount" readonly
                                                    style="background-color: #e9ecef; font-weight: bold;">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><strong>Total PO Value</strong></label>
                                                <input type="text" class="form-control" id="total-subnet" readonly
                                                    style="background-color: #e9ecef; font-weight: bold;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary" name="addnew">Add PO</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <p class="text-center text-muted">Please select a project to view and manage its purchase
                                    orders.</p>
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
                            <h5 class="modal-title">Edit Purchase Order</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                            <!-- PO Header -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>PO Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="edit_poNumber" name="poNumber"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>PO Description <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="edit_description"
                                            name="description" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>VAT %</label>
                                        <input type="text" class="form-control" id="edit_vat" name="vat"
                                            step="0.01" min="0" max="100"
                                            oninput="calculateEditPoTotals()">
                                    </div>
                                </div>
                            </div>

                            <!-- Line Items -->
                            <h6 class="mb-2">Line Items <span class="text-danger">*</span></h6>
                            <div id="edit-po-items-container"></div>
                            <div class="mb-2">
                                <button type="button" class="btn btn-sm btn-secondary" onclick="addEditPoLineItem()">
                                    <i class="fe fe-plus"></i> Add Line Item
                                </button>
                            </div>

                            <!-- PO Totals -->
                            <div class="row mt-3 pt-3 border-top">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><strong>Total Purchase Value</strong></label>
                                        <input type="text" class="form-control" id="edit_total-subcost" readonly
                                            style="background-color: #e9ecef; font-weight: bold;">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><strong>VAT Amount</strong></label>
                                        <input type="text" class="form-control" id="edit_total-vatAmount" readonly
                                            style="background-color: #e9ecef; font-weight: bold;">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><strong>Total PO Value</strong></label>
                                        <input type="text" class="form-control" id="edit_total-subnet" readonly
                                            style="background-color: #e9ecef; font-weight: bold;">
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
                                <h4 class="modal-title">Approve Purchase Order</h4>
                                <p class="mb-4">Are you sure you want to approve this purchase order?</p>
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
                                <p class="mb-4">Are you sure want to delete this purchase order?</p>
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

        let editItemIndex = 0;

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

        // Calculate individual line item amount (add form)
        function calculatePoItemAmount(element) {
            const lineItem = element.closest('.po-line-item');
            const qtyInput = lineItem.querySelector('.po-item-qty');
            const unitCostInput = lineItem.querySelector('.po-item-unitCost');
            applyNumberFormatting(qtyInput);
            applyNumberFormatting(unitCostInput);
            const qty = parseFormattedNumber(qtyInput.value);
            const unitCost = parseFormattedNumber(unitCostInput.value);
            const subcost = qty * unitCost;

            lineItem.querySelector('.po-item-subcost').value = formatCurrency(subcost);
            calculatePoTotals();
        }

        // Calculate PO totals from all line items (add form)
        function calculatePoTotals() {
            const lineItems = document.querySelectorAll('#po-items-container .po-line-item');
            let totalSubcost = 0;

            lineItems.forEach(lineItem => {
                const subcost = parseFormattedNumber(lineItem.querySelector('.po-item-subcost').value);
                totalSubcost += subcost;
            });

            const vatInput = document.getElementById('vat');
            applyNumberFormatting(vatInput);
            const vat = parseFormattedNumber(vatInput.value);
            const vatAmount = totalSubcost * (vat / 100);
            const subnet = totalSubcost + vatAmount;

            document.getElementById('total-subcost').value = formatCurrency(totalSubcost);
            document.getElementById('total-vatAmount').value = formatCurrency(vatAmount);
            document.getElementById('total-subnet').value = formatCurrency(subnet);
        }

        // Add line item (add form)
        function addPoLineItem() {
            const container = document.getElementById('po-items-container');
            const firstItem = container.querySelector('.po-line-item');
            const newItem = firstItem.cloneNode(true);

            const itemIndex = container.querySelectorAll('.po-line-item').length;
            newItem.setAttribute('data-item-index', itemIndex);

            // Clear values
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

            // Update function calls
            newItem.querySelectorAll('.po-item-qty, .po-item-unitCost').forEach(input => {
                input.setAttribute('oninput', 'calculatePoItemAmount(this)');
            });
            newItem.querySelector('.remove-po-line-item').setAttribute('onclick', 'removePoLineItem(this)');

            // Show remove buttons
            container.querySelectorAll('.remove-po-line-item').forEach(btn => {
                btn.style.display = 'inline-block';
            });

            container.appendChild(newItem);
        }

        // Remove line item (add form)
        function removePoLineItem(button) {
            const container = document.getElementById('po-items-container');
            const lineItems = container.querySelectorAll('.po-line-item');

            if (lineItems.length > 1) {
                button.closest('.po-line-item').remove();

                if (container.querySelectorAll('.po-line-item').length === 1) {
                    container.querySelector('.remove-po-line-item').style.display = 'none';
                }

                calculatePoTotals();
            }
        }

        // Calculate individual line item amount (edit form)
        function calculateEditPoItemAmount(element) {
            const lineItem = element.closest('.po-line-item');
            const qtyInput = lineItem.querySelector('.po-item-qty');
            const unitCostInput = lineItem.querySelector('.po-item-unitCost');
            applyNumberFormatting(qtyInput);
            applyNumberFormatting(unitCostInput);
            const qty = parseFormattedNumber(qtyInput.value);
            const unitCost = parseFormattedNumber(unitCostInput.value);
            const subcost = qty * unitCost;

            lineItem.querySelector('.po-item-subcost').value = formatCurrency(subcost);
            calculateEditPoTotals();
        }

        // Calculate PO totals from all line items (edit form)
        function calculateEditPoTotals() {
            const lineItems = document.querySelectorAll('#edit-po-items-container .po-line-item');
            let totalSubcost = 0;

            lineItems.forEach(lineItem => {
                const subcost = parseFormattedNumber(lineItem.querySelector('.po-item-subcost').value);
                totalSubcost += subcost;
            });

            const vatInput = document.getElementById('edit_vat');
            applyNumberFormatting(vatInput);
            const vat = parseFormattedNumber(vatInput.value);
            const vatAmount = totalSubcost * (vat / 100);
            const subnet = totalSubcost + vatAmount;

            document.getElementById('edit_total-subcost').value = formatCurrency(totalSubcost);
            document.getElementById('edit_total-vatAmount').value = formatCurrency(vatAmount);
            document.getElementById('edit_total-subnet').value = formatCurrency(subnet);
        }

        // Add line item (edit form)
        function addEditPoLineItem() {
            const container = document.getElementById('edit-po-items-container');
            const template = document.querySelector('#po-items-container .po-line-item');

            if (!template) {
                console.error('Template not found');
                return;
            }

            const newItem = template.cloneNode(true);
            const itemIndex = editItemIndex++;
            newItem.setAttribute('data-item-index', itemIndex);

            // Clear values
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

            // Update function calls
            newItem.querySelectorAll('.po-item-qty, .po-item-unitCost').forEach(input => {
                input.setAttribute('oninput', 'calculateEditPoItemAmount(this)');
            });
            newItem.querySelector('.remove-po-line-item').setAttribute('onclick', 'removeEditPoLineItem(this)');

            // Show remove buttons for all items
            container.querySelectorAll('.remove-po-line-item').forEach(btn => {
                btn.style.display = 'inline-block';
            });

            container.appendChild(newItem);
        }

        // Remove line item (edit form)
        function removeEditPoLineItem(button) {
            const container = document.getElementById('edit-po-items-container');
            const lineItems = container.querySelectorAll('.po-line-item');

            if (lineItems.length > 1) {
                button.closest('.po-line-item').remove();

                if (container.querySelectorAll('.po-line-item').length === 1) {
                    container.querySelector('.remove-po-line-item').style.display = 'none';
                }

                calculateEditPoTotals();
            }
        }

        // Recalculate on page load
        document.addEventListener('DOMContentLoaded', function() {
            applyNumberFormatting(document.getElementById('vat'));
            const lineItems = document.querySelectorAll('#po-items-container .po-line-item');
            lineItems.forEach(lineItem => {
                const qtyInput = lineItem.querySelector('.po-item-qty');
                const unitCostInput = lineItem.querySelector('.po-item-unitCost');
                applyNumberFormatting(qtyInput);
                applyNumberFormatting(unitCostInput);
                if (qtyInput && unitCostInput && (qtyInput.value || unitCostInput.value)) {
                    calculatePoItemAmount(qtyInput);
                }
            });
            calculatePoTotals();
        });

        function editfunc(id, poNumber, description, vat, items) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_poNumber').value = poNumber || '';
            document.getElementById('edit_description').value = description || '';
            document.getElementById('edit_vat').value = formatNumberForDisplay(vat || 0);

            // Clear existing items
            const container = document.getElementById('edit-po-items-container');
            container.innerHTML = '';
            editItemIndex = 0;

            // Parse items if it's a string
            let itemsArray = items;
            if (typeof items === 'string') {
                try {
                    itemsArray = JSON.parse(items);
                } catch (e) {
                    itemsArray = [];
                }
            }

            // Get UOM select template from add form
            const uomTemplate = document.querySelector('#po-items-container select[name="item_uomId[]"]');

            // Add line items
            if (itemsArray && itemsArray.length > 0) {
                itemsArray.forEach((item, index) => {
                    // Clone template from add form
                    const template = document.querySelector('#po-items-container .po-line-item');
                    if (template) {
                        const newItem = template.cloneNode(true);
                        newItem.setAttribute('data-item-index', index);

                        // Update field names and values
                        newItem.querySelector('input[name="item_description[]"]').value = item.description || '';
                        const uomSelect = newItem.querySelector('select[name="item_uomId[]"]');
                        if (uomSelect && item.uomId) {
                            uomSelect.value = item.uomId;
                        }
                        newItem.querySelector('input[name="item_qty[]"]').value = item.qty || '';
                        newItem.querySelector('input[name="item_unitCost[]"]').value = item.unitCost || '';

                        // Update function calls
                        newItem.querySelectorAll('.po-item-qty, .po-item-unitCost').forEach(input => {
                            input.setAttribute('oninput', 'calculateEditPoItemAmount(this)');
                        });
                        newItem.querySelector('.remove-po-line-item').setAttribute('onclick',
                            'removeEditPoLineItem(this)');
                        newItem.querySelector('.remove-po-line-item').style.display = itemsArray.length > 1 ?
                            'inline-block' : 'none';

                        container.appendChild(newItem);

                        // Calculate item amount
                        const qtyInput = newItem.querySelector('.po-item-qty');
                        if (qtyInput && qtyInput.value) {
                            calculateEditPoItemAmount(qtyInput);
                        }
                    }
                });
                editItemIndex = itemsArray.length;
            } else {
                // Add one empty item using template
                if (document.querySelector('#po-items-container .po-line-item')) {
                    addEditPoLineItem();
                }
            }

            calculateEditPoTotals();
            $("#edit_modal").modal('show');
        }

        function approvefunc(id) {
            document.getElementById('approveid').value = id;
            $("#approve_modal").modal('show');
        }

        function deletefunc(id) {
            document.getElementById('deleteid').value = id;
            $("#delete_modal").modal('show');
        }

        document.addEventListener('input', function(e) {
            if (
                e.target.classList.contains('po-item-qty') ||
                e.target.classList.contains('po-item-unitCost') ||
                e.target.id === 'vat' ||
                e.target.id === 'edit_vat'
            ) {
                applyNumberFormatting(e.target);
            }
        });

        function stripCommasBeforeSubmit(formId) {
            const form = document.getElementById(formId);
            if (!form) return;
            form.addEventListener('submit', function() {
                form.querySelectorAll('input[name="item_qty[]"], input[name="item_unitCost[]"], input[name="vat"]')
                    .forEach(
                        (input) => {
                            input.value = normalizeNumericInput(input.value);
                        });
            });
        }

        stripCommasBeforeSubmit('addPoForm');
        stripCommasBeforeSubmit('editForm');
    </script>
@endsection
<!-- /Page Wrapper -->
