<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    {{ env('Page_Title') }}
@endsection
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">

                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Trial Balance</li>
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
                            <h4 class="card-title">Trial Balance</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" name="mainform" id="mainform">
                                {{ csrf_field() }}
                                <div class="panel-body">
                                    <div class="row">

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>As at</label>
                                                <input type="date" name="todate" value="{{ $todate }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label><br></label>
                                                <br>
                                                <button type="submit" class="btn btn-success" name="update"
                                                    style="align:left">search</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive" style="font-size: 12px;" id="tableData">
                                        <table class="table table-bordered table-striped table-highlight">
                                            <thead>
                                                <tr bgcolor="#c7c7c7">
                                                    <th>Account Name</th>
                                                    <th>Debit</th>
                                                    <th>Credit</th>
                                                </tr>
                                            </thead>
                                            @php
                                                $Totaldebit = 0;
                                                $Totalcredit = 0;
                                            @endphp
                                            @foreach ($TrialBal as $data)
                                                <tr>

                                                    <td><a href="/balance-breakdown/{{ $data->accountid }}"
                                                            target="_blank">{{ $data->accountName }} -
                                                            {{ $data->accounthead }}
                                                        </a> </td>
                                                    @if ($data->Credit > 0)
                                                        <td style="text-align: right; ">
                                                            {{ number_format(abs($data->Credit), 2, '.', ',') }} </td>
                                                        <td style="text-align: right; ">0.00</td>
                                                        @php $Totaldebit+= $data->Credit; @endphp
                                                    @else
                                                        <td style="text-align: right; ">0.00</td>
                                                        <td style="text-align: right; ">
                                                            {{ number_format(abs($data->Credit), 2, '.', ',') }} </td>

                                                        @php $Totalcredit+= $data->Credit; @endphp
                                                    @endif
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td>Total</td>
                                                <td style="text-align: right; ">
                                                    {{ number_format(abs($Totaldebit), 2, '.', ',') }} </td>
                                                <td style="text-align: right; ">
                                                    {{ number_format(abs($Totalcredit), 2, '.', ',') }} </td>
                                            </tr>


                                        </table>
                                    </div>
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

            const Reload = (form) => document.forms[form].submit();
        </script>
    @endsection

    @section('styles')
    @endsection
    <!-- /Page Wrapper -->
