<?php
//phpinfo();
//exit;
//composer update --ignore-platform-reqs
Tools::getLib("QueryTemplate");
Tools::getLib("TemplateForm");
Tools::getModel("ExamModel");
$tpf = new TemplateForm();
$qr = new QueryTemplate();
$exm = new ExamModel();
$tpl->setCanExport(false);
$tpl->setCanPrint(false);
$qr->setFileUpload(true);
$qr->setFormMethod("post");
$errors = array();
$vals = array();

if (isset($_POST['_chk']) == 1) {

    $inc = 0;


    $branch = (isset($_POST['branch'])) ? $tool->GetExplodedInt($_POST['branch']) : '';
    $class = (isset($_POST['class'])) ? $tool->GetExplodedInt($_POST['class']) : '';
    $section = (isset($_POST['section'])) ? $tool->GetExplodedInt($_POST['section']) : '';
    $session = (isset($_POST['session'])) ? $tool->GetExplodedInt($_POST['session']) : '';
    $exam = (isset($_POST['exam_name'])) ? $tool->GetExplodedInt($_POST['exam_name']) : '';

    $param = array(
        "branch" => $branch,
        "class" => $class,
        "section" => $section,
        "session" => $session
    );

    $resDateLogArr = $exm->examDateLogs($param);
    $resDateLog = $resDateLogArr[0];
    $datelog_id = $resDateLog['id'];
    $date = $resDateLog['exam_start_date'];


    $subjectNumbers = $exm->examSubjectsByClassBranch($session, $exam, $branch, $class);


    if (empty($exam)) {
        $errors[] = $tool->Message("alert", "exam_required");
    }

    if (empty($branch)) {
        $errors[] = $tool->Message("alert", "branch_required");
    }

    if (empty($class)) {
        $errors[] = $tool->Message("alert", "class_required");
    }

    if (empty($section)) {
        $errors[] = $tool->Message("alert", "section_required");
    }

    if (empty($session)) {
        $errors[] = $tool->Message("alert", "session_required");
    }


    if (!$tool->checkDateFormat($date)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("date_invalid"));
    }

    $cacheDir = $tool->getCacheDir();
    define("DIR_SEP", DIRECTORY_SEPARATOR);
    $dir = $cacheDir . DIR_SEP . "csvs" . DIR_SEP . "exams" . date("Y") . DIR_SEP;
    $uploadfile = $dir . "exam_results_" . $branch . $class . $section . "_" . basename($_FILES['file']['name']);
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }

    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
        Tools::getLib("Parsecsv");
        $csv = new parseCSV();
        $csv->auto($uploadfile);

        $idData = array();
        $Subvals = array();
        $subKeys = array();
        $dataStart = array();
        $data = array();

        foreach ($csv->titles as $value):
            $subKeys[] = $value;
        endforeach;


        foreach ($csv->data as $key => $row):
            $id = $row['ID'];
            for ($i = 0; $i < count($subKeys); $i++) {
                if (empty($id)) {
                    continue;
                }

                if (!is_numeric($id)) {
                    continue;
                }

                $subkey = $subKeys[$i];
                $subVal = $row[$subKeys[$i]];

                if ($subkey == 'ID') {
                    continue;
                }

                if (empty($subVal)) {
                    $subVal = 0;
                }


                $subVal = filter_var($subVal, FILTER_VALIDATE_FLOAT);

                //$vals[] = "(NULL, '$id', '$branch', '$class', '$subkey', '$subVal', '$exam', '$date', '$sessionId', '$AdminId', '$created')";
                $vals[] = $tool->setInsertDefaultValues(array($id, $branch, $class, $section, $session, $exam, $subkey, $subjectNumbers[$subkey], $subVal, "$date"));
            }
        endforeach;
    }
    else {
        $upload_error = $_FILES['file']['error'];
        switch ($upload_error) {
            case UPLOAD_ERR_INI_SIZE:
                echo "The uploaded file exceeds the `upload_max_filesize` directive in `php.ini`.";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                echo "The uploaded file exceeds the `MAX_FILE_SIZE` directive that was specified in the HTML form.";
                break;
            case UPLOAD_ERR_PARTIAL:
                echo "The uploaded file was only partially uploaded.";
                break;
            case UPLOAD_ERR_NO_FILE:
                echo "No file was uploaded.";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                echo "Missing a temporary folder.";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                echo "Failed to write file to disk.";
                break;
            case UPLOAD_ERR_EXTENSION:
                echo "A PHP extension stopped the file upload.";
                break;
            default:
                echo "Unknown error occurred during file upload.";
                break;
        }
    }


    $where['branch_id'] = $branch;
    $where['class_id'] = $class;
    $where['section_id'] = $section;
    $where['session_id'] = $session;
    $where['exam_id'] = $exam;

    if (count($errors) == 0) {

        if (
            !empty($branch)
            && !empty($class)
            && !empty($section)
            && !empty($session)
            && !empty($exam)
        ) {


            $exm->deleteExamData($where);
            //echo '<pre>'; print_r($where); echo '</pre>';
        }

        $res = $exm->insertClassAllNumbers($vals);

        if ($res["status"]) {
            $_SESSION['msg'] = $res['msg'];
            $tool->Redir("exam", "insertbycsv", "", "list");
            exit;
        } else {
            echo $tool->Message("alert", $res["msg"]);
        }
    }
}



$tpl->renderBeforeContent();
$tool->displayErrorArray($errors);
$qr->searchContentAbove();
$tpl->setCanExport(false);
$tpl->setCanPrint(false);
$qr->setFormMethod("post");
$qr->setFileUpload(true);
?>

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
            <?php $tool->trans("exam_name") ?>
        </label>
        <?php echo $tpl->examDropDown($exm->getExamNames()); ?>
    </div>

    <div class="span3"><label class="fonts">
            <?php $tool->trans("result_file") ?>
        </label><input type="file" name="file" id="file" required="required"></div>

    <div class="span3"><label>&nbsp;</label>
        <input type="submit" class="btn">
    </div>

    <div class="span3"><label>&nbsp;</label></div>



</div>
<div class="row-fluid">
    <div class="span12">&nbsp;</div>
</div>
<div class="row-fluid">
    <div class="span8" style="text-align: start;">
        <ul>
            <li><strong>Be carefull with class and session selection</strong></li>
            <li><strong>Format CSV before upload.</strong></li>
            <li><strong>Double check profile in case of any error, see video on dashboard.</strong></li>
        </ul>

    </div>
</div>



<?php
$qr->searchContentBottom();

$tpl->footer();
unset($tpf);
unset($atd);
unset($tpf);
