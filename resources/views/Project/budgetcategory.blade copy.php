<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Budget Classification Setup
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
                            <li class="breadcrumb-item active">Budget Classification Setup</li>
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
                            <h4 class="card-title"> Classification</h4>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Category</label>
                                            <?php if ($category == '') {
                                                $category = old('category');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $category }}" required
                                                name="category">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Is Measure</label>
                                            <input type="hidden" name="isMeasure" id="isMeasureHidden" value="0">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="isMeasure"
                                                    {{ old('isMeasure', $isMeasure ?? 0) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="isMeasure">
                                                    <span id="isMeasureLabel"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Is Milestone</label>
                                            <input type="hidden" name="isMilestone" id="isMilestoneHidden" value="0">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="isMilestone"
                                                    {{ old('isMilestone', $isMilestone ?? 0) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="isMilestone">
                                                    <span id="isMilestoneLabel"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Is Sub Contractor</label>
                                            <input type="hidden" name="isSubContrator" id="isSubContratorHidden"
                                                value="0">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="isSubContrator"
                                                    {{ old('isSubContrator', $isSubContrator ?? 0) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="isSubContrator">
                                                    <span id="isSubContratorLabel"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary" name="addnew">Create</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">

                    <!-- List of budget classifications -->
                    <div class="card card-table">
                        <div class="card-header">
                            <h4 class="card-title">Budget Classifications</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th rowspan="1">S/N</th>
                                            <th rowspan="1">Category</th>
                                            <th rowspan="1">Is Measure</th>
                                            <th rowspan="1">Is Milestone</th>
                                            <th rowspan="1">Is Sub Contractor</th>
                                            <th rowspan="1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                        @endphp

                                        @foreach ($budgetCategories as $list)
                                            <tr>
                                                <td>
                                                    {{ $i++ }}
                                                </td>
                                                <td>
                                                    {{ $list->category }}
                                                </td>
                                                <td>
                                                    @if ($list->isMeasure)
                                                        <span class="badge bg-success">Yes</span>
                                                    @else
                                                        <span class="badge bg-secondary">No</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($list->isMilestone)
                                                        <span class="badge bg-success">Yes</span>
                                                    @else
                                                        <span class="badge bg-secondary">No</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($list->isSubContrator)
                                                        <span class="badge bg-success">Yes</span>
                                                    @else
                                                        <span class="badge bg-secondary">No</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a class="btn btn-sm bg-success-light"
                                                        href="javascript: editfunc('{{ $list->id }}','{{ $list->category }}','{{ $list->isMeasure }}','{{ $list->isMilestone }}','{{ $list->isSubContrator }}')">
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
                    <!-- /List of budget classifications -->

                </div>
            </div>
        </div>

        <!-- Edit Details Modal -->
        <div class="modal fade" id="edit_details" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Budget Classification</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            {{ csrf_field() }}
                            <div class="row ">
                                <div class="col-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Category</label>
                                        <input type="text" id="category" name="category" class="form-control"
                                            style="text-align: left;" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Is Measure</label>
                                        <input type="hidden" name="isMeasure" id="edit_isMeasureHidden" value="0">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="edit_isMeasure">
                                            <label class="form-check-label" for="edit_isMeasure">
                                                <span id="edit_isMeasureLabel">No</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Is Milestone</label>
                                        <input type="hidden" name="isMilestone" id="edit_isMilestoneHidden"
                                            value="0">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="edit_isMilestone">
                                            <label class="form-check-label" for="edit_isMilestone">
                                                <span id="edit_isMilestoneLabel">No</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Is Sub Contractor</label>
                                        <input type="hidden" name="isSubContrator" id="edit_isSubContratorHidden"
                                            value="0">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="edit_isSubContrator">
                                            <label class="form-check-label" for="edit_isSubContrator">
                                                <span id="edit_isSubContratorLabel">No</span>
                                            </label>
                                        </div>
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
                                <input type="hidden" id="deleteid" name="id">
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

        .form-check-input {
            width: 3rem;
            height: 1.5rem;
            cursor: pointer;
        }

        .form-check-label {
            margin-left: 10px;
            cursor: pointer;
        }
    </style>
@endsection
@section('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script>
        function editfunc(id, cat, isMeasure, isMilestone, isSubContrator) {
            document.getElementById('id').value = id;
            document.getElementById('category').value = cat;

            // Set toggle switches
            const isMeasureChecked = isMeasure == 1 || isMeasure == '1';
            const isMilestoneChecked = isMilestone == 1 || isMilestone == '1';
            const isSubContratorChecked = isSubContrator == 1 || isSubContrator == '1';

            document.getElementById('edit_isMeasure').checked = isMeasureChecked;
            document.getElementById('edit_isMilestone').checked = isMilestoneChecked;
            document.getElementById('edit_isSubContrator').checked = isSubContratorChecked;

            // Update hidden values
            document.getElementById('edit_isMeasureHidden').value = isMeasureChecked ? '1' : '0';
            document.getElementById('edit_isMilestoneHidden').value = isMilestoneChecked ? '1' : '0';
            document.getElementById('edit_isSubContratorHidden').value = isSubContratorChecked ? '1' : '0';

            // Update labels
            document.getElementById('edit_isMeasureLabel').textContent = isMeasureChecked ? 'Yes' : 'No';
            document.getElementById('edit_isMilestoneLabel').textContent = isMilestoneChecked ? 'Yes' : 'No';
            document.getElementById('edit_isSubContratorLabel').textContent = isSubContratorChecked ? 'Yes' : 'No';

            $("#edit_details").modal('show')
        }

        // Update labels and hidden values when toggles change
        document.getElementById('isMeasure')?.addEventListener('change', function() {
            document.getElementById('isMeasureLabel').textContent = this.checked ? 'Yes' : 'No';
            document.getElementById('isMeasureHidden').value = this.checked ? '1' : '0';
        });

        document.getElementById('isMilestone')?.addEventListener('change', function() {
            document.getElementById('isMilestoneLabel').textContent = this.checked ? 'Yes' : 'No';
            document.getElementById('isMilestoneHidden').value = this.checked ? '1' : '0';
        });

        document.getElementById('isSubContrator')?.addEventListener('change', function() {
            document.getElementById('isSubContratorLabel').textContent = this.checked ? 'Yes' : 'No';
            document.getElementById('isSubContratorHidden').value = this.checked ? '1' : '0';
        });

        document.getElementById('edit_isMeasure')?.addEventListener('change', function() {
            document.getElementById('edit_isMeasureLabel').textContent = this.checked ? 'Yes' : 'No';
            document.getElementById('edit_isMeasureHidden').value = this.checked ? '1' : '0';
        });

        document.getElementById('edit_isMilestone')?.addEventListener('change', function() {
            document.getElementById('edit_isMilestoneLabel').textContent = this.checked ? 'Yes' : 'No';
            document.getElementById('edit_isMilestoneHidden').value = this.checked ? '1' : '0';
        });

        document.getElementById('edit_isSubContrator')?.addEventListener('change', function() {
            document.getElementById('edit_isSubContratorLabel').textContent = this.checked ? 'Yes' : 'No';
            document.getElementById('edit_isSubContratorHidden').value = this.checked ? '1' : '0';
        });

        // Initialize hidden values on page load
        document.addEventListener('DOMContentLoaded', function() {
            const isMeasureCheckbox = document.getElementById('isMeasure');
            const isMilestoneCheckbox = document.getElementById('isMilestone');
            const isSubContratorCheckbox = document.getElementById('isSubContrator');

            if (isMeasureCheckbox) {
                document.getElementById('isMeasureHidden').value = isMeasureCheckbox.checked ? '1' : '0';
            }
            if (isMilestoneCheckbox) {
                document.getElementById('isMilestoneHidden').value = isMilestoneCheckbox.checked ? '1' : '0';
            }
            if (isSubContratorCheckbox) {
                document.getElementById('isSubContratorHidden').value = isSubContratorCheckbox.checked ? '1' : '0';
            }
        });

        function deletefunc(id) {
            document.getElementById('deleteid').value = id;

            $("#delete_modal").modal('show')
        }
    </script>
@endsection
<!-- /Page Wrapper -->
