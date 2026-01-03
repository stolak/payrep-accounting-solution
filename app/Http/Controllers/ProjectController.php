<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Auth;
use Session;
class ProjectController extends Basefunction {

    public function project(Request $request)
    {
        $data['projectCode'] = $request->input('projectCode');
        $data['name'] = $request->input('name');
        $data['description'] = $request->input('description');
        $data['categoryId'] = $request->input('categoryId');
        $data['location'] = $request->input('location');
        $data['status'] = $request->input('status');
        $data['id'] = $request->input('id');
        $data['expenseAccountId'] = $request->input('expenseAccountId');
        $data['clientId'] = $request->input('clientId');
        
        if (isset($_POST['addnew'])) {
            $this->validate($request, [
                'projectCode' => 'required|string|unique:projects,projectCode',
                'name' => 'required|string|unique:projects,name',
                'description' => 'nullable|string',
                'categoryId' => 'nullable|integer',
                'location' => 'nullable|string',
                'status' => 'nullable|string',
                'clientId' => 'nullable|integer',
            ]);

            DB::table('projects')->insert([
                'projectCode' => $data['projectCode'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'categoryId' => $data['categoryId'] ?? null,
                'location' => $data['location'] ?? null,
                'status' => $data['status'] ?? "Active",
                'expenseAccountId' => $data['expenseAccountId'] ?? null,
                'clientId' => $data['clientId'] ?? null,
                'createdAt' => now(),
                'updatedAt' => now(),
                'createdBy' => Auth::user()->id,
            ]);
            return back()->with('message', 'New record successfully added.');
        }
        
        if (isset($_POST['update'])) {
            $this->validate($request, [
                'projectCode' => 'required|string|unique:projects,projectCode,' . $request->input('id'),
                'name' => 'required|string|unique:projects,name,' . $request->input('id'),
                'description' => 'nullable|string',
                'categoryId' => 'nullable|integer',
                'location' => 'nullable|string',
                'status' => 'nullable|string',
                'clientId' => 'nullable|integer',
                'id' => 'required|integer',
            ]);

            DB::table('projects')->where('id', $data['id'])->update([
                'projectCode' => $data['projectCode'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'categoryId' => $data['categoryId'] ?? null,
                'location' => $data['location'] ?? null,
                'status' => $data['status'] ?? "Active",
                'expenseAccountId' => $data['expenseAccountId'] ?? null,
                'clientId' => $data['clientId'] ?? null,
                // 'incomeAccountId' => $data['incomeAccountId'] ?? null,
                'updatedAt' => now(),
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
            ->select('projects.id', 'projectCode', 'projects.clientId', 'projects.expenseAccountId', 'projects.name', 'description', 'categoryId', 'location', 'projects.status', 'createdAt', 'updatedAt', 'createdBy', 'account_charts.accountdescription as expenseAccountName', 'project_categories.category as categoryName', 'clients.name as clientName')
            ->leftJoin('account_charts', 'projects.expenseAccountId', '=', 'account_charts.id')
            ->leftJoin('project_categories', 'projects.categoryId', '=', 'project_categories.id')
            ->leftJoin('clients', 'projects.clientId', '=', 'clients.id')
            ->orderBy('createdAt', 'desc')
            ->get();
        
        // Fetch categories if needed (assuming there's a categories table)
         $data['projectCategories'] = DB::table('project_categories')
            ->select('id', 'category')
            ->orderBy('category', 'asc')
            ->get();
        $data['accountLookUp'] = $this->AccountLookUpByHeadId(1);
        
        // Fetch clients list for dropdown
        $data['clients'] = DB::table('clients')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
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

    public function budget(Request $request)
    {
        $data['name'] = $request->input('name');
        $data['description'] = $request->input('description');
        $data['categoryId'] = $request->input('categoryId');
        $data['id'] = $request->input('id');
        
        if (isset($_POST['addnew'])) {
            $this->validate($request, [
                'name' => 'required|string|unique:budgets,name',
                'description' => 'nullable|string',
                'categoryId' => 'required|integer',
            ]);

            DB::table('budgets')->insert([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'categoryId' => $data['categoryId'],
            ]);
            return back()->with('message', 'New record successfully added.');
        }
        
        if (isset($_POST['update'])) {
            $this->validate($request, [
                'name' => 'required|string|unique:budgets,name,' . $request->input('id'),
                'description' => 'nullable|string',
                'categoryId' => 'required|integer',
                'id' => 'required|integer',
            ]);

            DB::table('budgets')->where('id', $data['id'])->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'categoryId' => $data['categoryId'],
            ]);
            return back()->with('message', 'Record successfully updated.');
        }
        
        if (isset($_POST['del'])) {
            $del = $request->input('deleteid');
            // Check if budget has related records before deletion
            // Add your related table checks here if needed
            if (DB::table('project_budget')->where('budgetId', $del)->first()) {
                return back()->with('error_message', 'Budget has related project budgets. Hence, record cannot be deleted!');
            }
            DB::table('budgets')->where('id', $del)->delete();
            return back()->with('message', 'Record successfully deleted.');
        }
        
        // Fetch budgets list with category
        $data['budgets'] = DB::table('budgets')
            ->leftJoin('budget_categories', 'budgets.categoryId', '=', 'budget_categories.id')
            ->select('budgets.id', 'budgets.name', 'budgets.description', 'budgets.categoryId', 'budget_categories.category as categoryName')
            ->orderBy('budgets.name', 'asc')
            ->get();
        
        // Fetch budget categories for dropdown
        $data['budgetCategories'] = DB::table('budget_categories')
            ->select('id', 'category')
            ->orderBy('category', 'asc')
            ->get();
        
        return view('Project.budget', $data);
    }

    public function projectBudget(Request $request)
    {
        $data['projectId'] = $request->input('projectId');
        $data['budgetId'] = $request->input('budgetId');
        $data['unit'] = $request->input('unit');
        $data['unitCost'] = $request->input('unitCost');
        $data['amount'] = $request->input('amount');
        $data['id'] = $request->input('id');
        
        // Handle project selection - reload page with selected project
        if ($request->has('select_project')) {
            $data['projectId'] = $request->input('projectId');
            Session(['selected_project_id' => $data['projectId']]);
        }
        
        // Get selected project from session if not in request
        if (empty($data['projectId'])) {
            $data['projectId'] = Session::get('selected_project_id');
        }
        
        if (isset($_POST['addnew'])) {
            // Custom validation logic
            $unit = $request->input('unit');
            $unitCost = $request->input('unitCost');
            $amount = $request->input('amount');
            
            // Check if unit and unitCost are both present and > 0
            $hasUnitAndCost = !empty($unit) && $unit > 0 && !empty($unitCost) && $unitCost > 0;
            
            // Validation rules
            $rules = [
                'projectId' => 'required|integer',
                'budgetId' => 'required|integer',
            ];
            
            // If both unit and unitCost are present, they must both be valid
            if (!empty($unit) || !empty($unitCost)) {
                $rules['unit'] = 'required|numeric|min:0';
                $rules['unitCost'] = 'required|numeric|min:0';
            }
            
            // If unit and unitCost are not both present and > 0, amount is required
            if (!$hasUnitAndCost) {
                $rules['amount'] = 'required|numeric|min:0';
            } else {
                $rules['amount'] = 'nullable|numeric|min:0';
            }
            
            $this->validate($request, $rules);

            // Check if this project-budget combination already exists
            $existing = DB::table('project_budget')
                ->where('projectId', $data['projectId'])
                ->where('budgetId', $data['budgetId'])
                ->first();
            
            if ($existing) {
                return back()->with('error_message', 'This budget is already assigned to this project.');
            }

            // Calculate amount if unit and unitCost are present and > 0
            $calculatedAmount = $amount;
            if ($hasUnitAndCost) {
                $calculatedAmount = $unit * $unitCost;
            }

            DB::table('project_budget')->insert([
                'projectId' => $data['projectId'],
                'budgetId' => $data['budgetId'],
                'unit' => $unit ?? null,
                'unitCost' => $unitCost ?? null,
                'amount' => $calculatedAmount,
                'createdBy' => Auth::user()->id,
                'createdAt' => now(),
                'updatedAt' => now(),
            ]);
            return back()->with('message', 'New record successfully added.');
        }
        
        if (isset($_POST['update'])) {
            // Custom validation logic
            $unit = $request->input('unit');
            $unitCost = $request->input('unitCost');
            $amount = $request->input('amount');
            
            // Check if unit and unitCost are both present and > 0
            $hasUnitAndCost = !empty($unit) && $unit > 0 && !empty($unitCost) && $unitCost > 0;
            
            // Validation rules
            $rules = [
                'projectId' => 'required|integer',
                'budgetId' => 'required|integer',
                'id' => 'required|integer',
            ];
            
            // If both unit and unitCost are present, they must both be valid
            if (!empty($unit) || !empty($unitCost)) {
                $rules['unit'] = 'required|numeric|min:0';
                $rules['unitCost'] = 'required|numeric|min:0';
            }
            
            // If unit and unitCost are not both present and > 0, amount is required
            if (!$hasUnitAndCost) {
                $rules['amount'] = 'required|numeric|min:0';
            } else {
                $rules['amount'] = 'nullable|numeric|min:0';
            }
            
            $this->validate($request, $rules);

            // Check if this project-budget combination already exists (excluding current record)
            $existing = DB::table('project_budget')
                ->where('projectId', $data['projectId'])
                ->where('budgetId', $data['budgetId'])
                ->where('id', '!=', $data['id'])
                ->first();
            
            if ($existing) {
                return back()->with('error_message', 'This budget is already assigned to this project.');
            }

            // Calculate amount if unit and unitCost are present and > 0
            $calculatedAmount = $amount;
            if ($hasUnitAndCost) {
                $calculatedAmount = $unit * $unitCost;
            }

            DB::table('project_budget')->where('id', $data['id'])->update([
                'budgetId' => $data['budgetId'],
                'unit' => $unit ?? null,
                'unitCost' => $unitCost ?? null,
                'amount' => $calculatedAmount,
                'updatedAt' => now(),
            ]);
            return back()->with('message', 'Record successfully updated.');
        }
        
        if (isset($_POST['del'])) {
            $del = $request->input('deleteid');
            DB::table('project_budget')->where('id', $del)->delete();
            return back()->with('message', 'Record successfully deleted.');
        }
        
        // Fetch projects list
        $data['projects'] = DB::table('projects')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();
        
        // Fetch budgets list
        $data['budgets'] = DB::table('budgets')
            ->select('id', 'name as budgetName' )
            ->orderBy('name', 'asc')
            ->get();
        
        // Fetch project budgets for selected project
        $data['projectBudgets'] = collect();
        if (!empty($data['projectId'])) {
            $data['projectBudgets'] = DB::table('project_budget')
                ->leftJoin('budgets', 'project_budget.budgetId', '=', 'budgets.id')
                ->leftJoin('budget_categories', 'budgets.categoryId', '=', 'budget_categories.id')
                ->where('project_budget.projectId', $data['projectId'])
                ->select('project_budget.id', 'project_budget.projectId', 'project_budget.budgetId', 'project_budget.unit', 'project_budget.unitCost', 'project_budget.amount', 'budgets.name as budgetName', 'budget_categories.category as budgetCategoryName')
                ->orderBy('budget_categories.category', 'asc')
                ->orderBy('budgets.name', 'asc')
                ->get();
        }
        
        return view('Project.projectbudget', $data);
    }

    public function budgetCategory(Request $request)
    {
        $data['category'] = $request->input('category');
        $data['id'] = $request->input('id');
        
        if (isset($_POST['addnew'])) {
                $this->validate($request, [
                'category' => 'required|string|unique:budget_categories,category',
            ]);

            DB::table('budget_categories')->insert([
                'category' => $data['category'],
            ]);
            return back()->with('message', 'New record successfully added.');
        }
        
        if (isset($_POST['update'])) {
                $this->validate($request, [
                'category' => 'required|string|unique:budget_categories,category,' . $request->input('id'),
                'id' => 'required|integer',
            ]);

            DB::table('budget_categories')->where('id', $data['id'])->update([
                'category' => $data['category'],
            ]);
            return back()->with('message', 'Record successfully updated.');
        }
        
        if (isset($_POST['del'])) {
            $del = $request->input('deleteid');
            // Check if category has related records before deletion
            // Add your related table checks here if needed
            if (DB::table('budgets')->where('categoryId', $del)->first()) {
                return back()->with('error_message', 'Category has related budgets. Hence, record cannot be deleted!');
            }
            DB::table('budget_categories')->where('id', $del)->delete();
            return back()->with('message', 'Record successfully deleted.');
        }
        
        // Fetch budget categories list
        $data['budgetCategories'] = DB::table('budget_categories')
            ->select('id', 'category')
            ->orderBy('category', 'asc')
            ->get();
        
        return view('Project.budgetcategory', $data);
    }

    public function projectBudgetSummary(Request $request)
    {
        $data['projectId'] = $request->input('projectId');
        
        // Handle project selection - reload page with selected project
        if ($request->has('select_project')) {
            $data['projectId'] = $request->input('projectId');
            Session(['selected_project_id_summary' => $data['projectId']]);
        }
        
        // Get selected project from session if not in request
        if (empty($data['projectId'])) {
            $data['projectId'] = Session::get('selected_project_id_summary');
        }
        
        // Fetch projects list
        $data['projects'] = DB::table('projects')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();
        
        // Fetch project budget summary by category for selected project
        $data['budgetSummary'] = collect();
        $data['totalAmount'] = 0;
        
        if (!empty($data['projectId'])) {
            $data['budgetSummary'] = DB::table('project_budget')
                ->leftJoin('budgets', 'project_budget.budgetId', '=', 'budgets.id')
                ->leftJoin('budget_categories', 'budgets.categoryId', '=', 'budget_categories.id')
                ->where('project_budget.projectId', $data['projectId'])
                ->select(
                    'budget_categories.id as categoryId',
                    'budget_categories.category as categoryName',
                    DB::raw('SUM(project_budget.amount) as totalAmount')
                )
                ->groupBy('budget_categories.id', 'budget_categories.category')
                ->orderBy('budget_categories.category', 'asc')
                ->get();
            
            // Calculate grand total
            $data['totalAmount'] = $data['budgetSummary']->sum('totalAmount');
        }
        
        return view('Project.projectbudgetsummary', $data);
    }

    public function client(Request $request)
    {
        $data['name'] = $request->input('name');
        $data['clientAccountId'] = $request->input('clientAccountId');
        $data['id'] = $request->input('id');
        
        if (isset($_POST['addnew'])) {
            $this->validate($request, [
                'name' => 'required|string|unique:clients,name',
                'clientAccountId' => 'nullable|integer',
            ]);

            DB::table('clients')->insert([
                'name' => $data['name'],
                'clientAccountId' => $data['clientAccountId'] ?? null,
            ]);
            return back()->with('message', 'New record successfully added.');
        }
        
        if (isset($_POST['update'])) {
            $this->validate($request, [
                'name' => 'required|string|unique:clients,name,' . $request->input('id'),
                'clientAccountId' => 'nullable|integer',
                'id' => 'required|integer',
            ]);

            DB::table('clients')->where('id', $data['id'])->update([
                'name' => $data['name'],
                'clientAccountId' => $data['clientAccountId'] ?? null,
            ]);
            return back()->with('message', 'Record successfully updated.');
        }
        
        if (isset($_POST['del'])) {
            $del = $request->input('deleteid');
            // Check if client has related records before deletion
            // Add your related table checks here if needed
            // if (DB::table('related_table')->where('clientId', $del)->first()) {
            //     return back()->with('error_message', 'Client has related records. Hence, record cannot be deleted!');
            // }
            DB::table('clients')->where('id', $del)->delete();
            return back()->with('message', 'Record successfully deleted.');
        }
        
        // Fetch clients list with account information
        $data['clients'] = DB::table('clients')
            ->leftJoin('account_charts', 'clients.clientAccountId', '=', 'account_charts.id')
            ->select('clients.id', 'clients.name', 'clients.clientAccountId', 'account_charts.accountdescription as accountName')
            ->orderBy('clients.name', 'asc')
            ->get();
        
        // Fetch account charts for dropdown (using headId 6 as default, adjust if needed)
        $data['accountLookUp'] = $this->AccountLookUpByHeadId(6);
        
        return view('Project.client', $data);
    }
   




    

}
