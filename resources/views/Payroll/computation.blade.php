<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    Salary Computation
@endsection
@section('content')
            <div class="page-wrapper">
				<div class="content container-fluid">
					<!-- Page Header -->
					<div class="page-header">
						<div class="row">
							<div class="col">
								<h3 class="page-title">Payroll</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><a href="/">Home</a></li>
									<li class="breadcrumb-item active">Salary Computation</li>
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
									<h4 class="card-title">Salary Computation</h4>
								</div>
								<div class="card-body">
									<form method="post" name="mainform" id="mainform">
                                    {{ csrf_field() }}
										<div class="row">
										    <div class="col-md-5">
											    <div class="form-group">
													<label>Active year</label>
													<input type="text" class="form-control"  value="{{$year}}" readonly >
												</div>
											</div>
											<div class="col-md-5">
											    <div class="form-group">
													<label>Active Month</label>
													<input type="text" class="form-control"  value="{{$month}}"  readonly >
												</div>
											</div>
										</div>
										
										<div class="text-right">
											<button class="btn btn-primary" type="submit" name="compute">Compute</button>
										</div>
									</form>
								</div>
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
