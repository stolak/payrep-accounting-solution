<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Auth;
use Carbon\Carbon;
use DateTime;
use App\Models\Loan;
use Illuminate\Support\Facades\Input;
use Session;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\AccountSubhead;
use App\Models\User;
use App\Models\AccountHead;
use App\Models\AccountChart;
use App\Http\Traits\SessionTrait;
use App\Http\Traits\AccountTrait;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
public function subaccount(Request $request){

    if(!(URL::previous()==URL::current() )){
        $request->session()->forget('category');
        $request->session()->forget('economiccode');
        $request->session()->forget('description');
    }
    $data['accountHead'] = SessionTrait::validate($request, 'accountHead');
    $data['subaccount'] = SessionTrait::validate($request, 'subaccount');
    $data['payment_method'] = SessionTrait::validate($request, 'payment_method');

    if (isset ($_POST['addnew'])) {
        $this->validate($request, [
            'subaccount'          => 'required',
            'accountHead'    => 'required',
            'payment_method' => 'required|in:Cash,Non-cash',
          ]);
    $head=DB::table('account_heads')->where('id',$request->input('accountHead'))->first();
    if (!$head) {
            return back()->with('error_message','Invalid Account head!'  );
        }
        DB::table('account_subheads')->insertGetId([
            'groupid' => $head->groupid,
            'headid' => $request->input('accountHead'),
            'subheadcode' => 0,// resolve later
            'subhead' => $request->input('subaccount'),
            'status' => 1,
            'rank' => 0,
            'payment_method' => $request->input('payment_method'),
        ]);

        SessionTrait::forget($request, [
            'accountHead',
            'subaccount',
            'payment_method',
        ]);
        return back()->with('message', 'New record successfully added.');
    }

    if (isset ($_POST['update'])) {
        $this->validate($request, [
            'id' => 'required|integer|exists:account_subheads,id',
            'subaccount' => 'required',
            'accountHead' => 'required',
            'payment_method' => 'required|in:Cash,Non-cash',
        ]);

        $head = DB::table('account_heads')->where('id', $request->input('accountHead'))->first();
        if (!$head) {
            return back()->with('error_message', 'Invalid Account head!');
        }

        DB::table('account_subheads')->where('id', $request->input('id'))->update([
            'groupid' => $head->groupid,
            'headid' => $request->input('accountHead'),
            'subhead' => $request->input('subaccount'),
            'payment_method' => $request->input('payment_method'),
        ]);

        return back()->with('message', 'Record successfully updated.');
    }


    if ( isset($_POST['delete']) ) {

        if (DB::table('account_charts')->where('subheadid',$request->input('id'))->first()) {
            return back()->with('error_message','This account already exist in an account. Hence, record cannot be deleted!'  );
        }
        DB::table('account_subheads')->where('id', '=', $request->input('id'))->delete();

        if (Loan::where('customer_id',$request->input('id'))->first()) {
            return back()->with('error_message','Client Already exist with transaction. Hence, record cannot be deleted!'  );
        }
        Customer::destroy($request->input('id'));
        return back()->with('message', ' Record successfully trashed.');
    }



    $data['accountHeads']= AccountHead::all();
    $data['subaccounts']= AccountSubhead::where('headid',($data['accountHead']  && $data['accountHead']!=='All')? '=':'<>',$data['accountHead'])
    ->leftJoin('account_heads', 'account_subheads.headid', '=', 'account_heads.id')
    ->select('account_subheads.*', 'account_heads.accounthead')->get();
    // dd($data['subaccounts']);
	return view('account.subaccount', $data);

}

public function newaccount(Request $request)
{

    $data['accountHead'] = SessionTrait::validate($request, 'accountHead');
    $data['subaccount'] = SessionTrait::validate($request, 'subaccount');
    $data['account'] = SessionTrait::validate($request, 'account');
    $data['accountno'] = SessionTrait::validate($request, 'accountno');

    if (isset ($_POST['addnew'])) {
        $this->validate($request, [
            'subaccount'          => 'required',
            'accountHead'    => 'required',
            'account'    => [
                'required',
                Rule::unique('account_charts', 'accountdescription')->where(function ($query) use ($request) {
                    return $query->where('subheadid', $request->input('subaccount'));
                }),
            ],
            'accountno'    => 'nullable|unique:account_charts,accountno',
          ]);
    $subhead=DB::table('account_subheads')->where('id', $request->input('subaccount'))->first();
    if (!$subhead) {
        return back()->with('error_message','Invalid Account subhead!'  );
    }
     DB::table('account_charts')->insert([
        'groupid' => $subhead->groupid,
        'headid' => $subhead->headid,
        'subheadid' => $request->input('subaccount'),
        'accountno' => $request->input('accountno')?$request->input('accountno'):0,
        'accountdescription' => $request->input('account'),
        'status' => 1,
        'rank' => 0,

    ]);

    SessionTrait::forget($request, [
        'accountHead',
        'subaccount',
    ]);
    return back()->with('message', 'New record successfully added.');
    }

    if (isset ($_POST['update'])) {
        $accountChart = DB::table('account_charts')->where('id', $request->input('id'))->first();
        if (!$accountChart) {
            return back()->with('error_message', 'Invalid account record.');
        }

        $this->validate($request, [
        'id' => 'required|integer|exists:account_charts,id',
        'accountdescription' => [
            'required',
            Rule::unique('account_charts', 'accountdescription')
                ->where(function ($query) use ($accountChart) {
                    return $query->where('subheadid', $accountChart->subheadid);
                })
                ->ignore($request->input('id')),
        ],
        'accountno' => [
            'required',
            Rule::unique('account_charts', 'accountno')->ignore($request->input('id')),
        ],
      ]);

      DB::table('account_charts')->where('id',$request->input('id'))->update([
            'accountno' => $request->input('accountno'),
        'accountdescription' => $request->input('accountdescription'),
        'rank' =>$request->input('rank'),
        ]);


        return back()->with('message', 'record successfully updated.');
    }


    if ( isset($_POST['delete']) ) {
        if (DB::table('account_transactions')->where('accountid',$request->input('id'))->first()) {
        return back()->with('error_message','This account already exist in a transaction. Hence, record cannot be deleted!'  );
        }
        DB::table('account_charts')->where('id', '=', $request->input('id'))->delete();


        return back()->with('message', ' Record successfully trashed.');
    }

    $data['accountHeads']= AccountHead::all();
    $data['subaccounts']= AccountSubhead::where('headid', $data['accountHead'])
    ->leftJoin('account_heads', 'account_subheads.headid', '=', 'account_heads.id')
    ->select('account_subheads.*', 'account_heads.accounthead')->get();

    $data['accounts']= AccountChart::leftJoin('account_heads', 'account_charts.headid', '=', 'account_heads.id')
    ->leftJoin('account_subheads', 'account_charts.subheadid', '=', 'account_subheads.id')
    ->where('account_charts.headid',($data['accountHead']  && $data['accountHead']!=='All')? '=':'<>',$data['accountHead'])
    ->where('account_charts.subheadid',($data['subaccount'] && $data['subaccount']!=='All')? '=':'<>',$data['subaccount'])
    ->select('account_charts.*', 'account_subheads.subhead', 'account_heads.accounthead')->get();
    // dd( $data['accounts']);
	return view('account.account', $data);

}
public function NewAccSubCode($head) {
    $data=0;
    $hcode =$this->FetchAccHeadCode($head);
    $dt=DB::Select("SELECT * FROM `tblaccountsubhead` WHERE `headid`='$head' order by `subheadcode` DESC  LIMIT 1");
    if($dt){
        $lastcode=$dt[0]->subheadcode;
        $intc=strlen($lastcode);
        $intchcode=strlen($hcode);
        $data=substr($lastcode, $intchcode, ($intc-$intchcode));
    }
    $data+=1;
    $newcode=$hcode.$data;
    while(strlen($newcode)<5)
    {
    $hcode=$hcode . "0";
    $newcode=$hcode.$data;
    }
    return $newcode;
}

public function trialbalance(Request $request){

    $data['todate']=$request->input('todate');
    if ($data['todate']=="") {$data['todate']=date("Y-m-d");}
    $data['TrialBal'] = AccountTrait::trialBal('1900-01-01', $data['todate']);
    
    return view('account.trialbalance', $data);
}

public function balanceBreakdown(Request $request, $id){

    $data['todate']=$request->input('todate');
    if ($data['todate']=="") {$data['todate']=date("Y-m-d");}
    $data['TrialBal'] = AccountTrait::balanceBreakdown('1900-01-01', $data['todate'], $id);
    // $data['TrialBal'] = AccountTrait::balanceBreakdown('1900-01-01', $data['todate'], $account);
    return view('account.balance_breakdown', $data);
}
public function pl(Request $request)
   {
       	$data['todate']=$request->input('todate');
       	if ($data['todate']=="") {$data['todate']=date("Y-m-d");}

    $data['Incomedata'] = AccountTrait::income('1900-01-01', $data['todate']);
    $data['Expensedata'] = AccountTrait::expenses('1900-01-01', $data['todate']);
    // dd($data['Expensedata']);

	return view('account.pl', $data);
   }
   public function balanceSheet(Request $request)
   {
   	$data['asatdate']=$request->input('asatdate');
   	if($data['asatdate']==""){$data['asatdate']=date("Y-m-d");}
    $data['CurrentAsset'] = AccountTrait::CurrentAsset($data['asatdate']);
    $data['FixedAsset'] = AccountTrait::FixedAsset($data['asatdate']);
    $data['Liability'] = AccountTrait::Liability($data['asatdate']);
    $data['LongLiability'] = AccountTrait::LongLiability($data['asatdate']);
    $data['Equity'] = AccountTrait::Equity($data['asatdate']);
    $data['PL'] = AccountTrait::PL($data['asatdate']);
	return view('account.balancesheet', $data);
   }
}
