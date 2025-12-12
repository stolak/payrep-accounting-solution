<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Salary Chart
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
									<li class="breadcrumb-item active">Salary Chart</li>
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
									<h4 class="card-title">Salary Chart</h4>
								</div>
								<div class="card-body">
									<form method="post" name="mainform" id="mainform">
                                    {{ csrf_field() }}
										<div class="row">
										    <div class="col-md-3">
											    <div class="form-group">
													<label>Grade Level</label>
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
											<div class="col-md-2">
												<div class="form-group">
													<label>{{$list->variable}}</label>
													@php $para=$list->ref_code; @endphp
													<input type="number" step="0.01" required class="form-control"  value="{{$GradeChart? $GradeChart->$para : 0}}" name="{{$list->ref_code}}" >
												</div>
											</div>
											@endforeach
										</div>
										<h3>Deduction</h3>
										<div class="row">
											@foreach($DeductionVariable as $list)
											<div class="col-md-2">
												<div class="form-group">
													<label>{{$list->variable}}</label>
													@php $para=$list->ref_code; @endphp
													<input type="number" step="0.01" required class="form-control"  value="{{$GradeChart? $GradeChart->$para : 0}}" name="{{$list->ref_code}}" >
												</div>
											</div>
											@endforeach
										</div>
										
										<div class="text-right">
											<button type="submit" class="btn btn-primary" name="update">Update</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					
				<div class="row">
						<div class="col-md-12">
						
							<!-- Salary Chart -->
							<div class="card card-table">
								<div class="card-header">
									<h4 class="card-title">Salary Chart</h4>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-hover table-center mb-0">
											<thead>
												<tr>
													<th rowspan="1">S/N</th>
													<th rowspan="1">Grade</th>
													@foreach($EarningVariable as $list)
													<th rowspan="1">{{$list->variable}}</th>
													@endforeach
													@foreach($DeductionVariable as $list)
													<th rowspan="1">{{$list->variable}}</th>
													@endforeach
													<th rowspan="1">Action</th>
												</tr>
											</thead>
											<tbody>
											    @php
											    $i=1;
											    @endphp
											   
											    @foreach($SalaryChart as $list2)
												<tr>
													<td>{{ $i++ }}</td>
													<td>{{ $list2->grades}}</td>
													@foreach($EarningVariable as $list)
														@php $para=$list->ref_code; @endphp
														<td>{{number_format($list2->$para,2, '.', ',')}}</td>
													@endforeach
													@foreach($DeductionVariable as $list)
														@php $para=$list->ref_code; @endphp
														<td>({{number_format($list2->$para,2, '.', ',')}})</td>
													@endforeach
													<td>
														<a class="btn btn-sm bg-success-light" href="javascript: ReloadWithPara('{{$list2->grade}}')">
															<i class="fe fe-pencil"></i>
														</a>
													</td>
												</tr>
											    @endforeach
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<!-- /Salary Chart -->
							
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
function ReloadWithPara(id)
{
    document.getElementById('grade').value = id;
   document.forms["mainform"].submit();
}
</script>
@endsection
			<!-- /Page Wrapper -->
