<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$columns = $set->getProfileCols();
$set = new SettingModel();
$profileCols = array();
$errors = array();

$set->setTrans(array($tool->transnoecho("section"), $tool->transnoecho("class")));

$grNumber = (!empty($_GET['gr_number'])) ? $tool->GetInt($_GET['gr_number']) : '';
$id = (!empty($_GET['student_id'])) ? ($_GET['student_id']) : '';

if (isset($_GET['_chk']) == 1) {
    $param['fields'] = '';


    $branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
    $class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
    $section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
    $session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';

    if (!empty($_GET['prfile_column'])) {
        $all_fields = implode(",", $_GET['prfile_column']);
    } else {
        $all_fields = '';
    }

    if (empty($id) && empty($grNumber)) {
        if (empty($branch) || empty($session)) {
            $errors[] = $tool->Message("alert", $tool->transnoecho("please_select_session_branch"));
        }
    }

    $param['branch'] = $branch;
    $param['class'] = $class;
    $param['section'] = $section;
    $param['session'] = $session;
    $param['id'] = $id;
    $param['gr'] = $grNumber;




    $tableCols = array();

    $tableCols["id"] = $tool->transnoecho("id");
    $tableCols["grnumber"] = $tool->transnoecho("grnumber");
    $tableCols["name"] = $tool->transnoecho("name");
    $tableCols["fname"] = $tool->transnoecho("father_name");
    $tableCols["branch_title"] = $tool->transnoecho("branch");
    $tableCols["class_title"] = $tool->transnoecho("class");
    $tableCols["section_title"] = $tool->transnoecho("section");

    $qr->setShowSerialNumber(true);
    Tools::getModel("StudentsModel");
    $stu = new StudentsModel();
    $qr->setAction(true);
    $qr->setCustomActions(
        array
        (
            array("label" => '<i class="icon-edit"></i>', "link" => Tools::makeLink("students", "admissionfrom", "", ""))
            ,
            array("label" => '<i class="icon-user"></i>', "link" => Tools::makeLink("students", "profile", "", ""))
        )
    );

    $qr->setDynamicParam(array("id" => "id"));



    $colArr = array();
    foreach ($columns as $column) {
        $colArr[$column['id']] = $column['title'];
    }


    if (!empty($_GET['profile_cols'])) {
        foreach ($_GET['profile_cols'] as $key) {
            $keyArr = explode("-", $key);
            $keys = $keyArr[0];
            $profileCols[$keys] = $keys;
            $tableCols[$keys] = $colArr[$keys];
            //@$param['fields'] .= ", `".Tools::getPrefix()."student_profile`.`" . $keys . "`";
            @$param['fields'] .= ", " . $keys;
        }
        @$param['fields'] = str_replace("eng_name", "jb_students.eng_name", $param['fields']);
    }

    if (!empty($param['id'])) {
        $id = $param['id'];
        $param = array();
        $param['id'] = $id;
    }

    if (!empty($param['gr'])) {
        $gr = $param['gr'];
        $param = array();
        $param['gr'] = $gr;
    }

    $qr->setCols($tableCols);
    if (count($errors) == 0) {
        $qr->setData($stu->StudentdSearchWithProfile($param));
        if (isset($_GET['export_csv']) == 1) {
            $qr->exportData();
        }
    }





    //StudentdSearchWithProfile
}

//echo '<pre>';print_r($profileCols );echo '</pre>';die('Call');

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
            <?php $tool->trans("id_name") ?>
        </label><input value="<?php echo $id ?>" type="text" name="student_id" id="student_id"></div>
    <div class="span3"><label class="fonts">
            <?php $tool->trans("grnumber") ?>
        </label><input value="<?php echo $grNumber ?>" type="text" name="gr_number" id="gr_number"></div>
    <div class="span3">
        <label class="fonts">
            <?php $tool->trans("fields_required") ?>
        </label>
        <?php
        echo $tpl->GetMultiOptions(array("name" => "profile_cols[]", "data" => $columns, "sel" => $profileCols));
        ?>
    </div>
    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
    <div class="span3">&nbsp;</div>
</div>

<?php
$qr->searchContentBottom();


if (isset($_GET['_chk']) == 1) {
    if (count($errors) == 0) {
        $qr->contentHtml();
    }

}

?>

<style>
    @media print {

        .table,
        .table tr,
        .table td,
        .table th {
            border-color: black;
        }

        .page-break {
            display: block;
            page-break-before: always;
        }
    }
</style>
<?php
$tpl->footer();