<?php

namespace App\Http\Controllers;

use App\Http\Traits\AccountTrait;
use App\Models\AccountChart;
use Auth;
//use session;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session;

class AccountReport extends Controller
{
    use AccountTrait;


    public function TrialBalance(Request $request)
    {
        //if (!$this->AuthenticateRoute("new-brand")) return view('lock.index');
        $data['fromdate'] = $request->input('fromdate');
        $data['todate'] = $request->input('todate');
        if ($data['todate'] == "") {$data['todate'] = date("Y-m-d");}

        if ($data['fromdate'] == "") {$data['fromdate'] = date("Y-m-d");}
        //dd(date('m-d',strtotime($data['fromdate'])));
        $data['TrialBal'] = $this->TrialBal($data['fromdate'], $data['todate']);
        return view('AccountReport.trialbalance', $data);
    }

    public function BalanceSheet(Request $request)
    {
        //if (!$this->AuthenticateRoute("new-brand")) return view('lock.index');
        $data['asatdate'] = $request->input('asatdate');
        if ($data['asatdate'] == "") {$data['asatdate'] = date("Y-m-d");}
        $data['CurrentAsset'] = $this->CurrentAsset($data['asatdate']);
        $data['FixedAsset'] = $this->FixedAsset($data['asatdate']);
        $data['Liability'] = $this->Liability($data['asatdate']);
        $data['LongLiability'] = $this->LongLiability($data['asatdate']);
        $data['Equity'] = $this->Equity($data['asatdate']);
        $data['PL'] = $this->PL($data['asatdate']);
        return view('AccountReport.balancesheet', $data);
    }

    public function IncomeExpense(Request $request)
    {
        //if (!$this->AuthenticateRoute("new-brand")) return view('lock.index');
        $data['fromdate'] = $request->input('fromdate');
        $data['todate'] = $request->input('todate');
        if ($data['todate'] == "") {$data['todate'] = date("Y-m-d");}
        if ($data['fromdate'] == "") {$data['fromdate'] = date("Y-m-d");}
        $data['Incomedata'] = $this->Income($data['fromdate'], $data['todate']);
        $data['Expensedata'] = $this->Expenses($data['fromdate'], $data['todate']);

        return view('AccountReport.plreport', $data);
    }

    public function Notes(Request $request, $d_at = null, $id = 0)
    {
        //if (!$this->AuthenticateRoute("new-brand")) return view('lock.index');
        $data['asatdate'] = $request->input('asatdate');
        if ($data['asatdate'] == '') {
            $data['asatdate'] = $d_at;
        }

        if ($data['asatdate'] == "") {$data['asatdate'] = date("Y-m-d");}
        if ($id == 0) {$data['BalanceSheetFullSubHead'] = $this->PLFull($data['asatdate']);} else {
            $data['BalanceSheetFullSubHead'] = $this->BalanceSheetFullSubHead($data['asatdate'], $id);}
        return view('AccountReport.fullsubhead', $data);
    }

    public function PLNotes(Request $request, $from = null, $to = null, $id = 0)
    {
        //if (!$this->AuthenticateRoute("new-brand")) return view('lock.index');
        $data['from'] = $request->input('from');
        if ($data['from'] == '') {
            $data['from'] = $from;
        }

        if ($data['from'] == "") {$data['from'] = date("Y-m-d");}
        $data['to'] = $request->input('to');
        if ($data['to'] == '') {
            $data['to'] = $to;
        }

        if ($data['to'] == "") {$data['to'] = date("Y-m-d");}
        $data['PLFullSubHead'] = $this->PLFullSubHead($data['from'], $data['to'], $id);
        return view('AccountReport.plfullsubhead', $data);
    }

    public function GeneralNotes(Request $request)
    {
        $data['asatdate'] = $request->input('asatdate');
        if ($data['asatdate'] == "") {$data['asatdate'] = date("Y-m-d");}
        $data['BalanceSheetFullSubHeadPL'] = $this->PLFull($data['asatdate']);
        $data['BalanceSheetFullSubHead'] = $this->GeneralNote($data['asatdate']);
        return view('AccountReport.generalnote', $data);
    }

    public function RefTransaction($ref = null)
    {
        //if (!$this->AuthenticateRoute("new-brand")) return view('lock.index');
        $data['ref'] = $ref;
        $data['RefTrans'] = DB::Select("SELECT *
        ,(SELECT `accountdescription` FROM `tblaccountchart` WHERE `tblaccountchart`.`id`=accountid) as accountName
        FROM `tblaccount_transaction` WHERE `ref`='$ref'");
        return view('AccountReport.ref_trans', $data);
    }

    public function General_Transaction(Request $request)
    {
        //if (!$this->AuthenticateRoute("new-brand")) return view('lock.index');
        $data['fromdate'] = $request->input('fromdate');
        $data['todate'] = $request->input('todate');
        if ($data['todate'] == "") {$data['todate'] = date("Y-m-d");}
        if ($data['fromdate'] == "") {$data['fromdate'] = date("Y-m-d");}

        $data['Gen_Transaction'] = $this->Gen_Transaction($data['fromdate'], $data['todate']);
        return view('AccountReport.general_trans', $data);
    }

    public function Chart_of_Account()
    {
        //die("ksksks");
        $data['AccountList'] = $this->AccountList('', '');
        //dd( $data['AccountList']);
        return view('AccountReport.chart_account', $data);
    }

    public function PettyReport(Request $request)
    {
        $data['particular'] = $request->input('particular');
        $data['fromdate'] = $request->input('fromdate');
        $data['todate'] = $request->input('todate');
        if ($data['todate'] == "") {$data['todate'] = date("Y-m-d");}
        if ($data['fromdate'] == "") {$data['fromdate'] = date("Y-m-d");}
        if ($data['fromdate'] > $data['todate']) {
            [$data['fromdate'], $data['todate']] = [$data['todate'], $data['fromdate']];
        }

        $data['ProjectAccount'] = $this->ProjectAccount();
        $data['PettyTransaction'] = DB::table('pettyhandling_transactions as p')
            ->leftJoin('petty_expenses as pe', 'p.projectid', '=', 'pe.id')
            ->leftJoin('account_charts as ac', 'p.accountid', '=', 'ac.id')
            ->leftJoin('users as u', 'p.postby', '=', 'u.id')
            ->select(
                'p.id',
                'p.transdate',
                'p.remark',
                'p.amount',
                'p.manual_ref',
                'pe.particular as Particular',
                DB::raw("CONCAT(COALESCE(ac.accountdescription, ''), '(', COALESCE(ac.accountno, ''), ')') as AccountName"),
                'u.name as Postedby',
                DB::raw("'' as FPost")
            )
            ->when(!empty($data['particular']), function ($query) use ($data) {
                $query->where('p.projectid', $data['particular']);
            })
            ->whereDate('p.transdate', '>=', $data['fromdate'])
            ->whereDate('p.transdate', '<=', $data['todate'])
            ->orderBy('p.transdate', 'desc')
            ->orderBy('p.id', 'desc')
            ->get();

        return view('AccountReport.pettycashreport', $data);
    }

    public function CashFlowReport(Request $request)
    {
        $data['fromdate'] = $request->input('fromdate');
        $data['todate'] = $request->input('todate');
        $data['subheadid'] = $request->input('subheadid');
        $data['accountid'] = $request->input('accountid');

        if ($data['todate'] == "") {
            $data['todate'] = date("Y-m-d");
        }
        if ($data['fromdate'] == "") {
            $data['fromdate'] = date("Y-m-d");
        }
        if ($data['fromdate'] > $data['todate']) {
            [$data['fromdate'], $data['todate']] = [$data['todate'], $data['fromdate']];
        }

        $data['cashSubheads'] = DB::table('account_subheads')
            ->where('status', 1)
            ->where('payment_method', 'Cash')
            ->select('id', 'subhead')
            ->orderBy('subhead', 'asc')
            ->get();

        $data['cashAccounts'] = DB::table('account_charts as ac')
            ->join('account_subheads as ash', 'ac.subheadid', '=', 'ash.id')
            ->where('ash.status', 1)
            ->where('ash.payment_method', 'Cash')
            ->when(!empty($data['subheadid']), function ($query) use ($data) {
                $query->where('ac.subheadid', $data['subheadid']);
            })
            ->select('ac.id', 'ac.accountno', 'ac.accountdescription')
            ->orderBy('ac.accountdescription', 'asc')
            ->get();

        $baseQuery = DB::table('account_transactions as t')
            ->join('account_subheads as s', 't.subheadid', '=', 's.id')
            ->leftJoin('account_charts as a', 't.accountid', '=', 'a.id')
            ->leftJoin('users as u', 't.postby', '=', 'u.id')
            ->where('s.payment_method', 'Cash')
            ->when(!empty($data['subheadid']), function ($query) use ($data) {
                $query->where('t.subheadid', $data['subheadid']);
            })
            ->when(!empty($data['accountid']), function ($query) use ($data) {
                $query->where('t.accountid', $data['accountid']);
            });

        $openingQuery = clone $baseQuery;
        $data['openingInflow'] = (float) $openingQuery
            ->whereDate('t.transdate', '<', $data['fromdate'])
            ->sum('t.debit');

        $openingOutflowQuery = clone $baseQuery;
        $data['openingOutflow'] = (float) $openingOutflowQuery
            ->whereDate('t.transdate', '<', $data['fromdate'])
            ->sum('t.credit');

        $data['openingBalance'] = $data['openingInflow'] - $data['openingOutflow'];

        $periodInflowQuery = clone $baseQuery;
        $data['periodInflow'] = (float) $periodInflowQuery
            ->whereDate('t.transdate', '>=', $data['fromdate'])
            ->whereDate('t.transdate', '<=', $data['todate'])
            ->sum('t.debit');

        $periodOutflowQuery = clone $baseQuery;
        $data['periodOutflow'] = (float) $periodOutflowQuery
            ->whereDate('t.transdate', '>=', $data['fromdate'])
            ->whereDate('t.transdate', '<=', $data['todate'])
            ->sum('t.credit');

        $data['netCashFlow'] = $data['periodInflow'] - $data['periodOutflow'];
        $data['closingBalance'] = $data['openingBalance'] + $data['netCashFlow'];

        $periodDetailQuery = clone $baseQuery;
        $data['cashTransactions'] = $periodDetailQuery
            ->whereDate('t.transdate', '>=', $data['fromdate'])
            ->whereDate('t.transdate', '<=', $data['todate'])
            ->select(
                't.id',
                't.transdate',
                't.ref',
                't.manual_ref',
                't.remarks',
                't.debit',
                't.credit',
                't.accountid',
                'a.accountno',
                'a.accountdescription',
                's.subhead',
                'u.name as postedBy'
            )
            ->orderBy('t.transdate', 'desc')
            ->orderBy('t.id', 'desc')
            ->get();

        $ledgerSummaryQuery = clone $baseQuery;
        $data['cashFlowByLedger'] = $ledgerSummaryQuery
            ->whereDate('t.transdate', '>=', $data['fromdate'])
            ->whereDate('t.transdate', '<=', $data['todate'])
            ->select(
                't.accountid',
                'a.accountno',
                'a.accountdescription',
                DB::raw('SUM(t.debit) as inflow'),
                DB::raw('SUM(t.credit) as outflow'),
                DB::raw('SUM(t.debit - t.credit) as netflow')
            )
            ->groupBy('t.accountid', 'a.accountno', 'a.accountdescription')
            ->orderBy('a.accountdescription', 'asc')
            ->get();

        return view('AccountReport.cashflowreport', $data);
    }

    public function QuarterlyBalanceSheetold(Request $request)
    {

        $data['year'] = $request->input('year');
        $data['qtr'] = $request->input('qtr');
        $curdate = $data['year'] . '-' . DB::table('tblevaluation_period')->where('id', $data['qtr'])->value('endperiod_date');
        $prevdate = ($data['year'] - 1) . '-' . DB::table('tblevaluation_period')->where('id', $data['qtr'])->value('endperiod_date');
        $begincurdate = $data['year'] . '-' . DB::table('tblevaluation_period')->where('id', $data['qtr'])->value('period_begin');
        $beginprevdate = ($data['year'] - 1) . '-' . DB::table('tblevaluation_period')->where('id', $data['qtr'])->value('period_begin');
        $data['asatdate'] = $prevdate;
        if ($data['asatdate'] == "") {$data['asatdate'] = date("Y-m-d");}
        $data['CurrentAsset'] = $this->CurrentAsset($curdate);
        $data['FixedAsset'] = $this->FixedAsset($curdate);
        $data['Liability'] = $this->Liability($curdate);
        $data['LongLiability'] = $this->LongLiability($curdate);
        $data['Equity'] = $this->Equity($curdate);
        $data['PL'] = $this->PL($curdate);

        $data['CurrentAsset2'] = $this->CurrentAsset($prevdate);
        $data['FixedAsset2'] = $this->FixedAsset($prevdate);
        $data['Liability2'] = $this->Liability($prevdate);
        $data['LongLiability2'] = $this->LongLiability($prevdate);
        $data['Equity2'] = $this->Equity($prevdate);
        $data['PL2'] = $this->PL($prevdate);
        $data['PLWithinCurrentYearCurrentQtr'] = $this->PLWithin($begincurdate, $curdate);
        $data['PLWithinPrevYearCurrentQtr'] = $this->PLWithin($beginprevdate, $prevdate);
        $data['QuarterlyPeriod'] = $this->QuarterlyPeriod();
        $data['curdate'] = $curdate;
        $data['prevdate'] = $prevdate;

        return view('AccountReport.qtrbalancesheet', $data);
    }

    public function QuarterlyBalanceSheet(Request $request)
    {

        $data['year'] = $request->input('year');
        $data['qtr'] = $request->input('qtr');
        $curdate = $data['year'] . '-' . DB::table('tblevaluation_period')->where('id', $data['qtr'])->value('endperiod_date');
        $prevdate = ($data['year'] - 1) . '-' . DB::table('tblevaluation_period')->where('id', $data['qtr'])->value('endperiod_date');
        $begincurdate = $data['year'] . '-' . DB::table('tblevaluation_period')->where('id', $data['qtr'])->value('period_begin');
        $beginprevdate = ($data['year'] - 1) . '-' . DB::table('tblevaluation_period')->where('id', $data['qtr'])->value('period_begin');
        switch ($data['qtr']) {
            case 1:
                $preQrtIncur = 4;
                $preYrIncur = $data['year'] - 1;
                $preYrInpre = $data['year'] - 2;
                break;
            case 2:
                $preQrtIncur = 1;
                $preYrIncur = $data['year'];
                $preYrInpre = $data['year'] - 1;
                break;
            case 3:
                $preQrtIncur = 2;
                $preYrIncur = $data['year'];
                $preYrInpre = $data['year'] - 1;
                break;
            case 4:
                $preQrtIncur = 3;
                $preYrIncur = $data['year'];
                $preYrInpre = $data['year'] - 1;
                break;
            default:
                $preQrtIncur = 0;
                $preYrIncur = 0;

                $preYrInpre = 0;
        }
        $curdate2 = $preYrIncur . '-' . DB::table('tblevaluation_period')->where('id', $preQrtIncur)->value('endperiod_date');
        $prevdate2 = $preYrInpre . '-' . DB::table('tblevaluation_period')->where('id', $preQrtIncur)->value('endperiod_date');
        $begincurdate2 = $preYrIncur . '-' . DB::table('tblevaluation_period')->where('id', $preQrtIncur)->value('period_begin');
        $beginprevdate2 = $preYrInpre . '-' . DB::table('tblevaluation_period')->where('id', $preQrtIncur)->value('period_begin');
        $data['asatdate'] = $prevdate;
        if ($data['asatdate'] == "") {$data['asatdate'] = date("Y-m-d");}
        $data['CurrentAsset'] = $this->CurrentAssetCompare($curdate, $prevdate);
        $data['FixedAsset'] = $this->FixedAssetCompare($curdate, $prevdate);
        $data['Liability'] = $this->LiabilityCompare($curdate, $prevdate);
        $data['LongLiability'] = $this->LongLiabilityCompare($curdate, $prevdate);
        $data['Equity'] = $this->EquityCompare($curdate, $prevdate);
        $data['PL'] = $this->PLCompare($curdate, $prevdate);

        $data['PLWithinCurrentYearCurrentQtr'] = $this->PLWithin($begincurdate, $curdate);
        $data['PLWithinPrevYearCurrentQtr'] = $this->PLWithin($beginprevdate, $prevdate);
        $data['PLWithinCurrentYearCurrentQtr2'] = $this->PLWithin($begincurdate2, $curdate2);
        $data['PLWithinPrevYearCurrentQtr2'] = $this->PLWithin($beginprevdate2, $prevdate2);
        $data['QuarterlyPeriod'] = $this->QuarterlyPeriod();
        $data['curdate'] = $curdate;
        $data['prevdate'] = $prevdate;

        return view('AccountReport.qtrbalancesheet', $data);
    }

    public function ComparativeBalanceSheet(Request $request)
    {

        $data['asatdate1'] = $request->input('asatdate1');
        if ($data['asatdate1'] == "") {$data['asatdate1'] = date("Y-m-d");}
        $data['asatdate2'] = $request->input('asatdate2');
        if ($data['asatdate2'] == "") {$data['asatdate2'] = date("Y-m-d");}
        $data['asatdate3'] = $request->input('asatdate3');
        if ($data['asatdate3'] == "") {$data['asatdate3'] = date("Y-m-d");}
        $data['asatdate4'] = $request->input('asatdate4');
        if ($data['asatdate4'] == "") {$data['asatdate4'] = date("Y-m-d");}
        $data['asatdate5'] = $request->input('asatdate5');
        if ($data['asatdate5'] == "") {$data['asatdate5'] = date("Y-m-d");}
        $data['asatdate6'] = $request->input('asatdate6');
        if ($data['asatdate6'] == "") {$data['asatdate6'] = date("Y-m-d");}
        $data['asatdate5b'] = $request->input('asatdate5b');
        if ($data['asatdate5b'] == "") {$data['asatdate5b'] = date("Y-m-d");}
        $data['asatdate6b'] = $request->input('asatdate6b');
        if ($data['asatdate6b'] == "") {$data['asatdate6b'] = date("Y-m-d");}

        $data['asatdate7'] = $request->input('asatdate7');
        if ($data['asatdate7'] == "") {$data['asatdate7'] = date("Y-m-d");}
        $data['asatdate8'] = $request->input('asatdate8');
        if ($data['asatdate8'] == "") {$data['asatdate8'] = date("Y-m-d");}
        $data['asatdate9'] = $request->input('asatdate9');
        if ($data['asatdate9'] == "") {$data['asatdate9'] = date("Y-m-d");}
        $data['asatdate8b'] = $request->input('asatdate8b');
        if ($data['asatdate8b'] == "") {$data['asatdate8b'] = date("Y-m-d");}
        $data['asatdate9b'] = $request->input('asatdate9b');
        if ($data['asatdate9b'] == "") {$data['asatdate9b'] = date("Y-m-d");}
        $data['asatdate10'] = $request->input('asatdate10');
        if ($data['asatdate10'] == "") {$data['asatdate10'] = date("Y-m-d");}

        $data['CurrentAsset'] = $this->CurrentAssetComparative($data['asatdate1'], $data['asatdate2'], $data['asatdate3'], $data['asatdate4']);
        $data['FixedAsset'] = $this->FixedAssetComparative($data['asatdate1'], $data['asatdate2'], $data['asatdate3'], $data['asatdate4']);
        $data['Liability'] = $this->LiabilityComparative($data['asatdate1'], $data['asatdate2'], $data['asatdate3'], $data['asatdate4']);
        $data['LongLiability'] = $this->LongLiabilityComparative($data['asatdate1'], $data['asatdate2'], $data['asatdate3'], $data['asatdate4']);
        $data['Equity'] = $this->EquityComparative($data['asatdate1'], $data['asatdate2'], $data['asatdate3'], $data['asatdate4']);
        $data['PL'] = $this->PLComparative($data['asatdate1'], $data['asatdate2'], $data['asatdate3'], $data['asatdate4']);

        $data['PLWithinCurrentYearCurrentQtr'] = $this->PLWithin($data['asatdate5'], $data['asatdate5b']);
        $data['PLWithinPrevYearCurrentQtr'] = $this->PLWithin($data['asatdate6'], $data['asatdate6b']);
        $data['PLWithinCurrentYearCurrentQtr2'] = $this->PLWithin($data['asatdate8'], $data['asatdate8b']);
        $data['PLWithinPrevYearCurrentQtr2'] = $this->PLWithin($data['asatdate9'], $data['asatdate9b']);

        $data['CPL'] = $this->PL($data['asatdate7']);
        $data['PPL'] = $this->PL($data['asatdate10']);
        $data['QuarterlyPeriod'] = $this->QuarterlyPeriod();

        return view('AccountReport.comparativebalancesheet', $data);
    }

    public function ComparativePL(Request $request)
    {

        $data['asatdate5'] = $request->input('asatdate5');
        if ($data['asatdate5'] == "") {$data['asatdate5'] = date("Y-m-d");}
        $data['asatdate6'] = $request->input('asatdate6');
        if ($data['asatdate6'] == "") {$data['asatdate6'] = date("Y-m-d");}
        $data['asatdate5b'] = $request->input('asatdate5b');
        if ($data['asatdate5b'] == "") {$data['asatdate5b'] = date("Y-m-d");}
        $data['asatdate6b'] = $request->input('asatdate6b');
        if ($data['asatdate6b'] == "") {$data['asatdate6b'] = date("Y-m-d");}

        $data['asatdate7'] = $request->input('asatdate7');
        if ($data['asatdate7'] == "") {$data['asatdate7'] = date("Y-m-d");}
        $data['asatdate8'] = $request->input('asatdate8');
        if ($data['asatdate8'] == "") {$data['asatdate8'] = date("Y-m-d");}
        $data['asatdate9'] = $request->input('asatdate9');
        if ($data['asatdate9'] == "") {$data['asatdate9'] = date("Y-m-d");}
        $data['asatdate8b'] = $request->input('asatdate8b');
        if ($data['asatdate8b'] == "") {$data['asatdate8b'] = date("Y-m-d");}
        $data['asatdate9b'] = $request->input('asatdate9b');
        if ($data['asatdate9b'] == "") {$data['asatdate9b'] = date("Y-m-d");}
        $data['asatdate10'] = $request->input('asatdate10');
        if ($data['asatdate10'] == "") {$data['asatdate10'] = date("Y-m-d");}

        $data['PLWithinCurrentYearCurrentQtr'] = $this->PLWithin($data['asatdate5'], $data['asatdate5b']);
        //dd($data['asatdate6'].'  '.$data['asatdate6b']);
        $data['PLWithinPrevYearCurrentQtr'] = $this->PLWithin($data['asatdate6'], $data['asatdate6b']);
        //dd($data['PLWithinPrevYearCurrentQtr']);
        $data['PLWithinCurrentYearCurrentQtr2'] = $this->PLWithin($data['asatdate8'], $data['asatdate8b']);
        $data['PLWithinPrevYearCurrentQtr2'] = $this->PLWithin($data['asatdate9'], $data['asatdate9b']);

        $data['CPL'] = $this->PL($data['asatdate7']);
        $data['PPL'] = $this->PL($data['asatdate10']);

        $data['ExpensesComparative'] = $this->ExpensesComparative($data['asatdate5'], $data['asatdate5b'], $data['asatdate6'], $data['asatdate6b'], $data['asatdate8'], $data['asatdate8b'], $data['asatdate9'], $data['asatdate9b'], $data['asatdate7'], $data['asatdate10']);
        $data['IncomeComparative'] = $this->IncomeComparative($data['asatdate5'], $data['asatdate5b'], $data['asatdate6'], $data['asatdate6b'], $data['asatdate8'], $data['asatdate8b'], $data['asatdate9'], $data['asatdate9b'], $data['asatdate7'], $data['asatdate10']);

        return view('AccountReport.comparativepl', $data);
    }

    public function ChangeInEquity(Request $request)
    {
        $data['year1'] = $request->input('year1');
        if ($data['year1'] == "") {$data['year1'] = date("Y");}
        $data['year2'] = $request->input('year2');
        if ($data['year2'] == "") {$data['year2'] = date("Y");}
        $data['month1'] = $request->input('month1');
        if ($data['month1'] == "") {$data['month1'] = date("m");}
        $data['month2'] = $request->input('month2');
        if ($data['month2'] == "") {$data['month2'] = date("m") - 1;}
        $data['beginat1'] = date("Y-m-d", strtotime($data['year1'] . '-' . $data['month1'] . '-01'));
        $data['beginat2'] = date("Y-m-d", strtotime($data['year2'] . '-' . $data['month2'] . '-01'));
        $data['asatdate1'] = date("Y-m-t", strtotime($data['year1'] . '-' . $data['month1'] . '-01'));
        $data['asatdate2'] = date("Y-m-t", strtotime($data['year2'] . '-' . $data['month2'] . '-01'));
        $data['asatdate'] = $request->input('asatdate');
        if ($data['asatdate'] == "") {$data['asatdate'] = date("Y-m-d");}
        $data['CurrentAsset'] = $this->CurrentAsset($data['asatdate']);
        $data['FixedAsset'] = $this->FixedAsset($data['asatdate']);
        $data['Liability'] = $this->Liability($data['asatdate']);
        $data['LongLiability'] = $this->LongLiability($data['asatdate']);
        $data['Equity'] = $this->Equity($data['asatdate']);
        if (isset($_POST['update'])) {

            $this->validate($request, [
                'year' => 'required|string',
                'month' => 'required|string',
            ]);

            $capital = preg_replace('/[^\d.]/', '', $request->input('share_capital'));
            $earning = preg_replace('/[^\d.]/', '', $request->input('retain_earning'));

            if ($this->OwnersTransaction($request->input('year'), $request->input('month'))) {
                DB::table('tblowner_transactions')->where('year', $request->input('year'))->where('month', $request->input('month'))->update([
                    'capital' => is_numeric($capital) ? $capital : 0,
                    'earning' => is_numeric($earning) ? $earning : 0,
                ]);

            } else {

                DB::table('tblowner_transactions')->insert([
                    'year' => $request->input('year'),
                    'month' => $request->input('month'),
                    'capital' => is_numeric($capital) ? $capital : 0,
                    'earning' => is_numeric($earning) ? $earning : 0,
                ]);
            }
            return back()->with('message', 'record successfully updated.');
        }

        $data['Months'] = $this->Months();
        $data['PL'] = $this->PL($data['asatdate']);
        $data['OwnersTransaction1'] = $this->OwnersTransaction($data['year1'], $data['month1']);
        $data['OwnersTransaction2'] = $this->OwnersTransaction($data['year2'], $data['month2']);
        return view('AccountReport.changeinequity', $data);
    }

    ///////////////////////////////////////////// adams ///////////////////////////////////////

    public function AccountStatements(Request $request)
    {
        //if (!$this->AuthenticateRoute("new-brand")) return view('lock.index');
        $data['acctid'] = $request->input('acctid');
        if ($data['acctid'] == '') {$data['acctid'] = Session::get('acctid');}
        Session(['acctid' => $data['acctid']]);

        $data['accountname'] = AccountTrait::AccountName($data['acctid']);
        $data['fromdate'] = $request->input('fromdate');
        $data['todate'] = $request->input('todate');
        if ($data['todate'] == "") {$data['todate'] = date("Y-m-d");}
        if ($data['fromdate'] == "") {$data['fromdate'] = date("Y-m-d");}
        $data['TrialBal'] = AccountTrait::trialBal($data['fromdate'], $data['todate']);

        $data['AccountList'] = AccountChart::all();

        $data['AccountStatementRunningTotal'] = AccountTrait::AccountStatementRunningTotal($data['acctid'], $data['fromdate'], $data['todate']);

        return view('AccountReport.accountstatement', $data);
    }

    public function Transaction_Summary(Request $request)
    {
        $data['ref']=$request->input('ref');
       	$data['fromdate']=$request->input('fromdate');
       	$data['todate']=$request->input('todate');
       	if($data['todate']==""){$data['todate']=date("Y-m-d");}
        if($data['fromdate']==""){$data['fromdate']=date("Y-m-d");}

        if ( isset( $_POST['del'] ) ) {

            $del=$request->input('deleteid');
            $trans_date=db::table('account_transactions')->where('ref',$del)->value('transdate');

            if(db::table('financial_ends')->where('year_end_date','>=',$trans_date)->first())return back()->with('error_message',' This year perid has already been closed. Hence the transaction not deletable' );

            DB::delete("DELETE FROM `batch_post_temps` WHERE `ref`='$del'");

            DB::delete("DELETE FROM `account_transactions` WHERE `ref`='$del'");
            DB::delete("DELETE FROM `pettyhandling_transactions` WHERE `ref`='$del'");
            DB::delete("DELETE FROM `temp_journal_transfer` WHERE `ref`='$del'");

            DB::table('automated_record')->where('ref_no', $del)->update([
                'process_status' => 0,
                'processed_at' => '',
            ]);
            return back()->with('message',' Record successfully trashed.'  );
        }
        $data['RefBatch']= AccountTrait::refBatch();
        $data['Trans_Summary'] = AccountTrait::trans_Summary($data['fromdate'],$data['todate'],$data['ref']);

        // dd($data);
        return view('AccountReport.transaction_summary', $data);
    }

    public function RefTransactionPost(Request $request)
    {
        $data['ref'] = $request->input('ref');
        $ref = $data['ref'];

        if (isset($_POST['update'])) {
            $this->validate($request, ['account' => 'required']);
            $accountdetails = AccountTrait::getAccountDetails($request->input('account'));
            if (!$accountdetails) {
                return back()->with('error_message', ' Record not updated! Invalid Account selected');
            }

            DB::table('account_transactions')->where('id', $request->input('id'))->update([
                'groupid' => $accountdetails->groupid,
                'headid' => $accountdetails->headid,
                'subheadid' => $accountdetails->subheadid,
                'accountid' => $request->input('account'),
                'accountcode' => $accountdetails->accountno,
            ]);
        }
        $data['RefBatch'] = AccountTrait::refBatch();
        $data['AccountList'] = AccountChart::all();
        $data['RefTrans'] =  DB::table('account_transactions')
        ->select('account_transactions.*', 'account_charts.accountdescription as accountName')
        ->join('account_charts', 'account_transactions.accountid', '=', 'account_charts.id')
        ->where('account_transactions.ref', $ref)
        ->get();

        return view('AccountReport.ref_trans2', $data);
    }

}
