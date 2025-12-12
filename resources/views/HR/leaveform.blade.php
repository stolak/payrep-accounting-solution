<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Leave Application
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
									<li class="breadcrumb-item active">Leave Application</li>
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
									<h4 class="card-title">Create Leave Application</h4>
								</div>
								<div class="card-body">
									<form method="post">
                                    {{ csrf_field() }}
										<div class="row">
										    <div class="col-md-3">
											    <div class="form-group">
													<label>Leave type</label>
													<select class="select_picker form-control" id="leavetype" data-live-search="true" name="leavetype">
														<option value="">--Select--</option>
														@foreach($Leavetypes as $list)
														<option value="{{ $list->id }}" {{ (old('leavetype') == $list->id ||($leavetype) == $list->id  ) ? 'selected':'' }}>{{ $list->leavetype }} </option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="col-md-3">
											    <div class="form-group">
													<label>No of Days</label>
													<?php if($days=='') $grade= old('days'); ?>
													<input type="text" class="form-control"  value="{{$days}}" required name="days">
												</div>
											</div>
											<div class="col-md-3">
											    <div class="form-group">
													<label>Start Date</label>
													<input type="date" name="startdate" value="{{$startdate}}"   class="form-control" >
												</div>
											</div>
											<div class="col-md-3">
											    <div class="form-group">
													<label>End Date</label>
													<input type="date" name="enddate" value="{{$enddate}}"   class="form-control" >
												</div>
											</div>
										</div>
										<div class="row">
										    <div class="col-md-12">
											    <div class="form-group">
													<label>Leave Purpose</label>
													<input type="text" class="form-control"  value="{{$purpose}}" required name="purpose">
												</div>
											</div>
										</div>
										
										<div class="text-right">
											<button type="submit" class="btn btn-primary" name="addnew">Submit</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					
				<div class="row">
						<div class="col-md-12">
						
							<!-- List of leave applications -->
							<div class="card card-table">
								<div class="card-header">
									<h4 class="card-title">Leave Applications</h4>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-hover table-center mb-0">
											<thead>
												<tr>
													<th rowspan="1">S/N</th>
													<th rowspan="1">Staff name</th>
													<th rowspan="1">Leave type</th>
													<th rowspan="1">No of days</th>
													<th rowspan="1">Start day</th>
													<th rowspan="1">End day</th>
													<th rowspan="1">Purpose</th>
													<th rowspan="1">Releaving officer</th>
													<th rowspan="1">Action</th>
												</tr>
											</thead>
											<tbody>
											    @php
											    $i=1;
											    @endphp
											   
											    @foreach($Leaves as $list)
												<tr>
													<td>
														{{ $i++ }}
													</td>
													<td>
														{{ $list->staffname}}
													</td>
													<td>
														{{ $list->leavetype}}
													</td>
													<td>
														{{ $list->no_of_days}}
													</td>
													<td>
														{{ $list->start_day}}
													</td>
													<td>
														{{ $list->end_day}}
													</td>
													<td>
														{{ $list->purpose}}
													</td>
													<td>
													</td>
													<td>
														<!--<a class="btn btn-sm bg-success-light" href="javascript: editfunc('{{$list->id}}','{{$list->staffname}}')">
															<i class="fe fe-pencil"></i>
														</a>
														<a class="btn btn-sm bg-danger-light" href="javascript: deletefunc('{{$list->id}}')">
															<i class="fe fe-trash"></i>
														</a>-->
													</td>
												</tr>
											    @endforeach
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<!-- /List of leave applications -->
							
						</div>
					</div>
				</div>
		
			</div>

@endsection
@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
<style>
label {
  color: black
  text-shadow: 1px 1px 2px #fff;
}
</style>
@endsection
@section('scripts')

<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
<script>
$('.select_picker').selectpicker({
          style: 'btn-default',
          size: 4
        });

    function editfunc(id,cat)
    {
        document.getElementById('id').value = id;
        document.getElementById('grade').value = cat;
        
        
        $("#edit_details").modal('show')
    }
   function deletefunc(id)
    {
        document.getElementById('deleteid').value = id;
                     
        $("#delete_modal").modal('show')
    }
    
             
</script>
@endsection
			<!-- /Page Wrapper -->
