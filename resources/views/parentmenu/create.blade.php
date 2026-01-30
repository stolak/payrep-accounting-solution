<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
     {{env('Page_Title')}}
@endsection
@section('content')
            <div class="page-wrapper">
				<div class="content container-fluid">
					<!-- Page Header -->
					<div class="page-header">
						<div class="row">
							<div class="col">
								<h3 class="page-title">Parent Menu Registration</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><a href="/">Home</a></li>
									<li class="breadcrumb-item active">Parent Menu</li>
								</ul>
							</div>
						</div>
					</div>
					<!-- /Page Header -->
					<!-- include notification -->
        			 @include('_partialView.nofication') 
        			 <!-- /include notification -->
        			 <form method="post" action="{{ url('/parent-menu/add') }}" class="form-horizontal">
          {{ csrf_field() }} 
          
          
          
          <div class="form-group">
            <label for="section" class="col-md-3 control-label">Parent Menu Name</label>
            <div class="col-md-9">
              <input id="parentMenuName" type="text" class="form-control" name="parentMenuName" value="{{ old('parentMenuName') }}" required>
            </div>
          </div>

          <div class="form-group">
            <label for="section" class="col-md-3 control-label">Rank Order</label>
            <div class="col-md-9">
              <input id="rankOrder" type="number" class="form-control" name="rankOrder" value="{{ old('rankOrder') }}" min="1" required>
            </div>
          </div>

          <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
              <button type="submit" class="btn btn-success btn-sm pull-right">Add Parent Menu</button>
            </div>
          </div>
        </form>
        
        <div class="box-body">
  <h2 class="text-center">ALL PARENT MENUS</h2>
  <div class="row"> {{ csrf_field() }}
    <div class="col-md-12">
      <table class="table table-striped table-condensed table-bordered input-sm">
        <thead>
          <tr class="input-sm">
            <th>S/N</th>
            <th>PARENT MENU NAME</th>
            <th>RANK ORDER</th>
            
            <th></th>
          </tr>
        </thead>
        <tbody>
        
        @php $key = 1; @endphp
        @foreach($parentMenus as $list)
        <tr>
          <td>{{$key ++}}</td>
          <td>{{strtoupper($list->parentMenu)}}</td>
          <td>{{$list->rankOrder}}</td>
          <td><a href="#" title="Edit" id="{{$list->id}}" class="btn btn-success fa fa-edit edits" onclick="EditParentMenu('{{$list->id}}','{{$list->parentMenu}}','{{$list->rankOrder}}')"></a></td>
        </tr>
        @endforeach
        </tbody> 
      </table>
    </div>
  </div>
  <!-- /.col --> 
  
</div>
		<form action="{{url('/parent-menu/update')}}" method="post">
{{ csrf_field() }} 
<div id="myModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Update Parent Menu</h4>
            </div>
            <div class="modal-body">
           
                    <div class="row" style="margin-bottom: 10px;">
                     <div class="form-group">
                        <label >Parent Menu Name</label>
                        <div class="col-md-12">
                          <input id="parentMenu" type="text" class="form-control" name="name" required>
                          <input id="id" type="hidden" class="form-control" name="parentMenuID" required>
                        </div>
                      </div>
                    </div>
                      
                    <div class="row">
                     <div class="form-group">
                       <label >Rank Order</label>
                        <div class="col-md-12">
                          <input id="rankOrders" type="number" class="form-control" name="rankOrder" value="" min="1" required>
                          
                        </div>
                      </div>
                    </div>    

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" id="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
</div>
<!--// modal Bootstrap -->
</form>
			</div>
		</div>
@endsection
@section('scripts')
<script>

   function EditParentMenu(id,parentMenuName,rankOrder)
    {
    $('#parentMenu').val(parentMenuName);
   $('#id').val(id);
   $('#rankOrders').val(rankOrder);
   $("#myModal").modal('show');
   }
   
  </script>      
@endsection

@section('styles')

@endsection
			<!-- /Page Wrapper -->

