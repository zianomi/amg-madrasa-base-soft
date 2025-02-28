<?php
/**
 * Created by PhpStorm.
 * User: mzia
 * Date: 11/27/2017
 * Time: 4:28 PM
 */
$id = isset($_GET['id']) ? $tool->GetInt($_GET['id']) : "";

if(empty($id)){
    $_SESSION['msg'] = $tool->Message("alert", $tool->transnoecho("Menu id required"));
    $tool->Redir("mainpage", "menu", "", "");
    exit;
}

Tools::getModel("BayyinatModel");
$bay = new BayyinatModel();

$errors = array();
$data = array();

if(isset($_POST['_chk'])==1){

    $menuId = isset($_POST['menu_id']) ? $tool->GetInt($_POST['menu_id']) : "";
    $menuLevel = isset($_POST['menu_level']) ? $tool->GetInt($_POST['menu_level']) : "";
    $menuType = isset($_POST['menu_type']) ? $tool->GetInt($_POST['menu_type']) : "";
    $pages = isset($_POST['pages']) ? $tool->GetInt($_POST['pages']) : "";
    $routes = isset($_POST['routes']) ? $tool->GetInt($_POST['routes']) : "";
    $position = isset($_POST['position']) ? $tool->GetInt($_POST['position']) : "";
    $label = isset($_POST['label']) ? $set->escape($_POST['label']) : "";
    $link = isset($_POST['link']) ? $set->escape($_POST['link']) : "";

    $menuCachefiles = glob(FRONTSITEROOT . '/cache/menus/'.Tools::getLang().'/*'); // get all file names
    foreach($menuCachefiles as $menuCachefile){ // iterate files
      if(is_file($menuCachefile))
        unlink($menuCachefile); // delete file
    }



    if(empty($menuType)){
        $errors[] = $tool->transnoecho("Please select menu type");
    }

    if(empty($position)){
        $errors[] = $tool->transnoecho("Please enter menu position");
    }

    if(empty($label)){
        $errors[] = $tool->transnoecho("Please enter menu label");
    }

    if($menuType == 1){
        if(empty($pages)){
            $errors[] = $tool->transnoecho("Please select page");
        }
        else{
            $pageDbData = $bay->SitePages(array("id" => $pages));
            $link = $pageDbData['slug'];
        }
    }

    if($menuType == 2){
        if(empty($routes)){
            $errors[] = $tool->transnoecho("Please select route");
        }
        else{
           $routeDbDataArr = $set->GetRoutes(array("lang" => $tool->getLangId(), "id" => $routes));
            $routeDbData = $routeDbDataArr[0];
            $link = $routeDbData['route'];
        }
    }

    if($menuType == 3){
        if(empty($link)){
            $errors[] = $tool->transnoecho("Please enter link");
        }
    }


    if(count($errors)==0){
        $data["parent_id"] = $menuLevel;
        $data["menu_id"] = $menuId;
        $data["menu_type"] = $menuType;
        $data["title"] = $label;
        $data["menu_link"] = $link;
        $data["position"] = $position;
        $data["lang"] = $tool->getLangId();

        $set->insertMenu($data);

        $_SESSION['msg'] = $tool->Message("succ", $tool->transnoecho("Menu updated"));

        $tool->Redir("mainpage", "menubuilder&id=".$menuId, "", "");
    }



}





$sitePages = $bay->GetAllSitePages(array("lang" => $tool->getLangId()));
$routes = $set->GetRoutes(array("lang" => $tool->getLangId()));

$displayParentMenusData = $set->GetSiteMenu(array("lang" => $tool->getLangId(), "menu_id" => $id, "main_parent" => "true"));


$tpl->renderBeforeContent();

$tool->displayErrorArray($errors);
?>




<div class="social-box">

    <div class="header">
        <h4><span class="fonts">Menu Items</span></h4>
    </div>

    <div class="body">

        <div class="row-fluid">


               <div class="span4">


                   <div class="dd" id="nestable">
                       <ol class="dd-list">
                           <?php
                           foreach($displayParentMenusData as $row){

                               $displayChildMenusData = $set->GetSiteMenu(array("parent_id" => $row['id'], "lang" => $tool->getLangId(), "menu_id" => $id));
                           ?>
                               <li class="dd-item parentitem" data-id="<?php echo $row['id'] ?>">
                                   <div class="dd-handle"><span class="fonts"><?php echo $row['title'] ?></span><span style="float: left"><?php echo $row['position'] ?></span></div>

                               <?php if(count($displayChildMenusData)>0){ ?>

                                       <ul>
                                           <?php
                                           foreach($displayChildMenusData as $rowChild){
                                           ?>
                                           <li class="dd-item childitem" data-id="<?php echo $rowChild['id'] ?>">
                                               <div class="dd-handle"><span class="fonts"><?php echo $rowChild['title'] ?></span> <span style="float: left"><?php echo $rowChild['position'] ?></span></div>
                                           </li>
                                           <?php } ?>
                                       </ul>

                               <?php
                                    }
                               ?>
                           </li>
                           <?php } ?>
                       </ol>
                   </div>

               </div>

            <div class="span4">&nbsp;</div>
            <form method="post" action="">
                <input type="hidden" name="_chk" value="1">
                <input type="hidden" name="menu_id" id="menu_id" value="<?php echo $id ?>">
               <div class="span4">


                   <?php  if($id == 1){ ?>

                   <div class="control-group">
                       <label class="control-label"><span class="fonts"><?php $tool->trans("Menu Level") ?></span></label>
                       <div class="controls">
                           <select name="menu_level" id="menu_level">
                               <option value="0">Parent</option>
                               <?php
                               $menus = $set->GetMenus($id);
                               $parentMneus = array();
                               foreach($menus as $menu){
                                   if(empty($menu['parent_id'])){
                                       $parentMneus[] = $menu;
                                   }
                               }
                               foreach($parentMneus as $menu){

                               ?>
                               <option value="<?php echo $menu['id'] ?>"><?php echo $menu['title'] ?></option>
                              <?php }
                               ?>
                           </select>
                       </div>
                   </div>

                   <?php } else{ ?>
                    <input type="hidden" name="menu_level" value="0">
                   <?php } ?>

                   <div class="control-group">
                      <label class="control-label"><span class="fonts"><?php $tool->trans("Menu type") ?></span></label>
                      <div class="controls">
                          <select name="menu_type" id="menu_type">
                              <option value="0">Please select</option>
                              <option value="1">Pages</option>
                              <option value="2">Routes</option>
                              <option value="3">Link</option>
                          </select>
                      </div>
                   </div>



                   <div class="control-group" id="pages_container" style="display: none;">
                     <label class="control-label"><span class="fonts"><?php $tool->trans("Pages") ?></span></label>
                     <div class="controls">
                         <select name="pages" id="pages">
                             <option value="0">Please select</option>
                             <?php
                             foreach($sitePages as $sitePage){
                             ?>
                             <option value="<?php echo $sitePage['id'] ?>"><?php echo $sitePage['title'] ?></option>
                             <?php } ?>

                         </select>
                     </div>
                   </div>


                   <div class="control-group" id="routes_container" style="display: none;">
                    <label class="control-label"><span class="fonts"><?php $tool->trans("Routes") ?></span></label>
                    <div class="controls">
                        <select name="routes" id="routes">
                            <option value="0">Please select</option>
                            <?php
                            foreach($routes as $route){
                            ?>
                            <option value="<?php echo $route['id'] ?>"><?php echo $route['title'] ?></option>
                            <?php } ?>

                        </select>
                    </div>
                  </div>



                   <div class="control-group" id="label_container">
                    <label class="control-label"><span class="fonts"><?php $tool->trans("Label") ?></span></label>
                    <div class="controls">
                       <input type="text" name="label" id="label">
                    </div>
                    </div>




                   <div class="control-group" id="link_container" style="display: none;">
                   <label class="control-label"><span class="fonts"><?php $tool->trans("Link") ?></span></label>
                   <div class="controls">
                      <input type="text" name="link" id="link">
                   </div>
                   </div>


                   <div class="control-group" id="position_container">
                      <label class="control-label"><span class="fonts"><?php $tool->trans("Position") ?></span></label>
                      <div class="controls">
                         <input type="text" name="position" id="position">
                      </div>
                      </div>


                   <div class="control-group" >
                   <label class="control-label">&nbsp;</label>
                   <div class="controls">
                      <input type="submit" value="Save" class="btn btn-success" />
                   </div>
                   </div>





               </div>

            </form>

           </div>

    </div>

</div>

<script type="text/javascript">

    function deleteRequest(id){

          var getmenu = makeJsLink("ajax","settings");
          var currentMenuId = $("#menu_id").val();
          var loaderImage = $("#amgloader");
          loaderImage.show();
          var data = 'ajax_request=delete_menu&id=' + id;
          $.ajax({
              type: "POST",
              url: getmenu,
              data: data,
              async: false,
              success: function (data) {
                  if(data == 1){
                      loaderImage.hide();

                      location.href= makeJsLink("mainpage","menubuilder&id="+currentMenuId);

                      return false;
                  }
                  else{
                      loaderImage.hide();
                      alert("Error!");
                  }
              }
          })



    }


    $(document).ready(function(){


        $("#nestable li").click(function(event){
            event.stopPropagation();
            deleteRequest($(this).attr("data-id"));
        });


        $("#menu_type").change(function(){
            var menuType = $(this).val();

             if(menuType == 1){
                 $("#pages_container").show();
                    setPagesVal();
             }
             else{
                 $("#pages_container").hide();
                 setLableNull();
             }

            if(menuType == 2){
                 $("#routes_container").show();
                 setRoutesVal();

             }
             else{
                 $("#routes_container").hide();
                setLableNull();
             }


            if(menuType == 3){
                $("#link_container").show();
            }
            else{
                $("#link_container").hide();
            }





        });
    });

function setDropDownText(id){
    var dataLabel = id.find(":selected").text();
    $("#label").val(dataLabel);
}

function setPagesVal(){
    $("#pages").change(function(){
        var pageId = $(this).val();
        if(pageId > 0){
            setDropDownText($(this));
        }
    });
}

function setLableNull(){
    $("#label").val("");
}

function setRoutesVal(){
    $("#routes").change(function(){
        var routeId = $(this).val();
        if(routeId > 0){
            setDropDownText($(this));
        }
    });
}
</script>
<?php

$tpl->footer();