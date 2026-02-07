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
                            <h4 class="card-title">Create Payroll Variable</h4>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <?php if ($variabletype == '') {
                                                $variabletype = old('variabletype');
                                            } ?>
                                            <label>Variable type</label>
                                            <select class="form-control" name="variabletype" id="variabletype"
                                                onchange="VariableTypeChange()">
                                                <option value="">--Select--</option>
                                                @foreach ($VariableType as $list)
                                                    <option value="{{ $list->id }}"
                                                        {{ $variabletype == $list->id ? 'selected' : '' }}>
                                                        {{ $list->particular }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Variable</label>
                                            <input type="text" class="form-control" value="" name="variable">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Statutory?</label>
                                        <br>
                                        <label>
                                            <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                                name="statutory">
                                        </label>
                                    </div>
                                    <div id="taxable-content">
                                        @if ($variabletype == 1)
                                            <div class="col-md-2">
                                                <label>Taxable?</label>
                                                <br>
                                                <label>
                                                    <input type="checkbox" data-toggle="toggle" data-on="Yes"
                                                        data-off="No" name="taxable" id="taxable">
                                                </label>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Pensionable?</label>
                                                <br>
                                                <label>
                                                    <input type="checkbox" data-toggle="toggle" data-on="Yes"
                                                        data-off="No" name="isPensionable" id="isPensionable">
                                                </label>
                                            </div>
                                        @endif
                                        @if ($variabletype == 2)
                                            <div class="col-md-2">
                                                <label>Before Tax?</label>
                                                <br>
                                                <label>
                                                    <input type="checkbox" data-toggle="toggle" data-on="Yes"
                                                        data-off="No" name="isbefore_tax" id="isbefore_tax">
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-2">
                                        <label>Function?</label>
                                        <br>
                                        <label>
                                            <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                                name="isFunction" id="isFunction" onchange="toggleFunctionCard()">
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Rank</label>
                                            <select class="form-control" name="rank">
                                                <option value="">--Select--</option>
                                                @for ($i = 0; $i <= 10; $i++)
                                                    <option value="{{ $i }}"
                                                        {{ old('rank') == $i || $rank == $i ? 'selected' : '' }}>
                                                        {{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Function Card - Shows when isFunction is true -->
                                <div id="function-card" style="display: none;" class="mt-3">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">Function Settings</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Percentage (%)</label>
                                                        <input type="number" step="0.01" min="0" max="100"
                                                            class="form-control" name="percent" id="percent"
                                                            value="{{ old('percent', $percent ?? '') }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Statutory Earnings List - Only for Deductions -->
                                            <div id="statutory-earnings-section" style="display: none;">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label><strong>Select Statutory Earnings:</strong></label>
                                                        <div class="form-group"
                                                            style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                                            @foreach ($StatutoryEarnings as $earning)
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="selected_earnings[]"
                                                                        value="{{ $earning->id }}"
                                                                        id="earning_{{ $earning->id }}">
                                                                    <label class="form-check-label"
                                                                        for="earning_{{ $earning->id }}">
                                                                        {{ $earning->variable }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary" name="addnew">Add New</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">

                    <!-- List of payroll variables -->
                    <div class="card card-table">
                        <div class="card-header">
                            <h4 class="card-title">Payroll Variables</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th rowspan="1">S/N</th>
                                            <th rowspan="1">Earning/deduction</th>
                                            <th rowspan="1">Payroll Variable</th>
                                            <th rowspan="1">Statutory</th>
                                            <th rowspan="1">Is Taxable</th>
                                            <th rowspan="1">Pensionable</th>
                                            <th rowspan="1">Before Tax</th>
                                            <th rowspan="1">Function</th>
                                            <th rowspan="1">Ordering Rank</th>
                                            <th rowspan="1">Status</th>
                                            <th rowspan="1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                            $id = 'id';
                                        @endphp

                                        @foreach ($PayrollVariable as $list)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $list->variabletype }}</td>
                                                <td>{{ $list->variable }}</td>
                                                <td>{{ $list->statutorys }}</td>
                                                <td>{{ $list->istaxables }}</td>
                                                <td>{{ $list->isPensionables ?? ($list->isPensionable == 1 ? 'Yes' : 'No') }}
                                                </td>
                                                <td>{{ $list->variable_type == 2 ? ($list->isbefore_tax == 1 ? 'Yes' : 'No') : 'N/A' }}
                                                </td>
                                                <td>{{ $list->isFunctions ?? ($list->isFunction == 1 ? 'Yes' : 'No') }}
                                                </td>
                                                <td>{{ $list->rank }}</td>
                                                <td>{{ $list->variablestatus }}</td>
                                                <td>
                                                    <a class="btn btn-sm bg-success-light"
                                                        href="javascript: editfunc('{{ $list->id }}','{{ $list->variabletype }}','{{ $list->variable }}','{{ $list->statutory }}','{{ $list->istaxable }}','{{ $list->isPensionable }}','{{ $list->isFunction }}','{{ $list->status }}','{{ $list->rank }}','{{ $list->variable_type }}','{{ $list->percent ?? 0 }}','{{ $list->isbefore_tax ?? 0 }}')">
                                                        <i class="fe fe-pencil"></i>
                                                    </a>
                                                    <a class="btn btn-sm bg-danger-light"
                                                        href="javascript: deletefunc('{{ $list->id }}','{{ $list->variabletype }}')">
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
                    <!-- /List of payroll variables -->

                </div>
            </div>
        </div>


        <!-- Edit Details Modal -->
        <div class="modal fade" id="edit_details" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Variable Modification</h5>
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
                                        <label>Variable Type</label>
                                        <input type="text" class="form-control" id="e_v_type" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Variable Description</label>
                                        <input type="text" class="form-control" required name="variable"
                                            id="e_variable">
                                    </div>
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Statutory?</label>
                                        <br>
                                        <label>
                                            <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                                name="statutory" id="e_statutory">
                                        </label>
                                    </div>
                                </div>
                                <div id="e_content">
                                </div>
                                <div id="e_before_tax_content">
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Function?</label>
                                        <br>
                                        <label>
                                            <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                                name="isFunction" id="e_isFunction" onchange="toggleEditFunctionCard()">
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <br>
                                        <label>
                                            <input type="checkbox" data-toggle="toggle" data-on="Active"
                                                data-off="Disable" name="status" id="e_status" class="form-control"
                                                data-width="100">
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-2">
                                    <div class="form-group">
                                        <label>Rank</label>
                                        <select class="form-control" name="rank" id="e_rank">
                                            @for ($i = 0; $i <= 10; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Function Card - Shows when isFunction is true -->
                            <div id="e_function-card" style="display: none;" class="mt-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Function Settings</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Percentage (%)</label>
                                                    <input type="number" step="0.01" min="0" max="100"
                                                        class="form-control" name="percent" id="e_percent">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Statutory Earnings List - Only for Deductions -->
                                        <div id="e_statutory-earnings-section" style="display: none;">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label><strong>Select Statutory Earnings:</strong></label>
                                                    <div class="form-group"
                                                        style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                                        @foreach ($StatutoryEarnings as $earning)
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="selected_earnings[]"
                                                                    value="{{ $earning->id }}"
                                                                    id="e_earning_{{ $earning->id }}">
                                                                <label class="form-check-label"
                                                                    for="e_earning_{{ $earning->id }}">
                                                                    {{ $earning->variable }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" id="e_id" name="id">
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
                                <p class="mb-4">Are you sure <span id="content5"></span>?</p>
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
    </style>
@endsection
@section('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script>
        var currentVariableType = ''; // Store current variable_type for edit

        function editfunc(id, vtype, variable, statutory, taxable, isPensionable, isFunction, status, rank, variable_type,
            percent, isbefore_tax) {
            document.getElementById('e_id').value = id;
            document.getElementById('e_v_type').value = vtype;
            document.getElementById('e_variable').value = variable;
            document.getElementById('e_percent').value = percent || 0;
            currentVariableType = variable_type; // Store variable_type

            $('#e_statutory').bootstrapToggle('off');
            $('#e_isFunction').bootstrapToggle('off');
            $('#e_status').bootstrapToggle('off');
            if (statutory == 1) $('#e_statutory').bootstrapToggle('on');
            if (isFunction == 1) $('#e_isFunction').bootstrapToggle('on');
            if (status == 1) $('#e_status').bootstrapToggle('on');
            document.getElementById('e_rank').value = rank;
            document.getElementById('e_content').innerHTML = '';
            document.getElementById('e_before_tax_content').innerHTML = '';
            if (variable_type == 1) {
                document.getElementById('e_content').innerHTML =
                    '<div class="col-12 col-sm-3"><div class="form-group"><label>Taxable?</label><br><label><input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="taxable" id="e_taxable"></label></div></div><div class="col-12 col-sm-3"><div class="form-group"><label>Pensionable?</label><br><label><input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="isPensionable" id="e_isPensionable"></label></div></div>';
                $('#e_taxable').bootstrapToggle({
                    on: 'Yes',
                    off: 'No'
                });
                $('#e_taxable').bootstrapToggle('off');
                if (taxable == 1) $('#e_taxable').bootstrapToggle('on');
                $('#e_isPensionable').bootstrapToggle({
                    on: 'Yes',
                    off: 'No'
                });
                $('#e_isPensionable').bootstrapToggle('off');
                if (isPensionable == 1) $('#e_isPensionable').bootstrapToggle('on');
            }
            if (variable_type == 2) {
                document.getElementById('e_before_tax_content').innerHTML =
                    '<div class="col-12 col-sm-3"><div class="form-group"><label>Before Tax?</label><br><label><input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="isbefore_tax" id="e_isbefore_tax"></label></div></div>';
                $('#e_isbefore_tax').bootstrapToggle({
                    on: 'Yes',
                    off: 'No'
                });
                $('#e_isbefore_tax').bootstrapToggle('off');
                if (isbefore_tax == 1) $('#e_isbefore_tax').bootstrapToggle('on');
            }

            // Load existing selected earnings
            loadSelectedEarnings(id);

            // Toggle function card based on isFunction and variable_type
            toggleEditFunctionCard();

            $("#edit_details").modal('show')
        }

        function loadSelectedEarnings(controlVariableId) {
            // Clear all checkboxes first
            $('input[name="selected_earnings[]"]').prop('checked', false);

            // Fetch selected earnings via AJAX
            $.ajax({
                url: '/get-function-control-variables',
                method: 'GET',
                data: {
                    control_variable_id: controlVariableId
                },
                success: function(response) {
                    if (response.success && response.selectedEarnings) {
                        response.selectedEarnings.forEach(function(earningId) {
                            $('#e_earning_' + earningId).prop('checked', true);
                        });
                    }
                },
                error: function() {
                    console.log('Error loading selected earnings');
                }
            });
        }

        function toggleFunctionCard() {
            var isFunctionChecked = $('#isFunction').prop('checked');
            var variableType = $('#variabletype').val();

            if (isFunctionChecked) {
                $('#function-card').show();
                // Show statutory earnings section only if it's a deduction (variable_type = 2)
                if (variableType == 2) {
                    $('#statutory-earnings-section').show();
                } else {
                    $('#statutory-earnings-section').hide();
                }
            } else {
                $('#function-card').hide();
                $('#statutory-earnings-section').hide();
            }
        }

        function toggleEditFunctionCard() {
            var isFunctionChecked = $('#e_isFunction').prop('checked');

            if (isFunctionChecked) {
                $('#e_function-card').show();
                // Show statutory earnings section only if it's a deduction (variable_type = 2)
                if (currentVariableType == 2) {
                    $('#e_statutory-earnings-section').show();
                } else {
                    $('#e_statutory-earnings-section').hide();
                }
            } else {
                $('#e_function-card').hide();
                $('#e_statutory-earnings-section').hide();
            }
        }

        function deletefunc(id, item) {
            document.getElementById('deleteid').value = id;
            document.getElementById('content5').innerHTML = item;

            $("#delete_modal").modal('show')
        }

        $(function() {
            $('#taxable').change(function() {
                //alert("jejej");
                alert($(this).prop('checked'));
                //$('#taxable').html('Toggle: ' + $(this).prop('checked'))
            })
        })

        function VariableTypeChange() {
            //alert("jejej");
            var variableType = document.getElementById('variabletype').value;
            if (variableType == 1) {
                document.getElementById('taxable-content').innerHTML =
                    '<div class="col-md-2"><label>Taxable?</label><br><label><input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="taxable" id="taxable" ></label></div><div class="col-md-2"><label>Pensionable?</label><br><label><input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="isPensionable" id="isPensionable"></label></div>';
                $('#taxable').bootstrapToggle({
                    on: 'Yes',
                    off: 'No'
                });
                $('#isPensionable').bootstrapToggle({
                    on: 'Yes',
                    off: 'No'
                });
            } else if (variableType == 2) {
                document.getElementById('taxable-content').innerHTML =
                    '<div class="col-md-2"><label>Before Tax?</label><br><label><input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="isbefore_tax" id="isbefore_tax"></label></div>';
                $('#isbefore_tax').bootstrapToggle({
                    on: 'Yes',
                    off: 'No'
                });
            } else {
                document.getElementById('taxable-content').innerHTML = '';
            }

            // Update function card based on variable type
            toggleFunctionCard();
            //document.forms["noform"].submit();
        }

        // Initialize on page load
        $(document).ready(function() {
            // Check if isFunction is checked on page load
            if ($('#isFunction').prop('checked')) {
                toggleFunctionCard();
            }

            // Listen to variable type changes
            $('#variabletype').on('change', function() {
                toggleFunctionCard();
            });
        });
    </script>
@endsection
<!-- /Page Wrapper -->
