<?php

class ErrorPage{
    private $statusCode = 404;

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }








    function headerStatus($code)
    {
            $status_codes = array(
                    100 => 'Continue',
                    101 => 'Switching Protocols',
                    102 => 'Processing',
                    200 => 'OK',
                    201 => 'Created',
                    202 => 'Accepted',
                    203 => 'Non-Authoritative Information',
                    204 => 'No Content',
                    205 => 'Reset Content',
                    206 => 'Partial Content',
                    207 => 'Multi-Status',
                    300 => 'Multiple Choices',
                    301 => 'Moved Permanently',
                    302 => 'Found',
                    303 => 'See Other',
                    304 => 'Not Modified',
                    305 => 'Use Proxy',
                    307 => 'Temporary Redirect',
                    400 => 'Bad Request',
                    401 => 'Unauthorized',
                    402 => 'Payment Required',
                    403 => 'Forbidden',
                    404 => 'Not Found',
                    405 => 'Method Not Allowed',
                    406 => 'Not Acceptable',
                    407 => 'Proxy Authentication Required',
                    408 => 'Request Timeout',
                    409 => 'Conflict',
                    410 => 'Gone',
                    411 => 'Length Required',
                    412 => 'Precondition Failed',
                    413 => 'Request Entity Too Large',
                    414 => 'Request-URI Too Long',
                    415 => 'Unsupported Media Type',
                    416 => 'Requested Range Not Satisfiable',
                    417 => 'Expectation Failed',
                    422 => 'Unprocessable Entity',
                    423 => 'Locked',
                    424 => 'Failed Dependency',
                    426 => 'Upgrade Required',
                    500 => 'Internal Server Error',
                    501 => 'Not Implemented',
                    502 => 'Bad Gateway',
                    503 => 'Service Unavailable',
                    504 => 'Gateway Timeout',
                    505 => 'HTTP Version Not Supported',
                    506 => 'Variant Also Negotiates',
                    507 => 'Insufficient Storage',
                    509 => 'Bandwidth Limit Exceeded',
                    510 => 'Not Extended'
            );


        return $status_codes[$code];

    }

    public function printMessage($code){

        switch($code){

            case 403:
                $messageHeading = "Forbidden";
                $messageText = "You are not allowed to open this page.";
            break;
            default:
                $messageHeading = "Page Not Found";
                $messageText = "You page you are looking for not found in this server.";

        }

        return array("heading" => $messageHeading, "message" => $messageText);
    }

    public function render(){

        $msgArr = $this->printMessage($this->getStatusCode());


        $html = '<div class="social-box">';
        $html .= '<div class="container-fluid body">';
        $html .= '<div class="row-fluid">';
        $html .= '<div class="error-'.$this->getStatusCode().'">';
        $html .= '<i class="icon-remove-sign icon-4x error-icon"></i>';
        $html .= '<h1>'.$msgArr["heading"].'</h1>';
        $html .= '<span class="text-error"><small><strong>Error '.$this->getStatusCode().'</strong></small></span>';
        $html .= '<p>'.$msgArr["message"].'</p>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }




}
