<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    {{ env('Page_Title') }}
@endsection

@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Setup</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Subaccount</li>
                        </ul>
                    </div>
                </div>
            </div>

            @include('_partialView.nofication')

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Create Subaccount</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" name="mainform" id="mainform">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Account Head</label>
                                            <select class="select2 form-control" name="accountHead" id="accountHead"
                                                onchange='reloadForm("mainform");'>
                                                <option value="All">--Select--</option>
                                                @foreach ($accountHeads as $list)
                                                    <option value="{{ $list->id }}"
                                                        {{ $accountHead == $list->id ? 'selected' : '' }}>
                                                        {{ $list->accounthead }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Sub Account</label>
                                            <input type="text" id="subaccount" name="subaccount"
                                                value="{{ $subaccount }}" class="form-control" style="text-align: left;"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Payment Method</label>
                                            <select class="form-control" name="payment_method" id="payment_method">
                                                <option value="">--Select--</option>
                                                <option value="Cash"
                                                    {{ old('payment_method', $payment_method) == 'Cash' ? 'selected' : '' }}>
                                                    Cash
                                                </option>
                                                <option value="Non-cash"
                                                    {{ old('payment_method', $payment_method) == 'Non-cash' ? 'selected' : '' }}>
                                                    Non-cash
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary" name="addnew">Submit</button>
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
                            <h4 class="card-title">Subaccounts</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Account Head</th>
                                            <th>SubHead</th>
                                            <th>Payment Method</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $sn = 1; @endphp
                                        @foreach ($subaccounts as $list)
                                            <tr>
                                                <td>{{ $sn++ }}</td>
                                                <td>{{ $list->accounthead }}</td>
                                                <td>{{ $list->subhead }}</td>
                                                <td>{{ $list->payment_method ?? 'N/A' }}</td>
                                                <td>
                                                    <a class="btn btn-sm bg-success-light"
                                                        href="javascript: editFunc('{{ $list->id }}','{{ $list->headid }}','{{ addslashes($list->subhead) }}','{{ $list->payment_method }}')">
                                                        <i class="fe fe-pencil"></i>
                                                    </a>
                                                    <a class="btn btn-sm bg-danger-light"
                                                        href="javascript: deleteRecord('{{ $list->id }}')">
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

        <div class="modal fade" id="delete_modal" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form method="post">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="form-content p-2">
                                <h4 class="modal-title">Delete</h4>
                                <p class="mb-4">Are you sure want to delete?</p>
                                <button type="submit" class="btn btn-primary" name="delete">Continue</button>
                                <input type="hidden" id="d-id" name="id">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="edit_details" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Subaccount</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>Account Head</label>
                                <select class="form-control" name="accountHead" id="e-accountHead">
                                    @foreach ($accountHeads as $list)
                                        <option value="{{ $list->id }}">{{ $list->accounthead }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Sub Account</label>
                                <input type="text" id="e-subaccount" name="subaccount" class="form-control"
                                    style="text-align: left;" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label>Payment Method</label>
                                <select class="form-control" name="payment_method" id="e-payment_method">
                                    <option value="Cash">Cash</option>
                                    <option value="Non-cash">Non-cash</option>
                                </select>
                            </div>
                            <input type="hidden" id="e-id" name="id">
                            <div class="form-content p-2">
                                <button type="submit" class="btn btn-primary" name="update">Save Changes</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const deleteRecord = (id) => {
            document.getElementById('d-id').value = id;
            $("#delete_modal").modal('show');
        }

        const editFunc = (id, headId, subaccount, paymentMethod) => {
            document.getElementById('e-id').value = id;
            document.getElementById('e-accountHead').value = headId;
            document.getElementById('e-subaccount').value = subaccount;
            document.getElementById('e-payment_method').value = paymentMethod || 'Cash';
            $("#edit_details").modal('show');
        }

        const reloadForm = (form) => document.forms[form].submit();
    </script>
@endsection

@section('styles')
@endsection
<!-- /Page Wrapper -->
