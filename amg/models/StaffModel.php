<?php

/**
 * Created by PhpStorm.
 * User: zia
 * Date: 4/7/2017
 * Time: 11:45 AM
 */
class StaffModel extends BaseModel
{

    protected function getTableName(){}

    public function staffNumbers($group){
        $sql = "SELECT
            `id`
            , `title`
            , `fone`
            , `group_id`
        FROM
            `jb_sms_numbers`
        WHERE (`group_id`  = $group)";
        $row = $this->getResults($sql);
        return $row;
    }


}