<?php

/**
 * Created by PhpStorm.
 * User: Iggi
 * Date: 13/10/2017
 * Time: 09:12
 */
require_once "vendor/autoload.php";
require_once "simple_html_dom.php";


/**
 * Class WebCrawler
 *
 * Base class containing the necessary methods to crawl a page
 */
use Dompdf\Dompdf;

class WebCrawler
{

    public $url;
    public $debug;
    public $proxy;
    private $starttime;

    /**
     * WebCrawler constructor.
     * @param string $url
     * @param string $set
     */
    function __construct($url = '', $set = '')
    {
        $this->starttime = microtime(true);
        $this->url = $url;
        if(!empty($set)){
            $this->url .= urlencode($set);
        }
    }


    /**
     * Setting the debug level
     * @param $level
     */
    public function setDebug($level){
        if(!empty($level)){
            $this->debug = 1;
        }else{
            $this->debug = 0;
        }
    }

    /**
     * Request handler
     * @param $url
     * @param array $headers
     * @param null $body
     * @param null $curl
     * @param string $method
     * @param null $proxy
     * @return array
     */
    public function post_curl($url, $headers = array(), $body = null, $curl = null, $method = "POST", $proxy = null){
        $start_time = microtime(true);
        $new_curl = false;
        if(empty($curl)) {
            $new_curl = true;
            $curl = curl_init();
        }
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_COOKIEFILE => "", //in memory cookies
//            CURLOPT_COOKIEFILE => "/tmp/crawler_cookies", //file cookies
//            CURLOPT_COOKIEJAR => "/tmp/crawler-cookies", //file cookies
            CURLOPT_HEADER => 1,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36',
            CURLOPT_HTTPHEADER => $headers
        ));

        if($method == "POST" && !empty($body)){
            curl_setopt($curl,CURLOPT_POSTFIELDS ,$body);
        }
        if(!empty($proxy)) {
            curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, 1);
            curl_setopt($curl,CURLOPT_PROXY ,$proxy);
        }
        $response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        $err = curl_error($curl);
        $return = array();
        $return["cookies"] = array();
        preg_match_all('/^set-cookie:\s*([^;]*)/mi', $header, $matches);
        foreach($matches[1] as $item) {
            parse_str($item, $cookie);
            $return["cookies"] = array_merge($return["cookies"], $cookie);
        }
        $return["url"] = $url;
        $return["header"] = $header;
        //Disabled for php < 5.5
//        $return["activeCookies"] = array();
//        $activeCookies = curl_getinfo($curl, CURLINFO_COOKIELIST);
//        foreach($activeCookies as $activeCookie){
//            $parsedCookie = explode("\t", $activeCookie);
//            $return["activeCookies"][] = $parsedCookie;
//        }
        $return["body"] = $body;
        $return["error"] = $err;
        $return["timing"] = sprintf("%01.2f sec",(microtime(true) - $start_time));
        if($new_curl) {
            curl_close($curl);
        }
        return $return;
    }

    /**
     * Replaces consecutive spaces with just one and also replaces html entities space, euro and br with space nothing and -
     * @param $str
     * @return mixed
     */
    public function replaceStr($str){
        return preg_replace('!\s+!', ' ', str_replace(array("&nbsp;", "&euro;", '<br />'), array(" ", "", "- "),$str));
    }

    /**
     * Clears a given string of any non numbers characters
     * @param $str
     * @return mixed
     */
    public static function onlyFloat($str){
        return preg_replace('/[^0-9\,\.]/', '', $str);
    }

    /**
     * Returns the username used to login and timing information.
     * Also if debug is enabled prints any extra variables supplied.
     * @param $extra
     * @return array
     */
    public function getParams($extra = null){
        $data = array();
        $data["timing"] = sprintf("%01.2f sec",(microtime(true) - $this->starttime));
        if(!empty($this->debug))
            $data["extra"] = $extra;
        return $data;
    }

    /**
     * The error handler
     * @param $message
     * @param null $details
     * @return mixed
     */
    public function errorHandler($message, $details = null){
        $data["success"] = false;
        $data["reason"] = $message;
        $data["details"] = $this->getParams($details);
        return $data;
    }

    public static function fixHtml($str, $convert)
    {
        // pass it to the DOMDocument constructor
        $doc = new DOMDocument();

        // Must include the content-type/charset meta tag with $encoding
        // Bad HTML will trigger warnings, suppress those
        @$doc->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset='.$convert.'">' .$str);
        // extract the components we want
        $nodes = $doc->getElementsByTagName('body')->item(0)->childNodes;
        $html = '';
        $len = $nodes->length;
        for ($i = 0; $i < $len; $i++) {
            $html .= $doc->saveHTML($nodes->item($i));
        }
        return $html;
    }

    public static function fixImageSrc($src)
    {
        $src = html_entity_decode($src, ENT_QUOTES);
        $src = str_replace(['%2F', '%3A'], ['/', ':'], urlencode($src));
        return $src;
    }

    public static function fixImageName($filename, $extra = ''){
        if(empty($extra)){
            return $filename;
        }
        $parts = explode("/", $filename);
        $name = end($parts);
        $nameparts = explode(".", $name);
        $ext = array_pop($nameparts);
        $fixedName = implode(".", $nameparts);
        $fixedName = $fixedName."_".trim($extra).".".$ext;
        $finalName = str_replace("/", "_", $fixedName);
        return urldecode($finalName);
    }

    /**
     * You can specify the url here if the class was not instantiated with a url
     * @param string $url
     * @param string $set
     * @return array|mixed
     */
    public function retrieveImages($url = '', $set = ''){
        if(!empty($url)){
            $this->url = $url;
            if(!empty($set)){
                $this->url .= urlencode($set);
            }
        }
        $data = array();
        $response = $this->post_curl($this->url, array(), null, null, "GET", $this->proxy);
        if(!empty($response["error"])){
           return $this->errorHandler("Error finding the page: ". $response["error"]);
        }
        $images = array();
        $dom = new simple_html_dom();
        $dom->load($response["body"]);

        /** EDITABLE SECTION START
         * You can copy this method and alter the code below for different pages/classes
         */
        $divs = $dom->find(".search-explanation");
        foreach($divs as $nodiv){
            $div = $nodiv->next_sibling();
            if(empty($div)){
                continue;
            }
            $label = $div->find(".detail-label", 1);
            if(!$label){
                $trash[] = $div->plaintext;
                continue;
            }
            $imgs = $div->find("img.card-image");
            $label = $label->plaintext;


            foreach($imgs as $img){
                $image = array();
                $image["src"] = WebCrawler::fixImageSrc($img->src);
                $image["label"] = WebCrawler::fixImageName($image["src"], $label);
                $images[] = $image;
            }
        }
        /** EDITABLE SECTION END */

        $dom->clear();
        $data["success"] = true;
        $data["images"] = $images;
        $data["details"] = $this->getParams($response);
        return $data;
    }

    /**
     * Folder can also be a subfolder
     *
     * ex: 'photos/folder1'
     * @param $src
     * @param string $name
     * @param string $folder
     */
    public function saveImage($src, $name = '', $folder = "photos"){
        if(empty($name)){
            $name = uniqid().".jpg";
        }
        $name = rtrim($folder, "/")."/".$name;
        file_put_contents($name, file_get_contents($src));
    }

    public function downloadImages($images = array(), $name = "download"){
        if(empty($images)){
            die("No images retrieved");
        }
        $name = $name.".zip";
        $zip = new ZipArchive();
        $tmp_file = tempnam('.','');

        if ($zip->open($tmp_file, ZipArchive::CREATE)!==TRUE) {
            die("Cannot create zip file");
        }
        foreach($images as $image){
            if(!empty($image["src"]) && !empty($image["label"])){
                $zip->addFromString($image["label"], file_get_contents($image["src"]));
            }
        }
        $zip->close();

        header('Content-Disposition: attachment; filename="'.$name.'"');
        header('Content-type: application/zip');
        readfile($tmp_file);
        unlink($tmp_file);
    }

    public function createPDF($images = array(), $name = "download"){
        if(empty($images)){
            die("No images retrieved");
        }
        $name = $name.".pdf";

        // instantiate and use the dompdf class
        $opt["isRemoteEnabled"] = true;
        $dompdf = new Dompdf($opt);
        $html = '<!DOCTYPE html><html><head>
        <style>
        .page{
            height: 100%;
        }
        .row{
            height: 349px;
            position: relative;
        }
        .page-change{
            page-break-after: always;
        }
        </style>
        </head>
        <body><div class="page">
        <div class="row">';
        $i = 1;
        $y = 1;
        $moreImages = array();
        foreach($images as $image){
            if(preg_match("/\((.*)\)/", $image["label"], $match)){
                $x = $match[1];
                $parts = explode("_", $x);
                $temp = '';
                foreach($parts as $part){
                    $temp_part = str_replace("x", "", $part);
                    if(empty($temp) || $temp < $temp_part){
                        $temp = $temp_part;
                    }
                }
                for($i = 1; $i < $temp; $i++){
                    $moreImages[] = $image;
                }
            }
        }
        $images = array_merge($images, $moreImages);
        foreach($images as $image){
            if(preg_match("/\-[1-9][AB]_\(/",$image["label"])){
                $dimensions = 'width="348"';
                $html .= '<img src="'.$image["src"].'" style="transform:rotate(90deg);float:left; margin: 50px -51px 0 -51px;" alt="'.$image["label"].'" '.$dimensions.' />';
            }else{
                $dimensions = 'width="246"';
                $html .= '<img src="'.$image["src"].'" style="float:left;" alt="'.$image["label"].'" '.$dimensions.' />';
            }
            if($y === 5){
                $html .= '</div>';
            }
            if($i === 25){
                $html .= '</div><div class="page-change"></div><div class="page">';
                $i = 0;
            }
            if($y === 5){
                $html .='<div class="row">';
                $y = 0;
            }
            $i++;
            $y++;
        }
        $html .= '</div></div></body></html>';
//        echo $html;
        $dompdf->loadHtml($html);
        $dompdf->setPaper('b3', 'portrait');
        $dompdf->render();
        $options = array();
        $options["Attachment"] = 0;
        $dompdf->stream($name, $options);
    }
}
