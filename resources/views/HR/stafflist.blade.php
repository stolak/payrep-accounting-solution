<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Register
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
									<li class="breadcrumb-item active">Staff Register</li>
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
						
							<!-- List of staff -->
							<div class="card card-table">
								<div class="card-header">
									<h4 class="card-title">Staff Register</h4>
									<div class="text-right">
										<button class="btn btn-primary" type="button" onclick="Addnew()">Add New</button>
									</div>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-hover table-center mb-0">
											<thead>
												<tr>
													<th rowspan="1">S/N</th>
													<th rowspan="1">Staff No</th>
													<th rowspan="1">Full Name</th>
													<th rowspan="1">Department</th>
													<th rowspan="1">Grade</th>
													<th rowspan="1">Action</th>
												</tr>
											</thead>
											<tbody>
											    @php
											    $i=1;
											    @endphp
											   
											    @foreach($Staffs as $list)
												<tr>
													<td>
														{{ $i++ }}
													</td>
													<td>
														{{$list->staff_no}}
													</td>
													<td>
														{{$list->first_name}} {{$list->middle_name}} {{$list->last_name}}
													</td>
													<td>
														{{$list->departments}}
													</td>
													<td>
														{{$list->grades}}
													</td>
													<td>
														<a class="btn btn-sm bg-success-light" href="javascript: editfunc('{{$list->id}}')">
															<i class="fe fe-pencil"></i>
														</a>
														<a class="btn btn-sm bg-danger-light" href="javascript: deletefunc('{{$list->id}}','{{$list->first_name}} {{$list->middle_name}} {{$list->last_name}}')">
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
							<!-- /List of staff -->
							
						</div>
					</div>
				</div>
  
			
			<!-- Delete Modal -->
			<div class="modal fade" id="delete_modal" aria-hidden="true" role="dialog">
				<div class="modal-dialog modal-dialog-centered" role="document" >
					<div class="modal-content">
						<form method="post" >
                                    {{ csrf_field() }}
						<div class="modal-body">
							<div class="form-content p-2">
								<h4 class="modal-title">Delete</h4>
								<p class="mb-4">Are you sure want to delete <span id="content5"></span>?</p>
								<button type="submit" class="btn btn-primary" name="delinv">Continue </button>
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
<form method="post"  id="updateform" name="updateform"  action="/staff-modification">
{{ csrf_field() }}
 <input type="hidden" class="form-control" id="staffid" name="staffid" value="">
</form>
<form method="post"  id="newform" name="newform"  action="/staff-registration">
{{ csrf_field() }}
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
 
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
<script>

    function editfunc(id)
    {
        document.getElementById('staffid').value = id;
        document.forms["updateform"].submit();
    }
    
    function editEventoryfunc(id,cat,br,item,msku,tx,c,sz,sn)
    {
        document.getElementById('invid').value = id;
         document.getElementById('brand').value = br;
         document.getElementById('category').value = cat;
         document.getElementById('item').value = item;
         document.getElementById('mskuformat').value = msku;
         document.getElementById('taxperc').value = tx;
         document.getElementById('hcalor').value = c;
         document.getElementById('hsize').value = sz;
         document.getElementById('hserialno').value = sn;
        //document.getElementById('brand').value = id;
        //document.getElementById('category').value = cat;
        
        
        $("#editModal").modal('show')
    }
   function deletefunc(id,item)
    {
        document.getElementById('deleteid').value = id;
        document.getElementById('content5').innerHTML = item;
                     
        $("#delete_modal").modal('show')
    }
    
     function deletePfunc(id,f)
    {
        //alert("djfjf");
        document.getElementById('deletepid').value = id;
        document.getElementById('contentpid').innerHTML = f;
                     
        $("#deleteModalp").modal('show')
    }
     function deleteSfunc(id,f)
    {
        //alert("djfjf");
        document.getElementById('deletesid').value = id;
        document.getElementById('contentsid').innerHTML = f;
                     
        $("#deleteModals").modal('show')
    }
    function Addnew()
    {
        document.forms["newform"].submit();
    }
    function newPformat()
    {
        $("#pnewModal").modal('show')
    }
    function newSformat()
    {
        $("#snewModal").modal('show')
    }
    function editPfunc(id,f,q,p)
    {
        document.getElementById('pid').value = id;
        document.getElementById('puformat').value = f;
        
        document.getElementById('puqty').value = q;
        
        document.getElementById('puprice').value = p;
       
        $("#pupdateModal").modal('show')
    }
    function editSfunc(id,f,q,p)
    {
        document.getElementById('sid').value = id;
        document.getElementById('suformat').value = f;
        document.getElementById('suqty').value = q;
        document.getElementById('suprice').value = p;
        $("#supdateModal").modal('show')
    }
    
    function SelectInventory(id)
    {
        document.getElementById('noid').value = id;
       document.forms["noform"].submit();
    }
             
</script>
@endsection
			<!-- /Page Wrapper -->
