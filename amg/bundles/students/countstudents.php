<?php
Tools::getLib("TemplateForm");
$tpf = new TemplateForm();
$tpl->setCanExport(false);
$tpl->setCanPrint(false);
$tpl->renderBeforeContent();
?>

    <div class="row-fluid">

        <div class="span12">

            <section id="accordion" class="social-box">
                <div class="header">
                    <h4><?php $tool->trans("count_students") ?></h4>
                </div>
                <div class="body">


                    <form action="" method="get" target="_blank">
                        <input type="hidden" name="menu" value="students"/>
                        <input type="hidden" name="page" value="countstudentsprint"/>


                        <div class="row-fluid">
                            <div class="span3"><label class="fonts"><?php $tool->trans("zones") ?></label>
                                <?php echo $tpl->GetOptions(array("name" => "zone", "data" => $set->getZones(), "sel" => "")); ?>
                            </div>
                            <div class="span3">
                                <label class="fonts"><?php $tool->trans("year") ?></label>
                                <select name="year" id="year">
                                    <?php echo $tpf->NewYearsDropDown(); ?>
                                </select></div>
                            <div class="span3">
                                <label class="fonts"><?php $tool->trans("month") ?></label><select name="month" id="month">
                                    <?php echo $tpf->NewMonthDropDown(); ?>
                                </select></div>
                            <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>
                        </div>





                    </form>
                </div>
            </section>
        </div>
    </div>

<?php

$tpl->footer();


/**
 * Created by PhpStorm.
 * User: ZIA
 * Date: 7/19/2018
 * Time: 8:21 PM
 */





exit;
Tools::getModel("StudentsModel");
$stu = new StudentsModel();

$sessionData = $set->getCurrentSession();
$sessionId = $sessionData['id'];

$students = $stu->studentsCount($sessionId);
$operatorsData = $stu->branchOperators();

$zones = array();
$branches = array();
$operators = array();

foreach ($operatorsData as $operator){
    $operators[$operator['branch_id']] = $operator['name'];
}

foreach ($students as $student){
    $zones[$student['zone_id']] = array("zone_id" => $student['zone_id'], "zone_title" => $student['zone_title']);
    $branches[$student['zone_id']][] = array(
        "tot" => $student['tot']
    , "branch_id" => $student['branch_id']
    , "branch_title" => $student['branch_title']
    , "total_students" => $student['total_students']
    );
}


$tpl->renderBeforeContent();


?>


    <div id="gr_res">&nbsp;</div>
    <div class="social-box">
        <div class="header">
            <button class="btn btn-info" data-toggle="collapse" onclick="printSpecial()">Print<i class="icon-print"></i></button>
        </div>
        <div class="body">



            <div id="printReady">


                <?php
                $z=0;
                foreach ($zones as $zone){


                ?>
                    <div class="alert alert-success fonts" style="font-size:20px; width: 719px;"><?php echo $zone['zone_title'] ?></div>
                    <table class="table table-bordered table-striped table-hover" style="width: 770px; margin-top: -19px;">

                        <thead>
                        <tr>
                            <th style="width: 10px">S#</th>
                            <th style="width: 10px">S#</th>
                            <th style="width: 350px"><?php $tool->trans("name") ?></th>
                            <th style="width: 200px"><?php $tool->trans("operator") ?></th>
                            <th style="width: 100px"><?php $tool->trans("count") ?></th>
                            <th style="width: 100px"><?php $tool->trans("inserted") ?></th>
                        </tr>
                        </thead>
                        <?php
                        $i=0;
                        foreach ($branches[$zone['zone_id']] as $branch){
                            $i++;
                            $z++;
                        ?>
                            <tr>
                                <td><?php echo $z ?></td>
                                <td><?php echo $i ?></td>
                                <td class="fonts"><?php echo $branch['branch_title'] ?></td>
                                <td><?php if(isset($operators[$branch['branch_id']])) echo $operators[$branch['branch_id']] ?></td>
                                <td><?php echo $branch['total_students'] ?></td>
                                <td><?php echo $branch['tot'] ?></td>
                            </tr>
                        <?php } ?>
                    </table>

                <?php } ?>

            </div>
        </div>
    </div>


<?php

$tpl->footer();