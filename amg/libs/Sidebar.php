<?php
/**
 * Created by PhpStorm.
 * User: zia
 * Date: 1/13/2017
 * Time: 12:05 AM
 */

class Sidebar{

    /**
     * @var Template
     */
    private $tpl;

    /**
     * Sidebar constructor.
     */
    public function __construct($obj){
        $this->tpl = $obj;
    }



    public function renderSidebar(){

        if(!$this->tpl->isMobile()){
            return $htmlNew = $this->sidebarContainer("");
        }

        $lang = Tools::getLang();
        $htmPrint = '';
        $ParentMenusA = 0;
        $rowsParentMenus = $this->tpl->parentMenuArray("all", array("published" => "yes"));
        $rowsSubmenuARR = $this->tpl->subMenuArray("menu", array("published" => "yes"));
        $childSubmenuARR = $this->tpl->childMenuArray("menu", array("published" => "yes"));
        $direction = Tools::getDirection();
        $userId = $this->tpl->getUserId();
        $cacheDir = $this->tpl->getCacheDir();
        $menuChaceDir = $cacheDir . DIRECTORY_SEPARATOR . "sidebar";
        $cacheFile = $menuChaceDir . DIRECTORY_SEPARATOR . "sidebar_nva_" . $lang . "_" . $userId . ".html";

        if (file_exists($cacheFile)) {
            $html = file_get_contents($cacheFile);
        }
        else{
        foreach($rowsParentMenus as $rowsParentMenu){
            $ParentMenusA++;

            if(isset($rowsSubmenuARR[$rowsParentMenu['id']])){
                $rowsSubmenus[$rowsParentMenu['id']] = $rowsSubmenuARR[$rowsParentMenu['id']];
            }





            $htmPrint .= '<div class="accordion-group">';
            $htmPrint .= '<div class="accordion-heading">';
            $htmPrint .= '<a class="accordion-toggle" href="#collapse-multi-level'.$rowsParentMenu['id'] . $ParentMenusA.'" data-parent="#accordion2" data-toggle="collapse">';
            if($direction == 'ltr'){
                $htmPrint .= '<i class="icon-angle-right"></i>';
            }else{
                $htmPrint .= '<i class="icon-angle-left"></i>';
            }
            $htmPrint .= '<span class="fonts" style="color: #FFFFFF">'.$rowsParentMenu['title'].'</span>';

            if(isset($rowsSubmenuARR[$rowsParentMenu['id']])) {
                if (count($rowsSubmenus[$rowsParentMenu['id']]) > 0) {
                    $htmPrint .= '<span class="arrow"></span>';
                }
            }


        $htmPrint .= '</a>';
        $htmPrint .= '</div>';
        $htmPrint .= '<ul class="accordion-body nav nav-list sub-menu collapse" id="collapse-multi-level'.$rowsParentMenu['id'] . $ParentMenusA.'">';
        $SubMenusA = 0;



            if(isset($rowsSubmenuARR[$rowsParentMenu['id']])) {
                foreach ($rowsSubmenus[$rowsParentMenu['id']] as $rowsSubmenu) {


                $SubMenusA++;
                if (isset($childSubmenuARR[$rowsSubmenu['id']])) {
                    $rowsChildmenus[$rowsSubmenu['id']] = $childSubmenuARR[$rowsSubmenu['id']];
                }


                $subInChild = '';

                if (isset($rowsChildmenus[$rowsSubmenu['id']]) && count($rowsChildmenus[$rowsSubmenu['id']]) > 0) {
                    $linkSubMenu = 'javascript:void(0)';

                    $subInChild .= '<li>';
                    $linksubInChild = Tools::makeLink($rowsSubmenu['bundle'], $rowsSubmenu['phpfile'], $rowsSubmenu['id'], "list");;
                    $subInChild .= ' <a data-target="#collapse-' . $rowsSubmenu['id'] . '-level" data-toggle="sub-menu-collapse" href="' . $linksubInChild . '">';
                    $subInChild .= '<span class="fonts" style="color: #FFFFFF">' . $rowsSubmenu['title'] . '</span>';
                    $subInChild .= '</a>';
                    $subInChild .= '</li>';

                } else {
                    $linkSubMenu = Tools::makeLink($rowsSubmenu['bundle'], $rowsSubmenu['phpfile'], $rowsSubmenu['id'], "list");
                }


                $htmPrint .= '<li>';
                $htmPrint .= '<a data-target="#collapse-' . $rowsSubmenu['id'] . $SubMenusA . '-level" data-toggle="sub-menu-collapse" href="' . $linkSubMenu . '" class="">';
                $htmPrint .= '<span class="fonts" style="color: #FFFFFF">' . $rowsSubmenu['title'] . '</span>';

                if (isset($rowsChildmenus[$rowsSubmenu['id']]) && count($rowsChildmenus[$rowsSubmenu['id']]) > 0) {
                    $htmPrint .= '<span class="arrow"></span>';
                }

                $htmPrint .= '</a>';
                $htmPrint .= '</li>';
                $htmPrint .= '<ul class="nav nav-list collapse" id="collapse-' . $rowsSubmenu['id'] . $SubMenusA . '-level">';
                $SubChildA = 0;

                $htmPrint .= $subInChild;
                if (isset($rowsChildmenus[$rowsSubmenu['id']])) {
                    foreach ($rowsChildmenus[$rowsSubmenu['id']] as $rowsChildmenu) {
                        $SubChildA++;
                        $htmPrint .= '<li>';
                        $linkChildMenu = $linkSubMenu = Tools::makeLink($rowsChildmenu['bundle'], $rowsChildmenu['phpfile'], $rowsChildmenu['id'], "list");;
                        $htmPrint .= ' <a data-target="#collapse-' . $rowsChildmenu['id'] . $SubChildA . '-level" data-toggle="sub-menu-collapse" href="' . $linkChildMenu . '">';
                        $htmPrint .= '<span class="fonts" style="color: #FFFFFF">' . $rowsChildmenu['title'] . '</span>';
                        $htmPrint .= '</a>';
                        $htmPrint .= '</li>';
                    } // end child menu
                }

                $htmPrint .= '</ul>';

                } // end sub menu
            }
            $htmPrint  .= '</ul>';
        $htmPrint .= '</div>';

        } // end parent menu



        $htmlNew = $this->sidebarContainer($htmPrint);



        $html = $htmlNew;

            if(!file_exists($menuChaceDir)){
                mkdir($menuChaceDir,0777,true);
            }

            file_put_contents($cacheFile,$html,LOCK_EX);

        }


        return $html;
    }

    public function sidebarContainer($htmPrint){
        $htmlNew = '<aside class="social-sidebar">';

        $htmlNew .= $this->userSettings();

        $htmlNew .= '<div class="social-sidebar-content">';
        $htmlNew .= '<div class="scrollable">';
        $htmlNew .= $this->userInfo();
        $htmlNew .= $this->NavigationSidebar();
        $htmlNew .= $this->searchSidebar();

        $htmlNew .= '<section class="menu">';

        $htmlNew .= '<div class="accordion-group">';
        $htmlNew .= '<div class="accordion-heading">';
        $htmlNew .= '<a class="accordion-toggle" href="'.Tools::getUrl().'">';
        $htmlNew .= '<span class="fonts" style="color: #FFFFFF"><i class="icon-home"></i></span>';



        $htmlNew .= '</a>';
        $htmlNew .= '</div>';
        $htmlNew .= '</div>';

        $htmlNew .= $htmPrint;


        $htmlNew .= '</section>';
        $htmlNew .= '</div>';
        $htmlNew .= '</div>';
        $htmlNew .= '</aside>';

        return $htmlNew;
    }


    public function userSettings(){

        $html = '<div class="user-settings">
            <div class="arrow"></div>
            <h3 class="user-settings-title">Settings shortcuts</h3>
            <div class="user-settings-content">
                <a href="basic-user-profile.html">
                    <div class="icon">
                        <i class="icon-user"></i>
                    </div>
                    <div class="title">My Profile</div>
                    <div class="content">View your profile</div>
                </a>
                <a href="chat-inbox.html">
                    <div class="icon">
                        <i class="icon-envelope"></i>
                    </div>
                    <div class="title">View Messages</div>
                    <div class="content">You have <strong>17</strong> new messages</div>
                </a>
                <a href="#view-pending-tasks">
                    <div class="icon">
                        <i class="icon-tasks"></i>
                    </div>

                    <div class="title">View Tasks</div>
                    <div class="content">You have <strong>8</strong> pending tasks</div>
                </a>
            </div>
            <div class="user-settings-footer">
                <a href="#more-settings">See more settings</a>
            </div>
        </div>';

        return $html;
    }

    public function userInfo(){

        $userName = Tools::getUserName();
        $html = '<div class="user">
          <img class="avatar" width="25" height="25" src="assets/img/default_user.jpg" alt="Julio Marquez">
          <span>'.$userName.'</span>
          <i class="icon-user trigger-user-settings"></i>
        </div>';

        return $html;
    }

    public function NavigationSidebar(){
        $html = '<div class="navigation-sidebar">
            <i class="switch-sidebar-icon icon-align-justify"></i>
       </div>';

        return $html;
    }

    public function searchSidebar(){
        return "";
        $html = '<div class="search-sidebar">
                          <img src="assets/img/icons/stuttgart-icon-pack/32x32/search.png" alt="Search">
          <form class="search-sidebar-form">
            <input type="text" class="search-query input-block-level" placeholder="Search">
          </form>
        </div>';
        return $html;
    }


}