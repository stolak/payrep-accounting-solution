@extends('layouts.layout')
@section('pageTitle')
    Payroll Summary
@endsection

@section('pageHead')
    <div id="page-head">
        <div id="page-title">
            <h1 class="page-header text-overflow">Report</h1>
        </div>
        <ol class="breadcrumb">
            <li><a href="/"><i class="demo-pli-home"></i></a></li>
            <li><a href="#">Payroll Report</a></li>
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
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <button class="btn btn-success" type="submit" name="view">View</button>
                                </div>
                            </div>
                        </div>
                       </div>
                </form>
                <h3>Payroll Report</h3>
            <div class="table-responsive" style="font-size: 11px; padding:10px;">
                <table id="mytable" class="table table-bordered table-striped table-highlight">
		        <thead>
		          <tr bgcolor="#c7c7c7">
		          
		            <th>S/N</th>
		            <th>Staff No</th>
		            <th>Names</th>
		            <th>Grade</th>
		            @foreach($EarningVariable as $list)
		            <th>{{$list->variable}}</th>
		             @endforeach
		             <th>Gross Pay</th>
		             @foreach($NonTaxableEarning as $list)
		            <th>{{$list->variable}}</th>
		             @endforeach
		             <th>Total Earning</th>
		              @foreach($DeductionVariable as $list)
		            <th>{{$list->variable}}</th>
		             @endforeach
		             <th>Gross Deduction</th>
		             <th>Net pay</th>
		          </tr>
		        </thead>
		               
		        <tbody>
		        
		          @php
		          $i=1;
		          $grossearn=0;
		          $totalearn=0;
		          $grossdeduction=0;
		          $net=0;
		          @endphp
		           
		            @foreach($Payroll as $list2)
		               <tr>
		               <td>{{ $i++ }} </td>
		               <td>{{ $list2->staff_no}} </td>
		               <td>{{ $list2->fullname}} </td>
		               <td>{{ $list2->grades}} </td>
		               @php $subgross=0; @endphp
		               @php $otherearn =0; @endphp
		               @php $subnet =0; @endphp
		                @foreach($EarningVariable as $list)
    		                @php $para=$list->ref_code; @endphp
    		                @php $subgross +=$list2->$para; @endphp
    		                <td>{{number_format($list2->$para,2, '.', ',')}}</td>
    		          @endforeach
    		          <td>{{ number_format($subgross,2, '.', ',')}} </td>
    		          @foreach($NonTaxableEarning as $list)
    		                @php $para=$list->ref_code; @endphp
    		                @php $otherearn +=$list2->$para; @endphp
    		                <td>{{number_format($list2->$para,2, '.', ',')}}</td>
    		          @endforeach
    		          <td>{{ number_format($otherearn+$subgross,2, '.', ',')}}</td>
    		          @php $subdeduction=0; @endphp
    		          @foreach($DeductionVariable as $list)
    		                 @php $para=$list->ref_code; @endphp
    		                 @php $subdeduction +=$list2->$para; @endphp
    		                 <td>@if( $list2->$para<0)({{number_format(abs($list2->$para),2, '.', ',')}})@else {{number_format(abs($list2->$para),2, '.', ',')}} @endif</td>
    		          @endforeach
		                   <td>@if( $subdeduction<0)({{ number_format(abs($subdeduction),2, '.', ',')}}) @else {{ number_format(abs($subdeduction),2, '.', ',')}} @endif</td>
		                   @php $subnet +=$subdeduction+$subgross+$otherearn; @endphp
		                   <td>{{ number_format($subnet,2, '.', ',')}} </td>
		               </tr>
		                @php
        		          $grossearn+=$subgross;
        		           $totalearn+=$subgross+$otherearn;
        		          $grossdeduction+=$subdeduction;
        		          $net+=$subnet;
		                @endphp
		            @endforeach
		            <tr>
		               <td colspan=4>Total</td>
		                @foreach($EarningVariable as $list)
		                @php $para=$list->ref_code; @endphp
    		                <td>@if( $MonthlyActiveVariable->$para<0)({{number_format(abs($MonthlyActiveVariable->$para),2, '.', ',')}}) @else {{number_format(abs($MonthlyActiveVariable->$para),2, '.', ',')}} @endif</td>
    		          @endforeach
    		          <td>{{ number_format($grossearn,2, '.', ',')}} </td>
    		          @foreach($NonTaxableEarning as $list)
		                @php $para=$list->ref_code; @endphp
    		                <td>@if( $MonthlyActiveVariable->$para<0)({{number_format(abs($MonthlyActiveVariable->$para),2, '.', ',')}}) @else {{number_format(abs($MonthlyActiveVariable->$para),2, '.', ',')}} @endif</td>
    		          @endforeach
    		          <td>{{ number_format($totalearn,2, '.', ',')}} </td>
    		          @foreach($DeductionVariable as $list)
    		                  @php $para=$list->ref_code; @endphp
    		                <td>@if( $MonthlyActiveVariable->$para<0)({{number_format(abs($MonthlyActiveVariable->$para),2, '.', ',')}}) @else {{number_format(abs($MonthlyActiveVariable->$para),2, '.', ',')}} @endif</td>
    		          @endforeach
		                   <td>@if( $grossdeduction<0)({{ number_format(abs($grossdeduction),2, '.', ',')}}) @else {{ number_format(abs($grossdeduction),2, '.', ',')}} @endif </td>
		                   <td>@if( $net<0)({{ number_format(abs($net),2, '.', ',')}}) @else {{ number_format(abs($net),2, '.', ',')}}  @endif</td>
		               
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
