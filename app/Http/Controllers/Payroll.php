<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Auth;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
class Payroll extends Basefunction 
{
  public function VariableContributionSetup(Request $request)
  {
    $data['title'] = $request->input('title');
    $data['staff_percentage'] = $request->input('staff_percentage');
    $data['company_percentage'] = $request->input('company_percentage');
    $data['variableId'] = $request->input('variableId');
    $data['status'] = $request->input('status');
    $data['id'] = $request->input('id');

    if (isset($_POST['update'])) {
    //   $this->validate($request, [
    //     'id' => 'required|integer|exists:variable_contribution_setup_map,id',
    //     'title' => 'required|string',
    //     'variableId' => 'required|integer|exists:tblpayroll_variable,id|unique:variable_contribution_setup_map,variableId,' . $data['id'],
    //     'staff_percentage' => 'required|numeric|min:0|max:100',
    //     'company_percentage' => 'required|numeric|min:0|max:100',
    //   ]);
	  $this->validate($request, [
        'id' => 'required|integer|exists:variable_contribution_setup_map,id',
        'title' => 'required|string',
        'variableId' => 'required|integer',
        'staff_percentage' => 'required|numeric|min:0|max:100',
        'company_percentage' => 'required|numeric|min:0|max:100',
        'status' => 'required|string|in:Active,Inactive',
      ]);

      DB::table('variable_contribution_setup_map')
        ->where('id', $data['id'])
        ->update([
          'title' => $data['title'],
          'staff_percentage' => $data['staff_percentage'],
          'company_percentage' => $data['company_percentage'],
          'variableId' => $data['variableId'],
          'status' => $data['status'],
        ]);

      return back()->with('message', 'Record successfully updated.');
    }

    
    $data['PayrollVariables'] = DB::table('tblpayroll_variable')
      ->select('id', 'variable', 'ref_code', 'variable_type', 'status')
	  ->orderBy('variable_type', 'asc')
	  ->orderBy('rank', 'asc')
      ->orderBy('variable', 'asc')
      ->get();

    $data['ContributionMaps'] = DB::table('variable_contribution_setup_map as m')
      ->leftJoin('tblpayroll_variable as v', 'm.variableId', '=', 'v.id')
      ->select(
        'm.id',
        'm.title',
        'm.staff_percentage',
        'm.company_percentage',
        'm.variableId',
        'm.status',
        'v.variable as variableName',
        'v.ref_code as variableCode'
      )
      ->orderBy('m.id', 'desc')
      ->get();

    return view('Payroll.variable_contribution_setup', $data);
  }

  public function ActivePeriod(Request $request)
   {
    $active_period=$this->Payroll_Active_period();
    $data['active_period'] = $active_period;
    $data['cyear'] = $active_period->year;
    $data['cmonth'] = $active_period->monthtx;
    $data['year']=$request->input('year');
   	$data['month']=$request->input('month');
   	$data['id']=$request->input('id');
   	$data['Months']=$this->Months();
    $data['mandateMessage'] = $request->input('mandateMessage');
   	if ( isset( $_POST['update'] ) ) {
   	    $this->validate($request, [
              'year'      => 'required|string',
              'month'      => 'required|string',
            ]);
   	    
   	    $proposedYear = $data['year'];
   	    $proposedMonth = $data['month'];
   	    
   	    // Check if there are any unlocked records (active period)
   	    $unlockedRecords = DB::table('tblpayroll_payment')
   	        ->where('isLocked', 0)
   	        ->select('year', 'month')
   	        ->distinct()
   	        ->get();
   	    
   	    // If there are unlocked records, check if they match the proposed period
   	    if($unlockedRecords->count() > 0) {
   	        $existingYear = $unlockedRecords->first()->year;
   	        $existingMonth = $unlockedRecords->first()->month;
   	        
   	        // Check if there are multiple different periods unlocked
   	        $uniquePeriods = $unlockedRecords->unique(function ($item) {
   	            return $item->year . '-' . $item->month;
   	        });
   	        
   	        if($uniquePeriods->count() > 1) {
   	            return back()->with('error_message', 'Multiple active periods detected. Please lock all payroll records before updating the active period.');
   	        }
   	        
   	        // If the proposed period is different from existing active period, prevent update
   	        if($existingYear != $proposedYear || $existingMonth != $proposedMonth) {
   	            // Get month name for display
   	            $monthName = '';
   	            foreach($data['Months'] as $m) {
   	                if($m->id == $existingMonth) {
   	                    $monthName = $m->month;
   	                    break;
   	                }
   	            }
   	            return back()->with('error_message', 'Cannot update active period. There is an existing active period (' . $existingYear . ' - ' . $monthName . ') with unlocked records. Please lock all payroll records for that period before updating.');
   	        }
   	    }
   	    
   	    // Proceed with update if validation passes
   	     DB::table('tblpayroll_active_period')->update( [ 'year' =>$data['year'],'month' =>$data['month'], 'mandateMessage' =>$data['mandateMessage'] ]);
    	   return back()->with('message','Successfully updated.'  );
         }
	return view('Payroll.activesetup', $data);
	    
   }
   public function SalaryComputation(Request $request)
   {
    $active_period=$this->Payroll_Active_period();
    $year=$active_period->year;//'2019';
    $month =$active_period->month;//'1';
    $mandateMessage = $active_period->mandateMessage;
    $data['year'] = $active_period->year;;
    $data['month'] = $active_period->monthtx;
   	$data['id']=$request->input('id');
   	if ( isset( $_POST['compute'] ) ) {
      // check if salary is lock
      if(DB::table('tblpayroll_payment')->where('month',$month)->where('year',$year)->where('isLocked', 1)->first()){
        return back()->with('error_message', 'This period is already locked salary cannot be computer for this period.'); 
      }
   	    DB::delete("DELETE FROM `tblstaff_monthly_cv` WHERE `year`='$year' and `month`='$month'");
           $data['Staffs'] = $this->StaffsForSalaryComputation('','');
           DB::delete("DELETE FROM `tblpayroll_payment` WHERE `month`='$month' and `year`='$year'");
            DB::delete("DELETE FROM `tblpayroll_variable_monthly` WHERE `month`='$month' and `year`='$year'");
            $curVariable= DB::Select("SELECT * FROM `tblpayroll_variable` WHERE `status`=1 ");
            foreach($curVariable as $curv){
            DB::table('tblpayroll_variable_monthly')->insert(array(
    			'variableid'    	=> $curv->id,
                'year'    	=> $year,
    			'month'	    	=> $month,
          
          
    			'variable_type'    	    => $curv->variable_type,	
    			'variable'            => $curv->variable,
    			'status'      => $curv->status,
    			'statutory'      => $curv->statutory,
    			'istaxable'      => $curv->istaxable,
    			'rank'      => $curv->rank,
    			'ref_code'      => $curv->ref_code,
		    ));
            }
			$data['ContributionMaps'] = DB::table('variable_contribution_setup_map as m')
			->leftJoin('tblpayroll_variable as v', 'm.variableId', '=', 'v.id')
			->where('m.status','Active')
			->whereNotNull('m.tb_code')
			->where('m.tb_code', '!=', '')
			->select(
				'm.id',
				'm.title',
				'm.staff_percentage',
				'm.company_percentage',
				'm.variableId',
				'v.variable as variableName',
				'v.ref_code as variableCode',
				'm.tb_code'
			)
			->orderBy('m.id', 'desc')
			->get();

	  
           foreach($data['Staffs'] as $v){
              $id=DB::table('tblpayroll_payment')->insertGetId([
                'staffid' => $v->id ,
                'staff_no' => $v->staff_no ,
                'fullname' =>$v->first_name." ". $v->middle_name." ". $v->last_name ,
                'grade' =>$v->grade ,
                'year' => $year ,
                'month' => $month ,
                'mandateMessage' => $mandateMessage,
                'bankid' => $v->bankid ,
                'account_no' => $v->account_no ,
    	        ]);  
    	      
    	     foreach($this->EarningVariable() as $v2){
				// dd($v);
             DB::table('tblpayroll_payment')
             ->where('id',$id)
            //  ->update( [ $v2->ref_code => $this->VariableValue($year, $month, $v2->ref_code, $v->id, $v->grade,1,)]);
			->update( [ $v2->ref_code => $this->VariableValue2($year, $month, $v2, $v,1,)]);
            }
            foreach($this->StatutoryVariableWithBeforeTaxFirst() as $v2){
             DB::table('tblpayroll_payment')
             ->where('id',$id)
            //  ->update( [ $v2->ref_code => -$this->VariableValue($year, $month,$v2->ref_code,$v->id,$v->grade,1)]);
			->update( [ $v2->ref_code => -$this->VariableValue2($year, $month,$v2,$v,1)]);
            }
			$staff_payment= DB::table('tblpayroll_payment')
			->where('id',$id)->first();
            $contributionUpdates = [];
            foreach ($data['ContributionMaps'] as $mapp){
				$tbCode = $mapp->tb_code ?? null;
				if (!is_string($tbCode) || $tbCode === '') {
					continue;
				}

				$variableCode = $mapp->variableCode ?? null;
				$currentAmount = 0;
				if (is_string($variableCode) && $variableCode !== '' && isset($staff_payment->{$variableCode})) {
					$currentAmount = (float) ($staff_payment->{$variableCode} ?? 0);
				}

				$contributionUpdates[$tbCode] = $this->employerContribution($currentAmount, $mapp);
			}

            if (!empty($contributionUpdates)) {
                DB::table('tblpayroll_payment')
                    ->where('id', $id)
                    ->update($contributionUpdates);
            }
           }
    	   return back()->with('message','Successfully computed.'  );
         }
    
	return view('Payroll.computation', $data);
	    
   }
    public function SalaryChart(Request $request)
   {
    
   	$data['grade']=$request->input('grade');
   	
         if ( isset( $_POST['update'] ) ) {
            $this->validate($request, [
              'grade'      => 'required|string',
            ]);
            if(!$this->GradeChart($data['grade'],1,1)){
              DB::table('tblpayroll_salary_new_chart')->insertGetId([
    	          'grade' => $data['grade'] ,
    	          'step' =>$request->input('step')!=null?$request->input('step'):1  ,
    	          'emp_type' => $request->input('emp_type')!=null?$request->input('emp_type'):1 ,
    	        ]);  
            }
            
            
            foreach($this->EarningVariable() as $v){
             DB::table('tblpayroll_salary_new_chart')->where('grade',$data['grade'])
             ->where('step',$request->input('step')!=null? $request->input('step'):1)
             ->where('emp_type',$request->input('emp_type') !=null?$request->input('emp_type'):1)
             ->update( [ $v->ref_code => $request->input($v->ref_code) ]);
            }
            foreach($this->DeductionVariable() as $v){
            DB::table('tblpayroll_salary_new_chart')->where('grade',$data['grade'])
             ->where('step',$request->input('step')!=null?$request->input('step'):1)
             ->where('emp_type',$request->input('emp_type')!=null?$request->input('emp_type'):1)
             ->update( [ $v->ref_code => $request->input($v->ref_code) ]);
            }
            
    	        return back()->with('message','record successfully updated.'  );
         }
         
   
    $data['Grade'] = $this->Grade();
    $data['GradeChart'] = $this->GradeChart($data['grade'],1,1);
    $data['EarningVariable'] = $this->EarningVariable();
    $data['DeductionVariable'] = $this->DeductionVariable();
    $data['SalaryChart'] = $this->SalaryCharts();
    //dd($data['SalaryChart']);
	return view('Payroll.salary_chart', $data);
	    
   }
   
    public function ControlVariable(Request $request)
   {
   	$data['variabletype']=$request->input('variabletype');
   	$data['variable']=$request->input('variable');
   	$data['statutory']=$request->input('statutory');
   	$data['taxable']=$request->input('taxable');
   	$data['isPensionable']=$request->input('isPensionable');
   	$data['isFunction']=$request->input('isFunction');
   	$data['isbefore_tax']=$request->input('isbefore_tax');
   	$data['percent']=$request->input('percent');
   	$data['rank']=$request->input('rank');
   	
   	if ( isset( $_POST['addnew'] ) ) {
            $this->validate($request, [
              'variable'        => 'required|string|unique:tblpayroll_variable,variable',
              'variabletype'    => 'required|string',
            ]);
            $id=DB::table('tblpayroll_variable')->insertGetId([
	          'variable_type' => $data['variabletype'] ,
	          'variable' => $data['variable'] ,
	          'statutory' => ($data['statutory']=='on')? 1:0 ,
	          'istaxable' => ($data['taxable']=='on')? 1:0 ,
	          'isPensionable' => ($data['isPensionable']=='on')? 1:0 ,
	          'isFunction' => ($data['isFunction']=='on')? 1:0 ,
	          'isbefore_tax' => ($data['variabletype']==2 && ($data['isbefore_tax']=='on'))? 1:0 ,
	          'percent' => $data['percent'] ? $data['percent'] : 0 ,
	          'rank' => $data['rank'] ,
	        ]);
	        
	        // Save function control variables if isFunction is true and variable_type is 2 (deduction)
	        if(($data['isFunction']=='on') && $data['variabletype']==2 && $request->has('selected_earnings')) {
	            $selectedEarnings = $request->input('selected_earnings');
	            if(is_array($selectedEarnings)) {
	                foreach($selectedEarnings as $earningId) {
	                    DB::table('functions_control_variables')->insert([
	                        'control_variabeId' => $id,
	                        'added_control_variableId' => $earningId
	                    ]);
	                }
	            }
	        }
    	   $newfield=$data['variabletype']."_".$id;
    	   DB::table('tblpayroll_variable')->where('id',$id)->update([ 'ref_code' => $newfield ]);
    	   if(!Schema::hasColumn('tblpayroll_salary_new_chart', $newfield)) $this->NewVariable( $newfield);
    	   return back()->with('message','New record successfully added.'  );
         }
         if ( isset( $_POST['update'] ) ) {
            $this->validate($request, [
              'variable'      => 'required|string|unique:tblpayroll_variable,variable,'.$request->input('id'),
              'id'      => 'required|string',
            ]);
             $variableId = $request->input('id');
             $variableInfo = DB::table('tblpayroll_variable')->where('id', $variableId)->first();
             
             DB::table('tblpayroll_variable')->where('id',$variableId)->update([
    	          'variable' => $data['variable'] ,
    	          'statutory' => ($data['statutory']=='on')? 1:0 ,
    	          'istaxable' => ($data['taxable']=='on')? 1:0 ,
    	          'isPensionable' => ($data['isPensionable']=='on')? 1:0 ,
    	          'isFunction' => ($data['isFunction']=='on')? 1:0 ,
    	          'isbefore_tax' => ($variableInfo && $variableInfo->variable_type==2 && ($data['isbefore_tax']=='on'))? 1:0 ,
    	          'percent' => $data['percent'] ? $data['percent'] : 0 ,
    	          'status' => ($request->input('status')=='on')? 1:0 ,
    	          'rank' => $data['rank'] ,
    	        ]);
    	        
    	        // Update function control variables if isFunction is true and variable_type is 2 (deduction)
    	        if($variableInfo && ($data['isFunction']=='on') && $variableInfo->variable_type==2) {
    	            // Delete existing selections
    	            DB::table('functions_control_variables')->where('control_variabeId', $variableId)->delete();
    	            
    	            // Insert new selections
    	            if($request->has('selected_earnings')) {
    	                $selectedEarnings = $request->input('selected_earnings');
    	                if(is_array($selectedEarnings)) {
    	                    foreach($selectedEarnings as $earningId) {
    	                        DB::table('functions_control_variables')->insert([
    	                            'control_variabeId' => $variableId,
    	                            'added_control_variableId' => $earningId
    	                        ]);
    	                    }
    	                }
    	            }
    	        } else {
    	            // If isFunction is false, delete all related records
    	            DB::table('functions_control_variables')->where('control_variabeId', $variableId)->delete();
    	        }
    	        
    	        return back()->with('message','record successfully updated.'  );
         }
        if ( isset( $_POST['del'] ) ) {
        $del = $request->input('deleteid');

        // Get ref_code
        $ref_code = DB::table('tblpayroll_variable')
            ->where('id', $del)
            ->value('ref_code');

        if (!$ref_code) {
            return back()->with('error_message', 'Invalid record selected.');
        }

        // Check Payroll Payment
        $payrollSum = DB::table('tblpayroll_payment')
            ->sum($ref_code);

        if ($payrollSum > 0) {
            return back()->with(
                'error_message',
                'This Variable has computed value in Payroll Report. Hence, record cannot be deleted!'
            );
        }

        // Check Salary Chart
        $salaryChartSum = DB::table('tblpayroll_salary_new_chart')
            ->sum($ref_code);
// dd($salaryChartSum);
        if ($salaryChartSum > 0) {
            return back()->with(
                'error_message',
                'This Variable has value in Salary Chart. Hence, record cannot be deleted!'
            );
        }

        // Check Staff Control Variable
        $staffCvSum = DB::table('tblstaff_cv')
            ->where('ref_code', $ref_code)
            ->sum('amount_monthly');

        if ($staffCvSum > 0) {
            return back()->with(
                'error_message',
                'This Variable has value in Staff Control Variable. Hence, record cannot be deleted!'
            );
        }
        // Delete record
        DB::table('tblpayroll_variable')
            ->where('id', $del)
            ->delete();

        // Drop column or related variable
        $this->DropVariable($ref_code);

        return back()->with('message', 'Record successfully trashed.');
    }
    
   $data['PayrollVariable'] = $this->AllPayrollVariable($data['variabletype']);
    $data['VariableType'] = $this->VariableType();
    // Get statutory earnings for function control variables
    $data['StatutoryEarnings'] = DB::table('tblpayroll_variable')
        ->where('variable_type', 1)
        ->where('statutory', 1)
        ->where('status', 1)
        ->orderBy('rank')
        ->get();
	return view('Payroll.variable_definition', $data);
	    
   }
   
   public function GetFunctionControlVariables(Request $request)
   {
       $controlVariableId = $request->input('control_variable_id');
       
       $selectedEarnings = DB::table('functions_control_variables')
           ->where('control_variabeId', $controlVariableId)
           ->pluck('added_control_variableId')
           ->toArray();
       
       return response()->json([
           'success' => true,
           'selectedEarnings' => $selectedEarnings
       ]);
   }
   
   public function StaffControlVariable(Request $request)
   {
   	$data['variabletype']=$request->input('variabletype');
   	$data['variable']=$request->input('variable');
   	$data['staffid']=$request->input('staffid');
   	
   	if($data['staffid']==''){$data['staffid']=Session::get('staffid');}
   	Session(['staffid' => $data['staffid']]);
   	$data['amount']=    $request->input('amount')!=''? $request->input('amount'):0;
   	$data['targetamount']=$request->input('targetamount')!=''? $request->input('targetamount'):0;
   	$data['continuity']=$request->input('continuity');
   	if ( isset( $_POST['addnew'] ) ) {
   	    
            $this->validate($request, [
              'staffid'        => 'required',
              'variable'    => 'required|string',
              'amount'      => 'required|numeric|between:0,9999999999999999.99',
            ]);
            $cvinfo= $this->VariableInfo($data['variable']);
            DB::table('tblstaff_cv')->insertGetId([
    	          'staffid' => $data['staffid'] ,
    	          'cvid' => $data['variable'] ,
    	          'ref_code' => $cvinfo->ref_code ,
    	          'cv_type' => $cvinfo->variable_type ,
    	          'amount_monthly' => $data['amount'] ,
    	          'amount_target' => ($data['targetamount']!=0|| (float)$data['targetamount' ] > (float)$data['amount'])? $data['targetamount']:$data['amount'] ,
    	          'is_continous' =>($request->input('continuity')=='on')? 0:1 ,
    	        ]);
    	        
    	        return back()->with('message','New record successfully added.'  );
    	         //return back()->withInput();
         }
         if ( isset( $_POST['update'] ) ) {
            $this->validate($request, [
              'subhead'      => 'required|string|unique:tblaccountsubhead,subhead,'.$request->input('id'),
              'id'      => 'required|string',
            ]);

             DB::table('tblaccountsubhead')->where('id',$data['id'])->update([
    	          'subhead' => $data['subhead'] ,
    	          'afs' => $data['afs'] ,
    	          'rank' => $data['rank'] ,
    	        ]);
    	        return back()->with('message','record successfully updated.'  );
         }
         if ( isset( $_POST['del'] ) ) {
        $del=$request->input('deleteid');
        //if( DB::table('tblaccountchart')->where('subheadid',$del)->first())return back()->with('error_message','Brand exist with product. Hence, record cannot be deleted!'  );
        DB::delete("DELETE FROM `tblstaff_cv` WHERE `id`='$del'");
         return back()->with('message',' Staff control variable successfully deleted.'  );
    }
   
    $data['Staffs'] = $this->Staffs('','');
    $data['PayrollVariable'] = $this->PayrollVariable($data['variabletype']);
    $data['VariableType'] = $this->VariableType();
    $data['StaffVariable']=$this->StaffVariable($data['staffid']);
	return view('Payroll.staffcv', $data);
	    
   }
   
   public function ReportPayroll(Request $request)
   {
   	$data['year']=$request->input('year');
   	$data['month']=$request->input('month');
   	$active_period=$this->Payroll_Active_period();
   	if($data['year']==''){$data['year']=$active_period->year;}
   	if($data['month']==''){$data['month']=$active_period->month;}
    //$data['EarningVariable'] = $this->PEarningVariable($data['year'],$data['month']);
    $data['EarningVariable'] = $this->TaxableEarningVariableTaxable($data['year'],$data['month']);
    $data['NonTaxableEarning'] = $this->NonTaxableEarningVariable($data['year'],$data['month']);
    $data['DeductionVariable'] = $this->PDeductionVariable($data['year'],$data['month']);
    $data['Months'] = $this->Months();
    $data['Payroll']=$this->Payrolls($data['year'],$data['month']);
    $data['MonthlyActiveVariable']=$this->MonthlyActiveVariable($data['year'],$data['month']);
    //dd($this->NetpaySummary($data['year'],$data['month']));
   
	return view('Payroll.payroll', $data);
   }
   public function Payroll_Mandate(Request $request)
   {
   	$data['year']=$request->input('year');
   	$data['month']=$request->input('month');
   	$active_period=$this->Payroll_Active_period();
   	if($data['year']==''){$data['year']=$active_period->year;}
   	if($data['month']==''){$data['month']=$active_period->month;}
    $data['Months'] = $this->Months();
    $data['NetpaySummary']=$this->NetpaySummary($data['year'],$data['month']);
    // dd($data['NetpaySummary']);
	return view('Payroll.payrollmandate', $data);
   }
   public function PayrollParticularReport(Request $request)
   {
   	$data['variable']=$request->input('variable');
    // dd($data['variable']);
   	$data['year']=$request->input('year');
   	$data['month']=$request->input('month');
   	$active_period=$this->Payroll_Active_period();
   	if($data['year']==''){$data['year']=$active_period->year;}
   	if($data['month']==''){$data['month']=$active_period->month;}
    $data['Months'] = $this->Months();
    $data['PayrollVariable'] = $this->PayrollVariable('');
    // dd($data['PayrollVariable']);
    $data['NetpaySummary']=$this->PayrollParticular($data['year'], $data['month'], $data['variable']);
    // dd($data['NetpaySummary']);
    // $data['NetpaySummary']=[];
    $variableData = DB::table('tblpayroll_variable')->where('ref_code', $data['variable'])->first();
    
    $data['variableName'] = $variableData? ($variableData->variable_type==1 ? 'Earning' : 'Deduction') . " - " . $variableData->variable : '';
    $data['PayrollVariable'] = $this->PayrollVariable('');
	return view('Payroll.variablereport', $data);
   }

   public function Payslip(Request $request)
   {
   	$data['year']=$request->input('year');
   	$data['month']=$request->input('month');
   	$data['staffid']=$request->input('staffid');
   	$active_period=$this->Payroll_Active_period();
    $data['Staffs']=$this->Staffs('','');
   	if($data['year']==''){$data['year']=$active_period->year;}
   	if($data['month']==''){$data['month']=$active_period->month;}

    $data['EarningVariable'] = $this->TaxableEarningVariableTaxable($data['year'],$data['month']);
    $data['NonTaxableEarning'] = $this->NonTaxableEarningVariable($data['year'],$data['month']);
    $data['DeductionVariable'] = $this->PDeductionVariable($data['year'],$data['month']);
    $data['Months'] = $this->Months();
    $data['Payroll']=$this->Payroll($data['year'],$data['month'],$data['staffid']);
    $data['MonthlyActiveVariable']=$this->MonthlyActiveVariable($data['year'],$data['month']);
	$data['ContributionMaps'] = DB::table('variable_contribution_setup_map as m')
      ->leftJoin('tblpayroll_variable as v', 'm.variableId', '=', 'v.id')
	  ->where('m.status','Active')
	  ->whereNotNull('m.tb_code')
	  ->where('m.tb_code', '!=', '')
      ->select(
        'm.id',
        'm.title',
        'm.staff_percentage',
        'm.company_percentage',
        'm.variableId',
        'v.variable as variableName',
        'v.ref_code as variableCode',
		'm.tb_code'
      )
      ->orderBy('m.id', 'desc')
      ->get();

	  

    // Map already-calculated employer contribution columns (e.g. c_pension, c_nhis, etc.) using tb_code
    $data['employerContributions'] = [];
    $data['totalEmployerContributions'] = 0;
    if ($data['Payroll']) {
      foreach ($data['ContributionMaps'] as $map) {
        $code = $map->tb_code;
        $amount = 0;
        if (is_string($code) && $code !== '' && isset($data['Payroll']->{$code})) {
          $amount = (float) ($data['Payroll']->{$code} ?? 0);
        }

        $label = $map->title ?: ($map->variableName ?: $code);
        $data['employerContributions'][] = (object) [
          'variable' => $label,
          'tb_code' => $code,
          'amount' => $amount,
        ];
        $data['totalEmployerContributions'] += $amount;
      }
    }
	return view('Payroll.payslip2', $data);
   }

   public function PayrollLock(Request $request)
   {
   	$data['year']=$request->input('year');
   	$data['month']=$request->input('month');
   	$data['Months'] = $this->Months();

   	// Lock payroll
   	if ( isset( $_POST['lock'] ) ) {
   	    $this->validate($request, [
              'year'      => 'required|string',
              'month'      => 'required|string',
            ]);
   	    $year = $data['year'];
   	    $month = $data['month'];
   	    
   	    // Get month name
   	    $monthName = '';
   	    foreach($data['Months'] as $m) {
   	        if($m->id == $month) {
   	            $monthName = $m->month;
   	            break;
   	        }
   	    }
   	    
   	    // Update all records matching year and month to isLocked=1
   	    $updated = DB::table('tblpayroll_payment')
   	        ->where('year', $year)
   	        ->where('month', $month)
   	        ->update(['isLocked' => 1]);
   	    
   	    return back()->with('message','Payroll successfully locked for ' . $year . ' - ' . $monthName . '. ' . $updated . ' record(s) updated.'  );
   	}

   	// Unlock payroll
   	if ( isset( $_POST['unlock'] ) ) {
   	    $this->validate($request, [
              'year'      => 'required|string',
              'month'      => 'required|string',
            ]);
   	    $year = $data['year'];
   	    $month = $data['month'];
   	    
   	    // Get month name
   	    $monthName = '';
   	    foreach($data['Months'] as $m) {
   	        if($m->id == $month) {
   	            $monthName = $m->month;
   	            break;
   	        }
   	    }
   	    
   	    // Check if there's already an unlocked period (excluding the current period being unlocked)
   	    // Get all periods with unlocked records
   	    $allUnlockedPeriods = DB::table('tblpayroll_payment')
   	        ->where('isLocked', 0)
   	        ->select('year', 'month')
   	        ->distinct()
   	        ->get();
   	    
   	    // Check if any period (other than the one being unlocked) has unlocked records
   	    foreach($allUnlockedPeriods as $unlockedPeriod) {
   	        if($unlockedPeriod->year != $year || $unlockedPeriod->month != $month) {
   	            $existingMonthName = '';
   	            foreach($data['Months'] as $m) {
   	                if($m->id == $unlockedPeriod->month) {
   	                    $existingMonthName = $m->month;
   	                    break;
   	                }
   	            }
   	            return back()->with('error_message', 'Cannot unlock this period. There is already an unlocked period (' . $unlockedPeriod->year . ' - ' . $existingMonthName . '). Please lock that period first before unlocking another one.');
   	        }
   	    }
   	    
   	    // Update all records matching year and month to isLocked=0
   	    $updated = DB::table('tblpayroll_payment')
   	        ->where('year', $year)
   	        ->where('month', $month)
   	        ->update(['isLocked' => 0]);
   	    
   	    return back()->with('message','Payroll successfully unlocked for ' . $year . ' - ' . $monthName . '. ' . $updated . ' record(s) updated.'  );
   	}

   	// Trash/Delete payroll period
   	if ( isset( $_POST['trash'] ) ) {
   	    $this->validate($request, [
              'year'      => 'required|string',
              'month'      => 'required|string',
            ]);
   	    $year = $data['year'];
   	    $month = $data['month'];
   	    
   	    // Get month name
   	    $monthName = '';
   	    foreach($data['Months'] as $m) {
   	        if($m->id == $month) {
   	            $monthName = $m->month;
   	            break;
   	        }
   	    }
   	    
   	    // Check if period is locked
   	    $lockedCount = DB::table('tblpayroll_payment')
   	        ->where('year', $year)
   	        ->where('month', $month)
   	        ->where('isLocked', 1)
   	        ->count();
   	    
   	    $totalCount = DB::table('tblpayroll_payment')
   	        ->where('year', $year)
   	        ->where('month', $month)
   	        ->count();
   	    
   	    if($lockedCount > 0 || ($totalCount > 0 && $lockedCount == $totalCount)) {
   	        return back()->with('error_message', 'Cannot delete this period. The period (' . $year . ' - ' . $monthName . ') is locked. Please unlock it first before deleting.');
   	    }
   	    
   	    // Delete all records matching year and month
   	    $deleted = DB::table('tblpayroll_payment')
   	        ->where('year', $year)
   	        ->where('month', $month)
   	        ->delete();
   	    
   	    return back()->with('message','Payroll period (' . $year . ' - ' . $monthName . ') successfully deleted. ' . $deleted . ' record(s) removed.'  );
   	}

   	// Get all unique periods with their lock status
   	$periods = DB::table('tblpayroll_payment')
   	    ->select('year', 'month')
   	    ->distinct()
   	    ->orderBy('year', 'desc')
   	    ->orderBy('month', 'desc')
   	    ->get();
   	
   	$data['Periods'] = [];
   	foreach($periods as $period) {
   	    $totalCount = DB::table('tblpayroll_payment')
   	        ->where('year', $period->year)
   	        ->where('month', $period->month)
   	        ->count();
   	    
   	    $lockedCount = DB::table('tblpayroll_payment')
   	        ->where('year', $period->year)
   	        ->where('month', $period->month)
   	        ->where('isLocked', 1)
   	        ->count();
   	    
   	    $unlockedCount = $totalCount - $lockedCount;
   	    
   	    // Determine status
   	    $status = 'unlocked';
   	    if($lockedCount == $totalCount && $totalCount > 0) {
   	        $status = 'locked';
   	    } elseif($lockedCount > 0 && $unlockedCount > 0) {
   	        $status = 'partial';
   	    }
   	    
   	    // Get month name
   	    $monthName = '';
   	    foreach($data['Months'] as $m) {
   	        if($m->id == $period->month) {
   	            $monthName = $m->month;
   	            break;
   	        }
   	    }
   	    
   	    $data['Periods'][] = [
   	        'year' => $period->year,
   	        'month' => $period->month,
   	        'monthName' => $monthName,
   	        'totalCount' => $totalCount,
   	        'lockedCount' => $lockedCount,
   	        'unlockedCount' => $unlockedCount,
   	        'status' => $status
   	    ];
   	}

	return view('Payroll.payrolllock', $data);
   }
}