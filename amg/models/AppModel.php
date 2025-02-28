<?php

class AppModel extends BaseModel
{

    public function deleteQuery($id)
    {
        $where = array('id' => $id);
        $this->delete('jb_quries', $where, 1);
    }

    public function updateContact($update, $where)
    {
        $table = 'jb_quries';
        return $this->update($table, $update, $where, 1);
    }

    public function GetQuriesData($param = array())
    {
        $sql = "SELECT
        `jb_quries`.`id`
        , `jb_quries`.`student_id`
        , `jb_quries`.`date`
        , `jb_quries`.`query_desc`
        , `jb_students`.`name`
        , `jb_students`.`fname`
        , `jb_classes`.`title` AS `class_title`
    FROM
        `jb_quries`
        INNER JOIN `jb_students` 
            ON (`jb_quries`.`student_id` = `jb_students`.`id`)
        INNER JOIN `jb_classes` 
            ON (`jb_students`.`class_id` = `jb_classes`.`id`)
    WHERE 1 ";

        $sql .= " AND `jb_quries`.`is_replied`  = 0";

        if (!empty($param['id'])) {
            $sql .= " AND `jb_quries`.`id` = " . $param['id'];
        }


        $sql .= " ORDER BY date DESC";

        $res = $this->getResults($sql);
        return $res;
    }

    public function fcmApiToken()
    {
        return 'bhjA4587';
    }

    public function fcmCompany()
    {
        return 'ALBADAR';
    }

    public function fcmApiUrl()
    {
        return 'https://amg.flyharam.com';
    }



    public function makeQueryFcmPayload($actionId, $device, $title, $message, $clickAction)
    {
        $payload['token'] = $this->fcmApiToken();


        $data = [
            "actionId" => $actionId,
            "priority" => "HIGH",
            "title" => $title,
            "content_available" => true,
            "bodyText" => $message,
            "clickAction" => $clickAction
        ];

        $payload['data'][] = array(
            "devices" => array($device),
            "msgbody" => $data,
            "compony" => $this->fcmCompany(),
            "datetime" => date("Y-m-d H:i:s")
        );


        return json_encode($payload);
    }

    public function makeBulkFcmPayload($dataToSend)
    {
        $payload['token'] = $this->fcmApiToken();

        foreach ($dataToSend as $dataItem) {
            $payload['data'][] = [
                "devices" => $dataItem['devices'],
                "msgbody" => $dataItem['msgbody'],
                "compony" => $dataItem['compony'],
                "datetime" => $dataItem['datetime']
            ];
        }

        return json_encode($payload);
    }




    function postFCm($data)
    {

        global $client;
        $url = $this->fcmApiUrl();
        $url .= "/insertfcm.php";

        try {

            return $client->request('POST', $url, [
                'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                'body' => $data
            ]);

        } catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
        }

        return null;

    }

    public function isLiveBranch($branch)
    {

    }




    protected function getTableName()
    {
    }

}



