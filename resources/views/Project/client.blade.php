<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Client Setup
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
                            <li class="breadcrumb-item active">Client Setup</li>
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
                            <h4 class="card-title">Create Client</h4>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <?php if ($name == '') {
                                                $name = old('name');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $name }}" required
                                                name="name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Client Code</label>
                                            <?php if ($clientCode == '') {
                                                $clientCode = old('client_code');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $clientCode }}"
                                                name="client_code">
                                            <small class="form-text text-muted">Optional. Auto-generated when left blank.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Client Type</label>
                                            <?php if ($clientType == '') {
                                                $clientType = old('client_type');
                                            } ?>
                                            <select class="select2 form-control" name="client_type" required>
                                                <option value="">--Select--</option>
                                                @foreach ($clientTypes as $type)
                                                    <option value="{{ $type->id }}"
                                                        {{ $clientType == $type->id ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Client Ledgers</label>
                                            <?php
                                            $selectedClientAccountIds = old('clientAccountIds', $clientAccountIds ?? []);
                                            if (!is_array($selectedClientAccountIds)) {
                                                $selectedClientAccountIds = [];
                                            }
                                            ?>
                                            <select class="select2 form-control" name="clientAccountIds[]" multiple
                                                required>
                                                @foreach ($accountLookUp as $account)
                                                    <option value="{{ $account->id }}"
                                                        {{ in_array($account->id, $selectedClientAccountIds) ? 'selected' : '' }}>
                                                        {{ $account->accountno }}-{{ $account->accountdescription }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">Select one or more ledgers. A ledger can
                                                belong to only one client.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Contact Address</label>
                                            <?php if ($contactAddress == '') {
                                                $contactAddress = old('contact_address');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $contactAddress }}"
                                                name="contact_address">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Contact Phone Number</label>
                                            <?php if ($contactPhoneNumber == '') {
                                                $contactPhoneNumber = old('contact_phone_number');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $contactPhoneNumber }}"
                                                name="contact_phone_number">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Contact Email Address</label>
                                            <?php if ($contactEmailAddress == '') {
                                                $contactEmailAddress = old('contact_email_address');
                                            } ?>
                                            <input type="email" class="form-control" value="{{ $contactEmailAddress }}"
                                                name="contact_email_address">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Project Categories</label>
                                            <select class="select2 form-control" name="projectCategoryIds[]" multiple>
                                                @foreach ($projectCategories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ in_array($category->id, old('projectCategoryIds', [])) ? 'selected' : '' }}>
                                                        {{ $category->category }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">Select one or more project
                                                categories</small>
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

                    <!-- List of clients -->
                    <div class="card card-table">
                        <div class="card-header">
                            <h4 class="card-title">Clients</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th rowspan="1">S/N</th>
                                            <th rowspan="1">Name</th>
                                            <th rowspan="1">Client Code</th>
                                            <th rowspan="1">Client Type</th>
                                            <th rowspan="1">Status</th>
                                            <th rowspan="1">Client Ledgers</th>
                                            <th rowspan="1">Contact</th>
                                            <th rowspan="1">Project Categories</th>
                                            <th rowspan="1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                        @endphp

                                        @foreach ($clients as $list)
                                            <tr>
                                                <td>
                                                    {{ $i++ }}
                                                </td>
                                                <td>
                                                    {{ $list->name }}
                                                </td>
                                                <td>
                                                    {{ $list->client_code ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    {{ $list->clientTypeName ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    {{ $list->status ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    @if ($list->clientLedgers && count($list->clientLedgers) > 0)
                                                        @foreach ($list->clientLedgers as $ledger)
                                                            <span class="badge badge-primary">
                                                                {{ $account->accountno }}-{{ $ledger->accountdescription }}
                                                            </span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $list->contact_phone_number ?? 'N/A' }}
                                                    <br>
                                                    <small>{{ $list->contact_email_address ?? '' }}</small>
                                                </td>
                                                <td>
                                                    @if ($list->projectCategories && count($list->projectCategories) > 0)
                                                        @foreach ($list->projectCategories as $category)
                                                            <span class="badge badge-info">{{ $category->category }}</span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $categoryIds = $list->projectCategories
                                                            ? $list->projectCategories->pluck('id')->toArray()
                                                            : [];
                                                        $ledgerIds = $list->clientLedgers
                                                            ? $list->clientLedgers->pluck('clientAccountId')->toArray()
                                                            : [];
                                                    @endphp
                                                    <a class="btn btn-sm bg-success-light"
                                                        href="javascript: editfunc(
                                                            '{{ $list->id }}',
                                                            '{{ addslashes($list->name) }}',
                                                            '{{ addslashes($list->client_code ?? '') }}',
                                                            '{{ $list->client_type ?? '' }}',
                                                            '{{ $list->status ?? 'Active' }}',
                                                            '{{ addslashes($list->contact_address ?? '') }}',
                                                            '{{ addslashes($list->contact_phone_number ?? '') }}',
                                                            '{{ addslashes($list->contact_email_address ?? '') }}',
                                                            {{ json_encode($ledgerIds) }},
                                                            {{ json_encode($categoryIds) }}
                                                        )">
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
                    <!-- /List of clients -->

                </div>
            </div>
        </div>

        <!-- Edit Details Modal -->
        <div class="modal fade" id="edit_details" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Client</h5>
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
                                        <label>Name</label>
                                        <input type="text" id="name" name="name" class="form-control"
                                            style="text-align: left;" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>Client Code</label>
                                        <input type="text" id="client_code" name="client_code" class="form-control"
                                            style="text-align: left;" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>Client Type</label>
                                        <select class="select2 form-control" id="client_type" name="client_type">
                                            <option value="">--Select--</option>
                                            @foreach ($clientTypes as $type)
                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Client Ledgers</label>
                                        <select class="select2 form-control" id="clientAccountIds"
                                            name="clientAccountIds[]" multiple required>
                                            @foreach ($accountLookUp as $account)
                                                <option value="{{ $account->id }}">
                                                    {{ $account->accountno }}-{{ $account->accountdescription }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Select one or more ledgers. A ledger can
                                            belong to only one client.</small>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="Active">Active</option>
                                            <option value="On Hold">On Hold</option>
                                            <option value="Inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-8">
                                    <div class="form-group">
                                        <label>Contact Address</label>
                                        <input type="text" id="contact_address" name="contact_address"
                                            class="form-control" style="text-align: left;" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>Contact Phone Number</label>
                                        <input type="text" id="contact_phone_number" name="contact_phone_number"
                                            class="form-control" style="text-align: left;" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>Contact Email Address</label>
                                        <input type="email" id="contact_email_address" name="contact_email_address"
                                            class="form-control" style="text-align: left;" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Project Categories</label>
                                        <select class="select2 form-control" id="projectCategoryIds"
                                            name="projectCategoryIds[]" multiple>
                                            @foreach ($projectCategories as $category)
                                                <option value="{{ $category->id }}">
                                                    {{ $category->category }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Select one or more project categories</small>
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
        function editfunc(id, name, clientCode, clientType, status, contactAddress,
            contactPhoneNumber, contactEmailAddress, clientAccountIds, projectCategoryIds) {
            document.getElementById('id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('client_code').value = clientCode || '';
            document.getElementById('status').value = status || 'Active';
            document.getElementById('contact_address').value = contactAddress || '';
            document.getElementById('contact_phone_number').value = contactPhoneNumber || '';
            document.getElementById('contact_email_address').value = contactEmailAddress || '';
            $('#client_type').val(clientType || '').trigger('change');

            // Set selected client ledgers
            $('#clientAccountIds').val(null).trigger('change');
            if (clientAccountIds && clientAccountIds.length > 0) {
                $('#clientAccountIds').val(clientAccountIds).trigger('change');
            }

            // Clear previous selections
            $('#projectCategoryIds').val(null).trigger('change');

            // Set selected project categories
            if (projectCategoryIds && projectCategoryIds.length > 0) {
                $('#projectCategoryIds').val(projectCategoryIds).trigger('change');
            }

            $("#edit_details").modal('show')
        }

        function deletefunc(id) {
            document.getElementById('deleteid').value = id;

            $("#delete_modal").modal('show')
        }
    </script>
@endsection
<!-- /Page Wrapper -->
