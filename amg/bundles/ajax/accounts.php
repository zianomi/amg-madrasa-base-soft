<?php
/**
 * Created by PhpStorm.
 * User: ZIA
 * Date: 9/14/2018
 * Time: 7:54 PM
 */



Tools::getModel("Accounts");
Tools::getLib("Utils");
$conn = $tool->getMysqlCon();
$ac = new Accounts();
$utils = new Utils();
if(isset($_GET['operation'])) {
    try {
        $result = NULL;
        $data = array("id" => 0, "parent_id" => 0, "name" => "root", "children" => array());
        switch($_GET['operation']) {

            case 'get_node':
                /*$node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;*/
                $rowData = $ac->getChartOfAccounts();

                /*if(empty($rowData)){
                    throw new Exception('Node does not exist');
                }*/
                $new[0] = array();

                if(!empty($rowData)){
                    foreach ($rowData as $a){
                        $new[$a['parent_id']][] = $a;
                    }
                    $tree = $utils->createTree($new, $new[0]); // changed
                    $result = $tree;

                }
                else{
                    $result = $data;
                }


                break;
            case 'create_node':
                $node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
                $nodeText = isset($_GET['text']) && $_GET['text'] !== '' ? $ac->filter($_GET['text']) : '';

                if($node == -1){
                    $node = 0;
                }

                if(empty($nodeText)){
                    echo $utils->thorwError("Please enter name");
                    exit;
                }

                //if($nodeText == "New node"){
                    //echo $utils->thorwError("Please enter name");
                    //exit;
                //}



                if(empty($node)){
                    $level = 1;
                }
                else{
                    $maxLevel = $ac->getSettings(array("key" => "account_levels"));
                    $parentNodeLevel = $ac->findChildrenByParent($node);
                    if($parentNodeLevel >= $maxLevel){
                        echo $utils->thorwError("Maximum " . $maxLevel . " allowed.");
                        exit;
                    }

                    $level = $parentNodeLevel + 1;
                }

                $ins['title'] = $nodeText;
                $ins['parent_id'] = $node;
                $ins['code'] = $ac->makeCode($node);
                $ins['level'] = $level;
                $ins['published'] = 1;
                $ins['is_root'] = 0;
                $res = $ac->insertChartOfAccount($ins);


                /*$sql ="INSERT INTO `treeview_items` (`title`,  `parent_id`) VALUES('".$nodeText."',  '".$node."')";
                mysqli_query($conn, $sql);*/

                if(is_numeric($res)){
                    $result = array('id' => $res);
                }
                else{
                    echo $utils->thorwError("Error: " . $res);
                }



                break;
            case 'rename_node':
                $node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
                //print_R($_GET);
                $nodeText = isset($_GET['text']) && $_GET['text'] !== '' ? $_GET['text'] : '';
                $sql ="UPDATE `jb_ac_chart_of_accounts` SET `title`='".$nodeText."' WHERE `id`= '".$node."'";
                mysqli_query($conn, $sql);
                break;
            case 'delete_node':
                $node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
                $sql ="DELETE FROM `treeview_items` WHERE `id`= '".$node."'";
                mysqli_query($conn, $sql);
                break;
            default:
                throw new Exception('Unsupported operation: ' . $_GET['operation']);
                break;
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result);
        exit;
    }
    catch (Exception $e) {
        echo $utils->thorwError($e->getMessage());
    }
    die();
}
exit;