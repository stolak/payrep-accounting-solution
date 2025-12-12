<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Modification
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
									<li class="breadcrumb-item active">Staff Record Modification</li>
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
									<h4 class="card-title">Staff Record Modification</h4>
									<div class="text-right">
										<button class="btn btn-primary" type="button" onclick="Addnew()">Add New</button>
									</div>
								</div>
								<div class="card-body">
									<form method="post" enctype="multipart/form-data" name="mainform" id="mainform">
                                    {{ csrf_field() }}
										<div class="row">
										    <div class="col-md-9">
										    </div>
										    <div class="col-md-3">
											    <div class="form-group">
													<select class="select_picker form-control" id="staffid" data-live-search="true" name="staffid" onchange="Reload();">
														<option value="">--Select--</option>
														@foreach($Staffs as $list)
														<option value="{{ $list->id }}" {{ (old('staffid') == $list->id ||($staffid) == $list->id  ) ? 'selected':'' }}>{{ $list->first_name }} {{ $list->middle_name }} {{ $list->last_name }}</option>
														@endforeach
													</select>
												</div>
												<div style="width: 150px;height:150px;" class="float-right" >
													<img src="{{$StaffProfile->img!=''?'/img/passport/'.$StaffProfile->img :'/img/profile_img.png'}}" alt="profile image" id="output_image" style="max-width: 100%;max-height: 100%;">
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
															@php if($staffno=='') $staffno= old('staffno') @endphp
															@php if($staffno=='') $staffno= $StaffProfile->staff_no @endphp
															<input type="text" class="form-control"   name="staffno" value="{{$staffno}}" >
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label>First Name</label>
															@php if($fname=='') $fname= old('fname') @endphp
															@php if($fname=='') $fname= $StaffProfile->first_name @endphp
															<input type="text" class="form-control"  value="{{$fname}}" name="fname" >
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label>Middle Name</label>
															@php if($mname=='') $mname= old('mname') @endphp
															@php if($mname=='') $mname= $StaffProfile->middle_name @endphp
															<input type="text" class="form-control"  value="{{$mname}}" name="mname" >
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label>Last Name</label>
															@php if($lname=='') $lname= old('lname') @endphp
															@php if($lname=='') $lname= $StaffProfile->last_name @endphp
															<input type="text" class="form-control"  value="{{$lname}}" name="lname" >
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
															@php if($phoneno=='') $phoneno= old('phoneno') @endphp
															@php if($phoneno=='') $phoneno= $StaffProfile->phone_no @endphp
															<input type="text" class="form-control"  value="{{$phoneno}}" name="phoneno" >
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label>Email</label>
															@php if($email=='') $email= old('email') @endphp
															@php if($email=='') $email= $StaffProfile->email @endphp
															<input type="text" class="form-control"  value="{{$email}}" name="email" >
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-group">
															<label>Address</label>
															@php if($address=='') $address= old('address') @endphp
															@php if($address=='') $address= $StaffProfile->address @endphp
															<input type="text" class="form-control"  value="{{$address}}" name="address" >
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
															@php if($department=='') $department= old('department') @endphp
															@php if($department=='') $department= $StaffProfile->department @endphp
															<label>Department</label>
															<select  class="form-control" name="department" id="department">
																<option value="" >-select-</option>
																@foreach($Department as $list)
																 <option value="{{ $list->id }}" {{ ($department == $list->id )? 'selected':''}}>{{ $list->department}}</option>
																@endforeach
															</select>
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label>Grade</label>
															@php if($grade=='') $grade= old('grade') @endphp
															@php if($grade=='') $grade= $StaffProfile->grade @endphp
															<select  class="form-control" name="grade" id="grade">
																@foreach($Grade as $list)
																 <option value="{{ $list->id }}" {{ ($grade == $list->id  ) ? 'selected':''}}>{{ $list->grade}}</option>
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
															@php if($bank=='') $bank= old('bank') @endphp
															@php if($bank=='') $bank= $StaffProfile->bankid @endphp
															<label>Bank</label>
															<select  class="form-control" name="bank" id="bank">
																<option value="" >-select-</option>
																@foreach($BankList as $list)
																 <option value="{{ $list->bankID }}" {{ ($bank == $list->bankID  ) ? 'selected':'' }}>{{ $list->bank}}</option>
																@endforeach
															</select>
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group">
															<label>Account Number</label>
															@php if($accountno=='') $accountno= old('accountno') @endphp
															@php if($accountno=='') $accountno= $StaffProfile->account_no @endphp
															<input type="text" class="form-control"  value="{{$accountno}}" name="accountno" >
														</div>
													</div>
												</div>
											</div>
										</div>
										
										<div class="text-right">
											<button type="submit" class="btn btn-primary" name="update">Update</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
		
			</div>

<form method="post"  id="newform" name="newform" action="/staff-registration">
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

    
    function Addnew()
    {
                     
       document.forms["newform"].submit();
    }
   
    $('.select_picker').selectpicker({
          style: 'btn-default',
          size: 4
        });
    
    function SelectInventory(id)
    {
        document.getElementById('noid').value = id;
       document.forms["noform"].submit();
    }
     function  Reload()
        {	
        document.forms["mainform"].submit();
        return;
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
