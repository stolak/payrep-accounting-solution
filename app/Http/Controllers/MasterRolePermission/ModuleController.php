<?php

namespace App\Http\Controllers\MasterRolePermission;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Session;
use DB;
use Auth;
use App\Models\Module;
use App\Models\ParentMenu;

class ModuleController extends Controller
{

   public function create()
    {
        $data['modules'] = Module::leftJoin('parent_menu', 'modules.parentMenuId', '=', 'parent_menu.id')
            ->select('modules.*', 'parent_menu.parentMenu')
            ->get();
        $data['parentMenus'] = ParentMenu::orderBy('rankOrder', 'asc')->get();
        return view('module.create', $data);
    }

    public function addModule(Request $request)
    {
        $this->validate($request, [
            'moduleName' => 'required|regex:/^[a-zA-Z0-9,.!?\-)\( ]*$/|max:1000|unique:modules,module',
        ]);
        $addModule = Module::create([
            'module' => $request->input('moduleName'),
            'module_rank' => $request->input('rank'),
            'parentMenuId' => $request->input('parentMenuId') ?: null,
        ]);
        if (!$addModule) {
            return redirect()
                ->route('CreateModule')->with('error_message','Sorry, error occur during adding new module. Try again');
        }
        return redirect()
            ->route('CreateModule')->with('message','Module Created Successfully');
    }

    public function updateModule(Request $request)
    {
        $this->validate($request, [
            'name'        => 'required|regex:/^[a-zA-Z0-9,.!?\-)\( ]*$/|max:1000',
            'moduleID'          => 'required|numeric',
        ]);
        $getUpdateModule        = Module::where('id', $request->input('moduleID'))->update([
            'module' => $request->input('name'),
            'module_rank' => $request->input('rank'),
            'parentMenuId' => $request->input('parentMenuId') ?: null,
        ]);
        if ($getUpdateModule){
            return redirect()->route('CreateModule')->with('message','Module Successfully Updated');
        } else {
            return redirect()->route('CreateModule')->with('error_message','Sorry, we cannot update this module');
        }
    }
}
