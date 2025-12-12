@extends('layouts.layout')
@section('pageTitle')
    Payroll Mandate
@endsection

@section('pageHead')
    <div id="page-head">
        <div id="page-title">
            <h1 class="page-header text-overflow">Report</h1>
        </div>
        <ol class="breadcrumb">
            <li><a href="/"><i class="demo-pli-home"></i></a></li>
            <li><a href="#">Payment Mandate</a></li>
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
                                    <label class="control-label">Year</label>
                                    <select  class="form-control" name="year" onchange="Reload()">
                                     <option value="">--Select--</option>
                                         <?php $curyr= date("Y"); ?>
                                        @for ($i = 2017; $i <= $curyr +1; $i++)
                        				<option value="{{ $i }}" {{(old('year') == $i ||($year) == $i) ? "selected" : ""}}>{{ $i }}</option>
                    				    @endfor
                                   </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Month</label>
                                    <select  class="form-control" name="month" onchange="Reload()">
                                     <option value="">--Select--</option>
                                          @foreach($Months as $list)
                                     <option value="{{ $list->id }}" {{ (old('month') == $list->id ||($month) == $list->id  ) ? 'selected':'' }}>{{ $list->month }}</option>
                                          @endforeach
                                   </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">Particular</label>
                                    <select  class="form-control" name="variable" >
                                     <option value="">--Select--</option>
                                    @foreach($PayrollVariable as $list)
                                     <option value="{{ $list->id }}" {{ (old('variable') == $list->id ||($variable) == $list->id  ) ? 'selected':'' }}>{{ $list->variable }}</option>
                                    @endforeach
                                   </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <button class="btn btn-success" type="submit" name="view">View</button>
                                </div>
                            </div>
                        </div>
                       </div>
                </form>
                <h3>Salary Mandate</h3>
            <div class="table-responsive" style="font-size: 11px; padding:10px;">
                <table id="mytable" class="table table-bordered table-striped table-highlight">
		        <thead>
		          <tr bgcolor="#c7c7c7">
		          
		            <th>S/N</th>
		            <th>Beneficiary</th>
		            <th>Amount</th>
		            <th>Bank</th>
		            <th>Account Number</th>
		            <th>Payment description</th>
		          </tr>
		        </thead>
		        <tbody>
		          @php $i=1; $net=0; @endphp
		            @foreach($NetpaySummary as $list2)
		               <tr>
		               <td>{{ $i++ }} </td>
		               <td>{{ $list2->fullname}} </td>
		               <td>{{ number_format(abs($list2->Net),2, '.', ',')}} </td>
		              <td>{{ $list2->bank}} </td>
		               <td>{{ $list2->account_no}} </td>
		               <td><code>-payment description-</code></td>
		               </tr>
		                @php $net+=$list2->Net; @endphp
		            @endforeach
		            <tr>
		               <td colspan=2>Total</td>
		                <td>@if( $net<0)({{ number_format(abs($net),2, '.', ',')}}) @else {{ number_format(abs($net),2, '.', ',')}}  @endif</td>
		                    <td colspan=3></td>
		               </tr>
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
</script>



  
@stop
