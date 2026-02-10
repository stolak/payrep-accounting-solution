<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Project Budget Setup
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
                            <li class="breadcrumb-item active">Project Budget Setup</li>
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
                        <!-- Budget Summary by Classification -->
                        <div class="card card-table">
                            <div class="card-header">
                                <h4 class="card-title">Budget Summary by Classification</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-center mb-0">
                                        <thead>
                                            <tr>
                                                <th rowspan="1">S/N</th>
                                                <th rowspan="1">Budget Classification</th>
                                                <th rowspan="1">Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $i = 1;
                                            @endphp

                                            @if ($budgetSummary->count() > 0)
                                                @foreach ($budgetSummary as $summary)
                                                    <tr>
                                                        <td>
                                                            {{ $i++ }}
                                                        </td>
                                                        <td>
                                                            {{ $summary->categoryName ?? 'Uncategorized' }}
                                                        </td>
                                                        <td style="text-align: right;">
                                                            {{ number_format($summary->totalAmount, 2, '.', ',') }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr style="background-color: #d0d0d0; font-weight: bold; font-size: 1.1em;">
                                                    <td></td>
                                                    <td class="text-right"><strong>Grand Total:</strong></td>
                                                    <td style="text-align: right;">
                                                        <strong>{{ number_format($totalAmount, 2, '.', ',') }}</strong>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="3" class="text-center">No budgets assigned to this
                                                        project yet.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /Budget Summary by Classification -->
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <p class="text-center text-muted">Please select a project to view budget summary by
                                    category.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if (!empty($projectId))
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Add Budget to Project</h4>
                            </div>
                            <div class="card-body">
                                <form method="post" id="addBudgetForm">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="projectId" value="{{ $projectId }}">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Classification <span class="text-danger">*</span></label>
                                                <?php if ($classificationId == '') {
                                                    $classificationId = old('classificationId');
                                                } ?>
                                                <select class="select2 form-control" name="classificationId"
                                                    id="classificationId" required onchange="selectClassification()">
                                                    <option value="">--Select Classification--</option>
                                                    @foreach ($budgetCategories as $category)
                                                        <option value="{{ $category->id }}"
                                                            {{ $classificationId == $category->id ? 'selected' : '' }}>
                                                            {{ $category->category }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Budget/sub contractor <span class="text-danger">*</span></label>
                                                <?php if ($budgetId == '') {
                                                    $budgetId = old('budgetId');
                                                } ?>
                                                <select class="select2 form-control" name="budgetId" id="budgetId" required
                                                    onchange="handleBudgetChange()">
                                                    <option value="">--Select Budget--</option>
                                                    @foreach ($budgets as $budget)
                                                        <option value="{{ $budget->id }}"
                                                            data-classificationid="{{ $budget->classificationId }}"
                                                            data-ismeasure="{{ $budget->isMeasure ?? 0 }}"
                                                            {{ $budgetId == $budget->id ? 'selected' : '' }}>
                                                            {{ $budget->budgetName }} - {{ $budget->budgetCategoryName }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2" id="unitFieldContainer">
                                            <div class="form-group">
                                                <label>Unit</label>
                                                <?php if ($unit == '') {
                                                    $unit = old('unit');
                                                } ?>
                                                <input type="number" class="form-control" value="{{ $unit }}"
                                                    name="unit" id="unit" step="0.01" min="0"
                                                    oninput="calculateAmount(); validateAmount();">
                                            </div>
                                        </div>
                                        <div class="col-md-2" id="unitCostFieldContainer">
                                            <div class="form-group">
                                                <label>Unit Cost</label>
                                                <?php if ($unitCost == '') {
                                                    $unitCost = old('unitCost');
                                                } ?>
                                                <input type="number" class="form-control" value="{{ $unitCost }}"
                                                    name="unitCost" id="unitCost" step="0.01" min="0"
                                                    oninput="calculateAmount(); validateAmount();">
                                            </div>
                                        </div>
                                        <div class="col-md-3" id="amountFieldContainer">
                                            <div class="form-group">
                                                <label>Amount <span id="amountRequired"
                                                        class="text-danger">*</span></label>
                                                <?php if ($amount == '') {
                                                    $amount = old('amount');
                                                } ?>
                                                <input type="number" class="form-control" value="{{ $amount }}"
                                                    name="amount" id="amount" step="0.01" min="0"
                                                    oninput="validateAmount()">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Note</label>
                                                <?php if ($note == '') {
                                                    $note = old('note');
                                                } ?>
                                                <textarea class="form-control" name="note" id="note" rows="3" placeholder="Additional notes...">{{ $note }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary" name="addnew">Add Budget</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <!-- List of project budgets -->
                        <div class="card card-table">
                            <div class="card-header">
                                <h4 class="card-title">Project Budgets</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-center mb-0">
                                        <thead>
                                            <tr>
                                                <th rowspan="1">S/N</th>

                                                <th rowspan="1">Budget Name</th>
                                                <th rowspan="1">Unit</th>
                                                <th rowspan="1">Unit Cost</th>
                                                <th rowspan="1">Amount</th>
                                                <th rowspan="1">Note</th>
                                                <th rowspan="1">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $i = 1;
                                                $totalAmount = 0;
                                                // Group budgets by classification
                                                $groupedBudgets = [];
                                                foreach ($projectBudgets as $list) {
                                                    $categoryName = $list->budgetCategoryName ?? 'Uncategorized';
                                                    if (!isset($groupedBudgets[$categoryName])) {
                                                        $groupedBudgets[$categoryName] = [];
                                                    }
                                                    $groupedBudgets[$categoryName][] = $list;
                                                }
                                            @endphp

                                            @if ($projectBudgets->count() > 0)
                                                @foreach ($groupedBudgets as $categoryName => $budgets)
                                                    @php
                                                        $categorySubtotal = 0;
                                                        $firstInCategory = true;
                                                    @endphp
                                                    @foreach ($budgets as $list)
                                                        @php
                                                            $categorySubtotal += $list->amount;
                                                            $totalAmount += $list->amount;
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                {{ $i++ }}
                                                            </td>

                                                            <td>
                                                                {{ $list->budgetName }}
                                                            </td>
                                                            <td style="text-align: right;">
                                                                {{ $list->unit ? number_format($list->unit, 2, '.', ',') : '-' }}
                                                            </td>
                                                            <td style="text-align: right;">
                                                                {{ $list->unitCost ? number_format($list->unitCost, 2, '.', ',') : '-' }}
                                                            </td>
                                                            <td style="text-align: right;">
                                                                {{ number_format($list->amount, 2, '.', ',') }}
                                                            </td>
                                                            <td>
                                                                @if (!empty($list->note))
                                                                    <span title="{{ $list->note }}"
                                                                        data-toggle="tooltip" data-placement="top">
                                                                        {{ Str::limit($list->note, 30) }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-sm bg-success-light"
                                                                    href="javascript:void(0)"
                                                                    onclick="editfunc('{{ $list->id }}','{{ $list->budgetId }}','{{ $list->classificationId }}','{{ $list->amount }}','{{ $list->unit ?? '' }}','{{ $list->unitCost ?? '' }}',{{ json_encode($list->note ?? '') }})">
                                                                    <i class="fe fe-pencil"></i>
                                                                </a>
                                                                <a class="btn btn-sm bg-danger-light"
                                                                    href="javascript: deletefunc('{{ $list->id }}')">
                                                                    <i class="fe fe-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    <tr style="background-color: #e8e8e8; font-weight: bold;">
                                                        <td></td>
                                                        <td class="text-right">
                                                            <strong>{{ $categoryName }} Subtotal:</strong>
                                                        </td>

                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td style="text-align: right;">
                                                            <strong>{{ number_format($categorySubtotal, 2, '.', ',') }}</strong>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                @endforeach
                                                <tr
                                                    style="background-color: #d0d0d0; font-weight: bold; font-size: 1.1em;">

                                                    <td colspan="5" class="text-right"><strong>Grand Total:</strong>
                                                    </td>
                                                    <td style="text-align: right;">
                                                        <strong>{{ number_format($totalAmount, 2, '.', ',') }}</strong>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="8" class="text-center">No budgets assigned to this
                                                        project
                                                        yet.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /List of project budgets -->
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <p class="text-center text-muted">Please select a project to view and manage its budgets.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="edit_modal" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form method="post" id="editForm">
                        {{ csrf_field() }}
                        <input type="hidden" name="projectId" value="{{ $projectId }}">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Project Budget</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Classification</label>
                                <select class="select2 form-control" id="edit_classificationId" name="classificationId"
                                    onchange="handleEditClassificationChange()">
                                    <option value="">--Select Classification--</option>
                                    @foreach ($budgetCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->category }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Select to filter budgets</small>
                            </div>
                            <div class="form-group">
                                <label>Budget <span class="text-danger">*</span></label>
                                <select class="select2 form-control" id="edit_budgetId" name="budgetId" required
                                    onchange="handleEditBudgetChange()">
                                    <option value="">--Select Budget--</option>
                                    @foreach ($budgets as $budget)
                                        <option value="{{ $budget->id }}"
                                            data-classificationid="{{ $budget->classificationId }}"
                                            data-ismeasure="{{ $budget->isMeasure ?? 0 }}">
                                            {{ $budget->budgetName }} - {{ $budget->budgetCategoryName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-4" id="edit_unitFieldContainer">
                                    <div class="form-group">
                                        <label>Unit</label>
                                        <input type="number" class="form-control" id="edit_unit" name="unit"
                                            step="0.01" min="0"
                                            oninput="calculateEditAmount(); validateEditAmount();">
                                    </div>
                                </div>
                                <div class="col-md-4" id="edit_unitCostFieldContainer">
                                    <div class="form-group">
                                        <label>Unit Cost</label>
                                        <input type="number" class="form-control" id="edit_unitCost" name="unitCost"
                                            step="0.01" min="0"
                                            oninput="calculateEditAmount(); validateEditAmount();">
                                    </div>
                                </div>
                                <div class="col-md-4" id="edit_amountFieldContainer">
                                    <div class="form-group">
                                        <label>Amount <span id="edit_amountRequired" class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="edit_amount" name="amount"
                                            step="0.01" min="0" oninput="validateEditAmount()">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Note</label>
                                <textarea class="form-control" id="edit_note" name="note" rows="3" placeholder="Additional notes..."></textarea>
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
                                <p class="mb-4">Are you sure want to delete this budget from the project?</p>
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

        function selectClassification() {
            var classificationId = document.getElementById('classificationId').value;
            // Get projectId from hidden input in the form
            var projectIdInput = document.querySelector('input[name="projectId"]');
            var projectId = projectIdInput ? projectIdInput.value : '{{ $projectId ?? '' }}';

            // Create a form to submit with classificationId and projectId
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = window.location.pathname;

            // Add CSRF token
            var csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Add projectId
            var projectInput = document.createElement('input');
            projectInput.type = 'hidden';
            projectInput.name = 'projectId';
            projectInput.value = projectId;
            form.appendChild(projectInput);

            // Add classificationId (even if empty, to clear filter)
            var classificationInput = document.createElement('input');
            classificationInput.type = 'hidden';
            classificationInput.name = 'classificationId';
            classificationInput.value = classificationId || '';
            form.appendChild(classificationInput);

            // Add select_project flag to maintain project selection
            var selectProjectInput = document.createElement('input');
            selectProjectInput.type = 'hidden';
            selectProjectInput.name = 'select_project';
            selectProjectInput.value = '1';
            form.appendChild(selectProjectInput);

            document.body.appendChild(form);
            form.submit();
        }

        function handleEditClassificationChange() {
            var classificationId = document.getElementById('edit_classificationId').value;
            var budgetSelect = document.getElementById('edit_budgetId');

            // Check if Select2 is initialized and destroy it
            var isSelect2 = $(budgetSelect).hasClass('select2-hidden-accessible');
            if (isSelect2) {
                $(budgetSelect).select2('destroy');
            }

            // Clear current selection
            budgetSelect.value = '';

            // Show/hide budget options based on classification
            for (var i = 0; i < budgetSelect.options.length; i++) {
                var option = budgetSelect.options[i];
                if (option.value === '') {
                    // Keep the default "--Select Budget--" option visible
                    option.hidden = false;
                } else {
                    var optionClassificationId = option.getAttribute('data-classificationid');
                    if (classificationId === '' || optionClassificationId === classificationId) {
                        option.hidden = false;
                    } else {
                        option.hidden = true;
                    }
                }
            }

            // Reinitialize Select2 if it was initialized before
            if (isSelect2) {
                $(budgetSelect).select2();
            }

            // Clear unit, unitCost, and amount when classification changes
            document.getElementById('edit_unit').value = '';
            document.getElementById('edit_unitCost').value = '';
            document.getElementById('edit_amount').value = '';
            handleEditBudgetChange();
        }

        function handleBudgetChange() {
            var budgetSelect = document.getElementById('budgetId');
            var selectedOption = budgetSelect.options[budgetSelect.selectedIndex];
            var isMeasure = selectedOption ? selectedOption.getAttribute('data-ismeasure') : '0';

            var unitContainer = document.getElementById('unitFieldContainer');
            var unitCostContainer = document.getElementById('unitCostFieldContainer');
            var amountContainer = document.getElementById('amountFieldContainer');

            // Clear unit and unitCost values when hiding
            if (isMeasure == '1' || isMeasure == 1) {
                // Show Unit and Unit Cost fields
                unitContainer.style.display = 'block';
                unitCostContainer.style.display = 'block';
                // amountContainer.className = 'col-md-4';
            } else {
                // Hide Unit and Unit Cost fields
                unitContainer.style.display = 'none';
                unitCostContainer.style.display = 'none';
                // amountContainer.className = 'col-md-12';
                // Clear unit and unitCost values
                document.getElementById('unit').value = '';
                document.getElementById('unitCost').value = '';
                // Trigger validation
                validateAmount();
            }
        }

        function handleEditBudgetChange() {
            var budgetSelect = document.getElementById('edit_budgetId');
            var selectedOption = budgetSelect.options[budgetSelect.selectedIndex];
            var isMeasure = selectedOption ? selectedOption.getAttribute('data-ismeasure') : '0';

            var unitContainer = document.getElementById('edit_unitFieldContainer');
            var unitCostContainer = document.getElementById('edit_unitCostFieldContainer');
            var amountContainer = document.getElementById('edit_amountFieldContainer');

            // Clear unit and unitCost values when hiding
            if (isMeasure == '1' || isMeasure == 1) {
                // Show Unit and Unit Cost fields
                unitContainer.style.display = 'block';
                unitCostContainer.style.display = 'block';
            } else {
                // Hide Unit and Unit Cost fields
                unitContainer.style.display = 'none';
                unitCostContainer.style.display = 'none';

                // Clear unit and unitCost values
                document.getElementById('edit_unit').value = '';
                document.getElementById('edit_unitCost').value = '';
                // Trigger validation
                validateEditAmount();
            }
        }

        function calculateAmount() {
            var unit = parseFloat(document.getElementById('unit').value) || 0;
            var unitCost = parseFloat(document.getElementById('unitCost').value) || 0;
            var amountInput = document.getElementById('amount');

            if (unit > 0 && unitCost > 0) {
                var calculatedAmount = unit * unitCost;
                amountInput.value = calculatedAmount.toFixed(2);
                amountInput.readOnly = true;
                amountInput.style.backgroundColor = '#f0f0f0';
                document.getElementById('amountRequired').style.display = 'none';
            } else {
                amountInput.readOnly = false;
                amountInput.style.backgroundColor = '';
                validateAmount();
            }
        }

        function validateAmount() {
            var unit = parseFloat(document.getElementById('unit').value) || 0;
            var unitCost = parseFloat(document.getElementById('unitCost').value) || 0;
            var amountInput = document.getElementById('amount');
            var amountRequired = document.getElementById('amountRequired');

            // If one is present, the other must be present
            var unitValue = document.getElementById('unit').value.trim();
            var unitCostValue = document.getElementById('unitCost').value.trim();

            if ((unitValue && !unitCostValue) || (!unitValue && unitCostValue)) {
                if (unitValue && !unitCostValue) {
                    document.getElementById('unitCost').setCustomValidity('Unit Cost is required when Unit is provided.');
                } else {
                    document.getElementById('unit').setCustomValidity('Unit is required when Unit Cost is provided.');
                }
            } else {
                document.getElementById('unit').setCustomValidity('');
                document.getElementById('unitCost').setCustomValidity('');
            }

            // If both are absent or 0, amount is required
            if (unit == 0 && unitCost == 0) {
                amountInput.required = true;
                amountRequired.style.display = 'inline';
            } else {
                amountInput.required = false;
                amountRequired.style.display = 'none';
            }

            return true;
        }

        function calculateEditAmount() {
            var unit = parseFloat(document.getElementById('edit_unit').value) || 0;
            var unitCost = parseFloat(document.getElementById('edit_unitCost').value) || 0;
            var amountInput = document.getElementById('edit_amount');

            if (unit > 0 && unitCost > 0) {
                var calculatedAmount = unit * unitCost;
                amountInput.value = calculatedAmount.toFixed(2);
                amountInput.readOnly = true;
                amountInput.style.backgroundColor = '#f0f0f0';
                document.getElementById('edit_amountRequired').style.display = 'none';
            } else {
                amountInput.readOnly = false;
                amountInput.style.backgroundColor = '';
                validateEditAmount();
            }
        }

        function validateEditAmount() {
            var unit = parseFloat(document.getElementById('edit_unit').value) || 0;
            var unitCost = parseFloat(document.getElementById('edit_unitCost').value) || 0;
            var amountInput = document.getElementById('edit_amount');
            var amountRequired = document.getElementById('edit_amountRequired');

            // If one is present, the other must be present
            var unitValue = document.getElementById('edit_unit').value.trim();
            var unitCostValue = document.getElementById('edit_unitCost').value.trim();

            if ((unitValue && !unitCostValue) || (!unitValue && unitCostValue)) {
                if (unitValue && !unitCostValue) {
                    document.getElementById('edit_unitCost').setCustomValidity(
                        'Unit Cost is required when Unit is provided.');
                } else {
                    document.getElementById('edit_unit').setCustomValidity('Unit is required when Unit Cost is provided.');
                }
            } else {
                document.getElementById('edit_unit').setCustomValidity('');
                document.getElementById('edit_unitCost').setCustomValidity('');
            }

            // If both are absent or 0, amount is required
            if (unit == 0 && unitCost == 0) {
                amountInput.required = true;
                amountRequired.style.display = 'inline';
            } else {
                amountInput.required = false;
                amountRequired.style.display = 'none';
            }

            return true;
        }

        function editfunc(id, budgetId, classificationId, amount, unit, unitCost, note) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_classificationId').value = classificationId || '';
            document.getElementById('edit_budgetId').value = budgetId;
            document.getElementById('edit_unit').value = unit || '';
            document.getElementById('edit_unitCost').value = unitCost || '';
            document.getElementById('edit_amount').value = amount || '';
            document.getElementById('edit_note').value = note || '';

            // Filter budgets based on classification
            handleEditClassificationChange();

            // Set the budget after filtering
            document.getElementById('edit_budgetId').value = budgetId;
            if (document.getElementById('edit_budgetId').classList.contains('select2-hidden-accessible')) {
                $('#edit_budgetId').trigger('change');
            }

            // Handle visibility based on selected budget's isMeasure
            handleEditBudgetChange();

            // Trigger validation to set required state
            validateEditAmount();

            $("#edit_modal").modal('show')
        }

        function deletefunc(id) {
            document.getElementById('deleteid').value = id;
            $("#delete_modal").modal('show')
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap tooltips for note field
            if (typeof $ !== 'undefined' && $.fn.tooltip) {
                $('[data-toggle="tooltip"]').tooltip();
            }

            // Check if budget is already selected and handle visibility
            var budgetSelect = document.getElementById('budgetId');
            if (budgetSelect && budgetSelect.value) {
                handleBudgetChange();
            } else {
                // If no budget selected, hide unit and unitCost fields by default
                var unitContainer = document.getElementById('unitFieldContainer');
                var unitCostContainer = document.getElementById('unitCostFieldContainer');
                var amountContainer = document.getElementById('amountFieldContainer');
                if (unitContainer && unitCostContainer && amountContainer) {
                    unitContainer.style.display = 'none';
                    unitCostContainer.style.display = 'none';
                }
            }
        });

        // Form validation on submit
        document.getElementById('addBudgetForm').addEventListener('submit', function(e) {
            if (!validateAmount()) {
                e.preventDefault();
                return false;
            }
        });

        document.getElementById('editForm').addEventListener('submit', function(e) {
            if (!validateEditAmount()) {
                e.preventDefault();
                return false;
            }
        });
    </script>
@endsection
<!-- /Page Wrapper -->
