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
                            <li class="breadcrumb-item active">Project Purchase Order Setup</li>
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
                                                        {{ $project->name }}</option>
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
                                <h4 class="card-title">Add Purchase Order</h4>
                            </div>
                            <div class="card-body">
                                <form method="post" id="addPoForm">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="projectId" value="{{ $projectId }}">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Description <span class="text-danger">*</span></label>
                                                <?php if ($description == '') {
                                                    $description = old('description');
                                                } ?>
                                                <input type="text" class="form-control" value="{{ $description }}"
                                                    name="description" id="description" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Unit of Measure (UOM)</label>
                                                <?php if ($uomId == '') {
                                                    $uomId = old('uomId');
                                                } ?>
                                                <select class="select2 form-control" name="uomId" id="uomId">
                                                    <option value="">--Select UOM--</option>
                                                    @foreach ($uoms as $uom)
                                                        <option value="{{ $uom->id }}"
                                                            {{ $uomId == $uom->id ? 'selected' : '' }}>
                                                            {{ $uom->measurement }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Quantity <span class="text-danger">*</span></label>
                                                <?php if ($qty == '') {
                                                    $qty = old('qty');
                                                } ?>
                                                <input type="number" class="form-control" value="{{ $qty }}"
                                                    name="qty" id="qty" step="0.01" min="0" required
                                                    oninput="calculatePoAmounts()">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Unit Cost <span class="text-danger">*</span></label>
                                                <?php if ($unitCost == '') {
                                                    $unitCost = old('unitCost');
                                                } ?>
                                                <input type="number" class="form-control" value="{{ $unitCost }}"
                                                    name="unitCost" id="unitCost" step="0.01" min="0" required
                                                    oninput="calculatePoAmounts()">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>VAT %</label>
                                                <?php if ($vat == '') {
                                                    $vat = old('vat');
                                                } ?>
                                                <input type="number" class="form-control" value="{{ $vat }}"
                                                    name="vat" id="vat" step="0.01" min="0" max="100"
                                                    oninput="calculatePoAmounts()">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Sub Cost</label>
                                                <input type="number" class="form-control" id="subcost" readonly
                                                    style="background-color: #f0f0f0;">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>VAT Amount</label>
                                                <input type="number" class="form-control" id="vatAmount" readonly
                                                    style="background-color: #f0f0f0;">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Sub Net</label>
                                                <input type="number" class="form-control" id="subnet" readonly
                                                    style="background-color: #f0f0f0;">
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
                                                <th rowspan="1">Description</th>
                                                <th rowspan="1">UOM</th>
                                                <th rowspan="1">Qty</th>
                                                <th rowspan="1">Unit Cost</th>
                                                <th rowspan="1">Sub Cost</th>
                                                <th rowspan="1">VAT %</th>
                                                <th rowspan="1">VAT Amount</th>
                                                <th rowspan="1">Sub Net</th>
                                                <th rowspan="1">Status</th>
                                                <th rowspan="1">Created By</th>
                                                <th rowspan="1">Approved By</th>
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
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            {{ $i++ }}
                                                        </td>
                                                        <td>
                                                            {{ $list->description }}
                                                        </td>
                                                        <td>
                                                            {{ $list->uomMeasurement ?? 'N/A' }}
                                                        </td>
                                                        <td style="text-align: right;">
                                                            {{ number_format($list->qty, 2, '.', ',') }}
                                                        </td>
                                                        <td style="text-align: right;">
                                                            {{ number_format($list->unitCost, 2, '.', ',') }}
                                                        </td>
                                                        <td style="text-align: right;">
                                                            {{ number_format($list->subcost, 2, '.', ',') }}
                                                        </td>
                                                        <td style="text-align: right;">
                                                            {{ $list->vat ? number_format($list->vat, 2, '.', ',') . '%' : '0%' }}
                                                        </td>
                                                        <td style="text-align: right;">
                                                            {{ number_format($list->vatAmount, 2, '.', ',') }}
                                                        </td>
                                                        <td style="text-align: right;">
                                                            {{ number_format($list->subnet, 2, '.', ',') }}
                                                        </td>
                                                        <td>
                                                            @if ($list->status == 'Approved')
                                                                <span class="badge bg-success">Approved</span>
                                                            @else
                                                                <span class="badge bg-warning">Pending</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ $list->createdByName ?? 'N/A' }}
                                                        </td>
                                                        <td>
                                                            {{ $list->approvedByName ?? 'N/A' }}
                                                        </td>
                                                        <td>
                                                            @if ($list->status != 'Approved')
                                                                <a class="btn btn-sm bg-success-light"
                                                                    href="javascript: editfunc('{{ $list->id }}','{{ addslashes($list->description) }}','{{ $list->uomId ?? '' }}','{{ $list->qty }}','{{ $list->unitCost }}','{{ $list->vat ?? 0 }}')">
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
                                                    <td></td>
                                                    <td colspan="9" class="text-right"><strong>Grand Total:</strong>
                                                    </td>
                                                    <td style="text-align: right;">
                                                        <strong>{{ number_format($totalSubnet, 2, '.', ',') }}</strong>
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="13" class="text-center">No purchase orders for this
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
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Description <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_description" name="description" required>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Unit of Measure (UOM)</label>
                                        <select class="select2 form-control" id="edit_uomId" name="uomId">
                                            <option value="">--Select UOM--</option>
                                            @foreach ($uoms as $uom)
                                                <option value="{{ $uom->id }}">{{ $uom->measurement }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Quantity <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="edit_qty" name="qty" step="0.01"
                                            min="0" required oninput="calculateEditPoAmounts()">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Unit Cost <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="edit_unitCost" name="unitCost"
                                            step="0.01" min="0" required oninput="calculateEditPoAmounts()">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>VAT %</label>
                                        <input type="number" class="form-control" id="edit_vat" name="vat" step="0.01"
                                            min="0" max="100" oninput="calculateEditPoAmounts()">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Sub Cost</label>
                                        <input type="number" class="form-control" id="edit_subcost" readonly
                                            style="background-color: #f0f0f0;">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>VAT Amount</label>
                                        <input type="number" class="form-control" id="edit_vatAmount" readonly
                                            style="background-color: #f0f0f0;">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Sub Net</label>
                                        <input type="number" class="form-control" id="edit_subnet" readonly
                                            style="background-color: #f0f0f0;">
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

        function calculatePoAmounts() {
            var qty = parseFloat(document.getElementById('qty').value) || 0;
            var unitCost = parseFloat(document.getElementById('unitCost').value) || 0;
            var vat = parseFloat(document.getElementById('vat').value) || 0;

            var subcost = qty * unitCost;
            var vatAmount = subcost * (vat / 100);
            var subnet = subcost + vatAmount;

            document.getElementById('subcost').value = subcost.toFixed(2);
            document.getElementById('vatAmount').value = vatAmount.toFixed(2);
            document.getElementById('subnet').value = subnet.toFixed(2);
        }

        function calculateEditPoAmounts() {
            var qty = parseFloat(document.getElementById('edit_qty').value) || 0;
            var unitCost = parseFloat(document.getElementById('edit_unitCost').value) || 0;
            var vat = parseFloat(document.getElementById('edit_vat').value) || 0;

            var subcost = qty * unitCost;
            var vatAmount = subcost * (vat / 100);
            var subnet = subcost + vatAmount;

            document.getElementById('edit_subcost').value = subcost.toFixed(2);
            document.getElementById('edit_vatAmount').value = vatAmount.toFixed(2);
            document.getElementById('edit_subnet').value = subnet.toFixed(2);
        }

        function editfunc(id, description, uomId, qty, unitCost, vat) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_description').value = description || '';
            document.getElementById('edit_uomId').value = uomId || '';
            document.getElementById('edit_qty').value = qty || '';
            document.getElementById('edit_unitCost').value = unitCost || '';
            document.getElementById('edit_vat').value = vat || 0;
            calculateEditPoAmounts();
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
    </script>
@endsection
<!-- /Page Wrapper -->



