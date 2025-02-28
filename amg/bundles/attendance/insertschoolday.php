<?php
$errors = array();
if(isset($_POST['_chk'])== 1){

    $vals = array();
        if(empty($_POST['date'])){
            $errors[] = $tool->Message("alert","Please Insert Date.");
        }

        $date = $tool->ChangeDateFormat($_POST['date']);

        $branch = $tool->GetExplodedInt($_POST['branch']);
        $session = $tool->GetExplodedInt($_POST['session']);
        if(!$tool->checkDateFormat($date)){
            $errors[] = $tool->Message("alert","Invalid Date.");
        }


        if(empty($branch) || empty($date)){
          $errors[] = $tool->Message("alert","All fields required.");
        }



if(count($errors) == 0){
    $a= 0;

    foreach($_POST['classes'] as $key){
        $class 		= $key;
        $vals[] = $tool->setInsertDefaultValues(array("NULL","$branch","$class","$session","$date"));
    }

        Tools::getModel("AttendanceModel");
        $atd = new AttendanceModel();

        $res = $atd->insertSchoolDay($vals);

        if($res["status"]){
            $_SESSION['msg'] = $res['msg'];
            $tool->Redir("attendance","insertschoolday","","");
            exit;
        }
        else{
            echo $tool->Message("alert",$res["msg"]);
        }
    }

}


if(isset($_GET['_chk'])==1){

    $branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
    $session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';



}

$tpl->renderBeforeContent();


Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$qr->searchContentAbove();
?>
<div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("date") ?></label><?php echo $tpl->getDateInput(); ?></div>
    <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>

</div>



<?php
$qr->searchContentBottom();

if(isset($_GET['_chk']) == 1){

if(empty($branch) || empty($session)){
    echo $tool->Message("alert",$tool->transnoecho("all_fields_required"));
    exit;

}

    echo $tpl->formTag("post");
    echo $tpl->formHidden();
?>
    <input type="hidden" name="date" value="<?php if(isset($_GET['date'])) echo $_GET['date'] ?>">
    <input type="hidden" name="branch" value="<?php echo $branch ?>">
    <input type="hidden" name="session" value="<?php echo $session ?>">
    <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
        <table class="table table-bordered table-striped table-hover flip-scroll">
            <thead>
            <tr>
                <th><input type="checkbox" onclick="checkAll(this)"></th>
                <th>S#</th>
                <th class="fonts"><?php $tool->trans("class") ?></th>
            </tr>
            </thead>


            <tbody>


            <?php

            $set = new SettingModel();
            $classes = $set->sessionClasses($session,$branch);

            $i=0;
            foreach($classes as $class){
                $i++;
            ?>
            <tr>
                <td class="fonts"><input type="checkbox" name="classes[<?php echo $class['id']; ?>]" id="classes" checked="checked" value="<?php echo $class['id']; ?>"/></td>
                <td class="fonts"><?php echo $i; ?></td>
                <td class="fonts"><?php echo $class['title']; ?></td>
            </tr>
            <?php } ?>


            <tr>
              <td colspan="3" style="text-align: center"><button type="submit" class="btn btn-success">
                              <i class="icon-filter"></i>Insert</button></td>
          </tr>
            </tbody>

        </table>
    </div>

<?php }
echo $tpl->formClose();
$tpl->footer();
