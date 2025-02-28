<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();

$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$date = ((isset($_GET['date'])) && (!empty($_GET['date']))) ? $tool->ChangeDateFormat($_GET['date']) : "";
$to_date = ((isset($_GET['to_date'])) && (!empty($_GET['to_date']))) ? $tool->ChangeDateFormat($_GET['to_date']) : "";

if (isset($_GET['_chk']) == 1) {



    $param = array("branch" => $branch, "class" => $class, "section" => $section, "session" => $session, "date" => $date, "to_date" => $to_date);

    $tableCols = array();

    $tableCols["id"] = $tool->transnoecho("id");
    $tableCols["name"] = $tool->transnoecho("name");
    //$tableCols["gender"] = $tool->transnoecho("gender");
    $tableCols["fname"] = $tool->transnoecho("father_name");
    $tableCols["class_title"] = $tool->transnoecho("class_title");
    $tableCols["section_title"] = $tool->transnoecho("section_title");
    $tableCols["date"] = $tool->transnoecho("date");
    $tableCols["attand"] = $tool->transnoecho("attand");


    Tools::getModel("AttendanceModel");
    $atd = new AttendanceModel();


    $qr->setCols($tableCols);

    $atdData = $atd->classAttandReport($param);


    $qr->setData($atdData);





    function atdMethod($value)
    {
        global $atd;
        return $atd->ReturnAtdName($value);
    }


    $qr->formatFieldWithFunction("attand", "atdMethod");


    if (isset($_GET['export_csv']) == 1) {
        $qr->exportData();
    }


    //StudentdSearchWithProfile
}

$tpl->renderBeforeContent();
$qr->searchContentAbove();



?>
<div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span3"><label class="fonts">
            <?php $tool->trans("session") ?>
        </label>
        <?php echo $tpl->getAllSession() ?>
    </div>
    <div class="span3"><label class="fonts">
            <?php $tool->trans("branch") ?>
        </label>
        <?php echo $tpl->userBranches() ?>
    </div>
    <div class="span3"><label class="fonts">
            <?php $tool->trans("class") ?>
        </label>
        <?php echo $tpl->getClasses() ?>
    </div>
    <div class="span3"><label class="fonts">
            <?php $tool->trans("section") ?>
        </label>
        <?php echo $tpl->getSecsions() ?>
    </div>
</div>


<div class="row-fluid">
    <div class="span3"><label class="fonts">
            <?php $tool->trans("date") ?>
        </label>
        <?php echo $tpl->getDateInput() ?>
    </div>
    <div class="span3"><label class="fonts">
            <?php $tool->trans("to_date") ?>
        </label>
        <?php echo $tpl->getToDateInput() ?>
    </div>
    <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>
    <div class="span3">&nbsp;</div>
</div>

<?php
$qr->searchContentBottom();


if (!empty($branch)) {
    echo '<div class="body">';
    $msg = $tool->GetExplodedVar($_GET['branch']);

    $msg .= " " . $_GET['date'] . " - " . $_GET['to_date'];
    echo $tool->MessageOnly("succ", $msg);
    echo '</div>';
}

if (isset($_GET['_chk']) == 1) {

    if (empty($branch) || empty($session) || empty($session) || empty($date) || empty($to_date)) {
        echo $tool->Message("alert", $tool->transnoecho("please_select_session_branch_and_dates"));
        exit;
    }

    if (!$tool->checkDateFormat($date)) {
        $errors[] = $tool->Message("alert", "invalid_from_date.");
        exit;
    }

    if (!$tool->checkDateFormat($to_date)) {
        $errors[] = $tool->Message("alert", "invalid_to_date.");
        exit;
    }


    $qr->contentHtml();
}

$tpl->footer();