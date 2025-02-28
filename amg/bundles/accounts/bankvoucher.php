<?php
Tools::getModel("Accounts");
Tools::getLib("Utils");
$ac = new Accounts();
$util = new Utils();
$errors = array();

$debits = array();
$credits = array();
$cofs[] = array();
$date = "";
$description = "";

$totalDebit = 0;
$totalCredit = 0;
$vals = array();
$temp = array();

if(isset($_POST['_chk'])==1){

    $date = isset($_POST['date']) ? $tool->ChangeDateFormat($_POST['date']) : "";
    $description = isset($_POST['description']) ? $ac->filter($_POST['description']) : "";

    if(empty($date)){
        $errors[] = $tool->transnoecho("please_enter_date");
    }

    if(empty($description)){
        $errors[] = $tool->transnoecho("please_enter_description");
    }

    if(!$tool->checkDateFormat($date)){
        $errors[] = $tool->transnoecho("please_enter_valid_date");
    }


    foreach ($_POST['chart_of_ac'] as $key => $val){
        $chartOfAccount = $tool->GetInt($val);
        $debit = $tool->GetInt($_POST['debit'][$key]);
        $credit = $tool->GetInt($_POST['credit'][$key]);
        $cofs[$key] = $_POST;

        if(empty($chartOfAccount) || empty($debit) || empty($credit)){
            $errors[] = $tool->transnoecho("please_enter_all_voucher_detail");
        }

        $totalDebit += $debit;
        $totalCredit += $credit;

        $temp['account_id'] = $chartOfAccount;
        $temp['debit'] = $debit;
        $temp['credit'] = $credit;
        $vals[] = $temp;

    }

    if($totalDebit != $totalCredit){
        $errors[] = $tool->transnoecho("debit_and_credit_should_be_equal");
    }


    if(count($errors)==0){
        $res = $ac->insertVoucherTransation($date,$ac->getTypeByPage($tpl->getCurrentPage()),$description,$vals);

        if($res){
            $_SESSION['msg'] = $tool->Message("succ",$tool->transnoecho("voucher_created"));
        }
        else{
            $_SESSION['msg'] = $tool->Message("alert",$tool->transnoecho("tracsaction_failed"));
        }

        $tool->Redir("accounts","bankvoucher","","");
        exit;
    }


}

$tpl->renderBeforeContent();
$rowData = $ac->getChartOfAccounts();



foreach ($rowData as $row){
    if($row['id'] == -1){
        continue;
    }
    $rows[] = array('id' => $row['id'], 'name' => $row['text'], 'parent' => $row['parent_id'], "code" => $row['code']);
}

$tree = $util->buildSelectTree($rows);
$chosen_rtl = $tpl->getChosenClass();

$tool->displayErrorArray($errors);
?>



    <div class="body">
        <div class="row-fluid">
            <div class="social-box">
                <div class="header fonts">
                    <h4><?php echo $tpl->getPageTitle() ?></h4>
                </div>

                <form method="post" action="">
                    <?php echo $tpl->formHidden() ?>
                <div class="body">

                    <div class="row-fluid">
                        <div class="span3">

                            <div class="row-fluid">
                                <label for="date"><?php $tool->trans("date") ?></label>
                                <input type="text" value="<?php if(isset($_POST['date'])) echo $_POST['date'] ?>" class="date" id="date" name="date"/>

                            </div>
                            <div class="row-fluid">

                                <label for="description"><?php $tool->trans("description") ?></label>
                                <input type="text" value="<?php if(isset($_POST['description'])) echo $_POST['description'] ?>" id="description" name="description"/>

                            </div>

                            <div class="row-fluid">
                                <button type="submit" name="submit" class="btn btn-success"> <?php $tool->trans("save") ?> <i class="icon-save"></i></button>
                            </div>
                        </div>
                        <div class="span9" id="voucher_detail">


                            <?php



                            foreach ($cofs as $cof => $cofVal){

                                $debit = null;
                                $credit = null;
                                $selectedCof = null;
                                if(isset($_POST['debit'][$cof])){
                                    $debit = $_POST['debit'][$cof];
                                }

                                if(isset($_POST['credit'][$cof])){
                                    $credit = $_POST['credit'][$cof];
                                }

                                if(isset($_POST['chart_of_ac'][$cof])){
                                    $selectedCof = $_POST['chart_of_ac'][$cof];
                                }

                                $util->setSel($selectedCof);
                            ?>
                            <div class="row-fluid contain_row">

                                <div class="span3">
                                    <label for="chart_of_ac"><?php $tool->trans("chart_of_ac") ?></label>
                                    <select name="chart_of_ac[]" class="chosen-select<?php echo $chosen_rtl ?> cof" >
                                        <?php echo $util->makeSelectPrintTree($tree); ?>
                                    </select>
                                </div>
                                <div class="span3">
                                    <?php
                                    if($debit == null && isset($_POST['_chk'])==1){
                                        echo '<p class="alert alert-error">' . $tool->transnoecho("please_enter_value") . '</p>';
                                    }
                                    ?>
                                    <label for="debit"><?php $tool->trans("debit") ?></label>
                                    <input type="text" value="<?php echo $debit ?>" class="debit" name="debit[]" style="width: 80%"/>
                                </div>
                                <div class="span3">
                                    <?php
                                    if($credit == null && isset($_POST['_chk'])==1){
                                        echo '<p class="alert alert-error">' . $tool->transnoecho("please_enter_value") . '</p>';
                                    }
                                    ?>
                                    <label for="credit"><?php $tool->trans("credit") ?></label>
                                    <input type="text" value="<?php echo $credit ?>" class="credit" name="credit[]"  style="width: 80%"/>
                                </div>

                                <div class="span3">
                                    <label>&nbsp;</label>
                                    <button type="button" name="submit" class="btn btn-primary add"> <?php $tool->trans("add") ?> <i class="icon-arrow-down"></i></button>
                                    <button type="button" name="submit" class="btn btn-danger del"> <?php $tool->trans("delete") ?> <i class="icon-remove"></i></button>
                                </div>
                            </div>
                         <?php } ?>
                        </div>
                    </div>



                </div>
                </form>

            </div>
        </div>
    </div>


    <script>
        $(document).ready(function(){
            var group_id = 0;
            $(document).on('click', '.add', function() {

                /*$('.contain_row:last')
                    .clone()
                    .appendTo("#voucher_detail");*/

                $("#voucher_detail").append( htmlRow() );


                //$(this).closest("select.cof").trigger("chosen:updated");
                $(".cof:last").chosen({ allow_single_deselect: true });

            });
            $(document).on('click', '.del', function() {
                if( ($('.del').length) > 1){
                    $(this).closest("div.contain_row").remove();
                }
            });
        });
        
        
        function htmlRow() {
            var htm = '';
            htm += '<div class="row-fluid contain_row">';
            htm += '<div class="span3">';
            htm += '<label for="chart_of_ac"><?php $tool->trans("chart_of_ac") ?></label>';
            htm += '<select name="chart_of_ac[]" class="chosen-select<?php echo $chosen_rtl ?> cof" >';
            htm += '<?php echo $util->makeSelectPrintTree($tree); ?>';
            htm += '</select>';
            htm += '</div>';
            htm += '<div class="span3">';
            htm += '<label for="debit"><?php $tool->trans("debit") ?></label>';
            htm += '<input type="text" class="debit" name="debit[]" style="width: 80%"/>';
            htm += '</div>';
            htm += '<div class="span3">';
            htm += '<label for="credit"><?php $tool->trans("credit") ?></label>';
            htm += '<input type="text" class="credit" name="credit[]"  style="width: 80%"/>';
            htm += '</div>';
            htm += '<div class="span3">';
            htm += '<label>&nbsp;</label>';
            htm += '<button type="button" name="submit" class="btn btn-primary add"> <?php $tool->trans("add") ?> <i class="icon-arrow-down"></i></button>';
            htm += '<button type="button" name="submit" class="btn btn-danger del"> <?php $tool->trans("delete") ?> <i class="icon-remove"></i></button>';
            htm += '</div>';
            htm += '</div>';
            return htm;
        }

    </script>
<?php
$tpl->footer();