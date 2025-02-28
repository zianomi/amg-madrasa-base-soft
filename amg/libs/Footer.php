<?php

/**
 * Created by PhpStorm.
 * User: zia
 * Date: 1/14/2017
 * Time: 11:42 PM
 */
class Footer
{

    public function renderFooter(){
        $html = '';
        $home = Tools::getUrl();
        $link = $home . "/?menu=ajax&page=editprofile";


        $html .= '

<!--div with class mian closing-->
</div>
<!--div with class mian closing-->


<!--div with class container-fluid-->
      </div>
<!--div with class container-fluid-->

<!--wraper div-->
            </div>
<!--wraper div-->

<footer id="footer" style="position: fixed;
    left: 0;
    bottom: 0;
    height: 20px !important;
    width: 100%;
    text-align: center;">
              <div class="container-fluid">
                <a href="http://www.amgsol.net" target="_blank">© Amg Solutions</a>
              </div>
            </footer>

            <!-- BEGIN SIDEBAR PANEL -->
    <div style="display: none;">
              <ul class="rightPanel">
                <li><a href="'.$link.'"><i class="icon-user"></i><span>My Profile</span></a></li>
                
              </ul>
    </div>



</body>
</html>
            ';

        return $html;
    }

}