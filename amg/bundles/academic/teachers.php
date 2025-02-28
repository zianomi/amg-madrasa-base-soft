<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();


$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$name = (isset($_GET['name'])) ? $set->filter($_GET['name']) : '';

$tpl->setCanExport(false);
$tpl->setCanPrint(false);
$tpl->setCanAdd(true);


$addLink = URL;
$addLink .= "/?menu=academic&page=editteacher&code=&action=list";

$qr->setCanAdd(true);
$qr->setAddLink($addLink);
$qr->setAddLinkButtonText(Tools::transnoecho("Add New Teacher"));

$qr->renderBeforeContent();
$qr->searchContentAbove();







$userBranches = $set->userBranches();

if (empty($userBranches)) {
    echo 'You do not have any branch access. Please contact admin to assign you a branch.';
    exit;
}




//$branch = $userBranches[0]['id'];

if (count($userBranches) > 1) {

    if (empty($branch)) {
        $sel = $userBranches[0]['id'];
    } else {
        $sel = $branch;
    }
    ?>


    <div class="row-fluid">

        <div class="span3"><label class="fonts">
                <?php $tool->trans("branch") ?>
            </label>
            <?php echo $tpl->userBranches($sel) ?>
        </div>
        <div class="span3"><label class="fonts">
                <?php $tool->trans("name") ?>
            </label>
            <input type="text" name="name" value="<?php echo $name ?>">
        </div>

        <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>

    </div>





    <?php
}


$qr->searchContentBottom();

if (empty($branch)) {
    $branch = $userBranches[0]['id'];
}


if (empty($branch)) {
    echo $tool->Message("alert", $tool->transnoecho("branch_required"));
    exit;
}



Tools::getModel("AcademicModel");
$acd = new AcademicModel();
$teachers = $acd->getUserTeachers(array("branch" => $branch, "name" => $name));

?>

<div class="body">
    <div id="printReady">







        <div id="editable_wrapper" class="dataTables_wrapper form-inline">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>
                            <?php $tool->trans("S#") ?>
                        </th>
                        <!--<th class="fonts"><?php /*$tool->trans("ID") */?></th>-->
                        <th class="fonts">
                            <?php $tool->trans("Name") ?>
                        </th>
                        <th class="fonts">
                            <?php $tool->trans("Username") ?>
                        </th>
                        <th class="fonts">
                            <?php $tool->trans("Password") ?>
                        </th>
                        <th class="fonts">
                            <?php $tool->trans("Phone number") ?>
                        </th>
                        <th class="fonts">
                            <?php $tool->trans("Status") ?>
                        </th>
                        <th class="fonts">
                            <?php $tool->trans("Action") ?>
                        </th>




                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    foreach ($teachers as $teacher) {




                        $i++;
                        ?>
                        <tr>
                            <td>
                                <?php echo $i; ?>
                            </td>
                            <!--<td><?php /*echo $teacher['id'] */?></td>-->
                            <td>
                                <?php echo $teacher['name'] ?>
                            </td>
                            <td class="fonts">
                                <?php echo $teacher['username']; ?>
                            </td>
                            <td class="fonts">
                                <?php echo $teacher['password'] ?>
                            </td>
                            <td class="fonts">
                                <?php echo $teacher['phone_number']; ?>
                            </td>
                            <td class="fonts status" data-type="select" data-name="block_teacher"
                                data-pk="<?php echo $teacher['id']; ?>"
                                data-url="<?php echo Tools::makeLink("ajax", "students", "", "") ?>">
                                <?php if ($teacher['published'] == 1)
                                    echo 'Active';
                                else
                                    echo 'Blocked'; ?>
                            </td>
                            <td>
                                <a class="btn"
                                    href="<?php echo Tools::makeLink("academic", "editteacher&id=" . $teacher['id'], "", "") ?>">Edit</a>

                                <a class="btn"
                                    href="<?php echo Tools::makeLink("academic", "subjectteachers&name=" . $teacher['name'] . "&_chk=1&id=" . $teacher['id'], "", "") ?>">Subjects</a>
                                <!--<a class="btn" href="<?php /*echo Tools::makeLink("academic","secsteachers&name=".$teacher['name']."&id=".$teacher['id'],"","")*/?>">Sections</a>-->
                                <!--<a class="btn" href="<?php /*echo Tools::makeLink("academic","timestructure&name=".$teacher['name']."&id=".$teacher['id'],"","")*/?>">Timetable</a>-->
                                <!--<a class="btn" href="<?php //echo FRONT_SITE_URL ?>/login?directTeacher=123&user=<?php //echo ($teacher['username']) ?>&pwd=<?php //echo ($teacher['password']) ?>" target="_blank"><?php //Tools::trans("Observe"); ?></a>-->
                                <a class="btn"
                                    href="<?php echo Tools::makeLink("academic", "teachertimes&_chk=1&id=" . $teacher['id'], "", "") ?>">
                                    <?php Tools::trans("Time"); ?>
                                </a>


                            </td>


                        </tr>

                    <?php } ?>


                </tbody>
            </table>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('.status').editable({
            value: 2,

            source: [
                { value: "1", text: 'Active' },
                { value: "0", text: 'Blocked' }
            ]
        });
    });
</script>

<?php

$tpl->footer();
