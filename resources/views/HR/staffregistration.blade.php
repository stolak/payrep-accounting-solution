<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Registration
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
									<li class="breadcrumb-item active">Staff Registration</li>
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
									<h4 class="card-title">Staff Registration</h4>
								</div>
								<div class="card-body">
									<form method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}
										<div class="row">
										    <div class="col-md-9">
										    </div>
										    <div class="col-md-3">
												<div style="width: 150px;height:150px;" class="float-right" >
													<img src="{{'/img/profile_img.png'}}" alt="profile image" id="output_image" style="max-width: 100%;max-height: 100%;">
												</div>
												<input type="file" class="hidden-print" name="passport" accept="image/*" onchange="preview_image(event)">
											</div>
										</div>
										
										<div class="card">
											<div class="card-header">
												<h4 class="card-title">Personal details</h4>
											</div>
											<div class="card-body">
												<div class="row">
													<div class="col-md-3">
														<div class="form-group">
															<label>Staff No</label>
															<input type="text" class="form-control"  value="" name="staffno" >
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label>First Name</label>
															<input type="text" class="form-control"  value="" name="fname" >
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label>Middle Name</label>
															<input type="text" class="form-control"  value="" name="mname" >
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label>Last Name</label>
															<input type="text" class="form-control"  value="" name="lname" >
														</div>
													</div>
												</div>
											</div>
										</div>

										<div class="card">
											<div class="card-header">
												<h4 class="card-title">Contact details</h4>
											</div>
											<div class="card-body">
												<div class="row">
													<div class="col-md-3">
														<div class="form-group">
															<label>Phone no</label>
															<input type="text" class="form-control"  value="" name="phoneno" >
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label>Email</label>
															<input type="text" class="form-control"  value="" name="email" >
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-group">
															<label>Address</label>
															<input type="text" class="form-control"  value="" name="address" >
														</div>
													</div>
												</div>
											</div>
										</div>
										
										<div class="card">
											<div class="card-header">
												<h4 class="card-title">Employment details</h4>
											</div>
											<div class="card-body">
												<div class="row">
													<div class="col-md-3">
														<div class="form-group">
															<label>Department</label>
															<select  class="form-control" name="department" id="department">
																<option value="" >-select-</option>
																@foreach($Department as $list)
																 <option value="{{ $list->id }}" >{{ $list->department}}</option>
																@endforeach
															</select>
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label>Grade</label>
															<select  class="form-control" name="grade" id="grade">
																@foreach($Grade as $list)
																 <option value="{{ $list->id }}" >{{ $list->grade}}</option>
																@endforeach
															</select>
														</div>
													</div>
												</div>
											</div>
										</div>
										
										<div class="card">
											<div class="card-header">
												<h4 class="card-title">Account details</h4>
											</div>
											<div class="card-body">
												<div class="row">
													<div class="col-md-4">
														<div class="form-group">
															<label>Bank</label>
															<select  class="form-control" name="bank" >
																<option value="" >-select-</option>
																@foreach($BankList as $list)
																 <option value="{{ $list->bankID }}" >{{ $list->bank}}</option>
																@endforeach
															</select>
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group">
															<label>Account Number</label>
															<input type="text" class="form-control"  value="" name="accountno" >
														</div>
													</div>
												</div>
											</div>
										</div>
										
										<div class="text-right">
											<button type="submit" class="btn btn-primary" name="submit">Submit</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
		
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
 
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
<script>

    function editfunc(id,cat)
    {
        document.getElementById('id').value = id;
        document.getElementById('category').value = cat;
        
        
        $("#edit_details").modal('show')
    }
    
   
    
    
    function preview_image(event) 
    { 
     var reader = new FileReader();
     reader.onload = function()
     {
      var output = document.getElementById('output_image');
      output.src = reader.result;
     }
     reader.readAsDataURL(event.target.files[0]);
    } 
             
</script>
@endsection
			<!-- /Page Wrapper -->
