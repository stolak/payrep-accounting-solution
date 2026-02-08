<?php

namespace App\Http\Controllers\MasterRolePermission;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Session;
use DB;
use Auth;
use App\Models\Module;
use App\Models\Submodule;
use App\Models\UserRole;
use App\Models\AssignRoleModule;

class AssignModuleRoleController extends Controller
{
    public function create(Request $request)
    {
    $data['role']=$request->input('role');
   	if ($data['role']=='') {$data['role']=Session::get('role');}
   	    Session(['role' => $data['role']]);
        $data['roles'] = UserRole::all();
        
        // Get all parent menus ordered by rankOrder
        $parentMenus = DB::table('parent_menu')
            ->orderBy('rankOrder', 'ASC')
            ->get();
        
        // Get all modules with their parent menu info, ordered by module_rank
        $modules = DB::table('modules')
            ->leftJoin('parent_menu', 'parent_menu.id', '=', 'modules.parentMenuId')
            ->select('modules.id', 'modules.module', 'modules.module_rank', 'modules.parentMenuId', 
                     'parent_menu.id as parentMenuId', 'parent_menu.parentMenu', 'parent_menu.rankOrder')
            ->orderBy('modules.module_rank', 'ASC')
            ->get();
        
        // Get all active submodules with their module info, ordered by rank
        $submodules = DB::table('submodules')
            ->where('submodules.status', 1)
            ->join('modules', 'modules.id', '=', 'submodules.moduleid')
            ->leftJoin('parent_menu', 'parent_menu.id', '=', 'modules.parentMenuId')
            ->selectRaw('submodules.id as modID, submodules.id, submodules.submodule, submodules.rank,
                        modules.id as moduleID, modules.module, modules.module_rank, modules.parentMenuId,
                        parent_menu.id as parentMenuId, parent_menu.parentMenu, parent_menu.rankOrder')
            ->orderBy('submodules.rank', 'ASC')
            ->get();
        
        // Check active status for each submodule
        foreach ($submodules as $b){
            $b->active = (DB::table('assign_role_modules')
                ->where('roleid', $data['role'])
                ->where('submoduleid', $b->modID)
                ->first()) ? 1 : 0;
        }
        
        // Organize data hierarchically: Parent Menu -> Module -> Submodule
        $organizedData = [];
        
        foreach ($parentMenus as $parentMenu) {
            $parentMenuData = [
                'id' => $parentMenu->id,
                'parentMenu' => $parentMenu->parentMenu,
                'rankOrder' => $parentMenu->rankOrder,
                'modules' => []
            ];
            
            // Get modules for this parent menu
            $parentModules = $modules->where('parentMenuId', $parentMenu->id);
            
            foreach ($parentModules as $module) {
                $moduleData = [
                    'id' => $module->id,
                    'module' => $module->module,
                    'module_rank' => $module->module_rank,
                    'submodules' => []
                ];
                
                // Get submodules for this module
                $moduleSubmodules = $submodules->where('moduleID', $module->id);
                
                foreach ($moduleSubmodules as $submodule) {
                    $moduleData['submodules'][] = $submodule;
                }
                
                if (count($moduleData['submodules']) > 0) {
                    $parentMenuData['modules'][] = $moduleData;
                }
            }
            
            // Only add parent menu if it has modules with submodules
            if (count($parentMenuData['modules']) > 0) {
                $organizedData[] = $parentMenuData;
            }
        }
        
        // Also handle modules without parent menu (parentMenuId is null)
        $modulesWithoutParent = $modules->where('parentMenuId', null);
        if ($modulesWithoutParent->count() > 0) {
            $noParentData = [
                'id' => null,
                'parentMenu' => 'Other Modules',
                'rankOrder' => 999,
                'modules' => []
            ];
            
            foreach ($modulesWithoutParent as $module) {
                $moduleData = [
                    'id' => $module->id,
                    'module' => $module->module,
                    'module_rank' => $module->module_rank,
                    'submodules' => []
                ];
                
                $moduleSubmodules = $submodules->where('moduleID', $module->id);
                foreach ($moduleSubmodules as $submodule) {
                    $moduleData['submodules'][] = $submodule;
                }
                
                if (count($moduleData['submodules']) > 0) {
                    $noParentData['modules'][] = $moduleData;
                }
            }
            
            if (count($noParentData['modules']) > 0) {
                $organizedData[] = $noParentData;
            }
        }
        
        $data['organizedData'] = $organizedData;
        
        return view('assignModule.assign', $data);
    }

    
    public function assignSubModule(Request $request)
    {
        $data['role']=$request->input('role');
       	if($data['role']==''){$data['role']=Session::get('role');}
       	Session(['role' => $data['role']]);
        $this->validate($request, [
            'role'          => 'required|numeric',
        ]);
        $roleID             = $request['role'];
        $data['submodules']    = DB::table('submodules')->where('submodules.status', 1)->get();
        AssignRoleModule::where('roleid', $roleID)->delete();
        //$this->getDeleteAssignModuleRole($roleID); //clear and assign afresh
        foreach($data['submodules'] as $b){
            
                if($request['arraysubModule_'.$b->id]){
                    AssignRoleModule::create([
                        'roleid' => $roleID,
                        'submoduleid' => $b->id,
                    ]);
                    // $this->getAssignSubModuleRole($roleID, $b->id);
                }
        }
        
        return redirect()->route('AssignModule')->with('message','Module Assigned Successfully');
    }


    public function displaySubModules()
    {
      $data['submodules'] = $this->getAllSubModule(0);
      return view('subModule/viewsubmodules', $data);
    }


    
}
