<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Variable Contribution Setup
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
                            <li class="breadcrumb-item active">Variable Contribution Setup</li>
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
                    <div class="card card-table">
                        <div class="card-header">
                            <h4 class="card-title">Variable Contributions</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Title</th>
                                            <th>Variable</th>
                                            <th>Staff (%)</th>
                                            <th>Company (%)</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1; @endphp
                                        @foreach ($ContributionMaps as $list)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $list->title }}</td>
                                                <td>{{ $list->variableName }}
                                                </td>
                                                <td>{{ number_format((float) $list->staff_percentage, 2, '.', ',') }}</td>
                                                <td>{{ number_format((float) $list->company_percentage, 2, '.', ',') }}
                                                </td>
                                                <td>
                                                    @php
                                                        $st = $list->status ?? 'Active';
                                                    @endphp
                                                    @if ($st === 'Inactive')
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    @else
                                                        <span class="badge badge-success">Active</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a class="btn btn-sm bg-success-light"
                                                        href="javascript: editfunc(
                                                            '{{ $list->id }}',
                                                            '{{ addslashes($list->title) }}',
                                                            '{{ $list->variableId }}',
                                                            '{{ $list->staff_percentage }}',
                                                            '{{ $list->company_percentage }}',
                                                            '{{ $list->status ?? 'Active' }}'
                                                        )"
                                                        title="Edit">
                                                        <i class="fe fe-pencil"></i>
                                                    </a>

                                                </td>
                                            </tr>
                                        @endforeach
                                        @if (count($ContributionMaps) == 0)
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">No records found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /List -->
                </div>
            </div>
        </div>

        <!-- Edit Details Modal -->
        <div class="modal fade" id="edit_details" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Variable Contribution</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Title</label>
                                        <input type="text" id="edit_title" name="title" class="form-control" required
                                            readonly>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Payroll Variable</label>
                                        <select class="form-control" id="edit_variableId" name="variableId" required>
                                            <option value="">--Select--</option>
                                            @foreach ($PayrollVariables as $v)
                                                <option value="{{ $v->id }}">
                                                    {{ $v->variable }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Staff (%)</label>
                                        <input type="number" step="0.01" min="0" max="100"
                                            id="edit_staff_percentage" name="staff_percentage" class="form-control"
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Company (%)</label>
                                        <input type="number" step="0.01" min="0" max="100"
                                            id="edit_company_percentage" name="company_percentage" class="form-control"
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" id="edit_status" name="status" required>
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" id="edit_id" name="id">
                            <div class="form-content p-2">
                                <button type="submit" class="btn btn-primary" name="update">Save Changes</button>
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
                                <p class="mb-4">Are you sure you want to delete?</p>
                                <button type="submit" class="btn btn-primary" name="del">Continue</button>
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

@section('scripts')
    <script>
        function editfunc(id, title, variableId, staffPercentage, companyPercentage, status) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_title').value = title || '';
            document.getElementById('edit_variableId').value = variableId || '';
            document.getElementById('edit_staff_percentage').value = staffPercentage || '';
            document.getElementById('edit_company_percentage').value = companyPercentage || '';
            document.getElementById('edit_status').value = status || 'Active';
            $("#edit_details").modal('show');
        }

        function deletefunc(id) {
            document.getElementById('deleteid').value = id;
            $("#delete_modal").modal('show');
        }
    </script>
@endsection
<!-- /Page Wrapper -->
