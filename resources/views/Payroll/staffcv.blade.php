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
	        
                <form method="post" id="mainform" name="mainform">
                    {{ csrf_field() }}
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label class="control-label">Staff Names</label>
                                    
                                        <select class="select_picker form-control" id="staffid" data-live-search="true" name="staffid" onchange="Reload();">
                                     <option value="">--Select--</option>
                                        @foreach($Staffs as $list)
                                        <option value="{{ $list->id }}" {{ (old('staffid') == $list->id ||($staffid) == $list->id  ) ? 'selected':'' }}>{{ $list->first_name }} {{ $list->middle_name }} {{ $list->last_name }}</option>
                                        @endforeach
                                   </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Variable type</label>
                                    <select  class="form-control" name="variabletype" onchange="Reload();">
                                     <option value="">--Select--</option>
                                          @foreach($VariableType as $list)
                                     <option value="{{ $list->id }}" {{ (old('variabletype') == $list->id ||($variabletype) == $list->id  ) ? 'selected':'' }}>{{ $list->particular }}</option>
                                          @endforeach
                                   </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">Contral Variable</label>
                                    <select  class="form-control" name="variable" >
                                     <option value="">--Select--</option>
                                    @foreach($PayrollVariable as $list)
                                     <option value="{{ $list->id }}" {{ (old('variable') == $list->id ||($variable) == $list->id  ) ? 'selected':'' }}>{{ $list->variable }}</option>
                                    @endforeach
                                   </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Monthly Amount</label>
                                    <?php if($amount=='') $amount= old('amount'); ?>
                                    <input type="text" class="form-control"  value="{{$amount}}" name="amount" id="amount" >
                                </div>
                            </div>
                             <?php if($continuity=='') $continuity= old('continuity'); ?>
                            <div class="col-sm-2">
                                <label class="control-label">Continuity</label>
                                <br>
                              <label>
                                <input type="checkbox" data-toggle="toggle" data-on="Target Apply" data-off="No Limit" name="continuity" data-width="120" id="continuity" {{($continuity=='on')? 'checked':''}} >
                              </label>
                            </div>
                            <div id="target-content">
                                @if($continuity=='on')
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Target Amount</label>
                                    <?php if($targetamount=='') $targetamount= old('targetamount'); ?>
                                    <input type="text" class="form-control"  value="{{$targetamount}}" name="targetamount" id="targetamount" >
                                </div>
                            </div>
                            @endif
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
		            <th>Amount</th>
		            <th>Target</th>
		            <th>Last Processed</th>
		            <th>Action</th>
		          </tr>
		        </thead>
		               
		        <tbody>
		        
		          @php
		          $i=1;
		          $id='id';
		          @endphp
		           
		            @foreach($StaffVariable as $list)
		                           
		               <tr>
		               <td>{{ $i++ }} </td>
		               <td> {{$list->particular}}</td>
		               <td> {{$list->variables}}</td>
		               <td> {{number_format(abs($list->amount_monthly),2, '.', ',')}}</td>
		               <td>@if($list->is_continous==1)  Not Applicable @else{{number_format(abs($list->amount_target),2, '.', ',')}} @endif</td>
		               <td>
		               
		               </td>
		              <td>
		               <a onclick="deletefunc('{{$list->id}}','{{$list->variables}}')" class="btn btn-danger glyphicon glyphicon-remove btn-xs"></a>
		               </td>
		               </tr>
		            @endforeach
		            </tbody>
		      </table>
		     </div>
            </div>
        </div>
   
     <div id="deleteModal" class="modal fade" >
        <div class="modal-dialog box box-default" role="document" style="color:black;font-size:24px;">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Staff Variable</h4>
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
   
<form method="post"  id="noform" name="noform">
{{ csrf_field() }}
 <input type="hidden" class="form-control" id="noid" name="id" value="">

</form>
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
@stop
@section('scripts')

<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>
$('.select_picker').selectpicker({
          style: 'btn-default',
          size: 4
        });
    function editfunc(id,ocode,desc,account)
    {
        document.getElementById('id').value = id;
        document.getElementById('e_o_code').value = ocode;
        document.getElementById('e_description').value = desc;
        document.getElementById('e_account').value = account;
        $("#editModal").modal('show')
    }
   
    function deletefunc(id,item)
    {
        document.getElementById('deleteid').value = id;
        document.getElementById('content5').innerHTML = item;
                     
        $("#deleteModal").modal('show')
    }
   function  Reload()
        {	
        document.forms["mainform"].submit();
        return;
        }
    $(function() {
    $('#continuity').change(function() {
       document.getElementById('target-content').innerHTML ='';
       if ($(this).prop('checked')==true){
           document.getElementById('target-content').innerHTML ='<div class="col-sm-3"><div class="form-group"><label class="control-label">Target Amount</label><input type="text" class="form-control"  value="" name="targetamount" id="targetamount" ></div></div>';
            document.getElementById('targetamount').value = document.getElementById('amount').value ;
           
       }
    })
  })
</script>



  
@stop
