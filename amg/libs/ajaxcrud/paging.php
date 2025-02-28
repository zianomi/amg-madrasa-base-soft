<?php
/**
 * Created by PhpStorm.
 * User: mzia
 * Date: 9/12/18
 * Time: 5:27 PM
 */



if(!class_exists("paging")){
    class paging{

        var $pRecordCount;
        var $pStartFile;
        var $pRowsPerPage;
        var $pRecord;
        var $pCounter;
        var $pPageID;
        var $pShowLinkNotice;
        var $tableName;

        function processPaging($rowsPerPage,$pageID){
            $record = $this->pRecordCount;
            if($record >=$rowsPerPage)
                $record=ceil($record/$rowsPerPage);
            else
                $record=1;
            if(empty($pageID) or $pageID==1){
                $pageID=1;
                $startFile=0;
            }
            if($pageID>1)
                $startFile=($pageID-1)*$rowsPerPage;

            $this->pStartFile   = $startFile;
            $this->pRowsPerPage = $rowsPerPage;
            $this->pRecord      = $record;
            $this->pPageID      = $pageID;

            return $record;
        }
        function myRecordCount($query){
            global $mysqliConn;


            $rs      			= $mysqliConn->query($query) or die("Database Error <br>".$query);
            $rsCount 			= mysqli_num_rows($rs);

            $this->pRecordCount = $rsCount;
            unset($rs);
            return $rsCount;
        }

        function startPaging($query){
            $query    = $query." LIMIT ".$this->pStartFile.",".$this->pRowsPerPage;
            $rs = q($query);
            return $rs;
        }

        function pageLinks($url){
            $cssclass = "paging_links";
            $this->pShowLinkNotice = "&nbsp;";
            $totalpages = ceil($this->pRecordCount / $this->pRowsPerPage);
            $currentpage = $this->pPageID;


            $min = max($currentpage - 5, 1); // there are no pages < 1
            $max = min($currentpage + 5, $totalpages); // and no pages > total_pages

            $link = '<div class="text-center"><div class="pagination"><ul>';
            if($this->pRecordCount>$this->pRowsPerPage){
                $this->pShowLinkNotice = "Page ".$this->pPageID. " of ".$this->pRecord;
                //Previous link
                //$link = "";
                if($this->pPageID !== 1){
                    $prevPage = $this->pPageID - 1;
                    $link .= "<li><a href=\"javascript:;\" onClick=\"" . $this->getOnClick("&pid=1") . "\" class=\"$cssclass\">|<<</a></li> ";
                    $link .= "<li><a href=\"javascript:;\" onClick=\"" . $this->getOnClick("&pid=$prevPage") ."\" class=\"$cssclass\"><<</a></li>";
                }


                for($ctr = $min; $ctr <= $max; ++$ctr) {




                    if($this->pPageID==$ctr)
                        $link .=  "<li><a href=\"javascript:;\" onClick=\"" . $this->getOnClick("&pid=$ctr") . "\" class=\"$cssclass\"><b>$ctr</b></a></li>";
                    else
                        $link .= "  <li><a href=\"javascript:;\" onClick=\"" . $this->getOnClick("&pid=$ctr") . "\" class=\"$cssclass\">$ctr</a></li>";
                }
                //Previous Next link
                if($this->pPageID<($ctr-1)){
                    $nextPage = $this->pPageID + 1;
                    $link .= "<li><a href=\"javascript:;\" onClick=\"" . $this->getOnClick("&pid=$nextPage") . "\" class=\"$cssclass\">>></a></li>";
                    $link .="<li><a href=\"javascript:;\" onClick=\"" . $this->getOnClick("&pid=".$this->pRecord) . "\" class=\"$cssclass\">>>|</a></li>";
                }
                $link .= '</ul></div></div>';
                return $link;
            }
        }

        function getOnClick($paging_query_string){
            global $db_table;
            //if any hardcoding is needed...(advanced feature for special needs)
            $extra_query_params = "";

            if(isset($_GET['amgFilterAction'])){
                if($_GET['amgFilterAction'] == "true"){
                    unset($_GET['menu']);
                    unset($_GET['page']);
                    unset($_GET['table']);
                    unset($_GET['pid']);
                    foreach($_GET as $keyFilterForm => $valFilterForm){

                        $extra_query_params .= "&" . $keyFilterForm . "=" . $valFilterForm;
                    }

                }
            }

            //$extra_query_params = "&Dealer=" . htmlentities($_REQUEST['Dealer']);
            return "pageTable('" . $extra_query_params . "$paging_query_string', '$this->tableName');";
        }

    }}