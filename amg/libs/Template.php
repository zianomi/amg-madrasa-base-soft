<?php

/**
 * Created by PhpStorm.
 * User: mzia
 * Date: 1/12/2017
 * Time: 11:38 AM
 */
require dirname(__DIR__) . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR . "SettingModel.php";

class Template
{
    private static $instance;
    private $cssFileDir;
    private $jsFileDir;
    private $cssFileName = "base";
    private $jsFiles = array();
    private $cssFiles = array();
    private $jsFileName = "base";


    private $bundle = "settings";
    private $phpFile = "menus";
    private $fileCode = 1;
    private $fileAction = "list";
    private $maxSearchCol = 4;
    private $errorCode = 404;
    private $canAdd = false;
    private $canEdit = false;
    private $canDelete = false;
    private $canPrint = false;
    public $canExport = false;
    public $pageTitle = "";
    var $transObj;
    private $parentTitle = "";
    private $tool;
    private $showJsExport = false;
    private $showSearchButton = true;

    /**
     * Private ctor so nobody else can instantiate it
     *
     */
    protected function __construct($tools)
    {
        $this->tool = $tools;
    }


    /**
     * Call this method to get singleton
     *
     * @return Template
     */
    public static function getInstance($tool)
    {
        if (null === static::$instance) {
            static::$instance = new Template($tool);
        }
        return static::$instance;
    }


    /**
     * @return int
     */
    public function getMaxSearchCol()
    {
        return $this->maxSearchCol;
    }

    /**
     * @param int $maxSearchCol
     */
    public function setMaxSearchCol($maxSearchCol)
    {
        $this->maxSearchCol = $maxSearchCol;
    }




    /**
     * @return mixed
     */
    public function getCssFileDir()
    {
        return $this->cssFileDir;
    }

    /**
     * @param mixed $cssFileDir
     */
    public function setCssFileDir($cssFileDir)
    {
        $this->cssFileDir = $cssFileDir;
    }

    /**
     * @return mixed
     */
    public function getJsFileDir()
    {
        return $this->jsFileDir;
    }

    /**
     * @param mixed $jsFileDir
     */
    public function setJsFileDir($jsFileDir)
    {
        $this->jsFileDir = $jsFileDir;
    }

    /**
     * @return array
     */
    public function getJsFiles()
    {
        return $this->jsFiles;
    }

    /**
     * @param array $jsFiles
     */
    public function setJsFiles($jsFiles)
    {
        $this->jsFiles = $jsFiles;
    }

    /**
     * @return array
     */
    public function getCssFiles()
    {
        return $this->cssFiles;
    }

    /**
     * @param array $cssFiles
     */
    public function setCssFiles($cssFiles)
    {
        $this->cssFiles = $cssFiles;
    }

    /**
     * @return string
     */
    public function getBundle()
    {
        return $this->bundle;
    }

    /**
     * @param string $bundle
     */
    public function setBundle($bundle)
    {
        $this->bundle = $bundle;
    }

    /**
     * @return string
     */
    public function getPhpFile()
    {
        return $this->phpFile;
    }

    /**
     * @param string $phpFile
     */
    public function setPhpFile($phpFile)
    {
        $this->phpFile = $phpFile;
    }

    /**
     * @return int
     */
    public function getFileCode()
    {
        return $this->fileCode;
    }

    /**
     * @param int $fileCode
     */
    public function setFileCode($fileCode)
    {
        $this->fileCode = $fileCode;
    }

    /**
     * @return string
     */
    public function getFileAction()
    {
        return $this->fileAction;
    }

    /**
     * @param string $fileAction
     */
    public function setFileAction($fileAction)
    {
        $this->fileAction = $fileAction;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param int $errorCode
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
    }

    /**
     * @return boolean
     */
    public function isCanAdd()
    {
        return $this->canAdd;
    }

    /**
     * @param boolean $canAdd
     */
    public function setCanAdd($canAdd)
    {
        $this->canAdd = $canAdd;
    }

    /**
     * @return boolean
     */
    public function isCanEdit()
    {
        return $this->canEdit;
    }

    /**
     * @param boolean $canEdit
     */
    public function setCanEdit($canEdit)
    {
        $this->canEdit = $canEdit;
    }

    /**
     * @return boolean
     */
    public function isCanDelete()
    {
        return $this->canDelete;
    }

    /**
     * @param boolean $canDelete
     */
    public function setCanDelete($canDelete)
    {
        $this->canDelete = $canDelete;
    }

    /**
     * @return boolean
     */
    public function isCanPrint()
    {
        return $this->canPrint;
    }

    /**
     * @param boolean $canPrint
     */
    public function setCanPrint($canPrint)
    {
        $this->canPrint = $canPrint;
    }

    /**
     * @return boolean
     */
    public function isCanExport()
    {
        return $this->canExport;
    }

    /**
     * @param boolean $canExport
     */
    public function setCanExport($canExport)
    {
        $this->canExport = $canExport;
    }

    /**
     * @return string
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    /**
     * @param string $pageTitle
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }

    /**
     * @return string
     */
    public function getParentTitle()
    {
        return $this->parentTitle;
    }

    /**
     * @param string $parentTitle
     */
    public function setParentTitle($parentTitle)
    {
        $this->parentTitle = $parentTitle;
    }






    public function getSettingObj()
    {
        return new SettingModel();
    }

    public function getBaseCss()
    {
        if (Tools::getDirection() == "rtl") {
            $cssFilesArr = array('bootstrap-rtl', 'social-rtl', 'bootstrap-responsive-rtl', 'social.plugins-rtl');
        } else {
            $cssFilesArr = array('bootstrap', 'social', 'bootstrap-responsive', 'social.plugins');
        }

        //$cssFilesArr[] = "social-coloredicons-buttons";
        $cssFilesArr[] = "social-jquery-ui-1.10.0.custom";
        $cssFilesArr[] = "font-awesome";
        $cssFilesArr[] = "datepicker";
        $cssFilesArr[] = "amg";
        $cssFilesArr[] = "demo";
        $cssFilesArr[] = "uipro_style";
        $cssFilesArr[] = "social.theme-blue";
        $cssFilesArr[] = Tools::getLang();

        return $cssFilesArr;
    }

    public function setCss($css = array())
    {
        $this->cssFiles = $css;
    }

    public function getCss()
    {

        if (!empty($this->cssFiles) && is_array($this->cssFiles)) {
            $files = array_merge($this->getBaseCss(), $this->cssFiles);
            $cssFile = $files;
        } else {
            $cssFile = $this->getBaseCss();
        }

        return $cssFile;
    }

    public function setCssFileName($fileName)
    {
        $this->cssFileName = $fileName;
    }

    public function getCssFileName()
    {
        return $this->cssFileName . "_" . Tools::getLang();
    }



    public function getBaseJs()
    {
        $jsFiles = array(
            "jquery.min",
            "jquery-ui-1.10.1.custom.min",
            "bootstrap",
            "jquery.slimscroll.min",
            "uipro.min",
            "jquery.ui.touch-punch",
            "bootstrap-datepicker",
            "bootstrapSwitch",
            "jquery.uniform",
            "form-stuff.elements",
            "ui-elements.jquery-ui",
            "demo-settings",
            "extents",
            "app",
            "sidebar",
            "chosen.jquery",
            "bootstrap-editable",
            "amg"
        );





        return $jsFiles;
    }

    public function setJs($js = array())
    {
        $this->jsFiles = $js;
    }

    public function getJs()
    {

        if (!empty($this->jsFiles)) {
            $files = array_merge($this->getBaseJs(), $this->jsFiles);
            $jsFile = $files;
        } else {
            $jsFile = $this->getBaseJs();
        }

        return $jsFile;
    }

    public function setJsFileName($fileName)
    {
        $this->jsFileName = $fileName;
    }

    public function getJsFileName()
    {
        return $this->jsFileName;
    }




    public function renderError()
    {
        $html = $this->header();
        $html .= $this->sidebar();
        $html .= $this->menu();
        $html .= $this->breadCrumbs();


        require dirname(__DIR__) . DIRECTORY_SEPARATOR . "bundles" . DIRECTORY_SEPARATOR . "settings" . DIRECTORY_SEPARATOR . "ErrorPage.php";

        $errorPage = new ErrorPage();
        $errorPage->setStatusCode($this->getErrorCode());
        $html .= $errorPage->render();

        //$html .= $this->footer();

        echo $html;
    }


    public function ChangeDateFormat($date)
    {
        $tool = $this->getToolObj();
        return $tool->ChangeDateFormat($date);
    }




    public function header()
    {
        $html = '';
        $langCode = Tools::getLang();
        if ($langCode == "en") {
            $siteTitle = "Al-Badar";
        } else {
            $siteTitle = "البدر";
        }
        $html .= '<!DOCTYPE html>
        <html>
          <head>
            <meta charset="utf-8">
            <title>' . $this->getPageTitle() . ' | ' . $siteTitle . '</title>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="description" content="">
            <!--[if lt IE 9]>
              <script src="<?php echo WEB ?>/js/html5shiv.js"></script>
            <![endif]-->

          
            <link rel="icon" type="image/png" href="' . Tools::getWebUrl() . '/img/icon.png">
            ';

        $drs = DIRECTORY_SEPARATOR;
        $cacheDir = Tools::getCacheDir();
        $cssCacheDir = $cacheDir . $drs . "css";
        $cssFileName = $this->getCssFileName();
        $jsFileName = $this->getJsFileName();

        $currentCacheFile = $cssCacheDir . $drs . $cssFileName;

        $jsFileNameFile = $cacheDir . $drs . "js" . $drs . $jsFileName;

        $webUrl = Tools::getWebUrl();
        $direction = Tools::getDirection();


        if (!file_exists($currentCacheFile)) {
            $this->writeCssCache();
        }

        $html .= "\r\n";
        $html .= '<link href="' . $webUrl . '/css/css.php?file=' . $cssFileName . '" rel="stylesheet" type="text/css" media="all" />';

        if (!file_exists($jsFileNameFile)) {
            $this->writeJsCache();
        }


        $html .= "<style>

                  .wraper {display:none;}
                  #amgloader {
                      border: 16px solid #ffffff;
                      border-radius: 50%;
                      border-top: 16px solid blue;
                      border-right: 16px solid green;
                      border-bottom: 16px solid red;
                      width: 60px;
                      height: 60px;
                      -webkit-animation: spin 2s linear infinite;
                      animation: spin 2s linear infinite;
                      position: fixed;
                      z-index: 999;
                      margin: auto;
                      top: 0;
                      left: 0;
                      bottom: 0;
                      right: 0;
                  }
                  @-webkit-keyframes spin {
                      0% {
                          -webkit-transform: rotate(0deg);
                      }
                      100% {
                          -webkit-transform: rotate(360deg);
                      }
                  }
                  @keyframes spin {
                      0% {
                          transform: rotate(0deg);
                      }
                      100% {
                          transform: rotate(360deg);
                      }
                  }
                  body{opacity: 0.5;}
              </style>";

        $html .= "\r\n";
        $html .= '<script type="text/javascript" src="' . $webUrl . '/js/js.php?file=' . $jsFileName . '"></script>';
        $html .= '<script type="text/javascript">var siteUrl = "' . Tools::getUrl() . '"</script>';
        $html .= "\r\n";
        $html .= '</head>';
        $html .= "\r\n";

        if ($direction == 'rtl') {
            $html .= '<body class="rtl">';
        } else {
            $html .= '<body>';
        }

        //$html .= '<img id="amgloader" src="'.Tools::getWebUrl().'/img/loader.gif" style="display:none;" alt="loading..." title="loading..."/>';
        $html .= '<div id="amgloader"></div>';
        $html .= "\r\n";
        $html .= '<div class="wraper">';



        return $html;
    }

    public function writeCssCache()
    {
        $cacheDir = Tools::getCacheDir();
        $drs = DIRECTORY_SEPARATOR;
        $cssCacheDir = $cacheDir . $drs . "css";



        if (!file_exists($cssCacheDir)) {
            mkdir($cssCacheDir, 0777, true);
        }


        $currentCacheFile = $cssCacheDir . $drs . $this->getCssFileName();


        if (!file_exists($currentCacheFile)) {

            //$cssFileDir = $this->getCssFileDir();
            $cssFileDir = CSSPATH;
            $buffer = "";
            $data = "";





            foreach ($this->getCss() as $file) {


                $data .= file_get_contents($cssFileDir . $drs . $file . ".css");
            }




            $buffer .= preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $data);
            $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
            $buffer = str_replace(': ', ':', $buffer);
            $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);

            file_put_contents($currentCacheFile, $buffer);
        }
    }

    public function writeJsCache()
    {

        $cacheDir = Tools::getCacheDir();
        $drs = DIRECTORY_SEPARATOR;
        $jsCacheDir = $cacheDir . $drs . "js";

        if (!file_exists($jsCacheDir)) {
            mkdir($jsCacheDir, 0777, true);
        }

        //$jsFileDir = $this->getJsFileDir();
        $jsFileDir = JSPATH;


        require __DIR__ . DIRECTORY_SEPARATOR . "JsMerger.php";


        $JsMerger = new JsMerger();
        $JsMerger->setJsDir($jsFileDir);
        $JsMerger->setPath($jsCacheDir);
        $JsMerger->setJsFileName($this->getJsFileName());
        $JsMerger->setJsFiles($this->getJs());

        $JsMerger->writeJsFile();
    }

    public function sidebar()
    {





        require __DIR__ . DIRECTORY_SEPARATOR . "Sidebar.php";

        $sidebar = new Sidebar($this);

        return $sidebar->renderSidebar();
    }

    public function menu()
    {
        require __DIR__ . DIRECTORY_SEPARATOR . "Menu.php";
        $menu = new Menu($this);
        return $menu->renderMenu();
    }

    public function breadCrumbs()
    {
        require __DIR__ . DIRECTORY_SEPARATOR . "BreadCrumbs.php";
        $breadCrumbs = new BreadCrumbs();
        $home = Tools::getUrl();
        $link = $home . "/?menu=settings&page=menus&bundle=" . $this->getBundle();
        $dashboardLable = "";
        $langCode = Tools::getLang();
        if ($langCode == "en") {
            $dashboardLable = "Dashboard";
        } else if ($langCode == "ar") {
            $dashboardLable = "الصفحه الاولي";
        } else {
            $dashboardLable = "پہلا صفحہ";
        }

        return $breadCrumbs->renderBreadCrumbs($dashboardLable, $link, $this->getParentTitle(), $this->getPageTitle());
    }

    public function footer()
    {
        require __DIR__ . DIRECTORY_SEPARATOR . "Footer.php";
        $footer = new Footer();
        echo $footer->renderFooter();
    }


    public function renderBeforeContent()
    {
        $html = $this->header();
        $html .= $this->sidebar();
        $html .= $this->menu();
        $html .= $this->breadCrumbs();

        echo $html;
    }

    public function parentMenuArray($type = "all", $param = array())
    {
        $set = $this->getSettingObj();

        $maiMenu = array();
        $whereArr = array("level" => 1, "lang" => Tools::getLangId());

        if (isset($param['published'])) {
            if ($param['published'] == "yes") {
                $whereArr['published'] = "yes";
            }
        }

        if ($type == "menu") {
            $parent = $set->getUserMenus($whereArr);
        } else {
            $parent = $set->getSystemModules($whereArr);
        }


        foreach ($parent as $key) {
            $maiMenu[$key['id']] = array(
                "id" => $key['id'],
                "title" => $key['title'],
                "bundle" => $key['bundle'],
                "phpfile" => $key['phpfile'],
                "extra" => $key['extra'],
                "published" => $key['published']
            );
        }

        return $maiMenu;
    }


    public function subMenuArray($type = "all", $param = array())
    {
        $set = $this->getSettingObj();

        $maiMenu = array();
        $whereArr = array("level" => 2, "lang" => Tools::getLangId());

        if (isset($param['published'])) {
            if ($param['published'] == "yes") {
                $whereArr['published'] = "yes";
            }
        }
        if ($type == "menu") {
            $whereArr["user"] = Tools::getUserId();
            $parent = $set->getUserMenus($whereArr);
        } else {
            $parent = $set->getSystemModules($whereArr);
        }



        foreach ($parent as $key) {
            $maiMenu[$key['parent_id']][$key['id']] = array(
                "id" => $key['id'],
                "title" => $key['title'],
                "bundle" => $key['bundle'],
                "phpfile" => $key['phpfile'],
                "extra" => $key['extra'],
                "published" => $key['published']
            );
        }

        return $maiMenu;
    }


    public function childMenuArray($type = "all", $param = array())
    {
        $set = $this->getSettingObj();

        $maiMenu = array();
        $whereArr = array("level" => 3, "lang" => Tools::getLangId());

        if (isset($param['published'])) {
            if ($param['published'] == "yes") {
                $whereArr['published'] = "yes";
            }
        }

        if ($type == "menu") {
            $whereArr["user"] = Tools::getUserId();
            $parent = $set->getUserMenus($whereArr);
        } else {
            $parent = $set->getSystemModules($whereArr);
        }


        foreach ($parent as $key) {
            $maiMenu[$key['parent_id']][$key['id']] = array(
                "id" => $key['id'],
                "title" => $key['title'],
                "bundle" => $key['bundle'],
                "phpfile" => $key['phpfile'],
                "extra" => $key['extra'],
                "published" => $key['published']
            );
        }

        return $maiMenu;
    }

    public function MakeLink($bundle, $phpFile, $pageCode, $action)
    {

        return Tools::getUrl() . "?menu=" . $bundle . "&page=" . $phpFile . "&code=" . $pageCode . "&action=" . $action;
    }

    public function formHidden()
    {
        $bundle = $this->getBundle();
        $phpFile = $this->getPhpFile();
        $fileCode = $this->getFileCode();
        $action = $this->getFileAction();
        $html = '';
        $html .= '<input type="hidden" name="menu" value="' . $bundle . '" />' . "\n";
        $html .= '<input type="hidden" name="page" value="' . $phpFile . '" />' . "\n";
        $html .= '<input type="hidden" name="code" value="' . $fileCode . '" />' . "\n";
        $html .= '<input type="hidden" name="action" value="' . $action . '" />' . "\n";
        $html .= '<input type="hidden" name="_chk" value="1" />' . "\n";
        return $html;
    }


    public function GetOptions($param = array())
    {

        $chosen_class = "chosen-select";

        if (Tools::getDirection() == "rtl") {
            $chosen_class = "chosen-select chosen-rtl";
        }

        //$chosen_class = "";

        //$dataArr = $param['data'];
        $name = $param['name'];

        $html = '';
        //class="chosen-select'.$chosen_rtl.'"
        $html .= '<select name="' . $name . '" id="' . $name . '" class="' . $chosen_class . '">';

        //$html .= '<option value=""></option>';


        /*foreach($dataArr as $row){

            if(isset($param['sel'])){
                if ($row['id'] == ($param['sel'])) {
                    $sel = 'selected="selected"';
                } else {
                    $sel = '';
                }
            }


            $html .= '<option value="' . $row['id'] . '-' . $row['title'] . '" ' . $sel . '>' . $row['title'] . '</option>';
        }*/

        $html .= $this->GetOptionVals($param);

        $html .= '</select>';

        return $html;
    }

    public function GetOptionVals($param = array())
    {

        $html = '<option value=""></option>';

        $dataArr = $param['data'];
        $sel = '';

        foreach ($dataArr as $row) {

            if (isset($param['sel'])) {
                if ($row['id'] == ($param['sel'])) {
                    $sel = 'selected="selected"';
                } else {
                    $sel = '';
                }
            }


            $html .= '<option value="' . $row['id'] . '-' . $row['title'] . '" ' . $sel . '>' . $row['title'] . '</option>';
        }

        return $html;
    }

    public function getChosenClass()
    {
        $chosen_rtl = "";

        if (Tools::getDirection() == "rtl") {
            $chosen_rtl = " chosen-rtl";
        }

        return $chosen_rtl;
    }


    public function GetMultiOptions($param = array())
    {

        $chosen_rtl = $this->getChosenClass();
        ;


        $dataArr = $param['data'];


        $name = $param['name'];
        $html = '';

        $selecedIndex = array();

        if (isset($param['sel'])) {

            if (is_array($param['sel'])) {
                foreach ($param['sel'] as $selectedRow) {
                    $selecedIndex[$selectedRow] = ' selected';
                }
            }
        }

        $html .= '<select name="' . $name . '" class="chosen-select' . $chosen_rtl . '" multiple="multiple">';

        $html .= '<option value=""></option>';


        foreach ($dataArr as $row) {
            /*if(isset($param['sel'][$row['id']])){
                $sel = 'selected="selected"';
            }
            else {
                $sel = '';
            }*/

            if (isset($selecedIndex[$row['id']])) {
                $sel = ' selected';
            } else {
                $sel = '';
            }


            $html .= '<option value="' . $row['id'] . '-' . $row['title'] . '" ' . $sel . '>' . $row['title'] . '</option>';
        }

        $html .= '</select>';

        return $html;
    }

    public function formClose()
    {
        return '</form>';
    }

    public function formTag($formMethod = "get", $formAction = "", $formId = "amg_form", $formClass = "formular")
    {



        $html = '';
        $html .= '<form id="' . $formId . '" name="amg_form" method="' . $formMethod . '" class="' . $formClass . '" action="' . $formAction . '">';

        return $html;
    }


    public function isMobile()
    {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }

    public function getCacheDir()
    {
        return Tools::getCacheDir();
    }

    public function getUserId()
    {
        return Tools::getUserId();
    }

    public function removeMenuCache()
    {
        $cacheDir = Tools::getCacheDir();
        $userId = Tools::getUserId();
        $lang = Tools::getLang();
        $menuChaceDir = $cacheDir . DIRECTORY_SEPARATOR . "menu";
        $cacheFile = $menuChaceDir . DIRECTORY_SEPARATOR . "header_nva_" . $lang . "_" . $userId . ".html";
        $sidebarMenuChaceDir = $cacheDir . DIRECTORY_SEPARATOR . "sidebar";
        $sidebarCacheFile = $sidebarMenuChaceDir . DIRECTORY_SEPARATOR . "sidebar_nva_" . $lang . "_" . $userId . ".html";

        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }

        if (file_exists($sidebarCacheFile)) {
            unlink($sidebarCacheFile);
        }
    }



    public function getCurrentPage()
    {
        return Tools::makeLink($this->getBundle(), $this->getPhpFile(), $this->getFileCode(), $this->getFileCode());
    }


    public function downloadCsvHeaders($filename)
    {
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=' . $filename . '');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

    }

    public function array2csv(array &$array)
    {
        if (count($array) == 0) {
            return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($array as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    }


    public function getAllBranch($param = array())
    {
        $set = $this->getSettingObj();

        $sel = "";

        if (!empty($param['sel'])) {
            $sel = $param['sel'];
        }

        $branches = $set->allBranches();
        return $this->GetOptions(array("data" => $branches, "name" => "branch", "sel" => $sel));
    }

    public function getToolObj()
    {
        global $tool;
        return $tool;
    }

    public function userBranches($selected = "")
    {
        $set = $this->getSettingObj();
        $tool = $this->getToolObj();

        $branches = $set->userBranches();

        $sel = "";
        $html = "";

        $sessionArr = Tools::handleSessionData();

        if (isset($sessionArr['branchID'])) {
            $sel = $sessionArr['branchID'];
        }
        if (isset($_REQUEST['branch'])) {
            $sel = $tool->GetExplodedInt($_REQUEST['branch']);
        }

        if (!empty($selected)) {
            $sel = $selected;
        }


        $html .= $this->GetOptions(array("data" => $branches, "name" => "branch", "sel" => $sel));

        return $html;
    }


    public function getClasses($param = array())
    {

        $sessionClasses = array();

        $branch = $this->getSelectedBranchVal();
        $session = $this->getSelectedSessionVal();
        $set = $this->getSettingObj();

        $type = "";

        if (isset($param['branch']) && !empty($param['branch'])) {
            $branch = $param['branch'];
        }


        if (!empty($branch) && !empty($session)) {
            $sessionClasses = $set->sessionClasses($session, $branch);
        }



        $tool = $this->getToolObj();

        $sel = "";

        if (!empty($param['sel'])) {
            $sel = $param['sel'];
        }

        $html = "";

        if (isset($_REQUEST['class'])) {

            $sel = $tool->GetExplodedInt($_REQUEST['class']);
        }

        $html .= $this->GetOptions(array("data" => $sessionClasses, "name" => "class", "sel" => $sel));

        return $html;
    }


    public function getSecsions($param = array())
    {

        $secsions = array();

        $session = $this->getSelectedSessionVal();



        $tool = $this->getToolObj();
        $sel = "";
        $class = "";
        $branch = "";
        $html = "";

        if (isset($param['class']) && !empty($param['class'])) {
            $class = $param['class'];
        }

        if (isset($_REQUEST['class'])) {
            $class = $tool->GetExplodedInt($_REQUEST['class']);
        }

        if (isset($_REQUEST['branch'])) {
            $branch = $tool->GetExplodedInt($_REQUEST['branch']);
        }


        if (!empty($class) && !empty($session) && !empty($branch)) {
            $set = $this->getSettingObj();
            $secsions = $set->sessionSections($session, $class, $branch);
        }

        if (isset($param['section']) && !empty($param['section'])) {
            $sel = $param['section'];
        }


        if (isset($_REQUEST['section'])) {
            $sel = $tool->GetExplodedInt($_REQUEST['section']);
        }

        $html .= $this->GetOptions(array("data" => $secsions, "name" => "section", "sel" => $sel));

        return $html;
    }




    public function getAllSession($param = array())
    {
        $set = $this->getSettingObj();
        $sessions = $set->allSessions();
        $sel = "";
        $html = "";
        $tool = $this->getToolObj();

        if (!empty($param['sel'])) {
            $sel = $param['sel'];
        }


        if (empty($param['sel'])) {
            $sessionArr = Tools::handleSessionData();
            if (isset($sessionArr['sessionId'])) {
                $sel = $sessionArr['sessionId'];
            }
            if (isset($_REQUEST['session'])) {
                $sel = $tool->GetExplodedInt($_REQUEST['session']);
            }
        }

        if (empty($sel)) {
            $sel = $sessions[0]['id'];
        }




        $html .= $this->GetOptions(array("data" => $sessions, "name" => "session", "sel" => $sel));

        return $html;
    }


    public function getReportSession($hideclass = false)
    {
        $set = $this->getSettingObj();
        $sessions = $set->allSessions();
        $sel = "";
        $html = '';
        $tool = $this->getToolObj();

        $sessionArr = Tools::handleSessionData();
        if (isset($sessionArr['sessionId'])) {
            $sel = $sessionArr['sessionId'];
        }
        if (isset($_REQUEST['session'])) {
            $sel = $tool->GetExplodedInt($_REQUEST['session']);
        }

        $chosen_class = "chosen-select";

        if (Tools::getDirection() == "rtl") {
            $chosen_class = "chosen-select chosen-rtl";
        }


        if ($hideclass) {
            $hideclasss = ' class="customhideclass"';
        } else {
            $hideclasss = "";
        }

        $html = '<div id="reportsession" ' . $hideclasss . '>';

        $html .= '<select name="session" id="session" class="' . $chosen_class . '">';

        $html .= '<option value=""></option>';
        $html .= '<option value="999999999999999-varss">Date Range</option>';

        $html .= $this->GetOptionVals(array("data" => $sessions, "sel" => $sel));
        $html .= '</select></div>';

        return $html;
    }

    public function sessionOrCustomDate($show = "session")
    {

        $htm = '';
        $sessionShow = false;
        $dateShow = true;

        if ($show == "dates") {
            $sessionShow = true;
            $dateShow = false;
        }


        $htm .= $this->customDates($dateShow);
        $htm .= $this->getReportSession($sessionShow);

        return $htm;
    }

    public function handleSessionCustomDate()
    {

        if (!empty($_REQUEST['date']) && !empty($_REQUEST['to_date'])) {
            return "dates";
        }
        return "session";
    }


    public function customDates($hideclass = true)
    {


        if ($hideclass) {
            $hideclasss = ' class="customhideclass"';
        } else {
            $hideclasss = "";
        }

        $htm = '<div id="reportdates"' . $hideclasss . '>';

        $dateSel = "";
        $toDateSel = "";
        if (isset($_REQUEST['date'])) {
            $dateSel = $_REQUEST['date'];
        }

        if (isset($_REQUEST['date'])) {
            $toDateSel = $_REQUEST['to_date'];
        }
        $htm .= '<input name="date" placeholde="' . Tools::transnoecho("from_date") . '"  type="text" class="start_date" value="' . $dateSel . '"/><br />';
        $htm .= '<input name="to_date" placeholde="' . Tools::transnoecho("to_date") . '"  type="text" class="end_date" value="' . $toDateSel . '"/><br /> ';
        $htm .= '<span id="show_session_orign">Clear</span>';
        $htm .= '</div>';
        return $htm;
    }

    public function getIdInput($sel = "")
    {

        if (isset($_REQUEST['session'])) {
            $sel = $tool->GetExplodedInt($_REQUEST['session']);
        }


        $html .= '<input value="' . $sel . '" type="text" name="student_id" id="student_id">';

        return $html;
    }

    public function getSelectedBranchVal()
    {
        $sel = "";
        $tool = $this->getToolObj();

        $sessionArr = Tools::handleSessionData();
        if (isset($sessionArr['branchID'])) {
            $sel = $sessionArr['branchID'];
        }
        if (isset($_REQUEST['branch'])) {
            $sel = $tool->GetExplodedInt($_REQUEST['branch']);
        }

        return $sel;
    }

    public function getSelectedSessionVal()
    {
        $sel = "";
        $tool = $this->getToolObj();
        $sessionArr = Tools::handleSessionData();
        if (isset($sessionArr['sessionId'])) {
            $sel = $sessionArr['sessionId'];
        }
        if (isset($_REQUEST['session'])) {
            $sel = $tool->GetExplodedInt($_REQUEST['session']);
        }

        if (empty($sel)) {
            $set = $this->getSettingObj();
            $sessions = $set->getCurrentSession();
            $sel = $sessions['id'];
        }

        return $sel;
    }

    public function CurrenPageUrl()
    {

        $pageURL = 'http';

        if (@$_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }

        $pageURL .= "://";

        if ($_SERVER["SERVER_PORT"] != "80") {

            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {

            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }


        return $pageURL;
    }


    public function makeAjaxCall($name, $data, $target_html, $url)
    {

        $html = '<script type="text/javascript">';
        $html .= '$("' . $name . '").change(function(){
        var maincatdata = \'main_drop_down=\' + $("' . $name . '").val() + \'&target=' . $data . '\';
         $.ajax({
              type: "POST",
              url: "' . $url . '",
              data: maincatdata,
              async: false,
              success: function (data) {
                    $("' . $target_html . '").html(data);
              }
                })
     });';
        $html .= '</script>';
        return $html;
    }


    public function Message($type, $msg)
    {
        switch ($type) {
            case 'info':
                $class = 'alert';
                break;
            case 'alert':
                $class = 'alert alert-error';
                break;
            case 'succ':
                $class = 'alert alert-success';
                break;
        }
        $htm = '<div class="' . $class . '" style="font-family: \'Jameel Noori Nastaleeq\'; font-size:20px;">
                      <button data-dismiss="alert" class="close" type="button">×</button>' . $msg . '</div>';

        return $htm;
    }

    public function getTransObject()
    {
        include_once __DIR__ . DIRECTORY_SEPARATOR . "TransLabelsClass.php";
        $this->transObj = new TransLabelsClass();
        $this->transObj->setLang(Tools::getLang());
        $transArr = $this->transObj->transArray();
        return $transArr;
    }

    public function getGenderTrans($gender)
    {
        $tool = $this->getToolObj();
        $transArr = $this->getTransObject();
        $transKey = $tool->makeGenderKey($gender);
        return $transArr[$transKey];
    }

    public function getTable($table, $name, $where = "")
    {
        $set = $this->getSettingObj();
        $sessions = $set->getTitleTable($table, $where);
        $sel = "";
        $html = "";
        $tool = $this->getToolObj();
        if (!empty($param['sel'])) {
            $sel = $param['sel'];
        }
        if (empty($param['sel'])) {
            if (isset($_REQUEST[$name])) {
                $sel = $tool->GetExplodedInt($_REQUEST[$name]);
            }
        }
        $html .= $this->GetOptions(array("data" => $sessions, "name" => $name, "sel" => $sel));
        return $html;
    }

    public function getDateInput($name = "date")
    {
        $sel = "";
        if (isset($_REQUEST[$name])) {
            $sel = $_REQUEST[$name];
        }


        return '<input name="' . $name . '" type="text" class="start_date" value="' . $sel . '"/> ';
    }

    public function getToDateInput($name = "to_date")
    {

        $sel = "";
        if (isset($_REQUEST[$name])) {
            $sel = $_REQUEST[$name];
        }



        return '<input name="' . $name . '" type="text" class="end_date" value="' . $sel . '"/> ';
    }


    public function FormatedNumber($number)
    {
        return number_format($number, 1);
    }


    public function examDropDown($data = array(), $selected = "")
    {
        $tool = $this->getToolObj();
        $sel = "";
        $sessionArr = Tools::handleSessionData();

        if (isset($sessionArr['examName'])) {
            $sel = $sessionArr['examName'];
        }
        if (isset($_REQUEST['exam_name'])) {
            $sel = $tool->GetExplodedInt($_REQUEST['exam_name']);
        }

        if (!empty($selected)) {
            $sel = $selected;
        }


        return $this->GetOptions(array("name" => "exam_name", "data" => $data, "sel" => $sel));
    }

    public function makeDateByExam($exam, $year)
    {
        switch ($exam) {
            case 1:
                $date = $year . '-02-28';
                break;
            case 2:
                $date = $year . '-05-28';
                break;
            case 3:
                $date = $year . '-08-28';
                break;
            default:
                $date = $year . '-08-28';
        }
        return $date;
    }


    public function Equality()
    {
        $htm = '<option value="=">Equal</option>';
        $htm .= '<option value=">=">More than or Equal</option>';
        $htm .= '<option value="<=">Less than or Equal</option>';
        $htm .= '<option value="!=">Not Equal</option>';

        return $htm;
    }

    public static function uploadDir()
    {
        return WEBPATH;
    }

    public function getDatedPath()
    {
        return date("Y") . "/" . date("m") . "/";
    }


    function checkDocMime($tmpname)
    {
        // MIME types: http://filext.com/faq/office_mime_types.php
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mtype = finfo_file($finfo, $tmpname);
        finfo_close($finfo);
        if (
            $mtype == ("image/jpeg") ||
            $mtype == ("image/jpg") ||
            $mtype == ("image/png") ||
            $mtype == ("application/pdf")
        ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }


    public function uploadFile($param = array())
    {
        $realPath = self::uploadDir();
        $targetFileName = "";
        $datedPath = $this->getDatedPath();
        $targetDir = $realPath . "/uploads/" . $datedPath;
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }


        if (!$this->checkDocMime($_FILES[$param['name']]['tmp_name'])) {
            echo "Please upload image or pdf.";
            exit;
        }

        $extenTion = pathinfo($_FILES[$param['name']]["name"], PATHINFO_EXTENSION);
        $targetFileName .= time();
        if (!empty($param['extra_name'])) {
            $targetFileName .= "_" . $param['extra_name'];
        }
        $targetFileName .= "." . $extenTion;
        $targetFile = $targetDir . strtolower($targetFileName);
        if (!empty($_FILES[$param['name']]["name"])) {
            if (move_uploaded_file($_FILES[$param['name']]["tmp_name"], $targetFile)) {
                return $targetFileName;
            } else {
                return false;
            }
        }
        return false;
    }


    public function blankIframeWithoutEcho()
    {
        return '<iframe id="expIframe" style="display:none"></iframe>';
    }

    /**
     * @return bool
     */
    public function isShowJsExport()
    {
        return $this->showJsExport;
    }

    /**
     * @param bool $showJsExport
     */
    public function setShowJsExport($showJsExport)
    {
        $this->showJsExport = $showJsExport;
    }

    /**
     * @return bool
     */
    public function isShowSearchButton()
    {
        return $this->showSearchButton;
    }

    /**
     * @param bool $showSearchButton
     */
    public function setShowSearchButton($showSearchButton)
    {
        $this->showSearchButton = $showSearchButton;
    }


    public function branchBreadCrumbs()
    {

        $headings = array();

        if (isset($_REQUEST['session'])) {
            $sessionArr = explode('-', $_GET['session'], 2);
            $headings[] = $sessionArr[1];
        }

        if (isset($_REQUEST['branch'])) {
            $headings[] = $this->tool->GetExplodedVar($_REQUEST['branch']);
        }
        if (isset($_REQUEST['class'])) {
            $headings[] = $this->tool->GetExplodedVar($_REQUEST['class']);
        }

        if (isset($_REQUEST['section'])) {
            $headings[] = $this->tool->GetExplodedVar($_REQUEST['section']);
        }

        return $this->makeBreadCrumbs($headings);
    }

    public function makeBreadCrumbs($arr = array())
    {
        $direction = Tools::getDirection();
        if ($direction == 'ltr') {
            $breadLeftRight = 'right';
        } else {
            $breadLeftRight = 'left';
        }

        $icon = '&nbsp;<span class="icon-angle-' . $breadLeftRight . '"></span>';

        $end = end($arr);

        $html = '<ul class="breadcrumb">';
        foreach ($arr as $r) {
            $html .= ' <li><strong>' . $r . '</strong></li>';
            if ($r != $end) {
                $html .= $icon;
            }
        }
        $html .= '</u>';

        return $html;
    }


    public function arrayBreadCrumbs($param = array())
    {
        foreach ($param as $k) {
            $headings[] = $k;
        }

        return $this->makeBreadCrumbs($headings);
    }
}
