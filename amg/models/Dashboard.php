<?php
/**
 * Created by PhpStorm.
 * User: zia
 * Date: 5/2/2017
 * Time: 10:29 PM
 */
class Dashboard extends BaseModel
{

    public function countBranchesInZone(){
        $sql = "SELECT
    COUNT(`jb_branches`.`id`) AS `total`
    , `jb_zones`.`id` AS `zone_id`
    , `jb_zones`.`title` AS `zone_title`
    , `jb_branches`.`id` AS `branch_id`
    , `jb_branches`.`title` AS `branch_title`
    , `jb_branches`.`zone_id`
FROM
    `jb_branches`
    INNER JOIN `jb_zones` 
        ON (`jb_branches`.`zone_id` = `jb_zones`.`id`)
GROUP BY `jb_branches`.`zone_id`";

        $res = $this->getResults($sql);

        return $res;
    }

    protected function getTableName(){}

    public function countStudents(){
        $sql = "
        SELECT `jb_branches`.`zone_id` , `jb_zones`.`title`
        ,SUM(if (`student_status` = 'current', 1, 0)) as current 
        ,SUM(if (`student_status` = 'completed', 1, 0)) as completed 
        ,SUM(if (`student_status` = 'dependent', 1, 0)) as dependent 
        ,SUM(if (`student_status` = 'terminated', 1, 0)) as terminateds 
        FROM `jb_students` 
        INNER JOIN `jb_branches` ON (`jb_students`.`branch_id` = `jb_branches`.`id`) 
        INNER JOIN `jb_zones` ON (`jb_branches`.`zone_id` = `jb_zones`.`id`) 
        GROUP BY `jb_branches`.`zone_id`
        ";

        $res = $this->getResults($sql);

        return $res;
    }
    
    

}