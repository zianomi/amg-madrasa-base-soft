<?php

/**
 * Created by PhpStorm.
 * User: mzia
 * Date: 1/16/2017
 * Time: 5:53 PM
 */
class QueryTemplate extends Template
{


    private $formMethod = "get";
    private $formAction = "";
    private $formClass = "formular";
    private $formId = "amg_form";
    private $fileUpload = false;
    private $formTargetBlank = false;
    private $cols = array();
    private $data = array();
    private $addLink = "";
    private $action = false;
    private $startCheckBox = false;
    private $dynamicParam = array();
    private $customActions = array();
    private $format_field_with_function = array();
    private $anchorDataId;
    private $checBoxparam = array();
    private $removeCsvCols = array();
    private $postFormAttribute = "";
    private $showSerialNumber = false;
    private $pageHeading = "Search Data";
    private $addLinkButtonText = "Add Record";


    public function __construct()
    {
        global $tool;
        parent::__construct($tool);
    }

    public function formatFieldWithFunction($field, $function_name){
        $this->format_field_with_function[$field] = $function_name;
    }

    /**
     * @return string
     */
    public function getFormMethod()
    {
        return $this->formMethod;
    }

    /**
     * @param string $formMethod
     */
    public function setFormMethod($formMethod)
    {
        $this->formMethod = $formMethod;
    }

    /**
     * @return string
     */
    public function getFormAction()
    {
        return $this->formAction;
    }

    /**
     * @param string $formAction
     */
    public function setFormAction($formAction)
    {
        $this->formAction = $formAction;
    }

    /**
     * @return string
     */
    public function getFormClass()
    {
        return $this->formClass;
    }

    /**
     * @param string $formClass
     */
    public function setFormClass($formClass)
    {
        $this->formClass = $formClass;
    }

    /**
     * @return string
     */
    public function getFormId()
    {
        return $this->formId;
    }

    /**
     * @param string $formId
     */
    public function setFormId($formId)
    {
        $this->formId = $formId;
    }

    /**
     * @return boolean
     */
    public function isFormTargetBlank()
    {
        return $this->formTargetBlank;
    }

    /**
     * @param boolean $formTargetBlank
     */
    public function setFormTargetBlank($formTargetBlank)
    {
        $this->formTargetBlank = $formTargetBlank;
    }



    /**
     * @return string
     */
    public function getPageHeading()
    {
        return $this->pageHeading;
    }

    /**
     * @param string $pageHeading
     */
    public function setPageHeading($pageHeading)
    {
        $this->pageHeading = $pageHeading;
    }




    /**
     * @return array
     */
    public function getCols()
    {
        return $this->cols;
    }

    /**
     * @param array $cols
     */
    public function setCols($cols)
    {
        $this->cols = $cols;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getAddLink()
    {
        return $this->addLink;
    }

    /**
     * @param string $addLink
     */
    public function setAddLink($addLink)
    {
        $this->addLink = $addLink;
    }

    /**
     * @return boolean
     */
    public function isAction()
    {
        return $this->action;
    }

    /**
     * @param boolean $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getAddLinkButtonText()
    {
        return $this->addLinkButtonText;
    }

    /**
     * @param string $addLinkButtonText
     */
    public function setAddLinkButtonText($addLinkButtonText)
    {
        $this->addLinkButtonText = $addLinkButtonText;
    }

    /**
     * @return boolean
     */
    public function isStartCheckBox()
    {
        return $this->startCheckBox;
    }

    /**
     * @param boolean $startCheckBox
     */
    public function setStartCheckBox($startCheckBox)
    {
        $this->startCheckBox = $startCheckBox;
    }


    /**
     * @return array
     */
    public function getDynamicParam()
    {
        return $this->dynamicParam;
    }

    /**
     * @param array $dynamicParam
     */
    public function setDynamicParam($dynamicParam)
    {
        $this->dynamicParam = $dynamicParam;
    }





    /**
     * @return array
     */
    public function getCustomActions()
    {
        return $this->customActions;
    }

    /**
     * @param array $customActions
     */
    public function setCustomActions($customActions)
    {
        $this->customActions = $customActions;
    }





    /**
     * @return array
     */
    public function getChecBoxparam()
    {
        return $this->checBoxparam;
    }

    /**
     * @param array $checBoxparam
     */
    public function setChecBoxparam($checBoxparam)
    {
        $this->checBoxparam = $checBoxparam;
    }

    /**
     * @return array
     */
    public function getRemoveCsvCols()
    {
        return $this->removeCsvCols;
    }

    /**
     * @param array $removeCsvCols
     */
    public function setRemoveCsvCols($removeCsvCols)
    {
        $this->removeCsvCols = $removeCsvCols;
    }

    /**
     * @return string
     */
    public function getPostFormAttribute()
    {
        return $this->postFormAttribute;
    }

    /**
     * @param string $postFormAttribute
     */
    public function setPostFormAttribute($postFormAttribute)
    {
        $this->postFormAttribute = $postFormAttribute;
    }

    /**
     * @return mixed
     */
    public function getAnchorDataId()
    {
        return $this->anchorDataId;
    }

    /**
     * @param mixed $anchorDataId
     */
    public function setAnchorDataId($anchorDataId)
    {
        $this->anchorDataId = $anchorDataId;
    }

    /**
     * @return boolean
     */
    public function isFileUpload()
    {
        return $this->fileUpload;
    }

    /**
     * @param boolean $fileUpload
     */
    public function setFileUpload($fileUpload)
    {
        $this->fileUpload = $fileUpload;
    }

    /**
     * @return bool
     */
    public function isShowSerialNumber()
    {
        return $this->showSerialNumber;
    }

    /**
     * @param bool $showSerialNumber
     */
    public function setShowSerialNumber($showSerialNumber)
    {
        $this->showSerialNumber = $showSerialNumber;
    }










    public function getParentObject(){
        global $tpl;
        return $tpl;
    }









    public function boxButtons(){
        $tpl = $this->getParentObject();
        //$currentPage = $tpl->getCurrentPage() . $tpl->getParams();
        $currentPage = $tpl->CurrenPageUrl();
        $exportLink = $currentPage . "&export_csv=1";



        $html = "";
        $html .= '<div class="header">';
        if(!empty($tpl->getPageTitle())){
            $html .= '<h4 class="fonts">' . $tpl->getPageTitle() . '</h4>';
        }
        else{
            $html .= '<h4>' . $this->getPageHeading() . '</h4>';
        }
        $html .= '<div class="tools">';
        $html .= '<div class="btn-group">';
        $canExport = $tpl->isCanExport();
        $canPrint = $tpl->isCanPrint();
        $canAdd = $tpl->isCanAdd();
        $canAddLink = $this->getAddLink();
        $addLinkText = $this->getAddLinkButtonText();
        $showJsExport = $tpl->isShowJsExport();
        /*if($canExport){

            $html .= '<a href="'.$exportLink.'" class="btn btn-primary">Export <i class="icon-arrow-right"></i></a>';
        }

        if($canPrint){
           $html .= '<button class="btn btn-info" data-toggle="collapse" data-target="#advanced-search" onclick="printSpecial()">Print <i class="icon-print"></i></button>';
        }
        if($canAdd && !empty($canAddLink)){
            $html .= '<a href="'.$canAddLink.'" class="btn">'.$addLinkText.' <i class="icon-edit"></i></a>';
        }*/

        if($canPrint){
            $html .= '<button class="btn btn-info" data-toggle="collapse" data-target="#advanced-search" onclick="printSpecial()">Print <i class="icon-print"></i></button>';
        }

        if( ($canExport) && (!$showJsExport) ){
            $html .= '<a href="'.$exportLink.'" class="btn btn-primary">Export <i class="icon-arrow-right"></i></a>';
        }


        if(isset($_GET['_chk'])==1){
            if($showJsExport){
                $html .= $this->blankIframeWithoutEcho();
                $html .= '<a href="javascript:void(0)" class="btn btn-primary" id="btnExport" onclick="fnExcelReport();">Export<i class="icon-arrow-right"></i></a>';
            }




        }





        if($canAdd && !empty($canAddLink)){
            $html .= '<a href="'.$canAddLink.'" class="btn">'.$addLinkText.' <i class="icon-edit"></i></a>';
        }

        if($this->isShowSearchButton()){
            $html .= '<button class="btn btn-success" data-toggle="collapse" data-target="#advanced-search">Search <i class="icon-filter"></i></button>';
        }



        //$html .= '<button class="btn btn-success" data-toggle="collapse" data-target="#advanced-search">Search <i class="icon-filter"></i></button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;

    }




    public function contentHtml()
    {

        $html = '';
        $tpl = $this->getParentObject();

        $total = count($this->getData());

        if($this->isStartCheckBox()){
            $formAttributes = $this->getPostFormAttribute();

            $html .= '<form '.$formAttributes.'>';
            $html .= $tpl->formHidden();
        }
        $html .= '

<div id="printReady">
<div class="body">



                <div class="alert alert-info">
                    <strong>Total Record</strong> '.$total.'</div>
                <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
                <table class="table table-bordered table-striped table-hover flip-scroll">
                    <thead>
                    <tr>';

                        if($this->isShowSerialNumber()){
                            $html .= '<th>S#</th>';
                        }

                        if($this->isStartCheckBox()){
                            $html .= '<th><input type="checkbox" onclick="checkAll(this)"></th>';
                        }



                        foreach($this->cols as $cols){
                            $html .= '<th class="fonts">'.$cols.'</th>';
                        }



                        if($this->isAction()){
                            $html .= '<th class="no-print">Action</th>';
                        }

                        $html .= '

                    </tr>
                    </thead>


                    <tbody>';

                        $checkBoxParamArr = $this->getChecBoxparam();
                        $dynamicParam = $this->getDynamicParam();
                        $passedData = $this->getData();
                        $params = array();
                        $ij=0;
                        foreach($passedData as $data) {

                            $ij++;

                            $html .= '<tr>';


                            if($this->isShowSerialNumber()){
                                $html .= '<td>'.$ij.'</td>';
                            }


                            if(!empty($dynamicParam)){
                                foreach($dynamicParam as $keys){

                                    if(isset($data[$keys])){
                                        $params[$keys] = "&" . $keys . "=" . $data[$keys];
                                    }
                                }
                            }

                            $link = "";
                            if(!empty($params)){
                                $link = implode("",$params);
                            }


                            if($this->isStartCheckBox()){
                                /*$valAdd = "";
                                if(!empty($vals)){
                                    if(isset($data[$vals])){
                                        $valAdd = $data[$vals];
                                    }
                                }*/
                                $checkValArr = "";
                            $checkVal = "";
                            if(!empty($checkBoxParamArr)){

                                foreach($checkBoxParamArr as $checkBoxParam){
                                    $checkValArr .= "[" . $data[$checkBoxParam] . "]";
                                    $checkVal .= $data[$checkBoxParam];
                                }
                            }

                                $html .= '<td class="fonts"><input type="checkbox" name="select_all'.$checkValArr.'" checked="checked" value="'.$checkVal.'">';
                            }

                            $printTdVal = "";

                            foreach (array_keys($this->cols) as $cols) {
                                if (isset($this->format_field_with_function[$cols]) && $this->format_field_with_function[$cols] != ''){
                                    $printTdVal = call_user_func($this->format_field_with_function[$cols], $data[$cols]);
                                    //$html .= '<td class="fonts">' . call_user_func($this->format_field_with_function[$cols], $data[$cols]) . '</td>';
                                }
                                elseif($cols == "date"){
                                    $printTdVal = $this->ChangeDateFormat($data[$cols]);
                                    //$html .= '<td class="fonts">' . $this->ChangeDateFormat($data[$cols]) . '</td>';

                                }
                                else{
                                    $printTdVal = $data[$cols];

                                }

                                $html .= '<td class="fonts">' . $printTdVal . '</td>';

                            }


                            $customActions = $this->getCustomActions();
                            $getAnchorDataId = $this->getAnchorDataId();
                            $dataId = "";

                            if(isset($data[$getAnchorDataId])){
                                $dataId = " data-id='".$data[$getAnchorDataId]."'";
                            }

                            $linkHtmlArr = array();
                            $anchorClass = "";
                            $linkToPass = "javascript:void(0);";

                            foreach($customActions as $customAction){
                                /*if($this->isSetLinkToVoid()){

                                }
                                else{
                                    $linkToPass = $customAction['link'].$link;
                                }*/

                                if(isset($customAction['class'])){
                                    $anchorClass = $customAction['class'];
                                }

                                if(isset($customAction['link']) && !empty($customAction['link'])){
                                    $linkToPass = $customAction['link'] . $link;
                                }

                                $linkHtmlArr[] = "<a href='".$linkToPass."' ".$anchorClass."".$dataId.">".$customAction['label']."</a>" ;
                            }

                            $linkHtml = "";
                            if(!empty($linkHtmlArr)){
                                $linkHtml .= implode("&nbsp;&nbsp",$linkHtmlArr);
                            }

                            if($this->isAction()){
                                $html .= '<td class="no-print">'.$linkHtml.'</td>';
                            }
                            $html .= '</tr>';
                        }

                    $html .= '
                       </tbody>

                </table>
                </div>

    </div>
</div>';
        if($this->isStartCheckBox()){
            $html .= '</form>';
        }



 $html .= '
</div>

';

        if($total > 0){
            echo $html;
        }
        else{
            $transArr = $this->getTransObject();
            echo $this->Message("alert",$transArr['no_record_found']);
        }


    }





    public function searchContentBottom()
    {

        $html = '';
        $html .= '</div>';
        $html .= '</form>';
        $html .= '</div>';
        $html .= '</div>';


        echo $html;
    }

    public function searchContentAbove(){
        $formMethod = $this->getFormMethod();
        $formAction = $this->getFormAction();
        $formClass = $this->getFormClass();
        $formId = $this->getFormId();

        $multiPartFormData = '';
        $target = '';

        $tpl = $this->getParentObject();

        $html = '<div class="social-box">';

           $html .= $this->boxButtons();

           $html .= '

        <div class="body">
       <div id="jamia_msg">&nbsp;</div>';
        $html .= '<div id="advanced-search" class="collapse">';
        if($this->isFileUpload()){
            $multiPartFormData = ' enctype="multipart/form-data"';
        }

        if($this->isFormTargetBlank()){
            $target = ' target="_blank"';
        }
        $html .= '<form id="'.$formId.'" name="amg_form" method="'.$formMethod.'" class="'.$formClass.'" action="'.$formAction.'" '.$multiPartFormData.$target.'>';
        $html .= $tpl->formHidden();
        $html .= '<div class="container text-center">';

        echo $html;
    }


    public function exportData(){
        //$export_array = array();
        $data = $this->getCols();
        $removeCsvCols = $this->getRemoveCsvCols();
        if(!empty($removeCsvCols)){
           foreach($removeCsvCols as $removeCsvCol){
               if(isset($data[$removeCsvCol])){
                   unset($data[$removeCsvCol]);
               }
           }
        }
        $export_array[] = array_values($data);



        $data = $this->getData();




        foreach($data as $row){
                if(!empty($removeCsvCols)){
                   foreach($removeCsvCols as $removeCsvCol){
                       if(isset($row[$removeCsvCol])){
                           unset($row[$removeCsvCol]);
                       }
                   }
                }


            foreach (array_keys($this->cols) as $cols) {
                if (isset($this->format_field_with_function[$cols]) && $this->format_field_with_function[$cols] != ''){
                    $printTdVal = call_user_func($this->format_field_with_function[$cols], $row[$cols]);
                }
                elseif($cols == "date"){
                    $printTdVal = $this->ChangeDateFormat($row[$cols]);

                }
                else{
                    $printTdVal = $row[$cols];
                }

                $row[$cols] = $printTdVal;
            }


            $export_array[] = $row;
        }

        $tpl = $this->getParentObject();
        $this->downloadCsvHeaders($tpl->getBundle() . "_" . $tpl->getPhpFile() . "_data_" . date("d_m_Y") . ".csv");
        echo $this->array2csv($export_array);
        exit;

    }







}
