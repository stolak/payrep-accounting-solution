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
        $data['revenue_accountId'] = $request->input('revenue_accountId');
        $data['clientId'] = $request->input('clientId');
        $data['clientAccountId'] = $request->input('clientAccountId');
        $data['expenseClassificationLedger'] = $request->input('expenseClassificationLedger', []);
        
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
                'clientAccountId' => 'nullable|integer|exists:account_charts,id',
                'revenue_accountId' => 'nullable|integer',
                'po_poNumber' => 'required|array|min:1',
                'po_poNumber.*' => 'required|string|distinct',
                'po_description' => 'required|array|min:1',
                'po_description.*' => 'required|string',
                'po_vat' => 'nullable|array',
                'po_vat.*' => 'nullable|numeric|min:0|max:100',
                'po_item_description' => 'required|array|min:1',
                'po_item_description.*' => 'required|array|min:1',
                'po_item_description.*.*' => 'required|string',
                'po_item_qty' => 'required|array|min:1',
                'po_item_qty.*' => 'required|array|min:1',
                'po_item_qty.*.*' => 'required|numeric|min:0',
                'po_item_unitCost' => 'required|array|min:1',
                'po_item_unitCost.*' => 'required|array|min:1',
                'po_item_unitCost.*.*' => 'required|numeric|min:0',
                'po_item_uomId' => 'nullable|array',
                'po_item_uomId.*' => 'nullable|array',
                'po_item_uomId.*.*' => 'nullable|integer',
            ]);

            // Validate that client has the selected project category
            if (!empty($data['clientId']) && !empty($data['categoryId'])) {
                $clientCategoryExists = DB::table('client_project_categories')
                    ->where('clientId', $data['clientId'])
                    ->where('project_categoryId', $data['categoryId'])
                    ->exists();
                
                if (!$clientCategoryExists) {
                    $clientName = DB::table('clients')->where('id', $data['clientId'])->value('name');
                    $categoryName = DB::table('project_categories')->where('id', $data['categoryId'])->value('category');
                    return back()->withInput()->with('error_message', "The selected client '{$clientName}' does not have the project category '{$categoryName}' assigned. Please assign the category to the client first.");
                }
            }

            if (!empty($data['clientAccountId'])) {
                if (empty($data['clientId'])) {
                    return back()->withInput()->with('error_message', 'Please select client before selecting client ledger.');
                }

                $clientLedgerExists = DB::table('client_ledgers')
                    ->where('clientId', $data['clientId'])
                    ->where('clientAccountId', $data['clientAccountId'])
                    ->exists();

                if (!$clientLedgerExists) {
                    return back()->withInput()->with('error_message', 'The selected client ledger does not belong to the selected client.');
                }
            }

            // Validate project category expense classifications and their selected ledgers
            $categoryClassifications = collect();
            if (!empty($data['categoryId'])) {
                $categoryClassifications = DB::table('project_categories_expense_classification')
                    ->where('project_categoryId', $data['categoryId'])
                    ->pluck('expense_classificationId')
                    ->unique()
                    ->values();
            }

            if ($categoryClassifications->count() > 0) {
                if (!is_array($data['expenseClassificationLedger'])) {
                    return back()->withInput()->with('error_message', 'Expense classification ledgers are required.');
                }

                foreach ($categoryClassifications as $classificationId) {
                    $expenseLedgerId = $data['expenseClassificationLedger'][$classificationId] ?? null;
                    if (empty($expenseLedgerId)) {
                        $classificationName = DB::table('budget_classifications')->where('id', $classificationId)->value('category');
                        return back()->withInput()->with('error_message', "Please select expense ledger for '{$classificationName}'.");
                    }

                    if (!DB::table('account_charts')->where('id', $expenseLedgerId)->exists()) {
                        return back()->withInput()->with('error_message', "Invalid expense ledger selected for classification ID {$classificationId}.");
                    }
                }
            }

            // Create project first
            $projectId = DB::table('projects')->insertGetId([
                'projectCode' => $data['projectCode'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'categoryId' => $data['categoryId'] ?? null,
                'location' => $data['location'] ?? null,
                'status' => $data['status'] ?? "Active",
                'expenseAccountId' => $data['expenseAccountId'] ?? null,
                'revenue_accountId' => $data['revenue_accountId'] ?? null,
                'clientId' => $data['clientId'] ?? null,
                'clientAccountId' => $data['clientAccountId'] ?? null,
                'createdAt' => now(),
                'updatedAt' => now(),
                'createdBy' => Auth::user()->id,
            ]);

            // Insert project expense ledgers based on selected category classifications
            if (!empty($data['categoryId']) && !empty($data['expenseClassificationLedger']) && is_array($data['expenseClassificationLedger'])) {
                $allowedClassifications = DB::table('project_categories_expense_classification')
                    ->where('project_categoryId', $data['categoryId'])
                    ->pluck('expense_classificationId')
                    ->unique()
                    ->toArray();

                foreach ($allowedClassifications as $classificationId) {
                    $expenseLedgerId = $data['expenseClassificationLedger'][$classificationId] ?? null;
                    if (!empty($expenseLedgerId)) {
                        DB::table('project_expense_ledger')->insert([
                            'projectId' => $projectId,
                            'project_categoryId' => $data['categoryId'],
                            'classificationId' => $classificationId,
                            'expenseAccountId' => $expenseLedgerId,
                        ]);
                    }
                }
            }

            // Automatically create payment milestones based on project category
            if (!empty($data['categoryId'])) {
                $categoryMilestones = DB::table('project_category_payment_milestone')
                    ->where('projectCategoryId', $data['categoryId'])
                    ->select('milestone', 'percentage', 'rank')
                    ->orderBy('rank', 'asc')
                    ->get();
                
                foreach ($categoryMilestones as $categoryMilestone) {
                    DB::table('payment_milestone')->insert([
                        'projectId' => $projectId,
                        'milestone' => $categoryMilestone->milestone,
                        'percentage' => $categoryMilestone->percentage,
                        'rank' => $categoryMilestone->rank,
                        'createdAt' => now(),
                        'updatedAt' => now(),
                    ]);
                }
            }

            // Create POs for the project with line items
            $poPoNumbers = $request->input('po_poNumber', []);
            $poDescriptions = $request->input('po_description', []);
            $poVats = $request->input('po_vat', []);
            
            // Get line items for each PO
            $poItemDescriptions = $request->input('po_item_description', []);
            $poItemUomIds = $request->input('po_item_uomId', []);
            $poItemQties = $request->input('po_item_qty', []);
            $poItemUnitCosts = $request->input('po_item_unitCost', []);

            foreach ($poPoNumbers as $poIndex => $poNumber) {
                if (!empty($poNumber) && !empty($poDescriptions[$poIndex])) {
                    $poDescription = $poDescriptions[$poIndex];
                    $vat = $poVats[$poIndex] ?? 0;
                    
                    // Calculate totals from line items
                    $totalSubcost = 0;
                    $itemDescriptions = $poItemDescriptions[$poIndex] ?? [];
                    $itemUomIds = $poItemUomIds[$poIndex] ?? [];
                    $itemQties = $poItemQties[$poIndex] ?? [];
                    $itemUnitCosts = $poItemUnitCosts[$poIndex] ?? [];
                    
                    // Calculate total subcost from all line items
                    foreach ($itemDescriptions as $itemIndex => $itemDescription) {
                        if (!empty($itemDescription)) {
                            $qty = $itemQties[$itemIndex] ?? 0;
                            $unitCost = $itemUnitCosts[$itemIndex] ?? 0;
                            $totalSubcost += $qty * $unitCost;
                        }
                    }
                    
                    $vatAmount = $totalSubcost * ($vat / 100);
                    $subnet = $totalSubcost + $vatAmount;

                    // Create PO header
                    $poId = DB::table('project_po')->insertGetId([
                        'projectId' => $projectId,
                        'poNumber' => $poNumber,
                        'description' => $poDescription,
                        'vat' => $vat,
                        'vatAmount' => $vatAmount,
                        'subnet' => $subnet,
                        'status' => 'Pending',
                        'createdAt' => now(),
                        'updatedAt' => now(),
                        'createdBy' => Auth::user()->id,
                    ]);
                    
                    // Create PO line items
                    foreach ($itemDescriptions as $itemIndex => $itemDescription) {
                        if (!empty($itemDescription)) {
                            $qty = $itemQties[$itemIndex] ?? 0;
                            $unitCost = $itemUnitCosts[$itemIndex] ?? 0;
                            $subcost = $qty * $unitCost;
                            
                            DB::table('project_po_item')->insert([
                                'poId' => $poId,
                                'description' => $itemDescription,
                                'uomId' => $itemUomIds[$itemIndex] ?? null,
                                'qty' => $qty,
                                'unitCost' => $unitCost,
                                'subcost' => $subcost,
                                'createdAt' => now(),
                                'updatedAt' => now(),
                            ]);
                        }
                    }
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
                'clientAccountId' => 'nullable|integer|exists:account_charts,id',
                'revenue_accountId' => 'nullable|integer',
                'id' => 'required|integer',
            ]);

            // Validate that client has the selected project category
            if (!empty($data['clientId']) && !empty($data['categoryId'])) {
                $clientCategoryExists = DB::table('client_project_categories')
                    ->where('clientId', $data['clientId'])
                    ->where('project_categoryId', $data['categoryId'])
                    ->exists();
                
                if (!$clientCategoryExists) {
                    $clientName = DB::table('clients')->where('id', $data['clientId'])->value('name');
                    $categoryName = DB::table('project_categories')->where('id', $data['categoryId'])->value('category');
                    return back()->withInput()->with('error_message', "The selected client '{$clientName}' does not have the project category '{$categoryName}' assigned. Please assign the category to the client first.");
                }
            }

            if (!empty($data['clientAccountId'])) {
                if (empty($data['clientId'])) {
                    return back()->withInput()->with('error_message', 'Please select client before selecting client ledger.');
                }

                $clientLedgerExists = DB::table('client_ledgers')
                    ->where('clientId', $data['clientId'])
                    ->where('clientAccountId', $data['clientAccountId'])
                    ->exists();

                if (!$clientLedgerExists) {
                    return back()->withInput()->with('error_message', 'The selected client ledger does not belong to the selected client.');
                }
            }

            // Validate project category expense classifications and their selected ledgers
            $categoryClassifications = collect();
            if (!empty($data['categoryId'])) {
                $categoryClassifications = DB::table('project_categories_expense_classification')
                    ->where('project_categoryId', $data['categoryId'])
                    ->pluck('expense_classificationId')
                    ->unique()
                    ->values();
            }

            if ($categoryClassifications->count() > 0) {
                if (!is_array($data['expenseClassificationLedger'])) {
                    return back()->withInput()->with('error_message', 'Expense classification ledgers are required.');
                }

                foreach ($categoryClassifications as $classificationId) {
                    $expenseLedgerId = $data['expenseClassificationLedger'][$classificationId] ?? null;
                    if (empty($expenseLedgerId)) {
                        $classificationName = DB::table('budget_classifications')->where('id', $classificationId)->value('category');
                        return back()->withInput()->with('error_message', "Please select expense ledger for '{$classificationName}'.");
                    }

                    if (!DB::table('account_charts')->where('id', $expenseLedgerId)->exists()) {
                        return back()->withInput()->with('error_message', "Invalid expense ledger selected for classification ID {$classificationId}.");
                    }
                }
            }

            DB::table('projects')->where('id', $data['id'])->update([
                'projectCode' => $data['projectCode'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'categoryId' => $data['categoryId'] ?? null,
                'location' => $data['location'] ?? null,
                'status' => $data['status'] ?? "Active",
                'expenseAccountId' => $data['expenseAccountId'] ?? null,
                'revenue_accountId' => $data['revenue_accountId'] ?? null,
                'clientId' => $data['clientId'] ?? null,
                'clientAccountId' => $data['clientAccountId'] ?? null,
                // 'incomeAccountId' => $data['incomeAccountId'] ?? null,
                'updatedAt' => now(),
            ]);

            DB::table('project_expense_ledger')->where('projectId', $data['id'])->delete();
            if (!empty($data['categoryId']) && !empty($data['expenseClassificationLedger']) && is_array($data['expenseClassificationLedger'])) {
                $allowedClassifications = DB::table('project_categories_expense_classification')
                    ->where('project_categoryId', $data['categoryId'])
                    ->pluck('expense_classificationId')
                    ->unique()
                    ->toArray();

                foreach ($allowedClassifications as $classificationId) {
                    $expenseLedgerId = $data['expenseClassificationLedger'][$classificationId] ?? null;
                    if (!empty($expenseLedgerId)) {
                        DB::table('project_expense_ledger')->insert([
                            'projectId' => $data['id'],
                            'project_categoryId' => $data['categoryId'],
                            'classificationId' => $classificationId,
                            'expenseAccountId' => $expenseLedgerId,
                        ]);
                    }
                }
            }
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
            ->select(
                'projects.id',
                'projectCode',
                'projects.clientId',
                'projects.clientAccountId',
                'projects.expenseAccountId',
                'projects.revenue_accountId',
                'projects.name',
                'description',
                'categoryId',
                'location',
                'projects.status',
                'projects.createdAt',
                'projects.updatedAt',
                'projects.createdBy',
                'expense_account.accountdescription as expenseAccountName',
                'revenue_account.accountdescription as revenueAccountName',
                'client_ledger.accountdescription as clientAccountName',
                'project_categories.category as categoryName',
                'clients.name as clientName'
            )
            ->leftJoin('account_charts as expense_account', 'projects.expenseAccountId', '=', 'expense_account.id')
            ->leftJoin('account_charts as revenue_account', 'projects.revenue_accountId', '=', 'revenue_account.id')
            ->leftJoin('account_charts as client_ledger', 'projects.clientAccountId', '=', 'client_ledger.id')
            ->leftJoin('project_categories', 'projects.categoryId', '=', 'project_categories.id')
            ->leftJoin('clients', 'projects.clientId', '=', 'clients.id')
            ->orderBy('createdAt', 'desc')
            ->get();

        foreach ($data['projects'] as $project) {
            $project->expenseClassificationLedger = DB::table('project_expense_ledger')
                ->where('projectId', $project->id)
                ->select('classificationId', 'expenseAccountId')
                ->get()
                ->pluck('expenseAccountId', 'classificationId');
        }
        
        // Fetch categories if needed (assuming there's a categories table)
         $data['projectCategories'] = DB::table('project_categories')
            ->select('id', 'category')
            ->orderBy('category', 'asc')
            ->get();
        $data['accountLookUp'] = $this->AccountLookUpBysubHeadId(env('PROJECT_EXPENSE_ID'));
        $data['revenueLookUp'] = $this->AccountLookUpBysubHeadId(env('PROJECT_REVENUE_ID'));
        
        // Fetch clients list for dropdown
        $data['clients'] = DB::table('clients')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        $data['clientLedgers'] = DB::table('client_ledgers')
            ->join('account_charts', 'client_ledgers.clientAccountId', '=', 'account_charts.id')
            ->select(
                'client_ledgers.clientId',
                'client_ledgers.clientAccountId',
                'account_charts.accountdescription',
                'account_charts.accountno'
            )
            ->orderBy('account_charts.accountdescription', 'asc')
            ->get();

        $data['categoryExpenseClassifications'] = DB::table('project_categories_expense_classification')
            ->join('budget_classifications', 'project_categories_expense_classification.expense_classificationId', '=', 'budget_classifications.id')
            ->select(
                'project_categories_expense_classification.project_categoryId',
                'project_categories_expense_classification.expense_classificationId as classificationId',
                'budget_classifications.category as classificationName'
            )
            ->orderBy('budget_classifications.category', 'asc')
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
        // Get classificationId from query string or form input
        $data['classificationId'] = $request->input('classificationId') ?? $request->query('classificationId') ?? '';
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
        
        // Fetch budgets list with classification, filtered by classificationId if provided
        $budgetsQuery = DB::table('budgets')
            ->leftJoin('budget_classifications', 'budgets.classificationId', '=', 'budget_classifications.id')
            ->select('budgets.id', 'budgets.name', 'budgets.description', 'budgets.classificationId', 'budget_classifications.category as categoryName');
        
        // Filter by classificationId if provided
        if (!empty($data['classificationId'])) {
            $budgetsQuery->where('budgets.classificationId', $data['classificationId']);
        }
        
        $data['budgets'] = $budgetsQuery->orderBy('budgets.name', 'asc')->get();
        
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
        $data['note'] = $request->input('note');
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
                'note' => $data['note'] ?? null,
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
                'note' => $data['note'] ?? null,
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
            ->select('id', 'name', 'projectCode', 'categoryId')
            ->orderBy('name', 'asc')
            ->get();
        
        // Fetch budget classifications for dropdown
        // Filter by project category's associated expense classifications if project is selected
        $budgetCategoriesQuery = DB::table('budget_classifications')
            ->select('id', 'category');
        
        // If a project is selected, filter classifications by project category
        if (!empty($data['projectId'])) {
            // Get the project's categoryId
            $project = DB::table('projects')
                ->select('categoryId')
                ->where('id', $data['projectId'])
                ->first();
            
            if ($project && !empty($project->categoryId)) {
                // Get expense classification IDs associated with this project category
                $expenseClassificationIds = DB::table('project_categories_expense_classification')
                    ->where('project_categoryId', $project->categoryId)
                    ->pluck('expense_classificationId')
                    ->toArray();
                
                // Filter budget classifications to only show those associated with the project category
                if (!empty($expenseClassificationIds)) {
                    $budgetCategoriesQuery->whereIn('id', $expenseClassificationIds);
                } else {
                    // If no classifications are associated, return empty result
                    $budgetCategoriesQuery->whereRaw('1 = 0');
                }
            }
        }
        
        $data['budgetCategories'] = $budgetCategoriesQuery
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
                ->select('project_budget.id', 'project_budget.projectId', 'project_budget.budgetId', 'project_budget.unit', 'project_budget.unitCost', 'project_budget.amount', 'project_budget.note', 'budgets.name as budgetName', 'budgets.classificationId', 'budget_classifications.category as budgetCategoryName')
                ->orderBy('budget_classifications.category', 'asc')
                ->orderBy('budgets.name', 'asc')
                ->get();
        }
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
            ->select('id', 'name', 'projectCode')
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
        
        // dd($data['projects']);
        return view('Project.projectbudgetsummary', $data);
    }

    public function client(Request $request)
    {
        $data['name'] = $request->input('name');
        $data['clientAccountIds'] = $request->input('clientAccountIds', []);
        $data['clientCode'] = $request->input('client_code');
        $data['clientType'] = $request->input('client_type');
        $data['status'] = $request->input('status');
        $data['contactAddress'] = $request->input('contact_address');
        $data['contactPhoneNumber'] = $request->input('contact_phone_number');
        $data['contactEmailAddress'] = $request->input('contact_email_address');
        $data['projectCategoryIds'] = $request->input('projectCategoryIds', []);
        $data['id'] = $request->input('id');
        
        if (isset($_POST['addnew'])) {
            $this->validate($request, [
                'name' => 'required|string|unique:clients,name',
                'clientAccountIds' => 'required|array|min:1',
                'clientAccountIds.*' => 'required|integer|exists:account_charts,id|distinct',
                'client_code' => 'required|string|unique:clients,client_code',
                'client_type' => 'required|integer|exists:client_type,id',
                'contact_address' => 'nullable|string',
                'contact_phone_number' => 'nullable|string',
                'contact_email_address' => 'nullable|email',
                'projectCategoryIds' => 'nullable|array',
                'projectCategoryIds.*' => 'integer|exists:project_categories,id',
            ]);

            $existingLedger = DB::table('client_ledgers')
                ->whereIn('clientAccountId', $data['clientAccountIds'])
                ->first();
            if ($existingLedger) {
                return back()->withInput()->with('error_message', 'One or more selected client ledgers are already assigned to another client.');
            }

            DB::transaction(function () use ($data) {
                $clientId = DB::table('clients')->insertGetId([
                    'name' => $data['name'],
                    'client_code' => $data['clientCode'],
                    'client_type' => $data['clientType'],
                    'contact_address' => $data['contactAddress'] ?? null,
                    'contact_phone_number' => $data['contactPhoneNumber'] ?? null,
                    'contact_email_address' => $data['contactEmailAddress'] ?? null,
                    'createdBy' => Auth::user()->id,
                    'createdAt' => now(),
                ]);

                foreach ($data['clientAccountIds'] as $accountId) {
                    DB::table('client_ledgers')->insert([
                        'clientId' => $clientId,
                        'clientAccountId' => $accountId,
                    ]);
                }

                // Insert project categories
                if (!empty($data['projectCategoryIds']) && is_array($data['projectCategoryIds'])) {
                    foreach ($data['projectCategoryIds'] as $categoryId) {
                        DB::table('client_project_categories')->insert([
                            'clientId' => $clientId,
                            'project_categoryId' => $categoryId,
                        ]);
                    }
                }
            });

            return back()->with('message', 'New record successfully added.');
        }
        
        if (isset($_POST['update'])) {
            $this->validate($request, [
                'name' => 'required|string|unique:clients,name,' . $request->input('id'),
                'clientAccountIds' => 'required|array|min:1',
                'clientAccountIds.*' => 'required|integer|exists:account_charts,id|distinct',
                'client_code' => 'required|string|unique:clients,client_code,' . $request->input('id'),
                'client_type' => 'required|integer|exists:client_type,id',
                'status' => 'required|in:Active,On Hold,Inactive',
                'contact_address' => 'nullable|string',
                'contact_phone_number' => 'nullable|string',
                'contact_email_address' => 'nullable|email',
                'id' => 'required|integer',
                'projectCategoryIds' => 'nullable|array',
                'projectCategoryIds.*' => 'integer|exists:project_categories,id',
            ]);

            $existingLedger = DB::table('client_ledgers')
                ->whereIn('clientAccountId', $data['clientAccountIds'])
                ->where('clientId', '<>', $data['id'])
                ->first();
            if ($existingLedger) {
                return back()->withInput()->with('error_message', 'One or more selected client ledgers are already assigned to another client.');
            }

            DB::transaction(function () use ($data) {
                DB::table('clients')->where('id', $data['id'])->update([
                    'name' => $data['name'],
                    'client_code' => $data['clientCode'],
                    'client_type' => $data['clientType'],
                    'status' => $data['status'],
                    'contact_address' => $data['contactAddress'] ?? null,
                    'contact_phone_number' => $data['contactPhoneNumber'] ?? null,
                    'contact_email_address' => $data['contactEmailAddress'] ?? null,
                ]);

                DB::table('client_ledgers')->where('clientId', $data['id'])->delete();
                foreach ($data['clientAccountIds'] as $accountId) {
                    DB::table('client_ledgers')->insert([
                        'clientId' => $data['id'],
                        'clientAccountId' => $accountId,
                    ]);
                }

                // Delete existing project categories
                DB::table('client_project_categories')->where('clientId', $data['id'])->delete();

                // Insert new project categories
                if (!empty($data['projectCategoryIds']) && is_array($data['projectCategoryIds'])) {
                    foreach ($data['projectCategoryIds'] as $categoryId) {
                        DB::table('client_project_categories')->insert([
                            'clientId' => $data['id'],
                            'project_categoryId' => $categoryId,
                        ]);
                    }
                }
            });

            return back()->with('message', 'Record successfully updated.');
        }
        
        if (isset($_POST['del'])) {
            $del = $request->input('deleteid');
            // Check if client has related records before deletion
            // Add your related table checks here if needed
            // if (DB::table('related_table')->where('clientId', $del)->first()) {
            //     return back()->with('error_message', 'Client has related records. Hence, record cannot be deleted!');
            // }
            
            // Delete associated project categories
            DB::table('client_project_categories')->where('clientId', $del)->delete();
            DB::table('client_ledgers')->where('clientId', $del)->delete();
            
            DB::table('clients')->where('id', $del)->delete();
            return back()->with('message', 'Record successfully deleted.');
        }
        
        // Fetch clients list with related information
        $clients = DB::table('clients')
            ->leftJoin('client_type', 'clients.client_type', '=', 'client_type.id')
            ->select(
                'clients.id',
                'clients.name',
                'clients.client_code',
                'clients.client_type',
                'clients.status',
                'clients.contact_address',
                'clients.contact_phone_number',
                'clients.contact_email_address',
                'client_type.name as clientTypeName'
            )
            ->orderBy('clients.name', 'asc')
            ->get();

        // Fetch project categories and ledgers for each client
        foreach ($clients as $client) {
            $client->projectCategories = DB::table('client_project_categories')
                ->join('project_categories', 'client_project_categories.project_categoryId', '=', 'project_categories.id')
                ->where('client_project_categories.clientId', $client->id)
                ->select('project_categories.id', 'project_categories.category')
                ->get();

            $client->clientLedgers = DB::table('client_ledgers')
                ->join('account_charts', 'client_ledgers.clientAccountId', '=', 'account_charts.id')
                ->where('client_ledgers.clientId', $client->id)
                ->select(
                    'client_ledgers.clientAccountId',
                    'account_charts.accountdescription',
                    'account_charts.accountno'
                )
                ->orderBy('account_charts.accountdescription', 'asc')
                ->get();
        }

        $data['clients'] = $clients;
        
        // Fetch account charts for dropdown (using headId 6 as default, adjust if needed)
        $data['accountLookUp'] = $this->AccountLookUpBysubHeadId(env('CLIENT_ID'));
        
        // Fetch project categories for dropdown
        $data['projectCategories'] = DB::table('project_categories')
            ->select('id', 'category')
            ->orderBy('category', 'asc')
            ->get();

        // Fetch client types for dropdown
        $data['clientTypes'] = DB::table('client_type')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();
        
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
                'vat' => 'nullable|numeric|min:0|max:100',
                'item_description' => 'required|array|min:1',
                'item_description.*' => 'required|string',
                'item_qty' => 'required|array|min:1',
                'item_qty.*' => 'required|numeric|min:0',
                'item_unitCost' => 'required|array|min:1',
                'item_unitCost.*' => 'required|numeric|min:0',
                'item_uomId' => 'nullable|array',
                'item_uomId.*' => 'nullable|integer',
            ]);

            // Calculate totals from line items
            $itemDescriptions = $request->input('item_description', []);
            $itemUomIds = $request->input('item_uomId', []);
            $itemQties = $request->input('item_qty', []);
            $itemUnitCosts = $request->input('item_unitCost', []);
            $vat = $data['vat'] ?? 0;
            
            $totalSubcost = 0;
            foreach ($itemDescriptions as $index => $itemDescription) {
                if (!empty($itemDescription)) {
                    $qty = $itemQties[$index] ?? 0;
                    $unitCost = $itemUnitCosts[$index] ?? 0;
                    $totalSubcost += $qty * $unitCost;
                }
            }
            
            $vatAmount = $totalSubcost * ($vat / 100);
            $subnet = $totalSubcost + $vatAmount;

            // Create PO header
            $poId = DB::table('project_po')->insertGetId([
                'projectId' => $data['projectId'],
                'poNumber' => $data['poNumber'],
                'description' => $data['description'],
                'vat' => $vat,
                'vatAmount' => $vatAmount,
                'subnet' => $subnet,
                'status' => 'Pending',
                'createdAt' => now(),
                'updatedAt' => now(),
                'createdBy' => Auth::user()->id,
            ]);
            
            // Create PO line items
            foreach ($itemDescriptions as $index => $itemDescription) {
                if (!empty($itemDescription)) {
                    $qty = $itemQties[$index] ?? 0;
                    $unitCost = $itemUnitCosts[$index] ?? 0;
                    $subcost = $qty * $unitCost;
                    
                    DB::table('project_po_item')->insert([
                        'poId' => $poId,
                        'description' => $itemDescription,
                        'uomId' => $itemUomIds[$index] ?? null,
                        'qty' => $qty,
                        'unitCost' => $unitCost,
                        'subcost' => $subcost,
                        'createdAt' => now(),
                        'updatedAt' => now(),
                    ]);
                }
            }
            
            return back()->with('message', 'New PO record successfully added.');
        }
        
        if (isset($_POST['update'])) {
            $this->validate($request, [
                'projectId' => 'required|integer',
                'poNumber' => 'required|string|unique:project_po,poNumber,' . $request->input('id'),
                'description' => 'required|string',
                'vat' => 'nullable|numeric|min:0|max:100',
                'id' => 'required|integer',
                'item_description' => 'required|array|min:1',
                'item_description.*' => 'required|string',
                'item_qty' => 'required|array|min:1',
                'item_qty.*' => 'required|numeric|min:0',
                'item_unitCost' => 'required|array|min:1',
                'item_unitCost.*' => 'required|numeric|min:0',
                'item_uomId' => 'nullable|array',
                'item_uomId.*' => 'nullable|integer',
            ]);

            // Calculate totals from line items
            $itemDescriptions = $request->input('item_description', []);
            $itemUomIds = $request->input('item_uomId', []);
            $itemQties = $request->input('item_qty', []);
            $itemUnitCosts = $request->input('item_unitCost', []);
            $vat = $data['vat'] ?? 0;
            
            $totalSubcost = 0;
            foreach ($itemDescriptions as $index => $itemDescription) {
                if (!empty($itemDescription)) {
                    $qty = $itemQties[$index] ?? 0;
                    $unitCost = $itemUnitCosts[$index] ?? 0;
                    $totalSubcost += $qty * $unitCost;
                }
            }
            
            $vatAmount = $totalSubcost * ($vat / 100);
            $subnet = $totalSubcost + $vatAmount;

            // Update PO header
            DB::table('project_po')->where('id', $data['id'])->update([
                'poNumber' => $data['poNumber'],
                'description' => $data['description'],
                'vat' => $vat,
                'vatAmount' => $vatAmount,
                'subnet' => $subnet,
                'updatedAt' => now(),
            ]);
            
            // Delete existing line items
            DB::table('project_po_item')->where('poId', $data['id'])->delete();
            
            // Create new line items
            foreach ($itemDescriptions as $index => $itemDescription) {
                if (!empty($itemDescription)) {
                    $qty = $itemQties[$index] ?? 0;
                    $unitCost = $itemUnitCosts[$index] ?? 0;
                    $subcost = $qty * $unitCost;
                    
                    DB::table('project_po_item')->insert([
                        'poId' => $data['id'],
                        'description' => $itemDescription,
                        'uomId' => $itemUomIds[$index] ?? null,
                        'qty' => $qty,
                        'unitCost' => $unitCost,
                        'subcost' => $subcost,
                        'createdAt' => now(),
                        'updatedAt' => now(),
                    ]);
                }
            }
            
            return back()->with('message', 'PO record successfully updated.');
        }
        
        if (isset($_POST['approve'])) {
            $approveId = $request->input('approveid');
            $refno=$this->RefNo();
            $poProjectData = DB::table('project_po')
                ->join('projects', 'project_po.projectId', '=', 'projects.id')
                ->leftJoin('clients', 'projects.clientId', '=', 'clients.id')
                ->where('project_po.id', $approveId)
                ->select(
                    'project_po.id as poId',
                    'projects.id as projectId',
                    'projects.name as projectName',
                    'projects.clientId',
                    'projects.revenue_accountId',
                    'clients.name as clientName',
                    'clients.clientAccountId',
                    'project_po.subnet'
                )
                ->first();

            if (!$poProjectData) {
                return back()->with('error_message', 'Selected PO record was not found.');
            }

            if (empty($poProjectData->clientId)) {
                return back()->with('error_message', 'The project has no client assigned. Kindly update the project before approving this PO.');
            }

            if (empty($poProjectData->clientAccountId)) {
                return back()->with('error_message', "The selected client '{$poProjectData->clientName}' does not have a client account assigned.");
            }

            if (empty($poProjectData->revenue_accountId)) {
                return back()->with('error_message', "The project '{$poProjectData->projectName}' does not have a revenue account assigned.");
            }

            if (!$this->FetchAccountCodes($poProjectData->clientAccountId)) {
                return back()->with('error_message', "Client account ID '{$poProjectData->clientAccountId}' was not found in chart of accounts.");
            }

            if (!$this->FetchAccountCodes($poProjectData->revenue_accountId)) {
                return back()->with('error_message', "Project revenue account ID '{$poProjectData->revenue_accountId}' was not found in chart of accounts.");
            }
            $this->DebitAccount($poProjectData->clientAccountId, $poProjectData->subnet, $refno, now(), 'PO Approved', Auth::user()->id, $refno, $poProjectData->projectId);
            $this->CreditAccount($poProjectData->revenue_accountId, $poProjectData->subnet, $refno, now(), 'PO Approved', Auth::user()->id, $refno, $poProjectData->projectId);

            DB::table('project_po')->where('id', $approveId)->update([
                'status' => 'Approved',
                'approvedBy' => Auth::user()->id,
                'updatedAt' => now(),
            ]);
            return back()->with('message', 'PO record successfully approved.');
        }
        
        if (isset($_POST['del'])) {
            $del = $request->input('deleteid');
            // Delete line items first
            DB::table('project_po_item')->where('poId', $del)->delete();
            // Then delete PO header
            DB::table('project_po')->where('id', $del)->delete();
            return back()->with('message', 'PO record successfully deleted.');
        }
        
        // Fetch projects list
        $data['projects'] = DB::table('projects')
            ->select('id', 'name', 'projectCode')
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
            $projectPos = DB::table('project_po')
                ->leftJoin('users as creator', 'project_po.createdBy', '=', 'creator.id')
                ->leftJoin('users as approver', 'project_po.approvedBy', '=', 'approver.id')
                ->where('project_po.projectId', $data['projectId'])
                ->select(
                    'project_po.id',
                    'project_po.projectId',
                    'project_po.poNumber',
                    'project_po.description',
                    'project_po.vat',
                    'project_po.vatAmount',
                    'project_po.subnet',
                    'project_po.status',
                    'project_po.createdBy',
                    'project_po.approvedBy',
                    'project_po.createdAt',
                    'project_po.updatedAt',
                    'creator.name as createdByName',
                    'approver.name as approvedByName'
                )
                ->orderBy('project_po.createdAt', 'desc')
                ->get();
            
            // Fetch line items for each PO
            foreach ($projectPos as $po) {
                $po->items = DB::table('project_po_item')
                    ->leftJoin('uom', 'project_po_item.uomId', '=', 'uom.id')
                    ->where('project_po_item.poId', $po->id)
                    ->select(
                        'project_po_item.id',
                        'project_po_item.description',
                        'project_po_item.uomId',
                        'project_po_item.qty',
                        'project_po_item.unitCost',
                        'project_po_item.subcost',
                        'uom.measurement as uomMeasurement'
                    )
                    ->orderBy('project_po_item.id', 'asc')
                    ->get();
            }
            
            $data['projectPos'] = $projectPos;
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
            ->select('id', 'name', 'projectCode')
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
        $data['accountId'] = $request->input('accountId');
        $data['paymentMilestoneId'] = $request->input('paymentMilestoneId');
        $data['reference_number'] = $request->input('reference_number');
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
                'accountId' => 'required|integer|exists:account_charts,id',
                'paymentMilestoneId' => 'required|integer',
                'reference_number' => 'required|string|unique:project_expense,reference_number',
                'debit' => 'required|numeric|min:0',
                'transactionDate' => 'required|date',
            ]);
            $refno=$this->RefNo();
            DB::table('project_expense')->insert([
                'system_ref' => $refno,
                'projectId' => $data['projectId'],
                'budgetId' => $data['budgetId'],
                'accountId' => $data['accountId'],
                'paymentMilestoneId' => $data['paymentMilestoneId'],
                'reference_number' => $data['reference_number'],
                'debit' => $data['debit'],
                'credit' => 0,
                'transactionDate' => $data['transactionDate'],
                'status' => 'Pending',
                'createdBy' => Auth::user()->id,
                'createdAt' => now(),
                'updatedAt' => now(),
                'isVendor' => 1,
            ]);
            return back()->with('message', 'New fund disbursement successfully added.');
        }
        
        if (isset($_POST['update'])) {
            $this->validate($request, [
                'projectId' => 'required|integer',
                'budgetId' => 'required|integer',
                'accountId' => 'required|integer|exists:account_charts,id',
                'paymentMilestoneId' => 'required|integer',
                'reference_number' => 'required|string|unique:project_expense,reference_number,' . $request->input('id'),
                'debit' => 'required|numeric|min:0',
                'transactionDate' => 'required|date',
                'id' => 'required|integer',
            ]);

            DB::table('project_expense')->where('id', $data['id'])->update([
                'budgetId' => $data['budgetId'],
                'accountId' => $data['accountId'],
                'paymentMilestoneId' => $data['paymentMilestoneId'],
                'reference_number' => $data['reference_number'],
                'debit' => $data['debit'],
                'transactionDate' => $data['transactionDate'],
                'updatedAt' => now(),
            ]);
            return back()->with('message', 'Fund disbursement successfully updated.');
        }
        
        if (isset($_POST['approve'])) {
            $approveId = $request->input('approveid');
            $refno = $this->RefNo();

            $approvalData = DB::table('project_expense')
                ->leftJoin('budgets', 'project_expense.budgetId', '=', 'budgets.id')
                ->where('project_expense.id', $approveId)
                ->where('project_expense.isVendor', 1)
                ->select(
                    'project_expense.id',
                    'project_expense.projectId',
                    'project_expense.accountId',
                    'project_expense.budgetId',
                    'project_expense.debit',
                    'project_expense.transactionDate',
                    'project_expense.reference_number',
                    'project_expense.status',
                    'budgets.accountId as vendorAccountId',
                    'budgets.name as vendorName'
                )
                ->first();

            if (!$approvalData) {
                return back()->with('error_message', 'Fund disbursement record was not found.');
            }

            if ($approvalData->status === 'Approved') {
                return back()->with('error_message', 'This fund disbursement has already been approved.');
            }

            if (empty($approvalData->accountId)) {
                return back()->with('error_message', 'Drawn ledger is missing on this fund disbursement record.');
            }

            if (empty($approvalData->vendorAccountId)) {
                return back()->with('error_message', "Vendor account is not configured for '{$approvalData->vendorName}'.");
            }

            if (!$this->FetchAccountCodes($approvalData->accountId)) {
                return back()->with('error_message', "Drawn ledger account ID '{$approvalData->accountId}' was not found in chart of accounts.");
            }

            if (!$this->FetchAccountCodes($approvalData->vendorAccountId)) {
                return back()->with('error_message', "Vendor account ID '{$approvalData->vendorAccountId}' was not found in chart of accounts.");
            }

            if ((float) $approvalData->debit <= 0) {
                return back()->with('error_message', 'Fund disbursement amount must be greater than zero before approval.');
            }

            $transDate = !empty($approvalData->transactionDate)
                ? date('Y-m-d', strtotime($approvalData->transactionDate))
                : date('Y-m-d');
            $manualRef = $approvalData->reference_number ?: $refno;
            $remark = 'Fund disbursement approved';
            $userId = Auth::user()->id;

            // Fund source gets credited, vendor account gets debited.
            $this->CreditAccount(
                $approvalData->accountId,
                $approvalData->debit,
                $refno,
                $transDate,
                $remark,
                $userId,
                $manualRef,
                $approvalData->projectId
            );
            $this->DebitAccount(
                $approvalData->vendorAccountId,
                $approvalData->debit,
                $refno,
                $transDate,
                $remark,
                $userId,
                $manualRef,
                $approvalData->projectId
            );

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
            ->select('id', 'name', 'projectCode')
            ->orderBy('name', 'asc')
            ->get();
        
        // Fetch unique vendors associated with selected project from vendor_projects
        $data['budgets'] = collect();
        if (!empty($data['projectId'])) {
            $data['budgets'] = DB::table('vendor_projects')
                ->leftJoin('budgets', 'vendor_projects.vendorId', '=', 'budgets.id')
                ->where('vendor_projects.projectId', $data['projectId'])
                ->whereNotNull('vendor_projects.vendorId')
                ->select(
                    'budgets.id',
                    'budgets.name as budgetName',
                    DB::raw("'Vendor' as budgetCategoryName")
                )
                ->distinct()
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

        // Fetch source accounts (fund drawn from)
        $data['accounts'] = DB::table('account_charts')
            ->where('status', 1)
            ->select('id', 'accountno', 'accountdescription')
            ->orderBy('accountdescription', 'asc')
            ->get();
        
        // Fetch fund disbursements for selected project
        $data['fundDisbursements'] = collect();
        $data['totalDisbursed'] = 0;
        if (!empty($data['projectId'])) {
            $data['fundDisbursements'] = DB::table('project_expense')
                ->leftJoin('budgets', 'project_expense.budgetId', '=', 'budgets.id')
                ->leftJoin('account_charts', 'project_expense.accountId', '=', 'account_charts.id')
                ->leftJoin('payment_milestone', 'project_expense.paymentMilestoneId', '=', 'payment_milestone.id')
                ->leftJoin('budget_classifications', 'budgets.classificationId', '=', 'budget_classifications.id')
                ->where('project_expense.projectId', $data['projectId'])
                ->where('project_expense.isVendor', 1)
                ->select(
                    'project_expense.id',
                    'project_expense.budgetId',
                    'project_expense.accountId',
                    'project_expense.paymentMilestoneId',
                    'project_expense.reference_number',
                    'project_expense.debit',
                    'project_expense.status',
                    'project_expense.transactionDate',
                    'project_expense.createdAt',
                    'project_expense.approvedAt',
                    'budgets.name as budgetName',
                    'account_charts.accountdescription as accountName',
                    'account_charts.accountno as accountNo',
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

    public function fieldExpense(Request $request)
    {
        $data['projectId'] = $request->input('projectId');
        $data['budgetId'] = $request->input('budgetId');
        $data['paymentMilestoneId'] = $request->input('paymentMilestoneId');
        $data['reference_number'] = $request->input('reference_number');
        $data['amount'] = $request->input('amount');
        $data['transactionDate'] = $request->input('transactionDate');
        $data['description'] = $request->input('description');
        $data['id'] = $request->input('id');

        // Handle project selection - reload page with selected project
        if ($request->has('select_project')) {
            $data['projectId'] = $request->input('projectId');
            Session(['selected_field_expense_project_id' => $data['projectId']]);
        }

        // Get selected project from session if not in request
        if (empty($data['projectId'])) {
            $data['projectId'] = Session::get('selected_field_expense_project_id');
        }

        if (isset($_POST['addnew'])) {
            $this->validate($request, [
                'projectId' => 'required|integer',
                'budgetId' => 'required|integer',
                'paymentMilestoneId' => 'nullable|integer',
                'reference_number' => 'required|string|unique:project_expense,reference_number',
                'amount' => 'required|numeric|min:0',
                'transactionDate' => 'required|date',
                'description' => 'required|string',
            ]);
            $refno=$this->RefNo();
            // validate accountId using $this->FetchAccountCodes($data['accountId'])
            // dd(Auth::user()->ledgerId);
            if (empty(Auth::user()->ledgerId)) {
                return back()->with('error_message', "You are not authorized to add field expense.");
            }
            if (!$this->FetchAccountCodes(Auth::user()->ledgerId)) {
                return back()->with('error_message', "You are not authorized to add field expense.");
            }
            DB::table('project_expense')->insert([
                'system_ref' => $refno,
                'projectId' => $data['projectId'],
                'accountId' => Auth::user()->ledgerId,
                'budgetId' => $data['budgetId'],
                'paymentMilestoneId' => $data['paymentMilestoneId'] ?? null,
                'reference_number' => $data['reference_number'],
                'debit' => $data['amount'],
                'credit' => 0,
                'description' => $data['description'],
                'transactionDate' => $data['transactionDate'],
                'status' => 'Pending',
                'createdBy' => Auth::user()->id,
                'createdAt' => now(),
                'updatedAt' => now(),
                'isVendor' => 0,
            ]);
            return back()->with('message', 'New field expense successfully added.');
        }

        if (isset($_POST['update'])) {
            $this->validate($request, [
                'projectId' => 'required|integer',
                'budgetId' => 'required|integer',
                'paymentMilestoneId' => 'nullable|integer',
                'reference_number' => 'required|string|unique:project_expense,reference_number,' . $request->input('id'),
                'amount' => 'required|numeric|min:0',
                'transactionDate' => 'required|date',
                'description' => 'required|string',
                'id' => 'required|integer',
            ]);

            DB::table('project_expense')->where('id', $data['id'])->update([
                'budgetId' => $data['budgetId'],
                'paymentMilestoneId' => $data['paymentMilestoneId'] ?? null,
                'reference_number' => $data['reference_number'],
                'debit' => $data['amount'],
                'description' => $data['description'],
                'transactionDate' => $data['transactionDate'],
                'updatedAt' => now(),
            ]);
            return back()->with('message', 'Field expense successfully updated.');
        }

        if (isset($_POST['approve'])) {
            $approveId = $request->input('approveid');
            $refno=$this->RefNo();
            $approvalData = DB::table('project_expense')
                ->leftJoin('projects', 'project_expense.projectId', '=', 'projects.id')
                ->where('project_expense.id', $approveId)
                ->select(
                    'project_expense.id',
                    'project_expense.projectId',
                    'project_expense.accountId',
                    'project_expense.debit',
                    'project_expense.transactionDate',
                    'project_expense.reference_number',
                    'project_expense.status',
                    'projects.expenseAccountId as expenseAccountId',
                    'projects.name as projectName'
                )
                ->first();

            if (!$approvalData) {
                return back()->with('error_message', 'Fund disbursement record was not found.');
            }

            if ($approvalData->status === 'Approved') {
                return back()->with('error_message', 'This fund disbursement has already been approved.');
            }

            if (empty($approvalData->accountId)) {
                return back()->with('error_message', 'Drawn ledger is missing on this fund disbursement record.');
            }

            if (empty($approvalData->expenseAccountId)) {
                return back()->with('error_message', "Expense account is not configured for '{$approvalData->projectName}'.");
            }

            if (!$this->FetchAccountCodes($approvalData->accountId)) {
                return back()->with('error_message', "Drawn ledger account ID '{$approvalData->accountId}' was not found in chart of accounts.");
            }

            if (!$this->FetchAccountCodes($approvalData->expenseAccountId)) {
                return back()->with('error_message', "Expense account ID '{$approvalData->expenseAccountId}' was not found in chart of accounts.");
            }

            if ((float) $approvalData->debit <= 0) {
                return back()->with('error_message', 'Fund disbursement amount must be greater than zero before approval.');
            }

            $transDate = !empty($approvalData->transactionDate)
                ? date('Y-m-d', strtotime($approvalData->transactionDate))
                : date('Y-m-d');
            $manualRef = $approvalData->reference_number ?: $refno;
            $remark = 'Fund disbursement approved';
            $userId = Auth::user()->id;

            // Fund source gets credited, vendor account gets debited.
            $this->CreditAccount(
                $approvalData->accountId,
                $approvalData->debit,
                $refno,
                $transDate,
                $remark,
                $userId,
                $manualRef,
                $approvalData->projectId
            );
            $this->DebitAccount(
                $approvalData->expenseAccountId,
                $approvalData->debit,
                $refno,
                $transDate,
                $remark,
                $userId,
                $manualRef,
                $approvalData->projectId
            );
            DB::table('project_expense')->where('id', $approveId)->update([
                'status' => 'Approved',
                'approvedBy' => Auth::user()->id,
                'approvedAt' => now(),
                'updatedAt' => now(),
            ]);
            return back()->with('message', 'Field expense successfully approved.');
        }

        if (isset($_POST['del'])) {
            $del = $request->input('deleteid');
            DB::table('project_expense')->where('id', $del)->delete();
            return back()->with('message', 'Field expense successfully deleted.');
        }

        // Fetch projects list
        $data['projects'] = DB::table('projects')
            ->select('id', 'name', 'projectCode', 'categoryId')
            ->orderBy('name', 'asc')
            ->get();

        // Fetch budgets for selected project from mapped classifications where:
        // budgets.isVendor = 0 and budget_classifications.isSubContrator = 0
        $data['budgets'] = collect();
        if (!empty($data['projectId'])) {
            $project = DB::table('projects')
                ->select('id', 'categoryId')
                ->where('id', $data['projectId'])
                ->first();

            if ($project && !empty($project->categoryId)) {
                $classificationIds = DB::table('project_categories_expense_classification')
                    ->where('project_categoryId', $project->categoryId)
                    ->pluck('expense_classificationId')
                    ->toArray();

                if (!empty($classificationIds)) {
                    $data['budgets'] = DB::table('budgets')
                        ->leftJoin('budget_classifications', 'budgets.classificationId', '=', 'budget_classifications.id')
                        ->where('budgets.isVendor', 0)
                        ->where('budget_classifications.isSubContrator', 0)
                        ->whereIn('budgets.classificationId', $classificationIds)
                        ->select(
                            'budgets.id',
                            'budgets.name as budgetName',
                            'budgets.classificationId',
                            'budget_classifications.category as budgetCategoryName'
                        )
                        ->orderBy('budget_classifications.category', 'asc')
                        ->orderBy('budgets.name', 'asc')
                        ->get();
                }
            }
        }

        // Fetch payment milestones for selected project (optional field in form)
        $data['paymentMilestones'] = collect();
        if (!empty($data['projectId'])) {
            $data['paymentMilestones'] = DB::table('payment_milestone')
                ->where('projectId', $data['projectId'])
                ->select('id', 'milestone', 'percentage', 'rank', 'projectId')
                ->orderBy('rank', 'asc')
                ->get();
        }

        // Fetch field expenses for selected project
        $data['fieldExpenses'] = collect();
        $data['totalFieldExpense'] = 0;
        if (!empty($data['projectId'])) {
            $data['fieldExpenses'] = DB::table('project_expense')
                ->leftJoin('budgets', 'project_expense.budgetId', '=', 'budgets.id')
                ->leftJoin('payment_milestone', 'project_expense.paymentMilestoneId', '=', 'payment_milestone.id')
                ->leftJoin('budget_classifications', 'budgets.classificationId', '=', 'budget_classifications.id')
                ->where('project_expense.projectId', $data['projectId'])
                ->where('budgets.isVendor', 0)
                ->where('budget_classifications.isSubContrator', 0)
                ->where('project_expense.isVendor', 0)
                ->select(
                    'project_expense.id',
                    'project_expense.projectId',
                    'project_expense.budgetId',
                    'project_expense.paymentMilestoneId',
                    'project_expense.reference_number',
                    'project_expense.debit',
                    'project_expense.description',
                    'project_expense.status',
                    'project_expense.transactionDate',
                    'project_expense.createdAt',
                    'project_expense.approvedAt',
                    'budgets.name as budgetName',
                    'budget_classifications.category as budgetCategoryName',
                    'payment_milestone.milestone',
                    'payment_milestone.rank as milestoneRank'
                )
                ->orderBy('project_expense.transactionDate', 'desc')
                ->orderBy('budgets.name', 'asc')
                ->get();

            $data['totalFieldExpense'] = $data['fieldExpenses']->sum('debit');
        }

        return view('Project.fieldexpense', $data);
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
            ->select('id', 'name', 'projectCode')
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
            ->select('id', 'name', 'projectCode')
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
        $data['vendorId'] = $request->input('vendorId');
        $data['tradeName'] = $request->input('trade_name');
        $data['vendorType'] = $request->input('vendor_type');
        $data['taxNumber'] = $request->input('tax_number');
        $data['vendorCategory'] = $request->input('vendor_category');
        $data['address'] = $request->input('address');
        $data['email'] = $request->input('email');
        $data['contactPhoneNumber'] = $request->input('contact_phone_number');
        $data['bankid'] = $request->input('bankid');
        $data['bankAccountName'] = $request->input('bank_account_name');
        $data['bankAccountNumber'] = $request->input('bank_account_number');
        $data['currency'] = $request->input('currency');
        $data['status'] = $request->input('status');
        $data['id'] = $request->input('id');
        
        if (isset($_POST['addnew'])) {
            $this->validate($request, [
                'name' => 'required|string|unique:budgets,name',
                'description' => 'nullable|string',
                'vendorId' => 'required|string|unique:budgets,vendorId',
                'trade_name' => 'nullable|string',
                'vendor_type' => 'nullable|integer|exists:vendor_type,id',
                'tax_number' => 'nullable|string',
                'vendor_category' => 'nullable|integer|exists:vendor_catogory,id',
                'address' => 'nullable|string',
                'email' => 'nullable|email',
                'contact_phone_number' => 'nullable|string',
                'bankid' => 'nullable|integer|exists:tblbanklist,bankID',
                'bank_account_name' => 'nullable|string',
                'bank_account_number' => 'nullable|string',
                'currency' => 'nullable|string|max:10',
                'accountId' => 'nullable|integer',
            ]);

            DB::table('budgets')->insert([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'classificationId' => 1,
                'isVendor' => 1,
                'vendorId' => $data['vendorId'],
                'trade_name' => $data['tradeName'] ?? null,
                'vendor_type' => $data['vendorType'] ?? null,
                'tax_number' => $data['taxNumber'] ?? null,
                'vendor_category' => $data['vendorCategory'] ?? null,
                'address' => $data['address'] ?? null,
                'email' => $data['email'] ?? null,
                'contact_phone_number' => $data['contactPhoneNumber'] ?? null,
                'bankid' => $data['bankid'] ?? null,
                'bank_account_name' => $data['bankAccountName'] ?? null,
                'bank_account_number' => $data['bankAccountNumber'] ?? null,
                'currency' => $data['currency'] ?? null,
                'accountId' => $data['accountId'] ?? null,
                'status' => 'Active',
                'createdAt' => now(),
                'updatedAt' => now(),
            ]);
            return back()->with('message', 'New vendor successfully added.');
        }
        
        if (isset($_POST['update'])) {
            $this->validate($request, [
                'name' => 'required|string|unique:budgets,name,' . $request->input('id'),
                'description' => 'nullable|string',
                'vendorId' => 'required|string|unique:budgets,vendorId,' . $request->input('id'),
                'trade_name' => 'nullable|string',
                'vendor_type' => 'nullable|integer|exists:vendor_type,id',
                'tax_number' => 'nullable|string',
                'vendor_category' => 'nullable|integer|exists:vendor_catogory,id',
                'address' => 'nullable|string',
                'email' => 'nullable|email',
                'contact_phone_number' => 'nullable|string',
                'bankid' => 'nullable|integer|exists:tblbanklist,bankID',
                'bank_account_name' => 'nullable|string',
                'bank_account_number' => 'nullable|string',
                'currency' => 'nullable|string|max:10',
                'accountId' => 'nullable|integer',
                'status' => 'required|in:Active,On Hold,Inactive',
                'id' => 'required|integer',
            ]);

            DB::table('budgets')->where('id', $data['id'])->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'vendorId' => $data['vendorId'],
                'trade_name' => $data['tradeName'] ?? null,
                'vendor_type' => $data['vendorType'] ?? null,
                'tax_number' => $data['taxNumber'] ?? null,
                'vendor_category' => $data['vendorCategory'] ?? null,
                'address' => $data['address'] ?? null,
                'email' => $data['email'] ?? null,
                'contact_phone_number' => $data['contactPhoneNumber'] ?? null,
                'bankid' => $data['bankid'] ?? null,
                'bank_account_name' => $data['bankAccountName'] ?? null,
                'bank_account_number' => $data['bankAccountNumber'] ?? null,
                'currency' => $data['currency'] ?? null,
                'accountId' => $data['accountId'] ?? null,
                'status' => $data['status'],
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
            ->leftJoin('vendor_type', 'budgets.vendor_type', '=', 'vendor_type.id')
            ->leftJoin('vendor_catogory', 'budgets.vendor_category', '=', 'vendor_catogory.id')
            ->leftJoin('tblbanklist', 'budgets.bankid', '=', 'tblbanklist.bankID')
            ->where('budgets.classificationId', 1)
            ->where('budgets.isVendor', 1)
            ->select(
                'budgets.id',
                'budgets.name',
                'budgets.description',
                'budgets.vendorId',
                'budgets.trade_name',
                'budgets.vendor_type',
                'budgets.tax_number',
                'budgets.vendor_category',
                'budgets.address',
                'budgets.email',
                'budgets.contact_phone_number',
                'budgets.bankid',
                'budgets.bank_account_name',
                'budgets.bank_account_number',
                'budgets.currency',
                'budgets.accountId',
                'budgets.status',
                'account_charts.accountdescription as accountName',
                'account_charts.accountno as accountNo',
                'vendor_type.name as vendorTypeName',
                'vendor_catogory.name as vendorCategoryName',
                'tblbanklist.bank as bankName'
            )
            ->orderBy('budgets.name', 'asc')
            ->get();
        
        // Fetch account charts for dropdown
        $data['accountLookUp'] = $this->AccountLookUpBysubHeadId(env('VENDOR_ID'));

        // Fetch vendor type, category and banks for dropdowns
        $data['vendorTypes'] = DB::table('vendor_type')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        $data['vendorCategories'] = DB::table('vendor_catogory')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        $data['banks'] = DB::table('tblbanklist')
            ->select('bankID', 'bankCode', 'bank')
            ->orderBy('bank', 'asc')
            ->get();
        
        return view('Project.vendor', $data);
    }

    public function projectCategoryPaymentMilestone(Request $request)
    {
        $data['projectCategoryId'] = $request->input('projectCategoryId');
        $data['milestone'] = $request->input('milestone');
        $data['percentage'] = $request->input('percentage');
        $data['rank'] = $request->input('rank');
        $data['id'] = $request->input('id');
        
        // Handle project category selection - reload page with selected category
        if ($request->has('select_category')) {
            $data['projectCategoryId'] = $request->input('projectCategoryId');
            Session(['selected_project_category_payment_milestone_id' => $data['projectCategoryId']]);
        }
        
        // Get selected project category from session if not in request
        if (empty($data['projectCategoryId'])) {
            $data['projectCategoryId'] = Session::get('selected_project_category_payment_milestone_id');
        }
        
        if (isset($_POST['addnew'])) {
            $this->validate($request, [
                'projectCategoryId' => 'required|integer',
                'milestone' => 'required|string',
                'percentage' => 'required|numeric|min:0|max:100',
                'rank' => 'required|integer|min:1',
            ]);

            // Check total percentage for this project category
            $existingMilestones = DB::table('project_category_payment_milestone')
                ->where('projectCategoryId', $data['projectCategoryId'])
                ->sum('percentage');
            
            $totalPercentage = $existingMilestones + $data['percentage'];
            
            if ($totalPercentage > 100) {
                return back()->with('error_message', 'Total percentage cannot exceed 100%. Current total: ' . $existingMilestones . '%, Adding: ' . $data['percentage'] . '% = ' . $totalPercentage . '%');
            }

            DB::table('project_category_payment_milestone')->insert([
                'projectCategoryId' => $data['projectCategoryId'],
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
                'projectCategoryId' => 'required|integer',
                'milestone' => 'required|string',
                'percentage' => 'required|numeric|min:0|max:100',
                'rank' => 'required|integer|min:1',
                'id' => 'required|integer',
            ]);

            // Get current milestone percentage
            $currentMilestone = DB::table('project_category_payment_milestone')
                ->where('id', $data['id'])
                ->first();
            
            // Check total percentage for this project category (excluding current record)
            $existingMilestones = DB::table('project_category_payment_milestone')
                ->where('projectCategoryId', $data['projectCategoryId'])
                ->where('id', '!=', $data['id'])
                ->sum('percentage');
            
            $totalPercentage = $existingMilestones + $data['percentage'];
            
            if ($totalPercentage > 100) {
                return back()->with('error_message', 'Total percentage cannot exceed 100%. Current total (excluding this milestone): ' . $existingMilestones . '%, New percentage: ' . $data['percentage'] . '% = ' . $totalPercentage . '%');
            }

            DB::table('project_category_payment_milestone')->where('id', $data['id'])->update([
                'milestone' => $data['milestone'],
                'percentage' => $data['percentage'],
                'rank' => $data['rank'],
                'updatedAt' => now(),
            ]);
            return back()->with('message', 'Payment milestone successfully updated.');
        }
        
        if (isset($_POST['del'])) {
            $del = $request->input('deleteid');
            DB::table('project_category_payment_milestone')->where('id', $del)->delete();
            return back()->with('message', 'Payment milestone successfully deleted.');
        }
        
        // Fetch project categories list
        $data['projectCategories'] = DB::table('project_categories')
            ->select('id', 'category')
            ->orderBy('category', 'asc')
            ->get();
        
        // Fetch payment milestones for selected project category
        $data['paymentMilestones'] = collect();
        $data['totalPercentage'] = 0;
        if (!empty($data['projectCategoryId'])) {
            $data['paymentMilestones'] = DB::table('project_category_payment_milestone')
                ->where('projectCategoryId', $data['projectCategoryId'])
                ->select('id', 'milestone', 'percentage', 'rank', 'projectCategoryId')
                ->orderBy('rank', 'asc')
                ->get();
            
            $data['totalPercentage'] = $data['paymentMilestones']->sum('percentage');
        }
        
        return view('Project.projectcategorypaymentmilestone', $data);
    }

    public function projectCategoryExpenseClassification(Request $request)
    {
        $data['projectCategoryId'] = $request->input('projectCategoryId');
        
        // Handle project category selection - reload page with selected category
        if ($request->has('select_category')) {
            $data['projectCategoryId'] = $request->input('projectCategoryId');
            Session(['selected_project_category_expense_classification_id' => $data['projectCategoryId']]);
        }
        
        // Get selected project category from session if not in request
        if (empty($data['projectCategoryId'])) {
            $data['projectCategoryId'] = Session::get('selected_project_category_expense_classification_id');
        }
        
        if (isset($_POST['assign'])) {
            $this->validate($request, [
                'projectCategoryId' => 'required|integer',
            ]);
            
            $projectCategoryId = $request->input('projectCategoryId');
            
            // Delete existing expense classifications for this project category
            DB::table('project_categories_expense_classification')
                ->where('project_categoryId', $projectCategoryId)
                ->delete();
            
            // Insert new expense classifications
            if ($request->has('expense_classifications')) {
                $expenseClassifications = $request->input('expense_classifications');
                if (is_array($expenseClassifications)) {
                    foreach ($expenseClassifications as $expenseClassificationId) {
                        DB::table('project_categories_expense_classification')->insert([
                            'project_categoryId' => $projectCategoryId,
                            'expense_classificationId' => $expenseClassificationId,
                        ]);
                    }
                }
            }
            
            return back()->with('message', 'Expense classifications successfully assigned.');
        }
        
        // Fetch project categories list
        $data['projectCategories'] = DB::table('project_categories')
            ->select('id', 'category')
            ->orderBy('category', 'asc')
            ->get();
        
        // Fetch budget classifications (expense classifications)
        $data['expenseClassifications'] = DB::table('budget_classifications')
            ->select('id', 'category', 'isMeasure', 'isMilestone', 'isSubContrator')
            ->orderBy('category', 'asc')
            ->get();
        
        // Fetch assigned expense classifications for selected project category
        $data['assignedClassifications'] = collect();
        if (!empty($data['projectCategoryId'])) {
            $data['assignedClassifications'] = DB::table('project_categories_expense_classification')
                ->where('project_categoryId', $data['projectCategoryId'])
                ->pluck('expense_classificationId')
                ->toArray();
        }
        
        return view('Project.projectcategoryexpenseclassification', $data);
    }

    public function projectInvoice(Request $request)
    {
        $data['projectId'] = $request->input('projectId');
        $data['InvoiceNumber'] = $request->input('InvoiceNumber');
        $data['amount'] = $request->input('amount');
        $data['vat'] = $request->input('vat');
        $data['wht'] = $request->input('wht');
        $data['vatInclude'] = $request->input('vatInclude');
        $data['expectedAmount'] = $request->input('expectedAmount');
        $data['dueDate'] = $request->input('dueDate');
        $data['status'] = $request->input('status');
        $data['id'] = $request->input('id');
        
        // Handle project selection - reload page with selected project
        if ($request->has('select_project')) {
            $data['projectId'] = $request->input('projectId');
            Session(['selected_project_invoice_id' => $data['projectId']]);
        }
        
        // Get selected project from session if not in request
        if (empty($data['projectId'])) {
            $data['projectId'] = Session::get('selected_project_invoice_id');
        }
        
        if (isset($_POST['addnew'])) {
            $this->validate($request, [
                'projectId' => 'required|integer',
                'InvoiceNumber' => 'required|string|unique:project_invoice,InvoiceNumber',
                'amount' => 'required|numeric|min:0',
                'vat' => 'nullable|numeric|min:0|max:100',
                'wht' => 'nullable|numeric|min:0|max:100',
                'dueDate' => 'required|date',
            ]);

            // Get form values
            $amount = $data['amount'];
            $vat = $data['vat'] ?? 0;
            $wht = $data['wht'] ?? 0;
            $isVatInclusive = $data['vatInclude'] ? 1 : 0;
            
            // Calculate VAT amount, WHT amount, and expected amount based on VAT inclusive flag
            $vatAmount = 0;
            $whtAmount = 0;
            $expectedAmount = 0;
            
            if ($isVatInclusive) {
                // VAT is already included in the amount
                // Extract base amount: amount = base + (base * vat/100)
                // base = amount / (1 + vat/100)
                $baseAmount = $amount / (1 + ($vat / 100));
                $vatAmount = $amount - $baseAmount;
                $whtAmount = ($baseAmount * $wht) / 100;
                $expectedAmount = $amount - $whtAmount - $vatAmount;
            } else {
                // VAT is added to the amount
                $baseAmount = $amount;
                $vatAmount = ($amount * $vat) / 100;
                $whtAmount = ($amount * $wht) / 100;
                $expectedAmount = $amount - $vatAmount - $whtAmount;
            }

            DB::table('project_invoice')->insert([
                'projectId' => $data['projectId'],
                'InvoiceNumber' => $data['InvoiceNumber'],
                'amount' => $amount,
                'vat' => $vat,
                'wht' => $wht,
                'vatAmount' => round($vatAmount, 2),
                'whtAmount' => round($whtAmount, 2),
                'isVatInclusive' => $isVatInclusive,
                'expectedAmount' => round($expectedAmount, 2),
                'dueDate' => $data['dueDate'],
                'status' => 'Pending', // Default status, not editable during creation
                'createdBy' => Auth::user()->id,
                'createdAt' => now(),
                'updateAt' => now(),
            ]);
            return back()->with('message', 'New invoice successfully added.');
        }
        
        if (isset($_POST['update'])) {
            $this->validate($request, [
                'projectId' => 'required|integer',
                'InvoiceNumber' => 'required|string|unique:project_invoice,InvoiceNumber,' . $request->input('id'),
                'amount' => 'required|numeric|min:0',
                'vat' => 'nullable|numeric|min:0|max:100',
                'wht' => 'nullable|numeric|min:0|max:100',
                'dueDate' => 'required|date',
                'status' => 'nullable|string',
                'id' => 'required|integer',
            ]);

            // Get form values
            $amount = $data['amount'];
            $vat = $data['vat'] ?? 0;
            $wht = $data['wht'] ?? 0;
            $isVatInclusive = $data['vatInclude'] ? 1 : 0;
            
            // Calculate VAT amount, WHT amount, and expected amount based on VAT inclusive flag
            $vatAmount = 0;
            $whtAmount = 0;
            $expectedAmount = 0;
            
            if ($isVatInclusive) {
                // VAT is already included in the amount
                // Extract base amount: amount = base + (base * vat/100)
                // base = amount / (1 + vat/100)
                $baseAmount = $amount / (1 + ($vat / 100));
                $vatAmount = $amount - $baseAmount;
                $whtAmount = ($baseAmount * $wht) / 100;
                $expectedAmount = $amount - $whtAmount - $vatAmount;
            } else {
                // VAT is added to the amount
                $baseAmount = $amount;
                $vatAmount = ($amount * $vat) / 100;
                $whtAmount = ($amount * $wht) / 100;
                $expectedAmount = $amount - $vatAmount - $whtAmount;
            }

            $updateData = [
                'projectId' => $data['projectId'],
                'InvoiceNumber' => $data['InvoiceNumber'],
                'amount' => $amount,
                'vat' => $vat,
                'wht' => $wht,
                'vatAmount' => round($vatAmount, 2),
                'whtAmount' => round($whtAmount, 2),
                'isVatInclusive' => $isVatInclusive,
                'expectedAmount' => round($expectedAmount, 2),
                'dueDate' => $data['dueDate'],
                'status' => $data['status'] ?? 'Pending',
                'updateAt' => now(),
            ];

            // If status is being changed to Validated/Approved, set validatedBy and validatedAt
            $currentInvoice = DB::table('project_invoice')->where('id', $data['id'])->first();
            if ($currentInvoice && ($currentInvoice->status != 'Validated' && $currentInvoice->status != 'Approved') && 
                ($data['status'] == 'Validated' || $data['status'] == 'Approved')) {
                $updateData['validatedBy'] = Auth::user()->id;
                $updateData['validatedAt'] = now();
            }

            DB::table('project_invoice')->where('id', $data['id'])->update($updateData);
            return back()->with('message', 'Invoice successfully updated.');
        }
        
        if (isset($_POST['del'])) {
            $del = $request->input('deleteid');
            DB::table('project_invoice')->where('id', $del)->delete();
            return back()->with('message', 'Invoice successfully deleted.');
        }
        
        // Fetch projects list
        $data['projects'] = DB::table('projects')
            ->select('id', 'name', 'projectCode')
            ->orderBy('name', 'asc')
            ->get();
        
        // Fetch project name and client name for selected project
        $data['projectName'] = '';
        $data['clientName'] = '';
        if (!empty($data['projectId'])) {
            $projectInfo = DB::table('projects')
                ->leftJoin('clients', 'projects.clientId', '=', 'clients.id')
                ->where('projects.id', $data['projectId'])
                ->select(
                    'projects.name as projectName',
                    'clients.name as clientName'
                )
                ->first();
            
            if ($projectInfo) {
                $data['projectName'] = $projectInfo->projectName ?? '';
                $data['clientName'] = $projectInfo->clientName ?? '';
            }
        }
        
        // Fetch invoices for selected project
        $data['invoices'] = collect();
        if (!empty($data['projectId'])) {
            $data['invoices'] = DB::table('project_invoice')
                ->leftJoin('projects', 'project_invoice.projectId', '=', 'projects.id')
                ->leftJoin('users as creator', 'project_invoice.createdBy', '=', 'creator.id')
                ->leftJoin('users as validator', 'project_invoice.validatedBy', '=', 'validator.id')
                ->where('project_invoice.projectId', $data['projectId'])
                ->select(
                    'project_invoice.id',
                    'project_invoice.InvoiceNumber',
                    'project_invoice.projectId',
                    'project_invoice.amount',
                    'project_invoice.vat',
                    'project_invoice.wht',
                    'project_invoice.vatAmount',
                    'project_invoice.whtAmount',
                    'project_invoice.isVatInclusive',
                    'project_invoice.expectedAmount',
                    'project_invoice.dueDate',
                    'project_invoice.status',
                    'project_invoice.createdAt',
                    'project_invoice.validatedAt',
                    'projects.name as projectName',
                    'projects.projectCode',
                    'creator.name as createdByName',
                    'validator.name as validatedByName'
                )
                ->orderBy('project_invoice.createdAt', 'desc')
                ->get();
        }
        
        return view('Project.projectinvoice', $data);
    }

    public function vendorProject(Request $request)
    {
        $data['projectId'] = $request->input('projectId');
        $data['vendorId'] = $request->input('vendorId');
        $data['description'] = $request->input('description');
        $data['quantity'] = $request->input('quantity');
        $data['unitCost'] = $request->input('unitCost');
        $data['amount'] = $request->input('amount');
        $data['status'] = $request->input('status');
        $data['id'] = $request->input('id');
        $data['projectClassification'] = null;
        // Handle project selection - reload page with selected project
        if ($request->has('select_project')) {
            $data['projectId'] = $request->input('projectId');
            Session(['selected_vendor_project_id' => $data['projectId']]);
        }
        
        // Get selected project from session if not in request
        if (empty($data['projectId'])) {
            $data['projectId'] = Session::get('selected_vendor_project_id');
        }

        // Check that selected project category has expense_classificationId = 1
        
        
        if (isset($_POST['addnew'])) {
            if (!empty($data['projectId'])) {
                $projectClassification = DB::table('projects')
                    ->join('project_categories_expense_classification', 'projects.categoryId', '=', 'project_categories_expense_classification.project_categoryId')
                    ->where('projects.id', $data['projectId'])
                    ->where('project_categories_expense_classification.expense_classificationId', 1)
                    ->select(
                        'projects.categoryId as projectCategoryId',
                        'project_categories_expense_classification.expense_classificationId'
                    )
                    ->first();
    
                if (!$projectClassification) {
                    return back()->with('error_message', 'Project category is not configured  for  vendor delivery.');
                }
    
                $data['projectClassification'] = $projectClassification->projectCategoryId;
            }
            $this->validate($request, [
                'projectId' => 'required|integer',
                'vendorId' => 'required|integer',
                'description' => 'nullable|string',
                'quantity' => 'required|numeric|min:0',
                'unitCost' => 'required|numeric|min:0',
                'status' => 'nullable|string',
            ]);

            // Calculate amount
            $quantity = $data['quantity'];
            $unitCost = $data['unitCost'];
            $calculatedAmount = $quantity * $unitCost;

            DB::table('vendor_projects')->insert([
                'projectId' => $data['projectId'],
                'vendorId' => $data['vendorId'],
                'description' => $data['description'] ?? null,
                'quantity' => $quantity,
                'unitCost' => $unitCost,
                'amount' => $calculatedAmount,
                'status' => 'Pending', // Always set to Pending on creation
                'createdBy' => Auth::user()->id,
                'createdAt' => now(),
                'updateAt' => now(),
            ]);
            return back()->with('message', 'New vendor project successfully added.');
        }
        
        if (isset($_POST['update'])) {
            $this->validate($request, [
                'projectId' => 'required|integer',
                'vendorId' => 'required|integer',
                'description' => 'nullable|string',
                'quantity' => 'required|numeric|min:0',
                'unitCost' => 'required|numeric|min:0',
                'status' => 'nullable|string',
                'id' => 'required|integer',
            ]);

            // Calculate amount
            $quantity = $data['quantity'];
            $unitCost = $data['unitCost'];
            $calculatedAmount = $quantity * $unitCost;

            DB::table('vendor_projects')->where('id', $data['id'])->update([
                'vendorId' => $data['vendorId'],
                'description' => $data['description'] ?? null,
                'quantity' => $quantity,
                'unitCost' => $unitCost,
                'amount' => $calculatedAmount,
                'status' => $data['status'] ?? 'Pending',
                'updateAt' => now(),
            ]);
            return back()->with('message', 'Vendor project successfully updated.');
        }
        
        if (isset($_POST['approve'])) {
            $approveId = $request->input('approveid');
            $refno=$this->RefNo();

            $approvalData = DB::table('vendor_projects')
                ->leftJoin('budgets', 'vendor_projects.vendorId', '=', 'budgets.id')
                ->leftJoin('projects', 'vendor_projects.projectId', '=', 'projects.id')
                ->leftJoin('project_expense_ledger', function ($join) {
                    $join->on('vendor_projects.projectId', '=', 'project_expense_ledger.projectId')
                        ->where('project_expense_ledger.classificationId', 1);
                })
                ->where('vendor_projects.id', $approveId)
                ->select(
                    'vendor_projects.id',
                    'vendor_projects.status',
                    'vendor_projects.projectId',
                    'vendor_projects.amount',
                    'budgets.accountId',
                    'budgets.name as vendorName',
                    'project_expense_ledger.expenseAccountId',
                    'projects.name as projectName'
                )
                ->first();

            if (!$approvalData) {
                return back()->with('error_message', 'Vendor project record was not found.');
            }

            if ($approvalData->status === 'Approved') {
                return back()->with('error_message', 'This vendor project has already been approved.');
            }

            if (empty($approvalData->accountId)) {
                return back()->with('error_message', "No account is configured for vendor budget '{$approvalData->vendorName}'.");
            }

            if (empty($approvalData->expenseAccountId)) {
                return back()->with('error_message', "No  expense ledger is configured for SCB Delivery '{$approvalData->projectName}'.");
            }

            if (!$this->FetchAccountCodes($approvalData->accountId)) {
                return back()->with('error_message', "Vendor account ID '{$approvalData->accountId}' was not found in chart of accounts.");
            }

            if (!$this->FetchAccountCodes($approvalData->expenseAccountId)) {
                return back()->with('error_message', "Project expense account ID '{$approvalData->expenseAccountId}' was not found in chart of accounts.");
            }

            if ((float) $approvalData->amount <= 0) {
                return back()->with('error_message', 'Vendor project amount must be greater than zero before approval.');
            }

            $transDate = now()->format('Y-m-d');
            $remark = 'Vendor project approved';
            $userId = Auth::user()->id;

            $this->CreditAccount(
                $approvalData->accountId,
                $approvalData->amount,
                $refno,
                $transDate,
                $remark,
                $userId,
                $refno,
                $approvalData->projectId
            );

            $this->DebitAccount(
                $approvalData->expenseAccountId,
                $approvalData->amount,
                $refno,
                $transDate,
                $remark,
                $userId,
                $refno,
                $approvalData->projectId
            );

            DB::table('vendor_projects')->where('id', $approveId)->update([
                'status' => 'Approved',
                'approvedBy' => Auth::user()->id,
                'updateAt' => now(),
            ]);
            return back()->with('message', 'Vendor project successfully approved.');
        }
        
        if (isset($_POST['del'])) {
            $del = $request->input('deleteid');
            
            // Check if status is Approved - cannot delete if approved
            $vendorProject = DB::table('vendor_projects')->where('id', $del)->first();
            if ($vendorProject && $vendorProject->status == 'Approved') {
                return back()->with('error_message', 'Cannot delete vendor project with Approved status.');
            }
            
            DB::table('vendor_projects')->where('id', $del)->delete();
            return back()->with('message', 'Vendor project successfully deleted.');
        }
        
        // Fetch projects list
        $data['projects'] = DB::table('projects')
            ->select('id', 'name', 'projectCode')
            ->orderBy('name', 'asc')
            ->get();
        
        // Fetch vendors list (where isVendor = 1)
        $data['vendors'] = DB::table('budgets')
            ->where('isVendor', 1)
            ->select('id', 'name', 'description')
            ->orderBy('name', 'asc')
            ->get();
        
        // Fetch vendor projects for selected project
        $data['vendorProjects'] = collect();
        if (!empty($data['projectId'])) {
            $data['vendorProjects'] = DB::table('vendor_projects')
                ->leftJoin('projects', 'vendor_projects.projectId', '=', 'projects.id')
                ->leftJoin('budgets as vendor', 'vendor_projects.vendorId', '=', 'vendor.id')
                ->leftJoin('users as creator', 'vendor_projects.createdBy', '=', 'creator.id')
                ->leftJoin('users as approver', 'vendor_projects.approvedBy', '=', 'approver.id')
                ->where('vendor_projects.projectId', $data['projectId'])
                ->select(
                    'vendor_projects.id',
                    'vendor_projects.projectId',
                    'vendor_projects.vendorId',
                    'vendor_projects.description',
                    'vendor_projects.quantity',
                    'vendor_projects.unitCost',
                    'vendor_projects.amount',
                    'vendor_projects.status',
                    'vendor_projects.createdAt',
                    'vendor_projects.updateAt',
                    'projects.name as projectName',
                    'projects.projectCode',
                    'vendor.name as vendorName',
                    'creator.name as createdByName',
                    'approver.name as approvedByName'
                )
                ->orderBy('vendor_projects.createdAt', 'desc')
                ->get();
        }
        
        return view('Project.vendorproject', $data);
    }

    public function vendorProjectReport(Request $request)
    {
        $data['vendorId'] = $request->input('vendorId');
        $data['projectId'] = $request->input('projectId');
        $data['status'] = $request->input('status');
        $data['startDate'] = $request->input('startDate');
        $data['endDate'] = $request->input('endDate');
        
        // Handle filter form submission
        if ($request->has('select_vendor') && $request->input('select_vendor') == '1') {
            $data['vendorId'] = $request->input('vendorId');
            $data['projectId'] = $request->input('projectId');
            if (!empty($data['vendorId']) && $data['vendorId'] != 'all') {
                Session(['selected_vendor_report_id' => $data['vendorId']]);
            } else {
                Session::forget('selected_vendor_report_id');
            }
            if (!empty($data['projectId']) && $data['projectId'] != 'all') {
                Session(['selected_project_report_id' => $data['projectId']]);
            } else {
                Session::forget('selected_project_report_id');
            }
        }
        
        // Get selected vendor from session if not in request (default to 'all')
        if (empty($data['vendorId']) || $data['vendorId'] == '') {
            $data['vendorId'] = Session::get('selected_vendor_report_id', 'all');
        }
        
        // Get selected project from session if not in request (default to 'all')
        if (empty($data['projectId']) || $data['projectId'] == '') {
            $data['projectId'] = Session::get('selected_project_report_id', 'all');
        }
        
        // Fetch vendors list (where isVendor = 1)
        $data['vendors'] = DB::table('budgets')
            ->where('isVendor', 1)
            ->select('id', 'name', 'description')
            ->orderBy('name', 'asc')
            ->get();
        
        // Fetch projects list
        $data['projects'] = DB::table('projects')
            ->select('id', 'name', 'projectCode')
            ->orderBy('name', 'asc')
            ->get();
        
        // Fetch vendor projects with optional filters
        $data['vendorProjects'] = collect();
        $data['totalAmount'] = 0;
        
        // Build query - always fetch data (no requirement for vendor/project selection)
        $query = DB::table('vendor_projects')
            ->leftJoin('projects', 'vendor_projects.projectId', '=', 'projects.id')
            ->leftJoin('budgets as vendor', 'vendor_projects.vendorId', '=', 'vendor.id')
            ->leftJoin('users as creator', 'vendor_projects.createdBy', '=', 'creator.id')
            ->leftJoin('users as approver', 'vendor_projects.approvedBy', '=', 'approver.id');
        
        // Apply vendor filter if not "all"
        if (!empty($data['vendorId']) && $data['vendorId'] != 'all') {
            $query->where('vendor_projects.vendorId', $data['vendorId']);
        }
        
        // Apply project filter if not "all"
        if (!empty($data['projectId']) && $data['projectId'] != 'all') {
            $query->where('vendor_projects.projectId', $data['projectId']);
        }
        
        // Apply status filter if provided
        if (!empty($data['status'])) {
            $query->where('vendor_projects.status', $data['status']);
        }
        
        // Apply date filters if provided
        if (!empty($data['startDate'])) {
            $query->whereDate('vendor_projects.createdAt', '>=', $data['startDate']);
        }
        if (!empty($data['endDate'])) {
            $query->whereDate('vendor_projects.createdAt', '<=', $data['endDate']);
        }
        
        $data['vendorProjects'] = $query
            ->select(
                'vendor_projects.id',
                'vendor_projects.projectId',
                'vendor_projects.vendorId',
                'vendor_projects.quantity',
                'vendor_projects.unitCost',
                'vendor_projects.amount',
                'vendor_projects.status',
                'vendor_projects.createdAt',
                'vendor_projects.updateAt',
                'projects.name as projectName',
                'projects.projectCode',
                'vendor.name as vendorName',
                'creator.name as createdByName',
                'approver.name as approvedByName'
            )
            ->orderBy('vendor_projects.createdAt', 'desc')
            ->get();
        
        $data['totalAmount'] = $data['vendorProjects']->sum('amount');
        
        return view('Project.vendorprojectreport', $data);
    }
   




    

}
