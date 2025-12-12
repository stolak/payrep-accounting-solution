@extends('layouts.layout')
@section('pageTitle')
    Modification
@endsection

@section('pageHead')
    <div id="page-head">
        <div id="page-title">
            <h1 class="page-header text-overflow">Setup</h1>
        </div>
        <ol class="breadcrumb">
            <li><a href="/"><i class="demo-pli-home"></i></a></li>
            <li><a href="#">Staff Record Modification</a></li>
        </ol>
    </div>
@endsection
@section('content')
    <div class="boxed">
        <div id="page-content">
        <div class="panel">
            <div class="panel-body">
              @include('_partialView.nofication')
	        <div class="panel-footer text-left">
                <button class="btn btn-success" type="button" onclick="Addnew()">Add New</button>
            </div>
                <form method="post" enctype="multipart/form-data" name="mainform" id="mainform">
                    {{ csrf_field() }}
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-md-9 ">
                            </div>
                                        <div class="form-group col-md-3 float-right">
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
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3  class="panel-title">Personal details</h3>	
                            </div>
                            <div class="panel-body"> 
                            <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Staff No</label>
                                    @php if($staffno=='') $staffno= old('staffno') @endphp
                                    @php if($staffno=='') $staffno= $StaffProfile->staff_no @endphp
                                    <input type="text" class="form-control"   name="staffno" value="{{$staffno}}" >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">First Name</label>
                                    @php if($fname=='') $fname= old('fname') @endphp
                                    @php if($fname=='') $fname= $StaffProfile->first_name @endphp
                                    <input type="text" class="form-control"  value="{{$fname}}" name="fname" >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Middle Name</label>
                                    @php if($mname=='') $mname= old('mname') @endphp
                                    @php if($mname=='') $mname= $StaffProfile->middle_name @endphp
                                    <input type="text" class="form-control"  value="{{$mname}}" name="mname" >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Last Name</label>
                                    @php if($lname=='') $lname= old('lname') @endphp
                                    @php if($lname=='') $lname= $StaffProfile->last_name @endphp
                                    <input type="text" class="form-control"  value="{{$lname}}" name="lname" >
                                </div>
                            </div>
                        </div>
                            </div>
                        </div>

                        
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3  class="panel-title">Contact details</h3>	
                            </div>
                            <div class="panel-body"> 
                            <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Phone no</label>
                                    @php if($phoneno=='') $phoneno= old('phoneno') @endphp
                                    @php if($phoneno=='') $phoneno= $StaffProfile->phone_no @endphp
                                    <input type="text" class="form-control"  value="{{$phoneno}}" name="phoneno" >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Email</label>
                                    @php if($email=='') $email= old('email') @endphp
                                    @php if($email=='') $email= $StaffProfile->email @endphp
                                    <input type="text" class="form-control"  value="{{$email}}" name="email" >
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Address</label>
                                    @php if($address=='') $address= old('address') @endphp
                                    @php if($address=='') $address= $StaffProfile->address @endphp
                                    <input type="text" class="form-control"  value="{{$address}}" name="address" >
                                </div>
                            </div>
                        </div>
                            </div>
                        </div>
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3  class="panel-title">Employment details</h3>	
                            </div>
                            <div class="panel-body"> 
                            <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    
                                    @php if($department=='') $department= old('department') @endphp
                                    @php if($department=='') $department= $StaffProfile->department @endphp
                                    <label class="control-label">Department</label>
                                    <select  class="form-control" name="department" id="department">
                                        <option value="" >-select-</option>
                                        @foreach($Department as $list)
                                         <option value="{{ $list->id }}" {{ ($department == $list->id )? 'selected':''}}>{{ $list->department}}</option>
                                        @endforeach
                                   </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Grade</label>
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
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3  class="panel-title">Account details</h3>	
                            </div>
                            <div class="panel-body"> 
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            
                                            @php if($bank=='') $bank= old('bank') @endphp
                                            @php if($bank=='') $bank= $StaffProfile->bankid @endphp
                                            <label class="control-label">Bank</label>
                                            <select  class="form-control" name="bank" id="bank">
                                                <option value="" >-select-</option>
                                                @foreach($BankList as $list)
                                                 <option value="{{ $list->bankID }}" {{ ($bank == $list->bankID  ) ? 'selected':'' }}>{{ $list->bank}}</option>
                                                @endforeach
                                           </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">Account Number</label>
                                            @php if($accountno=='') $accountno= old('accountno') @endphp
                                            @php if($accountno=='') $accountno= $StaffProfile->account_no @endphp
                                            <input type="text" class="form-control"  value="{{$accountno}}" name="accountno" >
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-left">
                            <button class="btn btn-success" type="submit" name="update">Update</button>
                        </div>
                    </div>
                </form>
            
                <!-- End Inline Form  -->
            
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
@stop
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



  
@stop
