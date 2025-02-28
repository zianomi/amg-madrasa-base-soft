<?php
$errors = array();
Tools::getModel("FeeModel");
Tools::getModel("StudentsModel");
$fee = new FeeModel();
$stu = new StudentsModel();


$tpl->renderBeforeContent();



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
                        <div class="span3"><label class="fonts"><?php $tool->trans("id") ?></label>
                            <input value="<?php if(isset($_GET['student_id'])) echo $_GET['student_id'] ?>" type="text" name="student_id" id="student_id"/>

                        </div>
                        <div class="span3"><label class="fonts">&nbsp;</label>
                            <button type="submit" class="btn btn-small" style="margin-bottom:10px;"><?php $tool->trans("search") ?></button>
                        </div>
                        <div class="span3"><label class="fonts">&nbsp;</label></div>
                        <div class="span3"><label class="fonts">&nbsp;</label></div>
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
                            $tpl->footer();
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

                        $fsData = $fee->paidAndDefaulterList(array("student_id" => $id, "stu_status" => "current", "paid_status" => $fee->paidStatus("pending")));


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
                            <th><?php $tool->trans("Sr#") ?></th>
                            <th><?php $tool->trans("Fee Type") ?></th>
                            <th><?php $tool->trans("Amount") ?></th>
                            <th><?php $tool->trans("Discount") ?></th>
                            <th><?php $tool->trans("Fee Date") ?></th>
                        </tr>
                    </thead>
                    <?php
                    $a=0;
                    $invoiceTotal = 0;


                    foreach($fsData as $rowFee){
                        $a++;
                    ?>


                        <input type="hidden" name="due_date[<?php echo $rowFee['paid_id'] ?>]" value="<?php echo $rowFee['paid_id'] ?>">
                        <input type="hidden" name="due_date_check[<?php echo $rowFee['paid_id'] ?>]" value="<?php echo $a ?>">
                    <tr>
                        <td><?php echo $a ?></td>
                        <td><?php echo $rowFee['title_en'] ?></td>
                        <td><?php echo $rowFee['fees'] ?></td>
                        <td class="discounts" data-pk="{id: <?php echo $rowFee['fee_paid_id_primary'] ?>, orign: '<?php echo $rowFee['fees'] ?>'}"><?php echo $rowFee['discount'] ?></td>
                        <td style="direction: ltr"><?php
                            if ($rowFee['duration_type'] == "year") {
                                echo date('Y', strtotime($rowFee['fee_date']));
                            }
                            else{
                                echo date('d F, Y', strtotime($rowFee['fee_date']));
                            }

                             ?></td>


                    </tr>
                    <?php } ?>


                </table>


                <?php $tpl->formClose() ?>

<?php } ?>
                </div>



    </div>
</div>


<script type="text/javascript" src="<?php echo Tools::getWebUrl() ?>/js/bootstrap-editable.js"></script>
<script type="text/javascript">
       $(function () {
           $.fn.editable.defaults.mode = 'popup';
           $('.discounts').editable({
               url: makeJsLink("ajax", "fees&ajax_request=edit_discount"),
               type: 'text',
               pk: 1,
               name: 'discunts',
               title: 'Enter number'
           });
       });
   </script>


<?php
$tpl->footer();