<?php
$errors = array();
Tools::getModel("FeeModel");
Tools::getModel("StudentsModel");
$fee = new FeeModel();
$stu = new StudentsModel();

function aasort (&$array, $key) {
    $sorter=array();
    $ret=array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii]=$va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii]=$array[$ii];
    }
    $array=$ret;
}

if(isset($_POST['_chk']) == 1){

    $paid = $fee->paidStatus("paid");
    $stuid = $tool->GetInt($_POST['stuid']);

    $date = date("Y-m-d");
    $dateTime = date("Y-m-d H:i:s");


    $year = date("Y");
    $month = str_pad(date("m"),2,0,STR_PAD_RIGHT);
    $invoiceDate = $year . "-" . $month . "-10";
    $invoiceNumber = $fee->genInvoiceNumber($invoiceDate,$stuid);

    $dueDateChecks = ($_POST['due_date_check']);
    $dueDateCheck  = asort($dueDateChecks);




    if(empty($stuid)){
        $errors[] = $tool->Message("alert","ID Required");
    }

    if(count($errors)==0){
        $i=0;

        $data['invoice_id'] = $invoiceNumber;
        $data['student_id'] = $stuid;
        $data['recp_date'] = $date;
        $data['invoice_status'] = $fee->paidStatus("paid");
        $data['fee_month'] = $invoiceDate;
        $data['created_user_id'] = $tool->getUserId();
        $data['created'] = $dateTime;


        $pStatus = $fee->paidStatus("paid");




        try {
            $fee->query("START TRANSACTION");
            $invoiceGeneratedId = $fee->insertSingleInvoice($data);
            if($invoiceGeneratedId > 0){
                foreach($_POST['paid'] as $key){
                    $due_date = ($_POST['due_date'][$key]);
                    if(!empty($key) && is_numeric($key)){
                        $i++;
                        $fee->updatePaidReceive($key,$invoiceGeneratedId,$pStatus,$stuid);
                        //$fee->updateInvoiceReciep($key);
                    }
                }
            }

            $fee->query("COMMIT");
            $_SESSION['msg'] = $tool->Message("succ", $i . " " . $tool->transnoecho("records_updated"));
            $newDate = $tool->ChangeDateFormat($date);
            $tool->Redir("fees","printinvoice&_chk=1&student_id=".$stuid."&id=".$invoiceGeneratedId,"","");
        } catch (Exception $e) {
            $fee->query("ROLLBACK");
            $_SESSION['msg'] = $tool->Message("alert", $i . " " . $tool->transnoecho("error"));
            $newDate = $tool->ChangeDateFormat($date);
            $tool->Redir("fees","receivefee","","");
        }









    }

}


$tpl->renderBeforeContent();

$tool->displayErrorArray($errors);

?>

<div id="gr_res">&nbsp;</div>
<div class="social-box">
    <div class="header">

    </div>
    <div class="body">
        <div id="jamia_msg">&nbsp;</div>
        <div id="advanced-search" class="in collapse">
        <form action="" method="get">
                <?php echo $tpl->FormHidden(); ?>
                <div align="center">

                    <div class="row-fluid">
                        <div class="span4"><label class="fonts"><?php $tool->trans("id") ?></label>
                            <input value="<?php if(isset($_GET['student_id'])) echo $_GET['student_id'] ?>" type="text" name="student_id" id="student_id"/>

                        </div>
                        <div class="span4"><label class="fonts"><?php $tool->trans("GR") ?></label>
                            <input value="" type="text" name="gr" id="gr"/>
                        </div>

                        <div class="span4"><label class="fonts">&nbsp;</label>
                            <button type="submit" class="btn btn-small" style="margin-bottom:10px;"><?php $tool->trans("search") ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>


        <div id="printReady">
                    <?php
                    $id = (isset($_GET['student_id'])) ? $tool->GetInt($_GET['student_id']) : NULL;
                    if (isset($_GET['_chk']) == 1) {
                        if (empty($id)) {
                            echo $tool->Message("alert",$tool->transnoecho("please_insert_id"));
                            exit;
                        }

                        $parameters = array("id"=>$id);



                        $res = $stu->studentSearch($parameters);

                        $total = count($res);
                        if ($total < 1) {
                            echo $tool->Message("alert",$tool->transnoecho("no_result_found"));
                            exit;
                        }

                        $row = $res[0];


                        $branchStuid = $stu->operatorBranchStudent($row['branch_id']);

                        if(empty($branchStuid)){
                            echo $tool->Message("alert",$tool->transnoecho("no_student_found"));
                            $tpl->footer();
                            exit;
                        }


                        ?>



                        <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                <tr>
                                    <th><?php $tool->trans("id") ?></th>
                                    <th><?php $tool->trans("gr") ?></th>
                                    <th><span class="fonts"><?php $tool->trans("name_father_name") ?></span></th>
                                    <th><span class="fonts"><?php $tool->trans("branch") ?></span></th>
                                    <th><span class="fonts"><?php $tool->trans("class") ?></span></th>
                                    <th><span class="fonts"><?php $tool->trans("section") ?></span></th>

                                </tr>
                                </thead>


                                <tbody>
                                <?php
                                $a=0;


                                    ?>
                                    <tr>

                                        <td class="avatar"><?php echo $row['id']; ?></td>
                                        <td class="avatar"><?php echo $row['grnumber']; ?></td>
                                        <td class="username"><span class="fonts">
                                  <?php echo $row['name']; ?> <?php echo $tpl->getGenderTrans($row['gender']); ?>  <?php echo $row['fname']; ?></span>
                                        </td>
                                        <td class="firstname"><span class="fonts">
                                  <?php echo $row['branch_title']; ?></span>
                                        </td>
                                        <td class="lastname"><span class="fonts">
                                  <?php echo $row['class_title']; ?></span>
                                        </td>
                                        <td class="sex"><span class="fonts">
                                  <?php echo $row['section_title']; ?></span>
                                        </td>
                                    </tr>



                                </tbody>
                            </table>
                        </div>
                    <?php


                    $fsData = $fee->getPaidTable($id);

                        if(count($fsData) < 1){
                            echo $tool->Message("alert",$tool->transnoecho("no_fee_found"));
                            exit;
                        }


                    ?>


                    <?php
                    echo $tpl->FormTag("post");
                    echo $tpl->FormHidden();
                    $total = 0;
                    ?>
                    <input type="hidden" name="stuid" value="<?php echo $id ?>">


                <table class="table table-bordered table-striped table-hover">

                    <thead>
                        <tr>

                            <th><!--<input type="checkbox" onclick="checkAll(this)">--></th>
                            <th><?php $tool->trans("Sr#") ?></th>
                            <th><?php $tool->trans("Fee Type") ?></th>
                            <th><?php $tool->trans("Amount") ?></th>
                            <th><?php $tool->trans("Discount") ?></th>
                            <th><?php $tool->trans("Amount To Pay") ?></th>
                            <th><?php $tool->trans("Due Date") ?></th>
                        </tr>
                    </thead>
                    <?php
                    $a=0;
                    $invoiceTotal = 0;


                    foreach($fsData as $rowFee){

                        $a++;
                        $amount = $rowFee['fees'] - $rowFee['discount'];
                        $total += $amount;
                        $invoiceTotal  += $amount;

                    //if($a>3)continue;
                    ?>


                        <input type="hidden" name="due_date[<?php echo $rowFee['paid_id'] ?>]" value="<?php echo $rowFee['paid_id'] ?>">
                        <input type="hidden" name="due_date_check[<?php echo $rowFee['paid_id'] ?>]" value="<?php echo $a ?>">
                    <tr>
                        <td>

                                <input type="checkbox"<?php if($a != 1) echo ' disabled'; ?> data-id="<?php echo $a ?>" id="chkbox<?php echo $a ?>" rel="<?php echo $amount?>" value="<?php echo $rowFee['paid_id'] ?>" name="paid[<?php echo $rowFee['paid_id'] ?>]">

                        </td>
                        <td><?php echo $a ?></td>
                        <td><?php echo $rowFee['title_en'] ?></td>
                        <td><?php echo $rowFee['fees'] ?></td>
                        <td><?php echo $rowFee['discount'] ?></td>
                        <td><?php echo $amount ?></td>
                        <td style="direction: ltr"><?php
                            if ($rowFee['duration_type'] == "year") {
                                echo date('Y', strtotime($rowFee['due_date']));
                            }
                            else{
                                echo date('d F, Y', strtotime($rowFee['due_date']));
                            }

                             ?></td>


                    </tr>
                    <?php } ?>


                </table>



                <div style="text-align: center">
                    <span style="text-align: left" id="output">Total: <?php echo $total ?></span>
                    <span style="text-align: right"><button class="btn btn-success"><i class="icon-filter"></i>Save</button></span>
                     </div>

                <?php $tpl->formClose() ?>

<?php } ?>
                </div>



    </div>
</div>


<script type="text/javascript">


    function checkedLow() {

        $("input[type=checkbox]:checked").each(function() {
            var currentPos = $(this).data("id");
            var $nexcheckbox = $('#chkbox' + (currentPos + 1));
            $nexcheckbox.prop('disabled', false);
        });



    }

    $(document).ready(function() {
        checkedLow();

        function recalculate() {
            var sum = 0;

            $("input[type=checkbox]:checked").each(function() {
                //var curPos = $(this).attr("data-pos");
                sum += parseInt($(this).attr("rel"));
            });

            $("#output").html("Total: "+sum);
        }

        $("input[type=checkbox]").change(function() {
            recalculate();
            checkedLow();
        });
    });
</script>
<?php
$tpl->footer();
