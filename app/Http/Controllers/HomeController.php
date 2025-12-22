<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
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
use App\Http\Traits\SessionTrait;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['activeCustomer']=Customer::where('status',1)->count();
        $data['activeLoans']=Loan::where('status',2)->count();
        $data['applications']=Loan::where('status',0)->count();
        $data['overdue']=Loan::where('amount_outstanding','>',0)->sum('amount_outstanding');
        return view('dashboard', $data);
    }
    
    
    public function index2($id)
    {
         $data['payslip'] = DB::table('payslip')->where('id',$id)->first();
        //  dd($data['payslip']);
        return view('customer.payslip', $data);
    }
    
    // SELECT `id`, `names`, `emp_no`, `Year`, `Month`, `basic`, `housing`, `transportation`, `medical`, `utility`, `tax`, `pension`, `nhf`, `loan`, `position` FROM `payslip` WHERE 1
     public function payslipEntry(Request $request)
    {
        
        if (isset ($_POST['addnew'])) {
            
            $this->validate($request, [
            'names'          => 'required',
            'emp_no'    => 'required',
            'Year'    => 'required',
            'Month'    => 'required',
            'basic'    => 'nullable|numeric',
            'housing'    => 'nullable|numeric',
            'transportation'    => 'nullable|numeric',
            'medical'    => 'nullable|numeric',
            'utility'    => 'nullable|numeric',
            'tax'    => 'nullable|numeric',
            'loan'    => 'nullable|numeric',
            'pension'    => 'nullable|numeric',
            'nhf'    => 'nullable|numeric',
            'position'    => 'required',
          ]);
         $customer = DB::table('payslip')->insertGetId([
        'names' => $request->input('names'),
        'emp_no' => $request->input('emp_no'),
        'Year' => $request->input('Year'),
        'Month' => $request->input('Month'),
        'basic' => $request->input('basic'),
        'housing' => $request->input('housing'),
        'transportation' => $request->input('transportation'),
        'medical' => $request->input('medical'),
        'utility' => $request->input('utility'),
        'tax' => $request->input('tax'),
        'pension' => $request->input('pension'),
        'nhf' => $request->input('nhf'),
        'loan' => $request->input('loan'),
        'position' => $request->input('position')
    ]);
        }
    $data['payslips'] = DB::table('payslip')->get();
        return view('customer.payslip_form', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCustomerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCustomerRequest  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
