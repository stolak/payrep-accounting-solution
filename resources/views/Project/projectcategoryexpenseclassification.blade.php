<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Project Category Expense Classification Setup
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
                            <li class="breadcrumb-item active">Project Category Expense Classification Setup</li>
                        </ul>
                    </div>
                    <div class="col-auto">
                        <a href="{{ url('/project-category') }}" class="btn btn-primary">
                            <i class="fe fe-arrow-left"></i> Back to Category
                        </a>
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
                            <h4 class="card-title">Select Project Category</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" id="categorySelectForm">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Project Category <span class="text-danger">*</span></label>
                                            <?php if ($projectCategoryId == '') {
                                                $projectCategoryId = old('projectCategoryId');
                                            } ?>
                                            <select class="select2 form-control" name="projectCategoryId"
                                                id="projectCategoryId" required onchange="selectCategory()">
                                                <option value="">--Select Project Category--</option>
                                                @foreach ($projectCategories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ $projectCategoryId == $category->id ? 'selected' : '' }}>
                                                        {{ $category->category }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="select_category" value="1">
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if (!empty($projectCategoryId))
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Assign Expense Classifications</h4>
                                <a href="{{ url('/budget-category' . (!empty($projectCategoryId) ? '?projectCategoryId=' . $projectCategoryId : '')) }}"
                                    class="btn btn-primary">
                                    <i class="fe fe-plus"></i> Add New
                                </a>
                            </div>
                            <div class="card-body">
                                <form method="post" id="assignForm">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="projectCategoryId" value="{{ $projectCategoryId }}">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label><strong>Select Expense Classifications:</strong></label>
                                                <div class="form-group"
                                                    style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; border-radius: 4px; background-color: #f8f9fa;">
                                                    @if ($expenseClassifications->count() > 0)
                                                        @foreach ($expenseClassifications as $classification)
                                                            <div
                                                                class="d-flex justify-content-between align-items-center mb-2">
                                                                <div class="form-check mb-0">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="expense_classifications[]"
                                                                        value="{{ $classification->id }}"
                                                                        id="classification_{{ $classification->id }}"
                                                                        {{ in_array($classification->id, $assignedClassifications ?? []) ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="classification_{{ $classification->id }}">
                                                                        <strong>{{ $classification->category }}</strong>
                                                                    </label>
                                                                </div>
                                                                <a class="btn btn-sm bg-info-light ml-2"
                                                                    href="{{ url('/budget-setup?classificationId=' . $classification->id) }}"
                                                                    title="View Element">
                                                                    View Element
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <p class="text-muted">No expense classifications available.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary" name="assign">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <p class="text-center text-muted">Please select a project category to assign expense
                                    classifications.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
    <style>
        label {
            color: black;
            text-shadow: 1px 1px 2px #fff;
        }

        .form-check-label {
            cursor: pointer;
        }

        .badge {
            margin-left: 5px;
            font-size: 0.75em;
        }
    </style>
@endsection
@section('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script>
        function selectCategory() {
            var projectCategoryId = document.getElementById('projectCategoryId').value;
            if (projectCategoryId) {
                document.getElementById('categorySelectForm').submit();
            }
        }
    </script>
@endsection
<!-- /Page Wrapper -->
