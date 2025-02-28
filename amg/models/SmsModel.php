<?php
require_once VENDOR . '/autoload.php';


use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\Notification;

//https://console.cron-job.org/dashboard
//https://console.cron-job.org/jobs
// amgdevs id

/**
 * Created by PhpStorm.
 * User: mzia
 * Date: 2/14/2017
 * Time: 2:24 PM
 */

class SmsModel extends BaseModel
{

    const inst = null;
    private $factory;
    private $telenorSession = "e818be0680d94288a69bbd2bf3e33eee";
    private $fcmServerUrl = "https://amg.flyharam.com";
    public $fcmServerToken = "bhjA4587";
    public $fcmServerCompany = "albadar";

    /**
     * Call this method to get singleton
     *
     * @return SmsModel
     */
    public static function Instance()
    {
        if (self::inst === null) {
            $inst = new SmsModel();
        }
        return $inst;
    }

    protected function __construct()
    {
        parent::__construct();
        //$this->factory = (new Factory)->withServiceAccount(AMG . DRS . 'conn' . DRS . "fcm-push-key.json");
    }


    protected function getTableName()
    {
    }


    public function getNumbers()
    {
        $numberCols = array("amergency_mobile", "father_mobile", "mother_mobile", "guardian_mobile");
        $numbers = array();
        foreach ($numberCols as $numberCol) {
            $numbers[] = array("id" => $numberCol, "title" => $this->getNumberLabels($numberCol));
        }

        return $numbers;
    }

    public function getNumberLabels($key)
    {
        $label = array(
            "ar" => array(
                "home_fone" => "هاتف المنزل",
                "father_mobile" => "ھاتف الاب",
                "mother_mobile" => "ھاتف الام",
                "gargin_mobile" => "ھاتف ولی الامر",
                "amergency_mobile" => "هاتف الطوارئ"
            ),
            "ur" => array(
                "home_fone" => "گھر کا فون",
                "father_mobile" => "والد کا نمبر",
                "mother_mobile" => "والدہ کا نمبر",
                "gargin_mobile" => "سرپرست فون",
                "amergency_mobile" => "ہنگامی فون"
            ),
            "en" => array(
                "home_fone" => "Home Fone",
                "father_mobile" => "Father Fone",
                "mother_mobile" => "Mother Fone",
                "gargin_mobile" => "Guardian Fone",
                "amergency_mobile" => "Amergency Fone"
            )
        );

        return $label[$this->getLang()][$key];
    }


    public function SendSMS($phoneNoRecip, $msgText)
    {


        $url = 'https://telenorcsms.com.pk:27677/corporate_sms2/api/sendsms.jsp';


        $num = '92' . substr($phoneNoRecip, -10);

        $number = '';

        if (strlen($num) == 12) {
            $number .= str_replace("+", "", $num);
        }





        $params = array(
            'session_id' => $this->telenorSession,
            'to' => $number,
            'text' => $msgText,
            'mask' => 'BIT HR.',
        );

        if (!empty($number)) {
            $curl = curl_init();

            $url_with_params = $url . '?' . http_build_query($params);

            curl_setopt($curl, CURLOPT_URL, $url_with_params);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            // Execute cURL session
            $response = curl_exec($curl);

            curl_close($curl);


            // echo $url_with_params . '<br />';

            // if ($response === false) {
            //     echo 'cURL error: ' . curl_error($curl);
            // } else {
            //     echo 'Response: ' . $response;
            // }
        }
    }

    public function SendSMS2($phoneNoRecip, $msgText)
    {
        $langLang = Tools::getLang();

        if ($langLang == "en") {
            $lang = "English";
        } else {
            $lang = "Urdu";
        }
        $type = "xml";
        $id = "";
        $pass = "";
        $mask = "";


        $number_arr = explode(",", $phoneNoRecip);
        $numbers = '';
        foreach ($number_arr as $key => $val) {
            $numbers .= '92' . substr($val, -10) . ',';
        }


        $new_numbers = rtrim($numbers, ',');



        // Data for text message
        $to = $new_numbers;
        $message = $msgText;
        $message = urlencode($message);
        // Prepare data for POST request
        $data = "id=" . $id . "&pass=" . $pass . "&msg=" . $message . "&to=" . $to . "&lang=" . $lang . "&mask=" . $mask . "&type=" . $type;
        // Send the POST request with cURL



        $ch = curl_init('http://www.sms4connect.com/api/sendsms.php/sendsms/url');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch); //This is the result from SMS4CONNECT
        curl_close($ch);
    }

    public function getDevices($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
        `jb_students`.`id`
        , `jb_students`.`name`
        , `jb_students`.`fname`
        , `jb_students`.`gender`
        , `jb_students`.`grnumber`
        , `jb_devices`.`device_token`
        , `jb_devices`.`os`
    FROM
        `jb_devices`
        INNER JOIN `jb_students`
            ON (`jb_students`.`id` = `jb_devices`.`student_id`) WHERE 1";

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "students`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "students`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "students`.`section_id` = " . $param['section'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "students`.`session_id` = " . $param['session'];
        }

        if (!empty($param['id'])) {
            $sql .= " AND `" . $pr . "students`.`id` = " . $param['id'];
        }

        if (!empty($param['ids'])) {
            $sql .= " AND `" . $pr . "students`.`id` IN (" . $param['ids'] . ")";
        }


        return $this->getResults($sql);
    }

    public function getNumber($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
            `jb_students`.`id`
            ,`jb_students`.`name`
            ,`jb_students`.`fname`
            ,`jb_students`.`gender`
            ,`jb_students`.`grnumber`
            , `jb_student_parents`.`home_fone`
            , `jb_student_parents`.`father_mobile`
            , `jb_student_parents`.`mother_mobile`
            , `jb_student_parents`.`gargin_mobile`
            , `jb_student_parents`.`amergency_mobile`
        FROM
            `jb_students`
            INNER JOIN `jb_student_profile`
                ON (`jb_students`.`id` = `jb_student_profile`.`student_id`)
            INNER JOIN `jb_student_parents`
                ON (`jb_students`.`id` = `jb_student_parents`.`student_id`) WHERE 1";


        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "students`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "students`.`class_id` = " . $param['class'];
        }

        if (!empty($param['classes'])) {

            if (is_array($param['classes'])) {

                if ($param['classes'][0] > 0) {
                    $tool = $this->toolObj();
                    $i = 0;
                    $sql .= " AND (";
                    foreach ($param['classes'] as $key) {


                        if ($i > 0) {
                            $sql .= " OR ";
                        }
                        $sql .= "`" . $pr . "students`.`class_id` = " . $tool->GetExplodedInt($key);
                        $i++;
                    }
                    $sql .= ")";
                }
            }
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "students`.`section_id` = " . $param['section'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "students`.`session_id` = " . $param['session'];
        }

        if (!empty($param['id'])) {
            $sql .= " AND `" . $pr . "students`.`id` = " . $param['id'];
        }

        if (!empty($param['parents'])) {
            $sql .= " AND `" . $pr . "student_parents`.`id` = " . $param['parents'];
        }

        //echo '<pre>'; print_r($sql); echo '</pre>';


        $res = $this->getResults($sql);

        if(empty($res)){
            return array();
        }

        if (count($res) <= 1) {
            $row = $res[0];
            if (isset($param['phone'])) {
                return $row[$param['phone']];
            } else {
                return $row['father_mobile'];
            }
        } else {
            $row = $res;
        }



        return $row;
    }

    public function insertNotification($data = array())
    {
        $tableName = $this->getPrefix() . "notifications";
        $this->insert($tableName, ($data));
        return $this->lastid();
    }


    public function insertNotifications($data = array())
    {
        $tableName = $this->getPrefix() . "notifications";
        $ins = $this->insertBulkWithoutId($tableName, $data, false);


        return $ins;
    }

    public function sendNotificationToMultiple($devices = array(), $data = array())
    {

        $messaging = $this->factory->createMessaging();
        $message = CloudMessage::new()->withData(
            $data
        );

        $messaging->sendMulticast($message, $devices);
    }


    public function sendNotificationToDevice($deviceToken, $data = array())
    {



        $messaging = $this->factory->createMessaging();
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create('Title', 'Body')) // optional
            ->withData($data) // optional
        ;

        $messaging->send($message);
    }





    public function sendFcmDataToServer($data)
    {

        $ch = curl_init();
        $fcmServerUrl = $this->fcmServerUrl . "/insertfcm.php";

        curl_setopt($ch, CURLOPT_URL, $fcmServerUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'cURL Error: ' . curl_error($ch);
        } else {
            echo "Response: " . $response;
            echo "\n";
            echo "cURL Info: \n";
            print_r(curl_getinfo($ch));
        }

        curl_close($ch);

    }

}
