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
													<?php if($variabletype=='') $variabletype= old('variabletype'); ?>
													<label>Variable type</label>
													<select  class="form-control" name="variabletype" id="variabletype" onchange="VariableTypeChange()">
														<option value="">--Select--</option>
														@foreach($VariableType as $list)
														<option value="{{ $list->id }}" {{ ($variabletype) == $list->id   ? 'selected':'' }}>{{ $list->particular }}</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="col-md-3">
											    <div class="form-group">
													<label>Variable</label>
													<input type="text" class="form-control"  value="" name="variable" >
												</div>
											</div>
											<div class="col-md-2">
												<label>Statutory?</label>
												<br>
												<label>
													<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="statutory">
												</label>
											</div>
											<div id="taxable-content">
											@if($variabletype==1)
											<div class="col-md-2">
												<label>Taxable?</label>
												<br>
												<label>
													<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="taxable" id="taxable" >
												</label>
											</div>
											<div class="col-md-2">
												<label>Pensionable?</label>
												<br>
												<label>
													<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="isPensionable" id="isPensionable">
												</label>
											</div>
											@endif
											</div>
											<div class="col-md-2">
												<label>Function?</label>
												<br>
												<label>
													<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="isFunction">
												</label>
											</div>
											<div class="col-md-2">
											    <div class="form-group">
													<label>Rank</label>
													<select  class="form-control" name="rank" >
														<option value="">--Select--</option>
														@for($i = 0; $i <= 10; $i++)
														<option value="{{ $i }}" {{ (old('rank') == $i ||($rank) == $i  ) ? 'selected':'' }}>{{$i}}</option>
														@endfor
													</select>
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
													<th rowspan="1">Function</th>
													<th rowspan="1">Ordering Rank</th>
													<th rowspan="1">Status</th>
													<th rowspan="1">Action</th>
												</tr>
											</thead>
											<tbody>
											    @php
											    $i=1;
											    $id='id';
											    @endphp
											   
											    @foreach($PayrollVariable as $list)
												<tr>
													<td>{{ $i++ }}</td>
													<td>{{$list->variabletype}}</td>
													<td>{{$list->variable}}</td>
													<td>{{$list->statutorys}}</td>
													<td>{{$list->istaxables}}</td>
													<td>{{$list->isPensionables ?? ($list->isPensionable == 1 ? 'Yes' : 'No')}}</td>
													<td>{{$list->isFunctions ?? ($list->isFunction == 1 ? 'Yes' : 'No')}}</td>
													<td>{{$list->rank}}</td>
													<td>{{$list->variablestatus}}</td>
													<td>
														<a class="btn btn-sm bg-success-light" href="javascript: editfunc('{{$list->id}}','{{$list->variabletype}}','{{$list->variable}}','{{$list->statutory}}','{{$list->istaxable}}','{{$list->isPensionable}}','{{$list->isFunction}}','{{$list->status}}','{{$list->rank}}','{{$list->variable_type}}')">
															<i class="fe fe-pencil"></i>
														</a>
														<a class="btn btn-sm bg-danger-light" href="javascript: deletefunc('{{$list->id}}','{{$list->variabletype}}')">
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
				<div class="modal-dialog modal-dialog-centered" role="document" >
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Variable Modification</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<form method="post" >
                                    {{ csrf_field() }}
								<div class="row ">
									<div class="col-12 col-sm-12">
										<div class="form-group">
											<label>Variable Type</label>
											<input type="text" class="form-control"  id="e_v_type" readonly>
										</div>
									</div>
							    </div>
							    <div class="row ">
									<div class="col-12 col-sm-12">
										<div class="form-group">
											<label>Variable Description</label>
											<input type="text" class="form-control"  required name="variable" id="e_variable">
										</div>
									</div>
							    </div>
							    <div class="row ">
									<div class="col-12 col-sm-3">
										<div class="form-group">
											<label>Statutory?</label>
											<br>
											<label>
												<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="statutory" id="e_statutory">
											</label>
										</div>
									</div>
									<div id="e_content">
									</div>
									<div class="col-12 col-sm-3">
										<div class="form-group">
											<label>Function?</label>
											<br>
											<label>
												<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="isFunction" id="e_isFunction">
											</label>
										</div>
									</div>
									<div class="col-12 col-sm-3">
										<div class="form-group">
											<label>Status</label>
											<br>
											<label>
												<input type="checkbox" data-toggle="toggle" data-on="Active" data-off="Disable" name="status" id="e_status" class="form-control" data-width="100">
											</label>
										</div>
									</div>
									<div class="col-12 col-sm-2">
										<div class="form-group">
											<label>Rank</label>
											<select  class="form-control" name="rank" id="e_rank" >
												@for($i = 0; $i <= 10; $i++)
												<option value="{{ $i }}" >{{$i}}</option>
												@endfor
											</select>
										</div>
									</div>
							    </div>
							   
								<input type="hidden" id="e_id" name="id" >
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
				<div class="modal-dialog modal-dialog-centered" role="document" >
					<div class="modal-content">
						<form method="post" >
                                    {{ csrf_field() }}
						<div class="modal-body">
							<div class="form-content p-2">
								<h4 class="modal-title">Delete</h4>
								<p class="mb-4">Are you sure <span id="content5"></span>?</p>
								<button type="submit" class="btn btn-primary" name="del">Continue </button>
								<input type="hidden" id="deleteid" name="deleteid" >
								<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
							</div>
						</div>
					</form>
					</div>
				</div>
			</div>
			<!-- /Delete Modal -->
		
			</div>

<form method="post"  id="noform" name="noform">
{{ csrf_field() }}
 <input type="hidden" class="form-control" id="noid" name="id" value="">

</form>

@endsection
@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
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
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>

    function editfunc(id,vtype,variable,statutory,taxable,isPensionable,isFunction,status,rank,variable_type)
    {
        document.getElementById('e_id').value = id;
          document.getElementById('e_v_type').value = vtype;
          document.getElementById('e_variable').value = variable;
          
          $('#e_statutory').bootstrapToggle('off');
          $('#e_isFunction').bootstrapToggle('off');
          $('#e_status').bootstrapToggle('off');
          if(statutory==1)$('#e_statutory').bootstrapToggle('on');
          if(isFunction==1)$('#e_isFunction').bootstrapToggle('on');
          if(status==1)$('#e_status').bootstrapToggle('on');
            document.getElementById('e_rank').value = rank;
            document.getElementById('e_content').innerHTML ='';
            if(variable_type==1){
            document.getElementById('e_content').innerHTML ='<div class="col-12 col-sm-3"><div class="form-group"><label>Taxable?</label><br><label><input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="taxable" id="e_taxable"></label></div></div><div class="col-12 col-sm-3"><div class="form-group"><label>Pensionable?</label><br><label><input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="isPensionable" id="e_isPensionable"></label></div></div>';
            $('#e_taxable').bootstrapToggle({
              on: 'Yes',
              off: 'No'
            });
            $('#e_taxable').bootstrapToggle('off');
            if(taxable==1)$('#e_taxable').bootstrapToggle('on');
            $('#e_isPensionable').bootstrapToggle({
              on: 'Yes',
              off: 'No'
            });
            $('#e_isPensionable').bootstrapToggle('off');
            if(isPensionable==1)$('#e_isPensionable').bootstrapToggle('on');
            }
        $("#edit_details").modal('show')
    }
   function deletefunc(id,item)
    {
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
    
    function VariableTypeChange()
    {
        //alert("jejej");
        if(document.getElementById('variabletype').value==1){
          document.getElementById('taxable-content').innerHTML = '<div class="col-md-2"><label>Taxable?</label><br><label><input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="taxable" id="taxable" ></label></div><div class="col-md-2"><label>Pensionable?</label><br><label><input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="isPensionable" id="isPensionable"></label></div>';   
            $('#taxable').bootstrapToggle({
              on: 'Yes',
              off: 'No'
            });
            $('#isPensionable').bootstrapToggle({
              on: 'Yes',
              off: 'No'
            });
        }else{
          document.getElementById('taxable-content').innerHTML='';  
        }
       //document.forms["noform"].submit();
    }
             
</script>
@endsection
			<!-- /Page Wrapper -->
