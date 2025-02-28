<?php

$branch = isset($_GET['branch']) ? $tool->GetExplodedInt($_GET['branch']) : '';
$session = isset($_GET['session']) ? $tool->GetExplodedInt($_GET['session']) : '';
$class = isset($_GET['class']) ? $tool->GetExplodedInt($_GET['class']) : '';
$errors = array();


Tools::getModel("AcademicModel");
$acd = new AcademicModel();
Tools::getLib("Paginator");
$url = URL . "?menu=academic&page=teacherassignments&code=&action=list&";

$params['session'] = $session;
$params['branch'] = $branch;
$params['class'] = $class;

$num_rows = $acd->getTeacherAssignements("count",$params);


$pagingObj = new Paginator;
$pagingObj->items_total = $num_rows;
$pagingObj->default_ipp = 250;
$pagingObj->mid_range = 5;
$pagingObj->paginate($url);
$params["limit"] = $pagingObj->limit;


$tpl->renderBeforeContent();
$tool->displayErrorArray($errors);




$res = $acd->getTeacherAssignements("q",$params);
?>


    <div class="social-box">
        <div class="header">
            <div class="tools">

                <button class="btn btn-success" data-toggle="collapse" data-target="#advanced-search">
                    <i class="icon-filter"></i><?php $tool->trans("search") ?></button>
            </div>
        </div>
        <div class="body">
            <div id="jamia_msg">&nbsp;</div>
            <div id="advanced-search" class="collapse">

                <?php
                echo $tpl->formTag();
                echo $tpl->formHidden();
                ?>
                <input type="hidden" name="_chk" value="1"/>
                <div class="container text-center">


                    <div class="row">


                        <div class="span3">
                            <label class="fonts"><?php $tool->trans("session") ?></label>

                            <?php echo $tpl->getAllSession(array("sel" => $session)); ?>
                        </div>

                        <div class="span3">
                            <label class="fonts"><?php $tool->trans("branch") ?></label>
                            <?php echo $tpl->getAllBranch(array("sel" => $branch)); ?>
                        </div>

                        <div class="span3"><label class="fonts"><?php $tool->trans("class") ?></label><?php echo $tpl->getClasses() ?></div>


                        <div class="span3">
                            <label class="fonts">&nbsp;</label>
                            <button type="submit" class="btn btn-small"><?php $tool->trans("search") ?></button>
                        </div>
                    </div>
                </div>
                <?php echo $tpl->formClose() ?>


            </div>

            <div id="printReady">






                                    <table class="table table-bordered table-striped table-hover">

                                        <thead>
                                        <tr>
                                            <th class="fonst"><?php $tool->trans("S#") ?></th>
                                            <th class="fonst"><?php $tool->trans("Title") ?></th>
                                            <th class="fonst"><?php $tool->trans("Date") ?></th>
                                            <th class="fonst"><?php $tool->trans("Session") ?></th>
                                            <th class="fonst"><?php $tool->trans("Branch") ?></th>
                                            <th class="fonst"><?php $tool->trans("Class") ?></th>
                                            <th class="fonst"><?php $tool->trans("Subject") ?></th>
                                            <th class="fonst"><?php $tool->trans("Teacher") ?></th>
                                            <th class="fonst"><?php $tool->trans("Action") ?></th>

                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php
                                        foreach($res as $row){
                                            ?>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td><?php echo $row['title']; ?></td>
                                                <td><?php echo $tool->ChangeDateFormat($row['date']); ?></td>
                                                <td><?php echo $row['session_title']; ?></td>
                                                <td><?php echo $row['branch_title']; ?></td>
                                                <td><?php echo $row['class_title']; ?></td>
                                                <td><?php echo $row['subject_title']; ?></td>
                                                <td><?php echo $row['teacher_title']; ?></td>
                                                <td>
                                                    <a href="<?php echo FRONT_SITE_URL ?>/assignment-detail/<?php echo $row['id']; ?>" target="_blank"><?php Tools::trans("View"); ?></a>

                                                </td>

                                            </tr>

                                        <?php } ?>
                                        </tbody>

                                    </table>


















                <div class="text-center">
                    <div class="pagination">
                        <ul>
                            <?php echo $pagingObj->display_jump_menu($url); ?>
                            <?php echo $pagingObj->display_pages(); ?>
                            <?php echo $pagingObj->display_items_per_page($url); ?>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>


<?php
$tpl->footer();
