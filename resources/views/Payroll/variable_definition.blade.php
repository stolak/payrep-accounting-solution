@extends('layouts.layout')
@section('pageTitle')
    Payroll Variable
@endsection

@section('pageHead')
    <div id="page-head">
        <div id="page-title">
            <h1 class="page-header text-overflow">Setup</h1>
        </div>
        <ol class="breadcrumb">
            <li><a href="/"><i class="demo-pli-home"></i></a></li>
            <li><a href="#">Payroll Variable</a></li>
        </ol>
    </div>
@endsection
@section('content')
    <div class="boxed">
        <div id="page-content">
        <div class="panel">
            <div class="panel-body">
              @include('_partialView.nofication')
	        
                <form method="post">
                    {{ csrf_field() }}
                    <div class="panel-body">
                        <div class="row"> 
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <?php if($variabletype=='') $variabletype= old('variabletype'); ?>
                                    <label class="control-label">Variable type</label>
                                    <select  class="form-control" name="variabletype" id="variabletype" onchange="VariableTypeChange()">
                                     <option value="">--Select--</option>
                                          @foreach($VariableType as $list)
                                     <option value="{{ $list->id }}" {{ ($variabletype) == $list->id   ? 'selected':'' }}>{{ $list->particular }}</option>
                                          @endforeach
                                   </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Variable</label>
                                    <input type="text" class="form-control"  value="" name="variable" >
                                </div>
                            </div>
                            
                            <div class="col-sm-2">
                                <label class="control-label">Statutory?</label>
                                <br>
                              <label>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="statutory">
                              </label>
                            </div>
                            <div id="taxable-content">
                            @if($variabletype==1)
                            <div class="col-sm-2">
                                <label class="control-label">Taxable?</label>
                                <br>
                              <label>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="taxable" id="taxable" >
                              </label>
                            </div>
                            @endif
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="control-label">Rank</label>
                                    <select  class="form-control" name="rank" >
                                     <option value="">--Select--</option>
                                          @for($i = 0; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ (old('rank') == $i ||($rank) == $i  ) ? 'selected':'' }}>{{$i}}</option>
                                          @endfor
                                   </select>
                                </div>
                            </div>
                            
                        </div>
                        <div class="panel-footer text-left">
                            <button class="btn btn-success" type="submit" name="addnew">Add New</button>
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
		            <th>Earning/deduction</th>
		            <th>Payroll Variable</th>
		            <th>Statutory</th>
		            <th>Is Taxable</th>
		            <th>Ordering Rank</th>
		            <th>Status</th>
		            <th>Action</th>
		            
		          </tr>
		        </thead>
		               
		        <tbody>
		        
		          @php
		          $i=1;
		          $id='id';
		          @endphp
		           
		            @foreach($PayrollVariable as $list)
		                           
		               <tr>
		               <td>{{ $i++ }} </td>
		               <td> {{$list->variabletype}}</td>
		               <td> {{$list->variable}}</td>
		               <td> {{$list->statutorys}}</td>
		               <td> {{$list->istaxables}}</td>
		               <td> {{$list->rank}}</td>
		               <td> {{$list->variablestatus}}</td>
		               <td>
		               <a onclick="editfunc('{{$list->id}}','{{$list->variabletype}}','{{$list->variable}}','{{$list->statutory}}','{{$list->istaxable}}','{{$list->status}}','{{$list->rank}}','{{$list->variable_type}}')" class="btn btn-success  glyphicon glyphicon-edit btn-xs"></a>&nbsp;
		               <a onclick="deletefunc('{{$list->id}}','{{$list->variabletype}}')" class="btn btn-danger glyphicon glyphicon-remove btn-xs"></a>
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
              <h4 class="modal-title">Variable Modification</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form class="form-horizontal"  method="post"  role="form">
                    {{ csrf_field() }}
            <div class="modal-body">  
                <div class="form-group" style="margin: 0 10px;">
                    <input type="hidden" class="form-control" id="e_id" name="id">
                    <div class="col-sm-12">
			            <div class="form-group">
                              <label class="control-label">Variable Type</label>
                              <input type="text" class="form-control"  id="e_v_type" readonly>
                        </div>
                    </div>
                    
                    <div class="col-sm-12">
			            <div class="form-group">
                             <label class="control-label">Variable Description</label>
                            <input type="text" class="form-control"  required name="variable" id="e_variable">
                        </div>
                    </div>
                    <div class="col-sm-3">
			            <div class="form-group">
			                <label class="control-label">Statutory?</label>
                                <br>
                              <label>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="statutory" id="e_statutory">
                              </label>
                        </div>
                    </div>
                    <div id="e_content">
                    <div class="col-sm-3">
			            <div class="form-group">
			                <label class="control-label">Taxable?</label>
                                <br>
                              <label>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="taxable" id="e_taxable">
                              </label>
                        </div>
                    </div>
                    </div>
                    <div class="col-sm-4">
			            <div class="form-group">
			                <label class="control-label">Status</label>
                                <br>
                              <label>
                                <input type="checkbox" data-toggle="toggle" data-on="Active" data-off="Disable" name="status" id="e_status" class="form-control" data-width="100">
                              </label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="control-label">Rank</label>
                            <select  class="form-control" name="rank" id="e_rank" >
                                  @for($i = 0; $i <= 10; $i++)
                                    <option value="{{ $i }}" >{{$i}}</option>
                                  @endfor
                           </select>
                        </div>
                    </div>
                </div>
            </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" name="update">Update</button>
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
              <h4 class="modal-title">Delect Control Variable</h4>
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
                    <button type="submit" name="del" class="btn btn-success">Yes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                </div>
                </form>
            </div>
            
          </div>
    </div>
    </div>
        <!--/// content end here -->
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

<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>

    function editfunyyyc(id,cat)
    {
        document.getElementById('id').value = id;
        document.getElementById('category').value = cat;
        
        
        $("#editModal").modal('show')
    }
    
    function editfunc(id,vtype,variable,statutory,taxable,status,rank,variable_type)
    {
        document.getElementById('e_id').value = id;
          document.getElementById('e_v_type').value = vtype;
          document.getElementById('e_variable').value = variable;
          
          $('#e_statutory').bootstrapToggle('off');
          $('#e_status').bootstrapToggle('off');
          if(statutory==1)$('#e_statutory').bootstrapToggle('on');
          if(status==1)$('#e_status').bootstrapToggle('on');
            document.getElementById('e_rank').value = rank;
            document.getElementById('e_content').innerHTML ='';
            if(variable_type==1){
            document.getElementById('e_content').innerHTML ='<div class="col-sm-3"><div class="form-group"><label class="control-label">Taxable?</label><br><label><input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="taxable" id="e_taxable"></label></div></div>';
            $('#e_taxable').bootstrapToggle({
              on: 'Yes',
              off: 'No'
            });
            $('#e_taxable').bootstrapToggle('off');
            if(taxable==1)$('#e_taxable').bootstrapToggle('on');
            }
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
          document.getElementById('taxable-content').innerHTML = '<div class="col-sm-2"><label class="control-label">Taxable?</label><br><label><input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="taxable" id="taxable" ></label></div>';   
            $('#taxable').bootstrapToggle({
              on: 'Yes',
              off: 'No'
            });
        }else{
          document.getElementById('taxable-content').innerHTML='';  
        }
       //document.forms["noform"].submit();
    }
             
</script>



  
@stop
