<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
{{env('Page_Title')}}
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
                        <li class="breadcrumb-item active">Subaccount</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->
        <!-- include notoifcation -->
        @include('_partialView.nofication')
        <!-- /include notoifcation -->
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Account Head</label>
                                        <select class="select2 form-control" name="accountHead" id="accountHead"
                                            onchange='Reload("mainform");'>
                                            <option value="All">--Select--</option>
                                            @foreach($accountHeads as $list)
                                            <option value="{{$list->id}}" {{$accountHead==$list->id? 'selected':''}}>
                                                {{$list->accounthead}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Sub Account</label>
                                        <select class="select2 form-control" name="subaccount" id="subaccount"
                                            onchange='Reload("mainform");'>
                                            <option value="All">--Select--</option>
                                            @foreach($subaccounts as $list)
                                            <option value="{{$list->id}}" {{$subaccount==$list->id? 'selected':''}}>
                                                {{$list->subhead}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Account</label>
                                        <input type="text" id="account" name="account" value="{{$account}}"
                                            class="form-control" style="text-align: left;" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Account No</label>
                                        <input type="text" id="accountno" name="accountno" value="{{$accountno}}"
                                            class="form-control" style="text-align: left;" autocomplete="off">
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

                <!-- List of economic code -->
                <div class="card card-table">
                    <div class="card-header">
                        <h4 class="card-title"> Subaccounts</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th rowspan="1">S/N</th>
                                        <th rowspan="1">Account Head</th>
                                        <th rowspan="1">SubHead</th>
                                        <th rowspan="1">Account details</th>
                                        <th rowspan="1">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $sn=1;@endphp
                                    @foreach($accounts as $list)
                                    <tr>
                                        <td>
                                            {{$sn++}}
                                        </td>
                                        <td>
                                            {{$list->accounthead}}
                                        </td>
                                        <td>
                                            {{$list->subhead}}
                                        </td>
                                        <td>
                                            {{$list->accountdescription}}({{$list->accountno}})
                                        </td>

                                        <td>
                                            <a class="btn btn-sm bg-success-light"
                                                href="javascript: editFunc('{{$list->id}}', '{{$list->subhead}}', '{{$list->accounthead}}',  '{{$list->accountno}}', '{{$list->accountdescription}}', '{{$list->rank}}')">
                                                <i class="fe fe-pencil"></i>
                                            </a>
                                            <a class="btn btn-sm bg-danger-light"
                                                href="javascript: deleteRecord('{{$list->id}}')">
                                                <i class="fe fe-trash"></i>
                                            </a>
                                        </td>

                                        @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /List of economic code -->

            </div>
        </div>
    </div>


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
                            <button type="submit" class="btn btn-primary" name="delete">Continue </button>
                            <input type="hidden" id="d-id" name="id">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Delete Modal -->

</div>
<!-- Edit Details Modal -->
<div class="modal fade" id="edit_details" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Details</h5>
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
                                <label>Account Head</label>
                                <input type="text" id="e-accounthead" name="accounthead" class="form-control" disabled
                                    style="text-align: left;" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label>Sub Head</label>
                                <input type="text" id="e-subhead" name="subhead" class="form-control" disabled
                                    style="text-align: left;" autocomplete="off">
                            </div>
                        </div>
                    </div>


                    <div class="row ">
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label>Description</label>
                                <input type="text" id="e-accountdescription" name="accountdescription"
                                    class="form-control" style="text-align: left;" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label>Account Number</label>
                                <input type="text" id="e-accountno" name="accountno" class="form-control"
                                    style="text-align: left;" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label>Rank</label>
                                <input type="number" id="e-rank" name="rank" class="form-control"
                                    style="text-align: left;" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="e-id" name="id">
                    <div class="form-content p-2">
                        <button type="submit" class="btn btn-primary " name="update">Save Changes</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Details Modal -->
@endsection
@section('scripts')
<script>
    const doAfterSelection = (famount, frate, fperiod, finterest, fmonthlyRepayment, ftotalRepayment) => {
        const amount = parseFloat(document.getElementById(famount).value.replace(/,/g, ''));
        const rate = document.getElementById(frate).value * 0.01;
        const period = document.getElementById(fperiod).value;
        const totalRepayment = amount * (Math.pow((1 + (rate / 12)), period));
        const totalInterest = totalRepayment - amount;
        const monthlyRepayment = totalRepayment / period;
        document.getElementById(finterest).value = formatValue(totalInterest);
        document.getElementById(fmonthlyRepayment).value = formatValue(monthlyRepayment);
        document.getElementById(ftotalRepayment).value = formatValue(totalRepayment);
    }
    const ValidateInput = (id) => {
        document.getElementById(id).value = formatValue(document.getElementById(id).value);
    }

    const formatValue = (val) => {
        return parseFloat(val
            .toString().replace(/\,/g, '')).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    const deleteRecord = (id) => {
        document.getElementById('d-id').value = id;
        $("#delete_modal").modal('show')
    }
    const editFunc = (id, subhead, accounthead, accountno, accountdescription, rank) => {
        document.getElementById('e-id').value = id;
        document.getElementById('e-accounthead').value = accounthead;
        document.getElementById('e-subhead').value = subhead;
        document.getElementById('e-accountno').value = accountno;
        document.getElementById('e-accountdescription').value = accountdescription;
        document.getElementById('e-rank').value = rank;
        $("#edit_details").modal('show')
    }
    const Reload = (form) => document.forms[form].submit();

</script>
@endsection

@section('styles')

@endsection
<!-- /Page Wrapper -->
