@extends('layouts.layout')
@section('pageTitle')
    Salary Chart
@endsection

@section('pageHead')
    <div id="page-head">
        <div id="page-title">
            <h1 class="page-header text-overflow">Setup</h1>
        </div>
        <ol class="breadcrumb">
            <li><a href="/"><i class="demo-pli-home"></i></a></li>
            <li><a href="#">Salary Chart</a></li>
        </ol>
    </div>
@endsection
@section('content')
    <div class="boxed">
        <div id="page-content">
        <div class="panel">
            <div class="panel-body">
              @include('_partialView.nofication')
	        
                <form method="post" name="mainform" id="mainform">
                    {{ csrf_field() }}
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Grade Level</label>
                                    <select  class="form-control" name="grade" id="grade" onchange="Reload()">
                                     <option value="">--Select--</option>
                                          @foreach($Grade as $list)
                                     <option value="{{ $list->id }}" {{ (old('grade') == $list->id ||($grade) == $list->id  ) ? 'selected':'' }}>{{ $list->grade }}</option>
                                          @endforeach
                                   </select>
                                </div>
                            </div>
                        </div>
                        <h3>Earning</h3>
                        <div class="row">
                            @foreach($EarningVariable as $list)
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="control-label">{{$list->variable}}</label>
                                    @php $para=$list->ref_code; @endphp
                                    <input type="number" step="0.01" required class="form-control"  value="{{$GradeChart? $GradeChart->$para : 0}}" name="{{$list->ref_code}}" >
                                </div>
                            </div>
                             @endforeach
                        </div>
                        <h3>Deduction</h3>
                        <div class="row">
                            @foreach($DeductionVariable as $list)
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="control-label">{{$list->variable}}</label>
                                   @php $para=$list->ref_code; @endphp
                                    <input type="number" step="0.01" required class="form-control"  value="{{$GradeChart? $GradeChart->$para : 0}}" name="{{$list->ref_code}}" >
                                </div>
                            </div>
                             @endforeach
                        </div>
                       
                        <div class="panel-footer text-left">
                            <button class="btn btn-success" type="submit" name="update">Update</button>
                        </div>
                        
                        
                    </div>
                </form>
                
            <div class="table-responsive" style="font-size: 11px; padding:10px;">
                <table id="mytable" class="table table-bordered table-striped table-highlight">
		        <thead>
		          <tr bgcolor="#c7c7c7">
		          
		            <th>S/N</th>
		            <th>Grade</th>
		            @foreach($EarningVariable as $list)
		            <th>{{$list->variable}}</th>
		             @endforeach
		              @foreach($DeductionVariable as $list)
		            <th>{{$list->variable}}</th>
		             @endforeach
		             <th>Action</th>
		          </tr>
		        </thead>
		               
		        <tbody>
		        
		          @php
		          $i=1;
		          @endphp
		           
		            @foreach($SalaryChart as $list2)
		                           
		               <tr>
		               <td>{{ $i++ }} </td>
		               <td>{{ $list2->grades}} </td>
		                @foreach($EarningVariable as $list)
    		                @php $para=$list->ref_code; @endphp
    		                <td>{{number_format($list2->$para,2, '.', ',')}}</td>
    		          @endforeach
    		          @foreach($DeductionVariable as $list)
    		                 @php $para=$list->ref_code; @endphp
    		                 <td>({{number_format($list2->$para,2, '.', ',')}})</td>
    		          @endforeach
		               <td>
		                   <a onclick="ReloadWithPara('{{$list2->grade}}')" class="btn btn-success  glyphicon glyphicon-edit btn-xs"></a>&nbsp;
		               
		               </td>
		              
		               </tr>
		            @endforeach
		            </tbody>
		                   
		      </table>
		     </div>
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
@stop
@section('scripts')

<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
<script>
function Reload()
    {
       document.forms["mainform"].submit();
    }
function ReloadWithPara(id)
{
    document.getElementById('grade').value = id;
   document.forms["mainform"].submit();
}
</script>



  
@stop
