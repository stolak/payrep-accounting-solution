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
  public function ActivePeriod(Request $request)
   {
    //   Schema::create('cruds', function (Blueprint $table) {
    //   $table->increments('id');
    //   $table->text('name');
    //   $table->text('color');
    //   $table->timestamps();
    // });
  //Die("kdkdk");
    $active_period=$this->Payroll_Active_period();
    $data['cyear'] = $active_period->year;
    $data['cmonth'] = $active_period->monthtx;
    $data['year']=$request->input('year');
   	$data['month']=$request->input('month');
   	$data['id']=$request->input('id');
   	$data['Months']=$this->Months();
   	if ( isset( $_POST['update'] ) ) {
   	    $this->validate($request, [
              'year'      => 'required|string',
              'month'      => 'required|string',
            ]);
   	     DB::table('tblpayroll_active_period')->update( [ 'year' =>$data['year'],'month' =>$data['month'] ]);
    	   return back()->with('message','Successfully updated.'  );
         }
	return view('Payroll.activesetup', $data);
	    
   }
   public function SalaryComputation(Request $request)
   {
    $active_period=$this->Payroll_Active_period();
    $year=$active_period->year;//'2019';
    $month =$active_period->month;//'1';
    $data['year'] = $active_period->year;;
    $data['month'] = $active_period->monthtx;
   	$data['id']=$request->input('id');
   	if ( isset( $_POST['compute'] ) ) {
   	    DB::delete("DELETE FROM `tblstaff_monthly_cv` WHERE `year`='$year' and `month`='$month'");
           $data['Staffs'] = $this->Staffs('','');
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
           foreach($data['Staffs'] as $v){
              $id=DB::table('tblpayroll_payment')->insertGetId([
                'staffid' => $v->id ,
                'staff_no' => $v->staff_no ,
                'fullname' =>$v->first_name." ". $v->middle_name." ". $v->last_name ,
                'grade' =>$v->grade ,
                'year' => $year ,
                'month' => $month ,
                'bankid' => $v->bankid ,
                'account_no' => $v->account_no ,
    	        ]);  
    	      
    	     foreach($this->EarningVariable() as $v2){
             DB::table('tblpayroll_payment')
             ->where('id',$id)
             ->update( [ $v2->ref_code => $this->VariableValue($year, $month,$v2->ref_code,$v->id,$v->grade,1)]);
            }
            foreach($this->DeductionVariable() as $v2){
             DB::table('tblpayroll_payment')
             ->where('id',$id)
             ->update( [ $v2->ref_code => -$this->VariableValue($year, $month,$v2->ref_code,$v->id,$v->grade,1)]);
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
	          'rank' => $data['rank'] ,
	        ]);
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
             DB::table('tblpayroll_variable')->where('id',$request->input('id'))->update([
    	          'variable' => $data['variable'] ,
    	          'statutory' => ($data['statutory']=='on')? 1:0 ,
    	          'istaxable' => ($data['taxable']=='on')? 1:0 ,
    	          'status' => ($request->input('status')=='on')? 1:0 ,
    	          'rank' => $data['rank'] ,
    	        ]);
    	        return back()->with('message','record successfully updated.'  );
         }
        if ( isset( $_POST['del'] ) ) {
        $del=$request->input('deleteid');
        $ref_code=DB::table('tblpayroll_variable')->where('id', $del)->value('ref_code');
        if(DB::Select("SELECT sum($ref_code) as sumT FROM `tblpayroll_payment`")[0]->sumT > 0)return back()->with('error_message','This Variable have computed value in Payroll Report. Hence, record cannot be deleted!'  );
        if(DB::Select("SELECT sum($ref_code) as sumT FROM `tblpayroll_salary_new_chart`")[0]->sumT > 0)return back()->with('error_message','This Variable have value in Salary Chart. Hence, record cannot be deleted!'  );
        DB::delete("DELETE FROM `tblpayroll_variable` WHERE `id`='$del'");
        $this->DropVariable( $ref_code);
         return back()->with('message',' Record successfully trashed.'  );
    }
    
   $data['PayrollVariable'] = $this->AllPayrollVariable($data['variabletype']);
    $data['VariableType'] = $this->VariableType();
	return view('Payroll.variable_definition', $data);
	    
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
    dd($data['Payroll']);
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
	return view('Payroll.payrollmandate', $data);
   }
   public function PayrollParticularReport(Request $request)
   {
   	$data['variable']=$request->input('variable');
   	$data['year']=$request->input('year');
   	$data['month']=$request->input('month');
   	$active_period=$this->Payroll_Active_period();
   	if($data['year']==''){$data['year']=$active_period->year;}
   	if($data['month']==''){$data['month']=$active_period->month;}
    $data['Months'] = $this->Months();
    $data['NetpaySummary']=$this->NetpaySummary($data['year'],$data['month']);
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
    // dd($data['Staffs']);
   	if($data['year']==''){$data['year']=$active_period->year;}
   	if($data['month']==''){$data['month']=$active_period->month;}
    //$data['EarningVariable'] = $this->PEarningVariable($data['year'],$data['month']);
    $data['EarningVariable'] = $this->TaxableEarningVariableTaxable($data['year'],$data['month']);
    $data['NonTaxableEarning'] = $this->NonTaxableEarningVariable($data['year'],$data['month']);
    $data['DeductionVariable'] = $this->PDeductionVariable($data['year'],$data['month']);
    $data['Months'] = $this->Months();
    $data['Payroll']=$this->Payroll($data['year'],$data['month'],$data['staffid']);
    $data['MonthlyActiveVariable']=$this->MonthlyActiveVariable($data['year'],$data['month']);
    //dd($this->NetpaySummary($data['year'],$data['month']));
     dd($data['Payroll']);
	return view('Payroll.payslip2', $data);
   }
}