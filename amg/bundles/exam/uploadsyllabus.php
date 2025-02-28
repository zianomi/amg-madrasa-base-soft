<?php

/** @var Tools $tool */
/** @var Template $tpl */
Tools::getLib("QueryTemplate");
Tools::getModel("ExamModel");
Tools::getLib("AmgSpace");

$qr = new QueryTemplate();
$amgSpace = new AmgSpace();
$exm = new ExamModel();
$amgSpace->setDomainName(DOMAIN_NAME);
$errors = array();
$tpl->setCanExport(false);
$tpl->setCanPrint(false);

if (isset($_GET['del']) == 1) {
    if (isset($_GET['id'])) {
        if (is_numeric($_GET['id'])) {
            $id = $_GET['id'];
            $paperRow = $exm->getSyllabus($id);
            $link = $paperRow['paper_link'];
            if (!empty($link)) {
                $filePath = $amgSpace->getFileUrl($link);
                $amgSpace->deleteDir($filePath);
            }

            $exm->deleteSyllabus($id);
            $url = isset($_GET['redir']) ? urldecode($_GET['redir']) : "";
            header("Location:" . $url);
            exit;
        }
    }
}


if (isset($_POST['_chk']) == 1) {
    $url = isset($_POST['url']) ? urldecode($_POST['url']) : "";
    $branch = (isset($_POST['branch'])) ? $tool->GetExplodedInt($_POST['branch']) : '';
    $class = (isset($_POST['class'])) ? $tool->GetExplodedInt($_POST['class']) : '';
    $session = (isset($_POST['session'])) ? $tool->GetExplodedInt($_POST['session']) : '';
    $exam = (isset($_POST['exam_name'])) ? $tool->GetExplodedInt($_POST['exam_name']) : '';
    $subject = (isset($_POST['subject'])) ? $exm->filter($_POST['subject']) : '';




    if (empty($url)) {
        $errors[] = Tools::transnoecho("url_missing");
    }

    if (empty($session)) {
        $errors[] = Tools::transnoecho("please_select_session");
    }

    if (empty($branch)) {
        $errors[] = Tools::transnoecho("please_select_branch");
    }

    if (empty($class)) {
        $errors[] = Tools::transnoecho("please_select_class");
    }

    if (empty($exam)) {
        $errors[] = Tools::transnoecho("please_select_exam");
    }

    if (empty($subject)) {
        $errors[] = Tools::transnoecho("please_select_subject");
    }

    if (empty($_FILES['pdf']['name'])) {
        $errors[] = Tools::transnoecho("please_select_pdf_file");
    }



    if (count($errors) == 0) {
        $data['session_id'] = $session;
        $data['class_id'] = $class;
        $data['exam_id'] = $exam;
        $data['subject'] = $subject;
        $data['paper_link'] = "";




        $amgSpace->setFileInputName('pdf');

        if (!empty($_FILES['pdf']['name'])) {
            $fileArr = $amgSpace->upload();
            $newAmgFileName = $fileArr['value'];
            $data['paper_link'] = $newAmgFileName;
        }


        if ($exm->insertSyllabus($data)) {
            $_SESSION['msg'] = $tool->Message("succ", $tool->transnoecho("paper_inserted"));
        } else {
            $_SESSION['msg'] = $tool->Message("alert", $tool->transnoecho("paper_insert_failed"));
        }
        if (empty($url)) {
            Tools::Redir("exam", "uploadsyllabus", "", "");
        } else {
            header("Location:" . $url);
        }
        exit;
    }
}



$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$exam = (isset($_GET['exam_name'])) ? $tool->GetExplodedInt($_GET['exam_name']) : '';

$param['branch'] = $branch;
$param['class'] = $class;
$param['session'] = $session;
$param['exam'] = $exam;


$tpl->renderBeforeContent();

$tool->displayErrorArray($errors);


$qr->searchContentAbove();



$tpl->formHidden();


?>
<div class="row">

    <div class="col3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
    <div class="col3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
    <div class="col3"><label class="fonts"><?php $tool->trans("class") ?></label><?php echo $tpl->getClasses() ?></div>
    <div class="col3"><label><?php $tool->trans("exam_name") ?></label><?php echo $tpl->examDropDown($exm->getExamNames()); ?></div>

    <div class="col3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>

</div>

<?php
$qr->searchContentBottom();

if (isset($_GET['_chk']) == 1) {

    if (empty($session) || empty($class) || empty($exam)) {
        echo $tool->Message("alert", Tools::transnoecho("all_fields_required"));
        $tpl->footer();
        exit;
    }
    $res = $exm->examSyllabus($param);





    $curPageUrl = urlencode($_SERVER['REQUEST_URI']);

    $colSize = 12;
?>

    <div class="body">
        <div id="printReady">




            <?php if (!empty($class)) { ?>
                <div class="alert alert-success fonts"><?php echo $tool->GetExplodedVar($_GET['class']); ?></div>
            <?php } ?>

            <?php if (!empty($exam)) { ?>
                <div class="alert alert-success fonts"><?php echo $tool->GetExplodedVar($_GET['exam_name']); ?></div>
            <?php } ?>



            <div class="row-fluid">


                <?php if ($tpl->isCanAdd()) {
                    $colSize = 9;
                ?>

                    <div class="span3">
                        <div class="row-fluid" id="student_res"></div>
                        <form method="post" enctype="multipart/form-data">
                            <?php echo $tpl->formHidden(); ?>
                            <input type="hidden" name="url" value="<?php echo $curPageUrl ?>">
                            <input type="hidden" name="session" value="<?php echo $_GET['session']; ?>">
                            <input type="hidden" name="branch" value="<?php echo $_GET['branch']; ?>">
                            <input type="hidden" name="class" value="<?php echo $_GET['class']; ?>">
                            <input type="hidden" name="exam_name" value="<?php echo $_GET['exam_name']; ?>">

                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <td>
                                            <label class="fonts"><?php Tools::trans("subject") ?></label>
                                            <input value="<?php if (isset($_POST['subject'])) echo $_POST['subject'] ?>" type="text" name="subject" id="subject">
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
                                            <input type="submit" class="btn btn-primary" value="Save">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                <?php } ?>
                <div class="span<?php echo $colSize ?>">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="fonts"><?php $tool->trans("s_no") ?></th>
                                <th class="fonts"><?php $tool->trans("session_title") ?></th>
                                <th class="fonts"><?php $tool->trans("class_title") ?></th>
                                <th class="fonts"><?php $tool->trans("exam_title") ?></th>
                                <th class="fonts"><?php $tool->trans("subject") ?></th>
                                <th class="fonts"><?php $tool->trans("pdf") ?></th>
                                <?php if ($tpl->isCanDelete()) { ?>
                                    <th class="fonts"><?php $tool->trans("delete") ?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;


                            foreach ($res as $row) {
                                $i++;
                            ?>
                                <tr>
                                    <td><?php echo $i ?></td>
                                    <td><?php echo $row['session_title'] ?></td>
                                    <td class="fonts"><?php echo $row['class_title'] ?></td>
                                    <td class="fonts"><?php echo $row['exam_title'] ?></td>
                                    <td class="fonts"><?php echo $row['subject'] ?></td>
                                    <td class="fonts"><a title="PDF" href="<?php echo $amgSpace->getCdnFileUrl($row['paper_link']) ?>" target="_blank"><i class="icon-book"></i></a></td>
                                    <?php if ($tpl->isCanDelete()) { ?>
                                        <td><a onclick="return confirm('Are you sure you want to delete?');" href="<?php echo Tools::makeLink("exam", "uploadsyllabus&del=1&id=" . $row['id'] . "&redir=" . $curPageUrl, "", "") ?>"><i class="icon-remove"></i></a> </td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>

                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>

<?php
}
$tpl->footer();
