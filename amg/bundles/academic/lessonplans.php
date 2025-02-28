<?php
/* @var Tools $tool */
/* @var Template $tpl */
Tools::getModel("AcademicModel");
Tools::getModel("ExamModel");
Tools::getLib("QueryTemplate");
Tools::getLib("TemplateForm");
Tools::getLib("AmgSpace");
$tpf = new TemplateForm();
$qr = new QueryTemplate();
$acd = new AcademicModel();
$exm = new ExamModel();
$amgSpace = new AmgSpace();
$tpl->setCanExport(false);
$tpl->setCanPrint(false);

$errors = array();
$vals = array();


$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$exam = (isset($_GET['exam_name'])) ? $tool->GetExplodedInt($_GET['exam_name']) : '';
$subject = (isset($_GET['subject'])) ? $tool->GetExplodedInt($_GET['subject']) : '';
$date = !empty($_GET['date']) ? ($_GET['date']) : "";

if (isset($_GET['del']) == 1) {
    if (isset($_GET['id'])) {
        if (is_numeric($_GET['id'])) {
            $id = $_GET['id'];


            $planRow = $exm->getPlanRow($id);

            $link = $planRow['pdf_link'];

            if(!empty($link)){
                $filePath = $amgSpace->getFileUrl($link);
                $amgSpace->deleteDir($filePath);
            }

            $acd->removeLessonPlan($id);
            $url = isset($_GET['redir']) ? urldecode($_GET['redir']) : "";
            header("Location:" . $url);
            exit;
        }
    }
}


if (isset($_POST['_chk']) == 1) {


    $inc = 0;

    $chapter = !empty($_POST['chapter']) ? $acd->filter($_POST['chapter']) : '';
    $topic = !empty($_POST['topic']) ? $acd->filter($_POST['topic']) : '';
    $subtopic = !empty($_POST['subtopic']) ? $acd->filter($_POST['subtopic']) : '';
    $week = !empty($_POST['week']) ? $tool->GetInt($_POST['week']) : '';
    $branch = !empty($_POST['branch']) ? $tool->GetInt($_POST['branch']) : '';
    $class = !empty($_POST['class']) ? $tool->GetInt($_POST['class']) : '';
    $session = !empty($_POST['session']) ? $tool->GetInt($_POST['session']) : '';
    $exam = !empty($_POST['exam']) ? $tool->GetInt($_POST['exam']) : '';
    $subject_post = !empty($_POST['subject_post']) ? $tool->GetInt($_POST['subject_post']) : '';
    if (empty($exam)) {
        $errors[] = $tool->Message("alert", "exam_required");
    }

    if (empty($branch)) {
        $errors[] = $tool->Message("alert", "branch_required");
    }

    if (empty($class)) {
        $errors[] = $tool->Message("alert", "class_required");
    }

    if (empty($session)) {
        $errors[] = $tool->Message("alert", "session_required");
    }

    if (empty($chapter)) {
        $errors[] = $tool->Message("alert", "chapter_required");
    }

    if(empty($_FILES['pdf']['name'])){
        $errors[] = Tools::transnoecho("please_select_pdf_file");
    }

    $amgSpace->setFileInputName('pdf');

    if(!empty($_FILES['pdf']['name'])){
        $fileArr = $amgSpace->upload();
        $newAmgFileName = $fileArr['value'];
        $vals['pdf_link'] = $newAmgFileName;
    }

    $url = isset($_POST['redir']) ? urldecode($_POST['redir']) : "";

    $vals['branch_id'] = $branch;
    $vals['session_id'] = $session;
    $vals['subject_id'] = $subject;
    $vals['exam_id'] = $exam;
    $vals['week_id'] = $week;
    $vals['chapter'] = $chapter;
    $vals['topic'] = $topic;
    $vals['subtopic'] = $subtopic;



    //echo '<pre>';
    //print_r($_POST);
    //print_r($errors);
    //echo '</pre>';
    //exit;






    if (count($errors) == 0) {

        if ($acd->insertSessionLesson($vals)) {
            $_SESSION['msg'] = $tool->Message("succ", $tool->transnoecho("record_inserted"));
            if (!empty($url)) {
                header("Location:" . $url);
            } else {
                $tool->Redir("academic", "lessonplans", "", "list");
            }

            exit;
        }

    }



}



$tpl->renderBeforeContent();
$tool->displayErrorArray($errors);
$qr->searchContentAbove();
?>

<input type="hidden" name="date" id="date">
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
            <?php $tool->trans("exam_name") ?>
        </label>
        <?php echo $tpl->examDropDown($exm->getExamNames()); ?>
    </div>
</div>

<div class="row-fluid">
    <div class="span3">
        <label class="fonts">
            <?php $tool->trans("subject") ?>
        </label>
        <select name="subject" id="subject">
            <option value=""></option>
        </select>
    </div>
    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>



</div>


<?php
$qr->searchContentBottom();
?>
<div class="body">

    <div id="printReady">
        <?php
        if (isset($_GET['_chk']) == 1) {

            if (empty($branch) || empty($class) || empty($session) || empty($subject)) {
                echo $tool->Message("alert", $tool->transnoecho("please_select_all_fields"));
                exit;
            }

            $param["branch"] = $branch;
            $param["session"] = $session;
            $param["class"] = $class;
            $param["subject"] = $subject;





            $res = $acd->getLessonPlans($param);








            ?>


            <div class="row-fluid">
                <div class="span12">
                    <?php echo $tpl->branchBreadCrumbs() ?>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <h2>
                        <?php
                        if (isset($_GET['subject'])) {
                            if (!empty($_GET['subject'])) {
                                echo $tool->GetExplodedVar($_GET['subject']);
                            }
                        }

                        if (isset($_GET['exam_name'])) {
                            if (!empty($_GET['exam_name'])) {
                                echo " - " . $tool->GetExplodedVar($_GET['exam_name']);
                            }
                        }
                        ?>
                    </h2>
                </div>

            </div>


            <div class="row-fluid">
                <div class="span3">
                    <form method="post" enctype="multipart/form-data">
                        <?php echo $tpl->FormHidden(); ?>

                        <input type="hidden" name="branch" value="<?php echo $branch ?>" />
                        <input type="hidden" name="exam" value="<?php echo $exam ?>" />
                        <input type="hidden" name="class" value="<?php echo $class ?>" />
                        <input type="hidden" name="session" value="<?php echo $session ?>">
                        <input type="hidden" name="subject_post" value="<?php echo $subject ?>">
                        <input type="hidden" name="redir" value="<?php echo urlencode($_SERVER['REQUEST_URI']) ?>">

                        <table>
                            <tr>
                                <td>
                                    <?php $tool->trans("chapter"); ?>
                                    <label>
                                        <input type="text" name="chapter">
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php $tool->trans("topic"); ?>
                                    <label>
                                        <input type="text" name="topic">
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php $tool->trans("subtopic"); ?>
                                    <label>
                                        <input type="text" name="subtopic">
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php $tool->trans("week"); ?>
                                    <label>
                                        <select name="week">
                                            <option value=""></option>
                                            <?php foreach ($acd->getSessionWeeks() as $k => $v) { ?>
                                                <option value="<?php echo $k ?>">
                                                    <?php echo $v ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="fonts"><?php Tools::trans("pdf") ?></label>
                                    <input type="file" name="pdf" value="">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="submit" class="btn btn-primary" value="<?php $tool->trans("save"); ?>">
                                </td>
                            </tr>
                        </table>

                    </form>
                </div>
                <div class="span9">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="fonts">
                                    <?php $tool->trans("exam") ?>
                                </th>
                                <th class="fonts">
                                    <?php $tool->trans("chapter") ?>
                                </th>
                                <th class="fonts">
                                    <?php $tool->trans("topic") ?>
                                </th>
                                <th class="fonts">
                                    <?php $tool->trans("subtopic") ?>
                                </th>
                                <th class="fonts">
                                    <?php $tool->trans("week") ?>
                                </th>
                                <th class="fonts">
                                    <?php $tool->trans("action") ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            $curPageUrl = urlencode($_SERVER['REQUEST_URI']);


                            foreach ($res as $row) {

                                $commonParam = "&_chk=1";
                                $commonParam .= "&redir=" . $curPageUrl;

                                $queryStringDel = $commonParam;
                                $queryStringDel .= "&del=1";
                                $queryStringDel .= "&id=" . $row['id'];

                                $linkDel = Tools::makeLink("academic", "lessonplans" . $queryStringDel, "", "");
                                ?>
                                <tr>

                                    <td class="fonts">
                                        <?php echo $row['exam_title']; ?>
                                    </td>

                                    <td class="fonts">
                                        <?php echo $row['chapter']; ?>
                                    </td>
                                    <td class="fonts">
                                        <?php echo $row['topic']; ?>
                                    </td>
                                    <td class="fonts">
                                        <?php echo $row['subtopic']; ?>
                                    </td>
                                    <td class="fonts">
                                        <?php echo $acd->getSessionWeek($row['week_id']); ?>
                                    </td>
                                    <td class="fonts">
                                        <a title="PDF" href="<?php echo $amgSpace->getCdnFileUrl( $row['pdf_link'] ) ?>" target="_blank"><i class="icon-book"></i></a>
                                        &nbsp;
                                        <a href="<?php echo $linkDel ?>"><i class="icon-remove"></i></a></td>



                                </tr>
                            <?php } ?>
                        </tbody>


                    </table>

                </div>
            </div>





















        <?php }

        ?>
    </div>

</div>
<script>
    var sessionObj = $("#session");
    var branchObj = $("#branch");
    var classObj = $("#class");

    function callRequest() {
        var session = sessionObj.val();
        var branch = branchObj.val();
        var Class = classObj.val();
        var datastring = $("#amg_form").serialize();
        var saveProgress = makeJsLink("ajax", "exam&ajax_request=show_parent");

        if (session !== "" && branch !== "" && Class !== "") {


            $.ajax({
                type: "POST",
                url: saveProgress,
                data: datastring,
                dataType: "json",
                success: function (data) {
                    if (data.status == 1) {
                        $('#subject').html(data.msg);
                        $('#date').val(data.date);

                    }
                    else {
                        alert(data.msg);
                    }
                },
                error: function () {
                    alert('error handing here');
                }
            });
        }

    }

    sessionObj.change(function () {
        callRequest();
    });

    branchObj.change(function () {
        callRequest();
    });

    classObj.change(function () {
        callRequest();
    });

</script>
<?php
$tpl->footer();
unset($tpf);
unset($atd);
unset($tpf);
