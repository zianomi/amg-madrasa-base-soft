<?php
/**
 * Created by PhpStorm.
 * User: ZIA
 * Date: 10/21/2018
 * Time: 10:57 PM
 */
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();

$tpl->renderBeforeContent();

$qr->searchContentAbove();
$qr->searchContentBottom();
?>

    <div class="body">


    <table class="table">
        <tbody><tr>
            <td colspan="2" class="center"><strong>Gray Electronic Repair Services</strong></td>
        </tr>
        <tr>
            <td colspan="2" class="center"><strong>Chart of Accounts</strong></td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2"><strong>ASSETS (1000-1999)</strong></td>
        </tr>
        <tr>
            <td style="width:36px;">1000</td>
            <td style="width:350px;">Cash</td>
        </tr>
        <tr>
            <td>1010</td>
            <td>Accounts Receivable</td>
        </tr>
        <tr>
            <td>1011</td>
            <td>Allowance for Doubtful Accounts</td>
        </tr>
        <tr>
            <td>1020</td>
            <td>Notes Receivable</td>
        </tr>
        <tr>
            <td>1030</td>
            <td>Interest Receivable</td>
        </tr>
        <tr>
            <td>1040</td>
            <td>Service Supplies</td>
        </tr>
        <tr>
            <td>1510</td>
            <td>Leasehold Improvements</td>
        </tr>
        <tr>
            <td>1520</td>
            <td>Furniture and Fixtures</td>
        </tr>
        <tr>
            <td>1521</td>
            <td>Accumulated Depreciation – Furniture and Fixtures</td>
        </tr>
        <tr>
            <td>1530</td>
            <td>Service Equipment</td>
        </tr>
        <tr>
            <td>1531</td>
            <td>Accumulated Depreciation – Service Equipment</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2"><strong>LIABILITIES (2000-2999)</strong></td>
        </tr>
        <tr>
            <td>2000</td>
            <td>Accounts Payable</td>
        </tr>
        <tr>
            <td>2010</td>
            <td>Notes Payable</td>
        </tr>
        <tr>
            <td>2020</td>
            <td>Salaries Payable</td>
        </tr>
        <tr>
            <td>2030</td>
            <td>Rent Payable</td>
        </tr>
        <tr>
            <td>2040</td>
            <td>Interest Payable</td>
        </tr>
        <tr>
            <td>2050</td>
            <td>Unearned Revenue</td>
        </tr>
        <tr>
            <td>2060</td>
            <td>Loans Payable</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2"><strong>OWNER'S EQUITY (3000-3999)</strong></td>
        </tr>
        <tr>
            <td>3000</td>
            <td>Mr. Gray, Capital</td>
        </tr>
        <tr>
            <td>3010</td>
            <td>Mr. Gray, Drawing</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2"><strong>REVENUES (4000-4999)</strong></td>
        </tr>
        <tr>
            <td>4000</td>
            <td>Service Revenue</td>
        </tr>
        <tr>
            <td>4010</td>
            <td>Interest Income</td>
        </tr>
        <tr>
            <td>4020</td>
            <td>Gain on Sale of Equipment</td>
        </tr>
        <tr>
            <td>4999</td>
            <td>Income Summary</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2"><strong>EXPENSES (5000-5999)</strong></td>
        </tr>
        <tr>
            <td>5000</td>
            <td>Rent Expense</td>
        </tr>
        <tr>
            <td>5010</td>
            <td>Salaries Expense</td>
        </tr>
        <tr>
            <td>5020</td>
            <td>Supplies Expense</td>
        </tr>
        <tr>
            <td>5030</td>
            <td>Utilities Expense</td>
        </tr>
        <tr>
            <td>5040</td>
            <td>Interest Expense</td>
        </tr>
        <tr>
            <td>5050</td>
            <td>Taxes and Licenses</td>
        </tr>
        <tr>
            <td>5060</td>
            <td>Depreciation Expense</td>
        </tr>
        <tr>
            <td>5070</td>
            <td>Doubtful Accounts Expense</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        </tbody></table>
    </div>
<?php
$tpl->footer();
