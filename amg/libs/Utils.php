<?php
/**
 * Created by PhpStorm.
 * User: ZIA
 * Date: 9/14/2018
 * Time: 12:03 PM
 */



class Utils
{

    private $sel = 0;

    /**
     * @return int
     */
    public function getSel()
    {
        return $this->sel;
    }

    /**
     * @param int $sel
     */
    public function setSel($sel)
    {
        $this->sel = $sel;
    }






    function  createTree(&$list, $parent){
        $tree = array();
        foreach ($parent as $k=>$l){
            if(isset($list[$l['id']])){
                $l['children'] = $this->createTree($list, $list[$l['id']]);
            }
            $tree[] = $l;
        }
        return $tree;
    }

    public function thorwError($message){
        header($_SERVER["SERVER_PROTOCOL"] . ' 500 Server Error');
        header('Status:  500 Server Error');
        return $message;
    }

    function generateTreeView($items, $currentParent, $currLevel = 0, $prevLevel = -1) {
        $html = '';
        foreach ($items as $itemId => $item) {
            $id = $item['id'];

            if ($currentParent == $item['parent_id']) {
                if ($currLevel > $prevLevel){
                    $html .= "<ul>";
                }

                if ($currLevel == $prevLevel){
                    $html .= "</li>";
                }


                $html .= '<li id="child_node_'.$id.'">'.$item['name'] . ' - ' . $item['code'];

                if ($currLevel > $prevLevel) {
                    $prevLevel = $currLevel;
                }

                $currLevel++;

                $html .= $this->generateTreeView ($items, $itemId, $currLevel, $prevLevel);
                $currLevel--;
            }
        }

        if ($currLevel == $prevLevel) $html .= "</li></ul>";

        return $html;
    }


    function buildSelectTree(Array $data, $parent = 0) {
        $tree = array();
        foreach ($data as $d) {
            if ($d['parent'] == $parent) {
                $children = $this->buildSelectTree($data, $d['id']);


                // set a trivial key
                if (!empty($children)) {
                    $d['_children'] = $children;
                }
                $tree[] = $d;
            }
        }
        return $tree;
    }

    function makeSelectPrintTree($tree,  $r = 0, $p = null) {
        $htm = '';
        foreach ($tree as $i => $t) {


            $disabled = '';

            $dash = ($t['parent'] == 0) ? '' : str_repeat('-', $r) . ' ';

            if(empty($t['_children'])){
                $disabled = '';
            }
            else{
                $disabled = ' disabled';
            }

            $sel = $this->getSel();

            if(!empty($sel)){
                if($sel == $t['id']){
                    $sel = " selected";
                }
                else{
                    $sel = "";
                }
            }

            $htm .= '<option value="'.$t['id'].'"'.$disabled.$sel.'>' . $t['code'] . " " . $dash . " " . $t['name'];
            if ($t['parent'] == $p) {
                // reset $r
                $r = 0;
            }
            if (isset($t['_children'])) {
                $htm .= $this->makeSelectPrintTree($t['_children'], ++$r, $t['parent']);
            }
        }

        return $htm;
    }
}