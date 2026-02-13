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
                            <li class="breadcrumb-item active">Petty Expense Account Setup</li>
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
                            <h4 class="card-title">Administrative Expense Setup</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" name="mainform" id="mainform">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Administrative Expense</label>
                                            <?php if ($particular == '') {
                                                $particular = old('particular');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $particular }}" required
                                                name="particular">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Lookup Expense Account</label>
                                            <select class="form-control" name="accountid">
                                                <option value="">--Select--</option>
                                                @foreach ($DefaultAccountLookUp as $list)
                                                    <option value="{{ $list->id }}"
                                                        {{ old('accountid') == $list->id || $accountid == $list->id ? 'selected' : '' }}>
                                                        {{ $list->accountdescription }}({{ $list->accountno }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><br></label>
                                            <br>
                                            <button class="btn btn-success" type="submit" name="add">Add New</button>
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
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Administrative Expense List</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" style="font-size: 11px;">
                                <table id="mytable" class="table table-bordered table-striped table-highlight">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Administrative Expense</th>
                                            <th>Lookup Expense Account</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                        @endphp
                                        @foreach ($DefaultAccount as $list)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $list->particular }}</td>
                                                <td>{{ $list->AccountName }}</td>
                                                <td>
                                                    <a class="btn btn-sm bg-success-light"
                                                        onclick="editfunc('{{ $list->id }}','{{ $list->expensenid }}','{{ $list->particular }}')"><i
                                                            class="fe fe-pencil"></i>
                                                    </a>&nbsp;
                                                    <a class="btn btn-sm bg-danger-light"
                                                        onclick="deletefunc('{{ $list->id }}')" ">
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
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal fade">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Record</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-horizontal" method="post" role="form">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" class="form-control" id="id" name="id">
                            <div class="form-group">
                                <label class="control-label">Particular:</label>
                                <input type="text" class="form-control" id="brand" name="particular">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Lookup Expense Account:</label>
                                <select class="form-control" id="manu" name="expensenid">
                                    <option value="">--Select--</option>
                                    @foreach ($DefaultAccountLookUp as $list)
                                        <option value="{{ $list->id }}">
                                            {{ $list->accountdescription }}({{ $list->accountno }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" name="update">Update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Edit Modal -->

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal fade">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Delete Record</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-horizontal" method="post" role="form">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <input type="hidden" class="form-control" id="deleteid" name="id" value="">
                        <center>
                            <h3>Are you sure?</h3>
                        </center>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="del" class="btn btn-success">Yes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Delete Modal -->
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
        function editfunc(id, manu, brand) {
            document.getElementById('id').value = id;
            document.getElementById('manu').value = manu;
            document.getElementById('brand').value = brand;
            $("#editModal").modal('show')
        }

        function deletefunc(id) {
            document.getElementById('deleteid').value = id;
            $("#deleteModal").modal('show')
        }

        function Reload() {
            document.forms["mainform"].submit();
            return;
        }
    </script>
@endsection
<!-- /Page Wrapper -->
