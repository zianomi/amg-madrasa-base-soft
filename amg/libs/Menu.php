<?php

/**
 * Created by PhpStorm.
 * User: zia
 * Date: 1/14/2017
 * Time: 10:14 PM
 */
class Menu
{

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





    public function menuLinks(){

        $rowsParentMenus = $this->tpl->parentMenuArray("all", array("published" => "yes"));
        $rowsSubmenuARR = $this->tpl->subMenuArray("menu", array("published" => "yes"));
        $childSubmenuARR = $this->tpl->childMenuArray("menu", array("published" => "yes"));
        $html = '';
        if (count($rowsParentMenus) > 0) {
            $html .= '<ul class="nav visible-desktop">';
            foreach ($rowsParentMenus as $row_main_menu) {

                $html .= '<li class="dropdown visible-desktop">';
                $html .= '<a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">';
                $html .= '<span class="fonts">' . $row_main_menu["title"] . '</span>';

                if (isset($rowsSubmenuARR[$row_main_menu["id"]])) {
                    if (count($rowsSubmenuARR[$row_main_menu["id"]]) > 0) {
                        $html .= '<b class="caret"></b>';
                    }
                }

                $html .= '</a>';

                if (isset($rowsSubmenuARR[$row_main_menu["id"]])) {
                    if (count($rowsSubmenuARR[$row_main_menu["id"]]) > 0) {
                        $html .= '<ul class="dropdown-menu">';
                        foreach ($rowsSubmenuARR[$row_main_menu["id"]] as $row_sub_menu) {


                            $html .= '<li class="dropdown';

                            if (isset($childSubmenuARR[$row_sub_menu['id']]) && count($childSubmenuARR[$row_sub_menu['id']]) > 0) {
                                $html .= '-submenu">';
                            } else {
                                $html .= '">';
                            }

                            $linkSubMenu = Tools::makeLink($row_sub_menu['bundle'], $row_sub_menu['phpfile'], $row_sub_menu['id'], "list");
                            $html .= '<a href="' . $linkSubMenu . '">';
                            $html .= '<span class="fonts">' . $row_sub_menu["title"] . '</span>';
                            $html .= '</a>';

                            if (isset($childSubmenuARR[$row_sub_menu['id']])) {
                                if (count($childSubmenuARR[$row_sub_menu['id']]) > 0) {
                                    $html .= '<ul class="dropdown-menu">';

                                    foreach ($childSubmenuARR[$row_sub_menu['id']] as $row_child_menu) {

                                        $html .= '<li>';
                                        $linkChildMenu = Tools::makeLink($row_child_menu['bundle'], $row_child_menu['phpfile'], $row_child_menu['id'], "list");
                                        $html .= '<a href="' . $linkChildMenu . '"><span class="fonts">' . $row_child_menu["title"] . '</span></a>';
                                        $html .= '</li>';
                                    }

                                    $html .= '</ul>';
                                }
                            }
                            $html .= '</li>';
                        }
                        $html .= '</ul>';
                    }
                }
                $html .= '</li>';
            }

            $html .= '</ul>';


        }

        return $html;
    }


    public function renderMenu()
    {

        $lang = Tools::getLang();
        $userId = $this->tpl->getUserId();
        $cacheDir = $this->tpl->getCacheDir();
        $direction = Tools::getDirection();



        $menuChaceDir = $cacheDir . DIRECTORY_SEPARATOR . "menu";
        $cacheFile = $menuChaceDir . DIRECTORY_SEPARATOR . "header_nva_" . $lang . "_" . $userId . ".html";

        if (file_exists($cacheFile)) {
            $html = file_get_contents($cacheFile);

        } else {


            $html = '<header>';
            $html .= '<nav class="navbar navbar-fixed-top social-navbar social-sm">';
            $html .= '<div class="navbar-inner">';
            $html .= '<div class="container-fluid">';
            $html .= '<a class="btn btn-navbar" data-toggle="collapse" data-target=".social-sidebar">';
            $html .= '<span class="icon-bar"></span>';
            $html .= '<span class="icon-bar"></span>';
            $html .= '<span class="icon-bar"></span>';
            $html .= '</a>';
            $html .= '<a class="brand" href="' . Tools::makeLink("", "", "", "") . '"><i class="icon-home"></i></a>';



            if(!$this->tpl->isMobile()){
                $html .= $this->menuLinks();
            }



        if ($direction == 'ltr') {
            $html .= '<ul class="nav pull-right nav-indicators">';
        } else {
            $html .= '<ul class="nav pull-left nav-indicators">';
        }
        $html .= '<li class="divider-vertical"></li>';
        $html .= '<li class="dropdown">';
        $html .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-caret-down"></i></a>';
        $html .= '<ul class="dropdown-menu">';
        $html .= ' <li><a href="' . Tools::makeLink("settings", "langchange&lang=en", "", "") . '"><i class="icon-cogs"></i> English</a></li>';
        $html .= '<li class="divider"></li>';
        //$html .= ' <li><a href="'.Tools::makeLink("settings","langchange&lang=ar","","").'"><i class="icon-cogs"></i>Arabic</a></li>';
        //$html .= '<li class="divider"></li>';
        $html .= ' <li><a href="' . Tools::makeLink("settings", "langchange&lang=ur", "", "") . '"><i class="icon-cogs"></i> اردو</a></li>';
        $html .= '<li class="divider"></li>';
        $html .= '<li><a href="' . Tools::makeLink("settings", "logout", "", "") . '"><i class="icon-off"></i> Log Out</a></li>';
        //$html .= '<li class="divider"></li>';
        $html .= '</ul>';
        $html .= '</li>';
        $html .= '</ul>';
        $html .= $this->otherHeader();

        //include other headers(li between this ul)
        //$html .= '<ul class="nav pull-right nav-indicators"></ul>';

        $html .= '</div>';
        $html .= '</div>';
        $html .= '</nav>';
        $html .= '</header>';

            if(!file_exists($menuChaceDir)){
                mkdir($menuChaceDir,0777,true);
            }

            file_put_contents($cacheFile,$html,LOCK_EX);



        }




        //return $html;
        return $html;

    }


    public function otherHeader()
    {

        $direction = Tools::getDirection();

        if ($direction == 'ltr') {
            $html = '<ul class="nav pull-right nav-indicators">';
        } else {
            $html = '<ul class="nav pull-left nav-indicators">';
        }

        //$html .= $this->taskDropDown();
        //$html .= $this->navMessage();
        $html .= '</ul>';

        return $html;
    }

    public function taskDropDown()
    {
        $html = '<li class="dropdown nav-tasks">
          <!-- BEGIN DROPDOWN TOGGLE -->
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <span class="badge">13</span>
            <i class="icon-tasks"></i>
          </a>
          <!-- END DROPDOWN TOGGLE -->
          <!-- BEGIN DROPDOWN MENU -->
          <ul class="dropdown-menu">
            <!-- BEGIN DROPDOWN HEADER -->
            <li class="nav-taks-header">
              <a tabindex="-1" href="#">You have <strong>13</strong> tasks in progress</a>
            </li>
            <!-- END DROPDOWN HEADER -->
            <!-- BEGIN DROPDOWN ITEMS -->
            <li>
              <a>
                <strong>Prepare Report</strong><span class="pull-left">30%</span>
                <div class="progress progress-danger active">
                    <div class="bar" style="width: 30%;"></div>
                </div>
              </a>
            </li>
            <li>
              <a>
                <strong>Make new update</strong><span class="pull-left">40%</span>
                <div class="progress progress-info active">
                    <div class="bar" style="width: 40%;"></div>
                </div>
              </a>
            </li>
            <li>
              <a>
                <strong>Fix critical bugs</strong><span class="pull-left">80%</span>
                <div class="progress progress-striped active">
                    <div class="bar" style="width: 80%;"></div>
                </div>
              </a>
            </li>
            <li>
              <a>
                <strong>Complete project</strong><span class="pull-left">5%</span>
                <div class="progress progress-success active">
                    <div class="bar" style="width: 5%;"></div>
                </div>
              </a>
            </li>
            <li>
              <a>
                <strong>Others</strong><span class="pull-left">15%</span>
                <div class="progress progress-warning active">
                    <div class="bar" style="width: 15%;"></div>
                </div>
              </a>
            </li>
            <!-- END DROPDOWN ITEMS -->
            <!-- BEGIN DROPDOWN FOOTER -->
            <li class="nav-taks-footer">
              <a tabindex="-1" href="#">View all tasks
              </a>
            </li>
            <!-- END DROPDOWN FOOTER -->
          </ul>
          <!-- END DROPDOWN MENU -->
        </li>';

        return $html;
    }


    public function navMessage()
    {
        $webUrl = Tools::getWebUrl();

        $html = ' <li class="dropdown nav-messages">
              <!-- BEGIN DROPDOWN TOGGLE -->
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <span class="badge">8</span>
                <i class="icon-envelope"></i>
              </a>
              <!-- END DROPDOWN TOGGLE -->
              <!-- BEGIN DROPDOWN MENU -->
              <ul class="dropdown-menu">
                <!-- BEGIN DROPDOWN HEADER -->
                <li class="nav-messages-header">
                  <a tabindex="-1" href="#">You have <strong>8</strong> new messages</a>
                </li>
                <!-- END DROPDOWN HEADER -->
                <!-- BEGIN DROPDOWN ITEMS -->
                                                <li class="nav-message-body">
                  <a>
                      <img src="' . $webUrl . '/img/people-face/user1_55.jpg" alt="User">
                      <div>
                        <small class="pull-left">Just Now</small>
                        <strong>Yadra Abels</strong>
                      </div>
                      <div>
                        Lorem ipsum dolor sit amet, consectetur...
                      </div>
                  </a>
                </li>
                                <li class="nav-message-body">
                  <a>
                      <img src="' . $webUrl . '/img/people-face/user2_55.jpg" alt="User">
                      <div>
                        <small class="pull-left">Just Now</small>
                        <strong>Cesar Mendoza</strong>
                      </div>
                      <div>
                        Lorem ipsum dolor sit amet, consectetur...
                      </div>
                  </a>
                </li>
                                <li class="nav-message-body">
                  <a>
                      <img src="' . $webUrl . '/img/people-face/user3_55.jpg" alt="User">
                      <div>
                        <small class="pull-left">Just Now</small>
                        <strong>John Doe</strong>
                      </div>
                      <div>
                        Lorem ipsum dolor sit amet, consectetur...
                      </div>
                  </a>
                </li>
                                <li class="nav-message-body">
                  <a>
                      <img src="' . $webUrl . '/img/people-face/user4_55.jpg" alt="User">
                      <div>
                        <small class="pull-left">Just Now</small>
                        <strong>Tobei Tsumura</strong>
                      </div>
                      <div>
                        Lorem ipsum dolor sit amet, consectetur...
                      </div>
                  </a>
                </li>
                                <!-- END DROPDOWN ITEMS -->
                <!-- BEGIN DROPDOWN FOOTER -->
                <li class="nav-messages-footer">
                  <a tabindex="-1" href="javascript:void(0);">View all messages
                  </a>
                </li>
                <!-- END DROPDOWN FOOTER -->
              </ul>
              <!-- END DROPDOWN MENU -->
            </li>';

        return $html;
    }

}