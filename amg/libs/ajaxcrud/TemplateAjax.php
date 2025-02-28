<?php
/**
 * Created by PhpStorm.
 * User: mzia
 * Date: 9/12/18
 * Time: 5:06 PM
 */


require_once __DIR__ . DIRECTORY_SEPARATOR . "Csv.php";
class TemplateAjax extends Csv{

    protected $ajaxcrud_root;
    protected $css_file;
    protected $css = true; 	//indicates a css spredsheet WILL be used
    protected $table_html; //the html for the table (to be modified on ADD via ajax)
    protected $cellspacing;
    protected $includeJQuery 	= true; //include jquery (default)
    protected $includeTableHeaders = true; //include table headers (default)
    //table border - default is off: 0
    protected $border;
    protected $loading_image_html;
    protected $printButton = true;
    protected $customButton;
    protected $customButtonStatus = false;
    protected $showFileDelete = true;


    public function __construct()
    {
        parent::__construct();
        $this->css = true;
        $this->border = 0;
    }


    /**
     * @return string
     */
    public function getAjaxcrudRoot()
    {
        return $this->ajaxcrud_root;
    }

    /**
     * @param string $ajaxcrud_root
     */
    public function setAjaxcrudRoot($ajaxcrud_root)
    {
        $this->ajaxcrud_root = $ajaxcrud_root;
        $this->loading_image_html = "<center><br /><br  /><img src=\'" . $this->ajaxcrud_root . "css/loading.gif\'><br /><br /></center>"; //changed via setLoadingImageHTML()
    }

    protected function setCSSFile($css_file){
        $this->css_file = $css_file;
    }

    public function disableJQuery() {
        $this->includeJQuery = false;
    }

    public function disableTableHeaders() {
        $this->includeTableHeaders = false;
    }

    public function addTableBorder(){
        $this->border = 1;
    }

    public function setLoadingImageHTML($html){
        $this->loading_image_html = $html;
    }

    /**
     * @return boolean
     */
    public function isPrintButton()
    {
        return $this->printButton;
    }

    /**
     * @param boolean $printButton
     */
    public function setPrintButton($printButton)
    {
        $this->printButton = $printButton;
    }

    /**
     * @return mixed
     */
    public function getCustomButton()
    {
        return $this->customButton;
    }

    /**
     * @param mixed $customButton
     */
    public function setCustomButton($customButton)
    {
        $this->customButton = $customButton;
    }

    /**
     * @return boolean
     */
    public function isCustomButtonStatus()
    {
        return $this->customButtonStatus;
    }

    /**
     * @param boolean $customButtonStatus
     */
    public function setCustomButtonStatus($customButtonStatus)
    {
        $this->customButtonStatus = $customButtonStatus;
    }

    /**
     * @return boolean
     */
    public function isShowFileDelete()
    {
        return $this->showFileDelete;
    }

    /**
     * @param boolean $showFileDelete
     */
    public function setShowFileDelete($showFileDelete)
    {
        $this->showFileDelete = $showFileDelete;
    }

}