<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Date Range Setup
@endsection
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Setup</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Date Range Setup</li>
                        </ul>
                    </div>
                </div>
            </div>

            @include('_partialView.nofication')

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Financial Date Range</h4>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Date From <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="date_from"
                                                value="{{ old('date_from', $date_from) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Date To <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="date_to"
                                                value="{{ old('date_to', $date_to) }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary" name="update">Update Range</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

