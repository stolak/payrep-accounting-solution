<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Payroll Variable
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
                            <li class="breadcrumb-item active">Payroll Variable</li>
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
                            <h4 class="card-title">Staff Payroll Variable</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" id="mainform" name="mainform">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Staff Names</label>
                                            <select class="form-control select2" id="staffid" name="staffid"
                                                onchange="Reload();">
                                                <option value="">--Select--</option>
                                                @foreach ($Staffs as $list)
                                                    <option value="{{ $list->id }}"
                                                        {{ old('staffid') == $list->id || $staffid == $list->id ? 'selected' : '' }}>
                                                        {{ $list->first_name }} {{ $list->middle_name }}
                                                        {{ $list->last_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Variable type</label>
                                            <select class="form-control select2" name="variabletype" onchange="Reload();">
                                                <option value="">--Select--</option>
                                                @foreach ($VariableType as $list)
                                                    <option value="{{ $list->id }}"
                                                        {{ old('variabletype') == $list->id || $variabletype == $list->id ? 'selected' : '' }}>
                                                        {{ $list->particular }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Control Variable</label>
                                            <select class="form-control select2" name="variable">
                                                <option value="">--Select--</option>
                                                @foreach ($PayrollVariable as $list)
                                                    <option value="{{ $list->id }}"
                                                        {{ old('variable') == $list->id || $variable == $list->id ? 'selected' : '' }}>
                                                        {{ $list->variable }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row align-items-end">
                                    <div class="col-md-3 col-sm-6">
                                        <div class="form-group">
                                            <label>Monthly Amount</label>
                                            <?php if ($amount == '') {
                                                $amount = old('amount');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $amount }}"
                                                name="amount" id="amount">
                                        </div>
                                    </div>

                                    <?php if ($continuity == '') {
                                        $continuity = old('continuity');
                                    } ?>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="form-group continuity-wrapper">
                                            <label>Continuity</label>
                                            <div>
                                                <input type="checkbox" data-toggle="toggle" data-on="Target Apply"
                                                    data-off="No Limit" name="continuity" data-width="150" id="continuity"
                                                    {{ $continuity == 'on' ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-6" id="target-content">
                                        @if ($continuity == 'on')
                                            <div class="form-group">
                                                <label>Target Amount</label>
                                                <?php if ($targetamount == '') {
                                                    $targetamount = old('targetamount');
                                                } ?>
                                                <input type="text" class="form-control" value="{{ $targetamount }}"
                                                    name="targetamount" id="targetamount">
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-md-3 col-sm-6 text-md-right">
                                        <button type="submit" class="btn btn-primary btn-block btn-md-auto"
                                            name="addnew">Add New</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">

                    <!-- Staff Variables -->
                    <div class="card card-table">
                        <div class="card-header">
                            <h4 class="card-title">Staff Variables</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th rowspan="1">S/N</th>
                                            <th rowspan="1">Earning/deduction</th>
                                            <th rowspan="1">Payroll Variable</th>
                                            <th rowspan="1">Amount</th>
                                            <th rowspan="1">Target</th>
                                            <th rowspan="1">Last Processed</th>
                                            <th rowspan="1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                            $id = 'id';
                                        @endphp

                                        @foreach ($StaffVariable as $list)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $list->particular }}</td>
                                                <td>{{ $list->variables }}</td>
                                                <td>{{ number_format(abs($list->amount_monthly), 2, '.', ',') }}</td>
                                                <td>
                                                    @if ($list->is_continous == 1)
                                                        Not Applicable
                                                        @else{{ number_format(abs($list->amount_target), 2, '.', ',') }}
                                                    @endif
                                                </td>
                                                <td>
                                                </td>
                                                <td>
                                                    <a class="btn btn-sm bg-danger-light"
                                                        href="javascript: deletefunc('{{ $list->id }}','{{ $list->variables }}')">
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
                    <!-- /Staff Variables -->

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
                                <p class="mb-4">Are you sure want to delete <span id="content5"></span>?</p>
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

    <form method="post" id="noform" name="noform">
        {{ csrf_field() }}
        <input type="hidden" class="form-control" id="noid" name="id" value="">

    </form>
@endsection
@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <style>
        label {
            color: black text-shadow: 1px 1px 2px #fff;
        }

        .continuity-wrapper {
            min-height: 76px;
        }

        #target-content .form-group {
            margin-bottom: 1rem;
        }
    </style>
@endsection
@section('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script>
        $('.select_picker').selectpicker({
            style: 'btn-default',
            size: 4
        });

        function editfunc(id, ocode, desc, account) {
            document.getElementById('id').value = id;
            document.getElementById('e_o_code').value = ocode;
            document.getElementById('e_description').value = desc;
            document.getElementById('e_account').value = account;
            $("#edit_details").modal('show')
        }

        function deletefunc(id, item) {
            document.getElementById('deleteid').value = id;
            document.getElementById('content5').innerHTML = item;

            $("#delete_modal").modal('show')
        }

        function Reload() {
            document.forms["mainform"].submit();
            return;
        }
        $(function() {
            $('#continuity').change(function() {
                document.getElementById('target-content').innerHTML = '';
                if ($(this).prop('checked') == true) {
                    document.getElementById('target-content').innerHTML =
                        '<div class="form-group"><label>Target Amount</label><input type="text" class="form-control" value="" name="targetamount" id="targetamount"></div>';
                    document.getElementById('targetamount').value = document.getElementById('amount').value;

                }
            })
        })
    </script>
@endsection
<!-- /Page Wrapper -->
