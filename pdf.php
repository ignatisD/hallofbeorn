<?php
/**
 * Created by PhpStorm.
 * User: iggi
 * Date: 13/10/2017
 * Time: 1:07 μμ
 */
if(empty($_POST["set"])) {
    die("You need to specify a set.");
}
require_once "WebCrawler.php";
$baseOrFullUrl = "http://hallofbeorn.com/LotR?CardSet=";
$crawler = new WebCrawler($baseOrFullUrl, $_POST["set"]);
$data = $crawler->retrieveImages();
if($data["success"] == true){
    $crawler->createPDF($data["images"]);
}else{
    die("Images not retrieved.");
}