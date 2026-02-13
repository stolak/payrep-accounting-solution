<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Fund Disbursement
@endsection
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Fund Disbursement</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Fund Disbursement</li>
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
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">Vendor Fund Disbursement</h4>
                                <a href="{{ url('/field-expense' . (!empty($projectId) ? '?projectId=' . $projectId : '')) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="fe fe-external-link"></i> Goto Field Expense
                                </a>
                            </div>
                            <div class="card-body">
                                <form method="post" id="addDisbursementForm">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="projectId" value="{{ $projectId }}">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Vendor <span class="text-danger">*</span></label>
                                                <?php if ($budgetId == '') {
                                                    $budgetId = old('budgetId');
                                                } ?>
                                                <select class="select2 form-control" name="budgetId" id="budgetId"
                                                    required>
                                                    <option value="">--Select Budget--</option>
                                                    @foreach ($budgets as $budget)
                                                        <option value="{{ $budget->id }}"
                                                            {{ $budgetId == $budget->id ? 'selected' : '' }}>
                                                            {{ $budget->budgetName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Payment Milestone <span class="text-danger">*</span></label>
                                                <?php if ($paymentMilestoneId == '') {
                                                    $paymentMilestoneId = old('paymentMilestoneId');
                                                } ?>
                                                <select class="select2 form-control" name="paymentMilestoneId"
                                                    id="paymentMilestoneId" required>
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
                                                <label>Drawn Ledger <span class="text-danger">*</span></label>
                                                <?php if ($accountId == '') {
                                                    $accountId = old('accountId');
                                                } ?>
                                                <select class="select2 form-control" name="accountId" id="accountId"
                                                    required>
                                                    <option value="">--Select Account--</option>
                                                    @foreach ($accounts as $account)
                                                        <option value="{{ $account->id }}"
                                                            {{ $accountId == $account->id ? 'selected' : '' }}>
                                                            {{ $account->accountdescription }}
                                                            ({{ $account->accountno }})
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
                                                <?php if ($debit == '') {
                                                    $debit = old('debit');
                                                } ?>
                                                <input type="number" class="form-control" value="{{ $debit }}"
                                                    name="debit" id="debit" step="0.01" min="0" required>
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
                        <!-- List of fund disbursements -->
                        <div class="card card-table">
                            <div class="card-header">
                                <h4 class="card-title">Fund Disbursements</h4>
                                @if (!empty($totalDisbursed))
                                    <div class="mt-2">
                                        <span class="badge bg-info">
                                            Total Disbursed: {{ number_format($totalDisbursed, 2) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-center mb-0">
                                        <thead>
                                            <tr>
                                                <th rowspan="1">S/N</th>
                                                <th rowspan="1">Beneficiary</th>
                                                <th rowspan="1">Drawn Ledger</th>
                                                <th rowspan="1">Milestone</th>
                                                <th rowspan="1">Reference Number</th>
                                                <th rowspan="1">Amount</th>
                                                <th rowspan="1">Transaction Date</th>
                                                <th rowspan="1">Status</th>
                                                <th rowspan="1">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $i = 1;
                                                $totalDisbursed = 0;
                                                // Group disbursements by budget
                                                $groupedDisbursements = [];
                                                foreach ($fundDisbursements as $disbursement) {
                                                    $budgetName = $disbursement->budgetName ?? 'Uncategorized';
                                                    if (!isset($groupedDisbursements[$budgetName])) {
                                                        $groupedDisbursements[$budgetName] = [];
                                                    }
                                                    $groupedDisbursements[$budgetName][] = $disbursement;
                                                }
                                            @endphp

                                            @if ($fundDisbursements->count() > 0)
                                                @foreach ($groupedDisbursements as $budgetName => $disbursements)
                                                    @php
                                                        $budgetSubtotal = 0;
                                                        $firstInBudget = true;
                                                    @endphp
                                                    @foreach ($disbursements as $disbursement)
                                                        @php
                                                            $budgetSubtotal += $disbursement->debit;
                                                            $totalDisbursed += $disbursement->debit;
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                {{ $i++ }}
                                                            </td>
                                                            <td>
                                                                @if ($firstInBudget)
                                                                    <strong>{{ $budgetName }}</strong>
                                                                    @php $firstInBudget = false; @endphp
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ $disbursement->accountName ?? 'N/A' }}
                                                                @if (!empty($disbursement->accountNo))
                                                                    ({{ $disbursement->accountNo }})
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ $disbursement->milestone }}
                                                            </td>
                                                            <td>
                                                                {{ $disbursement->reference_number }}
                                                            </td>
                                                            <td style="text-align: right;">
                                                                {{ number_format($disbursement->debit, 2, '.', ',') }}
                                                            </td>
                                                            <td>
                                                                {{ date('Y-m-d', strtotime($disbursement->transactionDate)) }}
                                                            </td>
                                                            <td>
                                                                @if ($disbursement->status == 'Approved')
                                                                    <span class="badge bg-success">Approved</span>
                                                                @else
                                                                    <span class="badge bg-warning">Pending</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($disbursement->status != 'Approved')
                                                                    <a class="btn btn-sm bg-success-light"
                                                                        href="javascript: editfunc('{{ $disbursement->id }}','{{ $disbursement->budgetId }}','{{ $disbursement->accountId }}','{{ $disbursement->paymentMilestoneId }}','{{ addslashes($disbursement->reference_number) }}','{{ $disbursement->debit }}','{{ $disbursement->transactionDate }}')">
                                                                        <i class="fe fe-pencil"></i>
                                                                    </a>
                                                                    <a class="btn btn-sm bg-info-light"
                                                                        href="javascript: approvefunc('{{ $disbursement->id }}')">
                                                                        <i class="fe fe-check"></i>
                                                                    </a>
                                                                @endif
                                                                <a class="btn btn-sm bg-danger-light"
                                                                    href="javascript: deletefunc('{{ $disbursement->id }}')">
                                                                    <i class="fe fe-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    <tr style="background-color: #e8e8e8; font-weight: bold;">
                                                        <td></td>
                                                        <td colspan="3" class="text-right">
                                                            <strong>{{ $budgetName }} Subtotal:</strong>
                                                        </td>
                                                        <td style="text-align: right;">
                                                            <strong>{{ number_format($budgetSubtotal, 2, '.', ',') }}</strong>
                                                        </td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                @endforeach
                                                <tr
                                                    style="background-color: #d0d0d0; font-weight: bold; font-size: 1.1em;">
                                                    <td></td>
                                                    <td colspan="4" class="text-right"><strong>Total
                                                            Disbursed:</strong></td>
                                                    <td style="text-align: right;">
                                                        <strong>{{ number_format($totalDisbursed, 2, '.', ',') }}</strong>
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="9" class="text-center">No fund disbursements recorded
                                                        for this project yet.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /List of fund disbursements -->
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <p class="text-center text-muted">Please select a project to view and manage its fund
                                    disbursements.</p>
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
                            <h5 class="modal-title">Edit Fund Disbursement</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Beneficiary <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_budgetId" name="budgetId" required>
                                    <option value="">--Select Budget--</option>
                                    @foreach ($budgets as $budget)
                                        <option value="{{ $budget->id }}">{{ $budget->budgetName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label>Payment Milestone <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_paymentMilestoneId" name="paymentMilestoneId"
                                    required>
                                    <option value="">--Select Milestone--</option>
                                    @foreach ($paymentMilestones as $milestone)
                                        <option value="{{ $milestone->id }}">{{ $milestone->milestone }}
                                            ({{ $milestone->percentage }}%)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Drawn Ledger <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_accountId" name="accountId" required>
                                    <option value="">--Select Account--</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->accountdescription }}
                                            ({{ $account->accountno }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Amount <span class="text-danger">*</span></label>
                                <input type="number" id="edit_debit" name="debit" class="form-control"
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
                                <p class="mb-4">Are you sure you want to approve this fund disbursement?</p>
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
                                <p class="mb-4">Are you sure want to delete this fund disbursement?</p>
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

        function editfunc(id, budgetId, accountId, paymentMilestoneId, referenceNumber, debit, transactionDate) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_budgetId').value = budgetId;
            document.getElementById('edit_accountId').value = accountId || '';
            document.getElementById('edit_paymentMilestoneId').value = paymentMilestoneId;
            document.getElementById('edit_debit').value = debit;
            document.getElementById('edit_reference_number').value = referenceNumber || '';
            document.getElementById('edit_transactionDate').value = transactionDate;

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
    </script>
@endsection
<!-- /Page Wrapper -->
