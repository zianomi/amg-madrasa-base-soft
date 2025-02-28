<?php
Tools::getLib("QueryTemplate");
Tools::getLib("Upload");
Tools::getModel("AcademicModel");
$acd = new AcademicModel();

$tpl->setCanExport(false);

$qr = new QueryTemplate();

$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$subject = (isset($_GET['subject'])) ? $tool->GetExplodedInt($_GET['subject']) : '';

//$date = (isset($_GET['date'])) ? $tool->ChangeDateFormat($_GET['date']) : "";

$param = array("class" => $class, "subject" => $subject);

/*if($tool->checkDateFormat($date)){
    $param['date'] = $date;
}*/

$subjectsData = $acd->getSubjects($param);
$classesData = $set->getTitleTable("classes");

$errors = array();

if(isset($_GET['del'])==1){
    if(isset($_GET['id'])){
        if(is_numeric($_GET['id'])){
            $id = $_GET['id'];
            $acd->removeHomeWork($id);
            $url = isset($_GET['redir']) ? urldecode($_GET['redir']) : "";
            header("Location:" . $url);
            exit;
        }
    }
}


if(isset($_POST['_chk'])==1) {
    $targetFileName = "";

    $url = isset($_POST['url']) ? urldecode($_POST['url']) : "";
    $class = isset($_POST['class']) ? $tool->GetInt($_POST['class']) : 0;
    $subject = isset($_POST['subject']) ? $tool->GetExplodedInt($_POST['subject']) : 0;
    $title = isset($_POST['title']) ? $acd->filter($_POST['title']) : 0;

    $datedPath = date("Y") . "/" . date("m") . "/";
    $uploadPath = UPLFRONTSITE . DRS;
    $targetDir = $uploadPath . $datedPath;


    $handle =  new Upload($_FILES['pdf']);


    if ($handle->uploaded) {
        $handle->file_new_name_body   = 'home_work' . $subject;
        $handle->mime_check = true;
        $handle->auto_create_dir = true;
        //$handle->file_max_size = '5120'; // 1KB
        $handle->allowed = array('application/pdf');
        $handle->process($targetDir);
        if ($handle->processed) {
            //echo 'image resized';
            $targetFileName = $handle->file_dst_name ;
            $handle->clean();
        } else {
            $errors[] = 'Error : ' . $handle->error;
        }
    }





    if(empty($subject)){
        $errors[] = Tools::transnoecho("please_select_subject");
    }

    if(empty($class)){
        $errors[] = Tools::transnoecho("please_enter_class");
    }

    /*if(empty($date) || !$tool->checkDateFormat($date)){
        $errors[] = Tools::transnoecho("please_enter_valid_date");
    }*/

    if(count($errors)==0){
        $data['title'] = $title;
        $data['class_id'] = $class;
        $data['subject_id'] = $subject;
        //$data['date'] = $date;
        //$data['description'] = $description;
        $data['pdf'] = $targetFileName;
        $data['path'] = $datedPath;
        $data['created_user_id'] = Tools::getUserId();
        $data['created'] = date("Y-m-d H:i:s");




        if($acd->insertHomeWork($data)){
            $_SESSION['msg'] = $tool->Message("succ",$tool->transnoecho("homework_inserted"));
        }
        else{
            $_SESSION['msg'] = $tool->Message("alert",$tool->transnoecho("error_in_insertion"));
        }
        if(empty($url)){
            Tools::Redir("academic","inserthomework","","");
        }
        else{
            header("Location:" . $url);
        }
        exit;
    }




}





$tpl->renderBeforeContent();

$tool->displayErrorArray($errors);


$qr->searchContentAbove();



$tpl->formHidden();


?>
    <div class="row-fluid">

        <div class="span3"><label class="fonts"><?php $tool->trans("class") ?>*</label>
            <select name="class" id="class">
                <?php

                echo $tpl->GetOptionVals(array("name" => "class", "data" => $classesData, "sel" => $class));
                ?>
            </select>
        </div>
        <!--<div class="span3"><label class="fonts"><?php /*$tool->trans("date") */?></label>

            <input type="text" name="date" class="date">
        </div>-->


        <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>

    </div>

<?php
$qr->searchContentBottom();

if(isset($_GET['_chk'])==1){

    if(empty($class)){
        echo $tool->Message("alert",Tools::transnoecho("please_select_class"));
        $tpl->footer();
        exit;
    }



    $res = $acd->getHomeWorks($param);



    $curPageUrl = urlencode($_SERVER['REQUEST_URI']);

    ?>

    <div class="body">
        <div id="printReady">





                <div class="row-fluid">

                    <div class="span3">

                        <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                            <?php echo $tpl->formHidden(); ?>
                            <input type="hidden" name="url" value="<?php echo $curPageUrl ?>">
                            <input type="hidden" name="class" value="<?php echo $class ?>">

                            <table class="table table-bordered table-striped">
                                <tbody>


                                <tr>
                                    <td>
                                        <label><?php Tools::trans("title") ?></label>
                                        <input type="text" name="title" id="title" value="" style="width: 94%">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label><?php Tools::trans("subject") ?></label>
                                        <select name="subject" id="subject" style="width: 98%">
                                            <?php
                                            echo $tpl->GetOptionVals(array("name" => "subject", "data" => $subjectsData, "sel" => $subject));
                                            ?>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <label><?php Tools::trans("PDF") ?></label>
                                        <input value="" type="file" name="pdf" class="" style="width: 95%">
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
                    <div class="span9">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th><?php $tool->trans("s_no") ?></th>

                                <th><?php $tool->trans("title") ?></th>
                                <th><?php $tool->trans("class") ?></th>
                                <th><?php $tool->trans("subject") ?></th>
                                <th><?php $tool->trans("view") ?></th>
                                <th><?php $tool->trans("action") ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i=0;

                            foreach ($res as $row){
                                $i++;
                                ?>
                                <tr>
                                    <td><?php echo $i ?></td>
                                    <td><?php echo $row['title'] ?></td>
                                    <td><?php echo $row['class_title'] ?></td>
                                    <td class="fonts"><?php echo $row['subject_title'] ?></td>
                                    <td class="fonts"><a href="<?php echo FRONT_SITE_WEB ?>/<?php echo $row['path'] ?><?php echo $row['pdf'] ?>" target="_blank"><?php $tool->trans("view") ?></a></td>

                                    <td><a onclick="return confirm('Are you sure you want to delete?');" href="<?php echo Tools::makeLink("academic","inserthomework&del=1&id=".$row['id']."&redir=".$curPageUrl,"","") ?>"><i class="icon-remove"></i></a> </td>
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

