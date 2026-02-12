<?php

namespace App\Http\Controllers;
//require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Schema;
use Auth;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
class HR extends Basefunction 
{
  public function StaffRreg(Request $request)
   {
   	$data['staffno']=$request->input('staffno');
   	
   	$data['fname']=$request->input('fname');
   	$data['mname']=$request->input('mname');
   	$data['lname']=$request->input('lname');
   	$data['phoneno']=$request->input('phoneno');
   	$data['email']=$request->input('email');
   	$data['address']=$request->input('address');
   	$data['department']=$request->input('department');
   	$data['grade']=$request->input('grade');
   	$data['offer_amount']=$request->input('offer_amount');
   	$data['bank']=$request->input('bank');
   	$data['accountno']=$request->input('accountno');
  	$data['bank_account_name']=$request->input('bank_account_name');
  	$data['pension_number']=$request->input('pension_number');
  	$data['payee_number']=$request->input('payee_number');
  	$data['nhf_number']=$request->input('nhf_number');

   	$data['id']=$request->input('id');
   	if ( isset( $_POST['submit'] ) ) {
            $this->validate($request, [
              'staffno'      => 'required|string|unique:tblstaff,staff_no',
              'fname'      => 'required|string',
              'lname'      => 'required|string',
              'grade'      => 'required|string',
              'offer_amount' => 'nullable|numeric|min:0',
              'bank_account_name' => 'nullable|string',
              'pension_number' => 'nullable|string',
              'payee_number' => 'nullable|string',
              'nhf_number' => 'nullable|string',
              // 'bank'      => 'required|string',
              // 'phoneno'      => 'required|string',
            ]);
            
            // Validate offer_amount against grade's salary range
            if($data['offer_amount']) {
                $gradeInfo = DB::table('tblstaff_grade_level')
                    ->where('id', $data['grade'])
                    ->first();
                
                if($gradeInfo) {
                    $lowerSalary = $gradeInfo->lower_salary ?? 0;
                    $upperSalary = $gradeInfo->upper_salary ?? 0;
                    
                    if($upperSalary > 0 && ($data['offer_amount'] < $lowerSalary || $data['offer_amount'] > $upperSalary)) {
                        return back()->with('error_message', 'Offer amount must be between ' . number_format($lowerSalary, 2) . ' and ' . number_format($upperSalary, 2) . ' for the selected grade.')->withInput();
                    }
                }
            }
            
           $data['id']= DB::table('tblstaff')->insertGetId([
    	          'staff_no' => $data['staffno'] ,
    	          'first_name' => $data['fname'] ,
    	          'middle_name' => $data['mname'] ,
    	          'last_name' => $data['lname'] ,
    	          'address' => $data['address'] ,
    	          'phone_no' => $data['phoneno'] ,
    	          'email' => $data['email'] ,
    	          'grade' => $data['grade'] ,
    	          'bankid' => $data['bank'] ,
    	          'account_no' => $data['accountno'] ,
    	          'bank_account_name' => $data['bank_account_name'] ,
    	          'pension_number' => $data['pension_number'] ,
    	          'payee_number' => $data['payee_number'] ,
    	          'nhf_number' => $data['nhf_number'] ,
    	          'department' => $data['department']!=''? $data['department']:0 ,
    	          'offer_amount' => $data['offer_amount'] ? $data['offer_amount'] : 0,
    	          'status' => 'Active',
    	        ]);
    	        
    	        if($request->hasFIle('passport')){
                $extension = $request->file('passport')->getClientOriginalExtension();
                $fileNameToStore = $data['id'] .'.'.$extension;
                $pic = DB::table('tblstaff')->where('id',$data['id'])->value('img');
        	    if($pic!='')
                   {
                        $file_path = env('upload_path').'/passport/'.$fileNameToStore;
                        if(is_file($file_path))unlink($file_path);
                   }
                    $request->file('passport')->move(env('upload_path').'/passport/', $fileNameToStore);
                    DB::table('tblstaff')->where('id',$data['id'])->update([ 'img' => $fileNameToStore ,]);
                }
    	        return back()->with('message','New record successfully added.'  );
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
        dd($del);
        if( DB::table('tblaccountchart')->where('subheadid',$del)->first())return back()->with('error_message','Brand exist with product. Hence, record cannot be deleted!'  );
        DB::delete("DELETE FROM `tblaccountsubhead` WHERE `id`='$del'");
        return back()->with('message',' Record successfully trashed.'  );
    }
    $data['Grade'] = $this->Grade();
    $data['BankList'] = $this->BankList();
    $data['Staffs'] = $this->Staffs('','');
    $data['Department'] = $this->Department();
	return view('HR.staffregistration', $data);
	    
   }
    public function StaffList(Request $request)
   {
   	$data['department']=$request->input('department');
   	$data['grade']=$request->input('grade');
    if ( isset( $_POST['delinv'] ) ) {
        $del=$request->input('deleteid');
        
        // Check if staff exists in payroll records
        if( DB::table('tblpayroll_payment')->where('staffid',$del)->first()) {
            return back()->with('error_message','Staff has payroll records. Hence, record cannot be deleted!'  );
        }
        
        // Check if staff exists in staff control variables
        if( DB::table('tblstaff_cv')->where('staffid',$del)->first()) {
            return back()->with('error_message','Staff has control variables. Hence, record cannot be deleted!'  );
        }
        
        // Check if staff exists in staff monthly CV
        if( DB::table('tblstaff_monthly_cv')->where('staffid',$del)->first()) {
            return back()->with('error_message','Staff has monthly control variables. Hence, record cannot be deleted!'  );
        }
        
        // Delete staff record
        DB::delete("DELETE FROM `tblstaff` WHERE `id`='$del'");
        return back()->with('message',' Record successfully trashed.'  );
    }
    $data['Grade'] = $this->Grade();
    $data['Staffs'] = $this->Staffs('','');
    $data['Department'] = $this->Department();
	  return view('HR.stafflist', $data);
	    
   }
   public function StaffRecordUpdate(Request $request)
   {
       $data['staffno']='';
       	$data['fname']='';
       	$data['mname']='';
       	$data['lname']='';
       	$data['phoneno']='';
       	$data['email']='';
       	$data['address']='';
       	$data['department']='';
       	$data['grade']='';
       	$data['offer_amount']='';
       	$data['bank']='';
       	$data['accountno']='';
       	$data['bank_account_name']='';
       	$data['pension_number']='';
       	$data['payee_number']='';
       	$data['nhf_number']='';
       	$data['status']='';
       $data['staffid']=$request->input('staffid');
       if($data['staffid']==''){$data['staffid']=Session::get('staffid');}
   	Session(['staffid' => $data['staffid']]);
   	if($data['staffid']==Session::get('prevstaffid')){
       	$data['staffno']=$request->input('staffno');
       	$data['fname']=$request->input('fname');
       	$data['mname']=$request->input('mname');
       	$data['lname']=$request->input('lname');
       	$data['phoneno']=$request->input('phoneno');
       	$data['email']=$request->input('email');
       	$data['address']=$request->input('address');
       	$data['department']=$request->input('department');
       	$data['grade']=$request->input('grade');
       	$data['offer_amount']=$request->input('offer_amount');
       	$data['bank']=$request->input('bank');
       	$data['accountno']=$request->input('accountno');
       	$data['bank_account_name']=$request->input('bank_account_name');
       	$data['pension_number']=$request->input('pension_number');
       	$data['payee_number']=$request->input('payee_number');
       	$data['nhf_number']=$request->input('nhf_number');
       	$data['status']=$request->input('status');
   	}
   	Session(['prevstaffid' => $data['staffid']]);
     if ( isset( $_POST['update'] ) ) {
        $this->validate($request, [
            //'subhead'      => 'required|string|unique:tblaccountsubhead,subhead,'.$request->input('id'),
            'staffno'      => 'required|string|unique:tblstaff,staff_no,'.$request->input('staffid'),// 'required|string|unique:tblstaff,staff_no',
            'fname'      => 'required|string',
            'lname'      => 'required|string',
            'grade'      => 'required|string',
            'grade'      => 'required|string',
            'phoneno'      => 'required|string',
            'staffid'      => 'required|string',
            'offer_amount' => 'nullable|numeric|min:0',
            'bank_account_name' => 'nullable|string',
            'pension_number' => 'nullable|string',
            'payee_number' => 'nullable|string',
            'nhf_number' => 'nullable|string',
        ]);
        
        // Validate offer_amount against grade's salary range
        if($data['offer_amount']) {
            $gradeInfo = DB::table('tblstaff_grade_level')
                ->where('id', $data['grade'])
                ->first();
            
            if($gradeInfo) {
                $lowerSalary = $gradeInfo->lower_salary ?? 0;
                $upperSalary = $gradeInfo->upper_salary ?? 0;
                
                if($upperSalary > 0 && ($data['offer_amount'] < $lowerSalary || $data['offer_amount'] > $upperSalary)) {
                    return back()->with('error_message', 'Offer amount must be between ' . number_format($lowerSalary, 2) . ' and ' . number_format($upperSalary, 2) . ' for the selected grade.')->withInput();
                }
            }
        }
        
        DB::table('tblstaff')->where('id',$data['staffid'])->update([
	        'staff_no' => $data['staffno'] ,
            'first_name' => $data['fname'] ,
            'middle_name' => $data['mname'] ,
            'last_name' => $data['lname'] ,
            'address' => $data['address'] ,
            'phone_no' => $data['phoneno'] ,
            'email' => $data['email'] ,
            'grade' => $data['grade'] ,
            'bankid' => $data['bank'] ,
    	    'account_no' => $data['accountno'] ,
            'bank_account_name' => $data['bank_account_name'] ,
            'pension_number' => $data['pension_number'] ,
            'payee_number' => $data['payee_number'] ,
            'nhf_number' => $data['nhf_number'] ,
            'department' => $data['department']!=''? $data['department']:0 ,
            'status' => $data['status'] ? $data['status'] : 'Active',
            'offer_amount' => $data['offer_amount'] ? $data['offer_amount'] : 0,
	   ]);
	    if($request->hasFIle('passport')){
	       
                $extension = $request->file('passport')->getClientOriginalExtension();
                $fileNameToStore = $data['staffid'] .'.'.$extension;
                $pic = DB::table('tblstaff')->where('id',$data['staffid'])->value('img');
        	    if($pic!='')
                   {
                       
                        $file_path = env('upload_path').'/passport/'.$fileNameToStore;
                        if(is_file($file_path))unlink($file_path);
                         //die("kxkskbbb");
                   }
                   //die("kxk123");
                    $request->file('passport')->move(env('upload_path').'/passport/', $fileNameToStore);
                    DB::table('tblstaff')->where('id',$data['staffid'])->update([ 'img' => $fileNameToStore ,]);
                }
	   return back()->with('message','record successfully updated.'  );
     }
    
    $data['Grade'] = $this->Grade();
    $data['Staffs'] = $this->Staffs('','');
     $data['BankList'] = $this->BankList();
    $data['Department'] = $this->Department();
    $data['StaffProfile'] = $this->StaffProfile($data['staffid']);
    
    // Initialize offer_amount if not set
    if($data['offer_amount'] == '') {
        $data['offer_amount'] = $data['StaffProfile']->offer_amount ?? 0;
    }
    
    //dd($data['StaffProfile']);
	return view('HR.staffrecmodification', $data);
	    
   }
  
   public function NewDepartment(Request $request)
   {
    
   	$data['department']=$request->input('department');
   	$data['id']=$request->input('id');
   	if ( isset( $_POST['addnew'] ) ) {
            $this->validate($request, [
              'department'      => 'required|string|unique:tbldepartment,department',
            ]);
            DB::table('tbldepartment')->insert([
    	          'department' => $data['department'] ,
    	        ]);
    	        return back()->with('message','New record successfully added.'  );
    	         //return back()->withInput();
         }
         if ( isset( $_POST['update'] ) ) {
            $this->validate($request, [
              'department'      => 'required|string|unique:tbldepartment,department,'.$request->input('id'),
            ]);

             DB::table('tbldepartment')->where('id',$data['id'])->update([
    	          'department' => $data['department'] ,
    	        ]);
    	        return back()->with('message','record successfully updated.'  );
    	         return back()->withInput();
         }
         if ( isset( $_POST['del'] ) ) {
        $del=$request->input('id');
        if( DB::table('tblstaff')->where('department',$del)->first())return back()->with('error_message','Department exist with Staff. Hence, record cannot be deleted!'  );
        DB::delete("DELETE FROM `tbldepartment` WHERE `id`='$del'");
         return back()->with('message',' Record successfully trashed.'  );
    }
    $data['Department'] = $this->Department();
	return view('HR.department', $data);
	    
   }
   public function NewGrade(Request $request)
   {
    
   	$data['grade']=$request->input('grade');
   	$data['lower_salary']=$request->input('lower_salary');
   	$data['upper_salary']=$request->input('upper_salary');
   	$data['id']=$request->input('id');
   	if ( isset( $_POST['addnew'] ) ) {
            $this->validate($request, [
              'grade'      => 'required|string|unique:tblstaff_grade_level,grade',
              'lower_salary' => 'nullable|numeric|min:0',
              'upper_salary' => 'nullable|numeric|min:0',
            ]);
            
            // Validate that upper_salary is greater than or equal to lower_salary
            if($data['upper_salary'] && $data['lower_salary'] && $data['upper_salary'] < $data['lower_salary']) {
                return back()->with('error_message', 'Upper salary must be greater than or equal to lower salary.')->withInput();
            }
            
            DB::table('tblstaff_grade_level')->insert([
    	          'grade' => $data['grade'] ,
    	          'lower_salary' => $data['lower_salary'] ? $data['lower_salary'] : 0,
    	          'upper_salary' => $data['upper_salary'] ? $data['upper_salary'] : 0,
    	        ]);
    	        return back()->with('message','New record successfully added.'  );
    	         //return back()->withInput();
         }
         if ( isset( $_POST['update'] ) ) {
            $this->validate($request, [
              'grade'      => 'required|string|unique:tblstaff_grade_level,grade,'.$request->input('id'),
              'lower_salary' => 'nullable|numeric|min:0',
              'upper_salary' => 'nullable|numeric|min:0',
            ]);
            
            // Validate that upper_salary is greater than or equal to lower_salary
            if($data['upper_salary'] && $data['lower_salary'] && $data['upper_salary'] < $data['lower_salary']) {
                return back()->with('error_message', 'Upper salary must be greater than or equal to lower salary.')->withInput();
            }

             DB::table('tblstaff_grade_level')->where('id',$data['id'])->update([
    	          'grade' => $data['grade'] ,
    	          'lower_salary' => $data['lower_salary'] ? $data['lower_salary'] : 0,
    	          'upper_salary' => $data['upper_salary'] ? $data['upper_salary'] : 0,
    	        ]);
    	        return back()->with('message','record successfully updated.'  );
    	         return back()->withInput();
         }
         if ( isset( $_POST['del'] ) ) {
        $del=$request->input('id');
        if( DB::table('tblstaff')->where('grade',$del)->first())return back()->with('error_message','Grade level exist with Staff. Hence, record cannot be deleted!'  );
        DB::delete("DELETE FROM `tblstaff_grade_level` WHERE `id`='$del'");
         return back()->with('message',' Record successfully trashed.'  );
    }
   $data['Grade'] = $this->Grade();
	return view('HR.gradelevel', $data);
	    
   }
   
   public function LeaveApplication(Request $request)
   {
    
   	//$data['staffid']=$request->input('staffid');
   	$data['staffid']=1;
   	$data['purpose']=$request->input('purpose');
   	$data['leavetype']=$request->input('leavetype');
   	$data['days']=$request->input('days');
   	$data['startdate']=$request->input('startdate');
   	$data['enddate']=$request->input('enddate');
   	$data['grade']=$request->input('grade');
   	$data['id']=$request->input('id');
   	if ( isset( $_POST['addnew'] ) ) {
            $this->validate($request, [
              'purpose'      => 'required|string',
              'leavetype'      => 'required|string',
              'startdate'      => 'required|string',
              'enddate'      => 'required|string',
              'days'      => 'required|string',
            ]);
            DB::table('tblleave')->insert([
                'staffid' => $data['staffid'] ,
                'purpose' => $data['purpose'] ,
                'leave_type' => $data['leavetype'] ,
                'no_of_days' => $data['days'] ,
                'start_day' => $data['startdate'] ,
                'end_day' => $data['enddate'] ,
                //'grade' => $data['grade'] ,
                //'grade' => $data['grade'] ,
    	        ]);
    	        return back()->with('message','New record successfully added.'  );
    	         //return back()->withInput();
         }
         if ( isset( $_POST['update'] ) ) {
            $this->validate($request, [
              'grade'      => 'required|string|unique:tblstaff_grade_level,grade,'.$request->input('id'),
            ]);

             DB::table('tblstaff_grade_level')->where('id',$data['id'])->update([
    	          'grade' => $data['grade'] ,
    	        ]);
    	        return back()->with('message','record successfully updated.'  );
    	         return back()->withInput();
         }
         if ( isset( $_POST['del'] ) ) {
        $del=$request->input('id');
        if( DB::table('tblstaff')->where('grade',$del)->first())return back()->with('error_message','Grade level exist with Staff. Hence, record cannot be deleted!'  );
        DB::delete("DELETE FROM `tblstaff_grade_level` WHERE `id`='$del'");
         return back()->with('message',' Record successfully trashed.'  );
    }
   $data['Leaves'] = $this->Leaves($data['staffid']);
   $data['Leavetypes'] = $this->Leavetypes();
	return view('HR.leaveform', $data);
	    
   }
}