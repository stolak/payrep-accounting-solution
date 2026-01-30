<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>Main</span>
                </li>
                <li class="active">
                    <a href="/"><i class="fe fe-home"></i> <span>Dashboard</span></a>
                </li>

                @php
                    // Get parent menus that have modules with assigned submodules for the user's role
$userParentMenus = DB::table('assign_role_modules')
    ->join('submodules', 'submodules.id', '=', 'assign_role_modules.submoduleid')
    ->join('modules', 'modules.id', '=', 'submodules.moduleid')
    ->leftJoin('parent_menu', 'parent_menu.id', '=', 'modules.parentMenuId')
    ->where('assign_role_modules.roleid', '=', Auth::user()->userrole)
    ->where('submodules.status', '=', 1)
    ->whereNotNull('modules.parentMenuId')
    ->groupBy('parent_menu.id')
    ->groupBy('parent_menu.parentMenu')
    ->groupBy('parent_menu.rankOrder')
    ->select('parent_menu.id', 'parent_menu.parentMenu', 'parent_menu.rankOrder')
    ->orderBy('parent_menu.rankOrder', 'ASC')
    ->get();

// Get modules without parent menu (for backward compatibility)
$modulesWithoutParent = DB::table('assign_role_modules')
    ->join('submodules', 'submodules.id', '=', 'assign_role_modules.submoduleid')
    ->join('modules', 'modules.id', '=', 'submodules.moduleid')
    ->where('assign_role_modules.roleid', '=', Auth::user()->userrole)
    ->where('submodules.status', '=', 1)
    ->whereNull('modules.parentMenuId')
    ->groupBy('modules.id')
    ->groupBy('modules.module')
    ->groupBy('modules.module_rank')
    ->select('modules.id', 'modules.module', 'modules.module_rank')
    ->orderBy('modules.module_rank', 'ASC')
                        ->get();
                @endphp

                {{-- Display parent menus with their modules and submodules --}}
                @if ($userParentMenus)
                    @foreach ($userParentMenus as $parentMenu)
                        @php
                            // Get modules for this parent menu that have assigned submodules
                            $userModules = DB::table('assign_role_modules')
                                ->join('submodules', 'submodules.id', '=', 'assign_role_modules.submoduleid')
                                ->join('modules', 'modules.id', '=', 'submodules.moduleid')
                                ->where('assign_role_modules.roleid', '=', Auth::user()->userrole)
                                ->where('submodules.status', '=', 1)
                                ->where('modules.parentMenuId', '=', $parentMenu->id)
                                ->groupBy('modules.id')
                                ->groupBy('modules.module')
                                ->groupBy('modules.module_rank')
                                ->select('modules.id', 'modules.module', 'modules.module_rank')
                                ->orderBy('modules.module_rank', 'ASC')
                                ->get();
                        @endphp
                        @if ($userModules->count() > 0)
                            <li class="submenu">
                                <a href="#"><i class="fe fe-vector"></i>
                                    <span>{{ $parentMenu->parentMenu }}</span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    @foreach ($userModules as $module)
                                        @php
                                            // Get submodules for this module
                                            $userLinks = DB::table('assign_role_modules')
                                                ->join(
                                                    'submodules',
                                                    'submodules.id',
                                                    '=',
                                                    'assign_role_modules.submoduleid',
                                                )
                                                ->where('assign_role_modules.roleid', '=', Auth::user()->userrole)
                                                ->where('submodules.moduleid', '=', $module->id)
                                                ->where('submodules.status', '=', 1)
                                                ->distinct()
                                                ->select('submodules.*')
                                                ->orderBy('submodules.rank', 'ASC')
                                                ->get();
                                        @endphp
                                        @if ($userLinks->count() > 0)
                                            <li class="submenu">
                                                <a href="#"><span>{{ $module->module }}</span> <span
                                                        class="menu-arrow"></span></a>
                                                <ul style="display: none;">
                                                    @foreach ($userLinks as $route)
                                                        <li><a
                                                                href="{!! url($route->links) !!}">{{ $route->submodule }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    @endforeach
                @endif

                {{-- Display modules without parent menu (for backward compatibility) --}}
                @if ($modulesWithoutParent)
                    @foreach ($modulesWithoutParent as $module)
                        @php
                            $userLinks = DB::table('assign_role_modules')
                                ->join('submodules', 'submodules.id', '=', 'assign_role_modules.submoduleid')
                                ->where('assign_role_modules.roleid', '=', Auth::user()->userrole)
                                ->where('submodules.moduleid', '=', $module->id)
                                ->where('submodules.status', '=', 1)
                                ->distinct()
                                ->select('submodules.*')
                                ->orderBy('submodules.rank', 'ASC')
                                ->get();
                        @endphp
                        @if ($userLinks->count() > 0)
                            <li class="submenu">
                                <a href="#"><i class="fe fe-vector"></i> <span>{{ $module->module }}</span> <span
                                        class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    @foreach ($userLinks as $route)
                                        <li><a href="{!! url($route->links) !!}">{{ $route->submodule }}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    @endforeach
                @endif
                @include('layouts.techsidemenu')
            </ul>
        </div>
    </div>
</div>
