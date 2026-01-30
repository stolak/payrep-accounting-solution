<?php

namespace App\Http\Controllers\MasterRolePermission;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Session;
use DB;
use Auth;
use App\Models\ParentMenu;

class ParentMenuController extends Controller
{

   public function create()
    {
        $data['parentMenus'] = ParentMenu::orderBy('rankOrder', 'asc')->get();
        return view('parentmenu.create', $data);
    }

    public function addParentMenu(Request $request)
    {
        $this->validate($request, [
            'parentMenuName' => 'required|regex:/^[a-zA-Z0-9,.!?\-)\( ]*$/|max:1000|unique:parent_menu,parentMenu',
            'rankOrder' => 'required|numeric|min:1',
        ]);
        $addParentMenu = ParentMenu::create([
            'parentMenu' => $request->input('parentMenuName'),
            'rankOrder' => $request->input('rankOrder'),
        ]);
        if (!$addParentMenu) {
            return redirect()
                ->route('CreateParentMenu')->with('error_message','Sorry, error occur during adding new parent menu. Try again');
        }
        return redirect()
            ->route('CreateParentMenu')->with('message','Parent Menu Created Successfully');
    }

    public function updateParentMenu(Request $request)
    {
        $this->validate($request, [
            'name'        => 'required|regex:/^[a-zA-Z0-9,.!?\-)\( ]*$/|max:1000|unique:parent_menu,parentMenu,' . $request->input('parentMenuID'),
            'parentMenuID'          => 'required|numeric',
            'rankOrder' => 'required|numeric|min:1',
        ]);
        $getUpdateParentMenu        = ParentMenu::where('id', $request->input('parentMenuID'))->update([
            'parentMenu' => $request->input('name'),
            'rankOrder' => $request->input('rankOrder'),
        ]);
        if ($getUpdateParentMenu){
            return redirect()->route('CreateParentMenu')->with('message','Parent Menu Successfully Updated');
        } else {
            return redirect()->route('CreateParentMenu')->with('error_message','Sorry, we cannot update this parent menu');
        }
    }
}

