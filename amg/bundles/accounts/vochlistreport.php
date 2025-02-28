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

        <div class="row-fluid">
            <div class="span12" style="text-align: center;">
                <h4>Iqra & Company</h4>
                <h5>General Ledger</h5>
                <h6>Voucher Detail</h6>
                <hr />
            </div>
        </div>

        <div class="row-fluid">
        </div>

    </div>
<?php
$tpl->footer();