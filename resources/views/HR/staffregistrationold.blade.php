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
	        <div class="panel-footer text-left">
                <button class="btn btn-success" type="button" onclick="Addnew()">Add New</button>
            </div>
                <form method="post">
                    {{ csrf_field() }}
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
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">Address</label>
                                    <input type="text" class="form-control"  value="" name="address" >
                                </div>
                            </div>
                        </div>
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
                        <div class="panel-footer text-left">
                            <button class="btn btn-success" type="submit" name="submit">Update</button>
                        </div>
                    </div>
                </form>
            
             <!-- end display selected inventory detail if exist-->
                <!--===================================================-->
                <!-- End Inline Form  -->
            <div class="table-responsive" style="font-size: 11px; padding:10px;">
                <table id="mytable" class="table table-bordered table-striped table-highlight">
		        <thead>
		          <tr bgcolor="#c7c7c7">
		            <th>S/N</th>
		            <th>Staff No</th>
		            <th>Full Name</th>
		            <th>Department</th>
		            <th>Grade</th>
		            <th></th>
		          </tr>
		        </thead>
		               
		        <tbody>
		        
		          @php
		          $i=1;
		          @endphp
		           
		            @foreach($Staffs as $list)
		                           
		               <tr>
		               <td>{{ $i++ }} </td>
		               <td> {{$list->staff_no}}</td>
		               <td> {{$list->first_name}} {{$list->middle_name}} {{$list->last_name}}</td>
		               <td> {{$list->department}}</td>
		               <td> {{$list->grade}}</td>
		               <td>
		               
		               </td>
		              
		               </tr>
		            @endforeach
		            </tbody>
		      </table>
		     </div>
            </div>
        </div>
    <div id="editModal" class="modal fade" >
        <div class="modal-dialog box box-default" role="document" style="color:black;">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Modifiy Selected Inventory</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form class="form-horizontal"  method="post"  role="form">
                    {{ csrf_field() }}
            <div class="modal-body">  
                <div class="form-group" style="margin: 0 10px;">
                    <input type="hidden" class="form-control" id="invid" name="id">
                    <div class="col-sm-12">
			            <div class="form-group">
                              <label class="control-label">Brand</label>
                                    <select  class="form-control" name="brand" id="brand" >
                                     <option value="">--Select--</option>
                                    
                                   </select>
                        </div>
                    </div>
                    <div class="col-sm-12">
			            <div class="form-group">
                              <label class="control-label">Item Category</label>
                                    <select  class="form-control" name="category" id="category" >
                                     <option value="">--Select--</option>
                                   
                                   </select>
                        </div>
                    </div>
                    <div class="col-sm-12">
			            <div class="form-group">
                             <label class="control-label">Item Description</label>
                            
                            <input type="text" class="form-control"  required name="item" id="item">
                        </div>
                    </div>
                    <div class="col-sm-12">
			            <div class="form-group">
                            <label class="control-label">Min. SKU</label>
                            <select  class="form-control" name="mskuformat"  id="mskuformat">
                             <option value="">--Select--</option>
                           
                           </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
			            <div class="form-group">
			                <label class="control-label">Tax</label>
                            <select  class="form-control" name="taxperc" id="taxperc">
                            
                            
                           </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
			            <div class="form-group">
			                <label class="control-label">Has Color?</label>
                            <select  class="form-control" name="hcalor" id="hcalor">
                            
                            
                           </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">Has Size?</label>
                            <select  class="form-control" name="hsize" id="hsize">
                             
                            
                           </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            
                             <label class="control-label">Has Serial Number?</label>
                            <select  class="form-control" name="hserialno" id="hserialno">
                            
                            
                           </select>
                        </div>
                    </div>
                </div>
            </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" name="modinventory">Create</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            
                </form>
            </div>
            
          </div>
        </div>
       
        
        
       

     <div id="deleteModal" class="modal fade" >
        <div class="modal-dialog box box-default" role="document" style="color:black;font-size:24px;">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Delete Inventory Item</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form class="form-horizontal"  method="post"  role="form">
                    {{ csrf_field() }}
            <div class="modal-body">  
                <div class="form-group" style="margin: 0 10px;">
                    <input type="hidden" class="form-control" id="deleteid" name="deleteid" value="">
                    <div class="col-sm-12">
                        <center><h1 style="color:black;">Are you sure <div id="content5"></div>?</h1></center>
                        
                    </div>
                </div>
            </div>
                <div class="modal-footer">
                    <button type="submit" name="delinv" class="btn btn-success">Yes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                </div>
                </form>
            </div>
            
          </div>
    </div>
    </div>
        <!--/// content end here -->
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
                     
        $("#deleteModal").modal('show')
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
                     
        $("#newModal").modal('show')
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



  
@stop
