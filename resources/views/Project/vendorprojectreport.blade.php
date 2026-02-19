<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Vendor Project Report
@endsection
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Report</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Vendor Project Report</li>
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
                            <h4 class="card-title">Filter Options</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" id="filterForm">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Vendor</label>
                                            <?php
                                            if ($vendorId == '') {
                                                $vendorId = old('vendorId', 'all');
                                            }
                                            ?>
                                            <select class="select2 form-control" name="vendorId" id="vendorId">
                                                <option value="all"
                                                    {{ $vendorId == 'all' || $vendorId == '' ? 'selected' : '' }}>All
                                                    Vendors</option>
                                                @foreach ($vendors as $vendor)
                                                    <option value="{{ $vendor->id }}"
                                                        {{ $vendorId == $vendor->id ? 'selected' : '' }}>
                                                        {{ $vendor->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Project</label>
                                            <?php
                                            if ($projectId == '') {
                                                $projectId = old('projectId', 'all');
                                            }
                                            ?>
                                            <select class="select2 form-control" name="projectId" id="projectId">
                                                <option value="all"
                                                    {{ $projectId == 'all' || $projectId == '' ? 'selected' : '' }}>All
                                                    Projects</option>
                                                @foreach ($projects as $project)
                                                    <option value="{{ $project->id }}"
                                                        {{ $projectId == $project->id ? 'selected' : '' }}>
                                                        {{ $project->projectCode }} - {{ $project->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <?php if ($status == '') {
                                                $status = old('status');
                                            } ?>
                                            <select class="form-control" name="status" id="status">
                                                <option value="">All Status</option>
                                                <option value="Pending" {{ $status == 'Pending' ? 'selected' : '' }}>
                                                    Pending</option>
                                                <option value="Approved" {{ $status == 'Approved' ? 'selected' : '' }}>
                                                    Approved</option>
                                                <option value="Rejected" {{ $status == 'Rejected' ? 'selected' : '' }}>
                                                    Rejected</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <?php if ($startDate == '') {
                                                $startDate = old('startDate');
                                            } ?>
                                            <input type="date" class="form-control" name="startDate" id="startDate"
                                                value="{{ $startDate }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>End Date</label>
                                            <?php if ($endDate == '') {
                                                $endDate = old('endDate');
                                            } ?>
                                            <input type="date" class="form-control" name="endDate" id="endDate"
                                                value="{{ $endDate }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button type="button" class="btn btn-primary" onclick="applyFilters()">
                                                    <i class="fe fe-filter"></i> Apply Filters
                                                </button>
                                                <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                                                    <i class="fe fe-x"></i> Clear
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="select_vendor" id="select_vendor" value="1">
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <!-- Vendor Project Report -->
                    <div class="card card-table">
                        <div class="card-header">
                            <h4 class="card-title">Vendor Project Report</h4>
                            @if (!empty($totalAmount) || $vendorProjects->count() > 0)
                                <div class="mt-2">
                                    <span class="badge bg-info">
                                        Total Amount: {{ number_format($totalAmount, 2) }}
                                    </span>
                                    <span class="badge bg-secondary ml-2">
                                        Total Records: {{ $vendorProjects->count() }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th rowspan="1">S/N</th>
                                            <th rowspan="1">Vendor</th>
                                            <th rowspan="1">Project Code</th>
                                            <th rowspan="1">Project Name</th>
                                            <th rowspan="1" style="text-align: right;">VAT (%)</th>
                                            <th rowspan="1" style="text-align: right;">VAT Amount</th>
                                            <th rowspan="1" style="text-align: right;">Amount</th>
                                            <th rowspan="1">Status</th>
                                            <th rowspan="1">Created By</th>
                                            <th rowspan="1">Approved By</th>
                                            <th rowspan="1">Created Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                        @endphp

                                        @if ($vendorProjects->count() > 0)
                                            @foreach ($vendorProjects as $vendorProject)
                                                <tr>
                                                    <td>
                                                        {{ $i++ }}
                                                    </td>
                                                    <td>
                                                        <strong>{{ $vendorProject->vendorName ?? 'N/A' }}</strong>
                                                    </td>
                                                    <td>
                                                        <strong>{{ $vendorProject->projectCode ?? 'N/A' }}</strong>
                                                    </td>
                                                    <td>
                                                        {{ $vendorProject->projectName ?? 'N/A' }}
                                                    </td>
                                                    <td style="text-align: right;">
                                                        {{ number_format($vendorProject->vat ?? 0, 2, '.', ',') }}
                                                    </td>
                                                    <td style="text-align: right;">
                                                        {{ number_format($vendorProject->vatAmount ?? 0, 2, '.', ',') }}
                                                    </td>
                                                    <td style="text-align: right;">
                                                        <strong>{{ number_format($vendorProject->amount, 2, '.', ',') }}</strong>
                                                    </td>
                                                    <td>
                                                        @if ($vendorProject->status == 'Approved')
                                                            <span class="badge bg-success">Approved</span>
                                                        @elseif ($vendorProject->status == 'Rejected')
                                                            <span class="badge bg-danger">Rejected</span>
                                                        @else
                                                            <span class="badge bg-warning">Pending</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $vendorProject->createdByName ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        {{ $vendorProject->approvedByName ?? '-' }}
                                                    </td>
                                                    <td>
                                                        {{ date('Y-m-d', strtotime($vendorProject->createdAt)) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="11" class="text-center">No vendor projects found for the
                                                    selected criteria.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    @if ($vendorProjects->count() > 0)
                                        <tfoot>
                                            <tr style="background-color: #e8e8e8; font-weight: bold;">
                                                <td colspan="6" class="text-right">
                                                    <strong>Total:</strong>
                                                </td>
                                                <td style="text-align: right;">
                                                    <strong>{{ number_format($totalAmount, 2, '.', ',') }}</strong>
                                                </td>
                                                <td colspan="4"></td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /Vendor Project Report -->
                </div>
            </div>
        </div>
    </div>
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
        function applyFilters() {
            // Ensure select_vendor is set to maintain selections
            document.getElementById('select_vendor').value = '1';
            document.getElementById('filterForm').submit();
        }

        function clearFilters() {
            document.getElementById('vendorId').value = 'all';
            document.getElementById('projectId').value = 'all';
            document.getElementById('status').value = '';
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            document.getElementById('select_vendor').value = '1';
            document.getElementById('filterForm').submit();
        }
    </script>
@endsection
<!-- /Page Wrapper -->
