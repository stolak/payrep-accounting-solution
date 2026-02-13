<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    User Ledger Assignment
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
                            <li class="breadcrumb-item active">User Ledger Assignment</li>
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
                            <h4 class="card-title">Assign Ledger to User</h4>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>User <span class="text-danger">*</span></label>
                                            <?php if ($userId == '') {
                                                $userId = old('userId');
                                            } ?>
                                            <select class="select2 form-control" name="userId" required>
                                                <option value="">--Select User--</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ $userId == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Ledger <span class="text-danger">*</span></label>
                                            <?php if ($ledgerId == '') {
                                                $ledgerId = old('ledgerId');
                                            } ?>
                                            <select class="select2 form-control" name="ledgerId" required>
                                                <option value="">--Select Ledger--</option>
                                                @foreach ($ledgers as $ledger)
                                                    <option value="{{ $ledger->id }}"
                                                        {{ $ledgerId == $ledger->id ? 'selected' : '' }}>
                                                        {{ $ledger->accountdescription }} ({{ $ledger->accountno }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <div class="form-group w-100">
                                            <button type="submit" class="btn btn-primary btn-block"
                                                name="addnew">Assign</button>
                                        </div>
                                    </div>
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
                            <h4 class="card-title">User Ledger Assignments</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>User Name</th>
                                            <th>Ledger</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1; @endphp
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $user->name }}</td>
                                                <td>
                                                    @if (!empty($user->ledgerId))
                                                        {{ $user->ledgerName }} ({{ $user->ledgerNo }})
                                                    @else
                                                        <span class="text-muted">Not Assigned</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a class="btn btn-sm bg-success-light"
                                                        href="javascript:editfunc('{{ $user->id }}','{{ addslashes($user->name) }}','{{ $user->ledgerId ?? '' }}')">
                                                        <i class="fe fe-pencil"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="edit_modal" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="post">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <h5 class="modal-title">Update User Ledger</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>User</label>
                            <input type="text" id="edit_user_name" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label>Ledger <span class="text-danger">*</span></label>
                            <select class="form-control" id="edit_ledgerId" name="ledgerId" required>
                                <option value="">--Select Ledger--</option>
                                @foreach ($ledgers as $ledger)
                                    <option value="{{ $ledger->id }}">
                                        {{ $ledger->accountdescription }} ({{ $ledger->accountno }})
                                    </option>
                                @endforeach
                            </select>
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
        function editfunc(id, userName, ledgerId) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_user_name').value = userName;
            document.getElementById('edit_ledgerId').value = ledgerId || '';
            $("#edit_modal").modal('show');
        }
    </script>
@endsection
<!-- /Page Wrapper -->


