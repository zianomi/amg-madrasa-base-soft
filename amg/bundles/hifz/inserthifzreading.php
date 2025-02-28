<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
Tools::getLib("TemplateForm");
$tpf = new TemplateForm();
Tools::getModel("HifzModel");
$hfz = new HifzModel();
$errors = array();
$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$hifz_year = (isset($_GET['hifz_year'])) ? $tool->GetExplodedInt($_GET['hifz_year']) : '';
$year = (isset($_GET['year'])) ? $tool->GetInt($_GET['year']) : '';
$month = (isset($_GET['month'])) ? $tool->GetInt($_GET['month']) : '';


if(isset($_POST['_chk'])==1) {


    $inc = 0;

    $branch = !empty($_POST['branch']) ? $tool->GetInt($_POST['branch']) : '';
    $class = !empty($_POST['class']) ? $tool->GetInt($_POST['class']) : '';
    $section = !empty($_POST['section']) ? $tool->GetInt($_POST['section']) : '';
    $session = !empty($_POST['session']) ? $tool->GetInt($_POST['session']) : '';
    $hifz_year = !empty($_POST['hifz_year']) ? $tool->GetInt($_POST['hifz_year']) : '';


    $date = !empty($_POST['date']) ? $_POST['date'] : '';


    if(empty($branch)){
        $errors[] = $tool->Message("alert","branch_required");
    }

    if(empty($class)){
        $errors[] = $tool->Message("alert","class_required");
    }

    if(empty($section)){
        $errors[] = $tool->Message("alert","section_required");
    }

    if(empty($session)){
        $errors[] = $tool->Message("alert","session_required");
    }

    if(empty($hifz_year)){
        $errors[] = $tool->Message("alert","hifz_year_required");
    }

    if(!$tool->checkDateFormat($date)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("date_invalid"));
    }

    foreach($_POST['para'] as $key => $val){
        $pageNumber = $_POST['page_number'][$key];
        $lineNumber = $_POST['line_number'][$key];
        $para = $_POST['para'][$key];

        if(empty($pageNumber)){
            $errors[] = $tool->Message("alert","Page Number " . " " . $key . " required.");
        }

        if(empty($lineNumber)){
            $errors[] = $tool->Message("alert", "Line Number " . $key . " required.");
        }

        if(empty($para)){
            $errors[] = $tool->Message("alert",  "Para " . $key . " required.");
        }

        if(!empty($para) && !empty($pageNumber) && !empty($lineNumber)){
            $vals[] = $tool->setInsertDefaultValues(array("NULL",$branch,$class,$section,$session,$key,$hifz_year,$val,"$pageNumber","$lineNumber","$date"));
        }

    }




    if(count($errors)==0){

        $res = $hfz->insertStuData($vals);


        if($res["status"]){
            $_SESSION['msg'] = $res['msg'];
            $tool->Redir("hifz","inserthifzreading","","list");
            exit;
        }
        else{
            echo $tool->Message("alert",$res["msg"]);
        }

    }



}


$tpl->renderBeforeContent();

$tool->displayErrorArray($errors);

$qr->searchContentAbove();




?>
<div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("class") ?></label><?php echo $tpl->getClasses() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("section") ?></label><?php echo $tpl->getSecsions() ?></div>
</div>


<div class="row-fluid">
    <div class="span3">
        <label class="fonts"><?php $tool->trans("year")?></label>
        <select name="year" id="year">
          <?php echo $tpf->NewYearsDropDown(); ?>
        </select></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("month")?></label><select name="month" id="month">
      <?php echo $tpf->NewMonthDropDown(); ?>
    </select></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("hifz_year")?></label>
        <select name="hifz_year" id="hifz_year">
            <?php echo $tpl->GetOptionVals(array("data" => $set->getTitleTable("hifz_years"), "sel" => $hifz_year)); ?>
        </select>
    </div>

    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
</div>

<?php
$qr->searchContentBottom();

if(isset($_GET['_chk'])==1){

    $date = $tpf->MakeEamDateFromMonthYear($year, $month);


    if(empty($branch)){
        echo  $tool->Message("alert",$tool->transnoecho("branch_required"));
        exit;
    }

    if(empty($class)){
        echo  $tool->Message("alert",$tool->transnoecho("class_required"));
        exit;
    }

    if(empty($section)){
        echo  $tool->Message("alert",$tool->transnoecho("section_required"));
        exit;
    }

    if(empty($session)){
        echo  $tool->Message("alert",$tool->transnoecho("session_required"));
        exit;
    }

    if(empty($hifz_year)){
        echo  $tool->Message("alert",$tool->transnoecho("hifz_year_required"));
        exit;
    }

    if(!$tool->checkDateFormat($date)){
        echo  $tool->Message("alert",$tool->transnoecho("date_invalid"));
        exit;
    }





}
?>

    <div id="printReady">
                    <?php
                    if (isset($_GET['_chk']) == 1) {


                    $param = array(
                    "branch" => $branch
                    ,"class" => $class
                    ,"section" => $section
                    ,"session" => $session
                    ,"date" => $date
                    ,"year" => $hifz_year

                    );

                    $res = $hfz->insertSyllabus($param);


                    if(count($res)==0){
                        echo $tool->Message("alert",$tool->transnoecho("no_students_found"));
                        return;
                    }





                        ?>

                  <form method="post">

                      <input type="hidden" name="date" value="<?php echo $date ?>"/>
                      <input type="hidden" name="branch" value="<?php echo $branch ?>"/>
                      <input type="hidden" name="class" value="<?php echo $class ?>"/>
                      <input type="hidden" name="section" value="<?php echo $section ?>">
                      <input type="hidden" name="session" value="<?php echo $session ?>">
                      <input type="hidden" name="hifz_year" value="<?php echo $hifz_year ?>">


                    <?php echo $tpl->FormHidden();   ?>

                        <h2 class="fonts">

                            <?php


                            if(isset($_GET['branch'])){
                                if(!empty($_GET['branch'])){
                                    echo $tool->GetExplodedVar($_GET['branch']);
                                }
                            }

                            $dataPara = $hfz->getCurrentMonthSyllabus(array("year" => $hifz_year, "class" => $class, "month" => $month));



                            ?>
                            <br>
                        </h2>

                        <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
                            <table class="table table-bordered table-striped table-hover flip-scroll">
                                <thead>
                                <tr>

                                    <th class="fonts"><?php $tool->trans("id")?></th>
                                    <th class="fonts"><?php $tool->trans("name_father_name")?></th>
                                    <th class="fonts"><?php $tool->trans("para")?></th>
                                    <th class="fonts"><?php $tool->trans("page_number")?></th>
                                    <th class="fonts"><?php $tool->trans("line_number")?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                foreach($res as $row) { ?>
                                    <tr>

                                        <td class="avatar"><?php echo $row['id']; ?></td>
                                        <td class="fonts"><?php echo $row['name']; ?>  <?php echo $row['fname']; ?></td>

                                        <td>
                                            <select name="para[<?php echo $row['id']; ?>]">
                                                <option value=""></option>
                                                <?php
                                                foreach($dataPara as $keyPara){
                                                    $sel = "";
                                                    /*if($keyPara['id'] == $rowStandard["quran_id"]){
                                                        $sel = ' selected="selected"';
                                                    }else{
                                                        $sel = "";
                                                    }*/

                                                ?>
                                                <option value="<?php echo $keyPara['id'] ?>"<?php echo $sel ?>><?php echo $keyPara['title'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td class="avatar"><input type="number" value="" name="page_number[<?php echo $row['id']; ?>]"></td>
                                        <td class="avatar"><input type="number" value="" name="line_number[<?php echo $row['id']; ?>]"></td>


                                    </tr>
                                <?php } ?>
                                </tbody>

                                <tr class="txtcenter">
                                    <td colspan="5" style="text-align: center">
                                        <button type="submit" class="btn txtcenter">Save</button>
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
