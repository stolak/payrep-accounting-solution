<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserRoleController extends Controller
{
    public function index(Request $request)
    {
        if (isset($_POST['addnew'])) {
            $data = $request->validate([
                'rolename' => 'required|string|max:100|unique:user_roles,rolename',
            ]);

            DB::table('user_roles')->insert([
                'rolename' => trim($data['rolename']),
                'editable' => 1,
                'assignabled' => 1,
                'status' => 1,
            ]);

            return back()->with('message', 'User role successfully created.');
        }

        if (isset($_POST['update'])) {
            $data = $request->validate([
                'id' => 'required|integer|exists:user_roles,id',
                'rolename' => 'required|string|max:100|unique:user_roles,rolename,' . $request->input('id'),
            ]);

            DB::table('user_roles')
                ->where('id', $data['id'])
                ->update([
                    'rolename' => trim($data['rolename']),
                ]);

            return back()->with('message', 'User role successfully updated.');
        }

        if (isset($_POST['del'])) {
            $data = $request->validate([
                'id' => 'required|integer|exists:user_roles,id',
            ]);

            $inUse = DB::table('users')->where('userrole', $data['id'])->exists();
            $inUse2 = DB::table('assign_role_modules')->where('roleid', $data['id'])->exists();
            if ($inUse) {
                return back()->with('error_message', 'This role is assigned to one or more users and cannot be deleted.');
            }
            if ($inUse2) {
                return back()->with('error_message', 'This role is assigned to one or more modules and cannot be deleted.');
            }

            DB::table('user_roles')->where('id', $data['id'])->delete();
            return back()->with('message', 'User role successfully deleted.');
        }

        $roles = DB::table('user_roles')
        ->where('editable', 1)
            ->select('id', 'rolename')
            ->orderBy('rolename', 'asc')
            ->get();

        return view('auth.userrole', compact('roles'));
    }
}


