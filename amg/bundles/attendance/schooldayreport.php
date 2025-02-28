<?php
Tools::getModel("AttendanceModel");
$atd = new AttendanceModel();

if(isset($_GET['del'])==1){
    if(isset($_GET['id'])){
        if(is_numeric($_GET['id'])){
            $id = $_GET['id'];
            $atd->deleteDay($id);
            $link = (urldecode($_GET['link']));
            if(empty($link)){
                Tools::Redir("attendance","schooldayreport","","");
            }
            else{
                header("Location:" . $link);
            }
            exit();
        }
    }
}

Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();

$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$date = ((isset($_GET['date'])) && (!empty($_GET['date']))) ? $tool->ChangeDateFormat($_GET['date']) : "";
$to_date = ((isset($_GET['to_date'])) && (!empty($_GET['to_date']))) ? $tool->ChangeDateFormat($_GET['to_date']) : "";

if(isset($_GET['_chk'])==1){



    $param = array("branch" => $branch, "class" => $class, "session" => $session, "date" => $date, "to_date" => $to_date);

    $tableCols = array();

    $tableCols["branch_title"] = $tool->transnoecho("branch_title");
    $tableCols["class_title"] = $tool->transnoecho("class_title");
    //$tableCols["gender"] = $tool->transnoecho("gender");
    $tableCols["session_title"] = $tool->transnoecho("session_title");
    $tableCols["date"] = $tool->transnoecho("date");
    //$tableCols["attand"] = $tool->transnoecho("attand");





    $qr->setCols($tableCols);

    $atdData = $atd->dayLogsReport($param);

    $link = "";
    if(!empty($_GET['branch'])){
        $link .= "&branch=" . $_GET['branch'];
    }
    if(!empty($_GET['class'])){
        $link .= "&class=" . $_GET['class'];
    }
    if(!empty($_GET['session'])){
        $link .= "&session=" . $_GET['session'];
    }
    if(!empty($_GET['date'])){
        $link .= "&date=" . $_GET['date'];
    }
    if(!empty($_GET['to_date'])){
        $link .= "&to_date=" . $_GET['to_date'];
    }
    $link .= "&_chk=1";

    $qr->setData($atdData);

    $curPageUrl = urlencode($_SERVER['REQUEST_URI']);

    $qr->setAction(true);
    $qr->setCustomActions(array
        (
            array("label" => '<i class="icon-remove"></i>',"link" => Tools::makeLink("attendance","schooldayreport&del=1&link=".$curPageUrl,"",""))
        )
    );
    $qr->setDynamicParam(array("id" => "id"));

    if(isset($_GET['export_csv'])==1){
        $qr->exportData();
    }


    //StudentdSearchWithProfile
}

$tpl->renderBeforeContent();
$qr->searchContentAbove();



?>
<div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("class") ?></label><?php echo $tpl->getClasses() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("date")?></label><?php echo $tpl->getDateInput() ?></div>
</div>


<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("to_date")?></label><?php echo $tpl->getToDateInput() ?></div>
    <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>
    <div class="span3">&nbsp;</div>
    <div class="span3">&nbsp;</div>
</div>

<?php
$qr->searchContentBottom();


if(isset($_GET['_chk'])==1){

    if(empty($branch) || empty($class) || empty($date) || empty($to_date)){
        echo $tool->Message("alert",$tool->transnoecho("please_select_branch_and_class"));
        exit;
    }

    if(!empty($date)){
        if(!$tool->checkDateFormat($date)){
            $errors[] = $tool->Message("alert","invalid_from_date.");
            exit;
        }
    }

    if(!empty($to_date)) {
        if (!$tool->checkDateFormat($to_date)) {
            $errors[] = $tool->Message("alert", "invalid_to_date.");
            exit;
        }
    }


    $qr->contentHtml();
}

$tpl->footer();