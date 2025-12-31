<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Auth;
class ProjectController extends Controller {

    public function project(Request $request)
    {
        $data['name'] = $request->input('name');
        $data['description'] = $request->input('description');
        $data['categoryId'] = $request->input('categoryId');
        $data['location'] = $request->input('location');
        $data['status'] = $request->input('status');
        $data['id'] = $request->input('id');
        
        if (isset($_POST['addnew'])) {
            $this->validate($request, [
                'name' => 'required|string|unique:projects,name',
                'description' => 'nullable|string',
                'categoryId' => 'nullable|integer',
                'location' => 'nullable|string',
                'status' => 'nullable|integer',
            ]);

            DB::table('projects')->insert([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'categoryId' => $data['categoryId'] ?? null,
                'location' => $data['location'] ?? null,
                'status' => $data['status'] ?? 1,
                'createdAt' => now(),
                'updateAt' => now(),
                'createdBy' => Auth::user()->id,
            ]);
            return back()->with('message', 'New record successfully added.');
        }
        
        if (isset($_POST['update'])) {
            $this->validate($request, [
                'name' => 'required|string|unique:projects,name,' . $request->input('id'),
                'description' => 'nullable|string',
                'categoryId' => 'nullable|integer',
                'location' => 'nullable|string',
                'status' => 'nullable|integer',
                'id' => 'required|integer',
            ]);

            DB::table('projects')->where('id', $data['id'])->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'categoryId' => $data['categoryId'] ?? null,
                'location' => $data['location'] ?? null,
                'status' => $data['status'] ?? 1,
                'updateAt' => now(),
            ]);
            return back()->with('message', 'Record successfully updated.');
        }
        
        if (isset($_POST['del'])) {
            $del = $request->input('deleteid');
            // Check if project has related records before deletion
            // Add your related table checks here if needed
            // if (DB::table('related_table')->where('projectId', $del)->first()) {
            //     return back()->with('error_message', 'Project has related records. Hence, record cannot be deleted!');
            // }
            DB::table('projects')->where('id', $del)->delete();
            return back()->with('message', 'Record successfully deleted.');
        }
        
        // Fetch projects list
        $data['projects'] = DB::table('projects')
            ->select('id', 'name', 'description', 'categoryId', 'location', 'status', 'createdAt', 'updateAt', 'createdBy')
            ->orderBy('createdAt', 'desc')
            ->get();
        
        // Fetch categories if needed (assuming there's a categories table)
         $data['projectCategories'] = DB::table('project_categories')
            ->select('id', 'category')
            ->orderBy('category', 'asc')
            ->get();
        
        return view('Project.project', $data);
    }

    public function projectCategory(Request $request)
    {
        $data['category'] = $request->input('category');
        $data['id'] = $request->input('id');
        
        if (isset($_POST['addnew'])) {
            $this->validate($request, [
                'category' => 'required|string|unique:project_categories,category',
            ]);

            DB::table('project_categories')->insert([
                'category' => $data['category'],
            ]);
            return back()->with('message', 'New record successfully added.');
        }
        
        if (isset($_POST['update'])) {
            $this->validate($request, [
                'category' => 'required|string|unique:project_categories,category,' . $request->input('id'),
                'id' => 'required|integer',
            ]);

            DB::table('project_categories')->where('id', $data['id'])->update([
                'category' => $data['category'],
            ]);
            return back()->with('message', 'Record successfully updated.');
        }
        
        if (isset($_POST['del'])) {
            $del = $request->input('deleteid');
            // Check if category has related records before deletion
            // Add your related table checks here if needed
            if (DB::table('projects')->where('categoryId', $del)->first()) {
                return back()->with('error_message', 'Category has related projects. Hence, record cannot be deleted!');
            }
            DB::table('project_categories')->where('id', $del)->delete();
            return back()->with('message', 'Record successfully deleted.');
        }
        
        // Fetch project categories list
        $data['projectCategories'] = DB::table('project_categories')
            ->select('id', 'category')
            ->orderBy('category', 'asc')
            ->get();
        
        return view('Project.projectcategory', $data);
    }

   



    

}
