<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . "BaseModel.php";
class SettingModel extends BaseModel
{

    protected function getTableName()
    {
        // TODO: Implement getTableName() method.
    }

    public function getSystemModules($param = array())
    {
        $sql = "SELECT `" . $this->getPrefix() . "system_modules`.`id` , `" . $this->getPrefix() . "system_modules`.`parent_id`,
        `" . $this->getPrefix() . "system_modules`.`bundle`, `" . $this->getPrefix() . "system_modules`.`extra`, `" . $this->getPrefix() . "system_modules`.`published`,
`" . $this->getPrefix() . "system_modules`.`phpfile` , `" . $this->getPrefix() . "system_module_translations`.`title`
FROM `" . $this->getPrefix() . "system_modules` INNER JOIN `" . $this->getPrefix() . "system_module_translations`
ON (`" . $this->getPrefix() . "system_modules`.`id` = `" . $this->getPrefix() . "system_module_translations`.`module_id`) WHERE 1
";

        $sql .= " AND `" . $this->getPrefix() . "system_module_translations`.`lang_id` = " . $this->getLangId();

        if (!empty($param['level'])) {
            $sql .= " AND `" . $this->getPrefix() . "system_modules`.`level` = " . $param['level'];
        }

        if (!empty($param['parent'])) {
            $sql .= " AND `" . $this->getPrefix() . "system_modules`.`parent_id` = " . $param['parent'];
        }

        if (!empty($param['published'])) {
            if ($param['published'] == "yes") {
                $sql .= " AND `" . $this->getPrefix() . "system_modules`.`published` = 1 ";
            }
        }




        $sql .= " ORDER BY `" . $this->getPrefix() . "system_modules`.`position` ASC";



        $data = $this->getResults($sql);
        return $data;
    }

    public function getUserMenus($param = array())
    {

        $sql = "
        SELECT
            `jb_system_module_translations`.`title`
            , `jb_system_modules`.`title` AS `module_name`
            , `jb_system_modules`.`id`
            , `jb_system_modules`.`parent_id`
            , `jb_system_modules`.`level`
            , `jb_system_modules`.`bundle`
            , `jb_system_modules`.`phpfile`
            , `jb_system_modules`.`extra`
            , `jb_system_modules`.`published`
            , `jb_login_user_modules`.`can_add`
            , `jb_login_user_modules`.`can_edit`
            , `jb_login_user_modules`.`can_delete`
            , `jb_login_user_modules`.`can_print`
            , `jb_login_user_modules`.`can_export`
        FROM
            `jb_login_user_modules`
            INNER JOIN `jb_system_modules`
                ON (`jb_login_user_modules`.`system_module_id` = `jb_system_modules`.`id`)
            INNER JOIN `jb_system_module_translations`
                ON (`jb_system_module_translations`.`module_id` = `jb_system_modules`.`id`)
        WHERE 1";

        $sql .= " AND `" . $this->getPrefix() . "system_module_translations`.`lang_id` = " . $this->getLangId();

        if (!empty($param['user'])) {
            $sql .= " AND `" . $this->getPrefix() . "login_user_modules`.`user_id` = " . $param['user'];
        }

        if (!empty($param['level'])) {
            $sql .= " AND `" . $this->getPrefix() . "system_modules`.`level` = " . $param['level'];
        }

        if (!empty($param['parent'])) {
            $sql .= " AND `" . $this->getPrefix() . "system_modules`.`parent_id` = " . $param['parent'];
        }

        $sql .= " AND `" . $this->getPrefix() . "system_modules`.`published` = 1";

        $sql .= " ORDER BY `" . $this->getPrefix() . "system_modules`.`position` ASC";


        $data = $this->getResults($sql);

        return $data;
    }

    public function getTransLabels()
    {
        $sql = "SELECT * FROM " . $this->getPrefix() . "unique_labels WHERE 1";
        $sql .= " AND `" . $this->getPrefix() . "unique_labels`.`lang_id` = " . $this->getLangId();
        $data = $this->getResults($sql);
        $stringData = array();
        foreach ($data as $row) {
            $stringData[$row['label_key']] = $row['label_title'];
        }
        return $stringData;
    }

    public function checkLogin($user, $pwd)
    {
        $sql = "SELECT * FROM " . $this->getPrefix() . "users WHERE username = '$user' AND password = '$pwd' AND published = 1 LIMIT 1";
        $res = $this->getSingle($sql);
        return $res;
    }

    public function insertSystemModules($group, $data = array())
    {
        if ($group) {
            $tableName = $this->getPrefix() . "group_modules";
        } else {
            $tableName = $this->getPrefix() . "user_modules";
        }

        $columns = $this->getTableCols($tableName);
        $ids = $this->insert_multi($tableName, $columns, $data);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $ids . " inserted");
        }
        return $return;
    }

    public function insertBranches($group, $data = array())
    {

        if ($group) {
            $tableName = $this->getPrefix() . "group_branches";
        } else {
            $tableName = $this->getPrefix() . "user_branches";
        }
        $columns = $this->getTableCols($tableName);
        $ids = $this->insert_multi($tableName, $columns, $data);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $ids . " inserted");
        }
        return $return;
    }

    public function getUserModueles($param = array())
    {
        $sql = "SELECT * FROM " . $this->getPrefix() . "user_modules WHERE 1";
        if (!empty($param['id'])) {
            $sql .= " AND system_user_id = " . $param['id'];
        }
        $res = $this->getResults($sql);
        $data = array();
        foreach ($res as $row) {
            $data[$row['system_module_id']] = $row;
        }
        return $data;
    }

    public function getGroupModules($param = array())
    {
        $sql = "SELECT * FROM " . $this->getPrefix() . "group_modules WHERE 1";
        if (!empty($param['id'])) {
            $sql .= " AND system_group_id = " . $param['id'];
        }
        $res = $this->getResults($sql);
        $data = array();

        foreach ($res as $row) {
            $data[$row['system_module_id']] = $row;
        }
        return $data;
    }


    public function getUserBranchAll($param = array())
    {
        $sql = "SELECT
    `jb_user_branches`.`branch_id`
    , `jb_user_branches`.`user_id`
    , `jb_branches`.`title` AS `branch_title`
FROM
    `jb_user_branches`
    INNER JOIN `jb_branches` 
        ON (`jb_user_branches`.`branch_id` = `jb_branches`.`id`) WHERE 1";

        if (!empty($param['user_id'])) {
            $sql .= " AND user_id = " . $param['user_id'];
        }


        return $this->getResults($sql);
    }

    public function getUserBranches($param = array())
    {
        $sql = "SELECT * FROM " . $this->getPrefix() . "user_branches WHERE 1";
        if (!empty($param['id'])) {
            $sql .= " AND user_id = " . $param['id'];
        }
        $res = $this->getResults($sql);
        $data = array();
        foreach ($res as $row) {
            $data[$row['branch_id']] = $row;
        }
        return $data;
    }

    public function getGroupBranches($param = array())
    {
        $sql = "SELECT * FROM " . $this->getPrefix() . "group_branches WHERE 1";
        if (!empty($param['id'])) {
            $sql .= " AND group_id = " . $param['id'];
        }
        $res = $this->getResults($sql);
        $data = array();
        foreach ($res as $row) {
            $data[$row['branch_id']] = $row;
        }
        return $data;
    }

    public function insertLoginUserBrnaches($param = array())
    {

        $userId = $param['user'];
        $grupId = $param['group'];

        $userBranches = $this->getUserBranches(array("id" => $userId));
        $groupBranches = $this->getGroupBranches(array("id" => $grupId));
        //$this->removeLoginBrnaches($userId);

        $branchArr = array();

        foreach ($userBranches as $userBranche) {
            $branchArr[$userBranche['branch_id']] = $userBranche['branch_id'];
        }

        foreach ($groupBranches as $groupBranche) {
            $branchArr[$groupBranche['branch_id']] = $groupBranche['branch_id'];
        }


        $tableName = $this->getPrefix() . "login_user_branches";

        $columns = $this->getTableCols($tableName);

        $vals = array();

        foreach ($branchArr as $branch) {
            $vals[] = array("$userId", "$branch");
        }

        $this->removeLoginBrnaches($userId);
        $this->insert_multi($tableName, $columns, $vals, false);
    }

    public function loginUserBranches()
    {
        $tableName = $this->getPrefix() . "login_user_branches";
        $sql = "SELECT branch_id FROM " . $tableName . " WHERE user_id = " . $this->getUserId();
        $res = $this->getResults($sql);
        return $res;
    }

    public function loginUserBranchesIds($col = "branch_id")
    {
        $branches = $this->loginUserBranches();
        $branchString = "";
        foreach ($branches as $branch) {
            $branchString .= " OR " . $col . " = " . $branch['branch_id'];
        }
        return $branchString;
    }

    public function removeLoginBrnaches($id)
    {
        $where = array('user_id' => $id);
        $this->delete('jb_login_user_branches', $where, "");
    }

    public function UserEdit($id)
    {
        $sql = "SELECT * FROM " . $this->getPrefix() . "users WHERE id = $id LIMIT 1";
        $res = $this->getResults($sql);
        return $res[0];
    }

    public function UserGroups()
    {
        $sql = "SELECT * FROM " . $this->getPrefix() . "user_groups WHERE 1";
        $sql .= " AND id <> 3 ";
        $res = $this->getResults($sql);
        return $res;
    }

    public function allBranches()
    {
        $sql = "SELECT * FROM " . $this->getPrefix() . "branches WHERE 1 AND published = 1";
        $res = $this->getResults($sql);
        return $res;
    }

    public function userBranches()
    {

        $user = $this->getUserId();
        $sql = "SELECT
            `jb_branches`.`id`
            , `jb_branches`.`title`
        FROM
            `jb_login_user_branches`
            INNER JOIN `jb_branches`
                ON (`jb_login_user_branches`.`branch_id` = `jb_branches`.`id`)
        WHERE (`jb_branches`.`published` = 1
            AND `jb_login_user_branches`.`user_id` = $user)
        ORDER BY `jb_branches`.`position` ASC";
        $res = $this->getResults($sql);
        return $res;
    }

    public function allClasses()
    {
        $sql = "SELECT * FROM " . $this->getPrefix() . "classes WHERE 1 AND published = 1";
        $res = $this->getResults($sql);
        return $res;
    }

    public function sessionClasses($session = "", $branch = "")
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
            `jb_classes`.`id`
            , `jb_classes`.`title`
        FROM
            `jb_session_classes`
            INNER JOIN `jb_classes`
                ON (`jb_session_classes`.`class_id` = `jb_classes`.`id`)
        WHERE (`jb_session_classes`.`session_id` = $session
            AND `jb_session_classes`.`branch_id` = $branch AND `jb_classes`.`class_type` = 'current') ";

        $sql .= " ORDER BY `jb_classes`.`position` ASC ";

        $res = $this->getResults($sql);

        return $res;
    }

    public function sessionCurrentClasses($session = "", $branch = "")
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
            `jb_classes`.`id`
            , `jb_classes`.`title`
        FROM
            `jb_session_classes`
            INNER JOIN `jb_classes`
                ON (`jb_session_classes`.`class_id` = `jb_classes`.`id`)
        WHERE (`jb_session_classes`.`session_id` = $session
            AND `jb_session_classes`.`branch_id` = $branch) ";

        $sql .= " AND `jb_classes`.`class_type` = '" . $this->stuStatus("current") . "'";

        $sql .= " ORDER BY `jb_classes`.`position` ASC ";

        $res = $this->getResults($sql);

        return $res;
    }

    public function sessionSections($session, $class = "", $branch = "")
    {
        $sql = "SELECT
            `jb_sections`.`id`
            , `jb_sections`.`title`
        FROM
            `jb_session_sections`
            INNER JOIN `jb_sections`
                ON (`jb_session_sections`.`section_id` = `jb_sections`.`id`)
        WHERE 1 AND `jb_session_sections`.`session_id` = $session";

        $sql .= " AND `jb_session_sections`.`branch_id` = $branch";


        if (!empty($class)) {
            $sql .= " AND `jb_session_sections`.`class_id` = $class";
        }

        $sql .= " ORDER BY `jb_sections`.`position` ASC";


        $res = $this->getResults($sql);
        return $res;
    }

    public function allSections()
    {
        $sql = "SELECT * FROM " . $this->getPrefix() . "sections WHERE 1 AND published = 1";
        $res = $this->getResults($sql);
        return $res;
    }

    public function allSessions()
    {
        $sql = "SELECT * FROM " . $this->getPrefix() . "sessions WHERE 1 AND published = 1 ORDER BY end_date DESC";
        $res = $this->getResults($sql);

        $data = array();

        foreach ($res as $row) {
            $data[] = array("id" => $row['id'], "title" => $row['title']);
        }

        return $data;
    }

    public function getCurrentSession($id = "")
    {
        $sql = "SELECT * FROM " . $this->getPrefix() . "sessions WHERE 1 AND published = 1 ";
        if (!empty($id)) {
            $sql .= "AND id = $id ";
        }
        $sql .= "ORDER BY end_date DESC LIMIT 1";
        $res = $this->getSingle($sql);
        return $res;
    }

    public function getUserModules($id)
    {
        $sql = "SELECT
            `jb_user_modules`.`system_module_id`
            , `jb_user_modules`.`can_add`
            , `jb_user_modules`.`can_edit`
            , `jb_user_modules`.`can_delete`
            , `jb_user_modules`.`can_print`
            , `jb_user_modules`.`can_export`
            , `jb_system_modules`.`bundle`
            , `jb_system_modules`.`phpfile`
        FROM
            `jb_user_modules`
            INNER JOIN `jb_users`
                ON (`jb_user_modules`.`system_user_id` = `jb_users`.`id`)
            INNER JOIN `jb_system_modules`
                ON (`jb_system_modules`.`id` = `jb_user_modules`.`system_module_id`) WHERE 1";

        $sql .= " AND jb_users.id = $id";

        $res = $this->getResults($sql);

        return $res;
    }

    public function getUserGroupMdules($id)
    {

        $sql = "SELECT
            `jb_group_modules`.`system_module_id`
            , `jb_group_modules`.`can_add`
            , `jb_group_modules`.`can_edit`
            , `jb_group_modules`.`can_delete`
            , `jb_group_modules`.`can_print`
            , `jb_group_modules`.`can_export`
            , `jb_system_modules`.`bundle`
            , `jb_system_modules`.`phpfile`
        FROM
            `jb_group_modules`
            INNER JOIN `jb_users`
                ON (`jb_group_modules`.`system_group_id` = `jb_users`.`group_id`)
            INNER JOIN `jb_system_modules`
                ON (`jb_system_modules`.`id` = `jb_group_modules`.`system_module_id`) WHERE 1";

        $sql .= " AND jb_users.id = $id";


        $res = $this->getResults($sql);

        return $res;
    }

    public function userUniqueModules($id)
    {

        $loginModules = array();

        $userModules = $this->getUserModules($id);
        $userGroupMudules = $this->getUserGroupMdules($id);

        foreach ($userModules as $userModule) {
            $loginModules[$userModule['system_module_id']] = $userModule;
        }

        foreach ($userGroupMudules as $userGroupMudule) {
            $loginModules[$userGroupMudule['system_module_id']] = $userGroupMudule;
        }

        return $loginModules;
    }

    public function removeLoginModules($id)
    {
        $where = array('user_id' => $id);
        $this->delete('jb_login_user_modules', $where, "");
    }

    public function removeLastModules($id, $from = "group")
    {

        if ($from == "group") {
            $whereColumn = "system_group_id";
            $table = "jb_group_modules";
        } else {
            $whereColumn = "system_user_id";
            $table = "jb_user_modules";
        }

        $where = array($whereColumn => $id);
        $this->delete($table, $where, "");
    }

    public function removeLastBranches($id, $from = "group")
    {

        if ($from == "group") {
            $whereColumn = "group_id";
            $table = "jb_group_branches";
        } else {
            $whereColumn = "user_id";
            $table = "jb_user_branches";
        }

        $where = array($whereColumn => $id);
        $this->delete($table, $where, "");
    }

    public function insertLoginModules($id)
    {

        $modules = $this->userUniqueModules($id);


        $tableName = $this->getPrefix() . "login_user_modules";

        $columns = $this->getTableCols($tableName);

        $vals = array();

        foreach ($modules as $module) {

            $systemModuleId = $module['system_module_id'];
            $can_add = $module['can_add'];
            $can_edit = $module['can_edit'];
            $can_delete = $module['can_delete'];
            $can_print = $module['can_print'];
            $can_export = $module['can_export'];
            //$bundle = $module['bundle'];
            //$phpfile = $module['phpfile'];
            $vals[] = array("$id", "$systemModuleId", "$can_add", "$can_edit", "$can_delete", "$can_print", "$can_export");
        }

        $this->removeLoginModules($id);
        $this->insert_multi($tableName, $columns, $vals, false);
    }

    public function checkCurrenPage($bundle, $phpfile)
    {

        $userId = $this->getUserId();

        /*$sql = "SELECT
            `jb_login_user_modules`.`can_add`
            , `jb_login_user_modules`.`can_edit`
            , `jb_login_user_modules`.`can_delete`
            , `jb_login_user_modules`.`can_print`
            , `jb_login_user_modules`.`can_export`
            , `jb_system_modules`.`id`
            , `jb_system_modules`.`title`
        FROM
            `jb_login_user_modules`
            INNER JOIN `jb_system_modules`
                ON (`jb_login_user_modules`.`system_module_id` = `jb_system_modules`.`id`)";

        $sql .= " AND jb_login_user_modules.user_id = $userId";
        $sql .= " AND jb_system_modules.bundle = '$bundle'";
        $sql .= " AND jb_system_modules.phpfile = '$phpfile'";

        $sql .= " LIMIT 1";*/



        $sql = "SELECT
            `jb_system_modules`.`id`
            ,`jb_system_modules`.`parent_id`
            , `jb_system_module_translations`.`title`
            , mt.`title` AS parent_title
            , `jb_login_user_modules`.`can_add`
            , `jb_login_user_modules`.`can_edit`
            , `jb_login_user_modules`.`can_delete`
            , `jb_login_user_modules`.`can_print`
            , `jb_login_user_modules`.`can_export`
        FROM
            `jb_system_modules`
            INNER JOIN `jb_system_module_translations` 
                ON (`jb_system_modules`.`id` = `jb_system_module_translations`.`module_id`)
            INNER JOIN `jb_login_user_modules` 
                ON (`jb_system_modules`.`id` = `jb_login_user_modules`.`system_module_id`)
            LEFT JOIN `jb_system_module_translations` mt
                ON (`jb_system_modules`.`parent_id` = mt.`module_id` AND mt.`lang_id` = " . $this->getLangId() . ")
            WHERE 1    ";

        $sql .= " AND jb_login_user_modules.user_id = $userId";
        $sql .= " AND jb_system_modules.bundle = '$bundle'";
        $sql .= " AND jb_system_modules.phpfile = '$phpfile'";
        $sql .= " AND `jb_system_module_translations`.`lang_id` = " . $this->getLangId();

        $sql .= " LIMIT 1";

        $data = $this->getSingle($sql);
        return $data;
    }

    public function insertSessionClasses($data = array())
    {


        $tableName = $this->getPrefix() . "session_classes";

        $columns = $this->getTableCols($tableName);
        $ids = $this->insert_multi($tableName, $columns, $data);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $ids . " inserted");
        }
        return $return;
    }

    public function removeSessionClasses($branch, $session, $deletedIds = array())
    {
        $tableName = $this->getPrefix() . "session_classes";
        if (!empty($deletedIds)) {
            if (is_array($deletedIds)) {
                $ids = implode(",", $deletedIds);
                $sql = "DELETE FROM `$tableName` WHERE branch_id = $branch AND session_id = $session AND class_id IN ($ids)";
                $this->query($sql);
            }
        }
    }

    public function getSessionClasses($session, $branch)
    {

        $tableName = $this->getPrefix() . "session_classes";

        $sql = "SELECT * FROM `$tableName` WHERE 1 AND session_id = $session AND branch_id = $branch";
        $res = $this->getResults($sql);

        return $res;
    }

    public function insertSessionSections($data = array())
    {


        $tableName = $this->getPrefix() . "session_sections";

        $columns = $this->getTableCols($tableName);
        $ids = $this->insert_multi($tableName, $columns, $data);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $ids . " inserted");
        }
        return $return;
    }

    public function removeSessionSections($session, $branch)
    {
        $tableName = $this->getPrefix() . "session_sections";
        $this->query("DELETE FROM $tableName WHERE session_id = $session AND branch_id = $branch");
    }

    public function getSessionSections($session, $branch)
    {

        $tableName = $this->getPrefix() . "session_sections";

        $sql = "SELECT * FROM `$tableName` WHERE 1 AND session_id = $session AND branch_id = $branch";
        $res = $this->getResults($sql);

        return $res;
    }

    public function getProfileCols()
    {
        $colsProfile = $this->getTableCols("jb_student_profile");
        $colsParent = $this->getTableCols("jb_student_parents");
        $langId = $this->getLangId();


        $cols = array_merge($colsProfile, $colsParent);



        $cols[] = "doa";
        $cols[] = "eng_name";
        $cols[] = "eng_fname";

        $sql = "SELECT * FROM jb_unique_labels WHERE lang_id = $langId";
        $res = $this->getResults($sql);
        $columns = array();
        $data = array();

        foreach ($res as $row) {
            $data[$row['label_key']] = $row['label_title'];
        }

        foreach ($cols as $key) {

            if (
                $key == 'id'
                || $key == 'student_id'
                || $key == 'parents_id'
                || $key == 'username'
                || $key == 'password'
                || $key == 'created_user_id'
                || $key == 'created'
            ) {
                continue;
            }

            if (isset($data[$key])) {
                $columns[] = array("id" => $key, "title" => $data[$key]);
            } else {
                $columns[] = array("id" => $key, "title" => $key);
            }
        }

        return $columns;
    }

    public function getTitleTable($table, $where = "")
    {
        $table = $this->getPrefix() . $table;
        $sql = "SELECT id, title FROM $table WHERE published = 1 ";

        if (!empty($where)) {
            $sql .= $where;
        }

        $sql .= " ORDER BY position";
        $res = $this->getResults($sql);
        return $res;
    }

    public function getID($id)
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
            `" . $pr . "students`.`id`
            , `" . $pr . "students`.`name`
            , `" . $pr . "students`.`gender`
            , `" . $pr . "students`.`fname`
            , `" . $pr . "students`.`branch_id`
            , `" . $pr . "students`.`class_id`
            , `" . $pr . "students`.`section_id`
            , `" . $pr . "students`.`session_id`
            , `" . $pr . "login_user_branches`.`user_id`
        FROM
            `" . $pr . "students`
            INNER JOIN `" . $pr . "login_user_branches`
                ON (`" . $pr . "login_user_branches`.`branch_id` = `" . $pr . "students`.`branch_id`) WHERE 1 ";
        $sql .= " AND `" . $pr . "students`.`id` = $id";
        $sql .= " AND `" . $pr . "login_user_branches`.`user_id` = " . $this->getUserId();

        $sql .= " AND `" . $pr . "students`.`student_status` = '" . $this->stuStatus("current") . "'";

        $sql .= " GROUP BY  `" . $pr . "students`.`id`";
        $res = $this->getSingle($sql);

        return $res;
    }

    public function getSessionByDate($date)
    {
        $pr = $this->getPrefix();
        $sql = "SELECT id FROM " . $pr . "sessions WHERE `start_date` <= '$date' AND `end_date` >= '$date'";
        $res = $this->getSingle($sql);
        return $res["id"];
    }

    public function getSessionByStartAndEnd($start, $end)
    {

        $pr = $this->getPrefix();

        $sql = "SELECT id FROM " . $pr . "sessions WHERE 1 ";

        $sql .= "AND
                    (
        `" . $pr . "sessions`.`start_date` BETWEEN '$start' AND '$end'
        OR `" . $pr . "sessions`.`end_date` BETWEEN '$start' AND '$end'
        OR '$start' BETWEEN `" . $pr . "sessions`.`start_date` AND `" . $pr . "sessions`.`end_date`
        OR '$end' BETWEEN `" . $pr . "sessions`.`start_date` AND `" . $pr . "sessions`.`end_date`
        )
        ";

        $sql .= " LIMIT 1";

        $res = $this->getSingle($sql);
        return $res["id"];
    }

    public function getClassType($id)
    {
        $pr = $this->getPrefix();
        $sql = "SELECT class_type FROM " . $pr . "classes WHERE id = $id";
        $res = $this->getSingle($sql);
        return $res['class_type'];
    }

    public function getClassIDByType($type)
    {
        $pr = $this->getPrefix();
        $sql = "SELECT id FROM " . $pr . "classes WHERE class_type = '$type'";
        $res = $this->getSingle($sql);
        return $res['id'];
    }

    public function insertKeys($table, $data = array())
    {


        $tableName = $this->getPrefix() . $table;

        $columns = $this->getAllTableCols($tableName);
        $ids = $this->insert_multi($tableName, $columns, $data);
        if (!$ids) {
            $return = array("status" => false, "msg" => $this->getTrans("insert_failed"));
        } else {
            $return = array("status" => true, "msg" => $ids . " " . $this->getTrans("inserted"));
        }
        return $return;
    }

    public function GetDataSettings($key)
    {
        $sql = "SELECT data_val FROM jb_data_settings WHERE data_key = '$key'";
        $res = $this->getSingle($sql);
        return $res['data_val'];
    }

    public function DataKeys($key)
    {
        $keys = array();
        $keys['admin_app'] = 'admin_app';
        return $keys[$key];
    }

    public function getUsers()
    {
        $sql = "SELECT id, name AS title FROM jb_users WHERE published = 1";
        $res = $this->getResults($sql);
        return $res;
    }

    public function getZones()
    {
        $sql = "SELECT * FROM jb_zones WHERE published = 1";
        $res = $this->getResults($sql);
        return $res;
    }

    public function getClassModules()
    {
        $sql = "SELECT * FROM jb_class_modules WHERE published = 1";
        $res = $this->getResults($sql);
        return $res;
    }

    public function getZoneBranches($param = array())
    {
        $sql = "SELECT id, title, branch_address FROM jb_branches WHERE published = 1";
        if (!empty($param['zone'])) {
            $sql .= " AND zone_id = " . $param['zone'];
        }
        $res = $this->getResults($sql);
        return $res;
    }


    public function getFileKeys($bundle, $phpfile)
    {
        $sql = "SELECT trans_keys FROM jb_trans_keys WHERE bundle = '$bundle' AND phpfile = '$phpfile'";
        $res = $this->getResults($sql);


        $data = array();
        if (!empty($res)) {
            foreach ($res as $row) {
                $data[] = $row['trans_keys'];
            }
        }

        return $data;
    }


    public function SystemLanguages()
    {
        $sql = "SELECT * FROM " . $this->getPrefix() . "languages WHERE 1";
        $res = $this->getResults($sql);
        return $res;
    }


    public function getSystemBundles()
    {
        $sql = "SELECT bundle FROM " . $this->getPrefix() . "system_modules WHERE 1 GROUP BY bundle";
        $res = $this->getResults($sql);
        return $res;
    }
}
