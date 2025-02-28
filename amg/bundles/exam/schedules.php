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
            $paperRow = $exm->getSchedule($id);
            $link = $paperRow['paper_link'];
            if (!empty($link)) {
                $filePath = $amgSpace->getFileUrl($link);
                $amgSpace->deleteDir($filePath);
            }

            $exm->deleteSchedule($id);
            $url = isset($_GET['redir']) ? urldecode($_GET['redir']) : "";
            header("Location:" . $url);
            exit;
        }
    }
}


if (isset($_POST['_chk']) == 1) {
    $url = isset($_POST['url']) ? urldecode($_POST['url']) : "";
    $session = (isset($_POST['session'])) ? $tool->GetExplodedInt($_POST['session']) : '';
    $date = isset($_POST['date']) ? $tool->ChangeDateFormat($_POST['date']) : "";


    if (empty($url)) {
        $errors[] = Tools::transnoecho("url_missing");
    }

    if (empty($session)) {
        $errors[] = Tools::transnoecho("please_select_session");
    }

    if (empty($date)) {
        $errors[] = Tools::transnoecho("please_enter_date");
    }

    if (!$tool->checkDateFormat($date)) {
        $errors[] = Tools::transnoecho("please_enter_valid_date");
    }


    if (empty($_FILES['pdf']['name'])) {
        $errors[] = Tools::transnoecho("please_select_pdf_file");
    }



    if (count($errors) == 0) {
        $data['session_id'] = $session;
        $data['paper_link'] = "";
        $data['subject'] = $_POST['desc'];
        $data['date'] = $date;

        $amgSpace->setFileInputName('pdf');

        if (!empty($_FILES['pdf']['name'])) {
            $fileArr = $amgSpace->upload();
            $newAmgFileName = $fileArr['value'];
            $data['paper_link'] = $newAmgFileName;
        }


        if ($exm->insertSchedule($data)) {
            $_SESSION['msg'] = $tool->Message("succ", $tool->transnoecho("schedule_inserted"));
        } else {
            $_SESSION['msg'] = $tool->Message("alert", $tool->transnoecho("schedule_insert_failed"));
        }
        if (empty($url)) {
            Tools::Redir("exam", "schedules", "", "");
        } else {
            header("Location:" . $url);
        }
        exit;
    }
}



$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$param['session'] = $session;


$tpl->renderBeforeContent();

$tool->displayErrorArray($errors);


$qr->searchContentAbove();



$tpl->formHidden();


?>
<div class="row-fluid">
    <div class="col3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
    <div class="col3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>

</div>

<?php
$qr->searchContentBottom();

if (isset($_GET['_chk']) == 1) {

    if (empty($session)) {
        echo $tool->Message("alert", Tools::transnoecho("session_required"));
        $tpl->footer();
        exit;
    }
    $res = $exm->schedules($param);





    $curPageUrl = urlencode($_SERVER['REQUEST_URI']);

    $colSize = 12;
?>

    <div class="body">
        <div id="printReady">






            <?php if (!empty($session)) { ?>
                <div class="alert alert-success fonts"><?php echo $tool->GetExplodedVar($_GET['session']); ?></div>
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



                            <table class="table table-bordered table-striped">
                                <tbody>

                                    <tr>
                                        <td>
                                            <label class="fonts"><?php Tools::trans("desc") ?></label>
                                            <input type="text" name="desc" value="">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <label class="fonts"><?php Tools::trans("date") ?></label>
                                            <input type="text" name="date" class="date" value="">
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
                                <th class="fonts"><?php $tool->trans("subject") ?></th>
                                <th class="fonts"><?php $tool->trans("date") ?></th>

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
                                    <td><?php echo $row['subject'] ?></td>
                                    <td><?php echo $tool->ChangeDateFormat($row['date']) ?></td>
                                    <td class="fonts"><a title="PDF" href="<?php echo $amgSpace->getCdnFileUrl($row['paper_link']) ?>" target="_blank"><i class="icon-book"></i></a></td>
                                    <?php if ($tpl->isCanDelete()) { ?>
                                        <td><a onclick="return confirm('Are you sure you want to delete?');" href="<?php echo Tools::makeLink("exam", "schedules&del=1&id=" . $row['id'] . "&redir=" . $curPageUrl, "", "") ?>"><i class="icon-remove"></i></a> </td>
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
