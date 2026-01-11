<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Payment Milestone Setup
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
                            <li class="breadcrumb-item active">Payment Milestone Setup</li>
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
                                <h4 class="card-title">Add Payment Milestone</h4>
                            </div>
                            <div class="card-body">
                                <form method="post" id="addMilestoneForm">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="projectId" value="{{ $projectId }}">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Milestone <span class="text-danger">*</span></label>
                                                <?php if ($milestone == '') {
                                                    $milestone = old('milestone');
                                                } ?>
                                                <input type="text" class="form-control" value="{{ $milestone }}"
                                                    name="milestone" id="milestone" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Percentage (%) <span class="text-danger">*</span></label>
                                                <?php if ($percentage == '') {
                                                    $percentage = old('percentage');
                                                } ?>
                                                <input type="number" class="form-control" value="{{ $percentage }}"
                                                    name="percentage" id="percentage" step="0.01" min="0"
                                                    max="100" required oninput="validatePercentage()">
                                                <small class="text-muted">Remaining: <span
                                                        id="remainingPercentage">{{ 100 - ($totalPercentage ?? 0) }}</span>%</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Rank <span class="text-danger">*</span></label>
                                                <?php if ($rank == '') {
                                                    $rank = old('rank');
                                                } ?>
                                                <input type="number" class="form-control" value="{{ $rank }}"
                                                    name="rank" id="rank" min="1" required>
                                                <small class="text-muted">Order of milestone execution</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary" name="addnew">Add Milestone</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <!-- List of payment milestones -->
                        <div class="card card-table">
                            <div class="card-header">
                                <h4 class="card-title">Payment Milestones</h4>
                                @if (!empty($totalPercentage))
                                    <div class="mt-2">
                                        <span
                                            class="badge {{ $totalPercentage > 100 ? 'bg-danger' : ($totalPercentage == 100 ? 'bg-success' : 'bg-warning') }}">
                                            Total Percentage: {{ number_format($totalPercentage, 2) }}%
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
                                                <th rowspan="1">Rank</th>
                                                <th rowspan="1">Milestone</th>
                                                <th rowspan="1">Percentage (%)</th>
                                                <th rowspan="1">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $i = 1;
                                            @endphp

                                            @if ($paymentMilestones->count() > 0)
                                                @foreach ($paymentMilestones as $milestone)
                                                    <tr>
                                                        <td>
                                                            {{ $i++ }}
                                                        </td>
                                                        <td>
                                                            {{ $milestone->rank }}
                                                        </td>
                                                        <td>
                                                            {{ $milestone->milestone }}
                                                        </td>
                                                        <td>
                                                            {{ number_format($milestone->percentage, 2) }}%
                                                        </td>
                                                        <td>
                                                            <a class="btn btn-sm bg-success-light"
                                                                href="javascript: editfunc('{{ $milestone->id }}','{{ $milestone->milestone }}','{{ $milestone->percentage }}','{{ $milestone->rank }}')">
                                                                <i class="fe fe-pencil"></i>
                                                            </a>
                                                            <a class="btn btn-sm bg-danger-light"
                                                                href="javascript: deletefunc('{{ $milestone->id }}')">
                                                                <i class="fe fe-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center">No payment milestones added for
                                                        this project yet.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /List of payment milestones -->
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <p class="text-center text-muted">Please select a project to view and manage its payment
                                    milestones.</p>
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
                            <h5 class="modal-title">Edit Payment Milestone</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Milestone <span class="text-danger">*</span></label>
                                <input type="text" id="edit_milestone" name="milestone" class="form-control"
                                    required>
                            </div>
                            <div class="form-group">
                                <label>Percentage (%) <span class="text-danger">*</span></label>
                                <input type="number" id="edit_percentage" name="percentage" class="form-control"
                                    step="0.01" min="0" max="100" required
                                    oninput="validateEditPercentage()">
                                <small class="text-muted">Remaining: <span
                                        id="edit_remainingPercentage">{{ 100 - ($totalPercentage ?? 0) }}</span>%</small>
                            </div>
                            <div class="form-group">
                                <label>Rank <span class="text-danger">*</span></label>
                                <input type="number" id="edit_rank" name="rank" class="form-control" min="1"
                                    required>
                                <small class="text-muted">Order of milestone execution</small>
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
                                <p class="mb-4">Are you sure want to delete this payment milestone?</p>
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

        function validatePercentage() {
            var percentage = parseFloat(document.getElementById('percentage').value) || 0;
            var totalPercentage = {{ $totalPercentage ?? 0 }};
            var remaining = 100 - totalPercentage;
            var remainingSpan = document.getElementById('remainingPercentage');

            if (remainingSpan) {
                remainingSpan.textContent = (remaining - percentage).toFixed(2);

                if (percentage > remaining) {
                    remainingSpan.style.color = 'red';
                    remainingSpan.parentElement.classList.add('text-danger');
                } else {
                    remainingSpan.style.color = '';
                    remainingSpan.parentElement.classList.remove('text-danger');
                }
            }
        }

        function validateEditPercentage() {
            var percentage = parseFloat(document.getElementById('edit_percentage').value) || 0;
            var editId = document.getElementById('edit_id').value;
            var totalPercentage = {{ $totalPercentage ?? 0 }};

            // Get current milestone percentage if editing
            var currentMilestonePercentage = 0;
            @if (!empty($paymentMilestones))
                @foreach ($paymentMilestones as $ms)
                    if (editId == '{{ $ms->id }}') {
                        currentMilestonePercentage = {{ $ms->percentage }};
                    }
                @endforeach
            @endif

            var remaining = 100 - (totalPercentage - currentMilestonePercentage);
            var remainingSpan = document.getElementById('edit_remainingPercentage');

            if (remainingSpan) {
                remainingSpan.textContent = (remaining - percentage).toFixed(2);

                if (percentage > remaining) {
                    remainingSpan.style.color = 'red';
                    remainingSpan.parentElement.classList.add('text-danger');
                } else {
                    remainingSpan.style.color = '';
                    remainingSpan.parentElement.classList.remove('text-danger');
                }
            }
        }

        function editfunc(id, milestone, percentage, rank) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_milestone').value = milestone;
            document.getElementById('edit_percentage').value = percentage;
            document.getElementById('edit_rank').value = rank;

            // Trigger validation
            validateEditPercentage();

            $("#edit_modal").modal('show')
        }

        function deletefunc(id) {
            document.getElementById('deleteid').value = id;
            $("#delete_modal").modal('show')
        }

        // Form validation on submit
        document.getElementById('addMilestoneForm')?.addEventListener('submit', function(e) {
            var percentage = parseFloat(document.getElementById('percentage').value) || 0;
            var totalPercentage = {{ $totalPercentage ?? 0 }};
            var remaining = 100 - totalPercentage;

            if (percentage > remaining) {
                e.preventDefault();
                alert('Percentage cannot exceed remaining ' + remaining.toFixed(2) + '%');
                return false;
            }
        });

        document.getElementById('editForm')?.addEventListener('submit', function(e) {
            var percentage = parseFloat(document.getElementById('edit_percentage').value) || 0;
            var editId = document.getElementById('edit_id').value;
            var totalPercentage = {{ $totalPercentage ?? 0 }};

            // Get current milestone percentage if editing
            var currentMilestonePercentage = 0;
            @if (!empty($paymentMilestones))
                @foreach ($paymentMilestones as $ms)
                    if (editId == '{{ $ms->id }}') {
                        currentMilestonePercentage = {{ $ms->percentage }};
                    }
                @endforeach
            @endif

            var remaining = 100 - (totalPercentage - currentMilestonePercentage);

            if (percentage > remaining) {
                e.preventDefault();
                alert('Percentage cannot exceed remaining ' + remaining.toFixed(2) + '%');
                return false;
            }
        });
    </script>
@endsection
<!-- /Page Wrapper -->
