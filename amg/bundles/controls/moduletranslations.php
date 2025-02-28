<?php
$set = new SettingModel();

$parentMenus = $tpl->parentMenuArray();
$subMenus = $tpl->subMenuArray();
$childMenus = $tpl->childMenuArray();
$meunus =array();


$id = isset($_GET['id']) ? $tool->intVal($_GET['id']) : 0;



$from = isset($_GET['from']) ? $_GET['from'] : "";

$group = false;
$redirPage = "users";
if(isset($_POST['from'])){
    if($_POST['from'] == "groups"){
        $group = true;
        $redirPage = "groups";
    }
}


if(empty($id)){
    $_SESSION['msg'] = $tool->Message("alert",$tool->transnoecho("error_id_required"));
    $tool->Redir("controls",$redirPage,"12","list");
    exit;
}

if(isset($_POST['_chk'])==1){

    $vals = array();
    $valBranches = array();
    $id = $_POST['id'];


    foreach($_POST['pageid'] as $key => $val){
        $list = isset($val['list']) ? $tool->intVal($val['list']) : 0;
        $add = isset($val['add']) ? $tool->intVal($val['add']) : 0;
        $edit = isset($val['edit']) ? $tool->intVal($val['edit']) : 0;
        $delete = isset($val['delete']) ? $tool->intVal($val['delete']) : 0;
        $print = isset($val['print']) ? $tool->intVal($val['print']) : 0;
        $export = isset($val['export']) ? $tool->intVal($val['export']) : 0;
        $vals[] = $tool->setInsertDefaultValues(array("$id","$key","$add","$edit","$delete","$print","$export"));
    }

    foreach($_POST['branches'] as $branch){
        $branchId = $tool->GetExplodedInt($branch);
        $valBranches[] = $tool->setInsertDefaultValues(array("$id","$branchId"));
    }

    if($group){
        $removeTable = "group";
    }
    else{
        $removeTable = "users";
    }
    $set->removeLastModules($id,$removeTable);

    $res = $set->insertSystemModules($group,$vals);

    $set->removeLastBranches($id,$removeTable);
    $tpl->removeMenuCache();
    $resBranch = $set->insertBranches($group,$valBranches);


    $set->insertLoginModules(Tools::getUserId());


    $tpl->removeMenuCache();

    if($res["status"]){
        $_SESSION['msg'] = $res['msg'];
        $tool->Redir("controls",$redirPage,"12","list");
        exit;
    }
    else{
        echo $tool->Message("alert",$res["msg"]);
    }

}

$tpl->renderBeforeContent();

if($from == "groups"){
    $modulesData = $set->getGroupModules(array("id" => $id));
    $branchData = $set->getGroupBranches(array("id" => $id));
}
else{
    $modulesData = $set->getUserModueles(array("id" => $id));
    $branchData = $set->getUserBranches(array("id" => $id));
}

$selectedBranch = array();

foreach ($branchData as $rB){
    $selectedBranch[] = $rB['branch_id'];
}
?>
    <div class="row-fluid">

        <div class="span12">

            <section id="accordion" class="social-box">
              <div class="header">
                  <h4>Modules</h4>
              </div>
              <div class="body">


              <form action="#" method="post" id="module_pages">
                  <input type="hidden" name="_chk" value="1">
                  <input type="hidden" name="from" value="<?php echo $from ?>">
                  <input type="hidden" name="id" value="<?php echo $id ?>">

                  <?php echo $tpl->formHidden(); ?>

                <div id="menu-collapse">

                    <?php

                    foreach($parentMenus as $parentMenu) {

                        if($parentMenu['extra'] == 'dev'){
                            if($_SESSION['userType'] != 'dev'){
                                continue;
                            }
                        }

                    if(isset($subMenus[$parentMenu['id']])){
                        foreach($subMenus[$parentMenu['id']] as $subMenu) {

                            if($subMenu['extra'] == 'dev'){
                                if($_SESSION['userType'] != 'dev'){
                                    continue;
                                }
                            }

                            $meunus[$parentMenu['id']][$subMenu['id']] = array(
                                    "id" => $subMenu['id']
                            , "title" => $subMenu['title']
                            , "bundle" => $subMenu['bundle']
                            , "phpfile" => $subMenu['phpfile']
                            , "extra" => $subMenu['extra']
                            );
                            if(isset($childMenus[$subMenu['id']])){
                                foreach($childMenus[$subMenu['id']] as $childMenu) {

                                    if($childMenu['extra'] == 'dev'){
                                        if($_SESSION['userType'] != 'dev'){
                                            continue;
                                        }

                                    }

                                    $meunus[$parentMenu['id']][$childMenu['id']] = array("id" => $childMenu['id'], "title" => $childMenu['title'], "bundle" => $childMenu['bundle'], "phpfile" => $childMenu['phpfile'], "extra" => $childMenu['extra']);
                                }
                            }
                        }
                    }

                    $can_add = 0;
                    $can_edit = 0;
                    $can_delete = 0;
                    $can_print = 0;
                    $can_export = 0;
                    $hasPage = false;

                    if(isset($modulesData[$parentMenu['id']])){
                        $modulesDataSetParent = $modulesData[$parentMenu['id']];
                        $hasPage = true;
                        $can_add = ($modulesDataSetParent["can_add"] == 1) ? ' checked="checked"' : '';
                        $can_edit = ($modulesDataSetParent["can_edit"] == 1) ? ' checked="checked"' : '';
                        $can_delete = ($modulesDataSetParent["can_delete"] == 1) ? ' checked="checked"' : '';
                        $can_print = ($modulesDataSetParent["can_print"] == 1) ? ' checked="checked"' : '';
                        $can_export = ($modulesDataSetParent["can_export"] == 1) ? ' checked="checked"' : '';
                    }
                    ?>
                    <div class="group">
                        <h3><a href="#" class="fonts"><?php echo $parentMenu['title'] ?></a></h3>

                        <section id="feeds" class="feeds social-box social-bordered social-blue">

                    <div class="header"><h4><i class="icon-th-list"></i><?php echo $parentMenu['title'] ?></h4></div>

                    <table class="table table-bordered table-striped table-hover flip-scroll">

                        <thead>
                            <tr>
                               <th class="fonst"><?php $tool->trans("module"); ?></th>
                               <th class="fonst"><?php $tool->trans("list"); ?></th>
                               <th class="fonst"><?php $tool->trans("add"); ?></th>
                               <th class="fonst"><?php $tool->trans("edit"); ?></th>
                               <th class="fonst"><?php $tool->trans("delete"); ?></th>
                               <th class="fonst"><?php $tool->trans("print"); ?></th>
                               <th class="fonst"><?php $tool->trans("export"); ?></th>
                               <th class="fonst"><?php $tool->trans("translations"); ?></th>
                            </tr>
                        </thead>

                        <tbody>
                    <tr>
                        <td><?php  echo $parentMenu['title'] ?></td>
                        <td>
                            <div class="make-switch switch-mini">
                                <input type="checkbox" name="pageid[<?php echo $parentMenu['id'] ?>][list]"<?php if($hasPage) echo 'checked="checked"'; ?> value="1" />
                            </div>
                        </td>
                        <td>
                            <div class="make-switch switch-mini">
                                <input type="checkbox" name="pageid[<?php echo $parentMenu['id'] ?>][add]"<?php echo $can_add ?> value="1" />
                            </div>
                        </td>
                        <td>
                            <div class="make-switch switch-mini">
                                <input type="checkbox" name="pageid[<?php echo $parentMenu['id'] ?>][edit]"<?php echo $can_edit ?> value="1" />
                            </div>
                        </td>
                        <td>
                            <div class="make-switch switch-mini">
                                <input type="checkbox" name="pageid[<?php echo $parentMenu['id'] ?>][delete]"<?php echo $can_delete ?> value="1" />
                            </div>
                        </td>
                        <td>
                            <div class="make-switch switch-mini">
                                <input type="checkbox" name="pageid[<?php echo $parentMenu['id'] ?>][print]"<?php echo $can_print ?> value="1" />
                            </div>
                        </td>
                        <td>
                            <div class="make-switch switch-mini">
                                <input type="checkbox" name="pageid[<?php echo $parentMenu['id'] ?>][export]"<?php echo $can_export ?> value="1" />
                            </div>
                        </td>
                        <td>&nbsp;</td>
                    </tr>



                        <?php
                        if(isset($meunus[$parentMenu['id']])) {

                        foreach ($meunus[$parentMenu['id']] as $meunu) {

                            $can_add = 0;
                            $can_edit = 0;
                            $can_delete = 0;
                            $can_print = 0;
                            $can_export = 0;
                            $hasPage = false;
                            if(isset($modulesData[$meunu['id']])){
                                $modulesDataSet = $modulesData[$meunu['id']];
                                $hasPage = true;
                                $can_add = ($modulesDataSet["can_add"] == 1) ? ' checked="checked"' : '';
                                $can_edit = ($modulesDataSet["can_edit"] == 1) ? ' checked="checked"' : '';
                                $can_delete = ($modulesDataSet["can_delete"] == 1) ? ' checked="checked"' : '';
                                $can_print = ($modulesDataSet["can_print"] == 1) ? ' checked="checked"' : '';
                                $can_export = ($modulesDataSet["can_export"] == 1) ? ' checked="checked"' : '';
                            }
                            ?>
                                <tr>
                                    <td class="fonts"><?php if($meunu['extra'] == "alert") echo '<span style="color: #ff0f3a;">'.$meunu['title'].'</span>'; else echo $meunu['title'] ?></td>
                                    <td>
                                        <div class="make-switch switch-mini">
                                            <input type="checkbox" name="pageid[<?php echo $meunu['id'] ?>][list]"<?php if($hasPage) echo 'checked="checked"'; ?> value="1"/>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="make-switch switch-mini">
                                            <input type="checkbox" name="pageid[<?php echo $meunu['id'] ?>][add]"<?php echo $can_add ?> value="1"/>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="make-switch switch-mini">
                                            <input type="checkbox" name="pageid[<?php echo $meunu['id'] ?>][edit]"<?php echo $can_edit ?> value="1"/>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="make-switch switch-mini">
                                            <input type="checkbox" name="pageid[<?php echo $meunu['id'] ?>][delete]"<?php echo $can_delete ?> value="1"/>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="make-switch switch-mini">
                                            <input type="checkbox" name="pageid[<?php echo $meunu['id'] ?>][print]"<?php echo $can_print ?> value="1"/>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="make-switch switch-mini">
                                            <input type="checkbox" name="pageid[<?php echo $meunu['id'] ?>][export]"<?php echo $can_export ?> value="1"/>
                                        </div>
                                    </td>
                                    <td><a href="<?php echo Tools::makeLink("controls","stringextractor","0","edit&extra=".$meunu['extra']."&bundle=".$meunu['bundle']."&phpfile=".$meunu['phpfile']) ?>" class="btn btn-file">
                                        <i class="icon-white icon-share-alt"></i>
                                    </a></td>
                                </tr>

                            <?php }
                        }?>


                                </tbody>

                            </table>


                        </section>
                    </div>

                   <?php } ?>




                  <div class="group">
                      <h3><a href="#"><?php $tool->trans("branch") ?></a></h3>


                      <div class="row">

                          <div class="span12">

                              <?php
                              echo $tpl->GetMultiOptions(array("name" => "branches[]", "data" => $set->allBranches(), "sel" => $selectedBranch));
                              ?>
                          </div>
                      </div>

                  </div>

                </div>


                  <div class="form-actions txtcenter">
                      <button type="submit" class="btn btn-primary">Save</button>
                      <button type="reset" class="btn btn-danger">Cancel</button>
                   </div>

              </form>
              </div>
          </section>
        </div>
    </div>

<?php
$tpl->footer();
