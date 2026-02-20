<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Expense Classification
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
                            <li class="breadcrumb-item active">Expense Classification</li>
                        </ul>
                    </div>
                    @if (!empty(request('projectCategoryId')))
                        <div class="col-auto">
                            <a href="{{ url('/project-category-expense-classification?projectCategoryId=' . request('projectCategoryId')) }}"
                                class="btn btn-primary">
                                <i class="fe fe-arrow-left"></i> Back to Expense Classification
                            </a>
                        </div>
                    @endif
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
                            <h4 class="card-title"> Classification</h4>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Classification</label>
                                            <?php if ($category == '') {
                                                $category = old('category');
                                            } ?>
                                            <input type="text" class="form-control" value="{{ $category }}" required
                                                name="category">
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

                    <!-- List of budget classifications -->
                    <div class="card card-table">
                        <div class="card-header">
                            <h4 class="card-title">Expense Classifications</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th rowspan="1">S/N</th>
                                            <th rowspan="1">Classification</th>

                                            <th rowspan="1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                        @endphp

                                        @foreach ($budgetCategories as $list)
                                            <tr>
                                                <td>
                                                    {{ $i++ }}
                                                </td>
                                                <td>
                                                    {{ $list->category }}
                                                </td>


                                                <td>
                                                    <a class="btn btn-sm bg-success-light"
                                                        href="javascript: editfunc('{{ $list->id }}','{{ $list->category }}','{{ $list->isMeasure }}','{{ $list->isMilestone }}','{{ $list->isSubContrator }}')">
                                                        <i class="fe fe-pencil"></i>
                                                    </a>
                                                    <a class="btn btn-sm bg-info-light"
                                                        href="{{ url('/budget-setup?classificationId=' . $list->id) }}"
                                                        title="Expense Element">
                                                        <i class="fe fe-list"></i> Expense Element
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
                    <!-- /List of budget classifications -->

                </div>
            </div>
        </div>

        <!-- Edit Details Modal -->
        <div class="modal fade" id="edit_details" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Expense Classification</h5>
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
                                        <label>Expense Classification</label>
                                        <input type="text" id="category" name="category" class="form-control"
                                            style="text-align: left;" autocomplete="off">
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

        .form-check-input {
            width: 3rem;
            height: 1.5rem;
            cursor: pointer;
        }

        .form-check-label {
            margin-left: 10px;
            cursor: pointer;
        }
    </style>
@endsection
@section('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script>
        function editfunc(id, cat, isMeasure, isMilestone, isSubContrator) {
            document.getElementById('id').value = id;
            document.getElementById('category').value = cat;



            $("#edit_details").modal('show')
        }






        function deletefunc(id) {
            document.getElementById('deleteid').value = id;

            $("#delete_modal").modal('show')
        }
    </script>
@endsection
<!-- /Page Wrapper -->
