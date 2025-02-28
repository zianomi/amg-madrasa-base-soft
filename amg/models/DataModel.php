<?php



class DataModel extends BaseModel
{

    protected function getTableName()
    {

    }

    public function getDataCols(){
        $sql = "SELECT id, grnumber, gr_new, gr_unique FROM data";
        $res = $this->getResults($sql);
        return $res;
    }

    public function getData(){
        $sql = "SELECT * FROM data";
        $res = $this->getResults($sql);
        return $res;
    }

    public function updateDatesCol($updateCols,$updateWhere){


       /* UPDATE data d
    JOIN jb_classes c ON d.class_id = c.title
SET d.class_id = c.id;*/

        /*UPDATE data d
    JOIN jb_sections c ON d.section_id = c.title
SET d.section_id = c.id;*/


         $this->update( 'data', $updateCols, $updateWhere, 1 );
    }


    public function getNewData(){
        $sql = "SELECT * FROM bbs_data";
        $res = $this->getResults($sql);
        return $res;
    }
}
