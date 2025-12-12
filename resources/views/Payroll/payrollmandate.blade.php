<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Payroll Mandate
@endsection
@section('content')
            <div class="page-wrapper">
				<div class="content container-fluid">
					<!-- Page Header -->
					<div class="page-header">
						<div class="row">
							<div class="col">
								<h3 class="page-title">Report</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><a href="/">Home</a></li>
									<li class="breadcrumb-item active">Payment Mandate</li>
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
									<h4 class="card-title">Payment Mandate</h4>
								</div>
								<div class="card-body">
									<form method="post" name="mainform" id="mainform">
                                    {{ csrf_field() }}
										<div class="row">
										    <div class="col-md-3">
											    <div class="form-group">
													<label>Year</label>
													<select  class="form-control" name="year" onchange="Reload()">
														<option value="">--Select--</option>
														<?php $curyr= date("Y"); ?>
														@for ($i = 2017; $i <= $curyr +1; $i++)
														<option value="{{ $i }}" {{(old('year') == $i ||($year) == $i) ? "selected" : ""}}>{{ $i }}</option>
														@endfor
													</select>
												</div>
											</div>
											<div class="col-md-3">
											    <div class="form-group">
													<label>Month</label>
													<select  class="form-control" name="month" onchange="Reload()">
														<option value="">--Select--</option>
														@foreach($Months as $list)
														<option value="{{ $list->id }}" {{ (old('month') == $list->id ||($month) == $list->id  ) ? 'selected':'' }}>{{ $list->month }}</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="col-md-3">
											    <div class="form-group">
													<label><br></label>
													<br>
													<button class="btn btn-primary" type="submit" name="view">View</button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					
				<div class="row">
						<div class="col-md-12">
						
							<!-- Salary Mandate -->
							<div class="card card-table">
								<div class="card-header">
									<h4 class="card-title">Salary Mandate</h4>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-hover table-center mb-0">
											<thead>
												<tr>
													<th rowspan="1">S/N</th>
													<th rowspan="1">Beneficiary</th>
													<th rowspan="1">Amount</th>
													<th rowspan="1">Bank</th>
													<th rowspan="1">Account Number</th>
													<th rowspan="1">Payment description</th>
												</tr>
											</thead>
											<tbody>
											    @php $i=1; $net=0; @endphp
											    @foreach($NetpaySummary as $list2)
												<tr>
													<td>{{ $i++ }}</td>
													<td>{{ $list2->fullname}}</td>
													<td>{{ number_format(abs($list2->Net),2, '.', ',')}}</td>
													<td>{{ $list2->bank}}</td>
													<td>{{ $list2->account_no}}</td>
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
							<!-- /Salary Mandate -->
							
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
@endsection
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
@endsection
			<!-- /Page Wrapper -->
