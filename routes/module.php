<?php

use App\Http\Controllers\MasterRolePermission\ModuleController;
use App\Http\Controllers\MasterRolePermission\SubModuleController;
use App\Http\Controllers\MasterRolePermission\AssignModuleRoleController;
use App\Http\Controllers\MasterRolePermission\ParentMenuController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    //user modules
    Route::get('module/create', [ModuleController::class, 'create'])
    ->name('CreateModule');
    Route::post('module/add', [ModuleController::class, 'addModule']);
    Route::post('module/update', [ModuleController::class, 'updateModule']);

    // Sub-module route
    Route::get('sub-module/create', [SubModuleController::class, 'create'])
    ->name('createSubModule');
    Route::post('sub-module/add', [SubModuleController::class, 'addSubModule']);
    Route::post('sub-module/update', [SubModuleController::class, 'updateSubModule']);
    Route::any('/assign-module/create', [AssignModuleRoleController::class, 'create'])->name('AssignModule');
    Route::post('/assign-module/assign',   [AssignModuleRoleController::class, 'assignSubModule']);

    // Parent Menu route
    Route::get('parent-menu/create', [ParentMenuController::class, 'create'])
    ->name('CreateParentMenu');
    Route::post('parent-menu/add', [ParentMenuController::class, 'addParentMenu']);
    Route::post('parent-menu/update', [ParentMenuController::class, 'updateParentMenu']);
  
});
