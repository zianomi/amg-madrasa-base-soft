<?php
$rootPath = dirname(__DIR__, 2);
require $rootPath . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";
use \Gumlet\ImageResize;
//use Spatie\ImageOptimizer\OptimizerChainFactory;
//https://resmush.it/api
use SpacesAPI\Spaces;



class AmgSpace
{

    /*
     * Var SpacesConnect
     */
    private $spaceObj;
    private $path;
    private $spaceName = SPACE_NAME;
    private $regionName = SPACE_REGION;
    private $domainName = DOMAIN_NAME;
    private $fileInputName = "file";
    private $spaceKey;
    private $spaceSecret;
    private $accessType = "public";
    private $definedFileName;
    private $imageSize = array();
    private $width = 500;
    private $height = 500;





    private $allowedMimeTypes = array(
        'image/png',
        'image/jpeg',
        'image/gif',
        'application/msword',
        'application/pdf',
        'application/vnd.ms-excel',
        'application/msword',
        'application/vnd.ms-excel',
        'application/vnd.oasis.opendocument.text',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.oasis.opendocument.spreadsheet'
    );

    /**
     * AmgSpace constructor.
     */
    //public function __construct($SPACE_KEY,$SPACE_SECRET)
    public function __construct()
    {

        //$this->spaceObj = new Spaces(SPACE_KEY,SPACE_SECRET,$this->spaceName,$this->regionName);
        $this->spaceObj = new Spaces(SPACE_KEY,SPACE_SECRET);
        $this->spaceObj = $this->spaceObj->space($this->spaceName);
    }

    public function deleteDir($file){
        $this->spaceObj->deleteDirectory($file);
    }

    public function listObjects(){
        return $this->spaceObj->listFiles(DOMAIN_NAME);
    }

    public function listSpaces($path = ""){
        return $this->spaceObj->listFiles($path);
    }


    public function getUploadUrl(){
        $url = "https://";
        $url .= $this->getSpaceName();
        $url .= ".";
        $url .= $this->getRegionName();
        $url .= ".digitaloceanspaces.com";

        return $url;
    }

    public function getCdnUrl(){
        $url = "https://";
        $url .= $this->getSpaceName();
        $url .= ".";
        $url .= $this->getRegionName();
        $url .= ".cdn.digitaloceanspaces.com";

        return $url;
    }

    public function getCdnFileUrl($fileWithPath){
        return $this->getCdnUrl() . "/" . $fileWithPath;
    }

    public function getFileUrl($fileWithPath){
        return $fileWithPath;
    }

    public function getExt($filename){
        $ext     = explode('.', $filename);
        return end($ext);
    }

    public function upload($param = array()){


        $fileNameKey = $this->getFileInputName();
        $fileName = $_FILES[$fileNameKey]['name'];
        $fileTemp = $_FILES[$fileNameKey]['tmp_name'];

        $type = $this->getMimeType($fileTemp);
        if(!$this->checkFileType($type)){
            return array("key" => "fail", "msg" => "File not allowed", "value" => "", "fileName" => "", "type" => "");
        }


        $fileType = $this->mimeTypes($type);

        if(!empty($this->getDefinedFileName())){
            $name = $this->getDefinedFileName();
        }
        else{
            //$name = time() . $this->filterFilename($fileName);
            $name = time() . ".". $this->getExt($fileName);
        }




        /*if( ($type == "image/png") || ($type == "image/jpg") || ($type == "image/jpeg") || ($type == "image/gif")){

            $nameForTemp =  TEMP_IMAGE_DIR . DIRECTORY_SEPARATOR . "1" . $name;;
            $uploadedFileName = TEMP_IMAGE_DIR . DIRECTORY_SEPARATOR . $name;



            move_uploaded_file($fileTemp, $nameForTemp);

            $image = new ImageResize($nameForTemp);
            $image->resizeToLongSide($this->getWidth(),$this->getHeight());
            $image->save($uploadedFileName);
            $fileTemp = $uploadedFileName;
            @unlink($nameForTemp);


        }*/



        //$domainName = str_replace(".","_",$this->getDomainName()) . "/";
        if(empty($this->getPath())){
            $path = date("Y/m");
        }
        else{
            $path = $this->getPath();
        }

        $domainName = "albadar_edu_pk/";

        $saveAs = $domainName . $path . "/" . $name;
        //echo '<pre>'; print_r($saveAs); echo '</pre>';
        //die("");
        $uploadedData = $this->spaceObj->UploadFile($fileTemp,$saveAs);

        $uploadedData->makePublic();
        @unlink($fileTemp);


        if(!empty($uploadedData)){
            return array("key" => "succ",  "msg" => "File uploaded", "value" => $saveAs, "fileName" => $name, "type" => $fileType);
            /*if(is_array($uploadedData)){
                return array("key" => "succ",  "msg" => "File uploaded", "value" => $saveAs, "fileName" => $name, "type" => $fileType);
            }
            else{
                return array("key" => "fail",  "msg" => "Failed to upload to server", "value" => "", "fileName" => "", "type" => "");
            }*/
        }
        else{
            return array("key" => "fail", "value" => "",  "msg" => "Failed to upload to server ", "fileName" => "", "type" => "");
        }

    }

    public function checkFileType($type){
        if(in_array($type,$this->getAllowedMimeTypes())){
            return true;
        }

        return false;
    }

    public function getMimeType($file) {
        $mtype = false;
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mtype = finfo_file($finfo, $file);
            finfo_close($finfo);
        } elseif (function_exists('mime_content_type')) {
            $mtype = mime_content_type($file);
        }
        return $mtype;
    }

    public function mimeTypes($mimeType){

        $mimeTypes = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'docx' => 'application/msword',
            'xlsx' => 'application/vnd.ms-excel',
            'pptx' => 'application/vnd.ms-powerpoint',


            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );


        $mimeTypes = array(
            'txt'=>'text/plain',
            'text/html'=>'htm',
            'text/html'=>'html',
            'text/html'=>'php',
            'text/css'=>'css',
            'application/javascript'=>'js',
            'application/json'=>'json',
            'application/xml'=>'xml',
            'application/x-shockwave-flash'=>'swf',
            'video/x-flv'=>'flv',

// images
            'image/png'=>'png',
            'image/jpeg'=>'jpe',
            'image/jpeg'=>'jpeg',
            'image/jpeg'=>'jpg',
            'image/gif'=>'gif',
            'image/bmp'=>'bmp',
            'image/vnd.microsoft.icon'=>'ico',
            'image/tiff'=>'tiff',
            'image/tiff'=>'tif',
            'image/svg+xml'=>'svg',
            'image/svg+xml'=>'svgz',

// archives
            'application/zip'=>'zip',
            'application/x-rar-compressed'=>'rar',
            'application/x-msdownload'=>'exe',
            'application/x-msdownload'=>'msi',
            'application/vnd.ms-cab-compressed'=>'cab',

// audio/video
            'audio/mpeg'=>'mp3',
            'video/quicktime'=>'qt',
            'video/quicktime'=>'mov',

// adobe
            'application/pdf'=>'pdf',
            'image/vnd.adobe.photoshop'=>'psd',
            'application/postscript'=>'ai',
            'application/postscript'=>'eps',
            'application/postscript'=>'ps',

// ms office
            'application/msword'=>'doc',
            'application/rtf'=>'rtf',
            'application/vnd.ms-excel'=>'xls',
            'application/vnd.ms-powerpoint'=>'ppt',
            'application/msword'=>'docx',
            'application/vnd.ms-excel'=>'xlsx',
            'application/vnd.ms-powerpoint'=>'pptx',
// open office
            'application/vnd.oasis.opendocument.text'=>'odt',
            'application/vnd.oasis.opendocument.spreadsheet'=>'ods',

        );


        $ret = isset($mimeTypes[$mimeType]) ? $mimeTypes[$mimeType] : "";

        switch ($mimeType){
            case "image/png":
            case "image/jpeg":
            case "image/gif":
                $ret = "image";
                break;
            case "application/pdf":
                $ret = "pdf";
                break;
            case "application/msword":
            case "application/rtf":
            case "application/odt":
            case "application/vnd.openxmlformats-officedocument.wordprocessingml.document":
                $ret = "doc";
                break;

            case "application/vnd.ms-excel":
            case "application/vnd.oasis.opendocument.spreadsheet":
            case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet":
                $ret = "excel";
                break;
        }

        return $ret;
    }

    /**
     * @return array
     */
    public function getAllowedMimeTypes()
    {
        return $this->allowedMimeTypes;
    }

    /**
     * @param array $allowedMimeTypes
     */
    public function setAllowedMimeTypes($allowedMimeTypes)
    {
        $this->allowedMimeTypes = $allowedMimeTypes;
    }


    function filterFilename($filename, $beautify=true) {
        // sanitize filename
        $filename = preg_replace(
            '~
        [<>:"/\\|?*]|            # file system reserved https://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
        [\x00-\x1F]|             # control characters http://msdn.microsoft.com/en-us/library/windows/desktop/aa365247%28v=vs.85%29.aspx
        [\x7F\xA0\xAD]|          # non-printing characters DEL, NO-BREAK SPACE, SOFT HYPHEN
        [#\[\]@!$&\'()+,;=]|     # URI reserved https://tools.ietf.org/html/rfc3986#section-2.2
        [{}^\~`]                 # URL unsafe characters https://www.ietf.org/rfc/rfc1738.txt
        ~x',
            '-', $filename);
        // avoids ".", ".." or ".hiddenFiles"
        $filename = ltrim($filename, '.-');
        // optional beautification
        if ($beautify) $filename = $this->beautifyFilename($filename);
        // maximize filename length to 255 bytes http://serverfault.com/a/9548/44086
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $filename = mb_strcut(pathinfo($filename, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($filename)) . ($ext ? '.' . $ext : '');
        return $filename;
    }


    function beautifyFilename($filename) {
        // reduce consecutive characters
        $filename = preg_replace(array(
            // "file   name.zip" becomes "file-name.zip"
            '/ +/',
            // "file___name.zip" becomes "file-name.zip"
            '/_+/',
            // "file---name.zip" becomes "file-name.zip"
            '/-+/'
        ), '-', $filename);
        $filename = preg_replace(array(
            // "file--.--.-.--name.zip" becomes "file.name.zip"
            '/-*\.-*/',
            // "file...name..zip" becomes "file.name.zip"
            '/\.{2,}/'
        ), '.', $filename);
        // lowercase for windows/unix interoperability http://support.microsoft.com/kb/100625
        $filename = mb_strtolower($filename, mb_detect_encoding($filename));
        // ".file-name.-" becomes "file-name"
        $filename = trim($filename, '.-');
        return $filename;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return SpacesConnect
     */
    public function getSpaceObj()
    {
        return $this->spaceObj;
    }

    /**
     * @param SpacesConnect $spaceObj
     */
    public function setSpaceObj($spaceObj)
    {
        $this->spaceObj = $spaceObj;
    }



    /**
     * @return mixed
     */
    public function getSpaceName()
    {
        return $this->spaceName;
    }

    /**
     * @param mixed $spaceName
     */
    public function setSpaceName($spaceName)
    {
        $this->spaceName = $spaceName;
    }

    /**
     * @return mixed
     */
    public function getRegionName()
    {
        return $this->regionName;
    }

    /**
     * @param mixed $regionName
     */
    public function setRegionName($regionName)
    {
        $this->regionName = $regionName;
    }

    /**
     * @return mixed
     */
    public function getDomainName()
    {
        return $this->domainName;
    }

    /**
     * @param mixed $domainName
     */
    public function setDomainName($domainName)
    {
        $this->domainName = $domainName;
    }

    /**
     * @return mixed
     */
    public function getFileInputName()
    {
        return $this->fileInputName;
    }

    /**
     * @param mixed $fileInputName
     */
    public function setFileInputName($fileInputName)
    {
        $this->fileInputName = $fileInputName;
    }

    /**
     * @return mixed
     */
    public function getAccessType()
    {
        return $this->accessType;
    }

    /**
     * @param mixed $accessType
     */
    public function setAccessType($accessType)
    {
        $this->accessType = $accessType;
    }

    /**
     * @return mixed
     */
    public function getDefinedFileName()
    {
        return $this->definedFileName;
    }

    /**
     * @param mixed $definedFileName
     */
    public function setDefinedFileName($definedFileName)
    {
        $this->definedFileName = $definedFileName;
    }



    public function mime2ext($mime) {
        $mime_map = [
            'video/3gpp2'                                                               => '3g2',
            'video/3gp'                                                                 => '3gp',
            'video/3gpp'                                                                => '3gp',
            'application/x-compressed'                                                  => '7zip',
            'audio/x-acc'                                                               => 'aac',
            'audio/ac3'                                                                 => 'ac3',
            'application/postscript'                                                    => 'ai',
            'audio/x-aiff'                                                              => 'aif',
            'audio/aiff'                                                                => 'aif',
            'audio/x-au'                                                                => 'au',
            'video/x-msvideo'                                                           => 'avi',
            'video/msvideo'                                                             => 'avi',
            'video/avi'                                                                 => 'avi',
            'application/x-troff-msvideo'                                               => 'avi',
            'application/macbinary'                                                     => 'bin',
            'application/mac-binary'                                                    => 'bin',
            'application/x-binary'                                                      => 'bin',
            'application/x-macbinary'                                                   => 'bin',
            'image/bmp'                                                                 => 'bmp',
            'image/x-bmp'                                                               => 'bmp',
            'image/x-bitmap'                                                            => 'bmp',
            'image/x-xbitmap'                                                           => 'bmp',
            'image/x-win-bitmap'                                                        => 'bmp',
            'image/x-windows-bmp'                                                       => 'bmp',
            'image/ms-bmp'                                                              => 'bmp',
            'image/x-ms-bmp'                                                            => 'bmp',
            'application/bmp'                                                           => 'bmp',
            'application/x-bmp'                                                         => 'bmp',
            'application/x-win-bitmap'                                                  => 'bmp',
            'application/cdr'                                                           => 'cdr',
            'application/coreldraw'                                                     => 'cdr',
            'application/x-cdr'                                                         => 'cdr',
            'application/x-coreldraw'                                                   => 'cdr',
            'image/cdr'                                                                 => 'cdr',
            'image/x-cdr'                                                               => 'cdr',
            'zz-application/zz-winassoc-cdr'                                            => 'cdr',
            'application/mac-compactpro'                                                => 'cpt',
            'application/pkix-crl'                                                      => 'crl',
            'application/pkcs-crl'                                                      => 'crl',
            'application/x-x509-ca-cert'                                                => 'crt',
            'application/pkix-cert'                                                     => 'crt',
            'text/css'                                                                  => 'css',
            'text/x-comma-separated-values'                                             => 'csv',
            'text/comma-separated-values'                                               => 'csv',
            'application/vnd.msexcel'                                                   => 'csv',
            'application/x-director'                                                    => 'dcr',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
            'application/x-dvi'                                                         => 'dvi',
            'message/rfc822'                                                            => 'eml',
            'application/x-msdownload'                                                  => 'exe',
            'video/x-f4v'                                                               => 'f4v',
            'audio/x-flac'                                                              => 'flac',
            'video/x-flv'                                                               => 'flv',
            'image/gif'                                                                 => 'gif',
            'application/gpg-keys'                                                      => 'gpg',
            'application/x-gtar'                                                        => 'gtar',
            'application/x-gzip'                                                        => 'gzip',
            'application/mac-binhex40'                                                  => 'hqx',
            'application/mac-binhex'                                                    => 'hqx',
            'application/x-binhex40'                                                    => 'hqx',
            'application/x-mac-binhex40'                                                => 'hqx',
            'text/html'                                                                 => 'html',
            'image/x-icon'                                                              => 'ico',
            'image/x-ico'                                                               => 'ico',
            'image/vnd.microsoft.icon'                                                  => 'ico',
            'text/calendar'                                                             => 'ics',
            'application/java-archive'                                                  => 'jar',
            'application/x-java-application'                                            => 'jar',
            'application/x-jar'                                                         => 'jar',
            'image/jp2'                                                                 => 'jp2',
            'video/mj2'                                                                 => 'jp2',
            'image/jpx'                                                                 => 'jp2',
            'image/jpm'                                                                 => 'jp2',
            'image/jpeg'                                                                => 'jpeg',
            'image/pjpeg'                                                               => 'jpeg',
            'application/x-javascript'                                                  => 'js',
            'application/json'                                                          => 'json',
            'text/json'                                                                 => 'json',
            'application/vnd.google-earth.kml+xml'                                      => 'kml',
            'application/vnd.google-earth.kmz'                                          => 'kmz',
            'text/x-log'                                                                => 'log',
            'audio/x-m4a'                                                               => 'm4a',
            'audio/mp4'                                                                 => 'm4a',
            'application/vnd.mpegurl'                                                   => 'm4u',
            'audio/midi'                                                                => 'mid',
            'application/vnd.mif'                                                       => 'mif',
            'video/quicktime'                                                           => 'mov',
            'video/x-sgi-movie'                                                         => 'movie',
            'audio/mpeg'                                                                => 'mp3',
            'audio/mpg'                                                                 => 'mp3',
            'audio/mpeg3'                                                               => 'mp3',
            'audio/mp3'                                                                 => 'mp3',
            'video/mp4'                                                                 => 'mp4',
            'video/mpeg'                                                                => 'mpeg',
            'application/oda'                                                           => 'oda',
            'audio/ogg'                                                                 => 'ogg',
            'video/ogg'                                                                 => 'ogg',
            'application/ogg'                                                           => 'ogg',
            'font/otf'                                                                  => 'otf',
            'application/x-pkcs10'                                                      => 'p10',
            'application/pkcs10'                                                        => 'p10',
            'application/x-pkcs12'                                                      => 'p12',
            'application/x-pkcs7-signature'                                             => 'p7a',
            'application/pkcs7-mime'                                                    => 'p7c',
            'application/x-pkcs7-mime'                                                  => 'p7c',
            'application/x-pkcs7-certreqresp'                                           => 'p7r',
            'application/pkcs7-signature'                                               => 'p7s',
            'application/pdf'                                                           => 'pdf',
            'application/octet-stream'                                                  => 'pdf',
            'application/x-x509-user-cert'                                              => 'pem',
            'application/x-pem-file'                                                    => 'pem',
            'application/pgp'                                                           => 'pgp',
            'application/x-httpd-php'                                                   => 'php',
            'application/php'                                                           => 'php',
            'application/x-php'                                                         => 'php',
            'text/php'                                                                  => 'php',
            'text/x-php'                                                                => 'php',
            'application/x-httpd-php-source'                                            => 'php',
            'image/png'                                                                 => 'png',
            'image/x-png'                                                               => 'png',
            'application/powerpoint'                                                    => 'ppt',
            'application/vnd.ms-powerpoint'                                             => 'ppt',
            'application/vnd.ms-office'                                                 => 'ppt',
            'application/msword'                                                        => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/x-photoshop'                                                   => 'psd',
            'image/vnd.adobe.photoshop'                                                 => 'psd',
            'audio/x-realaudio'                                                         => 'ra',
            'audio/x-pn-realaudio'                                                      => 'ram',
            'application/x-rar'                                                         => 'rar',
            'application/rar'                                                           => 'rar',
            'application/x-rar-compressed'                                              => 'rar',
            'audio/x-pn-realaudio-plugin'                                               => 'rpm',
            'application/x-pkcs7'                                                       => 'rsa',
            'text/rtf'                                                                  => 'rtf',
            'text/richtext'                                                             => 'rtx',
            'video/vnd.rn-realvideo'                                                    => 'rv',
            'application/x-stuffit'                                                     => 'sit',
            'application/smil'                                                          => 'smil',
            'text/srt'                                                                  => 'srt',
            'image/svg+xml'                                                             => 'svg',
            'application/x-shockwave-flash'                                             => 'swf',
            'application/x-tar'                                                         => 'tar',
            'application/x-gzip-compressed'                                             => 'tgz',
            'image/tiff'                                                                => 'tiff',
            'font/ttf'                                                                  => 'ttf',
            'text/plain'                                                                => 'txt',
            'text/x-vcard'                                                              => 'vcf',
            'application/videolan'                                                      => 'vlc',
            'text/vtt'                                                                  => 'vtt',
            'audio/x-wav'                                                               => 'wav',
            'audio/wave'                                                                => 'wav',
            'audio/wav'                                                                 => 'wav',
            'application/wbxml'                                                         => 'wbxml',
            'video/webm'                                                                => 'webm',
            'image/webp'                                                                => 'webp',
            'audio/x-ms-wma'                                                            => 'wma',
            'application/wmlc'                                                          => 'wmlc',
            'video/x-ms-wmv'                                                            => 'wmv',
            'video/x-ms-asf'                                                            => 'wmv',
            'font/woff'                                                                 => 'woff',
            'font/woff2'                                                                => 'woff2',
            'application/xhtml+xml'                                                     => 'xhtml',
            'application/excel'                                                         => 'xl',
            'application/msexcel'                                                       => 'xls',
            'application/x-msexcel'                                                     => 'xls',
            'application/x-ms-excel'                                                    => 'xls',
            'application/x-excel'                                                       => 'xls',
            'application/x-dos_ms_excel'                                                => 'xls',
            'application/xls'                                                           => 'xls',
            'application/x-xls'                                                         => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
            'application/vnd.ms-excel'                                                  => 'xlsx',
            'application/xml'                                                           => 'xml',
            'text/xml'                                                                  => 'xml',
            'text/xsl'                                                                  => 'xsl',
            'application/xspf+xml'                                                      => 'xspf',
            'application/x-compress'                                                    => 'z',
            'application/x-zip'                                                         => 'zip',
            'application/zip'                                                           => 'zip',
            'application/x-zip-compressed'                                              => 'zip',
            'application/s-compressed'                                                  => 'zip',
            'multipart/x-zip'                                                           => 'zip',
            'text/x-scriptzsh'                                                          => 'zsh',
        ];

        return isset($mime_map[$mime]) ? $mime_map[$mime] : "";
    }

    /**
     * @return array
     */
    public function getImageSize()
    {
        return $this->imageSize;
    }

    /**
     * @param array $imageSize
     */
    public function setImageSize($imageSize)
    {
        $this->imageSize = $imageSize;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight(int $height): void
    {
        $this->height = $height;
    }









}

/*

$file_name = $_FILES['image']['name'];
      $file_size =$_FILES['image']['size'];
      $file_tmp =$_FILES['image']['tmp_name'];
      $file_type=$_FILES['image']['type'];
      $file_ext=strtolower(end(explode('.',$_FILES['image']['name'])));

 */
