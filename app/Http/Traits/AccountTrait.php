<?php
namespace App\Http\Traits;

use DB;
use Auth;

trait AccountTrait
{
    public static function debitAccount($accountid, $amount, $ref, $transdate, $remark, $userid, $manual_ref, $account=null)
    {

        if($amount==0) return null;

	    $accountdetails = AccountTrait::getAccountDetails($accountid);

	    return DB::table('account_transactions')->insert([
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

    public static  function creditAccount($accountid, $amount, $ref, $transdate, $remark, $userid, $manual_ref, $account=null)
    {
        if($amount==0) return null;
	    $accountdetails=AccountTrait::getAccountDetails($accountid);

	    return DB::table('account_transactions')->insert([
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

    public static function getAccountDetails($id)
    {
	    return DB::table('account_charts')->where('id', '=', $id)->first();
	}
    public static function getSubheadDetails($id)
    {
	    return DB::table('account_subheads')->where('id', $id)->first();;
	}

	public static function journalPending($status) {
	    $userid=Auth::user()->id;
		return DB::table('temp_journal_transfer')
		->where('temp_journal_transfer.status', $status)
		->where('temp_journal_transfer.postby', $userid)
		->leftjoin('account_charts','account_charts.id','temp_journal_transfer.accountid')
		->select('temp_journal_transfer.*','account_charts.accountdescription')->get();
	}


	public static function RefNo() {
        $alphabet = "0123456789";
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 6; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        $date = date_create();
        $initcode= date_format($date, 'U' ) ;
        return $initcode . implode($pass);

    }

    public static function trialBal($from,$to) {
	    if (date('m-d',strtotime($from))=="01-01")$from="1900-01-01";
	    $timedate= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$from' AND '$to')";
	    return DB::Select("SELECT  Sum(`debit`-`credit`) as  Credit, accountdescription as accountName, accountid, accounthead
	    FROM `account_transactions`
        left join account_charts on account_charts.id=account_transactions.accountid
        left join account_heads on account_charts.headid= account_heads.id
         WHERE  $timedate

         group by `accountid`,`accountdescription`,`accounthead`   order by accountName");

	}
    public static function balanceBreakdown($from, $to, $account) {
	    if (date('m-d',strtotime($from))=="01-01")$from="1900-01-01";
	    $timedate= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$from' AND '$to')";
	    return DB::Select("SELECT  Sum(`debit`-`credit`) as  Credit, accountdescription as accountName
	    FROM `account_transactions`
        left join account_charts_sub on account_charts_sub.id=account_transactions.account_sub
         WHERE  $timedate and account_transactions.accountid='$account'

         group by `account_sub`,`accountdescription`   order by accountName");

	}
    public static function income($fromdate, $todate) {
	    $timedate= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$fromdate' AND '$todate')";
	    return DB::Select("SELECT SUM(debit-credit) as tVal,`subheadid`,subhead

	      FROM `account_transactions`
          left join `account_subheads` on account_subheads.id=account_transactions.subheadid
          WHERE `account_transactions`.`headid`=7 and $timedate and `is_trial`=1 group by `subheadid`,`subhead`");

	}
	public static function expenses($fromdate, $todate) {
	    $timedate= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$fromdate' AND '$todate')";
        return DB::Select("SELECT SUM(debit-credit) as tVal,`subheadid`,subhead
        FROM `account_transactions`
        left join `account_subheads` on account_subheads.id=account_transactions.subheadid
        WHERE `account_transactions`.`headid`=6 and $timedate and `is_trial`=1 group by `subheadid`,`subhead`");

	}
	public static function PL($date) {
	    return DB::Select("SELECT SUM(credit-debit) as tVal FROM `account_transactions` WHERE (`headid`=7 or `headid`=6) and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date'");
	}
	public static function PL_List($date) {
	    return DB::Select("SELECT SUM(credit-debit) as tVal,accountid FROM `account_transactions` WHERE (`headid`=7 or `headid`=6) and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date' group by `accountid`");
	}
	public static function Equity($date) {
	    return DB::Select("SELECT SUM(credit-debit) as tVal,`subheadid`
	    ,(SELECT `subhead` FROM `account_subheads` WHERE account_subheads.id=subheadid)as Subhead
	    FROM `account_transactions` WHERE `headid`=5 and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date' group by `subheadid`
	    order by (SELECT `rank` FROM `account_subheads` WHERE account_subheads.id=subheadid)");

	}
	public static function LongLiability($date) {
	    return DB::Select("SELECT SUM(credit-debit) as tVal,`subheadid`
	    ,(SELECT `subhead` FROM `account_subheads` WHERE account_subheads.id=subheadid)as Subhead
	     FROM `account_transactions` WHERE `headid`=4 and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date' group by `subheadid`
	     order by (SELECT `rank` FROM `account_subheads` WHERE account_subheads.id=subheadid)");

	}
	public static function Liability($date) {
	    return DB::Select("SELECT SUM(credit-debit) as tVal,`subheadid`
	    ,(SELECT `subhead` FROM `account_subheads` WHERE account_subheads.id=subheadid)as Subhead
	      FROM `account_transactions` WHERE `headid`=3 and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date' group by `subheadid`
	      order by (SELECT `rank` FROM `account_subheads` WHERE account_subheads.id=subheadid)");

	}
	public static function FixedAsset($date) {
	    return DB::Select("SELECT SUM(debit-credit) as tVal,`subheadid`
	    ,(SELECT `subhead` FROM `account_subheads` WHERE account_subheads.id=subheadid)as Subhead
	      FROM `account_transactions` WHERE `headid`=2 and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date' group by `subheadid`
	      order by (SELECT `rank` FROM `account_subheads` WHERE account_subheads.id=subheadid)");

	}
	public static function CurrentAsset($date) {
	    return DB::Select("SELECT SUM(debit-credit) as tVal,`subheadid`
	    ,(SELECT `subhead` FROM `account_subheads` WHERE account_subheads.id=subheadid)as Subhead
	      FROM `account_transactions` WHERE `headid`=1 and DATE_FORMAT(`transdate`,'%Y-%m-%d')<='$date' group by `subheadid`
	      order by (SELECT `rank` FROM `account_subheads` WHERE account_subheads.id=subheadid)");

	}

    public static function UnpostedJournalPending_sef($status) {
        $userid = Auth::user()->id;
        return DB::select("
            SELECT
                temp_journal_transfer.ref,
                temp_journal_transfer.transdate,
                temp_journal_transfer.manual_ref,
                SUM(temp_journal_transfer.credit) AS t_val,
                users.name
            FROM
                `temp_journal_transfer`
            LEFT JOIN
                users ON `users`.`id` = temp_journal_transfer.postby
            WHERE
                `batch_status` = '0' AND `postby` = '$userid' and temp_journal_transfer.ref <> ''
            GROUP BY
                temp_journal_transfer.ref,
                temp_journal_transfer.transdate,
                temp_journal_transfer.manual_ref,
                users.name
            ORDER BY
                temp_journal_transfer.transdate
        ");
    }


    public static function SelectedJournalPending($ref, $status) {
        return DB::select("
            SELECT
                *,
                (SELECT CONCAT(`accountdescription`, '(', `accountno`, ')')
                 FROM `account_charts`
                 WHERE `account_charts`.`id` = `temp_journal_transfer`.accountid) as accountdescription
            FROM
                `temp_journal_transfer`
            WHERE
                `ref` = :ref
                AND `batch_status` = :status",
            ['ref' => $ref, 'status' => $status]
        );
    }

    public static function FetchAccountCodes($id) {
        return DB::table('account_charts')->where('id', '=', $id)->first();
    }

    public static function AccountName($id) {
        $dt = self::FetchAccountCodes($id);

        if ($dt) {
            return $dt->accountdescription . '(' . $dt->accountno . ')';
        }

        return '';
    }

    Public static function AccountStatementRunningTotal($account,$fromdate,$todate) {
        $opening="0";
        $result = DB::Select("SELECT Sum(`credit`-`debit`) as Opening FROM `account_transactions` WHERE DATE_FORMAT(`transdate`,'%Y-%m-%d')<'$fromdate' and `accountid`='$account'");
        if($result){$opening=$result[0]->Opening;}

        $timedate= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$fromdate' AND '$todate')";
         $result1 = " SELECT *, (@csum) as prev, `debit`,`credit`, (@csum := @csum +`credit`-`debit`) as `current`  FROM `account_transactions` JOIN (SELECT @csum := '$opening') r WHERE  $timedate and `accountid`='$account' order by DATE_FORMAT(`transdate`,'%Y-%m-%d') ,`id`";
        return DB::Select($result1);

    }

    public static function ProjectAccount() {
        return DB::select(DB::raw(
            "SELECT
                *,
                (SELECT CONCAT(accountdescription, '(', accountno, ')') FROM account_charts WHERE account_charts.id = expensenid) as AccountName
            FROM
                petty_expenses"
        ));
    }


    public static function PettyTransaction($petty = '', $br = '') {
        $qPetty = 1;
        if (!empty($petty)) {
            $qPetty = "`pettyhandling_transactions`.`projectid`='$petty'";
        }

        $qBranch = 1;
        if (!empty($br)) {
            $qBranch = "`pettyhandling_transactions`.`branch_id`='$br'";
        }

        return DB::select(DB::raw(
            "SELECT
                pettyhandling_transactions.*,
                (SELECT CONCAT(accountdescription, '(', accountno, ')') FROM account_charts WHERE account_charts.id = accountid) as AccountName,
                (SELECT particular FROM petty_expenses WHERE petty_expenses.id = projectid) as Particular,
                (SELECT name FROM users WHERE users.id = postby) as Postedby,
                branches.branch as Branch,
                users.name as FPost
            FROM
                pettyhandling_transactions
            LEFT JOIN
                branches ON pettyhandling_transactions.branch_id = branches.id
            LEFT JOIN
                users ON users.id = pettyhandling_transactions.final_post_by
            WHERE
                $qBranch AND $qPetty"
        ));
    }


    public static function defaultAccountLookUp($id) {
        $headId = DB::table('default_setups')->where('id', $id)->value('headid');

        return DB::select(DB::raw(
            "SELECT *
            FROM account_charts
            WHERE headid = :headId"
        ), ['headId' => $headId]);
    }



    public static function defaultAccount() {

        return DB::select(DB::raw(
            "SELECT
                *,
                (SELECT CONCAT(accountdescription, '(', accountno, ')') FROM account_charts WHERE account_charts.id = default_setups.accoountId) as AccountName
            FROM
                default_setups"
        ));
    }

    // public static function defaultProductAccountLookUp($id) {
    //     $accoountId = DB::table('product_types')->where('id', $id)->value('account_id');

    //     return DB::table('account_charts')->get();
    // }


    public static function defaultProductAccountLookUp($id) {
	    // $id2=DB::table('tblDefault_setup')->where('id', '=', $id)->value('headid');
	    return DB::Select("SELECT * FROM `account_charts`");
	}

    public static function defaultProductAccount() {

        return DB::select(DB::raw(
            "SELECT
                *,
                (SELECT CONCAT(accountdescription, '(', accountno, ')') FROM account_charts WHERE account_charts.id = product_types.account_id) as AccountName
            FROM
                product_types
            ORDER BY description"
        ));
    }

    public static function refBatch() {
        $result = DB::table('account_transactions')
        ->select('ref', DB::raw('MAX(manual_ref) as manual_ref'))
        ->groupBy('ref')
        ->orderBy('ref', 'asc')
        ->get();

    return $result;
        // return DB::Select("SELECT `ref`, `transdate` , MAX(`manual_ref`) as `manual_ref` FROM `account_transactions` GROUP BY `ref` ORDER BY `manual_ref`");
    }


    // Public function trans_Summary($from,$to,$ref=null) {
	//     $timedate= "(DATE_FORMAT(`transdate`,'%Y-%m-%d') BETWEEN '$from' AND '$to')";
	//     //$timedate=1;
	//     if($ref) return DB::Select("SELECT *, sum(`debit`) as sum_total FROM `account_transactions` WHERE  `ref`='$ref'  and is_trial= 1   group by `ref` order by  `transdate`");
	//     return DB::Select("SELECT *, sum(`debit`) as sum_total FROM `account_transactions` WHERE  $timedate  and is_trial= 1   group by `ref` order by  `transdate`");

	// }

    public static function trans_Summary($from, $to, $ref = null) {
        $timedate = "(DATE_FORMAT(`transdate`, '%Y-%m-%d') BETWEEN '$from' AND '$to')";

        // if ($ref) {
        //     return DB::Select("SELECT `transdate`, `manual_ref`, `ref`, `remarks`, `createdat`, SUM(`debit`) as sum_total FROM `account_transactions` WHERE  `ref`='$ref' AND is_trial= 1   GROUP BY `ref`, `transdate`, `manual_ref`, `remarks`, `createdat` ORDER BY  `transdate`");
        // } else {
        //     return DB::Select("SELECT `transdate`, `manual_ref`, `ref`, `remarks`, `createdat`, SUM(`debit`) as sum_total FROM `account_transactions` WHERE  $timedate AND is_trial= 1   GROUP BY `ref`, `transdate`, `manual_ref`, `remarks`, `createdat` ORDER BY  `transdate`");
        // }

        $query = DB::table('account_transactions')
        ->select(
            DB::raw('MAX(transdate) as transdate'),
            'ref',
            DB::raw('MAX(manual_ref) as manual_ref'),
            DB::raw('MAX(remarks) as remarks'),
            DB::raw('MAX(createdat) as createdat'),
            DB::raw('SUM(debit) as sum_total')
        )
        ->where('is_trial', 1);

        if ($ref) {
            $query->where('ref', $ref)
                ->groupBy('ref') // Exclude transdate from GROUP BY
                ->orderBy('transdate');
        } else {
            $query->whereBetween(DB::raw("DATE_FORMAT(transdate, '%Y-%m-%d')"), [$from, $to])
                ->groupBy('ref')
                ->orderBy('transdate');
        }

        return $query->get();
    }


}
