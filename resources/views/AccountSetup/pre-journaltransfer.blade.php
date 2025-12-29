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
                            <li class="breadcrumb-item active">Transaction</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            <!-- include notoifcation -->
            @include('_partialView.nofication')
            <!-- /include notoifcation -->

            @foreach ($AccountList as $list)
                <input type="hidden" id="id{{ $list->id }}" value="{{ $list->id }}">
                <input type="hidden" id="acct{{ $list->id }}" value="({{ $list->accountno }})">
                <input type="hidden" id="desc{{ $list->id }}" value="{{ $list->accountdescription }}">
            @endforeach
            <form method="post" id="delform" name="delform">

                {{ csrf_field() }}
            </form>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Pre-Journal Transfer</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" id="transdetails" name="transdetails" onsubmit="cleanAmountValues()">
                                <input type="hidden" name="ref" id="refid">

                                {{ csrf_field() }}
                                <div class="panel-body">

                                    <div class="table-responsive" style="font-size: 12px;">
                                        <table class="table table-bordered table-striped table-highlight">
                                            <tr>
                                                <th>Transaction Type</th>
                                                <th>Account</th>
                                                <th>Debit </th>
                                                <th>Credit</th>
                                                <th>Remarks</th>
                                                <th>Action</th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <select id="transactiontype" name="transactiontype" style="width:150px;"
                                                        class="form-control" onchange="TextBoxState()">
                                                        <option value="">-Select-</option>
                                                        @foreach ($AccountTransType as $list)
                                                            <option value="{{ $list->transtype }}"
                                                                {{ old('transactiontype') == $list->transtype || $transactiontype == $list->transtype ? 'selected' : '' }}>
                                                                {{ $list->transtype }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <?php if ($acctids == '') {
                                                        $acctids = old('acctids');
                                                    } ?>

                                                    <input type="hidden" id="acctids" name="acctids"
                                                        value="{{ $acctids }}">

                                                    <input autocomplete="off" type="text" list="refaccounts"
                                                        name="refaccounts" id="refaccountids"
                                                        @if ($acctids != '') style="display:none" @endif
                                                        style="width:200px;" class="form-control"
                                                        placeholder="Select Account" onchange="fetchMains()"
                                                        autocomplete="off">
                                                    <datalist id="refaccounts">
                                                        @foreach ($AccountList as $list)
                                                            <option
                                                                value="{{ $list->id }}:{{ $list->accountdescription }}({{ $list->accountno }})">
                                                                {{ $list->accountdescription }}({{ $list->accountno }})
                                                            </option>
                                                        @endforeach
                                                    </datalist>
                                                    <?php if ($accountnames == '') {
                                                        $accountnames = old('accountnames');
                                                    } ?>
                                                    <div class="input-group" id="hiddenid" style="width:200px;"><input
                                                            type="text" value="{{ $accountnames }}" class="form-control"
                                                            id="refaccountnames" readonly name="accountnames">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="btn btn-default"
                                                                onclick="UnfetchMains()">X</button>
                                                        </span>
                                                    </div>
                                                </td>

                                                <td>
                                                    @php
                                                        $dbtstatus = '';
                                                        $crdtstatus = '';
                                                        if ($transactiontype == '') {
                                                            $transactiontype = old('transactiontype');
                                                        }
                                                        if ($transactiontype == 'Credit') {
                                                            $dbtstatus = 'disabled';
                                                        }
                                                        if ($transactiontype == 'Debit') {
                                                            $crdtstatus = 'disabled';
                                                        }
                                                    @endphp
                                                    <div class="input-group"><span class="input-group-btn">
                                                            <button type="button" class="btn btn-default">N</button>
                                                            <?php if ($debitamount == '') {
                                                                $debitamount = old('debitamount');
                                                            } ?>
                                                        </span><input disabled type="text" id="debitamount"
                                                            name="debitamount"
                                                            value="{{ number_format($debitamount, 2, '.', ',') }}"
                                                            class="form-control" style="width:150px; text-align: right;"
                                                            {{ $dbtstatus }} autocomplete="off"
                                                            oninput="formatNumberInput(this)"></div>
                                                </td>
                                                <td>
                                                    <div class="input-group"><span class="input-group-btn">
                                                            <button type="button" class="btn btn-default">N</button>
                                                            <?php if ($creditamount == '') {
                                                                $creditamount = old('creditamount');
                                                            } ?>
                                                        </span><input disabled type="text" id="creditamount"
                                                            name="creditamount"
                                                            value="{{ number_format($creditamount, 2, '.', ',') }}"
                                                            class="form-control" style="width:150px; text-align: right;"
                                                            {{ $crdtstatus }} autocomplete="off"
                                                            oninput="formatNumberInput(this)">
                                                    </div>
                                                </td>
                                                <?php if ($remarks == '') {
                                                    $remarks = old('remarks');
                                                } ?>
                                                <td>
                                                    <input type="text" id="remarks" name="remarks"
                                                        value="{{ $remarks }}" class="form-control"
                                                        style="width:250px;" autocomplete="off">
                                                </td>

                                                <td>
                                                    <button type="submit" class="btn btn-primary"
                                                        name="add">Add</button>
                                                </td>
                                            </tr>

                                            @php
                                                $totaldebit = 0;
                                                $totalcredit = 0;
                                            @endphp

                                            @foreach ($JournalPending as $data)
                                                @php
                                                    $totaldebit += $data->debit;
                                                    $totalcredit += $data->credit;
                                                @endphp

                                                <tr>
                                                    <td><input type="text" class="form-control"
                                                            value="{{ $data->transtype }}" readonly></td>
                                                    <td><input type="text" class="form-control"
                                                            value="{{ $data->accountdescription }}" readonly
                                                            title="{{ $data->accountdescription }}"></td>
                                                    <td><input type="text" class="form-control"
                                                            value="{{ number_format($data->debit, 2, '.', ',') }}"
                                                            readonly style="text-align: right; "></td>
                                                    <td><input type="text" class="form-control"
                                                            value="{{ number_format($data->credit, 2, '.', ',') }}"
                                                            readonly style="text-align: right; "></td>

                                                    <td><input type="text" class="form-control"
                                                            value="{{ $data->remarks }}" readonly></td>
                                                    <td>
                                                        <a onclick="editfunc('{{ $data->id }}','{{ $data->transtype }}','{{ $data->accountid }}','{{ $data->debit }}','{{ $data->credit }}','{{ $data->remarks }}')"
                                                            class="fa fa-edit btn-xs"
                                                            style="color:rgb(14, 147, 81)"></a>&nbsp;

                                                        <a href="javascript: deletefunc('{{ $data->id }}')"><i
                                                                class="fa fa-trash" style="color:red"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach

                                            <tr>
                                                <td>Total</td>
                                                <td>
                                                    @if (number_format($totalcredit, 2, '.', ',') == number_format($totaldebit, 2, '.', ',') && $totaldebit > 0)
                                                        <b>Transaction Date:</b><input type="date" name="transdate"
                                                            value="{{ $transdate }}" class="form-control"
                                                            style="width:150px;">
                                                    @endif
                                                </td>
                                                <td><input type="text" class="form-control"
                                                        value="{{ number_format($totaldebit, 2, '.', ',') }}" readonly
                                                        style="text-align: right; "></td>
                                                <td><input type="text" class="form-control"
                                                        value="{{ number_format($totalcredit, 2, '.', ',') }}" readonly
                                                        style="text-align: right; "></td>
                                                <td>
                                                    @if (number_format($totalcredit, 2, '.', ',') == number_format($totaldebit, 2, '.', ',') && $totaldebit > 0)
                                                        <b>Ref No:</b><input type="text" id="manual_ref"
                                                            name="manual_ref" value="{{ $manual_ref }}"
                                                            class="form-control" style="width:250px;" autocomplete="off">
                                                    @endif
                                                </td>
                                                <?php $totalcredit = round($totalcredit, 2);
                                                $totaldebit = round($totaldebit, 2); ?>
                                                <td colspan=2>
                                                    @if ($totaldebit == $totalcredit && $totaldebit > 0)
                                                        <button type="submit" class="btn btn-primary"
                                                            name="post">Save</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <br>
                                    @if ($SelectedJournalPending)
                                        <div class="table-responsive" style="font-size: 12px;">
                                            <table class="table table-bordered table-striped table-highlight">
                                                <tr>
                                                    <th>Transaction Type</th>
                                                    <th>Account</th>
                                                    <th>Debit </th>
                                                    <th>Credit</th>
                                                    <th>Remarks</th>
                                                    <th>Action</th>
                                                </tr>
                                                @php
                                                    $totaldebit = 0;
                                                    $totalcredit = 0;
                                                @endphp
                                                @foreach ($SelectedJournalPending as $data)
                                                    @php
                                                        $totaldebit += $data->debit;
                                                        $totalcredit += $data->credit;
                                                    @endphp
                                                    <tr>
                                                        <td><input type="text" class="form-control"
                                                                value="{{ $data->transtype }}" readonly></td>
                                                        <td><input type="text" class="form-control"
                                                                value="{{ $data->accountdescription }}" readonly></td>
                                                        <td><input type="text" class="form-control"
                                                                value="{{ number_format($data->debit, 2, '.', ',') }}"
                                                                readonly style="text-align: right; "></td>
                                                        <td><input type="text" class="form-control"
                                                                value="{{ number_format($data->credit, 2, '.', ',') }}"
                                                                readonly style="text-align: right; "></td>
                                                        <td><input type="text" class="form-control"
                                                                value="{{ $data->remarks }}" readonly></td>
                                                        <td>

                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td>Total</td>
                                                    <td>
                                                        @if (number_format($totalcredit, 2, '.', ',') == number_format($totaldebit, 2, '.', ',') && $totaldebit > 0)
                                                            <b>Transaction Date:</b>{{ $data->transdate }}
                                                        @endif
                                                    </td>
                                                    <td><input type="text" class="form-control"
                                                            value="{{ number_format($totaldebit, 2, '.', ',') }}" readonly
                                                            style="text-align: right; "></td>
                                                    <td><input type="text" class="form-control"
                                                            value="{{ number_format($totalcredit, 2, '.', ',') }}"
                                                            readonly style="text-align: right; "></td>
                                                    <td>
                                                        @if (number_format($totalcredit, 2, '.', ',') == number_format($totaldebit, 2, '.', ',') && $totaldebit > 0)
                                                            <b>Ref No:</b>{{ $data->manual_ref }}
                                                        @endif
                                                    </td>
                                                    <?php $totalcredit = round($totalcredit, 2);
                                                    $totaldebit = round($totaldebit, 2); ?>
                                                    <td colspan=2>
                                                        @if ($totaldebit == $totalcredit && $totaldebit > 0)
                                                            <button type="submit" class="btn btn-primary"
                                                                name="close">Close</button>
                                                            <a href="javascript: Restore('{{ $ref }}')"
                                                                class="btn btn-warning">Restore</a>
                                                            <a href="javascript: DelTransa('{{ $ref }}')"
                                                                class="btn btn-danger"> Trash</a>
                                                        @endif
                                                    </td>
                                                </tr>


                                            </table>
                                        </div>
                                    @else
                                        <div class="table-responsive" style="font-size: 12px;">
                                            <table class="table table-bordered table-striped table-highlight">
                                                <tr>
                                                    <th>Transaction Date</th>
                                                    <th>Total Amount</th>
                                                    <th>PV/JV </th>
                                                    <th>Input by </th>
                                                    <th>Action</th>
                                                </tr>
                                                @foreach ($UnpostedJournalPending as $data)
                                                    <tr>
                                                        <td>{{ $data->transdate }}</td>
                                                        <td>{{ number_format($data->t_val, 2, '.', ',') }} </td>
                                                        <td>{{ $data->manual_ref }}</td>
                                                        <td>{{ $data->name }}</td>
                                                        <td>
                                                        <td><a onclick="TransactionDetail('{{ $data->ref }}')"
                                                                class="btn btn-success">View</a>
                                                        </td>
                                                    </tr>
                                                @endforeach



                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <div id="editModal" class="modal fade">
                <div class="modal-dialog box box-default" role="document" style="color:black;font-size:24px;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Entry</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="form-horizontal" method="post" role="form" onsubmit="cleanAmountValues()">
                            {{ csrf_field() }}
                            <div class="modal-body">
                                <div class="form-group" style="margin: 0 10px;">

                                    <input type="hidden" class="form-control" id="id" name="id">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    <h5>Trans Type: </h5>
                                                </label>
                                                <select class="form-control" id="e-transtype"name="transactiontype"
                                                    onchange="E_TextBoxState()">
                                                    <option value="">--Select--</option>
                                                    @foreach ($AccountTransType as $list)
                                                        <option value="{{ $list->transtype }}">{{ $list->transtype }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    <h5>Account Ledger: </h5>
                                                </label>
                                                <select class="form-control" id="e-acctids"name="acctids">
                                                    <option value="">--Select--</option>
                                                    @foreach ($AccountList as $list)
                                                        <option value="{{ $list->id }}">
                                                            {{ $list->accountdescription }}({{ $list->accountno }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                <h5>Debit: </h5>
                                            </label>
                                            <input type="text" class="form-control" id="e-debitamount"
                                                name="debitamount" oninput="formatNumberInput(this)">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                <h5>Credit: </h5>
                                            </label>
                                            <input type="text" class="form-control" id="e-creditamount"
                                                name="creditamount" oninput="formatNumberInput(this)">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="control-label">
                                                <h5>Remarks: </h5>
                                            </label>
                                            <input type="text" class="form-control" id="e-remarks" name="remarks">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success" name="update">Save</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>

            <div id="deleteModal" class="modal fade">
                <div class="modal-dialog box box-default" role="document" style="color:black;font-size:24px;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Delete Entry</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="form-horizontal" method="post" role="form">
                            {{ csrf_field() }}
                            <div class="modal-body">
                                <h3> You are about to delete this record! Do you really want to continue?</h3>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success" name="update">Continue</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                            <input type="hidden" id="delid" name="delid">
                        </form>
                    </div>

                </div>
            </div>

            <div id="restoreModal" class="modal fade">
                <div class="modal-dialog box box-default" role="document" style="color:black;font-size:24px;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Restore Entry</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="form-horizontal" method="post" role="form">
                            {{ csrf_field() }}
                            <div class="modal-body">
                                <h3> You are about to restore this record for further modification! Do you really want to
                                    continue?</h3>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success" name="reverse">Continue</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                            <input type="hidden" id="restoreref" name="ref">
                        </form>
                    </div>

                </div>


            </div>


            <div id="delrefModal" class="modal fade">
                <div class="modal-dialog box box-default" role="document" style="color:black;font-size:24px;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Delete Entry</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="form-horizontal" method="post" role="form">
                            {{ csrf_field() }}
                            <input type="hidden" name="delref" id="delref">
                            <div class="modal-body">
                                <h3> You are about to trash this record! Doing so will remove the record from the system. Do
                                    you really want to continue?</h3>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Continue</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                            <input type="hidden" id="restoreref" name="ref">
                        </form>
                    </div>

                </div>


            </div>

        </div>

        <!-- /Edit Details Modal -->
    @endsection
    {{-- @section('scripts')
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

            function TextBoxState() {
                var Val = document.getElementById("transactiontype").value;

                if (Val == "Debit") {
                    document.getElementById('debitamount').value = "{{ $drbal }}";
                    document.getElementById('creditamount').value = "";
                    document.getElementById('debitamount').removeAttribute('disabled');
                    document.getElementById('creditamount').setAttribute('disabled', 'disabled');
                }
                if (Val == "Credit") {
                    document.getElementById('creditamount').removeAttribute('disabled');
                    document.getElementById('debitamount').setAttribute('disabled', 'disabled');
                    document.getElementById('debitamount').value = "";
                    document.getElementById('creditamount').value = "{{ $crbal }}";
                }
                document.getElementById('remarks').value = "{{ $defaultremark }}"
                return;
            }
        </script>
    @endsection --}}

    @section('styles')
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
        <link rel="stylesheet" type="text/css"
            href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
        <style>
            label {
                color: black text-shadow: 1px 1px 2px #fff;
            }
        </style>
    @stop
    @section('scripts')

        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
        <script>
            function formatNumberInput(input) {
                // Get cursor position
                let cursorPos = input.selectionStart;
                let originalLength = input.value.length;

                // Remove all non-numeric characters except decimal point
                let value = input.value.replace(/[^\d.]/g, '');

                // Prevent multiple decimal points
                let parts = value.split('.');
                if (parts.length > 2) {
                    value = parts[0] + '.' + parts.slice(1).join('');
                }

                // Limit to 2 decimal places
                if (parts.length === 2 && parts[1].length > 2) {
                    value = parts[0] + '.' + parts[1].substring(0, 2);
                }

                // Format with thousand separators
                if (value) {
                    parts = value.split('.');
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    value = parts.join('.');
                }

                // Update the input value
                input.value = value;

                // Adjust cursor position
                let newLength = input.value.length;
                let lengthDiff = newLength - originalLength;
                let newCursorPos = cursorPos + lengthDiff;

                // Ensure cursor position is within bounds
                if (newCursorPos < 0) newCursorPos = 0;
                if (newCursorPos > newLength) newCursorPos = newLength;

                input.setSelectionRange(newCursorPos, newCursorPos);
            }

            function cleanAmountValues() {
                // Remove commas from debitamount, creditamount, e-debitamount, and e-creditamount before form submission
                const debitInput = document.getElementById('debitamount');
                const creditInput = document.getElementById('creditamount');
                const eDebitInput = document.getElementById('e-debitamount');
                const eCreditInput = document.getElementById('e-creditamount');

                if (debitInput && debitInput.value) {
                    debitInput.value = debitInput.value.replace(/,/g, '');
                }

                if (creditInput && creditInput.value) {
                    creditInput.value = creditInput.value.replace(/,/g, '');
                }

                if (eDebitInput && eDebitInput.value) {
                    eDebitInput.value = eDebitInput.value.replace(/,/g, '');
                }

                if (eCreditInput && eCreditInput.value) {
                    eCreditInput.value = eCreditInput.value.replace(/,/g, '');
                }
            }

            function formatNumber(value) {
                if (!value || value === '') return '';
                // Remove commas, format to 2 decimal places, then add commas back
                let numValue = parseFloat(value.toString().replace(/,/g, ''));
                if (isNaN(numValue)) return '';
                return numValue.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }

            function editfunc(id, transtype, acctids, debitamount, creditamount, remarks) {
                document.getElementById('id').value = id;
                document.getElementById('e-transtype').value = transtype;
                if (transtype == "Debit") {
                    document.getElementById('e-debitamount').removeAttribute('disabled');
                    document.getElementById('e-creditamount').setAttribute('disabled', 'disabled');
                } else {
                    document.getElementById('e-creditamount').removeAttribute('disabled');
                    document.getElementById('e-debitamount').setAttribute('disabled', 'disabled');
                }
                document.getElementById('e-acctids').value = acctids;
                document.getElementById('e-debitamount').value = formatNumber(debitamount);
                document.getElementById('e-creditamount').value = formatNumber(creditamount);
                document.getElementById('e-remarks').value = remarks;

                $("#editModal").modal('show')
            }

            function deletefunc(id) {
                document.getElementById('delid').value = id;
                $("#deleteModal").modal('show')
                return;
            }

            function Restore(id) {
                document.getElementById('restoreref').value = id;
                $("#restoreModal").modal('show')
                return;
            }


            function fetchMain() {
                var txv = document.getElementById('refaccountid').value;
                var tx = txv.split(':');
                var id = tx[0];
                //var id = document.getElementById('refaccountid').value;

                document.getElementById('acctid').value = id;

                document.getElementById('refaccountname').value = document.getElementById('desc' + id).value + " " + document
                    .getElementById('acct' + id).value;
            }

            function fetchMains() {
                var txv = document.getElementById('refaccountids').value;
                var tx = txv.split(':');
                var id = tx[0];
                //alert(id);
                //var id = document.getElementById('refaccountids').value;
                document.getElementById('acctids').value = id;
                document.getElementById("refaccountids").style.display = "none";
                document.getElementById('refaccountnames').value = document.getElementById('desc' + id).value + " " + document
                    .getElementById('acct' + id).value;
                // document.getElementById("hiddenid").style.display="block";
            }

            function UnfetchMains() {

                document.getElementById('acctids').value = '';
                document.getElementById('refaccountids').value = '';
                document.getElementById("refaccountids").style.display = "block";
                document.getElementById('refaccountnames').value = '';

            }

            function E_TextBoxState() {
                var Val = document.getElementById("e-transtype").value;

                if (Val == "Debit") {
                    document.getElementById('e-debitamount').value = "";
                    document.getElementById('e-creditamount').value = "";
                    document.getElementById('e-debitamount').removeAttribute('disabled');
                    document.getElementById('e-creditamount').setAttribute('disabled', 'disabled');
                }
                if (Val == "Credit") {
                    document.getElementById('e-creditamount').removeAttribute('disabled');
                    document.getElementById('e-debitamount').setAttribute('disabled', 'disabled');
                    document.getElementById('e-debitamount').value = "";
                    document.getElementById('e-creditamount').value = "";
                }
                return;
            }

            function TextBoxState() {
                var Val = document.getElementById("transactiontype").value;

                if (Val == "Debit") {
                    document.getElementById('debitamount').value =
                        "{{ !empty($drbal) ? number_format($drbal, 2, '.', ',') : '' }}";
                    document.getElementById('creditamount').value = "";
                    document.getElementById('debitamount').removeAttribute('disabled');
                    document.getElementById('creditamount').setAttribute('disabled', 'disabled');
                }
                if (Val == "Credit") {
                    document.getElementById('creditamount').removeAttribute('disabled');
                    document.getElementById('debitamount').setAttribute('disabled', 'disabled');
                    document.getElementById('debitamount').value = "";
                    document.getElementById('creditamount').value =
                        "{{ !empty($crbal) ? number_format($crbal, 2, '.', ',') : '' }}";
                }
                document.getElementById('remarks').value = "{{ $defaultremark }}"
                return;
            }

            function TransactionDetail(ref) {
                document.getElementById('refid').value = ref;
                document.forms["transdetails"].submit();

            }

            function DelTransa(ref) {

                document.getElementById('delref').value = ref;
                $("#delrefModal").modal('show');
                return;


            }
        </script>
    @stop
    <!-- /Page Wrapper -->
