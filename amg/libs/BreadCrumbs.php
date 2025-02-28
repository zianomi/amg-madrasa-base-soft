<?php

/**
 * Created by PhpStorm.
 * User: zia
 * Date: 1/14/2017
 * Time: 11:35 PM
 */
class BreadCrumbs
{





    function renderBreadCrumbs($dashboard,$menuLink,$menuName,$pageName){

        $direction = Tools::getDirection();
        $home = Tools::getUrl();
        if($direction == 'ltr'){
            $breadLeftRight = 'right';
        }else{
            $breadLeftRight = 'left';
        }
        $html = '<div id="main">';
        $html .= '<div class="container-fluid">';
        $html .= '<div class="row-fluid" style="margin-top: 30px">';
        $html .= '<div class="span12">';
        $html .= '<ul class="breadcrumb">';
        $html .= '<li>';
        $html .= '<i class="icon-home"></i>&nbsp;';
        $html .= '<a href="'.$home.'"><span class="fonts">'.$dashboard.'</span></a>';
        $html .= '&nbsp;<span class="icon-angle-'.$breadLeftRight.'"></span>';
        $html .= '</li>';
        if(!empty($menuLink) && !empty($menuName)){
            $html .= '<li><a href="'.$menuLink.'">&nbsp;<span class="fonts">'.$menuName.'</span></a>&nbsp;<span class="icon-angle-'.$breadLeftRight.'"></span></li>';
        }
        if(!empty($pageName)){
            $html .= '&nbsp;<li><a href="javascript:void(0);">&nbsp;<span class="fonts">'.$pageName.'</span></a></li>';

        }

        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<style>body{opacity: 1}.wraper {display: block;}#amgloader {display: none;}</style>';

        if(isset($_SESSION['msg'])){
            $html .= $_SESSION['msg'];
            unset($_SESSION['msg']);
        }


        return $html;

    }
}