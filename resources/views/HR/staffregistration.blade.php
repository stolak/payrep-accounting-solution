@extends('layouts.layout')
@section('pageTitle')
    Registration
@endsection

@section('pageHead')
    <div id="page-head">
        <div id="page-title">
            <h1 class="page-header text-overflow">Setup</h1>
        </div>
        <ol class="breadcrumb">
            <li><a href="/"><i class="demo-pli-home"></i></a></li>
            <li><a href="#">Staff Registration</a></li>
        </ol>
    </div>
@endsection
@section('content')
    <div class="boxed">
        <div id="page-content">
        <div class="panel">
            <div class="panel-body">
              @include('_partialView.nofication')
                <form method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    
                    <div class="panel-body">
                        
                        <div class="row">
                            <div class="form-group col-md-9 ">
                            </div>
                                      <div class="form-group col-md-3 float-right">
                                          <div style="width: 150px;height:150px;" class="float-right" >
                                            <img src="{{'/img/profile_img.png'}}" alt="profile image" id="output_image" style="max-width: 100%;max-height: 100%;">
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
                                    <input type="text" class="form-control"  value="" name="staffno" >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">First Name</label>
                                    <input type="text" class="form-control"  value="" name="fname" >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Middle Name</label>
                                    <input type="text" class="form-control"  value="" name="mname" >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Last Name</label>
                                    <input type="text" class="form-control"  value="" name="lname" >
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
                                    <input type="text" class="form-control"  value="" name="phoneno" >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Email</label>
                                    <input type="text" class="form-control"  value="" name="email" >
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Address</label>
                                    <input type="text" class="form-control"  value="" name="address" >
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
                                            <label class="control-label">Department</label>
                                            <select  class="form-control" name="department" id="department">
                                                <option value="" >-select-</option>
                                                @foreach($Department as $list)
                                                 <option value="{{ $list->id }}" >{{ $list->department}}</option>
                                                @endforeach
                                           </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="control-label">Grade</label>
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
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3  class="panel-title">Account details</h3>	
                            </div>
                            <div class="panel-body"> 
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">Bank</label>
                                            <select  class="form-control" name="bank" >
                                                <option value="" >-select-</option>
                                                @foreach($BankList as $list)
                                                 <option value="{{ $list->bankID }}" >{{ $list->bank}}</option>
                                                @endforeach
                                           </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">Account Number</label>
                                            <input type="text" class="form-control"  value="" name="accountno" >
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-left">
                            <button class="btn btn-success" type="submit" name="submit">Submit</button>
                        </div>
                    </div>
                </form>
            
                <!-- End Inline Form  -->
            
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
@stop
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
        
        
        $("#editModal").modal('show')
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
