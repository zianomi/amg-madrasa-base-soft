<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$columns = $set->getProfileCols();
$set = new SettingModel();
$profileCols = array();
$sel = "";
$errors = array();

if(isset($_GET['_chk'])==1){
    $branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
    $class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
    $section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
    $session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
    $fields =  isset($_GET['profile_cols']) ? $set->filter($_GET['profile_cols']) : "";
    $admission_date =  isset($_GET['admission_date']) ? $tool->ChangeDateFormat($_GET['admission_date']) : "";
    $key_word = isset($_GET['key_word']) ? $set->filter($_GET['key_word']) : "";



    $param = array("branch" => $branch, "class" => $class, "section" => $section, "session" => $session, "key_word"=> $key_word);


    $tableCols = array();


    $tableCols["student_id"] = $tool->transnoecho("id");
    $tableCols["name"] = $tool->transnoecho("name");
    $tableCols["fname"] = $tool->transnoecho("father_name");
    $tableCols["branch_title"] = $tool->transnoecho("branch");
    $tableCols["class_title"] = $tool->transnoecho("class");
    $tableCols["section_title"] = $tool->transnoecho("section");


    Tools::getModel("StudentsModel");
   $stu = new StudentsModel();



    $colArr = array();
    foreach($columns as $column){
        $colArr[$column['id']] = $column['title'];
    }

    if(!empty($_GET['profile_cols'])){
        $keyArr = explode("-",$_GET['profile_cols']);
        $keys = $keyArr[0];
        $profileCols[$keys] = $keys;
        $tableCols[$keys] = $colArr[$keys];
        $param["fields"] = $keys;
        $sel = $keys;
    }

    $param['admission_date'] = $admission_date;

    if(empty($branch) || empty($session)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("please_select_session_branch"));
    }

    if(count($errors)==0){
        $qr->setCols($tableCols);
        $qr->setData($stu->StudentdCustomSearch($param,$profileCols));
    }


    if(isset($_GET['export_csv'])==1){
        if(count($errors)==0){
            $qr->exportData();
        }
    }
}

$tpl->renderBeforeContent();
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
    <div class="span3"><label class="fonts"><?php $tool->trans("search_word")?></label><input value="<?php if(isset($_GET['key_word'])) echo $_GET['key_word']?>" type="text" name="key_word" id="key_word"></div>
    <div class="span3">
        <label class="fonts"><?php $tool->trans("fields_required")?></label>
        <?php
      echo $tpl->GetOptions(array("name" => "profile_cols", "data" => $columns, "sel" => $sel));
      ?></div>
    <div class="span3"><label><?php $tool->trans("date_admission") ?></label>
    <input type="text" class="datepicker" name="admission_date"></div>
    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>

</div>

<?php
$qr->searchContentBottom();


if(isset($_GET['_chk'])==1){
    if(count($errors)==0){
        $qr->contentHtml();
    }

}

$tpl->footer();
