<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
// use DB;
// use Auth;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Input;
// use Session;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\User;
use App\Models\LoanTransaction;
use App\Models\RepaymentLog;
use App\Http\Traits\LoanTrait;
use App\Models\AccountChart;
use App\Http\Traits\AccountTrait;
use App\Models\CustomerNote;
use App\Models\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CustomerController extends Controller
{
    function __construct(){
        define("REGEX", "/[^\d.]/");
        
        //define("MYID",  Auth::user()->id );
        // define("MYID", Customer::where('user_id', Auth::user()->id)->value('id'));
    }
    
    public function repayment(Request $request)
    {
        define("MYID", Customer::where('user_id', Auth::user()->id)->value('id'));
        if(!(URL::previous()==URL::current() )){
            $request->session()->forget('details');
            $request->session()->forget('amount');
        }
        $data['loan']=$request->input('loan');
        if ($data['loan']=='') {$data['loan']=Session::get('loan');}
        Session(['loan' => $data['loan']]);

        $data['amount']=$request->input('amount');
        if ($data['amount']=='') {$data['amount']=Session::get('amount');}
        if ($data['amount']=='') {$data['amount']= Loan::find($data['loan'])['amount_outstanding']??0;}
        Session(['amount' => $data['amount']]);



        $data['details']=$request->input('details');
        if ($data['details']=='') {$data['details']=Session::get('details');}
        Session(['details' => $data['details']]);

        if (isset ($_POST['addnew'])) {
            $this->validate(
                $request,
                [
                    'loan'  => 'required',
                    'amount'    => 'required',
                    'details'      => 'required',
                    'paymentDate'  => 'required',
                ]
            );
            $amount = preg_replace(REGEX, '', $request->input('amount'))?? 0 ;
            RepaymentLog::create([
                'customer_id' => Loan::find($request->input('loan'))->customer_id,
                'loan_id' => $request->input('loan'),
                'amount' => $amount ,
                'details' => $request->input('details'),
                'payment_date' => $request->input('paymentDate') ,
                'created_by'=> Auth::user()->id,
            ]);
            $request->session()->forget('amount');
            $request->session()->forget('details');
            $request->session()->forget('paymentDate');
            return back()->with('message', 'New record successfully added.');
        }

        if (isset($_POST['update'])|| isset($_POST['submit'])) {
            $this->validate(
                $request,
                [
                    'amount'    => 'required',
                    'details'      => 'required',
                    'paymentDate'  => 'required',
                ]
            );
            $amount = preg_replace(REGEX, '', $request->input('amount'))?? 0 ;
            RepaymentLog::where('id', $request->input('id'))->update([
                'amount' => $amount ,
                'details' => $request->input('details'),
                'payment_date' => $request->input('paymentDate') ,
            ]);
            $request->session()->forget('amount');
            $request->session()->forget('details');
            $request->session()->forget('paymentDate');
            return back()->with('message', 'New record successfully added.');
        }
        if (isset($_POST['delete'] ) ) {
            RepaymentLog::destroy($request->input('id'));
            return back()->with('message', ' Record successfully trashed.');
        }
        $data['Loans'] = RepaymentLog::where('loan_id', '=', $data['loan'])->get();
        $data['Loans2']= DB::table('loan_transactions')
        ->where('loan_transactions.customer_id', MYID)
        ->groupBy('loan_transactions.loan_id')
        ->groupBy('customers.first_name')
        ->groupBy('customers.middle_name')
        ->groupBy('customers.last_name')
        ->groupBy('loans.amount_approved')
        ->groupBy('loans.amount_outstanding')
        

            ->leftJoin('customers', 'customers.id', '=', 'loan_transactions.customer_id')
            ->leftJoin('loans', 'loans.id', '=', 'loan_transactions.loan_id')
            ->select(
                'loan_transactions.loan_id',
                'loans.amount_approved',
                'loans.amount_outstanding',
                'customers.first_name',
                'customers.middle_name',
                'customers.last_name',
                DB::raw('SUM(debit-credit) as balance')
            )
            ->havingRaw('SUM(debit-credit) > ?', [0])
            ->get();
        return view('customer.repayment', $data);
                
    }

    public function loan(Request $request)
    {
        
        // LoanTrait::computeDueLoan('2023-01-05');
         
        
        define("MYID", Customer::where('user_id', Auth::user()->id)->value('id')); 
       
        $data['Loans']= Loan::where('loans.customer_id', MYID) 
            ->leftJoin('users', 'users.id', '=', 'loans.marketer_id')
            ->leftJoin('customers', 'customers.id', '=', 'loans.customer_id')
            ->leftJoin('loan_statuses', 'loan_statuses.code', '=', 'loans.status')
            ->leftJoin('loan_types', 'loan_types.id', '=', 'loans.loan_type_id')
            ->select(
                'loans.*',
                'customers.first_name',
                'customers.middle_name',
                'customers.last_name',
                'users.name',
                'loan_statuses.status as statusText',
                'loan_types.particular'
                )
            ->get();
            foreach ( $data['Loans'] as $v) {
                $v->balance=DB::table('loan_transactions')->groupBy('loan_transactions.loan_id')
                    ->where('loan_id', $v->id)->select( DB::raw('SUM(debit-credit) as balance'))->value('balance');
            }
  
        return view('customer.loans', $data);
                
    }
    public function loanDetails(Request $request)
    {
        define("MYID", Customer::where('user_id', Auth::user()->id)->value('id'));
        if (URL::previous()!==URL::current()) {
            $request->session()->forget('loan');
            $request->session()->forget('description');
        }
        
        $data['loan']= $request->input('loan');
        if ($data['loan']=='') {$data['loan']=Session::get('loan');}
        Session(['loan' => $data['loan']]);
        if (!$data['loan']) {
            return redirect('/');
        }
        

        if (isset ($_POST['upload'])) {
            $this->validate($request, [
                'loan'    => 'required',
                'description'    => 'required',
                'document' => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
            ]);
            $imageName = time().'.'.request()->document->getClientOriginalExtension();
            // dd(public_path());
             request()->document->move(public_path().'/upload/', $imageName);
             DB::table('customer_documents')->insert([
                'customer_id' => Loan::find($request->input('loan'))->value('customer_id'),
                'loan_id' => $request->input('loan'),
                'description' => $request->input('description'),
                'url' => '/upload/'.$imageName,
                'created_by' => Auth::user()->id,
                
            ]);
            return back()->with('message', 'New record successfully uploaded.');
        }

        $data['Loans']= Loan::where('loans.customer_id', MYID)
        ->leftJoin('customers', 'customers.id', '=', 'loans.customer_id')
        ->select('loans.*', 'customers.first_name', 'customers.middle_name', 'customers.last_name')
        ->get();
        $data['Loan']= Loan::where('loans.id', '=', $data['loan'])
            ->leftJoin('users as marketer', 'marketer.id', '=', 'loans.marketer_id')
            ->leftJoin('users as approver', 'approver.id', '=', 'loans.approver_id')
            ->leftJoin('loan_types', 'loan_types.id', '=', 'loans.loan_type_id')
            ->leftJoin('loan_statuses', 'loan_statuses.code', '=', 'loans.status')
            ->select('loans.*', 'marketer.name as marketer', 'approver.name as approver',
            'loan_types.particular',
            'loan_statuses.status as statusText'
            )
            ->first();
           
        if($data['Loan']) {
        $data['Loan']->balance = DB::table('loan_transactions')
            ->groupBy('loan_transactions.loan_id')
            ->where('loan_id', $data['Loan']->id)
            ->select(DB::raw('SUM(debit-credit) as balance'))
            ->value('balance');
         $data['Loan']=$data['Loan']->toArray();
         
        }
        $data['Transactions']= DB::table('loan_transactions')
            ->where('loan_transactions.loan_id', $data['loan'])
            ->select(
                'loan_transactions.*'
            )
           ->orderBy('loan_transactions.transaction_date')
            ->get();
            


        $data['documents']= DB::table('customer_documents')->where('loan_id',$data['loan'])->get();

        $data['notes'] = CustomerNote::where('loan_id', $data['loan'])
        ->leftJoin('users', 'users.id', '=', 'customer_notes.created_by')
        ->select('customer_notes.*','users.name')
        ->get();
       
        
        return view('customer.loanDetails', $data);
                
    }

    public function profile(Request $request)
    {

    $currentProfile = Customer::where('user_id', Auth::user()->id)->first();
    
    if ($currentProfile) {
        $data['id']=$currentProfile->id;
        $data['guarantorName'] = $currentProfile->guarrantor_full_name;
        $data['guarantorPhoneNumber'] = $currentProfile->guarrantor_phone;
        $data['guarantorEmail'] = $currentProfile->guarrantor_email;
        $data['guarantorHomeAddress'] = $currentProfile->guarrantor_h_address;
        $data['guarantorOfficeAddress'] = $currentProfile->guarrantor_o_address;

        $data['title'] = $currentProfile->titleID;
        $data['email'] = $currentProfile->email;
        $data['phoneNumber'] = $currentProfile->phone;
        $data['otherName'] = $currentProfile->middle_name;
        $data['firstName'] = $currentProfile->first_name;
        $data['lastName'] = $currentProfile->last_name;
        $data['marketer'] = $currentProfile->marketerID;
        $data['address'] = $currentProfile->address;
        $data['officeAddress'] = $currentProfile->office_address;
        $data['bvn'] = $currentProfile->bvn;
        $data['nin'] = $currentProfile->nin;
        $data['remark'] = $currentProfile->remarks;
        $data['status_office']=$currentProfile->is_o_address_verified;
        $data['status_home']=$currentProfile->is_h_address_verified;
       
    } else {
        return redirect('/');
    }

    $data['Title']= Title::all();
    $data['customers']= Customer::leftJoin('titles', 'titles.id', '=', 'customers.titleID')
                            ->leftJoin('users', 'users.id', '=', 'customers.marketerID')
                            ->select('customers.*', 'titles.title', 'users.name')
                            ->get();
    $data['Marketer']= User::where('userrole', 3)->get();
    return view('customer.profile', $data);
    }
    
}
