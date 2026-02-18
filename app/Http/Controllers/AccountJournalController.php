<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Auth;
use Carbon\Carbon;
use DateTime;
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

class AccountJournalController extends Controller
{


   public function journal(Request $request)
  {

    $data['accountNumber'] = SessionTrait::validate($request, 'accountNumber');

    $data['accountnames'] = SessionTrait::validate($request, 'accountnames');
    $data['transactiontype'] = SessionTrait::validate($request, 'transactiontype');
    $data['debitamount'] = SessionTrait::validate($request, 'debitamount');
    $data['creditamount'] = SessionTrait::validate($request, 'creditamount');
    $data['remarks'] = SessionTrait::validate($request, 'remarks');
    $data['accounts'] = [];


    $data['acctid']=$request->input('acctid');
   	$data['id']=$request->input('id');
   	if($data['acctid']==''){$data['acctid']=Session::get('acctid');}
   	Session(['acctid' => $data['acctid']]);
   	$data['acctids']=$request->input('acctids');
   	$data['transactiontype']=$request->input('transactiontype');
   	$data['debitamount']=$request->input('debitamount');
   	$data['creditamount']=$request->input('creditamount');
   	$data['transdate']=$request->input('transdate');
   	$data['remarks']=$request->input('remarks');
   	$data['accountnames']=$request->input('accountnames');
   	// $data['accountname']=$this->AccountName($data['acctid']);
   	$data['manual_ref']=$request->input('manual_ref');
    $data['projectId']=$request->input('projectId');
    $projectId = $request->input('projectId');
   	$data['id']=$request->input('id');
   	if ( isset( $_POST['add'] ) ) {
        $this->validate($request, [
        'accountNumber'      => 'required|string',
        'transactiontype'      => 'required|string',
        'debitamount'      => 'required_without:creditamount|nullable|numeric|between:0,9999999999999999.99',
        'creditamount'      => 'required_without:debitamount|nullable|numeric|between:0,9999999999999999.99',
        'projectId'      => 'nullable|integer|exists:projects,id',
        //'transdate'      => 'required|string',
        'remarks'      => 'required|string',
        ],
        []
        ,
        ['accountNumber'=>'Principal Account number',
        'accountNumber'=>'Secondary Account number',
        'transactiontype'=>'Module Type',
        'debitamount'=>'Deposit',
        'creditamount'=>'Payment',
        'transdate'=>'Transaction Date',
        ]);
        $userid=Auth::user()->id;
        DB::table('temp_journal_transfer')->insert([
        'transtype' => $data['transactiontype'] ,
        'accountid' => $data['accountNumber'] ,
        'debit' => $data['debitamount'] !== null ? $data['debitamount'] : 0 ,
        'credit' => $data['creditamount'] !== null ? $data['creditamount'] : 0  ,
        //'transdate' => $data['transdate'] ,
        'remarks' => $data['remarks'] ,
        'projectId' => !empty($data['projectId']) ? $data['projectId'] : null,
        'postby' => Auth::user()->id ,
        ]);
        return back()->with('message','New record successfully added.'  );
      }
        if ( isset( $_POST['post'] ) ) {
            $data['daterange'] = db::table('tbldaterange')->first();
            $this->validate($request, [
            'transdate'      => 'required|date|after_or_equal:'.$data['daterange']->date_from.'|before_or_equal:'.$data['daterange']->date_to, //'required|string',
            'manual_ref'      => 'required|string|unique:account_transactions,manual_ref',
            'projectId'      => 'nullable|integer|exists:projects,id',
            ],[],[
            'transdate'=>'Transaction Date',
            ]);

            //dd("Validated pls check later");
              $data['JournalPending'] =AccountTrait::journalPending(0);
            $refno=AccountTrait::RefNo();
            $userid=Auth::user()->id;

              DB::table('temp_journal_transfer')->where('postby',Auth::user()->id)->where('status',0)->update([
                'status' => 1 ,
                'ref' => $refno ,
                'manual_ref' => $data['manual_ref'] ,
                'transdate' => $data['transdate'],
                  'projectId' => !empty($data['projectId']) ? $data['projectId'] : null,
              ]);

              foreach ($data['JournalPending'] as $b){
                if ($b->debit!=0) {
                  AccountTrait::debitAccount($b->accountid, $b->debit, $refno, $data['transdate'], $b->remarks, $userid, $data['manual_ref'], $projectId);
                }
                if ($b->credit!=0) {
                  AccountTrait::creditAccount($b->accountid, $b->credit, $refno, $data['transdate'], $b->remarks,$userid, $data['manual_ref'], $projectId);
                }
              }

              return back()->with('message','record successfully updated.'  );
         }
         $del=$request->input('delid');

         if(DB::delete("DELETE FROM `temp_journal_transfer` WHERE `id`='$del'")) return back()->with('message','record successfully deleted!'  );

        if ( isset( $_POST['update'] ) ) {
            $this->validate($request, [
            'id'      => 'required',
            'accountNumber'      => 'required|string',
            'transactiontype'      => 'required|string',
            'debitamount'      => 'required_without:creditamount|nullable|numeric|between:0,9999999999999999.99',
            'creditamount'      => 'required_without:debitamount|nullable|numeric|between:0,9999999999999999.99',
            'projectId'      => 'nullable|integer|exists:projects,id',
            //'transdate'      => 'required|string',
            'remarks'      => 'required|string',
            ],
            []
            ,
            ['acctid'=>'Principal Account number',
            'accountNumber'=>'Secondary Account number',
            'transactiontype'=>'Module Type',
            'debitamount'=>'Deposit',
            'creditamount'=>'Payment',
            'transdate'=>'Transaction Date',
            ]);

             DB::table('temp_journal_transfer')->where('id',$data['id'])->update([
    	          'transtype' => $data['transactiontype'] ,
    	          'accountid' => $data['accountNumber'] ,
    	          'debit' => $data['debitamount'] !== null ? $data['debitamount'] : 0 ,
    	          'credit' => $data['creditamount'] !== null ? $data['creditamount'] : 0  ,
    	          //'transdate' => $data['transdate'] ,
    	          'remarks' => $data['remarks'] ,
    	          'projectId' => !empty($data['projectId']) ? $data['projectId'] : null,
    	          'postby' => Auth::user()->id ,
    	        ]);
    	        return back()->with('message','record successfully updated.'  );
         }


    $data['AccountList'] =AccountChart::all() ;
    $postby=Auth::user()->id;
    $data['AccountTransType'] =DB::table('tbltranstype')->get();
    $data['JournalPending'] = AccountTrait::journalPending(0);
    $data['projects'] = DB::table('projects')
        ->where('status', 'Active')
        ->select('id', 'projectCode', 'name')
        ->orderBy('name', 'asc')
        ->get();
    if (empty($data['projectId'])) {
        $data['projectId'] = DB::table('temp_journal_transfer')
            ->where('postby', $postby)
            ->where('status', 0)
            ->value('projectId');
    }
    $crdr= DB::sELECT("SELECT ifnull(sum(`credit`-`debit`),0)as bal FROM `temp_journal_transfer` WHERE `postby`='$postby' and `status`=0")[0]->bal;
    $data['defaultremark']= DB::table('temp_journal_transfer')->where('postby',$postby)->where('status',0)->value('remarks');
    $data['crbal'] = ($crdr<0)? abs($crdr):'';
    $data['drbal'] = ($crdr>0)? abs($crdr):'';

	  return view('account.journal', $data);

   }

}
