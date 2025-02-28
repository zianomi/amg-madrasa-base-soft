<?php
Tools::getModel("AcademicModel");
Tools::getModel("ExamModel");
Tools::getLib("QueryTemplate");
Tools::getLib("TemplateForm");
$tpf = new TemplateForm();
$qr = new QueryTemplate();
$acd = new AcademicModel();
$exm = new ExamModel();
$tpl->setCanExport(false);
$tpl->setCanPrint(false);

$errors = array();
$vals = array();

$lesson = "";

$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$exam = (isset($_GET['exam_name'])) ? $tool->GetExplodedInt($_GET['exam_name']) : '';
//$subject = (isset($_GET['subject'])) ? $tool->GetExplodedInt($_GET['subject']) : '';
$date = !empty($_GET['date']) ? ($_GET['date']) : "";

if (isset($_GET['del']) == 1) {
    if (isset($_GET['id'])) {
        if (is_numeric($_GET['id'])) {
            $id = $_GET['id'];
            $acd->removeClassHomeworks($id);
            $url = isset($_GET['redir']) ? urldecode($_GET['redir']) : "";
            header("Location:" . $url);
            exit;
        }
    }
}


if (isset($_POST['_chk']) == 1) {


    $inc = 0;


    $title = !empty($_POST['title']) ? $set->filter($_POST['title']) : '';
    $desc = !empty($_POST['desc']) ? $set->filter($_POST['desc']) : '';
    $branch = !empty($_POST['branch']) ? $tool->GetInt($_POST['branch']) : '';
    $class = !empty($_POST['class']) ? $tool->GetInt($_POST['class']) : '';
    $session = !empty($_POST['session']) ? $tool->GetInt($_POST['session']) : '';
    $section = !empty($_POST['section']) ? $tool->GetInt($_POST['section']) : '';
    $subjectPost = !empty($_POST['subject_post']) ? $tool->GetInt($_POST['subject_post']) : '';
    $submitDate = !empty($_POST['date']) ? $tool->ChangeDateFormat($_POST['date']) : "";


    if (empty($session)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("session_required"));
    }

    if (empty($branch)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("branch_required"));
    }

    if (empty($class)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("class_required"));
    }

    if (empty($section)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("section_required"));
    }

    if (empty($subjectPost)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("subject_required"));
    }

    if (empty($submitDate)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("submit_date_required"));
    }

    if (empty($title)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("title_required"));
    }

    if (empty($desc)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("description_required"));
    }

    if(!$tool->checkDateFormat($submitDate)){
        $errors[] = $tool->Message("alert", $tool->transnoecho("submit_date_not_valid"));
    }



    $url = isset($_POST['redir']) ? urldecode($_POST['redir']) : "";



    $vals['title'] = $title;
    $vals['session_id'] = $session;
    $vals['branch_id'] = $branch;
    $vals['class_id'] = $class;
    $vals['section_id'] = $section;
    $vals['subject_id'] = $subjectPost;
    $vals['date'] = date("Y-m-d");
    $vals['submit_date'] = $submitDate;
    $vals['description'] = $desc;



    if (count($errors) == 0) {

        if ($acd->insertClassHomeworks($vals)) {
            $_SESSION['msg'] = $tool->Message("succ", $tool->transnoecho("record_inserted"));
            if (!empty($url)) {
                header("Location:" . $url);
            } else {
                $tool->Redir("academic", "homeworks", "", "list");
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

    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>



</div>


<?php
$qr->searchContentBottom();
?>
<div class="body">

    <div id="printReady">
        <?php
        if (isset($_GET['_chk']) == 1) {

            if (empty($branch) || empty($class) || empty($session)) {
                echo $tool->Message("alert", $tool->transnoecho("please_select_all_fields"));
                exit;
            }

            $param["branch"] = $branch;
            $param["session"] = $session;
            $param["class"] = $class;
            //$param["subject"] = $subject;





            //$res = $acd->getLessonPlans($param);
            //$resTeacher = $acd->getTeacherForSubjects($subject);


            //$lessons = array();
            /*foreach ($res as $row) {
                $lessons[] = array("id" => $row['id'], "title" => $row['chapter']);
            }*/







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
                    <form method="post">
                        <?php echo $tpl->FormHidden(); ?>

                        <input type="hidden" name="branch" value="<?php echo $branch ?>" />
                        <input type="hidden" name="exam" value="<?php echo $exam ?>" />
                        <input type="hidden" name="class" value="<?php echo $class ?>" />
                        <input type="hidden" name="session" value="<?php echo $session ?>">
                        <!--<input type="hidden" name="subject_post" value="<?php /*echo $subject */?>">-->
                        <input type="hidden" name="redir" value="<?php echo urlencode($_SERVER['REQUEST_URI']) ?>">

                        <table>
                            <tr>
                                <td>
                                    <label>
                                        <?php $tool->trans("subject"); ?>
                                    </label>
                                    <select name="subject_post" id="subject_post">
                                        <option value="">Please select</option>

                                        <?php
                                        if(!empty($branch) && !empty($class)){
                                            $param = array(
                                                "branch" => $branch,
                                                "class" => $class
                                            );
                                            $resSubs = $exm->getParentSubs($param);
                                            foreach ($resSubs as $row) {
                                                $sel="";
                                                if(isset($_POST['subject_post']) && $_POST['subject_post'] == $row['id']){
                                                    $sel = " selected";
                                                }

                                                ?>
                                                <option value="<?php echo $row['id']?>"<?php echo $sel?>><?php echo $row['title']?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>

                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                        <?php $tool->trans("section"); ?>
                                    </label>
                                    <?php echo $tpl->getSecsions() ?>
                                </td>
                            </tr>
                            <!--<tr>
                                <td>
                                    <label>
                                        <?php /*$tool->trans("lesson"); */?>
                                    </label>
                                    <?php
/*                                    echo $tpl->GetOptions(array("data" => $lessons, "name" => "lessons", "sel" => $lesson));
                                    */?>
                                </td>
                            </tr>-->
                            <tr>
                                <td>
                                    <label>
                                        <?php $tool->trans("title"); ?>
                                    </label>
                                    <input type="text" name="title" class="title" value="<?php if (isset($_POST['title']))
                                        echo $_POST['title'] ?>">

                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>
                                        <?php $tool->trans("submit_date"); ?>
                                    </label>
                                    <input type="text" name="date" class="date" value="<?php if (isset($_POST['submit_date']))
                                        echo $_POST['submit_date'] ?>">

                                    </td>
                                </tr>


                                <tr>
                                    <td>
                                        <label>
                                        <?php $tool->trans("desc"); ?>
                                    </label>
                                    <textarea name="desc" style="height: 120px;"><?php if (isset($_POST['desc']))
                                        echo $_POST['desc'] ?></textarea>

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
                                    <?php $tool->trans("subject_title") ?>
                                </th>
                                <th class="fonts"><?php $tool->trans("title") ?></th>
                                <th class="fonts"><?php $tool->trans("section_title") ?></th>
                                <th class="fonts">
                                    <?php $tool->trans("date") ?>
                                </th>
                                <th class="fonts">
                                    <?php $tool->trans("submit_date") ?>
                                </th>
                                <th class="fonts">
                                    <?php $tool->trans("desc") ?>
                                </th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            $curPageUrl = ($_SERVER['REQUEST_URI']);
                            //$curPageUrl = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

                            $res = $acd->getClassHomeWorks($param);


                            foreach ($res as $row) {



                                //$commonParam = "&_chk=1";
                                //$commonParam .= "&redir=" . urlencode($curPageUrl);


                                $queryStringDel = "&del=1";
                                $queryStringDel .= "&id=" . $row['id'];
                                $queryStringDel .= "&redir=" . urlencode($curPageUrl);

                                $linkDel = Tools::makeLink("academic", "homeworks" . $queryStringDel, "", "");

                                //echo '<pre>'; print_r($linkDel); echo '</pre>';
                                ?>
                                <tr>
                                    <td class="fonts">
                                        <?php echo $row['subject_title']; ?>
                                    </td>
                                    <td class="fonts"><?php echo $row['title']; ?></td>
                                    <td class="fonts"><?php echo $row['section_title']; ?></td>


                                    <td class="fonts">
                                        <?php echo $tool->ChangeDateFormat($row['date']); ?>
                                    </td>
                                    <td class="fonts">
                                        <?php echo $tool->ChangeDateFormat($row['submit_date']); ?>
                                    </td>
                                    <td class="fonts">
                                        <?php echo $row['description']; ?>
                                    </td>

                                    <td class="fonts"><a href="<?php echo $linkDel ?>"><i class="icon-remove"></i></a></td>



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
