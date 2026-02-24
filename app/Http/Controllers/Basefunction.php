<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Auth;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class Basefunction extends Controller
{

	Public function Payroll_Active_period() {
	    return DB::Select("SELECT tblpayroll_active_period.*,tblmonth.month as monthtx FROM `tblpayroll_active_period` join tblmonth on tblmonth.id=tblpayroll_active_period.month")[0];
	}
    Public function BatchModule() {
	    return DB::Select("SELECT * FROM `tblbatch_module`");
	}
	Public function Months() {
	    return DB::Select("SELECT * FROM `tblmonth`");
	}
	 Public function AFS() {
	    return DB::Select("SELECT * FROM `tblafs`");
	}
	Public function DepreciationType() {
	    return DB::Select("SELECT * FROM `tbldepreciation_type`");
	}
	 Public function Manufacture() {
	    return DB::Select("SELECT * FROM `tblmanufacturer`");
	}
	Public function YesNo() {
	    return DB::Select("SELECT * FROM `tblyesno`");
	}
	Public function MFormat() {
	    return DB::Select("SELECT * FROM `tblmeasurementformat`");
	}
	Public function ItemsCategory() {
	    return DB::Select("SELECT * FROM `tblitemcategory`");
	}
	Public function TaxPerc() {
	    return DB::Select("SELECT * FROM `tbltax_percentage`");
	}

	 Public function BrandList() {
	    return DB::Select("SELECT * FROM `tblbrand` join `tblmanufacturer` on `tblmanufacturer`.id= `tblbrand`.manufacturerid");
	}
	
	
	
	



  	 function dateDiff($date2, $date1)
	  {
	    list($year2, $mth2, $day2) = explode("-", $date2);
	    list($year1, $mth1, $day1) = explode("-", $date1);
	    if ($year1 > $year2) dd('Invalid Input - dates do not match');
	    $days_month = 0;
	    //$days_month = cal_days_in_month(CAL_GREGORIAN, $mth1, $year1);
	    $day_diff = 0;

	    if($year2 == $year1){
	      $mth_diff = $mth2 - $mth1;
	    }
	    else{
	      $yr_diff = $year2 - $year1;
	      $mth_diff = (12 * $yr_diff) - $mth1 + $mth2;
	    }
	    if($day1 > 1){
	      $mth_diff--;
	      //dd($mth1.",".$year1);
	      //$day_diff = $days_month - $day1 + 1;
	    }

	    $result = array('months'=>$mth_diff, 'days'=> $day_diff, 'days_of_month'=>$days_month);
	    return($result);
	  } //end of


    Public function InventoryList($brand,$category) {
		return DB::Select("SELECT `tblinventory`.*, tblbrand.brand, tblitemcategory.category
		FROM `tblinventory` left join tblbrand on tblbrand.id=`brandid` left join tblitemcategory  on
		tblitemcategory.id = `catid` WHERE 1 ");
	}

	Public function SelectItemdetails($id) {

		$data= DB::Select("SELECT `tblinventory`.*, tblbrand.brand, tblitemcategory.category, tblmeasurementformat.format
		FROM `tblinventory`
		left join tblbrand on tblbrand.id=`brandid`
		left join tblitemcategory  on tblitemcategory.id = `catid`
		left join tblmeasurementformat  on tblmeasurementformat.id = `minsku`
		WHERE  `tblinventory`.`id`='$id' ");
		if($data) return $data[0];
		return [];
	}

	Public function SalesFormat($itemid) {
	return DB::Select("SELECT tblsellingformat.*,tblmeasurementformat.format
	FROM `tblsellingformat`  left join tblmeasurementformat on tblmeasurementformat.id=`formatid`
	WHERE `itemid`='$itemid' order by minskuqty");
	}
	Public function PurchaseFormat($itemid) {
	return DB::Select("SELECT tblpurchaseformat.*,tblmeasurementformat.format
	FROM `tblpurchaseformat`  left join tblmeasurementformat on tblmeasurementformat.id=`formatid`
	WHERE `itemid`='$itemid' order by minskuqty");
	}
	Public function GetRegisterUserId($names, $username,$email,$pass,$usertype) {
		$role=DB::table('tblpredefined_role')->where('id', '=', $usertype)->value('roleid');
		//dd($role);
		return DB::table('users')->insertGetId([
					'name' => $names ,
					'username' => $username ,
					'userrole' => $role ,
					'usertype' => $usertype ,
					'password' =>bcrypt($pass),// Hash::make($pass) ,
					'email' => $email ,
					]);
		}
		function AuthenticateRoute($route) {
			$role=Auth::user()->userrole;
			return DB::SELECT("SELECT * FROM `tblassign_role_module` WHERE `roleid`='$role' and exists(SELECT null FROM `tblsubmodule` WHERE `links`='$route' and `tblsubmodule`.`id`=`submoduleid`)");
		}
		Public function DefaultPassword() {
			return '12345';
		}
		Public function SupCode($id) {
			$tempdata ="SUP";
			$newcode=$tempdata.$id;
			while(strlen($newcode)<8)
			{
			$tempdata=$tempdata . "0";
			$newcode=$tempdata.$id;
			}
			return $newcode;
		}
		Public function NewAccSubCode($head) {
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
		Public function NewAccCode($subhead) {
			$data=0;
			$hcode =$this->FetchSubAccountCode($subhead);
			$dt=DB::Select("SELECT * FROM `tblaccountchart` WHERE `subheadid`='$subhead' order by `accountno` DESC  LIMIT 1");
			if($dt){
				$lastcode=$dt[0]->accountno;
				$intc=strlen($lastcode);
				$intchcode=strlen($hcode);
				$data=substr($lastcode, $intchcode, ($intc-$intchcode));
			}
			$data+=1;
			$newcode=$hcode.$data;
			while(strlen($newcode)<10)
			{
			$hcode=$hcode . "0";
			$newcode=$hcode.$data;
			}
			return $newcode;
		}
		Public function FetchAccHeadCode($id) {
			return DB::table('tblaccounthead')->where('id', '=', $id)->value('accoundheadcode');
		}
		Public function FetchSubAccountCode($id) {
			return DB::table('tblaccountsubhead')->where('id', '=', $id)->value('subheadcode');
		}
		Public function FetchAccHeadID($id) {
			return DB::table('tblaccountsubhead')->where('id', '=', $id)->value('headid');
		}
		Public function getGroupid($id) {
			return DB::table('tblaccounthead')->where('id', '=', $id)->value('groupid');
		}

		Public function Suppliers() {
			return DB::Select("SELECT * FROM `tblsupplier`");
		}
		Public function AccountHead() {
			return DB::Select("SELECT * FROM `tblaccounthead` order by `accoundheadcode`");
		}
		Public function SubAccountList($id) {
			$qid=1;
			if($id!='')$qid="`headid`='$id'";
			return DB::Select("SELECT tblaccountsubhead.*,accounthead,tblafs.afs as Rank_order  FROM `tblaccountsubhead` left join tblaccounthead on tblaccounthead.id=`headid`
			left join tblafs on tblafs.id=tblaccountsubhead.`afs`
			WHERE $qid order by `headid`,rank");
		}
		Public function AccountList($hid,$shid) {
			$qhid=1;
			if($hid !=='')$qhid="tblaccountchart.`headid`='$hid'";
			$qshid=1;
			if($shid!=='')$qshid="tblaccountchart.`subheadid`='$shid'";
			//die("SELECT tblaccountchart.*,accounthead,subhead  FROM `tblaccountchart` left join tblaccountsubhead on tblaccountsubhead.id=tblaccountchart.`subheadid` left join tblaccounthead on tblaccounthead.id=tblaccountchart.`headid`
			//WHERE $qhid and $qshid order by `accountno`");
			return DB::Select("SELECT tblaccountchart.*,accounthead,subhead,tblafs.afs
			FROM `tblaccountchart` left join tblaccountsubhead on tblaccountsubhead.id=tblaccountchart.`subheadid` left join tblaccounthead on tblaccounthead.id=tblaccountchart.`headid`
			left join tblafs on tblafs.id=tblaccountsubhead.`afs`
			WHERE $qhid and $qshid order by `tblaccountsubhead`.`afs`,`tblaccountsubhead`.`rank`,subheadid,tblaccountchart.rank ,`tblaccountchart`.`accountno`");
		}

		function FetchAccountCodes($id) {
			return DB::table('account_charts')->where('id', '=', $id)->first();
		}

		Public function CreditAccount($accountid, $amount,$ref,$transdate,$remark,$userid,$manual_ref, $account=null) {
			$accountdetails=$this->FetchAccountCodes($accountid);
			return DB::table('account_transactions')->insertGetId([
					'groupid' => $accountdetails->groupid ,
					'headid' => $accountdetails->headid ,
					'subheadid' => $accountdetails->subheadid ,
					'accountid' => $accountid ,
					'accountcode' => $accountdetails->accountno ,
					'debit' => 0 ,
					'credit' => $amount ,
					'remarks' => $remark ,
					'ref' => $ref ,
					'manual_ref' => $manual_ref ,
					'transdate' => $transdate ,
					'postby' => $userid ,
					'account_sub'=>$account,
					'projectid' => $account,
			]);
		}


		Public function DebitAccount($accountid, $amount,$ref,$transdate,$remark,$userid,$manual_ref, $account=null) {
			$accountdetails=$this->FetchAccountCodes($accountid);
			return DB::table('account_transactions')->insertGetId([
					'groupid' => $accountdetails->groupid ,
					'headid' => $accountdetails->headid ,
					'subheadid' => $accountdetails->subheadid ,
					'accountid' => $accountid ,
					'accountcode' => $accountdetails->accountno ,
					'debit' => $amount ,
					'credit' => 0 ,
					'remarks' => $remark ,
					'ref' => $ref ,
					'manual_ref' => $manual_ref ,
					'transdate' => $transdate ,
					'postby' => $userid ,
					'account_sub'=>$account,
					'projectid' => $account,

			]);
		}
		Public function RefNo() {
			$alphabet = "0123456789";
			$pass = array();
			$alphaLength = strlen($alphabet) - 1;
			for ($i = 0; $i < 6; $i++) {
				$n = rand(0, $alphaLength);
				$pass[] = $alphabet[$n];
			}
			$date = date_create();
			$initcode= date_format($date, 'U' ) ;
			$Reference= $initcode . implode($pass);
			return $Reference;
		}
		Public function BatchPending($id,$status) {
			$userid=Auth::user()->id;
			return DB::Select("SELECT *
			,(SELECT Concat(`accountdescription`,'(',`accountno`,')') FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=principal_account) as principal
			,(SELECT Concat(`accountdescription`,'(',`accountno`,')') FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=secondary_account) as secondary
			FROM `tblbatch_post_temp` WHERE postby='$userid' and status='$status' and principal_account='$id'");
		}


		Public function AccountName($id) {
			$dt=$this->FetchAccountCodes($id);

			if($dt) return $dt->accountdescription.'('.$dt->accountno.')';
			return '';
		}
		Public function TrialBal($from,$to) {
			if (date('m-d',strtotime($from))=="01-01")$from="1900-01-01";
			$timedate= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$from' AND '$to')";
			return DB::Select("SELECT *, Sum(`debit`-`credit`) as  Credit
			,(SELECT `accountdescription` FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=accountid) as accountName
			FROM `tblaccount_transaction` WHERE  $timedate  group by `accountid` order by `accountcode`");
			return DB::Select("SELECT *, Sum(`debit`-`credit`) as  Credit
			,(SELECT `accountdescription` FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=accountid) as accountName
			FROM `tblaccount_transaction` WHERE  $timedate  and is_trial= 1 group by `accountid` order by `accountcode`");

		}
		Public function PL($date) {
			return DB::Select("SELECT SUM(credit-debit) as tVal,`subheadid` FROM `tblaccount_transaction` WHERE (`headid`=7 or `headid`=6) and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date'");
		}
		Public function PL_List($date) {
			return DB::Select("SELECT SUM(credit-debit) as tVal,accountid FROM `tblaccount_transaction` WHERE (`headid`=7 or `headid`=6) and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date' group by `accountid`");
		}
		Public function Equity($date) {
			return DB::Select("SELECT SUM(credit-debit) as tVal,`subheadid`
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			FROM `tblaccount_transaction` WHERE `headid`=5 and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date' group by `subheadid`
			order by (SELECT `rank` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)");

		}
		Public function LongLiability($date) {
			return DB::Select("SELECT SUM(credit-debit) as tVal,`subheadid`
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			FROM `tblaccount_transaction` WHERE `headid`=4 and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date' group by `subheadid`
			order by (SELECT `rank` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)");

		}
		Public function Liability($date) {
			return DB::Select("SELECT SUM(credit-debit) as tVal,`subheadid`
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			FROM `tblaccount_transaction` WHERE `headid`=3 and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date' group by `subheadid`
			order by (SELECT `rank` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)");

		}
		Public function FixedAsset($date) {
			return DB::Select("SELECT SUM(debit-credit) as tVal,`subheadid`
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			FROM `tblaccount_transaction` WHERE `headid`=2 and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date' group by `subheadid`
			order by (SELECT `rank` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)");

		}
		Public function CurrentAsset($date) {
			return DB::Select("SELECT SUM(debit-credit) as tVal,`subheadid`
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			FROM `tblaccount_transaction` WHERE `headid`=1 and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date' group by `subheadid`
			order by (SELECT `rank` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)");

		}
		Public function Income($fromdate,$todate) {
			$timedate= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$fromdate' AND '$todate')";
			return DB::Select("SELECT SUM(debit-credit) as tVal,`subheadid`
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			FROM `tblaccount_transaction` WHERE `headid`=7 and $timedate and `is_trial`=1 group by `subheadid`");

		}
		Public function Expenses($fromdate,$todate) {
			$timedate= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$fromdate' AND '$todate')";
			return DB::Select("SELECT SUM(debit-credit) as tVal,`subheadid`
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			FROM `tblaccount_transaction` WHERE `headid`=6 and $timedate and `is_trial`=1 group by `subheadid`");

		}
		Public function ExpensesComparative($f1,$t1,$f2,$t2,$f3,$t3,$f4,$t4,$t5,$t6) {
			$timedate1= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$f1' AND '$t1')";
			$timedate2= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$f2' AND '$t2')";
			$timedate3= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$f3' AND '$t3')";
			$timedate4= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$f4' AND '$t4')";
			$timedate5= "DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$t5'";
			$timedate6= "DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$t6'";
			return DB::Select(" Select *,
			ifnull(sum( case when  $timedate1 then (debit-credit) end),0) as tval1
			,ifnull(sum( case when  $timedate2 then (debit-credit) end),0) as tval2
			,ifnull(sum( case when  $timedate3 then (debit-credit) end),0) as tval3
			,ifnull(sum( case when  $timedate4 then (debit-credit) end),0) as tval4
			,ifnull(sum( case when  $timedate5 then (debit-credit) end),0) as tval5
			,ifnull(sum( case when  $timedate6 then (debit-credit) end),0) as tval6
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			FROM `tblaccount_transaction` WHERE `headid`=6 and `is_trial`=1 group by `subheadid`");
		}
		Public function IncomeComparative($f1,$t1,$f2,$t2,$f3,$t3,$f4,$t4,$t5,$t6) {
			$timedate1= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$f1' AND '$t1')";
			$timedate2= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$f2' AND '$t2')";
			$timedate3= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$f3' AND '$t3')";
			$timedate4= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$f4' AND '$t4')";
			$timedate5= "DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$t5'";
			$timedate6= "DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$t6'";
			return DB::Select(" select*,
			ifnull(sum( case when  $timedate1 then (debit-credit) end),0) as tval1
			,ifnull(sum( case when  $timedate2 then (debit-credit) end),0) as tval2
			,ifnull(sum( case when  $timedate3 then (debit-credit) end),0) as tval3
			,ifnull(sum( case when  $timedate4 then (debit-credit) end),0) as tval4
			,ifnull(sum( case when  $timedate5 then (debit-credit) end),0) as tval5
			,ifnull(sum( case when  $timedate6 then (debit-credit) end),0) as tval6
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			FROM `tblaccount_transaction` WHERE `headid`=7  and `is_trial`=1 group by `subheadid`");
		}
		Public function PLFull($date) {
			//
			return DB::Select("SELECT SUM(credit-debit) as tVal
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			,(SELECT `accountdescription` FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=accountid) as accountName
			FROM `tblaccount_transaction` WHERE (`headid`=7 or `headid`=6) and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date' group by `accountid`");

		}
		Public function EquityFull($date) {
			return DB::Select("SELECT SUM(credit-debit) as tVal
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			,(SELECT `accountdescription` FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=accountid) as accountName
			FROM `tblaccount_transaction` WHERE `headid`=5 and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date' group by `accountid`");

		}
		Public function LongLiabilityFull($date) {
			return DB::Select("SELECT SUM(credit-debit) as tVal
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			,(SELECT `accountdescription` FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=accountid) as accountName
			FROM `tblaccount_transaction` WHERE `headid`=4 and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date' group by `accountid`");

		}
		Public function LiabilityFull($date) {
			return DB::Select("SELECT SUM(credit-debit) as tVal
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			,(SELECT `accountdescription` FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=accountid) as accountName
			FROM `tblaccount_transaction` WHERE `headid`=3 and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date' group by `accountid`");

		}
		Public function FixedAssetFull($date) {
			return DB::Select("SELECT SUM(debit-credit) as tVal
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			,(SELECT `accountdescription` FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=accountid) as accountName
			FROM `tblaccount_transaction` WHERE `headid`=2 and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date' group by `accountid`");

		}
		Public function CurrentAssetFull($date) {
			return DB::Select("SELECT SUM(debit-credit) as tVal
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			,(SELECT `accountdescription` FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=accountid) as accountName
			FROM `tblaccount_transaction` WHERE `headid`=1 and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date' group by `accountid`");

		}
		Public function BalanceSheetFullSubHead($date,$id) {
			return DB::Select("SELECT SUM(debit-credit) as tVal
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			,(SELECT `accountdescription` FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=accountid) as accountName
			FROM `tblaccount_transaction` WHERE `subheadid`='$id' and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date' group by `accountid`");

		}
		Public function PLFullSubHead($from,$to,$id) {
			return DB::Select("SELECT SUM(debit-credit) as tVal
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			,(SELECT `accountdescription` FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=accountid) as accountName
			FROM `tblaccount_transaction` WHERE `subheadid`='$id' and  (DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$from' AND '$to')  group by `accountid`");

		}
		Public function DefaultAccountLookUp($id) {
			$id2=DB::table('tblDefault_setup')->where('id', '=', $id)->value('headid');
			return DB::Select("SELECT * FROM `tblaccountchart` WHERE `headid`='$id2'");
		}
		Public function AccountLookUpByHeadId($id) {
			return DB::Select("SELECT * FROM `account_charts` WHERE `headid`='$id'");
		}

		Public function AccountLookUpBysubHeadId($id) {
			return DB::Select("SELECT * FROM `account_charts` WHERE `subheadid`='$id'");
		}
		Public function ProjectAccount() {
			return DB::Select("SELECT * ,(SELECT Concat(`accountdescription`,'(',`accountno`,')') FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=expensenid) as AccountName FROM `project_expense`");
		}
			Public function PettyTransaction($petty='',$br='') {
			$qpetty=1;
			if($petty!='')$qpetty="`tblpettyhandling_transaction`.`projectid`='$petty'";
			$qbr=1;
			if($br!='')$qbr="`tblpettyhandling_transaction`.`branch_id`='$br'";
			return DB::Select("SELECT tblpettyhandling_transaction.*
			,(SELECT Concat(`accountdescription`,'(',`accountno`,')') FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=accountid) as AccountName
			,(SELECT particular FROM `petty_expenses` WHERE `petty_expenses`.`id`=projectid) as Particular
			,(SELECT name FROM `users` WHERE `users`.`id`=postby) as Postedby
			,tblbranch.branch as Branch
			,users.name as FPost
			FROM `tblpettyhandling_transaction`
			left join tblbranch  on `tblpettyhandling_transaction`.`branch_id`=tblbranch.id
			left join users  on `users`.`id`=tblpettyhandling_transaction.final_post_by where $qbr and $qpetty");
		}
		Public function AccountTransType() {
			return DB::Select("SELECT * FROM `tbltranstype`");
		}
		Public function JournalPending($status) {
			$userid=Auth::user()->id;
			return DB::Select("SELECT *
			,(SELECT Concat(`accountdescription`,'(',`accountno`,')') FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=`temp_journal_transfer`.accountid) as account_details
			FROM `temp_journal_transfer` WHERE `postby`='$userid' and `status`='0'");
		}
		Public function AccountStatementRunningTotal($account,$fromdate,$todate) {
		$opening="0";
		$result = DB::Select("SELECT Sum(`credit`-`debit`)as Opening FROM `tblaccount_transaction` WHERE DATE_FORMAT(`transdate`,'%Y-%m-%d')<'$fromdate' and `accountid`='$account'");
		if($result){$opening=$result[0]->Opening;}

		$timedate= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$fromdate' AND '$todate')";
		$result1 = " SELECT *, (@csum) as prev, `debit`,`credit`, (@csum := @csum +`credit`-`debit`) as `current`  FROM `tblaccount_transaction` JOIN (SELECT @csum := '$opening') r WHERE  $timedate and `accountid`='$account' order by DATE_FORMAT(`transdate`,'%Y-%m-%d') ,`id`";
		return DB::Select($result1);

		}
		Public function AssetCategoryList() {
			return DB::Select("SELECT *
			,(SELECT concat(`accountdescription`, '(',`accountno`,')' )FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=tblasset_category.asset_account) as AAcct1
			,(SELECT concat(`accountdescription`, '(',`accountno`,')') FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=tblasset_category.depreciation_account) as AAcct2
			,(SELECT concat(`accountdescription`, '(',`accountno`,')') FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=tblasset_category.sales_account) as AAcct3
			,(SELECT `d_type` FROM `tbldepreciation_type` WHERE `tbldepreciation_type`.`id`=depreciation_type) as type

			FROM `tblasset_category`");
		}
			Public function AssetCategoryPara($id) {
			$dt= DB::table('tblasset_category')->where('id',$id)->first();
			if($dt) return $dt;
				return DB::Select("SELECT  '' as `cat_code`, '' as `asset_account`, '' as `depreciation_account`, '' as `sales_account`, '' as `category`, '' as `depreciation_type`  ")[0];
		}
		Public function AssetTypeList($category) {
			return DB::Select("SELECT *
			,  ( select tblasset_category.category from tblasset_category where  tblasset_category.id = `tblasset_type`.`asset_category`) as Catgory FROM `tblasset_type` WHERE asset_category='$category'");
		}
	Public function AssetList($category,$type) {
		$qtype=$type?"`typeID`='$type'":1;
		$qcategory=$category?"`categoryId`='$category'":1;
			return DB::Select("SELECT *
		,  ( select tblasset_category.category from tblasset_category where  tblasset_category.id = `tblasset`.`categoryId`) as Catgory
		,  ( select tblasset_type.assettype from tblasset_type where  tblasset_type.id = `tblasset`.`typeID`) as Type
		FROM `tblasset` WHERE $qtype and $qcategory");
		}

		Public function AssetEntityList($category,$type,$status) {
			$qtype=$type?"`typeId`='$type'":1;
			$qcategory=$category?"`categoryId`='$category'":1;
			return DB::Select("SELECT *
			,(SELECT `accountdescription` FROM `tblaccountchart` WHERE `tblaccountchart`.id=`tblasset_entity`.supplier) as sup
			,(SELECT `category` FROM `tblasset_category` WHERE `tblasset_category`.id=`tblasset_entity`.categoryId) as cat
			,(SELECT `assettype` FROM `tblasset_type` WHERE  tblasset_type.id=`tblasset_entity`.typeId) as typ
			,(SELECT `asset_description` FROM `tblasset` WHERE tblasset.id=`tblasset_entity`.assetId) asset
			,(SELECT `d_type` FROM `tbldepreciation_type` WHERE tbldepreciation_type.id=`tblasset_entity`.depr_type) Dpt
			FROM `tblasset_entity` WHERE  status='$status' and $qcategory and $qtype");
		}
			Public function AssetBatchPending($id,$status) {
			$userid=Auth::user()->id;
			return DB::Select("SELECT *
			,(SELECT Concat(`accountdescription`,'(',`accountno`,')') FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=supplier) as principal
			,(SELECT asset_account FROM `tblasset_category` WHERE `tblasset_category`.`id`=categoryId) as asset_account
			,(SELECT `asset_description` FROM `tblasset` WHERE tblasset.id=`tblasset_entity`.assetId) asset
			FROM `tblasset_entity` WHERE createdby='$userid' and status='$status'");
		}
		Public function GeneralNote($date) {
			return DB::Select("SELECT SUM(debit-credit) as tVal
			,(SELECT `accounthead` FROM `tblaccounthead` WHERE tblaccounthead.id=headid)as Head
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			,(SELECT `accountdescription` FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=accountid) as accountName
			,accountcode
			FROM `tblaccount_transaction` WHERE  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date' group by `accountid` order by `headid`,`subheadid`");

		}

		Public function NewAccountApi($particularid,$desription,$code='') {
				$subhead=DB::table('tblaccount_setup_subhead')->where('id', $particularid)->value('subheadid');
				$headid=$this->FetchAccHeadID($subhead);
				DB::table('tblaccountchart')->insert([
					'groupid' => $this->getGroupid($headid) ,
					'headid' => $headid ,
					'subheadid' => $subhead ,
					'accountno' => $this->NewAccCode($subhead) ,
					'accountdescription' => $desription ,
					'createdby' => Auth::user()->id ,
					]);
					return null;

		}
	Public function Trans_Summary($from,$to,$ref=null) {
			$timedate= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$from' AND '$to')";
			//$timedate=1;
			if($ref) return DB::Select("SELECT *, sum(`debit`) as sum_total FROM `tblaccount_transaction` WHERE  `ref`='$ref'  and is_trial= 1   group by `ref` order by  `transdate`");
			return DB::Select("SELECT *, sum(`debit`) as sum_total FROM `tblaccount_transaction` WHERE  $timedate  and is_trial= 1   group by `ref` order by  `transdate`");

		}

		Public function Gen_Transaction($from,$to) {
			$timedate= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$from' AND '$to')";
			//$timedate=1;
			return DB::Select("SELECT *
			,(SELECT `accountdescription` FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=accountid) as accountName
			FROM `tblaccount_transaction` WHERE  $timedate   order by `transdate`, `ref`");

		}
		protected function RegisteredUser(){
			$role = DB::Select("SELECT *
			,(SELECT `rolename` FROM `user_role` WHERE `user_role`.`roleID`=`users`.`userrole`) as Role
			FROM `users` WHERE `usertype` is null");
			return $role;
		}
	Public function RefBatch() {
			return DB::Select("SELECT `ref`,`manual_ref` FROM `tblaccount_transaction` group by`ref`  order by `manual_ref`");
		}
		Public function RegOrganisation() {
			return DB::Select("SELECT *
			,(SELECT `accountdescription` FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=accountid) as accountName
			FROM `tblorganisation` ");

		}
		Public function EOYList() {
			return DB::Select("SELECT * FROM `tblfinancial_end`   order by `year_end_date`");
		}

		Public function UnpostedJournalPendingOLD($status) { // by mr steve
			$userid=Auth::user()->id;
			return DB::Select("SELECT temp_journal_transfer.*,sum(temp_journal_transfer.credit) as t_val,users.name
			FROM `temp_journal_transfer` left join users  on `users`.`id`=temp_journal_transfer.postby WHERE  `batch_status`='0' and temp_journal_transfer.status=1 group by ref
			order by temp_journal_transfer.transdate,temp_journal_transfer.id");
		}

		public function UnpostedJournalPending($status) {
			$userid = Auth::user()->id;

			return DB::select("
				SELECT
					temp_journal_transfer.ref,
					SUM(temp_journal_transfer.credit) AS t_val,
					users.name
				FROM
					`temp_journal_transfer`
				LEFT JOIN
					users ON `users`.`id` = temp_journal_transfer.postby
				WHERE
					`batch_status` = '0' AND
					temp_journal_transfer.status = :status
				GROUP BY
					temp_journal_transfer.ref, users.name
				ORDER BY
					temp_journal_transfer.transdate, temp_journal_transfer.id
			", ['status' => $status]);
		}
		
		Public function UnpostedJournalPending_sefOLD($status) {
			$userid=Auth::user()->id;
			return DB::Select("SELECT temp_journal_transfer.*,sum(temp_journal_transfer.credit) as t_val,users.name
			FROM `temp_journal_transfer` left join users  on `users`.`id`=temp_journal_transfer.postby WHERE  `batch_status`='0' and  `postby`='$userid'
			group by temp_journal_transfer.ref
			order by temp_journal_transfer.transdate,temp_journal_transfer.id");

		}

		public function UnpostedJournalPending_sef($status) {
			$userid = Auth::user()->id;
			return DB::select("
				SELECT
					temp_journal_transfer.*,
					sum(temp_journal_transfer.credit) as t_val,
					users.name
				FROM
					`temp_journal_transfer`
				LEFT JOIN
					users ON `users`.`id` = temp_journal_transfer.postby
				WHERE
					`batch_status` = '0' AND `postby` = '$userid'
				GROUP BY
					temp_journal_transfer.id,
					temp_journal_transfer.ref,
					temp_journal_transfer.transtype,
					temp_journal_transfer.accountid,
					temp_journal_transfer.debit,
					temp_journal_transfer.credit,
					temp_journal_transfer.status,
					temp_journal_transfer.batch_status,
					temp_journal_transfer.manual_ref,
					temp_journal_transfer.post_at,
					temp_journal_transfer.postby,
					temp_journal_transfer.remarks,
					temp_journal_transfer.created_at,
					temp_journal_transfer.f_post_at,
					temp_journal_transfer.final_post_by,
					users.name,
					temp_journal_transfer.transdate
				ORDER BY
					temp_journal_transfer.transdate,
					temp_journal_transfer.id
			");
		}


		Public function SelectedJournalPending($ref,$status) {
			return DB::Select("SELECT *
			,(SELECT Concat(`accountdescription`,'(',`accountno`,')') FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=`temp_journal_transfer`.accountid) as account_details
			FROM `temp_journal_transfer` WHERE `ref`='$ref' and `batch_status`='$status'");
		}
		Public function QuarterlyPeriod() {
			return DB::Select("SELECT * FROM `tblevaluation_period`");
		}
		Public function PLWithin($from ,$to) {
			$timedate= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$from' AND '$to')";
			return DB::Select("SELECT SUM(credit-debit) as tVal,`subheadid` FROM `tblaccount_transaction` WHERE (`headid`=7 or `headid`=6) and $timedate");
		}

		Public function LiabilityCompare($curdate,$predate) {
			return DB::Select("SELECT ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$curdate'then (credit-debit) end),0) as tval1
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$predate'then (credit-debit) end),0) as tval2,
			`subheadid`
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			FROM `tblaccount_transaction` WHERE `headid`=3  group by `subheadid`
			order by (SELECT `rank` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)");

		}
		Public function EquityCompare($curdate,$predate) {
			return DB::Select("SELECT ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$curdate'then (credit-debit) end),0) as tval1
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$predate'then (credit-debit) end),0) as tval2,
			`subheadid`
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			FROM `tblaccount_transaction` WHERE `headid`=5  group by `subheadid`
			order by (SELECT `rank` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)");

		}
		Public function LongLiabilityCompare($curdate,$predate) {
			return DB::Select("SELECT ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$curdate'then (credit-debit) end),0) as tval1
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$predate'then (credit-debit) end),0) as tval2,
			`subheadid`
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			FROM `tblaccount_transaction` WHERE `headid`=4  group by `subheadid`
			order by (SELECT `rank` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)");

		}
		Public function FixedAssetCompare($curdate,$predate) {
			return DB::Select("SELECT ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$curdate'then (debit-credit) end),0) as tval1
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$predate'then (debit-credit) end),0) as tval2,
			`subheadid`
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			FROM `tblaccount_transaction` WHERE `headid`=2  group by `subheadid`
			order by (SELECT `rank` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)");

		}
		Public function CurrentAssetCompare($curdate,$predate) {
			return DB::Select("SELECT ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$curdate'then (debit-credit) end),0) as tval1
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$predate'then (debit-credit) end),0) as tval2,
			`subheadid`
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			FROM `tblaccount_transaction` WHERE `headid`=1  group by `subheadid`
			order by (SELECT `rank` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)");

		}
		Public function PLCompare($curdate,$predate) {
			return DB::Select("SELECT  ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$curdate'then (credit-debit) end),0) as tval1
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$predate'then (credit-debit) end),0) as tval2
			,`subheadid` FROM `tblaccount_transaction` WHERE (`headid`=7 or `headid`=6)");
		}
		Public function LiabilityComparative($date1,$date2,$date3,$date4) {
			return DB::Select("SELECT ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date1'then (credit-debit) end),0) as tval1
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date2'then (credit-debit) end),0) as tval2
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date3'then (credit-debit) end),0) as tval3
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date4'then (credit-debit) end),0) as tval4,
			`subheadid`
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			FROM `tblaccount_transaction` WHERE `headid`=3  group by `subheadid`
			order by (SELECT `rank` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)");

		}
		Public function EquityComparative($date1,$date2,$date3,$date4) {
			return DB::Select("SELECT ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date1'then (credit-debit) end),0) as tval1
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date2'then (credit-debit) end),0) as tval2
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date3'then (credit-debit) end),0) as tval3
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date4'then (credit-debit) end),0) as tval4,
			`subheadid`
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			FROM `tblaccount_transaction` WHERE `headid`=5  group by `subheadid`
			order by (SELECT `rank` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)");

		}
		Public function LongLiabilityComparative($date1,$date2,$date3,$date4) {
			return DB::Select("SELECT ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date1'then (credit-debit) end),0) as tval1
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date2'then (credit-debit) end),0) as tval2
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date3'then (credit-debit) end),0) as tval3
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date4'then (credit-debit) end),0) as tval4

			,
			`subheadid`
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			FROM `tblaccount_transaction` WHERE `headid`=4  group by `subheadid`
			order by (SELECT `rank` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)");

		}
		Public function FixedAssetComparative($date1,$date2,$date3,$date4) {
			return DB::Select("SELECT ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date1'then (debit-credit) end),0) as tval1
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date2'then (debit-credit) end),0) as tval2
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date3'then (debit-credit) end),0) as tval3
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date4'then (debit-credit) end),0) as tval4
			,
			`subheadid`
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			FROM `tblaccount_transaction` WHERE `headid`=2  group by `subheadid`
			order by (SELECT `rank` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)");

		}
		Public function CurrentAssetComparative($date1,$date2,$date3,$date4) {
			return DB::Select("SELECT ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date1'then (debit-credit) end),0) as tval1
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date2'then (debit-credit) end),0) as tval2
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date3'then (debit-credit) end),0) as tval3
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date4'then (debit-credit) end),0) as tval4
			,`subheadid`
			,(SELECT `subhead` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)as Subhead
			FROM `tblaccount_transaction` WHERE `headid`=1  group by `subheadid`
			order by (SELECT `rank` FROM `tblaccountsubhead` WHERE tblaccountsubhead.id=subheadid)");

		}
		Public function PLComparative($date1,$date2,$date3,$date4) {
			return DB::Select("SELECT  ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date1'then (credit-debit) end),0) as tval1
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date2'then (credit-debit) end),0) as tval2
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date3'then (credit-debit) end),0) as tval3
			,ifnull(sum( case when  DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date4'then (credit-debit) end),0) as tval4
			,`subheadid` FROM `tblaccount_transaction` WHERE (`headid`=7 or `headid`=6)");
		}
		Public function OwnersTransaction($year,$month) {
			return DB::table('tblowner_transactions')->where('year',$year)->where('month',$month)->first();
		}

		Public function Grade() {
			return DB::Select("SELECT * FROM `tblstaff_grade_level`");
		}
		Public function Leaves($id) {
			$qstaffid=1;
			if($id!="")  $qstaffid="`staffid`='$id'";
			return DB::Select("SELECT tblleave.*
			,CONCAT( tblstaff.first_name, ' ', tblstaff.middle_name, ' ', tblstaff.last_name) AS staffname
			, tblleave_type.leavetype
			FROM `tblleave`
			left join tblstaff on tblstaff.id=tblleave.staffid
			left join tblleave_type on tblleave_type.id=tblleave.leave_type
		where $qstaffid");
		}
		Public function Department() {
			return DB::Select("SELECT * FROM `tbldepartment`");
		}
		Public function Leavetypes() {
			return DB::Select("SELECT * FROM `tblleave_type`");
		}
			Public function Staffs($department,$grade) {
			return DB::Select("SELECT tblstaff.*,tbldepartment.department as departments,
			tblstaff_grade_level.grade as grades
			FROM `tblstaff` left JOIN tbldepartment on tbldepartment.id=tblstaff.department 
			left JOIN tblstaff_grade_level on tblstaff_grade_level.id =tblstaff.grade 
			where tblstaff.id<>0");
		}

		Public function StaffsForSalaryComputation($department, $grade) {
			return DB::Select("SELECT tblstaff.*,tbldepartment.department as departments,
			tblstaff_grade_level.grade as grades
			FROM `tblstaff` left JOIN tbldepartment on tbldepartment.id=tblstaff.department 
			left JOIN tblstaff_grade_level on tblstaff_grade_level.id =tblstaff.grade 
			where tblstaff.id<>0 and tblstaff.status='Active'");
		}
		Public function VariableType() {
			return DB::Select("SELECT * FROM `tblvariable_type`");
		}
		Public function Status() {
			return DB::Select("SELECT * FROM `tblvariable_type`");
		}
		Public function PayrollVariable($id) {
			$qt=1;
			if($id!='') $qt="`variable_type`='$id'";
			return DB::Select("SELECT *,
			(SELECT `particular` FROM `tblvariable_type` WHERE `tblvariable_type`.`id`= tblpayroll_variable.variable_type) as variabletype
			,(SELECT `status` FROM `tblstatus` WHERE `tblstatus`.`id`= tblpayroll_variable.status) as variablestatus
			,(SELECT `yn` FROM `tblyesno` WHERE `tblyesno`.`id`= tblpayroll_variable.istaxable) as istaxables
			
			FROM `tblpayroll_variable` where $qt and `status`=1 order by variable_type, `rank` ");
		}
		Public function AllPayrollVariable($id) {
			$qt=1;
			if($id!='') $qt="`variable_type`='$id'";
			return DB::Select("SELECT *,
			(SELECT `particular` FROM `tblvariable_type` WHERE `tblvariable_type`.`id`= tblpayroll_variable.variable_type) as variabletype
			,(SELECT `status` FROM `tblstatus` WHERE `tblstatus`.`id`= tblpayroll_variable.status) as variablestatus
			,(SELECT `yn` FROM `tblyesno` WHERE `tblyesno`.`id`= tblpayroll_variable.istaxable) as istaxables
			,(SELECT `yn` FROM `tblyesno` WHERE `tblyesno`.`id`= tblpayroll_variable.statutory) as statutorys
			,(SELECT `yn` FROM `tblyesno` WHERE `tblyesno`.`id`= tblpayroll_variable.isPensionable) as isPensionables
			,(SELECT `yn` FROM `tblyesno` WHERE `tblyesno`.`id`= tblpayroll_variable.isFunction) as isFunctions
			FROM `tblpayroll_variable` where $qt  order by variable_type, `rank` ");
		}
		Public function EarningVariable() {
			return DB::Select("SELECT * FROM `tblpayroll_variable` where `variable_type`='1' and status=1 ORDER BY `rank` ");
		}
		Public function DeductionVariable() {
			return DB::Select("SELECT * FROM `tblpayroll_variable` where `variable_type`='2' and status=1 ORDER BY `rank` ");
		}
		Public function StatutoryVariableWithBeforeTaxFirst() {
			return DB::Select("SELECT * FROM `tblpayroll_variable` where `variable_type`='2' and status=1 ORDER BY `isbefore_tax` DESC, `rank` ");
		}
		Public function VariableWithBeforeTax() {
			return DB::Select("SELECT * FROM `tblpayroll_variable` where `isbefore_tax`=1 and status=1 ORDER BY `rank` ");
		}

		
		Public function SalaryCharts() {
			return DB::Select("SELECT *
			,(SELECT `grade` FROM `tblstaff_grade_level` WHERE `tblstaff_grade_level`.`id`= tblpayroll_salary_new_chart.grade) as grades
			FROM `tblpayroll_salary_new_chart` where 1 order by `grade` ");
		return DB::table ('tblpayroll_salary_new_chart')->orderBy('grade')->get();
		}
		Public function VariableValue($year, $month, $variable, $staffid, $grade, $step=1) {
			$amount=0;
			
			$checkCV= DB::Select("SELECT * FROM `tblstaff_cv` WHERE `staffid`='$staffid' and `ref_code`='$variable' ");
			if($checkCV)
			{
				$amount =$this->CVRembalance($staffid,$checkCV[0]->amount_monthly,$checkCV[0]->amount_target,$checkCV[0]->is_continous);
				if($amount>0){
				DB::table('tblstaff_monthly_cv')->insert(array(
				'staffid'    	=> $staffid,
				'staffcvid'    	=> $checkCV[0]->id,
				'month'	    	=> $month,
				'year'    	    => $year,	
				'cv'            => $checkCV[0]->cvid,
				'ref_code'      => $checkCV[0]->ref_code,
				
			));
			}
			
			} else{
			//check if the variable is a function control variable
			//    $isFunction=DB::Select("SELECT `isFunction` FROM `tblpayroll_variable` WHERE `id`='$variable' and `isFunction`=1");
			//    if($isFunction) $amount=$this->FunctionControlVariableValue($year, $month, $variable, $staffid, $grade, $step);
			//    else{
			//     $dat=DB::Select("SELECT `$variable` as amount FROM `tblpayroll_salary_new_chart` WHERE `grade`='$grade' and `step`='$step' ");
			//     if($dat) $amount=$dat[0]->amount;
			//    }
				$dat=DB::Select("SELECT `$variable` as amount FROM `tblpayroll_salary_new_chart` WHERE `grade`='$grade' and `step`='$step' ");
				if($dat) $amount=$dat[0]->amount;
			}
			return $amount;
			
		}
		Public function VariableValue2($year, $month, $variable, $staff, $step=1) {
			$amount=0;
			
			$checkCV= DB::Select("SELECT * FROM `tblstaff_cv` WHERE `staffid`='$staff->id' and `ref_code`='$variable->ref_code' ");
			if($checkCV)
			{
				$amount =$this->CVRembalance($staff->id,$checkCV[0]->amount_monthly,$checkCV[0]->amount_target,$checkCV[0]->is_continous);
				if($amount>0){
				DB::table('tblstaff_monthly_cv')->insert(array(
				'staffid'    	=> $staff->id,
				'staffcvid'    	=> $checkCV[0]->id,
				'month'	    	=> $month,
				'year'    	    => $year,	
				'cv'            => $checkCV[0]->cvid,
				'ref_code'      => $checkCV[0]->ref_code,
				
			));
			}
			
			} else{
			//check if the variable is a function control variable
			$isFunction=DB::Select("SELECT `isFunction` FROM `tblpayroll_variable` WHERE `id`='$variable->id' and `isFunction`=1");
			if($isFunction) {
				if($variable->variable_type==2){
					$amount = $this->deductionsFunction($year, $month, $staff->id, $variable->id, $variable->percent);
				} else {
					$amount = $this->earningsFunction($staff->offer_amount, $variable->percent);
				}
			}else{
					$dat=DB::Select("SELECT `$variable->ref_code` as amount FROM `tblpayroll_salary_new_chart` WHERE `grade`='$staff->grade' and `step`='$step' ");
					if($dat) $amount=$dat[0]->amount;
				}
			}
			
			return $amount;
			
		}
		Public function StaffVariable($staffid) {
			return DB::Select("SELECT  *
			,(SELECT `variable` FROM `tblpayroll_variable` WHERE `tblpayroll_variable`.id= tblstaff_cv.cvid) as variables
			,(SELECT `particular` FROM `tblvariable_type` WHERE `tblvariable_type`.id= tblstaff_cv.cv_type) as particular
			FROM `tblstaff_cv` WHERE `staffid`='$staffid' order by cv_type");
		}
		Public function VariableInfo($cv) {
		$dt= DB::Select("SELECT  * FROM `tblpayroll_variable` WHERE `id`='$cv' ");
			if($dt)return $dt[0];
			
			return DB::Select("SELECT '' as variable_type ,  '' as variable,'' as status ,'' as  istaxable,'' as  rank,'' as ref_code ")[0]; ;
		}
		Public function CVRembalance($id,$amount,$tamount,$recycling){
		if($recycling==1){return $amount;}
		$List= DB::Select("SELECT IFNULL(sum(`amount`),0) as TSum FROM `tblstaff_monthly_cv` WHERE `staffcvid`='$id'");
		$rem=$tamount-$List[0]->TSum;
		if($rem >= $amount){return $amount;}
		else{return $rem;}
		}
		Public function NewVariable( $variable) {
			
			DB::statement("ALTER TABLE tblpayroll_salary_new_chart ADD $variable  DOUBLE DEFAULT 0");
			DB::statement("ALTER TABLE tblpayroll_payment ADD $variable  DOUBLE DEFAULT 0");
		
	}

	Public function DropVariable( $variable) {
		DB::statement("ALTER TABLE tblpayroll_salary_new_chart DROP $variable");
		DB::statement("ALTER TABLE tblpayroll_payment DROP $variable");
		return "success";
	}

	Public function BankList() {
		return DB::Select("SELECT * FROM `tblbanklist`");
	}
	
	Public function Payrolls($year,$month) {
	    return DB::Select("SELECT *
	    ,(SELECT `grade` FROM `tblstaff_grade_level` WHERE `tblstaff_grade_level`.`id`= tblpayroll_payment.grade) as grades
	    FROM `tblpayroll_payment` WHERE `year`='$year' and `month`='$month'");
	}
	Public function Payroll($year,$month,$staffid) {
	    return DB::table('tblpayroll_payment')->where('year',$year)->where('month',$month)->where('staffid',$staffid)->first();
	}
	Public function MonthlyActiveVariable($year,$month) {
	    $allvariable=DB::Select("SELECT tblpayroll_variable.* 
	    FROM `tblpayroll_variable_monthly`  
	    join tblpayroll_variable 
	    on tblpayroll_variable.id= tblpayroll_variable_monthly.variableid 
	    WHERE `year`='$year' and `month`='$month'");
	    $sumallvariable="'0' as `init`";
	    foreach ($allvariable as $v){
	       $sumallvariable .=", sum($v->ref_code) as $v->ref_code"; 
	    }
	    $rdata= DB::Select("SELECT $sumallvariable FROM `tblpayroll_payment` WHERE `year`='$year' and `month`='$month'");
	     if($rdata) return $rdata[0];
	     return DB::Select("SELECT '0' as `init`")[0];
	}
	Public function PDeductionVariable($year,$month) {
	    $myArr = [];
	    $allvariable=DB::Select("SELECT tblpayroll_variable.* 
	    FROM `tblpayroll_variable_monthly`  
	     join tblpayroll_variable 
	    on tblpayroll_variable.id= tblpayroll_variable_monthly.variableid 
	    WHERE `year`='$year' and `month`='$month' order by tblpayroll_variable.rank");
	    $sumallvariable="'0' as `init`";
	    foreach ($allvariable as $v){
	       $sumallvariable .=", sum($v->ref_code) as $v->ref_code"; 
	    }
	    //$qdata= DB::Select("SELECT $sumallvariable FROM `tblpayroll_payment` WHERE `year`='$year' and `month`='$month'")[0];
	    $qdata= DB::Select("SELECT $sumallvariable FROM `tblpayroll_payment` WHERE `year`='$year' and `month`='$month'");
	    if($qdata)$qdata=$qdata[0];
	    $vdata= DB::Select("SELECT tblpayroll_variable_monthly.* FROM `tblpayroll_variable_monthly`  join tblpayroll_variable 
	    on tblpayroll_variable.id= tblpayroll_variable_monthly.variableid
	    where tblpayroll_variable_monthly.`variable_type`='2' and month='$month' and year='$year' order by `rank`");
	    //$vdata= DB::Select("SELECT * FROM `tblpayroll_variable` where `variable_type`='2' ");
	    foreach ($vdata as $v2){
	        $ref=$v2->ref_code;
	       if(!$qdata->$ref==0) 
	       $myArr[]=$v2;
	    }
	    return $myArr;
	}
	Public function TaxableEarningVariableTaxable($year,$month) {
	    $myArr = [];
	    $allvariable=DB::Select("SELECT tblpayroll_variable.* 
	    FROM `tblpayroll_variable_monthly`  
	     join tblpayroll_variable 
	    on tblpayroll_variable.id= tblpayroll_variable_monthly.variableid 
	    WHERE `year`='$year' and `month`='$month' order by tblpayroll_variable.rank");
	    
	    $sumallvariable="'0' as `init`";
	    foreach ($allvariable as $v){
	       $sumallvariable .=", sum($v->ref_code) as $v->ref_code"; 
	    }
	    
	    $qdata= DB::Select("SELECT $sumallvariable FROM `tblpayroll_payment` WHERE `year`='$year' and `month`='$month'");
	    if($qdata)$qdata=$qdata[0];
	    $vdata= DB::Select("SELECT tblpayroll_variable_monthly.* FROM `tblpayroll_variable_monthly`  join tblpayroll_variable 
	    on tblpayroll_variable.id= tblpayroll_variable_monthly.variableid
	    where tblpayroll_variable_monthly.`variable_type`='1' and tblpayroll_variable_monthly.istaxable=1 and month='$month' and year='$year' order by `rank`");
	    foreach ($vdata as $v2){
	        $ref=$v2->ref_code;
	       if(!$qdata->$ref==0) 
	       $myArr[]=$v2;
	    }
	    return $myArr;
	}
	Public function NonTaxableEarningVariable($year,$month) {
	    $myArr = [];
	    $allvariable=DB::Select("SELECT tblpayroll_variable.* 
	    FROM `tblpayroll_variable_monthly`  
	    join tblpayroll_variable 
	    on tblpayroll_variable.id= tblpayroll_variable_monthly.variableid 
	    WHERE `year`='$year' and `month`='$month' order by tblpayroll_variable.rank");
	    $sumallvariable="'0' as `init`";
	    foreach ($allvariable as $v){
	       $sumallvariable .=", sum($v->ref_code) as $v->ref_code"; 
	    }
	    $qdata= DB::Select("SELECT $sumallvariable FROM `tblpayroll_payment` WHERE `year`='$year' and `month`='$month'");
	    if($qdata)$qdata=$qdata[0];
	    $vdata= DB::Select("SELECT tblpayroll_variable_monthly.* FROM `tblpayroll_variable_monthly`  join tblpayroll_variable 
	    on tblpayroll_variable.id= tblpayroll_variable_monthly.variableid
	    where tblpayroll_variable_monthly.`variable_type`='1' and tblpayroll_variable_monthly.istaxable=0 and month='$month' and year='$year' order by `rank`");
	    foreach ($vdata as $v2){
	        $ref=$v2->ref_code;
	       if(!$qdata->$ref==0) 
	       $myArr[]=$v2;
	    }
	    return $myArr;
	}
	Public function PEarningVariable($year,$month) {
	    $myArr = [];
	    $allvariable=DB::Select("SELECT * FROM `tblpayroll_variable`");
	    $sumallvariable="'0' as `init`";
	    foreach ($allvariable as $v){
	       $sumallvariable .=", sum($v->ref_code) as $v->ref_code"; 
	    }
	    $qdata= DB::Select("SELECT $sumallvariable FROM `tblpayroll_payment` WHERE `year`='$year' and `month`='$month'")[0];
	    $vdata= DB::Select("SELECT * FROM `tblpayroll_variable` where `variable_type`='1' ");
	    foreach ($vdata as $v2){
	        $ref=$v2->ref_code;
	       if(!$qdata->$ref==0) 
	       $myArr[]=$v2;
	    }
	    return $myArr;
	}
	Public function NetpaySummary($year,$month) {
	    $allvariable=DB::Select("SELECT tblpayroll_variable.* 
	    FROM `tblpayroll_variable_monthly`  
	    join tblpayroll_variable 
	    on tblpayroll_variable.id= tblpayroll_variable_monthly.variableid 
	    WHERE `year`='$year' and `month`='$month' order by tblpayroll_variable.rank");
	    $sumallvariable="0 ";
	    foreach ($allvariable as $v){
	       $sumallvariable .="+`$v->ref_code`"; 
	    }
	    $sumallvariable .= " as Net";
	    $qdata= DB::Select("SELECT tblpayroll_payment.*,$sumallvariable, tblbanklist.bank, tblbanklist.bankCode
	    FROM `tblpayroll_payment` 
	    left join tblbanklist on tblbanklist.bankID=tblpayroll_payment.bankid
	    WHERE `year`='$year' and `month`='$month'");
	    return $qdata;
	}
	Public function PayrollParticular($year, $month, $particular) {
		$isVariableExist=DB::Select("SELECT * 
	    FROM `tblpayroll_variable_monthly`  
	    WHERE `year`='$year' and `month`='$month' and `ref_code`= '$particular'");
		if(!$isVariableExist){
			return [];
		}
	    $particular_variable = " `$particular` as Net";
	    $qdata= DB::Select("SELECT tblpayroll_payment.*, $particular_variable, tblbanklist.bank
	    FROM `tblpayroll_payment` 
	    left join tblbanklist on tblbanklist.bankID=tblpayroll_payment.bankid
	    WHERE `year`='$year' and `month`='$month' and `$particular`<>0");
	    return $qdata;
	}
	Public function GradeChart($grade,$step=1,$emp=1) {
	   return DB::table ('tblpayroll_salary_new_chart')->where('grade',$grade)
		->first();
	}
	Public function StaffProfile($id) {
	     $data= DB::Select("SELECT * FROM `tblstaff` WHERE `id`='$id'");
	    if($data) return $data[0];
	     return DB::Select("SELECT * FROM `tblstaff` WHERE `id`='0'")[0];
	}

	function calculateAnnualProgressiveTax(float $income): float
	{
		$tax = 0;

		$brackets = [
			[800000, 0.0],
			[2200000, 0.15],
			[8000000, 0.18],
			[13000000, 0.21],
			[25000000, 0.23],
			[PHP_FLOAT_MAX, 0.25],
		];

		foreach ($brackets as [$limit, $rate]) {
			if ($income <= 0) break;

			$taxable = min($income, $limit);
			$tax += $taxable * $rate;
			$income -= $taxable;
		}

		return round($tax, 2);
	}

	function calculateMonthlyProgressiveTax(float $income): float
	{
		$income = $income * 12;
		return round($this->calculateAnnualProgressiveTax($income) / 12, 2);
	}

	function earningsFunction( $annual_gross_pay,$percentage)	
	{
		$amount = $annual_gross_pay * $percentage / (100*12);
		return round($amount, 2);
	}
	function deductionsFunction($year, $month, $staffId, $variable,$percentage)	
	{
		// get cv elements from functions_control_variables where control_variabeId = variable
		// join with tblpayroll_variable to get ref_code
		$cvElements = DB::Select("
			SELECT 
				tblpayroll_variable.ref_code
			FROM functions_control_variables
			JOIN tblpayroll_variable ON tblpayroll_variable.id = functions_control_variables.added_control_variableId
			WHERE functions_control_variables.control_variabeId = '$variable'
		");
		
		// get the staff payroll for the month
		$staffPayroll = DB::Select("
			SELECT *
			FROM tblpayroll_payment
			WHERE staffid = '$staffId' AND year = '$year' AND month = '$month'
		");
		if($staffPayroll && count($cvElements) > 0){
			$sum=0;
			foreach ($cvElements as $cvElement) {
				// Access dynamic property using curly braces
				$refCode = $cvElement->ref_code;
				if(isset($staffPayroll[0]->$refCode)) {
					$sum += $staffPayroll[0]->$refCode;
				}
			}
			if($variable==2){
				// dd($sum*12);
				// get the sum of all before tax variables for the staff in the tblpayroll_payment table
				$beforeTaxVariables = $this->VariableWithBeforeTax();
				$beforeTaxSum = 0;
				$computedPayroll = DB::Select("
					SELECT *
					FROM tblpayroll_payment
					WHERE staffid = '$staffId' AND year = '$year' AND month = '$month'
				");
				if($computedPayroll && count($beforeTaxVariables) > 0){
					foreach ($beforeTaxVariables as $beforeTaxVariable) {
						$refCode = $beforeTaxVariable->ref_code;
						if(isset($computedPayroll[0]->$refCode)) {
							$beforeTaxSum += $computedPayroll[0]->$refCode;
						}
					}
				}
				
				$sum = $sum + $beforeTaxSum;
				$tax = $this->calculateMonthlyProgressiveTax($sum);
				return round($tax, 2);
			}
			return round($sum * $percentage / 100, 2);
		} else {
			return 0;
		}
	}

	function employerContribution($amount, $mapped_data){
		$employeePercentage=$mapped_data->staff_percentage;
		$employerPercentage=$mapped_data->company_percentage;
		//dd($amount, $employeePercentage, $employerPercentage);
		if($employeePercentage==0){
			$employerContribution = $amount*$employerPercentage*0.01;
		} else{
			$employerContribution =$amount*$employerPercentage/$employeePercentage;
		}
		return abs($employerContribution);
	}

}