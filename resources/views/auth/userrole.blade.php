@extends('layouts.layout')

@section('pageTitle')
    {{ env('Page_Title') }}
@endsection

@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">User Role Setup</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">User Role Setup</li>
                        </ul>
                    </div>
                </div>
            </div>

            @include('_partialView.nofication')

            <div class="card">
                <div class="card-body">
                    <form method="post" action="{{ url('/user-role') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Role Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="rolename" value="{{ old('rolename') }}"
                                        required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="addnew" class="btn btn-primary">Save Role</button>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="mytable">
                            <thead>
                                <tr>
                                    <th style="width: 70px;">S/N</th>
                                    <th>Role Name</th>
                                    <th style="width: 200px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roles as $index => $role)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $role->rolename }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-success"
                                                onclick="editRole('{{ $role->id }}', '{{ addslashes($role->rolename) }}')">Edit</button>
                                            <form method="post" action="{{ url('/user-role') }}"
                                                style="display:inline-block;">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $role->id }}">
                                                <button type="submit" name="del" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete this role?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No user role found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editRoleModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User Role</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ url('/user-role') }}">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="form-group">
                            <label>Role Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_rolename" name="rolename" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="update" class="btn btn-success">Update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function editRole(id, rolename) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_rolename').value = rolename;
            $('#editRoleModal').modal('show');
        }
    </script>
@endsection
