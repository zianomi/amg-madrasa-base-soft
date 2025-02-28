<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
Tools::getLib("TemplateForm");
$tpf = new TemplateForm();
Tools::getModel("StaffModel");
$staff = new StaffModel();
Tools::getModel("SmsModel");
$smsModel = new SmsModel();
$group = (isset($_GET['group'])) ? $tool->GetExplodedInt($_GET['group']) : '';


if(isset($_POST['_chk'])==1) {


    $group = !empty($_POST['group']) ? $tool->GetInt($_POST['group']) : '';

    if (empty($group)) {
        $errors[] = $tool->Message("alert", "group_required");
    }


    $string = $_POST['sms_text'];
    $msg = '';
    foreach($_POST['smsall'] as $key){

        $search = array('{name}');
        $replace = array($_POST['name'][$key]);
        $sms = str_replace($search, $replace, $string);
        $numer = $_POST['fone'][$key] . ',';
        $smsModel->SendSMS($_POST['fone'][$key],$sms);
        $msg .= $sms . " sent to <br />" . $_POST['fone'][$key] .  " "  . "<br />";


    }


    $_SESSION['msg'] =  $tool->Message("succ",$msg);

    $tool->Redir("staff","sendgroupsms","","");




}


$tpl->renderBeforeContent();
$qr->searchContentAbove();


?>
    <div class="row-fluid" id="student_res"></div>



    <div class="row-fluid">
        <div class="span3"><label class="fonts"><?php $tool->trans("group") ?></label>
            <select name="group" id="group">
                <?php echo $tpl->GetOptionVals(array("data" => $set->getTitleTable("sms_groups"), "sel" => $group)); ?>
            </select>
        </div>

        <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
        <div class="span3"><label>&nbsp;</label>&nbsp;</div>
        <div class="span3"><label>&nbsp;</label>&nbsp;</div>
    </div>

<?php
$qr->searchContentBottom();

if (isset($_GET['_chk']) == 1) {




    if (empty($group)) {
        echo $tool->Message("alert", $tool->transnoecho("group_required"));
        exit;
    }
    $res = $staff->staffNumbers($group);


}
?>

    <div id="printReady">
        <?php
        if (isset($_GET['_chk']) == 1) {





        if (count($res) == 0) {
            echo $tool->Message("alert", $tool->transnoecho("no_number_found"));
            return;
        }


        ?>

        <form method="post">

            <input type="hidden" name="group" value="<?php echo $group ?>">


            <?php echo $tpl->FormHidden(); ?>



            <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
                <table class="table table-bordered table-striped table-hover flip-scroll">
                    <thead>
                    <tr>

                        <th class="fonts"><?php $tool->trans("s#") ?></th>
                        <th><input type="checkbox" onclick="checkAll(this)"></th>
                        <th class="fonts"><?php $tool->trans("name") ?></th>
                        <th class="fonts"><?php $tool->trans("number") ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    foreach ($res as $row) { ?>
                        <tr>

                            <td class="avatar"><?php echo $row['id']; ?></td>
                            <td class="avatar">
                                <input type="checkbox" name="smsall[<?php echo $row['id']; ?>]" id="smsall" checked="checked" value="<?php echo $row['id']; ?>"/>
                            </td>
                            <td class="fonts"><?php echo $row['title']; ?></td>
                            <td class="fonts"><?php echo $row['fone']; ?></td>
                            <input type="hidden" name="fone[<?php echo $row['id']; ?>]" value="<?php echo $row['fone'] ?>">
                            <input type="hidden" name="name[<?php echo $row['id']; ?>]" value="<?php echo $row['title']; ?>">

                        </tr>
                    <?php } ?>
                    </tbody>

                    <tr>

                        <td colspan="4">
                            <textarea name="sms_text" id="sms_text" style="width: 95%"><?php $tool->trans("dear") ?> {name} </textarea></td>
                    </tr>

                    <tr class="txtcenter">
                        <td colspan="4" class="txtcenter">
                            <button type="submit" class="btn txtcenter"><?php $tool->trans("send") ?></button>
                        </td>
                    </tr>
                </table>
            </div>
            <?php }
            $tpl->formClose();
            ?>
    </div>

<?php

$tpl->footer();
