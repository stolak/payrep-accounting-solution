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
                            <li class="breadcrumb-item active">Statement of Account</li>
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
                            <h4 class="card-title">Statement of Account</h4>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                {{ csrf_field() }}
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">As At ehehh:</label>
                                                <input type="date" name="asatdate" value="{{ $asatdate }}"
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
                                    <div class="table-responsive" id="tableData">
                                        <table class="table table-bordered table-striped table-highlight">
                                            <thead>
                                                <tr bgcolor="#c7c7c7">
                                                    <th>ASSETS</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            @php
                                                $Totalasset = 0;
                                                $Totalliablity = 0;
                                                $Total_C_liablity = 0;
                                                $note = 1;
                                            @endphp
                                            @foreach ($CurrentAsset as $data)
                                                <tr>


                                                    <td>{{ $data->Subhead }}</td>

                                                    <td style="text-align: right; ">
                                                        @if ($data->tVal < 0)
                                                            ({{ number_format(abs($data->tVal), 2, '.', ',') }})
                                                            @else{{ number_format(abs($data->tVal), 2, '.', ',') }}
                                                        @endif
                                                    </td>

                                                    @php $Totalasset+= $data->tVal; @endphp
                                                </tr>
                                            @endforeach
                                            @foreach ($FixedAsset as $data)
                                                <tr>

                                                    <td>{{ $data->Subhead }}</td>
                                                    <td style="text-align: right; ">
                                                        @if ($data->tVal < 0)
                                                            ({{ number_format(abs($data->tVal), 2, '.', ',') }})
                                                            @else{{ number_format(abs($data->tVal), 2, '.', ',') }}
                                                        @endif
                                                    </td>

                                                    @php $Totalasset+= $data->tVal; @endphp
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td>Total</td>
                                                <td style="text-align: right; "><b>
                                                        @if ($Totalasset < 0)
                                                            ({{ number_format(abs($Totalasset), 2, '.', ',') }})
                                                            @else{{ number_format(abs($Totalasset), 2, '.', ',') }}
                                                        @endif
                                                    </b></td>
                                            </tr>
                                            <thead>
                                                <tr bgcolor="#c7c7c7">
                                                    <th>CURRENT LIABLITY</th>
                                                    <th>AMOUNT</th>
                                                </tr>
                                            </thead>
                                            @foreach ($Liability as $data)
                                                <tr>

                                                    <td>{{ $data->Subhead }}</td>

                                                    <td style="text-align: right; ">
                                                        @if ($data->tVal < 0)
                                                            {{ number_format(abs($data->tVal), 2, '.', ',') }}
                                                        @else
                                                            &#40; {{ number_format(abs($data->tVal), 2, '.', ',') }} &#41;
                                                        @endif
                                                    </td>

                                                    @php $Total_C_liablity+= $data->tVal; @endphp
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td>Total</td>

                                                <td style="text-align: right; "><b>
                                                        @if ($Totalasset < 0)
                                                            {{ number_format(abs($Total_C_liablity), 2, '.', ',') }}
                                                        @else
                                                            &#40; {{ number_format(abs($Total_C_liablity), 2, '.', ',') }}
                                                            &#41;
                                                        @endif
                                                    </b></0>
                                                </td>

                                            </tr>
                                            <thead>
                                                <tr bgcolor="#c7c7c7">
                                                    @php $netasset=$Totalasset-$Total_C_liablity; @endphp
                                                    <th>Net Current Asset</th>
                                                    <th style="text-align: right; ">
                                                        @if ($netasset < 0)
                                                            &#40; {{ number_format(abs($netasset), 2, '.', ',') }} &#41;
                                                        @else
                                                            {{ number_format(abs($netasset), 2, '.', ',') }}
                                                        @endif
                                                    </th>
                                                </tr>
                                            </thead>
                                            <thead>
                                                <tr>
                                                    <td colspan=3></td>
                                                </tr>
                                                <tr bgcolor="#c7c7c7">
                                                    <th>EQUITY</th>
                                                    <th>AMOUNT</th>
                                                </tr>
                                            </thead>


                                            @foreach ($LongLiability as $data)
                                                <tr>

                                                    <td>{{ $data->Subhead }}</td>
                                                    <tdstyle="text-align: right; ">
                                                    @if ($data->tVal < 0)
                                                        ({{ number_format(abs($data->tVal), 2, '.', ',') }})
                                                        @else{{ number_format(abs($data->tVal), 2, '.', ',') }}
                                                    @endif
                                                    </td>

                                                    @php $Totalliablity+= $data->tVal; @endphp
                                                </tr>
                                            @endforeach
                                            @foreach ($Equity as $data)
                                                <tr>

                                                    <td>{{ $data->Subhead }}(Equity)</td>

                                                    <td style="text-align: right; ">
                                                        @if ($data->tVal < 0)
                                                            ({{ number_format(abs($data->tVal), 2, '.', ',') }})
                                                            @else{{ number_format(abs($data->tVal), 2, '.', ',') }}
                                                        @endif
                                                    </td>


                                                    @php $Totalliablity+= $data->tVal; @endphp
                                                </tr>
                                            @endforeach
                                            @foreach ($PL as $data)
                                                <tr>

                                                    <td>P & L</td>

                                                    <td style="text-align: right; ">
                                                        @if ($data->tVal < 0)
                                                            ({{ number_format(abs($data->tVal), 2, '.', ',') }})
                                                            @else{{ number_format(abs($data->tVal), 2, '.', ',') }}
                                                        @endif
                                                    </td>

                                                    @php $Totalliablity+= $data->tVal; @endphp
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td>Total</td>

                                                <td style="text-align: right; "><b>
                                                        @if ($Totalliablity < 0)
                                                            ({{ number_format(abs($Totalliablity), 2, '.', ',') }})
                                                            @else{{ number_format(abs($Totalliablity), 2, '.', ',') }}
                                                        @endif
                                                    </b> </td>
                                            </tr>

                                        </table>
                                    </div>
                                </div>
                            </form>
                        </div>
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
                                    <input type="hidden" id="d-id" name="delid">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Delete Modal -->

        </div>

        <!-- /Edit Details Modal -->
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
