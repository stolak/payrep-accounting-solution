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
                            <form method="post">
                                {{ csrf_field() }}
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
                                            <select class="select2 form-control" name="clientId">
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
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Category</label>
                                            <?php if ($categoryId == '') {
                                                $categoryId = old('categoryId');
                                            } ?>
                                            <select class="select2 form-control" name="categoryId">
                                                <option value="">--Select--</option>
                                                @foreach ($projectCategories as $cat)
                                                    <option value="{{ $cat->id }}"
                                                        {{ $categoryId == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->category }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Expense Account</label>
                                            <?php if ($expenseAccountId == '') {
                                                $expenseAccountId = old('expenseAccountId');
                                            } ?>
                                            <select class="select2 form-control" name="expenseAccountId">
                                                <option value="">--Select--</option>
                                                @foreach ($accountLookUp as $account)
                                                    <option value="{{ $account->id }}"
                                                        {{ $expenseAccountId == $account->id ? 'selected' : '' }}>
                                                        {{ $account->accountdescription }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Location</label>
                                            <?php if ($location == '') {
                                                $location = old('location');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $location }}"
                                                name="location">
                                        </div>
                                    </div>

                                </div>

                                <!-- Purchase Orders Section -->
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <h5 class="mb-3">Purchase Orders <span class="text-danger">*</span> <small
                                                class="text-muted">(At least one PO is required)</small></h5>
                                        <div id="po-container">
                                            <div class="po-item card mb-3" data-po-index="0">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>PO Number <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    name="po_poNumber[]" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Description <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    name="po_description[]" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>Unit of Measure (UOM)</label>
                                                                <select class="select2 form-control" name="po_uomId[]">
                                                                    <option value="">--Select UOM--</option>
                                                                    @foreach ($uoms as $uom)
                                                                        <option value="{{ $uom->id }}">
                                                                            {{ $uom->measurement }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>Quantity <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control po-qty"
                                                                    name="po_qty[]" step="0.01" min="0" required
                                                                    oninput="calculatePoAmounts(this)">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>Unit Cost <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control po-unitCost"
                                                                    name="po_unitCost[]" step="0.01" min="0"
                                                                    required oninput="calculatePoAmounts(this)">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>VAT %</label>
                                                                <input type="number" class="form-control po-vat"
                                                                    name="po_vat[]" step="0.01" min="0"
                                                                    max="100" oninput="calculatePoAmounts(this)">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Sub Cost</label>
                                                                <input type="number" class="form-control po-subcost"
                                                                    readonly style="background-color: #f0f0f0;">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>VAT Amount</label>
                                                                <input type="number" class="form-control po-vatAmount"
                                                                    readonly style="background-color: #f0f0f0;">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Sub Net</label>
                                                                <input type="number" class="form-control po-subnet"
                                                                    readonly style="background-color: #f0f0f0;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <button type="button" class="btn btn-sm btn-danger remove-po"
                                                            onclick="removePoItem(this)" style="display: none;">
                                                            <i class="fe fe-trash"></i> Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right mb-3">
                                            <button type="button" class="btn btn-sm btn-secondary"
                                                onclick="addPoItem()">
                                                <i class="fe fe-plus"></i> Add Another PO
                                            </button>
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
                                            <th rowspan="1">Category</th>
                                            <th rowspan="1">Expense Account</th>
                                            <th rowspan="1">Description</th>
                                            <th rowspan="1">Location</th>
                                            <th rowspan="1">Status</th>
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
                                                    {{ $list->categoryName ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    {{ $list->expenseAccountName ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    {{ Str::limit($list->description ?? 'N/A', 50) }}
                                                </td>
                                                <td>
                                                    {{ $list->location ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    @if ($list->status == 'Active')
                                                        <span class="badge bg-success">Active</span>
                                                    @elseif ($list->status == 'Inactive')
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a class="btn btn-sm bg-success-light"
                                                        href="javascript: editfunc('{{ $list->id }}','{{ $list->projectCode }}','{{ $list->name }}','{{ $list->description }}','{{ $list->categoryId }}','{{ $list->location }}','{{ $list->status }}','{{ $list->clientId ?? '' }}','{{ $list->expenseAccountId ?? '' }}')">
                                                        <i class="fe fe-pencil"></i>
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
                                        <select class="form-control" id="categoryId" name="categoryId">
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
                                        <select class="form-control" id="clientId" name="clientId">
                                            <option value="">--Select--</option>
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Expense Account</label>
                                        <select class="form-control" id="expenseAccountId" name="expenseAccountId">
                                            <option value="">--Select--</option>
                                            @foreach ($accountLookUp as $account)
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
        function editfunc(id, projectCode, name, description, categoryId, location, status, clientId, expenseAccountId) {
            document.getElementById('id').value = id;
            document.getElementById('projectCode').value = projectCode || '';
            document.getElementById('name').value = name;
            document.getElementById('description').value = description || '';
            document.getElementById('categoryId').value = categoryId || '';
            document.getElementById('location').value = location || '';
            document.getElementById('status').value = status || 1;
            document.getElementById('clientId').value = clientId || '';
            document.getElementById('expenseAccountId').value = expenseAccountId || '';

            $("#edit_details").modal('show')
        }

        function deletefunc(id) {
            document.getElementById('deleteid').value = id;

            $("#delete_modal").modal('show')
        }

        let poIndex = 1;

        function addPoItem() {
            const container = document.getElementById('po-container');
            const firstItem = container.querySelector('.po-item');
            const newItem = firstItem.cloneNode(true);

            // Clear input values
            newItem.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => {
                if (!input.readOnly) {
                    input.value = '';
                } else {
                    input.value = '0.00';
                }
            });

            // Reset select dropdowns
            newItem.querySelectorAll('select').forEach(select => {
                select.value = '';
            });

            // Show remove button for all items
            container.querySelectorAll('.remove-po').forEach(btn => {
                btn.style.display = 'inline-block';
            });

            container.appendChild(newItem);
            poIndex++;
        }

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

        function calculatePoAmounts(element) {
            const poItem = element.closest('.po-item');
            const qty = parseFloat(poItem.querySelector('.po-qty').value) || 0;
            const unitCost = parseFloat(poItem.querySelector('.po-unitCost').value) || 0;
            const vat = parseFloat(poItem.querySelector('.po-vat').value) || 0;

            const subcost = qty * unitCost;
            const vatAmount = subcost * (vat / 100);
            const subnet = subcost + vatAmount;

            poItem.querySelector('.po-subcost').value = subcost.toFixed(2);
            poItem.querySelector('.po-vatAmount').value = vatAmount.toFixed(2);
            poItem.querySelector('.po-subnet').value = subnet.toFixed(2);
        }
    </script>
@endsection
<!-- /Page Wrapper -->
