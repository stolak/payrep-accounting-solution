<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Field Expense
@endsection
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Fund disbursement</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Field Expense</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            @include('_partialView.nofication')

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
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">Field Expense</h4>
                                <a href="{{ url('/fund-disbursement' . (!empty($projectId) ? '?projectId=' . $projectId : '')) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="fe fe-external-link"></i> Goto Vendor Disbursement
                                </a>
                            </div>
                            <div class="card-body">
                                <form method="post">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="projectId" value="{{ $projectId }}">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Expense Type <span class="text-danger">*</span></label>
                                                <?php if ($budgetId == '') {
                                                    $budgetId = old('budgetId');
                                                } ?>
                                                <select class="select2 form-control" name="budgetId" id="budgetId"
                                                    required>
                                                    <option value="">--Select Budget--</option>
                                                    @foreach ($budgets as $budget)
                                                        <option value="{{ $budget->id }}"
                                                            {{ $budgetId == $budget->id ? 'selected' : '' }}>
                                                            {{ $budget->budgetCategoryName }} - {{ $budget->budgetName }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Payment Milestone (Optional)</label>
                                                <?php if ($paymentMilestoneId == '') {
                                                    $paymentMilestoneId = old('paymentMilestoneId');
                                                } ?>
                                                <select class="select2 form-control" name="paymentMilestoneId"
                                                    id="paymentMilestoneId">
                                                    <option value="">--Select Milestone--</option>
                                                    @foreach ($paymentMilestones as $milestone)
                                                        <option value="{{ $milestone->id }}"
                                                            {{ $paymentMilestoneId == $milestone->id ? 'selected' : '' }}>
                                                            {{ $milestone->milestone }} ({{ $milestone->percentage }}%)
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Reference Number <span class="text-danger">*</span></label>
                                                <?php if ($reference_number == '') {
                                                    $reference_number = old('reference_number');
                                                } ?>
                                                <input type="text" class="form-control" value="{{ $reference_number }}"
                                                    name="reference_number" id="reference_number" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Amount <span class="text-danger">*</span></label>
                                                <?php if ($amount == '') {
                                                    $amount = old('amount');
                                                } ?>
                                                <input type="number" class="form-control" value="{{ $amount }}"
                                                    name="amount" id="amount" step="0.01" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Transaction Date <span class="text-danger">*</span></label>
                                                <?php if ($transactionDate == '') {
                                                    $transactionDate = old('transactionDate', date('Y-m-d'));
                                                } ?>
                                                <input type="date" class="form-control" value="{{ $transactionDate }}"
                                                    name="transactionDate" id="transactionDate" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
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
                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary" name="addnew">Save
                                        </button>
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
                                <h4 class="card-title">Field Expenses</h4>
                                @if (!empty($totalFieldExpense))
                                    <div class="mt-2">
                                        <span class="badge bg-info">
                                            Total Amount: {{ number_format($totalFieldExpense, 2) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-center mb-0">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Budget</th>
                                                <th>Milestone</th>
                                                <th>Reference Number</th>
                                                <th>Amount</th>
                                                <th>Transaction Date</th>
                                                <th>Description</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $i = 1; @endphp
                                            @if ($fieldExpenses->count() > 0)
                                                @foreach ($fieldExpenses as $expense)
                                                    <tr>
                                                        <td>{{ $i++ }}</td>
                                                        <td>{{ $expense->budgetName }}</td>
                                                        <td>{{ $expense->milestone ?? 'N/A' }}</td>
                                                        <td>{{ $expense->reference_number }}</td>
                                                        <td style="text-align: right;">
                                                            {{ number_format($expense->debit, 2, '.', ',') }}</td>
                                                        <td>{{ date('Y-m-d', strtotime($expense->transactionDate)) }}</td>
                                                        <td>{{ $expense->description }}</td>
                                                        <td>
                                                            @if ($expense->status == 'Approved')
                                                                <span class="badge bg-success">Approved</span>
                                                            @else
                                                                <span class="badge bg-warning">Pending</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($expense->status != 'Approved')
                                                                <a class="btn btn-sm bg-success-light"
                                                                    href="javascript: editfunc('{{ $expense->id }}','{{ $expense->budgetId }}','{{ $expense->paymentMilestoneId }}','{{ addslashes($expense->reference_number) }}','{{ $expense->debit }}','{{ $expense->transactionDate }}','{{ addslashes($expense->description) }}')">
                                                                    <i class="fe fe-pencil"></i>
                                                                </a>
                                                                <a class="btn btn-sm bg-info-light"
                                                                    href="javascript: approvefunc('{{ $expense->id }}')">
                                                                    <i class="fe fe-check"></i>
                                                                </a>
                                                            @endif
                                                            <a class="btn btn-sm bg-danger-light"
                                                                href="javascript: deletefunc('{{ $expense->id }}')">
                                                                <i class="fe fe-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="9" class="text-center">No field expense records found
                                                        for this project.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <p class="text-center text-muted">Please select a project to view and manage field
                                    expenses.</p>
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
                    <form method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="projectId" value="{{ $projectId }}">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Field Expense</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Budget <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_budgetId" name="budgetId" required>
                                    <option value="">--Select Budget--</option>
                                    @foreach ($budgets as $budget)
                                        <option value="{{ $budget->id }}">
                                            {{ $budget->budgetCategoryName }} - {{ $budget->budgetName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Payment Milestone (Optional)</label>
                                <select class="form-control" id="edit_paymentMilestoneId" name="paymentMilestoneId">
                                    <option value="">--Select Milestone--</option>
                                    @foreach ($paymentMilestones as $milestone)
                                        <option value="{{ $milestone->id }}">{{ $milestone->milestone }}
                                            ({{ $milestone->percentage }}%)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Amount <span class="text-danger">*</span></label>
                                <input type="number" id="edit_amount" name="amount" class="form-control"
                                    step="0.01" min="0" required>
                            </div>
                            <div class="form-group">
                                <label>Reference Number <span class="text-danger">*</span></label>
                                <input type="text" id="edit_reference_number" name="reference_number"
                                    class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Transaction Date <span class="text-danger">*</span></label>
                                <input type="date" id="edit_transactionDate" name="transactionDate"
                                    class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Description <span class="text-danger">*</span></label>
                                <input type="text" id="edit_description" name="description" class="form-control"
                                    required>
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
                    <form method="post">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="form-content p-2">
                                <h4 class="modal-title">Approve</h4>
                                <p class="mb-4">Are you sure you want to approve this field expense?</p>
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
                    <form method="post">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="form-content p-2">
                                <h4 class="modal-title">Delete</h4>
                                <p class="mb-4">Are you sure want to delete this field expense?</p>
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

        function editfunc(id, budgetId, paymentMilestoneId, referenceNumber, amount, transactionDate, description) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_budgetId').value = budgetId || '';
            document.getElementById('edit_paymentMilestoneId').value = paymentMilestoneId || '';
            document.getElementById('edit_reference_number').value = referenceNumber || '';
            document.getElementById('edit_amount').value = amount || '';
            document.getElementById('edit_transactionDate').value = transactionDate || '';
            document.getElementById('edit_description').value = description || '';
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
