<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Vendor Project Management
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
                            <li class="breadcrumb-item active">Vendor Project Management</li>
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
                                <h4 class="card-title">Add Vendor Project</h4>
                            </div>
                            <div class="card-body">
                                <form method="post" id="addVendorProjectForm">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="projectId" value="{{ $projectId }}">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Vendor <span class="text-danger">*</span></label>
                                                <?php if ($vendorId == '') {
                                                    $vendorId = old('vendorId');
                                                } ?>
                                                <select class="select2 form-control" name="vendorId" id="vendorId"
                                                    required>
                                                    <option value="">--Select Vendor--</option>
                                                    @foreach ($vendors as $vendor)
                                                        <option value="{{ $vendor->id }}"
                                                            {{ $vendorId == $vendor->id ? 'selected' : '' }}>
                                                            {{ $vendor->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <?php if ($description == '') {
                                                    $description = old('description');
                                                } ?>
                                                <input type="text" class="form-control" value="{{ $description }}"
                                                    name="description" id="description">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>Items <span class="text-danger">*</span></label>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 42%;">Item Description</th>
                                                            <th style="width: 16%;">Qty</th>
                                                            <th style="width: 16%;">Cost</th>
                                                            <th style="width: 16%;">Subtotal</th>
                                                            <th style="width: 10%;">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="add-items-body">
                                                        @php
                                                            $oldDescriptions = old('item_description', ['']);
                                                            $oldQties = old('item_qty', ['']);
                                                            $oldCosts = old('item_cost', ['']);
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
                                                                    <input type="number"
                                                                        class="form-control item-qty add-item-input"
                                                                        name="item_qty[]"
                                                                        value="{{ $oldQties[$idx] ?? '' }}" min="0"
                                                                        step="0.01" required>
                                                                </td>
                                                                <td>
                                                                    <input type="number"
                                                                        class="form-control item-cost add-item-input"
                                                                        name="item_cost[]"
                                                                        value="{{ $oldCosts[$idx] ?? '' }}" min="0"
                                                                        step="0.01" required>
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control item-subtotal"
                                                                        value="0" step="0.01" readonly>
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
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>VAT (%)</label>
                                                <input type="number" class="form-control"
                                                    value="{{ old('vat', $vat ?? 0) }}" name="vat" id="add_vat"
                                                    step="0.01" min="0" max="100">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Subtotal</label>
                                                <input type="number" class="form-control" id="add_subtotal"
                                                    step="0.01" readonly style="background-color: #f0f0f0;">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>VAT Amount</label>
                                                <input type="number" class="form-control" id="add_vatAmount"
                                                    step="0.01" readonly style="background-color: #f0f0f0;">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Total Amount</label>
                                                <input type="number" class="form-control" id="add_total" step="0.01"
                                                    readonly style="background-color: #f0f0f0;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary" name="addnew">Add Vendor
                                            Project</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <!-- List of vendor projects -->
                        <div class="card card-table">
                            <div class="card-header">
                                <h4 class="card-title">Vendor Projects</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-center mb-0">
                                        <thead>
                                            <tr>
                                                <th rowspan="1">S/N</th>
                                                <th rowspan="1">Vendor</th>
                                                <th rowspan="1">Description</th>
                                                <th rowspan="1">Items</th>
                                                <th rowspan="1">VAT</th>
                                                <th rowspan="1">Amount</th>
                                                <th rowspan="1">Status</th>
                                                <th rowspan="1">Created By</th>
                                                <th rowspan="1">Approved By</th>
                                                <th rowspan="1">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $i = 1;
                                            @endphp

                                            @if ($vendorProjects->count() > 0)
                                                @foreach ($vendorProjects as $vendorProject)
                                                    <tr>
                                                        <td>
                                                            {{ $i++ }}
                                                        </td>
                                                        <td>
                                                            <strong>{{ $vendorProject->vendorName }}</strong>
                                                        </td>
                                                        <td>
                                                            {{ $vendorProject->description ?? 'N/A' }}
                                                        </td>
                                                        <td>
                                                            <strong>{{ $vendorProject->itemCount ?? 0 }} item(s)</strong>
                                                            @if (!empty($vendorProject->items) && count($vendorProject->items) > 0)
                                                                <ul class="mb-0 mt-1 pl-3">
                                                                    @foreach ($vendorProject->items as $item)
                                                                        <li>
                                                                            {{ $item->item_description }}
                                                                            ({{ number_format($item->qty, 2, '.', ',') }}
                                                                            x
                                                                            {{ number_format($item->cost, 2, '.', ',') }})
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </td>
                                                        <td style="text-align: right;">
                                                            {{ number_format($vendorProject->vat ?? 0, 2, '.', ',') }}%
                                                        </td>
                                                        <td style="text-align: right;">
                                                            <strong>{{ number_format($vendorProject->amount, 2, '.', ',') }}</strong>
                                                        </td>
                                                        <td>
                                                            @if ($vendorProject->status == 'Approved')
                                                                <span class="badge bg-success">Approved</span>
                                                            @elseif ($vendorProject->status == 'Rejected')
                                                                <span class="badge bg-danger">Rejected</span>
                                                            @else
                                                                <span class="badge bg-warning">Pending</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ $vendorProject->createdByName ?? 'N/A' }}
                                                        </td>
                                                        <td>
                                                            {{ $vendorProject->approvedByName ?? '-' }}
                                                        </td>
                                                        <td>
                                                            @if ($vendorProject->status != 'Approved')
                                                                <a class="btn btn-sm bg-success-light"
                                                                    href="javascript: editfunc('{{ $vendorProject->id }}')">
                                                                    <i class="fe fe-pencil"></i>
                                                                </a>
                                                                <a class="btn btn-sm bg-info-light"
                                                                    href="javascript: approvefunc('{{ $vendorProject->id }}')">
                                                                    <i class="fe fe-check"></i>
                                                                </a>
                                                            @endif
                                                            @if ($vendorProject->status != 'Approved')
                                                                <a class="btn btn-sm bg-danger-light"
                                                                    href="javascript: deletefunc('{{ $vendorProject->id }}')">
                                                                    <i class="fe fe-trash"></i>
                                                                </a>
                                                            @endif
                                                            @if ($vendorProject->status == 'Approved')
                                                                <a class="btn btn-sm bg-primary-light"
                                                                    href="{{ url('/vendor-project-purchase-order') }}?vendorProjectId={{ $vendorProject->id }}"
                                                                    target="_blank" rel="noopener noreferrer"
                                                                    title="View Purchase Order">
                                                                    <i class="fa fa-file"></i>
                                                                </a>
                                                                <a class="btn btn-sm bg-warning-light"
                                                                    href="{{ url('/vendor-project-send-po-email') }}?vendorProjectId={{ $vendorProject->id }}"
                                                                    title="Send PO PDF to Vendor">
                                                                    <i class="fa fa-envelope"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="10" class="text-center">No vendor projects added for
                                                        this
                                                        project yet.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /List of vendor projects -->
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <p class="text-center text-muted">Please select a project to view and manage its vendor
                                    projects.</p>
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
                            <h5 class="modal-title">Edit Vendor Project</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Vendor <span class="text-danger">*</span></label>
                                        <select class="select2 form-control" name="vendorId" id="edit_vendorId" required>
                                            <option value="">--Select Vendor--</option>
                                            @foreach ($vendors as $vendor)
                                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <input type="text" id="edit_description" name="description"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Items <span class="text-danger">*</span></label>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th style="width: 42%;">Item Description</th>
                                                    <th style="width: 16%;">Qty</th>
                                                    <th style="width: 16%;">Cost</th>
                                                    <th style="width: 16%;">Subtotal</th>
                                                    <th style="width: 10%;">Action</th>
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
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>VAT (%)</label>
                                        <input type="number" id="edit_vat" name="vat" class="form-control"
                                            min="0" max="100" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Subtotal</label>
                                        <input type="number" id="edit_subtotal" class="form-control" readonly
                                            step="0.01" style="background-color: #f0f0f0;">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>VAT Amount</label>
                                        <input type="number" id="edit_vatAmount" class="form-control" readonly
                                            step="0.01" style="background-color: #f0f0f0;">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Total Amount</label>
                                        <input type="number" id="edit_total" class="form-control" readonly
                                            step="0.01" style="background-color: #f0f0f0;">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" id="edit_status" name="status">
                                            <option value="Pending">Pending</option>
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
                                <h4 class="modal-title">Approve</h4>
                                <p class="mb-4">Are you sure you want to approve this vendor project?</p>
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
                                <p class="mb-4">Are you sure want to delete this vendor project?</p>
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
            $vendorProjectEditData = ($vendorProjects ?? collect())
                ->mapWithKeys(function ($vendorProject) {
                    return [
                        $vendorProject->id => [
                            'id' => $vendorProject->id,
                            'vendorId' => $vendorProject->vendorId,
                            'description' => $vendorProject->description,
                            'vat' => (float) ($vendorProject->vat ?? 0),
                            'status' => $vendorProject->status ?? 'Pending',
                            'items' => collect($vendorProject->items ?? collect())
                                ->map(function ($item) {
                                    return [
                                        'description' => $item->item_description,
                                        'qty' => (float) $item->qty,
                                        'cost' => (float) $item->cost,
                                    ];
                                })
                                ->values()
                                ->toArray(),
                        ],
                    ];
                })
                ->toArray();
        @endphp
        const vendorProjectEditData = @json($vendorProjectEditData);

        function selectProject() {
            var projectId = document.getElementById('projectId').value;
            if (projectId) {
                document.getElementById('projectSelectForm').submit();
            }
        }

        function itemRowTemplate(prefix, data = {}) {
            const desc = escapeHtml(data.description || '');
            const qty = data.qty ?? '';
            const cost = data.cost ?? '';
            return `
                <tr class="item-row">
                    <td><input type="text" class="form-control item-description" name="item_description[]" value="${desc}" required></td>
                    <td><input type="number" class="form-control item-qty ${prefix}-item-input" name="item_qty[]" value="${qty}" min="0" step="0.01" required></td>
                    <td><input type="number" class="form-control item-cost ${prefix}-item-input" name="item_cost[]" value="${cost}" min="0" step="0.01" required></td>
                    <td><input type="number" class="form-control item-subtotal" value="0" readonly step="0.01"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-item-btn"><i class="fe fe-trash"></i></button>
                    </td>
                </tr>
            `;
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            };
            return String(text).replace(/[&<>"']/g, (m) => map[m]);
        }

        function addItemRow(prefix, data = {}) {
            const body = document.getElementById(prefix + '-items-body');
            body.insertAdjacentHTML('beforeend', itemRowTemplate(prefix, data));
            recalculateTotals(prefix);
        }

        function recalculateTotals(prefix) {
            let subtotal = 0;
            document.querySelectorAll('#' + prefix + '-items-body tr').forEach((row) => {
                const qty = parseFloat(row.querySelector('.item-qty')?.value) || 0;
                const cost = parseFloat(row.querySelector('.item-cost')?.value) || 0;
                const lineSubtotal = qty * cost;
                const subtotalInput = row.querySelector('.item-subtotal');
                if (subtotalInput) {
                    subtotalInput.value = lineSubtotal.toFixed(2);
                }
                subtotal += lineSubtotal;
            });

            const vat = parseFloat(document.getElementById(prefix + '_vat')?.value) || 0;
            const vatAmount = subtotal * (vat / 100);
            const total = subtotal + vatAmount;

            const subtotalEl = document.getElementById(prefix + '_subtotal');
            const vatAmountEl = document.getElementById(prefix + '_vatAmount');
            const totalEl = document.getElementById(prefix + '_total');
            if (subtotalEl) subtotalEl.value = subtotal.toFixed(2);
            if (vatAmountEl) vatAmountEl.value = vatAmount.toFixed(2);
            if (totalEl) totalEl.value = total.toFixed(2);
        }

        function editfunc(id) {
            const row = vendorProjectEditData[id];
            if (!row) return;

            document.getElementById('edit_id').value = row.id;
            document.getElementById('edit_vendorId').value = row.vendorId;
            document.getElementById('edit_description').value = row.description || '';
            document.getElementById('edit_vat').value = row.vat || 0;
            document.getElementById('edit_status').value = row.status || 'Pending';

            const editBody = document.getElementById('edit-items-body');
            editBody.innerHTML = '';
            (row.items || []).forEach((item) => addItemRow('edit', item));
            if ((row.items || []).length === 0) {
                addItemRow('edit');
            }
            recalculateTotals('edit');

            $('#edit_vendorId').trigger('change');
            $("#edit_modal").modal('show')
        }

        function approvefunc(id) {
            document.getElementById('approveid').value = id;
            $("#approve_modal").modal('show')
        }

        function deletefunc(id) {
            document.getElementById('deleteid').value = id;
            $("#delete_modal").modal('show')
        }

        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('add-item-input') || e.target.id === 'add_vat') {
                recalculateTotals('add');
            }
            if (e.target.classList.contains('edit-item-input') || e.target.id === 'edit_vat') {
                recalculateTotals('edit');
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
                recalculateTotals('add');
            }
            if (parentId === 'edit-items-body') {
                if (document.querySelectorAll('#edit-items-body tr').length === 0) {
                    addItemRow('edit');
                }
                recalculateTotals('edit');
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            recalculateTotals('add');
            if (document.querySelectorAll('#add-items-body tr').length === 0) {
                addItemRow('add');
            }
        });
    </script>
@endsection
<!-- /Page Wrapper -->
