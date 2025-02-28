<?php
/* @var $tpl Template */
/* @var $tool Tools */
Tools::getLib("QueryTemplate");
Tools::getLib("TemplateForm");

Tools::getModel("AcademicModel");

$tpl->setCanExport(false);


$qr = new QueryTemplate();
$tpf = new TemplateForm();

$acd = new AcademicModel();
$branch = isset($_GET['branch']) ? $tool->GetExplodedInt($_GET['branch']) : '';
$session = isset($_GET['session']) ? $tool->GetExplodedInt($_GET['session']) : '';
$date = isset($_GET['date']) ? $tool->ChangeDateFormat($_GET['date']) : '';


if(isset($_GET['del'])==1){
    if(isset($_GET['id'])){
        if(is_numeric($_GET['id'])){
            $id = $_GET['id'];
            $acd->removeStaffFixture($id);
            $url = isset($_GET['redir']) ? urldecode($_GET['redir']) : "";
            header("Location:" . $url);
            exit;
        }
    }
}




$tpl->renderBeforeContent();



$qr->searchContentAbove();



?>
    <div class="row-fluid">


        <div class="span3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>



        <div class="span3"><label class="fonts"><?php $tool->trans("date"); ?></label><input type="text" name="date" value="<?php if(isset($_GET['date'])) echo $_GET['date']?>" class="date" /></div>
        <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>


    </div>



<?php
$qr->searchContentBottom();

if(isset($_GET['_chk'])==1){



    if(empty($session) || empty($branch) || empty($date)){
        echo $tool->Message("alert",$tool->transnoecho("all_fields_required"));
        $tpl->footer();
        exit;
    }



    $curPageUrl = urlencode($_SERVER['REQUEST_URI']);


    $paramTeachers['branch'] = $branch;



    $paramTeachers['session'] = $session;
    $paramTeachers['date'] = $date;

    $enteredFixtures = $acd->getEnteredFixtures($paramTeachers);



    ?>

    <div class="body">
        <div id="printReady">




            <div class="row-fluid">
                <div class="span12">
                    <?php echo $tpl->branchBreadCrumbs() ?>
                </div>
            </div>



            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th><?php $tool->trans("original_teacher"); ?></th>
                        <th><?php $tool->trans("alternate_teacher"); ?></th>
                        <th><?php $tool->trans("period_title"); ?></th>
                        <th><?php $tool->trans("start_time"); ?></th>
                        <th><?php $tool->trans("end_time"); ?></th>
                        <th><?php $tool->trans("action"); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($enteredFixtures as $row){

                    $commonParam  = "&session=" . @$_GET['session'];
                    $commonParam .= "&_chk=1";
                    $commonParam .= "&redir=" . $curPageUrl;

                    $queryString = $commonParam;
                    $queryString .= "&gr_number=" . $row['origin_gr_number'];
                    $queryString .= "&date=" . $tool->ChangeDateFormat($date);

                    $queryStringDel = $commonParam;
                    $queryStringDel .= "&del=1";
                    $queryStringDel .= "&id=" . $row['id'];

                    $linkTimeTable = Tools::makeLink("academic","teachertimetable".$queryString,"","");
                    $linkDel = Tools::makeLink("academic","fixtures".$queryStringDel,"","");
                ?>
                    <tr>
                        <td><?php echo $row['origin_name'] ?> <?php echo $row['origin_fname'] ?></td>
                        <td><?php echo $row['name'] ?> <?php //echo $row['fname'] ?></td>
                        <td><?php echo $row['period_title'] ?></td>
                        <td><?php echo $row['start_time'] ?></td>
                        <td><?php echo $row['end_time'] ?></td>
                        <td>
                            <a href="<?php echo $linkTimeTable ?>" title="<?php $tool->trans("time_table"); ?>" target="_blank"><i class="icon-windows"></i> </a>
                            <a href="<?php echo $linkDel ?>" title="<?php $tool->trans("delete"); ?>"><i class="icon-remove"></i> </a>
                        </td>
                    </tr>

                <?php } ?>
                </tbody>
            </table>


        </div>
    </div>








    <?php
}
$tpl->footer();

