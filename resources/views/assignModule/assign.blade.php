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
                        <h3 class="page-title">Role privileges</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Role Privileges</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            <!-- include notoifcation -->
            @include('_partialView.nofication')
            <!-- /include notoifcation -->
            <form method="post" name="mainform" id="mainform" action="{{ url('/assign-module/assign') }}">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">

                            <div class="card-body">

                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Select user role</label>
                                            <select class="select2 form-control" name="role" id="role"
                                                onchange="Reload();">
                                                <option value="">--Select--</option>
                                                @foreach ($roles as $list)
                                                    <option value="{{ $list->id }}"
                                                        {{ old('role') == $list->id || $role == $list->id ? 'selected' : '' }}>
                                                        {{ $list->rolename }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary" name="addnew">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Recent Orders -->
                <div class="card card-table">
                    <div class="card-header">
                        <h4 class="card-title">Privileges</h4>
                    </div>
                    <div class="card-body">
                        @if (isset($organizedData))
                            @foreach ($organizedData as $parentMenu)
                                <div class="mb-4">
                                    <h5 class="mb-3"
                                        style="color: #2c5f2d; font-weight: bold; padding: 10px; background-color: #e8f5e9; border-left: 4px solid #2c5f2d;">
                                        <i class="fe fe-folder"></i> {{ $parentMenu['parentMenu'] }}
                                    </h5>
                                    @foreach ($parentMenu['modules'] as $module)
                                        <div class="mb-3 ml-4">
                                            <h6 class="mb-2"
                                                style="color: #555; font-weight: 600; padding: 8px; background-color: #f8f9fa; border-left: 3px solid #6c757d;">
                                                <i class="fe fe-folder-plus"></i> {{ $module['module'] }}
                                            </h6>
                                            <div class="table-responsive ml-4">
                                                <table class="table table-sm table-hover mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 60%;">Submodule</th>
                                                            <th style="width: 40%;">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($module['submodules'] as $submodule)
                                                            <tr>
                                                                <td>{{ $submodule->submodule }}</td>
                                                                <td>
                                                                    <div class="status-toggle">
                                                                        <input type="checkbox"
                                                                            id="status_{{ $submodule->modID }}"
                                                                            name="arraysubModule_{{ $submodule->modID }}"
                                                                            class="check"
                                                                            {{ $submodule->active ? 'checked' : '' }}>
                                                                        <label for="status_{{ $submodule->modID }}"
                                                                            class="checktoggle"></label>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>Module</th>
                                            <th>Submodule</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($submodules as $list)
                                            <tr>
                                                <td>{{ $list->module }}</td>
                                                <td>{{ $list->submodule }}</td>
                                                <td>
                                                    <div class="status-toggle">
                                                        <input type="checkbox" id="status_{{ $list->modID }}"
                                                            name="arraysubModule_{{ $list->modID }}" class="check"
                                                            {{ $list->active ? 'checked' : '' }}>
                                                        <label for="status_{{ $list->modID }}"
                                                            class="checktoggle"></label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </form> <!-- /Recent Orders -->

        </div>

    </div>
    <form method="post" name="mainform1" id="mainform1">
        {{ csrf_field() }}
        <input type ="hidden" id='id' name="role" />
    </form>
@endsection
@section('scripts')
    <script>
        function Reload() {
            document.getElementById('id').value = document.getElementById('role').value;
            document.forms["mainform1"].submit();
            return;
        }
    </script>
@endsection

@section('styles')
@endsection
<!-- /Page Wrapper -->
