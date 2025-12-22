<style>
    .salary-slip {
        margin: 15px;

        .empDetail {
            width: 100%;
            text-align: left;
            border: 2px solid black;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .head {
            margin: 10px;
            margin-bottom: 50px;
            width: 100%;
        }

        .companyName {
            text-align: right;
            font-size: 25px;
            font-weight: bold;
        }

        .salaryMonth {
            text-align: center;
        }

        .table-border-bottom {
            border-bottom: 1px solid;
        }

        .table-border-right {
            border-right: 1px solid;
        }

        .myBackground {
            padding-top: 10px;
            text-align: left;
            border: 1px solid black;
            height: 40px;
        }

        .myAlign {
            text-align: center;
            border-right: 1px solid black;
        }

        .myTotalBackground {
            padding-top: 10px;
            text-align: left;
            background-color: #EBF1DE;
            border-spacing: 0px;
        }

        .align-4 {
            width: 25%;
            float: left;
        }

        .tail {
            margin-top: 35px;
        }

        .align-2 {
            margin-top: 25px;
            width: 50%;
            float: left;
        }

        .border-center {
            text-align: center;
        }

        .border-center th,
        .border-center td {
            border: 1px solid black;
        }

        th,
        td {
            padding-left: 6px;
        }
    }
</style>

<div class="salary-slip">
    <table class="empDetail">
        <tr height="100px" style=''>
            <td colspan='4'>
                <img height="90px" src='https://app.credithubltd.com/assets/img/logo.jpeg' />
            </td>
            <td colspan='4' class="companyName">ACCOUNTING SOLUTIONS</td>
        </tr>
        <tr>
            <th>
                Name
            </th>
            <td>
                {{ $payslip->names }}
            </td>
            <td></td>
            <th>
                Employee Number
            </th>
            <td>
                {{ $payslip->emp_no }}
            </td>
            <td></td>
            <th>
                Position
            </th>
            <td>
                {{ $payslip->position }}
            </td>
        </tr>
        <tr>
            <th>

            </th>
            <td>

            </td>
            <td></td>
            <th>

            </th>
            <td>

            </td>
            <td></td>
            <th>
                Period
            </th>
            <td>
                {{ $payslip->Month }}, {{ $payslip->Year }}
            </td>
        </tr>

        <tr class="myBackground">
            <th colspan="2">
                Earnings
            </th>
            <th>
                Particular
            </th>
            <th class="table-border-right">
                Amount (Naira)
            </th>
            <th colspan="2">
                Deductions
            </th>
            <th>
                Particular
            </th>
            <th>
                Amount (Naira)
            </th>
        </tr>
        <tr>
            <th colspan="2">
                Basic Salary
            </th>
            <td></td>
            <td class="myAlign">
                {{ number_format($payslip->basic ?? 0, 2, '.', ',') }}
            </td>
            <th colspan="2">
                PAYE Tax
            </th>
            <td></td>

            <td class="myAlign">
                {{ number_format($payslip->tax ?? 0, 2, '.', ',') }}
            </td>
        </tr>
        <tr>
            <th colspan="2">
                Housing Allowance
            </th>
            <td></td>

            <td class="myAlign">
                {{ number_format($payslip->housing ?? 0, 2, '.', ',') }}
            </td>
            <th colspan="2">
                Employee Pension Contribution
            </th>
            <td></td>

            <td class="myAlign">
                {{ number_format($payslip->pension ?? 0, 2, '.', ',') }}
            </td>
        </tr>
        <tr>
            <th colspan="2">
                Transportation Allowance
            </th>
            <td></td>

            <td class="myAlign">
                {{ number_format($payslip->transportation ?? 0, 2, '.', ',') }}
            </td>
            <th colspan="2">
                NHF
            </th>
            <td></td>

            <td class="myAlign">
                {{ number_format($payslip->nhf ?? 0, 2, '.', ',') }}
            </td>
        </tr>
        <tr>
            <th colspan="2">
                Medical Allowance
            </th>
            <td></td>
            <td class="myAlign">
                {{ number_format($payslip->medical ?? 0, 2, '.', ',') }}
            </td>
            <th colspan="2">
                Salary Advances/Loan
            </th>
            <td></td>
            <td class="myAlign">
                {{ number_format($payslip->loan ?? 0, 2, '.', ',') }}
            </td>
        </tr>
        <tr>
            <th colspan="2">
                Utility Allowance
            </th>
            <td></td>

            <td class="myAlign">
                {{ number_format($payslip->utility ?? 0, 2, '.', ',') }}
            </td>
            <th colspan="2">

            </th>
            <td></td>

            <td class="myAlign">

            </td>
        </tr>
        <tr class="myBackground">
            <th colspan="3">
                Total Payments
            </th>
            <td class="myAlign">
                {{ number_format($payslip->basic + $payslip->housing + $payslip->transportation + $payslip->utility + $payslip->medical, 2, '.', ',') }}
            </td>
            <th colspan="3">
                Total Deductions
            </th>
            <td class="myAlign">
                {{ number_format($payslip->tax + $payslip->pension + $payslip->loan + $payslip->nhf, 2, '.', ',') }}
            </td>
        </tr>
        <tr height="40px">
            <th colspan="2">

            </th>
            <th>
            </th>
            <td class="table-border-right">
            </td>
            <th colspan="2" class="table-border-bottom">
                Net Salary
            </th>
            <td>
            </td>
            <td>
                {{ number_format($payslip->basic + $payslip->housing + $payslip->transportation + $payslip->utility + $payslip->medical - ($payslip->tax + $payslip->pension + $payslip->loan + $payslip->nhf), 2, '.', ',') }}
            </td>
        </tr>


    </table>

</div>
