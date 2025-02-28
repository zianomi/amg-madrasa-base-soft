<?php
$errors = array();
Tools::getModel("AttendanceModel");
$atd = new AttendanceModel();
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$qr->setFormMethod("post");
if(isset($_POST['_chk'])== 1){

    $vals = array();
        if(empty($_POST['date'])){
            $errors[] = $tool->Message("alert","Please Insert Date.");
        }

        $date = $tool->ChangeDateFormat($_POST['date']);

        $branch = $tool->GetInt($_POST['branch']);
        $attand = $tool->GetInt($_POST['attand']);
        $class = $tool->GetInt($_POST['class']);
        $section = $tool->GetInt($_POST['section']);
        $session = $tool->GetInt($_POST['session']);
        $id = $tool->GetInt($_POST['student_id']);

        if(!$tool->checkDateFormat($date)){
            $errors[] = $tool->Message("alert","Invalid Date.");
        }


        if(empty($branch) || empty($class) || empty($section) || empty($session) || empty($id) || empty($date)){
          $errors[] = $tool->Message("alert","All fields required.");
        }



if(count($errors) == 0){
    $a= 0;


        $data['student_id'] = $id;
        $data['branch_id'] = $branch;
        $data['class_id'] = $class;
        $data['section_id'] = $section;
        $data['session_id'] = $session;
        $data['date'] = $date;
        $data['attand'] = $attand;


        $res = $atd->insertIdAttand($data);
        if($res){
            $_SESSION['msg'] = $tool->Message("succ",$tool->transnoecho("inserted"));
            $tool->Redir("attendance","insertid",$_POST['code'],$_POST['action']);
            exit;
        }
        else{
            echo $tool->Message("alert",$res["msg"]);
        }

    }

}


$tpl->renderBeforeContent();



$qr->searchContentAbove();

?>
    <div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("id_name")?></label><input value="" type="text" name="student_id" id="student_id"></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("Attand")?></label><select name="attand" id="attand" class="input-block-level">

                <?php
                echo $atd->attandPaaram();
                ?>
                        </select></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("date") ?></label><?php echo $tpl->getDateInput(); ?></div>
    <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>

</div>



<?php
$qr->searchContentBottom();

$tpl->footer();
