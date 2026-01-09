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
            // Validate project fields
            $this->validate($request, [
                'projectCode' => 'required|string|unique:projects,projectCode',
                'name' => 'required|string|unique:projects,name',
                'description' => 'required|string',
                'categoryId' => 'nullable|integer',
                'location' => 'nullable|string',
                'status' => 'nullable|string',
                'clientId' => 'nullable|integer',
                'po_poNumber' => 'required|array|min:1',
                'po_poNumber.*' => 'required|string|distinct',
                'po_description' => 'required|array|min:1',
                'po_description.*' => 'required|string',
                'po_qty' => 'required|array|min:1',
                'po_qty.*' => 'required|numeric|min:0',
                'po_unitCost' => 'required|array|min:1',
                'po_unitCost.*' => 'required|numeric|min:0',
                'po_uomId' => 'nullable|array',
                'po_uomId.*' => 'nullable|integer',
                'po_vat' => 'nullable|array',
                'po_vat.*' => 'nullable|numeric|min:0|max:100',
            ]);

            // Create project first
            $projectId = DB::table('projects')->insertGetId([
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

            // Create POs for the project
            $poPoNumbers = $request->input('po_poNumber', []);
            $poDescriptions = $request->input('po_description', []);
            $poUomIds = $request->input('po_uomId', []);
            $poQties = $request->input('po_qty', []);
            $poUnitCosts = $request->input('po_unitCost', []);
            $poVats = $request->input('po_vat', []);

            foreach ($poDescriptions as $index => $description) {
                if (!empty($description) && !empty($poPoNumbers[$index])) {
                    $qty = $poQties[$index] ?? 0;
                    $unitCost = $poUnitCosts[$index] ?? 0;
                    $vat = $poVats[$index] ?? 0;
                    
                    $subcost = $qty * $unitCost;
                    $vatAmount = $subcost * ($vat / 100);
                    $subnet = $subcost + $vatAmount;

                    DB::table('project_po')->insert([
                        'projectId' => $projectId,
                        'poNumber' => $poPoNumbers[$index],
                        'description' => $description,
                        'uomId' => $poUomIds[$index] ?? null,
                        'qty' => $qty,
                        'unitCost' => $unitCost,
                        'subcost' => $subcost,
                        'vat' => $vat,
                        'vatAmount' => $vatAmount,
                        'subnet' => $subnet,
                        'status' => 'Pending',
                        'createdAt' => now(),
                        'updatedAt' => now(),
                        'createdBy' => Auth::user()->id,
                    ]);
                }
            }

            return back()->with('message', 'New project with purchase order(s) successfully added.');
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
        
        // Fetch UOMs list for dropdown
        $data['uoms'] = DB::table('uom')
            ->select('id', 'measurement')
            ->orderBy('measurement', 'asc')
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
        $data['classificationId'] = $request->input('classificationId');
        $data['id'] = $request->input('id');
        
        if (isset($_POST['addnew'])) {
            $this->validate($request, [
                'name' => 'required|string|unique:budgets,name',
                'description' => 'nullable|string',
                'classificationId' => 'required|integer',
            ]);

            DB::table('budgets')->insert([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'classificationId' => $data['classificationId'],
            ]);
            return back()->with('message', 'New record successfully added.');
        }
        
        if (isset($_POST['update'])) {
            $this->validate($request, [
                'name' => 'required|string|unique:budgets,name,' . $request->input('id'),
                'description' => 'nullable|string',
                'classificationId' => 'required|integer',
                'id' => 'required|integer',
            ]);

            DB::table('budgets')->where('id', $data['id'])->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'classificationId' => $data['classificationId'],
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
        
        // Fetch budgets list with classification
        $data['budgets'] = DB::table('budgets')
            ->leftJoin('budget_classifications', 'budgets.classificationId', '=', 'budget_classifications.id')
            ->select('budgets.id', 'budgets.name', 'budgets.description', 'budgets.classificationId', 'budget_classifications.category as categoryName')
            ->orderBy('budgets.name', 'asc')
            ->get();
        
        // Fetch budget classifications for dropdown
        $data['budgetCategories'] = DB::table('budget_classifications')
            ->select('id', 'category')
            ->orderBy('category', 'asc')
            ->get();
        
        return view('Project.budget', $data);
    }

    public function projectBudget(Request $request)
    {
        $data['projectId'] = $request->input('projectId');
        $data['budgetId'] = $request->input('budgetId');
        $data['classificationId'] = $request->input('classificationId');
        $data['unit'] = $request->input('unit');
        $data['unitCost'] = $request->input('unitCost');
        $data['amount'] = $request->input('amount');
        $data['id'] = $request->input('id');
        
        // Handle project selection - reload page with selected project
        if ($request->has('select_project')) {
            $data['projectId'] = $request->input('projectId');
            $data['classificationId'] = $request->input('classificationId');
            Session(['selected_project_id' => $data['projectId']]);
            Session(['selected_classification_id' => $data['classificationId']]);
        }
        
        // Get selected project from session if not in request
        if (empty($data['projectId'])) {
            $data['projectId'] = Session::get('selected_project_id');
        }
        
        // Get selected classification from session if not in request
        if (empty($data['classificationId'])) {
            $data['classificationId'] = Session::get('selected_classification_id');
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
        
        // Fetch budget classifications for dropdown
        $data['budgetCategories'] = DB::table('budget_classifications')
            ->select('id', 'category')
            ->orderBy('category', 'asc')
            ->get();
        
        // Fetch budgets list - filter by classificationId if selected
        $budgetsQuery = DB::table('budgets')
            ->leftJoin('budget_classifications', 'budgets.classificationId', '=', 'budget_classifications.id')
            ->select(
                'budgets.id',
                'budgets.classificationId', 
                'budgets.name as budgetName', 
                'budget_classifications.category as budgetCategoryName', 
                DB::raw('COALESCE(budget_classifications.isMeasure, 0) as isMeasure'),
                'budget_classifications.isMilestone', 
                'budget_classifications.isSubContrator'
            );
        
        // Filter by classificationId if provided
        if (!empty($data['classificationId'])) {
            $budgetsQuery->where('budgets.classificationId', $data['classificationId']);
        }
        
        $data['budgets'] = $budgetsQuery
            ->orderBy('budget_classifications.category', 'asc')
            ->orderBy('budgets.name', 'asc')
            ->get();
        
        // dd($data['budgets']);
        // Fetch project budgets for selected project
        $data['projectBudgets'] = collect();
        if (!empty($data['projectId'])) {
            $data['projectBudgets'] = DB::table('project_budget')
                ->leftJoin('budgets', 'project_budget.budgetId', '=', 'budgets.id')
                ->leftJoin('budget_classifications', 'budgets.classificationId', '=', 'budget_classifications.id')
                ->where('project_budget.projectId', $data['projectId'])
                ->select('project_budget.id', 'project_budget.projectId', 'project_budget.budgetId', 'project_budget.unit', 'project_budget.unitCost', 'project_budget.amount', 'budgets.name as budgetName', 'budgets.classificationId', 'budget_classifications.category as budgetCategoryName')
                ->orderBy('budget_classifications.category', 'asc')
                ->orderBy('budgets.name', 'asc')
                ->get();
        }
        
        return view('Project.projectbudget', $data);
    }

    public function budgetCategory(Request $request)
    {
        $data['category'] = $request->input('category');
        $data['isMeasure'] = $request->input('isMeasure', 0);
        $data['isMilestone'] = $request->input('isMilestone', 0);
        $data['isSubContrator'] = $request->input('isSubContrator', 0);
        $data['id'] = $request->input('id');
        
        if (isset($_POST['addnew'])) {
                $this->validate($request, [
                'category' => 'required|string|unique:budget_classifications,category',
                'isMeasure' => 'nullable|boolean',
                'isMilestone' => 'nullable|boolean',
                'isSubContrator' => 'nullable|boolean',
            ]);

            DB::table('budget_classifications')->insert([
                'category' => $data['category'],
                'isMeasure' => $data['isMeasure'] ? 1 : 0,
                'isMilestone' => $data['isMilestone'] ? 1 : 0,
                'isSubContrator' => $data['isSubContrator'] ? 1 : 0,
            ]);
            return back()->with('message', 'New record successfully added.');
        }
        
        if (isset($_POST['update'])) {
                $this->validate($request, [
                'category' => 'required|string|unique:budget_classifications,category,' . $request->input('id'),
                'isMeasure' => 'nullable|boolean',
                'isMilestone' => 'nullable|boolean',
                'isSubContrator' => 'nullable|boolean',
                'id' => 'required|integer',
            ]);

            DB::table('budget_classifications')->where('id', $data['id'])->update([ 
                'category' => $data['category'],
                'isMeasure' => $data['isMeasure'] ? 1 : 0,
                'isMilestone' => $data['isMilestone'] ? 1 : 0,
                'isSubContrator' => $data['isSubContrator'] ? 1 : 0,
            ]);
            return back()->with('message', 'Record successfully updated.');
        }
        
        if (isset($_POST['del'])) {
            $del = $request->input('deleteid');
            // Check if classification has related records before deletion
            // Add your related table checks here if needed
            if (DB::table('budgets')->where('classificationId', $del)->first()) {
                return back()->with('error_message', 'Classification has related budgets. Hence, record cannot be deleted!');
            }
            DB::table('budget_classifications')->where('id', $del)->delete();
            return back()->with('message', 'Record successfully deleted.');
        }
        
        // Fetch budget classifications list
        $data['budgetCategories'] = DB::table('budget_classifications')
            ->select('id', 'category', 'isMeasure', 'isMilestone', 'isSubContrator')
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
        
        // Fetch project budget summary by classification for selected project
        $data['budgetSummary'] = collect();
        $data['totalAmount'] = 0;
        
        if (!empty($data['projectId'])) {
            $data['budgetSummary'] = DB::table('project_budget')
                ->leftJoin('budgets', 'project_budget.budgetId', '=', 'budgets.id')
                ->leftJoin('budget_classifications', 'budgets.classificationId', '=', 'budget_classifications.id')
                ->where('project_budget.projectId', $data['projectId'])
                ->select(
                    'budget_classifications.id as categoryId',
                    'budget_classifications.category as categoryName',
                    DB::raw('SUM(project_budget.amount) as totalAmount')
                )
                ->groupBy('budget_classifications.id', 'budget_classifications.category')
                ->orderBy('budget_classifications.category', 'asc')
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

    public function projectPo(Request $request)
    {
        $data['projectId'] = $request->input('projectId');
        $data['poNumber'] = $request->input('poNumber');
        $data['description'] = $request->input('description');
        $data['uomId'] = $request->input('uomId');
        $data['qty'] = $request->input('qty');
        $data['unitCost'] = $request->input('unitCost');
        $data['subcost'] = $request->input('subcost');
        $data['vat'] = $request->input('vat');
        $data['vatAmount'] = $request->input('vatAmount');
        $data['subnet'] = $request->input('subnet');
        $data['id'] = $request->input('id');
        
        // Handle project selection - reload page with selected project
        if ($request->has('select_project')) {
            $data['projectId'] = $request->input('projectId');
            Session(['selected_project_po_id' => $data['projectId']]);
        }
        
        // Get selected project from session if not in request
        if (empty($data['projectId'])) {
            $data['projectId'] = Session::get('selected_project_po_id');
        }
        
        if (isset($_POST['addnew'])) {
            $this->validate($request, [
                'projectId' => 'required|integer',
                'poNumber' => 'required|string|unique:project_po,poNumber',
                'description' => 'required|string',
                'uomId' => 'nullable|integer',
                'qty' => 'required|numeric|min:0',
                'unitCost' => 'required|numeric|min:0',
                'vat' => 'nullable|numeric|min:0|max:100',
            ]);

            // Calculate subcost, vatAmount, and subnet
            $qty = $data['qty'];
            $unitCost = $data['unitCost'];
            $vat = $data['vat'] ?? 0;
            
            $subcost = $qty * $unitCost;
            $vatAmount = $subcost * ($vat / 100);
            $subnet = $subcost + $vatAmount;

            DB::table('project_po')->insert([
                'projectId' => $data['projectId'],
                'poNumber' => $data['poNumber'],
                'description' => $data['description'],
                'uomId' => $data['uomId'] ?? null,
                'qty' => $qty,
                'unitCost' => $unitCost,
                'subcost' => $subcost,
                'vat' => $vat,
                'vatAmount' => $vatAmount,
                'subnet' => $subnet,
                'status' => 'Pending', // Default status
                'createdAt' => now(),
                'updatedAt' => now(),
                'createdBy' => Auth::user()->id,
            ]);
            return back()->with('message', 'New PO record successfully added.');
        }
        
        if (isset($_POST['update'])) {
            $this->validate($request, [
                'projectId' => 'required|integer',
                'poNumber' => 'required|string|unique:project_po,poNumber,' . $request->input('id'),
                'description' => 'required|string',
                'uomId' => 'nullable|integer',
                'qty' => 'required|numeric|min:0',
                'unitCost' => 'required|numeric|min:0',
                'vat' => 'nullable|numeric|min:0|max:100',
                'id' => 'required|integer',
            ]);

            // Calculate subcost, vatAmount, and subnet
            $qty = $data['qty'];
            $unitCost = $data['unitCost'];
            $vat = $data['vat'] ?? 0;
            
            $subcost = $qty * $unitCost;
            $vatAmount = $subcost * ($vat / 100);
            $subnet = $subcost + $vatAmount;

            DB::table('project_po')->where('id', $data['id'])->update([
                'poNumber' => $data['poNumber'],
                'description' => $data['description'],
                'uomId' => $data['uomId'] ?? null,
                'qty' => $qty,
                'unitCost' => $unitCost,
                'subcost' => $subcost,
                'vat' => $vat,
                'vatAmount' => $vatAmount,
                'subnet' => $subnet,
                'updatedAt' => now(),
            ]);
            return back()->with('message', 'PO record successfully updated.');
        }
        
        if (isset($_POST['approve'])) {
            $approveId = $request->input('approveid');
            DB::table('project_po')->where('id', $approveId)->update([
                'status' => 'Approved',
                'approvedBy' => Auth::user()->id,
                'updatedAt' => now(),
            ]);
            return back()->with('message', 'PO record successfully approved.');
        }
        
        if (isset($_POST['del'])) {
            $del = $request->input('deleteid');
            DB::table('project_po')->where('id', $del)->delete();
            return back()->with('message', 'PO record successfully deleted.');
        }
        
        // Fetch projects list
        $data['projects'] = DB::table('projects')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();
        
        // Fetch UOMs list for dropdown
        $data['uoms'] = DB::table('uom')
            ->select('id', 'measurement')
            ->orderBy('measurement', 'asc')
            ->get();
        
        // Fetch project POs for selected project with joins
        $data['projectPos'] = collect();
        if (!empty($data['projectId'])) {
            $data['projectPos'] = DB::table('project_po')
                ->leftJoin('uom', 'project_po.uomId', '=', 'uom.id')
                ->leftJoin('users as creator', 'project_po.createdBy', '=', 'creator.id')
                ->leftJoin('users as approver', 'project_po.approvedBy', '=', 'approver.id')
                ->where('project_po.projectId', $data['projectId'])
                ->select(
                    'project_po.id',
                    'project_po.projectId',
                    'project_po.poNumber',
                    'project_po.description',
                    'project_po.uomId',
                    'project_po.qty',
                    'project_po.unitCost',
                    'project_po.subcost',
                    'project_po.vat',
                    'project_po.vatAmount',
                    'project_po.subnet',
                    'project_po.status',
                    'project_po.createdBy',
                    'project_po.approvedBy',
                    'project_po.createdAt',
                    'project_po.updatedAt',
                    'uom.measurement as uomMeasurement',
                    'creator.name as createdByName',
                    'approver.name as approvedByName'
                )
                ->orderBy('project_po.createdAt', 'desc')
                ->get();
        }
        
        return view('Project.projectpo', $data);
    }

    public function uom(Request $request)
    {
        $data['measurement'] = $request->input('measurement');
        $data['id'] = $request->input('id');
        
        if (isset($_POST['addnew'])) {
            $this->validate($request, [
                'measurement' => 'required|string|unique:uom,measurement',
            ]);

            DB::table('uom')->insert([
                'measurement' => $data['measurement'],
            ]);
            return back()->with('message', 'New record successfully added.');
        }
        
        if (isset($_POST['update'])) {
            $this->validate($request, [
                'measurement' => 'required|string|unique:uom,measurement,' . $request->input('id'),
                'id' => 'required|integer',
            ]);

            DB::table('uom')->where('id', $data['id'])->update([
                'measurement' => $data['measurement'],
            ]);
            return back()->with('message', 'Record successfully updated.');
        }
        
        if (isset($_POST['del'])) {
            $del = $request->input('deleteid');
            // Get the measurement value before deletion
            $uomRecord = DB::table('uom')->where('id', $del)->first();
            
            // Check if UOM has related records before deletion
            // Check if any project_po uses this measurement
            if ($uomRecord && DB::table('project_po')->where('uom', $uomRecord->measurement)->first()) {
                return back()->with('error_message', 'UOM has related purchase orders. Hence, record cannot be deleted!');
            }
            DB::table('uom')->where('id', $del)->delete();
            return back()->with('message', 'Record successfully deleted.');
        }
        
        // Fetch UOM list
        $data['uoms'] = DB::table('uom')
            ->select('id', 'measurement')
            ->orderBy('measurement', 'asc')
            ->get();
        
        return view('Project.uom', $data);
    }

    public function paymentMilestone(Request $request)
    {
        $data['projectId'] = $request->input('projectId');
        $data['milestone'] = $request->input('milestone');
        $data['percentage'] = $request->input('percentage');
        $data['rank'] = $request->input('rank');
        $data['id'] = $request->input('id');
        
        // Handle project selection - reload page with selected project
        if ($request->has('select_project')) {
            $data['projectId'] = $request->input('projectId');
            Session(['selected_payment_milestone_project_id' => $data['projectId']]);
        }
        
        // Get selected project from session if not in request
        if (empty($data['projectId'])) {
            $data['projectId'] = Session::get('selected_payment_milestone_project_id');
        }
        
        if (isset($_POST['addnew'])) {
            $this->validate($request, [
                'projectId' => 'required|integer',
                'milestone' => 'required|string',
                'percentage' => 'required|numeric|min:0|max:100',
                'rank' => 'required|integer|min:1',
            ]);

            // Check total percentage for this project
            $existingMilestones = DB::table('payment_milestone')
                ->where('projectId', $data['projectId'])
                ->sum('percentage');
            
            $totalPercentage = $existingMilestones + $data['percentage'];
            
            if ($totalPercentage > 100) {
                return back()->with('error_message', 'Total percentage cannot exceed 100%. Current total: ' . $existingMilestones . '%, Adding: ' . $data['percentage'] . '% = ' . $totalPercentage . '%');
            }

            DB::table('payment_milestone')->insert([
                'projectId' => $data['projectId'],
                'milestone' => $data['milestone'],
                'percentage' => $data['percentage'],
                'rank' => $data['rank'],
                'createdAt' => now(),
                'updatedAt' => now(),
            ]);
            return back()->with('message', 'New payment milestone successfully added.');
        }
        
        if (isset($_POST['update'])) {
            $this->validate($request, [
                'projectId' => 'required|integer',
                'milestone' => 'required|string',
                'percentage' => 'required|numeric|min:0|max:100',
                'rank' => 'required|integer|min:1',
                'id' => 'required|integer',
            ]);

            // Get current milestone percentage
            $currentMilestone = DB::table('payment_milestone')
                ->where('id', $data['id'])
                ->first();
            
            // Check total percentage for this project (excluding current record)
            $existingMilestones = DB::table('payment_milestone')
                ->where('projectId', $data['projectId'])
                ->where('id', '!=', $data['id'])
                ->sum('percentage');
            
            $totalPercentage = $existingMilestones + $data['percentage'];
            
            if ($totalPercentage > 100) {
                return back()->with('error_message', 'Total percentage cannot exceed 100%. Current total (excluding this milestone): ' . $existingMilestones . '%, New percentage: ' . $data['percentage'] . '% = ' . $totalPercentage . '%');
            }

            DB::table('payment_milestone')->where('id', $data['id'])->update([
                'milestone' => $data['milestone'],
                'percentage' => $data['percentage'],
                'rank' => $data['rank'],
                'updatedAt' => now(),
            ]);
            return back()->with('message', 'Payment milestone successfully updated.');
        }
        
        if (isset($_POST['del'])) {
            $del = $request->input('deleteid');
            DB::table('payment_milestone')->where('id', $del)->delete();
            return back()->with('message', 'Payment milestone successfully deleted.');
        }
        
        // Fetch projects list
        $data['projects'] = DB::table('projects')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();
        
        // Fetch payment milestones for selected project
        $data['paymentMilestones'] = collect();
        $data['totalPercentage'] = 0;
        if (!empty($data['projectId'])) {
            $data['paymentMilestones'] = DB::table('payment_milestone')
                ->where('projectId', $data['projectId'])
                ->select('id', 'milestone', 'percentage', 'rank', 'projectId')
                ->orderBy('rank', 'asc')
                ->get();
            
            $data['totalPercentage'] = $data['paymentMilestones']->sum('percentage');
        }
        
        return view('Project.paymentmilestone', $data);
    }

    public function fundDisbursement(Request $request)
    {
        $data['projectId'] = $request->input('projectId');
        $data['budgetId'] = $request->input('budgetId');
        $data['paymentMilestoneId'] = $request->input('paymentMilestoneId');
        $data['debit'] = $request->input('debit');
        $data['transactionDate'] = $request->input('transactionDate');
        $data['id'] = $request->input('id');
        
        // Handle project selection - reload page with selected project
        if ($request->has('select_project')) {
            $data['projectId'] = $request->input('projectId');
            Session(['selected_fund_disbursement_project_id' => $data['projectId']]);
        }
        
        // Get selected project from session if not in request
        if (empty($data['projectId'])) {
            $data['projectId'] = Session::get('selected_fund_disbursement_project_id');
        }
        
        if (isset($_POST['addnew'])) {
            $this->validate($request, [
                'projectId' => 'required|integer',
                'budgetId' => 'required|integer',
                'paymentMilestoneId' => 'required|integer',
                'debit' => 'required|numeric|min:0',
                'transactionDate' => 'required|date',
            ]);

            DB::table('project_expense')->insert([
                'projectId' => $data['projectId'],
                'budgetId' => $data['budgetId'],
                'paymentMilestoneId' => $data['paymentMilestoneId'],
                'debit' => $data['debit'],
                'credit' => 0,
                'transactionDate' => $data['transactionDate'],
                'status' => 'Pending',
                'createdBy' => Auth::user()->id,
                'createdAt' => now(),
                'updatedAt' => now(),
            ]);
            return back()->with('message', 'New fund disbursement successfully added.');
        }
        
        if (isset($_POST['update'])) {
            $this->validate($request, [
                'projectId' => 'required|integer',
                'budgetId' => 'required|integer',
                'paymentMilestoneId' => 'required|integer',
                'debit' => 'required|numeric|min:0',
                'transactionDate' => 'required|date',
                'id' => 'required|integer',
            ]);

            DB::table('project_expense')->where('id', $data['id'])->update([
                'budgetId' => $data['budgetId'],
                'paymentMilestoneId' => $data['paymentMilestoneId'],
                'debit' => $data['debit'],
                'transactionDate' => $data['transactionDate'],
                'updatedAt' => now(),
            ]);
            return back()->with('message', 'Fund disbursement successfully updated.');
        }
        
        if (isset($_POST['approve'])) {
            $approveId = $request->input('approveid');
            DB::table('project_expense')->where('id', $approveId)->update([
                'status' => 'Approved',
                'approvedBy' => Auth::user()->id,
                'approvedAt' => now(),
                'updatedAt' => now(),
            ]);
            return back()->with('message', 'Fund disbursement successfully approved.');
        }
        
        if (isset($_POST['del'])) {
            $del = $request->input('deleteid');
            DB::table('project_expense')->where('id', $del)->delete();
            return back()->with('message', 'Fund disbursement successfully deleted.');
        }
        
        // Fetch projects list
        $data['projects'] = DB::table('projects')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();
        
        // Fetch budgets associated with projectId where classification isMilestone = 1
        $data['budgets'] = collect();
        if (!empty($data['projectId'])) {
            $data['budgets'] = DB::table('project_budget')
                ->leftJoin('budgets', 'project_budget.budgetId', '=', 'budgets.id')
                ->leftJoin('budget_classifications', 'budgets.classificationId', '=', 'budget_classifications.id')
                ->where('project_budget.projectId', $data['projectId'])
                ->where('budget_classifications.isMilestone', 1)
                ->select('budgets.id', 'budgets.name as budgetName', 'budget_classifications.category as budgetCategoryName')
                ->orderBy('budget_classifications.category', 'asc')
                ->orderBy('budgets.name', 'asc')
                ->get();
        }
        
        // Fetch payment milestones for selected project
        $data['paymentMilestones'] = collect();
        if (!empty($data['projectId'])) {
            $data['paymentMilestones'] = DB::table('payment_milestone')
                ->where('projectId', $data['projectId'])
                ->select('id', 'milestone', 'percentage', 'rank', 'projectId')
                ->orderBy('rank', 'asc')
                ->get();
        }
        
        // Fetch fund disbursements for selected project
        $data['fundDisbursements'] = collect();
        $data['totalDisbursed'] = 0;
        if (!empty($data['projectId'])) {
            $data['fundDisbursements'] = DB::table('project_expense')
                ->leftJoin('budgets', 'project_expense.budgetId', '=', 'budgets.id')
                ->leftJoin('payment_milestone', 'project_expense.paymentMilestoneId', '=', 'payment_milestone.id')
                ->leftJoin('budget_classifications', 'budgets.classificationId', '=', 'budget_classifications.id')
                ->where('project_expense.projectId', $data['projectId'])
                ->select(
                    'project_expense.id',
                    'project_expense.budgetId',
                    'project_expense.paymentMilestoneId',
                    'project_expense.debit',
                    'project_expense.status',
                    'project_expense.transactionDate',
                    'project_expense.createdAt',
                    'project_expense.approvedAt',
                    'budgets.name as budgetName',
                    'budget_classifications.category as budgetCategoryName',
                    'payment_milestone.milestone',
                    'payment_milestone.rank as milestoneRank'
                )
                ->orderBy('budgets.name', 'asc')
                ->orderBy('payment_milestone.rank', 'asc')
                ->get();
            
            $data['totalDisbursed'] = $data['fundDisbursements']->sum('debit');
        }
        
        return view('Project.funddisbursement', $data);
    }

    public function budgetUtilizationReport(Request $request)
    {
        $data['projectId'] = $request->input('projectId');
        
        // Handle project selection - reload page with selected project
        if ($request->has('select_project')) {
            $data['projectId'] = $request->input('projectId');
            Session(['selected_budget_utilization_project_id' => $data['projectId']]);
        }
        
        // Get selected project from session if not in request
        if (empty($data['projectId'])) {
            $data['projectId'] = Session::get('selected_budget_utilization_project_id');
        }
        
        // Fetch projects list
        $data['projects'] = DB::table('projects')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();
        
        // Fetch budget utilization report by classification for selected project
        $data['budgetUtilization'] = collect();
        $data['totalBudgeted'] = 0;
        $data['totalExpense'] = 0;
        
        if (!empty($data['projectId'])) {
            // First, get budgeted amounts grouped by classification
            $budgetedByClassification = DB::table('project_budget')
                ->leftJoin('budgets', 'project_budget.budgetId', '=', 'budgets.id')
                ->leftJoin('budget_classifications', 'budgets.classificationId', '=', 'budget_classifications.id')
                ->where('project_budget.projectId', $data['projectId'])
                ->select(
                    'budget_classifications.id as classificationId',
                    'budget_classifications.category as classificationName',
                    DB::raw('SUM(project_budget.amount) as budgeted')
                )
                ->groupBy('budget_classifications.id', 'budget_classifications.category')
                ->get();
            
            // Get expenses (approved only) grouped by classification
            $expensesByClassification = DB::table('project_expense')
                ->leftJoin('budgets', 'project_expense.budgetId', '=', 'budgets.id')
                ->leftJoin('budget_classifications', 'budgets.classificationId', '=', 'budget_classifications.id')
                ->where('project_expense.projectId', $data['projectId'])
                ->where('project_expense.status', 'Approved')
                ->select(
                    'budget_classifications.id as classificationId',
                    DB::raw('SUM(project_expense.debit) as expense')
                )
                ->groupBy('budget_classifications.id')
                ->get()
                ->keyBy('classificationId');
            
            // Combine the data
            $data['budgetUtilization'] = $budgetedByClassification->map(function($item) use ($expensesByClassification) {
                $expense = $expensesByClassification->get($item->classificationId);
                $item->expense = $expense ? $expense->expense : 0;
                $item->percentageUtilized = $item->budgeted > 0 
                    ? round(($item->expense / $item->budgeted) * 100, 2) 
                    : 0;
                return $item;
            });
            
            // Calculate totals
            $data['totalBudgeted'] = $data['budgetUtilization']->sum('budgeted');
            $data['totalExpense'] = $data['budgetUtilization']->sum('expense');
            $data['totalPercentageUtilized'] = $data['totalBudgeted'] > 0 
                ? round(($data['totalExpense'] / $data['totalBudgeted']) * 100, 2) 
                : 0;
        }
        
        return view('Project.budgetutilizationreport', $data);
    }

    public function projectBudgetMilestoneReport(Request $request)
    {
        $data['projectId'] = $request->input('projectId');
        
        // Handle project selection - reload page with selected project
        if ($request->has('select_project')) {
            $data['projectId'] = $request->input('projectId');
            Session(['selected_project_budget_milestone_project_id' => $data['projectId']]);
        }
        
        // Get selected project from session if not in request
        if (empty($data['projectId'])) {
            $data['projectId'] = Session::get('selected_project_budget_milestone_project_id');
        }
        
        // Fetch projects list
        $data['projects'] = DB::table('projects')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();
        
        $data['projectBudgets'] = collect();
        $data['milestones'] = collect();
        $data['totalBudgeted'] = 0;
        $data['totalActualExpense'] = 0;
        
        if (!empty($data['projectId'])) {
            // Fetch milestones for the selected project, ordered by rank
            $data['milestones'] = DB::table('payment_milestone')
                ->where('projectId', $data['projectId'])
                ->select('id', 'milestone', 'percentage', 'rank', 'projectId')
                ->orderBy('rank', 'asc')
                ->get();
            
            // Fetch project budgets where classification isMilestone = 1
            $budgets = DB::table('project_budget')
                ->leftJoin('budgets', 'project_budget.budgetId', '=', 'budgets.id')
                ->leftJoin('budget_classifications', 'budgets.classificationId', '=', 'budget_classifications.id')
                ->where('project_budget.projectId', $data['projectId'])
                ->where('budget_classifications.isMilestone', 1)
                ->select(
                    'project_budget.id',
                    'project_budget.budgetId',
                    'project_budget.unit',
                    'project_budget.unitCost',
                    'project_budget.amount',
                    'budgets.name as budgetName'
                )
                ->orderBy('budgets.name', 'asc')
                ->get();
            
            // Fetch all approved expenses for this project, grouped by budget and milestone
            $expenses = DB::table('project_expense')
                ->where('project_expense.projectId', $data['projectId'])
                ->where('project_expense.status', 'Approved')
                ->select(
                    'project_expense.budgetId',
                    'project_expense.paymentMilestoneId',
                    DB::raw('SUM(project_expense.debit) as totalExpense')
                )
                ->groupBy('project_expense.budgetId', 'project_expense.paymentMilestoneId')
                ->get();
            
            // Create a keyed collection for quick lookup: budgetId_milestoneId => expense
            $expenseMap = [];
            foreach ($expenses as $expense) {
                $key = $expense->budgetId . '_' . $expense->paymentMilestoneId;
                $expenseMap[$key] = $expense->totalExpense;
            }
            
            // Process each budget
            $data['projectBudgets'] = $budgets->map(function($budget) use ($data, $expenseMap) {
                $budget->milestoneAmounts = [];
                $budget->milestoneExpenses = [];
                $budget->totalActualExpense = 0;
                
                // Calculate milestone amounts and fetch actual expenses
                foreach ($data['milestones'] as $milestone) {
                    // Calculate milestone amount (percentage of budget amount)
                    $milestoneAmount = ($budget->amount * $milestone->percentage) / 100;
                    $budget->milestoneAmounts[$milestone->id] = [
                        'amount' => $milestoneAmount,
                        'percentage' => $milestone->percentage,
                        'milestone' => $milestone->milestone
                    ];
                    
                    // Get actual expense for this budget and milestone
                    $expenseKey = $budget->budgetId . '_' . $milestone->id;
                    $actualExpense = isset($expenseMap[$expenseKey]) ? $expenseMap[$expenseKey] : 0;
                    $budget->milestoneExpenses[$milestone->id] = $actualExpense;
                    $budget->totalActualExpense += $actualExpense;
                }
                
                return $budget;
            });
            
            // Calculate totals after processing all budgets
            $data['totalBudgeted'] = $data['projectBudgets']->sum('amount');
            $data['totalActualExpense'] = $data['projectBudgets']->sum('totalActualExpense');
        }
        
        return view('Project.projectbudgetmilestonereport', $data);
    }

    public function vendor(Request $request)
    {
        $data['name'] = $request->input('name');
        $data['description'] = $request->input('description');
        $data['accountId'] = $request->input('accountId');
        $data['status'] = $request->input('status');
        $data['id'] = $request->input('id');
        
        if (isset($_POST['addnew'])) {
            $this->validate($request, [
                'name' => 'required|string|unique:budgets,name',
                'description' => 'nullable|string',
                'accountId' => 'nullable|integer',
                'status' => 'nullable|string',
            ]);

            DB::table('budgets')->insert([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'classificationId' => 1,
                'isVendor' => 1,
                'accountId' => $data['accountId'] ?? null,
                'status' => $data['status'] ?? 'Active',
                'createdAt' => now(),
                'updatedAt' => now(),
            ]);
            return back()->with('message', 'New vendor successfully added.');
        }
        
        if (isset($_POST['update'])) {
            $this->validate($request, [
                'name' => 'required|string|unique:budgets,name,' . $request->input('id'),
                'description' => 'nullable|string',
                'accountId' => 'nullable|integer',
                'status' => 'nullable|string',
                'id' => 'required|integer',
            ]);

            DB::table('budgets')->where('id', $data['id'])->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'accountId' => $data['accountId'] ?? null,
                'status' => $data['status'] ?? 'Active',
                'updatedAt' => now(),
            ]);
            return back()->with('message', 'Vendor successfully updated.');
        }
        
        if (isset($_POST['del'])) {
            $del = $request->input('deleteid');
            // Check if vendor has related records before deletion
            // Add your related table checks here if needed
            // if (DB::table('related_table')->where('vendorId', $del)->first()) {
            //     return back()->with('error_message', 'Vendor has related records. Hence, record cannot be deleted!');
            // }
            DB::table('budgets')->where('id', $del)->delete();
            return back()->with('message', 'Vendor successfully deleted.');
        }
        
        // Fetch vendors list (classificationId = 1 and isVendor = 1)
        $data['vendors'] = DB::table('budgets')
            ->leftJoin('account_charts', 'budgets.accountId', '=', 'account_charts.id')
            ->where('budgets.classificationId', 1)
            ->where('budgets.isVendor', 1)
            ->select(
                'budgets.id',
                'budgets.name',
                'budgets.description',
                'budgets.accountId',
                'budgets.status',
                'account_charts.accountdescription as accountName',
                'account_charts.accountno as accountNo'
            )
            ->orderBy('budgets.name', 'asc')
            ->get();
        
        // Fetch account charts for dropdown
        $data['accountLookUp'] = $this->AccountLookUpByHeadId(6);
        
        return view('Project.vendor', $data);
    }
   




    

}
