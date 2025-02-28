<?php

/**
 * Created by PhpStorm.
 * User: zia
 * Date: 10/14/2016
 * Time: 3:52 PM
 */
require __DIR__ . DIRECTORY_SEPARATOR . "ajaxCRUD.php";

//unset($_SESSION['amg_html_table_data']);
//unset($_SESSION['ajaxcrud_where_clause']);

class AmgCrud extends ajaxCRUD{

    public $disableDelete = 1;
    public $fourFields = true;
    public $publishedField = true;

    public function __construct()
    {
        global $tpl;
        parent::__construct();
        $this->lang = Tools::getLang();
        $menu = $tpl->getBundle();
        $page = $tpl->getPhpFile();
        $code = $tpl->getFileCode();
        $action = $tpl->getFileAction();
        $this->setAjaxFile(URL . "/?menu=$menu&page=$page&code=$code&action=$action");
        $this->setAjaxcrudRoot(AJAXCRUD);
        $this->setDbTablePk("id");
    }

    /**
     * @return bool
     */
    public function isFourFields()
    {
        return $this->fourFields;
    }

    /**
     * @param bool $fourFields
     */
    public function setFourFields($fourFields)
    {
        $this->fourFields = $fourFields;
    }

    /**
     * @return bool
     */
    public function isPublishedField()
    {
        return $this->publishedField;
    }

    /**
     * @param bool $publishedField
     */
    public function setPublishedField($publishedField)
    {
        $this->publishedField = $publishedField;
    }

    /**
     * @return int
     */
    public function getDisableDelete()
    {
        return $this->disableDelete;
    }

    /**
     * @param int $disableDelete
     */
    public function setDisableDelete($disableDelete)
    {
        $this->disableDelete = $disableDelete;
    }

    private function disableAllFields(){
        foreach($this->fields as $field){
            $this->disallowEdit($field);
        }
    }

    public function getPrefix(){
        return PR;
    }


    function displayAsArray($fields = array()){
        foreach($fields as $field => $fieldLabel){
            $this->displayAs($field,$fieldLabel);
            $this->setAddPlaceholderText($field,$fieldLabel);
        }
    }


    public function setBranchFilter($col = ""){
        global $tool,$set;
        $branchArr = $set->userBranches();
        $branches = array();
        foreach ($branchArr as $branch){
            $branches[] = $branch['id'];
        }
        $branchIds = implode(",",$branches);
        $this->addWhereClause(" WHERE ".$col."branch_id IN (" . $branchIds . ")");
        $this->defineRelationship("branch_id","jb_branches","id","title","",1, " AND jb_branches.id IN ($branchIds)");


    }


    public function showTable(){
        global $tpl;
        $this->AjaxCrudFooter();
        if($this->getDisableDelete() == 1){
            $this->disallowDelete();
        }
        if($tpl->isCanPrint() == 0){
            $this->setPrintButton(false);
        }
        if(!$tpl->isCanAdd()){
            $this->disallowAdd();
        }
        if(!$tpl->isCanExport()){
            $this->setAmgCsv(false);
        }
        else{
            $this->setAmgCsv(true);
        }
        if(!$tpl->isCanEdit()){
            $this->disableAllFields();
        }


        if(isset($_GET['amgFilterAction'])){
            if($_GET['amgFilterAction'] == "true"){
                unset($_GET['menu']);
                unset($_GET['page']);
                unset($_GET['amgFilterAction']);
                foreach($_GET as $keyFilterForm => $valFilterForm){
                    $amgCrudQueryWhere = "WHERE " . $this->db_table . "." . $keyFilterForm . "='" . $valFilterForm . "'";
                    if(!empty($valFilterForm)){
                        $this->addWhereClause($amgCrudQueryWhere);
                    }
                }
                $this->setAmgCrudFilterMode(true);
            }
        }

        if($this->isFourFields()){
            $userId = Tools::getUserId();
            $dateTime = date("Y-m-d H:i:s");
            $this->addValueOnInsert("created", $dateTime);
            $this->addValueOnInsert("created_user_id", $userId);
            $this->omitFieldCompletely("created");
            $this->omitFieldCompletely("updated");
            $this->omitFieldCompletely("created_user_id");
            $this->omitFieldCompletely("updated_user_id");
        }

        if($this->isPublishedField()){
            $this->defineAllowableValues("published", $this->softStatus());
            $this->formatFieldWithFunction('published', 'StatusField');
        }


        $params = array(
                "file_uploads" => $this->file_uploads
               ,"file_upload_info" => $this->file_upload_info
               ,"table" => $this->db_table
               ,"table_pk" => $this->db_table_pk
        );
        $pramsToPass = urlencode(serialize($params));
        parent::showTable();


        ?>
        <script type="text/javascript">
            var curPageUrl = makeJsLink("<?php echo $tpl->getBundle() ?>","<?php echo $tpl->getPhpFile() ?>","<?php echo $tpl->getFileCode() ?>","<?php echo $tpl->getFileAction() ?>");

        $(function(){


            var $amgTextboxCrudClass = $('.amg_textbox_class');
            if($($amgTextboxCrudClass).size()){
                $($amgTextboxCrudClass).editable({
                    url: makeJsLink("<?php echo $tpl->getBundle() ?>","<?php echo $tpl->getPhpFile() ?>&amgTextboxAjaxEdit=1")
                });
            }

            $('.amg_select_crud_class').each(function(){
               $(this).editable({
                   source: makeJsLink("<?php echo $tpl->getBundle() ?>","<?php echo $tpl->getPhpFile() ?>&amgSelectGetData=1&amgtable="+$(this).data("table")+"&amgtableidCol="+$(this).data("reftableid")+"&amgsort="+$(this).data("sort")+"&amgwhere="+$(this).data("where")+"&amgtablecol="+$(this).data("refcol")+"&amgtableid="+$(this).data("id")),
                   url: makeJsLink("<?php echo $tpl->getBundle() ?>","<?php echo $tpl->getPhpFile() ?>&amgTextboxAjaxEdit=1")
               });
            });




            $( '.file_upload' )
              .submit( function( e ) {

                  $('#amgloader').show();
                  $('<input />').attr('type', 'hidden')
                            .attr('name', "upload_params")
                            .attr('value', "<?php echo $pramsToPass ?>")
                            .appendTo('.file_upload');


                $.ajax( {
                  url: curPageUrl,
                  type: 'POST',
                  data: new FormData( this ),
                  processData: false,
                  contentType: false,
                    success: function (ret) {
                        $('#amgloader').hide();
                        try {
                            var data = JSON.parse(ret);
                            if(data.status == "true"){
                                location.reload();
                            }
                            else{
                                alert(data.msg);
                            }
                        }
                        catch(err)
                          {
                            alert(ret);
                          }


                    }
                } );
                e.preventDefault();
            } );



            $("#amg_ajaxcrud_add_button").click(function(){
                $('#amgloader').show();
                $( '#amg_add_form_crud' )
                  .submit( function( e ) {
                    $.ajax( {
                      url: curPageUrl,
                      type: 'POST',
                      data: new FormData( this ),
                      processData: false,
                      contentType: false,
                        success: function (ret) {
                            $('#amgloader').hide();
                            try {
                             var data = JSON.parse(ret);

                              if(data.status == "true"){
                                  location.reload();
                                  //$('#ajaxcrud_succ').fadeIn('slow', function () {
                                      //$(this).html(data.msg);
                                     //$(this).delay(3000).fadeOut('slow');
                                   //});
                                  //$('#amg_add_form_crud').trigger("reset");
                                  //$('#amg_add_form_crud').slideUp('slow');
                                  return false;
                              }
                              else{

                                  $('#ajaxcrud_error').fadeIn('slow', function () {
                                    $(this).html(data.msg);
                                   $(this).delay(3000).fadeOut('slow');
                                 });

                                  return false;
                              }
                            }

                            catch(err)
                               {
                                   $('#ajaxcrud_error').fadeIn('slow', function () {
                                       $(this).html(ret);
                                      $(this).delay(3000).fadeOut('slow');
                                    });
                                   return false;
                               }
                        },
                        error: function (ret) {

                            $('#ajaxcrud_error').fadeIn('slow', function () {
                               $(this).html(ret);
                               $(this).delay(3000).fadeOut('slow');
                            });
                            return false;
                        }
                    } );
                    e.preventDefault();
                  } );

            });



            $("#amgCrudSearchButton").click(function(){
                $('#amgloader').show();
                var $filterForm = $("#amgAjaxCrudFilterForm");
                var values = $filterForm.serialize();
                window.location.href = curPageUrl + "&amgFilterAction=true&" + values;
            });
            $('#amgAjaxCrudFilterForm').submit(function(e) {
                return false;
            });
            $("#amgAjaxCrudClearFilter").click(function(){
                window.location.href = curPageUrl;
            });
        });
        function pageTable(e, t) {
            var n = "&table=" + t + e + sortReq + filterReq;
            window.location.href = curPageUrl + n;
        }
        function changeSort(e, t, n) {
            sortReq = "&sort_field=" + t + "&sort_direction=" + n;
            var i = "&table=" + e + sortReq + filterReq;
            window.location.href = curPageUrl + i;
        }


            function deleteFile(e, t) {
                //var loc = location.href;
                //loc += loc.indexOf("?") === -1 ? "?" : "&";
                $('#amgloader').show();
                var reloadUrl = curPageUrl + "<?php if(isset($_GET['pid'])) echo "&pid=" . $_GET['pid']; ?>";

                $.ajax( {
                  url: curPageUrl,
                  type: 'GET',
                  data: "amgaction=delete_file&field_name=" + e + "&id=" + t + "&param=<?php echo $pramsToPass ?>",
                    success: function (ret) {
                        try {
                            var data = JSON.parse(ret);
                            if(data.status == "true"){
                                location.href = reloadUrl;
                            }
                        }
                        catch(err)
                          {
                            alert(ret);
                          }


                    }
                } );

            }

        </script>

        <?php

    }


    public function StatusField($data){
        $transArr = $this->transObj->transArray();
        if($data == 1){
            return '<span class="label label-success fonts">'.$transArr['enable'].'</span>';
        }
        else{
            return '<span class="label label-important fonts">'.$transArr['disable'].'</span>';
        }
    }

    public function AjaxCrudStatusValues(){
        $transArr = $this->transObj->transArray();
        return $MySoftStatus = array(
        array(1,$transArr['enable']),
        array(0,$transArr['disable'])
        );
    }

    public function AjaxCrudHeader($link,$label){
        return '<div class="header"><div class="tools"><a class="btn" href="'.$link.'">'.$label.'</a></div></div>';
    }

    public function AjaxCrudFooter(){
        $recoredLabelArr = $this->transObj->transArray();;
        echo '<div class="accordion-heading social-box social-green"><a class="accordion-toggle collapsed fonts" data-toggle="collapse" data-parent="#accordion-fa1" href="#collapseOne-fa1"> ' . $recoredLabelArr["total_records"] . ': <b>' . $this->getNumRows().'</b><br /></a></div>';
    }
    public function softStatus(){
        $transArr = $this->transObj->transArray();
        return array(
        array(1,$transArr['enable']),
        array(0,$transArr['disable'])
        );
    }
    public function addNewButton($menu,$page,$label){
        $link = Tools::makeLink($menu,$page,"","");
        $html = '<a class="btn" href="'.$link.'">'.$label.'</a>';
        return $html;
    }

}



function StatusField($data){
    $transArr = array(
        "ar" => array(
              "enable" => "تمكين"
              ,"disable" => "تعطيل"
        )
       ,"ur" => array(
               "enable" => "فعال"
               ,"disable" => "غیر فعال"
            )
       ,"en" => array(
              "enable" => "Enable"
              ,"disable" => "Disable"
            )
    );
    if($data == 1){
        return '<span class="label label-success fonts">'.$transArr[Tools::getLang()]['enable'].'</span>';
    }
    else{
        return '<span class="label label-important fonts">'.$transArr[Tools::getLang()]['disable'].'</span>';
    }
}


function currentMonthFolder(){
    $year = date("Y");
    $month = date("m");
    $path = UPLFRONTSITE . DRS . $year . DRS . $month . DRS;
    $pathToSave = $year . "___" . $month . "___";
    $url = URLFRONTSITE . "/" . $year . "/" . $month . "/";

    if(!file_exists($path)){
        mkdir($path,0777,true);
    }

    return array("url" => $url, "savePath" => $pathToSave, "path" => $path);
}


function makePdf($val){
    /*$url = "";
    $mainUploadDir = URLFRONTSITE;
    $pathArr = explode("___",$val);
    if(is_array($pathArr)){
        $url = $mainUploadDir . "/" . @$pathArr[0] . "/" . @$pathArr[1] . "/";
    }
    $pdfImagePath = WEB . "/images/pdf.ico";
    return "<a href='".$url.$val."' target='_new'> <img src=\"$pdfImagePath\" width=\"40\" /></a>";*/

    $pdfImagePath = WEB . "/images/pdf.ico";

    $amgSpace = new AmgSpace();
    $url = $amgSpace->getCdnFileUrl($val);
    $amgSpace = null;
    return "<a href='".$url."' target='_new'> <img src=\"$pdfImagePath\" width=\"40\" /></a>";
}

function makeImg($val){
    $url = "";
    /*$url = "";
    $mainUploadDir = URLFRONTSITE;
    $pathArr = explode("___",$val);
    if(is_array($pathArr)){
        $url = $mainUploadDir . "/" . @$pathArr[0] . "/" . @$pathArr[1] . "/";
    }*/
    $amgSpace = new AmgSpace();
    $url = $amgSpace->getCdnFileUrl($val);
    $amgSpace = null;
    return "<a href='".$url."' target='_new'> <img src=\"$url\" width=\"40\" /></a>";
}
