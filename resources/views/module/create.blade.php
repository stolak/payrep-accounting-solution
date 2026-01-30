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
								<h3 class="page-title">Module Registration</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><a href="/">Home</a></li>
									<li class="breadcrumb-item active">Module</li>
								</ul>
							</div>
						</div>
					</div>
					<!-- /Page Header -->
					<!-- include notification -->
        			 @include('_partialView.nofication') 
        			 <!-- /include notification -->
        			 <form method="post" action="{{ url('/module/add') }}" class="form-horizontal">
          {{ csrf_field() }} 
          
          
          
          <div class="form-group">
            <label for="section" class="col-md-3 control-label">Module Name</label>
            <div class="col-md-9">
              <input id="moduleName" type="text" class="form-control" name="moduleName" value="{{ old('moduleName') }}" required>
            </div>
          </div>

          <div class="form-group">
            <label for="section" class="col-md-3 control-label">Rank</label>
            <div class="col-md-9">
              <input id="rank" type="number" class="form-control" name="rank" value="{{ old('rank') }}" required>
            </div>
          </div>

          <div class="form-group">
            <label for="section" class="col-md-3 control-label">Parent Menu</label>
            <div class="col-md-9">
              <select id="parentMenuId" name="parentMenuId" class="form-control">
                <option value="">--Select Parent Menu--</option>
                @foreach($parentMenus as $parentMenu)
                  <option value="{{ $parentMenu->id }}" {{ old('parentMenuId') == $parentMenu->id ? 'selected' : '' }}>
                    {{ $parentMenu->parentMenu }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
              <button type="submit" class="btn btn-success btn-sm pull-right">Add Module</button>
            </div>
          </div>
        </form>
        
        <div class="box-body">
  <h2 class="text-center">ALL MODULES</h2>
  <div class="row"> {{ csrf_field() }}
    <div class="col-md-12">
      <table class="table table-striped table-condensed table-bordered input-sm">
        <thead>
          <tr class="input-sm">
            <th>S/N</th>
            <th>MODULE NAME</th>
            <th>RANK</th>
            <th>PARENT MENU</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        
        @php $key = 1; @endphp
        @foreach($modules as $list)
        <tr>
          <td>{{$key ++}}</td>
          <td>{{strtoupper($list->module)}}</td>
          <td>{{strtoupper($list->module_rank)}}</td>
          <td>{{$list->parentMenu ? strtoupper($list->parentMenu) : '-'}}</td>
          <td><a href="#" title="Edit" id="{{$list->id}}" class="btn btn-success fa fa-edit edits" onclick="Editmodule('{{$list->id}}','{{$list->module}}','{{$list->module_rank}}','{{$list->parentMenuId ?? ''}}')"></a></td>
        </tr>
        @endforeach
        </tbody> 
      </table>
    </div>
  </div>
  <!-- /.col --> 
  
</div>
		<form action="{{url('/module/update')}}" method="post">
{{ csrf_field() }} 
<div id="myModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Update Module</h4>
            </div>
            <div class="modal-body">
           
                    <div class="row" style="margin-bottom: 10px;">
                     <div class="form-group">
                        <label >Module Name</label>
                        <div class="col-md-12">
                          <input id="module" type="text" class="form-control" name="name" required>
                          <input id="id" type="hidden" class="form-control" name="moduleID" required>
                        </div>
                      </div>
                    </div>
                      
                    <div class="row">
                     <div class="form-group">
                       <label >Rank</label>
                        <div class="col-md-12">
                          <input id="ranks" type="number" class="form-control" name="rank" value="" required>
                          
                        </div>
                      </div>
                    </div>

                    <div class="row">
                     <div class="form-group">
                       <label >Parent Menu</label>
                        <div class="col-md-12">
                          <select id="edit_parentMenuId" name="parentMenuId" class="form-control">
                            <option value="">--Select Parent Menu--</option>
                            @foreach($parentMenus as $parentMenu)
                              <option value="{{ $parentMenu->id }}">{{ $parentMenu->parentMenu }}</option>
                            @endforeach
                          </select>
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

   function Editmodule(id,modulename,rank,parentMenuId)
    {
    $('#module').val(modulename);
   $('#id').val(id);
   $('#ranks').val(rank);
   $('#edit_parentMenuId').val(parentMenuId || '');
   $("#myModal").modal('show');
   }
   
  </script>      
@endsection

@section('styles')

@endsection
			<!-- /Page Wrapper -->