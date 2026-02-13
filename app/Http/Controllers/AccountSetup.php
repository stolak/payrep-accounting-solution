<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\AccountChart;
use DB;
use Auth;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Basefunction;
use Session;
use App\Http\Traits\AccountTrait;
class AccountSetup  extends Basefunction {

    public function SubAccount(Request $request)
    {
        //if (!$this->AuthenticateRoute("new-brand")) return view('lock.index');
        $data['subhead']=$request->input('subhead');
        $data['acchead']=$request->input('acchead');
        $data['afs']=$request->input('afs');
        $data['rank']=$request->input('rank');
        if($data['acchead']==''){$data['acchead']=Session::get('acchead');}
        Session(['acchead' => $data['acchead']]);
        $data['id']=$request->input('id');
        if ( isset( $_POST['addnew'] ) ) {
                $this->validate($request, [
                'subhead'      => 'required|string|unique:tblaccountsubhead,subhead',
                'acchead'      => 'required|string',
                ]);

                DB::table('tblaccountsubhead')->insert([
                    'groupid' => $this->getGroupid($data['acchead']) ,
                    'headid' => $data['acchead'] ,
                    'subheadcode' => $this->NewAccSubCode($data['acchead']) ,
                    'subhead' => $data['subhead'] ,
                    'afs' => $data['afs'] ,
                    'rank' => $data['rank'] ,
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
            if( DB::table('tblaccountchart')->where('subheadid',$del)->first())return back()->with('error_message','Brand exist with product. Hence, record cannot be deleted!'  );
            DB::delete("DELETE FROM `tblaccountsubhead` WHERE `id`='$del'");
            return back()->with('message',' Record successfully trashed.'  );
        }
        $data['BrandList'] = $this->BrandList();
        $data['AFS'] = $this->AFS();
        $data['AccountHead'] = $this->AccountHead();
        $data['SubAccountList'] = $this->SubAccountList($data['acchead']);
        return view('AccountSetup.subaccount', $data);

    }


    public function AccountChart(Request $request) {
        $data['subhead']=$request->input('subhead');
        $data['acchead']=$request->input('acchead');
        if($data['acchead']==Session::get('acchead')||$data['acchead']==''){if($data['subhead']==''){$data['subhead']=Session::get('subhead');}}
        $data['rank']=$request->input('rank');
        if($data['acchead']==''){$data['acchead']=Session::get('acchead');}


        Session(['subhead' => $data['subhead']]);
        Session(['acchead' => $data['acchead']]);
        $data['accountname']=$request->input('accountname');
        $data['id']=$request->input('id');
        if ( isset( $_POST['addnew'] ) ) {
            dd($request->all());
                $this->validate($request, [
                'accountname'      => 'required|string|unique:tblaccountchart,accountdescription',
                'subhead'      => 'required|string',

                ]);
                $headid=$this->FetchAccHeadID($data['subhead']);
                DB::table('tblaccountchart')->insert([
                    'groupid' => $this->getGroupid($headid) ,
                    'headid' => $headid ,
                    'subheadid' => $data['subhead'] ,//$this->NewAccSubCode($data['acchead']) ,
                    'accountno' => $this->NewAccCode( $data['subhead']) ,
                    'accountdescription' => $data['accountname'] ,
                    'rank' => $request->input('rank'),
                    'createdby' => Auth::user()->id ,
                    ]);
                    return back()->with('message','New record successfully added.'  );
                    //return back()->withInput();
            }
            if ( isset( $_POST['update'] ) ) {
                $this->validate($request, [
                'accountname'      => 'required|string|unique:tblaccountchart,accountdescription,'.$request->input('id'),
                ]);

                DB::table('tblaccountchart')->where('id',$data['id'])->update([
                    'accountdescription' => $data['accountname'] ,
                    'rank' => $request->input('rank'),
                    ]);
                    return back()->with('message','record successfully updated.'  );
            }
            if ( isset( $_POST['del'] ) ) {
            $del=$request->input('id');
            if( DB::table('tblaccount_transaction')->where('accountid',$del)->first())return back()->with('error_message','Account Already exist with transaction. Hence, record cannot be deleted!'  );
            if( DB::table('tblbatch_post_temp')->where('principal_account',$del)->first())return back()->with('error_message','Account Already exist with transaction. Hence, record cannot be deleted!'  );
            if( DB::table('tblbatch_post_temp')->where('secondary_account',$del)->first())return back()->with('error_message','Account Already exist with transaction. Hence, record cannot be deleted!'  );
            if( DB::table('tblDefault_setup')->where('accoountId',$del)->first())return back()->with('error_message','Account Already exist with a setup. Hence, record cannot be deleted!'  );
            if( DB::table('tblpettyhandling_transaction')->where('accountid',$del)->first())return back()->with('error_message','Account Already exist with a setup. Hence, record cannot be deleted!'  );

            DB::delete("DELETE FROM `tblaccountchart` WHERE `id`='$del'");
            return back()->with('message',' Record successfully trashed.'  );
        }
        $data['BrandList'] = $this->BrandList();
        $data['AccountHead'] = $this->AccountHead();
        $data['AccountList'] = $this->AccountList($data['acchead'],$data['subhead']);
        $data['SubAccountList'] = $this->SubAccountList($data['acchead']);
        return view('AccountSetup.newaccount', $data);

    }

    public function BatchPost(Request $request) {
        //if (!$this->AuthenticateRoute("new-brand")) return view('lock.index');
        $data['subhead']=$request->input('subhead');
        $data['acchead']=$request->input('acchead');
        $data['manual_ref']=$request->input('manual_ref');
        $data['id']=$request->input('id');
        $data['acctid']=$request->input('acctid');
        if($data['acctid']==''){$data['acctid']=Session::get('acctid');}
        Session(['acctid' => $data['acctid']]);
        $data['acctids']=$request->input('acctids');
        $data['transactiontype']=$request->input('transactiontype');
        $data['debitamount']=$request->input('debitamount');
        $data['creditamount']=$request->input('creditamount');
        $data['transdate']=$request->input('transdate');
        $data['remarks']=$request->input('remarks');
        $data['accountnames']=$request->input('accountnames');
        $data['accountname']=$this->AccountName($data['acctid']);
        $data['M_type']=$request->input('M_type');
        $data['id']=$request->input('id');
        if ( isset( $_POST['add'] ) ) {
            $data['daterange'] = db::table('tbldaterange')->first();
                $this->validate($request, [
                'acctid'      => 'required',
                'acctids'      => 'required|string|different:acctid',
                'transactiontype'      => 'required|string',
                'debitamount'      => 'required_without:creditamount|nullable|numeric|between:0,9999999999999999.99',
                'creditamount'      => 'required_without:debitamount|nullable|numeric|between:0,9999999999999999.99',
            'transdate'      => 'required|date|after_or_equal:'.$data['daterange']->date_from.'|before_or_equal:'.$data['daterange']->date_to, //'required|string',
                'remarks'      => 'required|string',
                ],
                []
                ,
                ['acctid'=>'Principal Account number',
                'acctids'=>'Secondary Account number',
                'transactiontype'=>'Module Type',
                'debitamount'=>'Deposit',
                'creditamount'=>'Payment',
                'transdate'=>'Transaction Date',
                ]);
                DB::table('tblbatch_post_temp')->insert([
                    'M_type' => $data['transactiontype'] ,
                    'principal_account' => $data['acctid'] ,
                    'secondary_account' => $data['acctids'] ,
                    'debit' => $data['debitamount'] !== null ? $data['debitamount'] : 0 ,
                    'credit' => $data['creditamount'] !== null ? $data['creditamount'] : 0  ,
                    'trans_date' => $data['transdate'] ,
                    'remark' => $data['remarks'] ,
                    'postby' => Auth::user()->id ,
                    ]);
            return back()->with('message','New record successfully added.'  );
            }

                if ( isset( $_POST['update'] ) ) {
                    $data['daterange'] = db::table('tbldaterange')->first();
                $this->validate($request, [
                'acctid'      => 'required',
                'acctids'      => 'required|string|different:acctid',
                'transactiontype'      => 'required|string',
                'debitamount'      => 'required_without:creditamount|nullable|numeric|between:0,9999999999999999.99',
                'creditamount'      => 'required_without:debitamount|nullable|numeric|between:0,9999999999999999.99',
                'transdate'      => 'required|date|after_or_equal:'.$data['daterange']->date_from.'|before_or_equal:'.$data['daterange']->date_to, //'required|string',

                'remarks'      => 'required|string',
                ],
                []
                ,
                ['acctid'=>'Principal Account number',
                'acctids'=>'Secondary Account number',
                'transactiontype'=>'Module Type',
                'debitamount'=>'Deposit',
                'creditamount'=>'Payment',
                'transdate'=>'Transaction Date',
                ]);

                DB::table('tblbatch_post_temp')->where('id',$data['id'])->update([
                    'M_type' => $data['transactiontype'] ,
                    'secondary_account' => $data['acctids'] ,
                    'debit' => $data['debitamount'] !== null ? $data['debitamount'] : 0 ,
                    'credit' => $data['creditamount'] !== null ? $data['creditamount'] : 0  ,
                    'trans_date' => $data['transdate'] ,
                    'remark' => $data['remarks'] ,
                    'postby' => Auth::user()->id ,
                    ]);

            return back()->with('message','New record successfully added.'  );
            }
            if ( isset( $_POST['post'] ) ) {
                $this->validate($request, [
                    'acctid'      => 'required',
                    'manual_ref'      => 'required|string|unique:tblaccount_transaction,manual_ref',
                ],
                []
                ,
                [
                    'acctid'=>'Principal Account number',
                    'manual_ref'=>'Reference Number',
                ]);
                $data['BatchPending'] =$this->BatchPending($data['acctid'],'0');
                $refno=$this->RefNo();
                $userid=Auth::user()->id;

                DB::table('tblbatch_post_temp')->where('postby',Auth::user()->id)->where('principal_account',$data['acctid'])->where('status',0)->update([
                    'status' => 1 ,
                    'ref' => $refno ,
                    'manual_ref' => $data['manual_ref'] ,
                    ]);
                    foreach ($data['BatchPending'] as $b){
                    if($b->debit!=0){
                        $this->DebitAccount($b->secondary_account, $b->debit,$refno,$b->trans_date,$b->remark,$userid,$data['manual_ref']);
                        $this->CreditAccount($b->principal_account,$b->debit,$refno,$b->trans_date,$b->remark,$userid,$data['manual_ref']);
                    }

                    if($b->credit!=0){
                        $this->DebitAccount($b->principal_account, $b->credit,$refno,$b->trans_date,$b->remark,$userid,$data['manual_ref']);
                        $this->CreditAccount($b->secondary_account, $b->credit,$refno,$b->trans_date,$b->remark,$userid,$data['manual_ref']);
                    }
                    }
                    return back()->with('message','record successfully updated.'  );
            }
            if ( isset( $_POST['del'] ) ) {
            $del=$request->input('deleteid');
            //dd($del);
            //if( DB::table('tblbatch_post_temp')->where('brandid',$del)->first())return back()->with('error_message','Brand exist with product. Hence, record cannot be deleted!'  );
            DB::delete("DELETE FROM `tblbatch_post_temp` WHERE `id`='$del'");
            return back()->with('message',' Record successfully trashed.'  );
        }

        $data['BrandList'] = $this->BrandList();
        $data['AccountHead'] = $this->AccountHead();
        $data['AccountList'] = $this->AccountList($data['acchead'],$data['subhead']);
        $data['SubAccountList'] = $this->SubAccountList($data['acchead']);
        $data['BatchModule'] = $this->BatchModule();
        $data['BatchPending'] = $this->BatchPending($data['acctid'],'0');
        //dd($data['BatchPending']);
        return view('AccountSetup.batchpost', $data);

    }


    public function EndofYearClosingOld(Request $request) {

        $data['manual_ref']=$request->input('manual_ref');
        $data['PLaccount']=DB::table('tblDefault_setup')->where('code', '=', 'PL')->value('accoountId');
        $data['EQaccount']=DB::table('tblDefault_setup')->where('code', '=','EQ')->value('accoountId');
        $data['remark']=$request->input('remark');
        $data['endofyear']=$request->input('endofyear');


        if ( isset( $_POST['post'] ) ) {
                $this->validate($request, [
                    'endofyear'      => 'required|date',
                    'remark'      => 'required',
                    'manual_ref'      => 'required|string|unique:tblaccount_transaction,manual_ref',
                ],
                []
                ,
                [
                    'endofyear'=>'End of financial year date',
                    'manual_ref'=>'Reference Number',
                ]);
                $date=$data['endofyear'];
                $return= DB::Select("SELECT * FROM `tblfinancial_end` WHERE DATE_FORMAT(`year_end_date`,'%Y-%m-%d')<='$date'");
                if($return) return back()->with('error_message','This process cannot be completed because end of the year for '. $return[0]->year_end_date. ' has been computed'   );
                $data['transdate']= date("Y-m-d", strtotime($data['endofyear']) + 86400);
                $refno=$this->RefNo();
                $plamount=$this->PL($data['endofyear'])[0]->tVal;
                $data['amount']=abs($plamount);
                $userid=Auth::user()->id;

                DB::table('tblfinancial_end')->insert([
                    'description' => $data['remark'] ,
                    'pl' => $plamount ,
                    'transaction_date' => $data['transdate'] ,
                    'year_end_date' => $data['endofyear'],
                    'ref' => $refno ,
                    'manual_ref' => $data['manual_ref']  ,
                    'postby' => Auth::user()->id ,
                    ]);
                if($plamount>0){
                $this->DebitAccount($data['PLaccount'],$data['amount'],$refno,$data['transdate'] !== null ? $data['transdate'] : date("Y-m-d"),$data['remark'],$userid,$data['manual_ref']);
                $this->CreditAccount($data['EQaccount'],$data['amount'],$refno, $data['transdate'] !== null ? $data['transdate'] : date("Y-m-d"), $data['remark'],$userid,$data['manual_ref']);
                }else{
                $this->DebitAccount($data['EQaccount'],$data['amount'],$refno,$data['transdate'] !== null ? $data['transdate'] : date("Y-m-d"),$data['remark'],$userid,$data['manual_ref']);
                $this->CreditAccount($data['PLaccount'],$data['amount'],$refno, $data['transdate'] !== null ? $data['transdate'] : date("Y-m-d"), $data['remark'],$userid,$data['manual_ref']);
                }
                DB::table('tblaccount_transaction')->where('ref',$refno)->update([
                    'is_trial' => 0 ,
                    ]);

                return back()->with('message','The financial year is successfully closed.'  );
            }
            if ( isset( $_POST['del'] ) ) {
            $del=$request->input('deleteid');
            DB::delete("DELETE FROM `tblaccount_transaction` WHERE `ref`='$del'");
            DB::delete("DELETE FROM `tblfinancial_end` WHERE `ref`='$del'");
            return back()->with('message',' Record successfully trashed.'  );
        }
        $data['PLaccounttext']=DB::table('tblaccountchart')->where('id', '=', $data['PLaccount'])->value('accountdescription');
        $data['EQaccounttext']=DB::table('tblaccountchart')->where('id', '=',$data['EQaccount'])->value('accountdescription');
        $data['EOYList'] = $this->EOYList();
        return view('AccountSetup.yearclosure', $data);
    }


    public function EndofYearClosing(Request $request) {

        $data['manual_ref']=$request->input('manual_ref');
        $data['PLaccount']=DB::table('tblDefault_setup')->where('code', '=', 'PL')->value('accoountId');
        $data['EQaccount']=DB::table('tblDefault_setup')->where('code', '=','EQ')->value('accoountId');
        $data['remark']=$request->input('remark');
        $data['endofyear']=$request->input('endofyear');


        if ( isset( $_POST['post'] ) ) {
                $this->validate($request, [
                    'endofyear'      => 'required|date',
                    'remark'      => 'required',
                    'manual_ref'      => 'required|string|unique:tblaccount_transaction,manual_ref',
                ],
                []
                ,
                [
                    'endofyear'=>'End of financial year date',
                    'manual_ref'=>'Reference Number',
                ]);
                $date=$data['endofyear'];
                //dd($data['endofyear']);
                $return= DB::Select("SELECT * FROM `tblfinancial_end` WHERE DATE_FORMAT(`year_end_date`,'%Y-%m-%d')>='$date'");

                if($return) return back()->with('error_message','This process cannot be completed because end of the year for '. $return[0]->year_end_date. ' has been computed'   );
                $data['transdate']= date("Y-m-d", strtotime($data['endofyear']) + 86400);
                $refno=$this->RefNo();
                $plamount=$this->PL($data['endofyear'])[0]->tVal;
                $data['amount']=abs($plamount);
                $userid=Auth::user()->id;

                DB::table('tblfinancial_end')->insert([
                    'description' => $data['remark'] ,
                    'pl' => $plamount ,
                    'transaction_date' => $data['transdate'] ,
                    'year_end_date' => $data['endofyear'],
                    'ref' => $refno ,
                    'manual_ref' => $data['manual_ref']  ,
                    'postby' => Auth::user()->id ,
                    ]);
                if($plamount>0){
                //$this->DebitAccount($data['PLaccount'],$data['amount'],$refno,$data['transdate'] !== null ? $data['transdate'] : date("Y-m-d"),$data['remark'],$userid,$data['manual_ref']);
                $this->CreditAccount($data['EQaccount'],$data['amount'],$refno, $data['transdate'] !== null ? $data['transdate'] : date("Y-m-d"), $data['remark'],$userid,$data['manual_ref']);
                }else{
                $this->DebitAccount($data['EQaccount'],$data['amount'],$refno,$data['transdate'] !== null ? $data['transdate'] : date("Y-m-d"),$data['remark'],$userid,$data['manual_ref']);
                // $this->CreditAccount($data['PLaccount'],$data['amount'],$refno, $data['transdate'] !== null ? $data['transdate'] : date("Y-m-d"), $data['remark'],$userid,$data['manual_ref']);
                }
                foreach ($this->PL_List($data['endofyear']) as $b){
                    if($b->tVal>0){
                        $this->DebitAccount($b->accountid,abs($b->tVal),$refno,$data['transdate'] !== null ? $data['transdate'] : date("Y-m-d"),$data['remark'],$userid,$data['manual_ref']);

                        //$this->DebitAccount($b->accountid, $b->debit,$refno,$data['transdate'],$b->remarks,$userid,$data['manual_ref']);

                    }
                    if($b->tVal<0){
                        $this->CreditAccount($b->accountid,abs($b->tVal),$refno,$data['transdate'] !== null ? $data['transdate'] : date("Y-m-d"),$data['remark'],$userid,$data['manual_ref']);

                    }
                    }
                DB::table('tblaccount_transaction')->where('ref',$refno)->update([
                    'is_trial' => 0 ,
                    ]);

                return back()->with('message','The financial year is successfully closed.'  );
            }
            if ( isset( $_POST['del'] ) ) {
            $del=$request->input('deleteid');
            DB::delete("DELETE FROM `tblaccount_transaction` WHERE `ref`='$del'");
            DB::delete("DELETE FROM `tblfinancial_end` WHERE `ref`='$del'");
            return back()->with('message',' Record successfully trashed.'  );
        }
        $data['PLaccounttext']=DB::table('tblaccountchart')->where('id', '=', $data['PLaccount'])->value('accountdescription');
        $data['EQaccounttext']=DB::table('tblaccountchart')->where('id', '=',$data['EQaccount'])->value('accountdescription');
        $data['EOYList'] = $this->EOYList();
        return view('AccountSetup.yearclosure', $data);
    }


    public function ProjectExpense(Request $request) {
        //if (!$this->AuthenticateRoute("new-brand")) return view('lock.index');
        $data['id']=$request->input('id');
        $data['particular']=$request->input('particular');
        $data['accountid']=$request->input('accountid');
        if ( isset( $_POST['add'] ) ) {
                $this->validate($request, [
                'particular'      => 'required|string|unique:petty_expenses,particular',
                'accountid'      => 'required|string',
                ]);
                DB::table('petty_expenses')->insert([
                    'particular' => $data['particular'],
                    'expensenid' => $data['accountid'] ,
                    ]);
                    return back()->with('message','New record successfully added.'  );
                    //return back()->withInput();
            }
            if ( isset( $_POST['update'] ) ) {
                $this->validate($request, [
                'particular'  => 'required|string',
                'expensenid'      => 'required|string',
                ]);
        $data['expensenid']=$request->input('expensenid');
                DB::table('petty_expenses')->where('id',$data['id'])->update([
                    'particular' => $data['particular'] ,
                    'expensenid' => $data['expensenid'] ,
                    ]);
                    return back()->with('message','record successfully updated.'  );
            }
        $data['DefaultAccountLookUp'] = DB::Select("SELECT * FROM `account_charts` WHERE `headid`='6'");
        $data['DefaultAccount'] = AccountTrait::ProjectAccount();
        // dd($data);
        return view('AccountSetup.projectaccount', $data);

    }

    public function UserLedgerAssignment(Request $request) {
        $data['userId'] = $request->input('userId');
        $data['ledgerId'] = $request->input('ledgerId');
        $data['id'] = $request->input('id');

        if (isset($_POST['addnew'])) {
            $this->validate($request, [
                'userId' => 'required|integer|exists:users,id',
                'ledgerId' => 'required|integer|exists:account_charts,id',
            ]);

            DB::table('users')->where('id', $data['userId'])->update([
                'ledgerId' => $data['ledgerId'],
            ]);

            return back()->with('message', 'Ledger successfully assigned to user.');
        }

        if (isset($_POST['update'])) {
            $this->validate($request, [
                'id' => 'required|integer|exists:users,id',
                'ledgerId' => 'required|integer|exists:account_charts,id',
            ]);

            DB::table('users')->where('id', $data['id'])->update([
                'ledgerId' => $data['ledgerId'],
            ]);

            return back()->with('message', 'User ledger assignment successfully updated.');
        }

        $data['users'] = DB::table('users')
            ->leftJoin('account_charts', 'users.ledgerId', '=', 'account_charts.id')
            ->select(
                'users.id',
                'users.name',
                'users.ledgerId',
                'account_charts.accountno as ledgerNo',
                'account_charts.accountdescription as ledgerName'
            )
            ->orderBy('users.name', 'asc')
            ->get();

        $data['ledgers'] =$this->AccountLookUpBysubHeadId(env('FIELD_LEDGER_ID'));;

        return view('AccountSetup.userledgerassignment', $data);
    }




    public function JournalPost(Request $request) {
        //if (!$this->AuthenticateRoute("new-brand")) return view('lock.index');


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
        $data['accountname']=$this->AccountName($data['acctid']);
        $data['manual_ref']=$request->input('manual_ref');
        $data['id']=$request->input('id');
        if ( isset( $_POST['add'] ) ) {
                $this->validate($request, [
                //'acctid'      => 'required',
                'acctids'      => 'required|string',
                'transactiontype'      => 'required|string',
                'debitamount'      => 'required_without:creditamount|nullable|numeric|between:0,9999999999999999.99',
                'creditamount'      => 'required_without:debitamount|nullable|numeric|between:0,9999999999999999.99',
                //'transdate'      => 'required|string',
                'remarks'      => 'required|string',
                ],
                []
                ,
                ['acctid'=>'Principal Account number',
                'acctids'=>'Secondary Account number',
                'transactiontype'=>'Module Type',
                'debitamount'=>'Deposit',
                'creditamount'=>'Payment',
                'transdate'=>'Transaction Date',
                ]);
                $userid=Auth::user()->id;
                // if($data['transactiontype']=='Credit' && DB::select("SELECT * FROM `temp_journal_transfer` WHERE `transtype`='Credit' and `status`=0 and `postby`='$userid'") &&DB::select("SELECT count(`transtype`) FROM `temp_journal_transfer` WHERE `transtype`='Debit' and `status`=0 and `postby`='$userid' group by `transtype` HAVING count(`transtype`) >1")){
                //   return back()->with('message','You can only perform one to many.'  );
                // }
                // if($data['transactiontype']=='Debit' && DB::select("SELECT * FROM `temp_journal_transfer` WHERE `transtype`='Debit' and `status`=0 and `postby`='$userid'") &&DB::select("SELECT count(`transtype`) FROM `temp_journal_transfer` WHERE `transtype`='Credit' and `status`=0 and `postby`='$userid' group by `transtype` HAVING count(`transtype`) >1")){
                //   return back()->with('message','You can only perform one to many.'  );
                // }
                DB::table('temp_journal_transfer')->insert([
                    'transtype' => $data['transactiontype'] ,
                    'accountid' => $data['acctids'] ,
                    'debit' => $data['debitamount'] !== null ? $data['debitamount'] : 0 ,
                    'credit' => $data['creditamount'] !== null ? $data['creditamount'] : 0  ,
                    //'transdate' => $data['transdate'] ,
                    'remarks' => $data['remarks'] ,
                    'postby' => Auth::user()->id ,
                    ]);
            return back()->with('message','New record successfully added.'  );
            }
            if ( isset( $_POST['post'] ) ) {
                $data['daterange'] = db::table('tbldaterange')->first();
                $this->validate($request, [
                'transdate'      => 'required|date|after_or_equal:'.$data['daterange']->date_from.'|before_or_equal:'.$data['daterange']->date_to, //'required|string',
                'manual_ref'      => 'required|string|unique:tblaccount_transaction,manual_ref',
                ],[],[
                'transdate'=>'Transaction Date',
                ]);

                //dd("Validated pls check later");
                $data['JournalPending'] =$this->JournalPending('0');
                $refno=$this->RefNo();
                $userid=Auth::user()->id;

                DB::table('temp_journal_transfer')->where('postby',Auth::user()->id)->where('status',0)->update([
                    'status' => 1 ,
                    'ref' => $refno ,
                    'manual_ref' => $data['manual_ref'] ,
                    'transdate' => $data['transdate'],
                    ]);
                    foreach ($data['JournalPending'] as $b){
                    if($b->debit!=0){
                        $this->DebitAccount($b->accountid, $b->debit,$refno,$data['transdate'],$b->remarks,$userid,$data['manual_ref']);

                    }
                    if($b->credit!=0){
                        $this->CreditAccount($b->accountid, $b->credit,$refno,$data['transdate'],$b->remarks,$userid, $data['manual_ref']);
                    }
                    }
                    return back()->with('message','record successfully updated.'  );
            }
            $del=$request->input('delid');

            if(DB::delete("DELETE FROM `temp_journal_transfer` WHERE `id`='$del'")) return back()->with('message','record successfully deleted!'  );

            if ( isset( $_POST['update'] ) ) {
                $this->validate($request, [
                'id'      => 'required',
                'acctids'      => 'required|string',
                'transactiontype'      => 'required|string',
                'debitamount'      => 'required_without:creditamount|nullable|numeric|between:0,9999999999999999.99',
                'creditamount'      => 'required_without:debitamount|nullable|numeric|between:0,9999999999999999.99',
                //'transdate'      => 'required|string',
                'remarks'      => 'required|string',
                ],
                []
                ,
                ['acctid'=>'Principal Account number',
                'acctids'=>'Secondary Account number',
                'transactiontype'=>'Module Type',
                'debitamount'=>'Deposit',
                'creditamount'=>'Payment',
                'transdate'=>'Transaction Date',
                ]);

                DB::table('temp_journal_transfer')->where('id',$data['id'])->update([
                    'transtype' => $data['transactiontype'] ,
                    'accountid' => $data['acctids'] ,
                    'debit' => $data['debitamount'] !== null ? $data['debitamount'] : 0 ,
                    'credit' => $data['creditamount'] !== null ? $data['creditamount'] : 0  ,
                    //'transdate' => $data['transdate'] ,
                    'remarks' => $data['remarks'] ,
                    'postby' => Auth::user()->id ,
                    ]);
                    return back()->with('message','record successfully updated.'  );
            }


            $data['AccountList'] = $this->AccountList('','');
            $data['AccountTransType'] = $this->AccountTransType();
            $data['JournalPending'] = $this->JournalPending(0);
            $postby= Auth::user()->id;
            $crdr= DB::sELECT("SELECT ifnull(sum(`credit`-`debit`),0)as bal FROM `temp_journal_transfer` WHERE `postby`='$postby' and `status`=0")[0]->bal;
            $data['defaultremark']= DB::table('temp_journal_transfer')->where('postby',$postby)->where('status',0)->value('remarks');
            $data['crbal'] = ($crdr<0)? abs($crdr):'';
            $data['drbal'] = ($crdr>0)? abs($crdr):'';
            return view('AccountSetup.journaltransfer', $data);
    }

    public function AccountParticularSubhead(Request $request) {
        //if (!$this->AuthenticateRoute("new-brand")) return view('lock.index');
        $data['id']=$request->input('id');
        $data['particular']=$request->input('particular');
        $data['subaccount']=$request->input('subaccount');

            if ( isset( $_POST['update'] ) ) {
                $this->validate($request, [
                'particular'      => 'required',
                'subaccount'      => 'required|string',
                ]);

                DB::table('tblaccount_setup_subhead')->where('id',$data['particular'])->update([
                    'subheadid' => $data['subaccount'] ,
                    ]);
                    return back()->with('message','record successfully updated.'  );
            }
        $data['DefaultAccountLookUp'] = DB::Select("SELECT * FROM `account_charts` WHERE `headid`='6'");
        $data['DefaultAccount'] = $this->ProjectAccount();
        $data['Particulars'] =DB::Select("SELECT tblaccount_setup_subhead.*, tblaccountsubhead.subhead , tblaccountsubhead.subheadcode FROM `tblaccount_setup_subhead` left join tblaccountsubhead  on tblaccountsubhead.id=`subheadid` ");
        $data['AccountSubhead'] =DB::Select("SELECT * FROM `tblaccountsubhead` order by `subheadcode`");
        return view('AccountSetup.particularsubhead', $data);

    }


    ////////////////////////////////////////// adams /////////////////////////////////////////////

    public function PreJournalPost(Request $request) {
        //if (!$this->AuthenticateRoute("new-brand")) return view('lock.index');

        if(!(URL::previous()==URL::current()))$request->session()->forget('ref');


        $data['ref']=$request->input('ref');

        if($data['ref']==''){
            $data['ref']=Session::get('ref');
        }

        $data['acctid']=$request->input('acctid');
        $data['id']=$request->input('id');

        if($data['acctid']==''){
            $data['acctid']=Session::get('acctid');
        }

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
        $data['id']=$request->input('id');
        // dd($data['acctid']);

        if ( isset( $_POST['add']) ) {
            $this->validate($request, [
                //'acctid'      => 'required',
                'acctids'      => 'required|string',
                'transactiontype'      => 'required|string',
                'debitamount'      => 'required_without:creditamount|nullable|numeric|between:0,9999999999999999.99',
                'creditamount'      => 'required_without:debitamount|nullable|numeric|between:0,9999999999999999.99',
                //'transdate'      => 'required|string',
                'remarks'      => 'required|string',
                ], [] ,
                ['acctid'=>'Principal Account number',
                'acctids'=>'Secondary Account number',
                'transactiontype'=>'Module Type',
                'debitamount'=>'Deposit',
                'creditamount'=>'Payment',
                'transdate'=>'Transaction Date',
                ]);

                $userid=Auth::user()->id;
                DB::table('temp_journal_transfer')->insert([
                    'transtype' => $data['transactiontype'] ,
                    'accountid' => $data['acctids'] ,
                    'debit' => $data['debitamount'] !== null ? $data['debitamount'] : 0 ,
                    'credit' => $data['creditamount'] !== null ? $data['creditamount'] : 0  ,
                    'batch_status' => 0,
                    'remarks' => $data['remarks'] ,
                    'postby' => Auth::user()->id ,
                ]);
                return back()->with('message','New record successfully added.'  );
        }

        if ( isset( $_POST['close'] ) ) {
            $data['ref']='0';
        }

        if ( isset( $_POST['post'] ) ) {

            $data['daterange'] = db::table('tbldaterange')->first();

            $this->validate($request, [
            'transdate'      => 'required|date|after_or_equal:'.$data['daterange']->date_from.'|before_or_equal:'.$data['daterange']->date_to, //'required|string',
            'manual_ref'      => 'required|string|unique:account_transactions,manual_ref',
            ],[],[
            'transdate'=>'Transaction Date',
            ]);

            $data['JournalPending'] = AccountTrait::journalPending(0);
            $refno = AccountTrait::RefNo(0);
            $userid=Auth::user()->id;

            DB::table('temp_journal_transfer')->where('postby',Auth::user()->id)->where('status',0)->update([
                'status' => 1 ,
                'ref' => $refno ,
                'manual_ref' => $data['manual_ref'],
                'transdate' => $data['transdate'],
                'post_at' => Carbon::now()->format('Y-m-d'),
                ]);
                return back()->with('message','record successfully posted.'  );
        }

        if ( isset( $_POST['reverse'] ) ) {

            DB::table('temp_journal_transfer')->where('postby',Auth::user()->id)->where('ref',$request->input('ref'))->update([
                'status' => 0 ,
                'ref' => '' ,
                'manual_ref' => "" ,
                'transdate' => "",
            ]);
            return back()->with('message','record successfully reversed.'  );
        }

        $del=$request->input('delid');
        $ref=$request->input('delref');
        if(DB::delete("DELETE FROM `temp_journal_transfer` WHERE `id`='$del'")) return back()->with('message','record successfully deleted!'  );
        if(DB::delete("DELETE FROM `temp_journal_transfer` WHERE `ref`='$ref' and `status`=1")) return back()->with('message','record successfully deleted!'  );

        if ( isset( $_POST['update'] ) ) {
            $this->validate($request, [
            'id'      => 'required',
            'acctids'      => 'required|string',
            'transactiontype'      => 'required|string',
            'debitamount'      => 'required_without:creditamount|nullable|numeric|between:0,9999999999999999.99',
            'creditamount'      => 'required_without:debitamount|nullable|numeric|between:0,9999999999999999.99',
            //'transdate'      => 'required|string',
            'remarks'      => 'required|string',
            ],
            []
            ,
            ['acctid'=>'Principal Account number',
            'acctids'=>'Secondary Account number',
            'transactiontype'=>'Module Type',
            'debitamount'=>'Deposit',
            'creditamount'=>'Payment',
            'transdate'=>'Transaction Date',
            ]);

            DB::table('temp_journal_transfer')->where('id',$data['id'])->update([
                'transtype' => $data['transactiontype'] ,
                'accountid' => $data['acctids'] ,
                'debit' => $data['debitamount'] !== null ? $data['debitamount'] : 0 ,
                'credit' => $data['creditamount'] !== null ? $data['creditamount'] : 0  ,
                //'transdate' => $data['transdate'] ,
                'remarks' => $data['remarks'] ,
                'postby' => Auth::user()->id ,
            ]);

            return back()->with('message','record successfully updated.'  );
        }

        $data['AccountList'] =AccountChart::all();
        $data['AccountTransType'] =DB::table('tbltranstype')->get();
        $data['JournalPending'] = AccountTrait::journalPending(0);
        $data['SelectedJournalPending'] = AccountTrait::SelectedJournalPending($data['ref'], 0);
        $data['UnpostedJournalPending'] = AccountTrait::UnpostedJournalPending_sef(0);
        $request->session()->forget('ref');

        $postby=Auth::user()->id;
        $crdr= DB::SELECT("SELECT ifnull(sum(`credit`-`debit`),0)as bal FROM `temp_journal_transfer` WHERE `postby`='$postby' and `status`=0")[0]->bal;
        $data['defaultremark']= DB::table('temp_journal_transfer')->where('postby',$postby)->where('status',0)->value('remarks');
        $data['crbal'] = ($crdr<0)? abs($crdr):'';
        $data['drbal'] = ($crdr>0)? abs($crdr):'';

        return view('AccountSetup.pre-journaltransfer', $data);
    }


    public function Journal_Final_post(Request $request) {

        if(!(URL::previous()==URL::current())) $request->session()->forget('ref');

        $data['ini_transdate']      = $request->input('ini_transdate');
        $data['manual_ref']         = $request->input('manual_ref');
        $data['ref']                = $request->input('ref');
        $data['transactiontype']    = $request->input('transactiontype');

        if($data['ref']==''){
            $data['ref']    = Session::get('ref');
        }

        Session(['ref' => $data['ref']]);

        if ( isset( $_POST['post'] ) ) {

            $this->validate($request, [
            'ini_transdate'      => 'required|string',
            'manual_ref'      => 'required|string',
            ],[],[
            'ini_transdate'=>'Transaction Date',
            ]);

            if(DB::table('temp_journal_transfer')->where('ref','<>',$data['ref'])->where('manual_ref',$data['manual_ref'])->first())return back()->with('error_message',$data['manual_ref'].' already exist with another record.'  );
            //die('waiting'); Carbon::now()
            $data['f_post_at']  = Carbon::now()->format('Y-m-d');
            $refno              = $data['ref'];
            $userid             = Auth::user()->id;

            if(DB::table('temp_journal_transfer')->where('ref',$data['ref'])->update([
                'batch_status' => 1 ,
                'f_post_at' => $data['f_post_at'],
                'final_post_by' => $userid,
                'manual_ref' => $data['manual_ref'],
                'transdate' => $data['ini_transdate'],
                ]))

                $data['JournalPending'] =DB::table('temp_journal_transfer')->where('ref',$data['ref'])->get();

                foreach ($data['JournalPending'] as $b){
                    if($b->debit!=0){
                        AccountTrait::DebitAccount($b->accountid, $b->debit,$refno,$b->transdate,$b->remarks,$userid,$b->manual_ref);
                    }
                    if($b->credit!=0){
                        AccountTrait::CreditAccount($b->accountid, $b->credit,$refno,$b->transdate,$b->remarks,$userid, $b->manual_ref);
                    }
                }
                return back()->with('message','Record successfully posted.'  );
        }


        if ( isset( $_POST['update'] ) ) {

            $this->validate($request, [
            'id'      => 'required',
            'acctids'      => 'required|string',
            'transactiontype'      => 'required|string',
            'debitamount'      => 'required_without:creditamount|nullable|numeric|between:0,9999999999999999.99',
            'creditamount'      => 'required_without:debitamount|nullable|numeric|between:0,9999999999999999.99',
            //'transdate'      => 'required|string',
            'remarks'      => 'required|string',
            ],
            []
            ,
            ['acctid'=>'Principal Account number',
            'acctids'=>'Secondary Account number',
            'transactiontype'=>'Module Type',
            'debitamount'=>'Deposit',
            'creditamount'=>'Payment',
            'transdate'=>'Transaction Date',
            ]);

            DB::table('temp_journal_transfer')->where('id',$request['id'])->update([
                'transtype' => $request['transactiontype'] ,
                'accountid' => $request['acctids'] ,
                'debit' => $request['debitamount'] !== null ? $request['debitamount'] : 0 ,
                'credit' => $request['creditamount'] !== null ? $request['creditamount'] : 0  ,
                //'transdate' => $data['transdate'] ,
                'remarks' => $request['remarks'] ,
                //'postby' => Auth::user()->id ,
            ]);
            return back()->with('message','record successfully updated.'  );
        }

        if ( isset( $_POST['delupdate'] ) ) {
            $del= $request['delref'];
            //dd("$del");
            if(DB::delete("DELETE FROM `temp_journal_transfer` WHERE `ref`='$del'")) return back()->with('message','record successfully deleted!');
        }

        // $data['AccountList'] = $this->AccountList('','');
        // $data['AccountTransType'] = $this->AccountTransType();
        // $data['SelectedJournalPending'] = $this->SelectedJournalPending($data['ref'],0);
        // $data['UnpostedJournalPending'] = $this->UnpostedJournalPending(0);

        $data['AccountList'] =AccountChart::all() ;
        $data['AccountTransType'] =DB::table('tbltranstype')->get();
        // $data['JournalPending'] = AccountTrait::journalPending(0);
        $data['SelectedJournalPending'] = AccountTrait::SelectedJournalPending($data['ref'], 0);
        $data['UnpostedJournalPending'] = AccountTrait::UnpostedJournalPending_sef(0);

        return view('AccountSetup.groupjournaltransfer', $data);

    }


    public function PettyCashHandling(Request $request) {

        $data['manual_ref']=$request->input('manual_ref');
        $data['particular']=$request->input('particular');
        $data['amount']=$request->input('amount');
        $data['remark']=$request->input('remark');
        $data['transdate']=$request->input('transdate');

        if ( isset( $_POST['post'] ) ) {

                $data['particular_accountid']=DB::table('petty_expenses')->where('id', '=', $data['particular'])->value('expensenid');
                $data['petty_accountid']=DB::table('default_setups')->where('id', '=', 1)->value('accoountId');

                $request['petty_accountid']= $data['petty_accountid'];
                $request['particular_accountid']= $data['particular_accountid'];

                $this->validate($request, [
                    'particular_accountid'      => 'required',
                    'petty_accountid'      => 'required',
                    'amount'      => 'required|numeric|between:0,9999999999999999.99',
                    'remark'      => 'required',
                    'manual_ref'      => 'required|string|unique:account_transactions,manual_ref',
                ],
                []
                ,
                [
                    'particular_accountid'=>'Particular Project',
                    'manual_ref'=>'Reference Number',
                ]);

                $refno= AccountTrait::RefNo();
                $userid=Auth::user()->id;
            try {
                AccountTrait::DebitAccount($data['particular_accountid'],$data['amount'],$refno,$data['transdate'] !== null ? $data['transdate'] : date("Y-m-d"),$data['remark'],$userid,$data['manual_ref']);
                AccountTrait::CreditAccount($data['petty_accountid'],$data['amount'],$refno, $data['transdate'] !== null ? $data['transdate'] : date("Y-m-d"), $data['remark'],$userid,$data['manual_ref']);

                DB::table('pettyhandling_transactions')->insert([
                    'projectid' => $data['particular'],
                    'accountid' => $data['particular_accountid'] ,
                    'amount' => $data['amount'] ,
                    'remark' => $data['remark'] ,
                    'transdate' =>  $data['transdate'] !== null ? $data['transdate'] : date("Y-m-d"),
                    'postby' => Auth::user()->id ,
                    'ref' => $refno ,
                    'manual_ref' => $data['manual_ref'] ,
                    'petty_accountid' => $data['petty_accountid'] ,
                ]);
            } catch (\Exception $e) {
                DB::table('account_transactions')->where('ref', '=', $refno)->delete();
                return back()->with('error_message', ' transaction filed. The account not properly setup.');
            }

                return back()->with('message','New record successfully added.'  );
                   
            }
            if ( isset( $_POST['update'] ) ) {
                $this->validate($request, [
                'particular'  => 'required|string',
                'expensenid'      => 'required|string',
                ]);

                DB::table('petty_expenses')->where('id',$data['id'])->update([
                    'particular' => $data['particular'] ,
                    'expensenid' => $data['accountid'] ,
                    ]);
                    return back()->with('message','record successfully updated.'  );
            }
            $data['ProjectAccount'] = AccountTrait::ProjectAccount();
            $data['PettyTransaction'] = AccountTrait::PettyTransaction($data['particular']);
            return view('AccountSetup.pettycash', $data);
    }


    public function DefaultAccountSetup(Request $request) {
        //if (!$this->AuthenticateRoute("new-brand")) return view('lock.index');
        $data['particular']=$request->input('particular');
        $data['accountid']=$request->input('accountid');

            if ( isset( $_POST['update'] ) ) {
                $this->validate($request, [
                'particular'  => 'required|string',
                'accountid'      => 'required|string',
                ]);

                DB::table('default_setups')->where('id',$data['particular'])->update([
                    'accoountId' => $data['accountid'] ,
                ]);

                return back()->with('message','record successfully updated.'  );
            }

        $data['DefaultAccountLookUp'] = AccountTrait::defaultAccountLookUp($data['particular']);
        $data['DefaultAccount'] = AccountTrait::defaultAccount();

        return view('AccountSetup.defaultaccount', $data);

    }

    public function DateRangeSetup(Request $request) {
        $data['date_from'] = $request->input('date_from');
        $data['date_to'] = $request->input('date_to');

        if (isset($_POST['update'])) {
            $this->validate($request, [
                'date_from' => 'required|date',
                'date_to' => 'required|date|after_or_equal:date_from',
            ]);

            DB::table('tbldaterange')->where('id', 1)->update([
                'date_from' => $data['date_from'],
                'date_to' => $data['date_to'],
            ]);

            return back()->with('message', 'Date range successfully updated.');
        }

        $data['daterange'] = DB::table('tbldaterange')->where('id', 1)->first();

        if (!$data['daterange']) {
            DB::table('tbldaterange')->insert([
                'id' => 1,
                'date_from' => date('Y-m-d'),
                'date_to' => date('Y-m-d'),
            ]);
            $data['daterange'] = DB::table('tbldaterange')->where('id', 1)->first();
        }

        if (empty($data['date_from'])) {
            $data['date_from'] = $data['daterange']->date_from ?? date('Y-m-d');
        }
        if (empty($data['date_to'])) {
            $data['date_to'] = $data['daterange']->date_to ?? date('Y-m-d');
        }

        return view('AccountSetup.daterange', $data);
    }


    public function DefaultProductSetup(Request $request) {
        //if (!$this->AuthenticateRoute("new-brand")) return view('lock.index');
        $data['particular']=$request->input('particular');
        $data['accountid']=$request->input('accountid');

        if ( isset( $_POST['update'] ) ) {
            $this->validate($request, [
            'particular'  => 'required|string',
            'accountid'      => 'required|string',
            ]);

            DB::table('product_types')->where('id', $data['particular'])->update([
                'account_id' => $data['accountid'] ,
            ]);

            return back()->with('message','record successfully updated.'  );
        }


        $data['DefaultAccountSetUp'] = AccountTrait::defaultProductAccountLookUp($data['particular']);
        $data['DefaultProduct'] = AccountTrait::defaultProductAccount();

        return view('AccountSetup.defaultproduct', $data);

    }

}
