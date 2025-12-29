@extends('layouts.print_layout')
@section('content')
    <div class="panel-body">
        <center>
            <h3>{{ env('Coy_Name') }} </h3>
        </center>
        <center>
            <h5>Trial Balance from {{ date('d/m/Y', strtotime($fromdate)) }} to {{ date('d/m/Y', strtotime($todate)) }}
            </h5>
        </center>
        <div class="table-responsive" style="font-size: 12px; padding:10px; color: black;" id="tableData">
            <table id="mytable" class="table table-bordered table-striped table-highlight" style="color: black;">
                <tr bgcolor="#c7c7c7">
                    <th>Account code</th>
                    <th>Account Name</th>
                    <th>Debit</th>
                    <th>Credit</th>
                </tr>
                </thead>
                @php
                    $Totaldebit = 0;
                    $Totalcredit = 0;
                @endphp
                @foreach ($TrialBal as $data)
                    <tr>
                        <td>{{ $data->accountcode }}</td>
                        <td>{{ $data->accountName }} </td>
                        @if ($data->Credit > 0)
                            <td style="text-align: right; "> {{ number_format(abs($data->Credit), 2, '.', ',') }} </td>
                            <td style="text-align: right; ">0.00</td>
                            @php $Totaldebit+= $data->Credit; @endphp
                        @else
                            <td style="text-align: right; ">0.00</td>
                            <td style="text-align: right; ">{{ number_format(abs($data->Credit), 2, '.', ',') }} </td>

                            @php $Totalcredit+= $data->Credit; @endphp
                        @endif
                    </tr>
                @endforeach
                <tr>
                    <td colspan=2>Total</td>
                    <td style="text-align: right; "><b>{{ number_format(abs($Totaldebit), 2, '.', ',') }} </b></td>
                    <td style="text-align: right; "><b>{{ number_format(abs($Totalcredit), 2, '.', ',') }} </b></td>
                </tr>


            </table>
        </div>
    </div>
@endsection
